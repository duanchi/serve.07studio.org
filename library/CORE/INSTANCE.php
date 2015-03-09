<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/9
 * Time: 下午12:44
 */

namespace CORE;


class INSTANCE {

	static private $__instance         =   [];

	static public function set($_object) {
		$_class_name                    =   get_class($_object);
		self::$__instance[$_class_name] =   $_object;

		return $_class_name;
	}

	static public function get($_key) {
		return isset(self::$__instance[$_key]) ? self::$__instance[$_key] : FALSE;
	}
}