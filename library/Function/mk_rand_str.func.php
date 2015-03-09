<?php
/**
 * File    libraries\Function\parse_config.func.php
 * Desc    生成随机字符串
 * Manual  svn://svn.vop.com/api/manual/Function/parse_config
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-12-09
 * Time    16:17
 */

function mk_rand_str($_length = 48) {
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz'; // characters to build the password from
	$string = '';
	for(;$_length >= 1;$_length--)
	{
		$position = mt_rand() % strlen($chars);
		$string .= substr($chars, $position, 1);
	}
	return $string;
}