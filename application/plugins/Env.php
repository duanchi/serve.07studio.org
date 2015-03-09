<?php
/**
 * File    application\plugin\Env.php
 * Desc    请求预处理插件模块
 * Manual  svn://svn.vop.com/api/manual/plugin/Process
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-22
 * Time    20:36
 */

class EnvPlugin extends Yaf\Plugin_Abstract {

	function __construct()
    {
        define('IS_PHP56', (PHP_VERSION > '5.6'));
        define('IS_WINDOWS', strpos(strtoupper(PHP_OS), 'WIN') !== FALSE);
    }
} 