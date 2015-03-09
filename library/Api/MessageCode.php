<?php
/**
 * File    libraries\VOP\MessageCode.php
 * Desc    消息代码处理单元
 * Manual  svn://svn.vop.com/api/manual/libraries/vop/Exception
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-23
 * Time    22:33
 */

namespace Api;

/**
 * Class MessageCode
 * @package VOP
 */
class MessageCode {

	private static $_MEMSET = NULL;

	/**
	 * function _connect
	 */
	private static function _connect() {

		$_conf = self::_get_conf();

		$_redis = new \Redis();
		$_redis->connect($_conf['host'], $_conf['port']);

		self::$_MEMSET = $_redis;
		self::_select();
	}

	/**
	 * function _get_conf
	 * @return array
	 */
	private static function _get_conf() {
		return [
			'host'  =>  isset(\Yaf\Registry::get('config')->get('memdb')->redis->host) ? \Yaf\Registry::get('config')->get('memdb')->redis->host : '127.0.0.1',
			'port'  =>  isset(\Yaf\Registry::get('config')->get('memdb')->redis->port) ? \Yaf\Registry::get('config')->get('memdb')->redis->port : 6379,
		];
	}

	/**
	 * function get
	 * @param null $_key
	 * @return array|bool
	 */
	public static function get($_key = NULL) {
		$_result = FALSE;

		//self::_init();
		if (!empty(self::$_MEMSET) &&!empty($_key)) {

			$_result = explode('|', self::$_MEMSET->get($_key));

		} elseif (empty(self::$_MEMSET)) {

			self::_init();
			$_result = explode('|', self::$_MEMSET->get($_key));

		}

		return $_result;
	}

	/**
	 * function _init
	 * @return bool
	 */
	private static function _init() {
		$_result = FALSE;

		if (empty(self::$_MEMSET)) {
			self::_connect();
		}

		if (!empty(self::$_MEMSET)) {
			$_set = parse_ini_file(MESSAGE_CODE_CONF_FILE_PATH);
			/*foreach($_set as $key => $value) {
				self::$_MEMSET->set($key, $value);
			}*/
			//use mset init values
			self::$_MEMSET->mset($_set);
			$_result = TRUE;
		}

		return $_result;
	}

	/**
	 * function _select
	 */
	private static function _select() {
		self::$_MEMSET->select(MEM_DB_EXCEPTION);
	}

} 