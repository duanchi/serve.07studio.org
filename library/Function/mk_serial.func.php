<?php
/**
 * File    libraries\Function\parse_config.func.php
 * Desc    配置文件预处理函数
 * Manual  svn://svn.vop.com/api/manual/Function/parse_config
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-12-09
 * Time    16:09
 */

function mk_serial() {
	//return time() . mk_rand_str(22);
	return mk_rand_str(32);
}