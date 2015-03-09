<?php
/**
 * Created by PhpStorm.
 * User: shijy33
 * Date: 13-11-27
 * Time: 10:31
 */

function package_error($_error, $_message = NULL) {

	return [
		'error' =>  [
			'status'    =>  (isset($_error[0]) ? $_error[0] : 'S-SF-001'),
			'message'   =>  (isset($_error[1]) ? $_error[1] : '系统错误') . (isset($_message) ? '('.$_message.')' : ''),
		]
	];
}