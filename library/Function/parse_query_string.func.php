<?php
/**
 * File    libraries\Function\parse_query_string.func.php
 * Desc    接口路由预处理函数
 * Manual  svn://svn.vop.com/api/manual/Function/parse_query_string
 * version 1.0.0
 * User    duanchi<http://weibo.com/shijingye>
 * Date    2013-11-10
 * Time    0:59
 */

/**
 * @name    parse_query_string
 * @desc    接口路由预处理函数
 * @param   null $_query_string
 * @return  array
 */
function parse_query_string($_query_string = NULL) {
	$_result = [
		  'version' =>  'v1',
		    'scope' =>	NULL,
		'interface' =>	NULL,
	];

	$_query_string = strtolower($_query_string);

	if (!$_query_string == NULL) {
		$_pre = explode('/', $_query_string, 3);
		$_count = count($_pre);
		switch($_count) {
			case 3:
				$_result = [
					  'version' =>  $_pre[0],
					    'scope' =>	$_pre[1],
					'interface' =>	$_pre[2],
				];
				break;

			case 2:
				if (strpos($_pre[0], 'v') === 0) {//如果以v开头,默认识别为版本
					$_result = [
						  'version' =>  $_pre[0],
						    'scope' =>	$_pre[1],
					];
				} else {
					$_result = [
						    'scope' =>	$_pre[0],
						'interface' =>	$_pre[1],
					];
				}
				break;

			case 1:
				$_result = [
					'scope' =>	$_pre[0],
				];
				break;

			default:

				break;
		}
	}

	return $_result;
}