<?php
/**
 * File    libraries\Function\parse_config.func.php
 * Desc    生成随机字符串
 * Manual  svn://svn.vop.com/api/manual/Function/parse_config
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-12-09
 * Time    16:32
 */

function mk_serial_package($_api, $_mvno, $_request_serial) {

	$_push_url = \DB\KV::get($_mvno['mvnokey'].':PUSHURL', MEM_DB_SERIAL);

	(empty($_push_url) || $_push_url == FALSE) ?$_push_url = 'no://url' : FALSE;


	//组装 push_url;
	//$_push_url = (isset($_mvno['push.'.$_api['apikey']]) && !empty($_mvno['push.'.$_api['apikey']]) ? $_mvno['push.'.$_api['apikey']] : FALSE);

	return msgpack_pack([
		'mvno'      =>  [$_mvno['mvnokey'], $_push_url],
		'api'       =>  $_api['apikey'],
		'serial'    =>  $_request_serial
	]);
}