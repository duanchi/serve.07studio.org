<?php
/**
 * File    libraries\Function\pre_process_request.func.php
 * Desc    请求预处理函数
 * Manual  svn://svn.vop.com/api/manual/Function/pre_process_request
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-12
 * Time    16:54
 */
/**
 * Function pre_process_request
 * @param $_get
 * @param $_post
 * @param $_version
 * @param $_scope
 * @param $_interface
 * @return array
 */
function pre_process_request($_get, $_post, $_version, $_scope, $_interface) {


	$_system = [
		'swift_number'  =>  NULL,
		   'timestamp'  =>  NULL,
		         'api'  =>  NULL,
		     'service'  =>  NULL,
		'service_type'  =>  NULL,
		   'qos_level'  =>  API_QOS_NORMAL,
	];

	/**
	 * IF $_POST, DECODE JSON PACKAGE
	 */

	$_post = json_decode($_post, TRUE);


	if (!empty($_post) && $_post != FALSE) {
		$_system['swift_number'] = (isset($_post['swift_number']) ? $_post['swift_number'] : NULL);
		$_system['timestamp'] = (isset($_post['timestamp']) ? $_post['timestamp'] : NULL);
		if (isset($_post['qos_level'])) {
			switch($_post['qos_level']) {
				case 'vip' :
					$_system['qos_level'] = API_QOS_VIP;
					break;

				case 'high_level' :
					$_system['qos_level'] = API_QOS_HIGH_LEVEL;
					break;

				case 'normal' :
				default :
					$_system['qos_level'] = API_QOS_NORMAL;
					break;
			}
		}
		unset($_post['swift_number']);
		unset($_post['timestamp']);
		unset($_post['qos_level']);
	}

	/**
	 * DECODE END
	 */

	$_result = [
		'authorize'	    =>	[
			      'mvno_id' =>  NULL,
			        'token'	=>	NULL,
			           'ip'	=>	$_SERVER['REMOTE_ADDR'],
		],
		'api'	        =>	[
			      'version' =>  empty($_version) ? '1' : $_version,
			        'scope'	=>	$_scope,
			    'interface'	=>	$_interface,
		],
		'method'	    =>	[
		   'request_method'	=>	constant('HTTP_' . $_SERVER['REQUEST_METHOD']),
			  'return_type' =>  DATA_TYPE_JSON,
		],
		'parameters'	=>	[
				   'system' =>  $_system,
			         'post'	=>	$_post,
			          'get'	=>	$_get,
		],
	];

	/**
	 * GET TOKEN
	 */

	$_result['authorize']['token'] = (isset($_result['parameters']['get']['token']) ? $_result['parameters']['get']['token'] : NULL);

	unset($_result['parameters']['get']['token']);
	/**
	 * GET TOKEN END
	 */

	/**
	 * GET MVNO ID
	 */
	if ($_result['method']['request_method'] == HTTP_POST) {

		$_result['authorize']['mvno_id'] = (isset($_result['parameters']['post']['mvno_id']) ? $_result['parameters']['post']['mvno_id'] : NULL);

	} else{

		$_result['authorize']['mvno_id'] = (isset($_result['parameters']['get']['mvno_id']) ? $_result['parameters']['get']['mvno_id'] : NULL);

	}

	/**
	 * GET MVNO ID END
	 */

	return $_result;
}