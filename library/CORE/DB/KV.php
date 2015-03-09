<?php
/**
 * File    application\api\libraries/DB/KV.php
 * Desc    K-V DB CLASS
 * Manual  svn://svn.vop.com/api/libraries/DB/KV
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-12-06
 * Time    15:16
 */

namespace CORE\DB;

class KV {

	private static $_MEMSET = NULL;

	private static $_DB = NULL;

	private static function _connect($_db) {

		$_conf = self::_get_conf();

		$_redis = new \Redis();
		$_redis->connect($_conf['host'], $_conf['port']);

		self::$_MEMSET = $_redis;
		self::_select($_db);
		self::$_DB = $_db;

		return TRUE;
	}

	/**
	 * function _get_conf
	 * @return array
	 */
	private static function _get_conf() {
		return [
			'host'  =>  \Yaf\Registry::get('config')->get('memdb')->redis->host,
			'port'  =>  \Yaf\Registry::get('config')->get('memdb')->redis->port,
		];
	}

	/**
	 * function get
	 * @param null $_key
	 * @return array|bool
	 */
	public static function get($_key = NULL, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->get($_key);

		return $_result;
	}

	public static function set($_key, $_value, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->set($_key, $_value);

		return $_result;
	}

	public static function append($_key, $_value, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->append($_key, $_value);

		return $_result;
	}

	//Set funcitons

	public static function sMembers($_key, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->sMembers($_key);

		return $_result;
	}

	public static function sAdd($_key, $_member, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->sAdd($_key, $_member);

		return $_result;
	}

	public static function sIsMember($_key, $_member, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->sIsMember($_key, $_member);

		return $_result;
	}

	public static function del($_key, $_db) {
		$_result = FALSE;

		self::_init($_db);
		$_result = self::$_MEMSET->del($_key);

		return $_result;
	}

	/**
	 * function _init
	 * @return bool
	 */
	private static function _init($_db) {
		$_result = FALSE;

		if (empty(self::$_MEMSET)) {

			$_result = self::_connect($_db);

		} elseif (self::$_DB != $_db) {

			$_result = self::_select($_db);

		} else {
			$_result = TRUE;
		}

		return $_result;
	}

	/**
	 * function _select
	 */
	private static function _select($_db) {
		self::$_MEMSET->select($_db);

		return TRUE;
	}
} 