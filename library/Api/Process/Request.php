<?php
/**
 * File    libraries\VOP\Process\Request.php
 * Desc    请求预处理模块
 * Manual  svn://svn.vop.com/api/manual/Plugin/Request
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-23
 * Time    16:15
 */

namespace Api\Process;


class Request {

    private $_request = [
        'url'           => [],
        'content-type'  => TYPE_JSON,
        'version'       => REQUEST_VERSION_NULL,
        'ranges'        => [
            'columns'       => NULL,
            'order'         => NULL,
            'limit'         => NULL,
        ],

        'access-token'  =>  NULL,
        'client-token'  =>  NULL,
        'client-id'     =>  NULL,

        'content'       => [],
    ];

	/**
	 * Function pretreatment
	 * @param $_get
	 * @param $_post
	 * @param $_version
	 * @param $_scope
	 * @param $_interface
	 * @return array
	 */
	public static function get() {
        $_tmp_request                   = [];

        //MAKE URL REQUEST

        //MAKE HTTP HEADER REQUEST
        $_tmp_header_request            = [];
        $_tmp_http_accept               = explode(';', str_replace(' ', '', strtolower($_SERVER['HTTP_ACCEPT'])));

        $_tmp_request['content-type']   = (($_tmp_http_accept[0] == TYPE_JSON) || ($_tmp_http_accept[0] == TYPE_MSGPACK) ? $_tmp_http_accept[0] : TYPE_NULL);

        if (isset($_tmp_http_accept[1]) && !empty($_tmp_http_accept[1])) {
            $_tmp = explode('=', $_tmp_http_accept[1]);
            isset($_tmp[1]) && !empty($_tmp[1]) ? $_tmp_request['version'] = $_tmp[1] : FALSE;
        }

        //@todo Ranges

        //MAKE CONTENT REQUEST
        $_tmp_request['content'] = file_get_contents('php://input');
        switch ($_tmp_request['content-type']) {
            case TYPE_MSGPACK :
                $_tmp_request['content'] = msgpack_unpack($_tmp_request['content']);
                break;

            case TYPE_JSON :
                $_tmp_request['content'] = json_decode($_tmp_request['content']);
                break;

            case TYPE_NULL :
            default :
                $_tmp_request['content'] = NULL;
                break;
        }

		return $_tmp_request;
	}

	private static function check_service($_name, $_type = 'service') {
		$_result = FALSE;
		$_conf = \Yaf\Registry::get('service');
		switch($_type) {

			case 'method':
				if (isset($_conf['method'][$_name]) && ($_conf['method'][$_name] == '1')) {
					$_result = TRUE;
				}
				break;

			case 'service':
			default:
			if (isset($_conf['service'][$_name]) && ($_conf['service'][$_name] == '1')) {
				$_result = TRUE;
			}
				break;
		}

		return $_result;
	}
}