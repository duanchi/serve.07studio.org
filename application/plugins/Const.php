<?php
/**
 * File    application\plugin\Ads.php
 * Desc    请求预处理插件模块
 * Manual  svn://svn.vop.com/api/manual/plugin/Process
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-22
 * Time    20:36
 */

class ConstPlugin extends Yaf\Plugin_Abstract {

	function __construct() {

		//parse app path
		$_conf = \Yaf\Registry::get('config')->get('application')->constant;
        foreach ($_conf as $_key => $_value) {
            define('ADS_' . strtoupper($_key), $_value);
        }

        //parse constant config
        $_conf = get_yaf_config(ADS_CONSTANT_SETTINGS);

        foreach ($_conf as $_property => $_value) {
            define($_property, $_value);
        }

		//parse status code config
		//$_conf = get_yaf_config('status_code/message_code_config.ini');
		//\Yaf\Registry::set('service',$_conf);

		//parse other
		$_conf = \Yaf\Registry::get('config')->get('application')->get('api');
		$this->_app_define($_conf);

		unset($_conf);
	}

	private function _app_define($_array = []) {
		foreach ($_array as $k => $v) define('API_'.strtoupper($k), $v);
	}
} 