<?php
/**
 * File    libraries\Function\parse_config.func.php
 * Desc    配置文件预处理函数
 * Manual  svn://svn.vop.com/api/manual/Function/parse_config
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-10
 * Time    0:59
 */

/**
 * PARSE_CONFIG_FILE FROM INI TO CONFIG VARS
 * @param string $_config
 * @return Ambigous <boolean, multitype:>
 */
function parse_config($_config = NULL) {
	if (!is_file($_config)) $_config = NULL;
	return ($_config != NULL ? parse_ini_file($_config, TRUE) : FALSE);
}