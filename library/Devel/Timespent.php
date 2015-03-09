<?php
/**
 * Created by PhpStorm.
 * User: lovemybud
 * Date: 14-2-15
 * Time: 23:51
 */

namespace Devel;


class Timespent {
	private static $start_time = 0;
	private static $stop_time = 0;
	private static $spend_time = [];
	private static $total_time = 0;

	public static function _init() {
		self::$total_time = self::get_microtime();
		self::start();
	}

	public static function get_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	public static function start()
	{
		self::$start_time = self::get_microtime();
	}

	public static function suspend($_flag = 'FINISH')
	{
		self::$stop_time = self::get_microtime();
		self::_spent($_flag);
	}

	public static function record($_flag = 'FINISH')
	{
		self::suspend($_flag);
		self::start();
	}

	private static function _spent($_flag = '')
	{
		$time = round((self::$stop_time - self::$start_time) * 1000, 4);
		!empty($_flag) ? self::$spend_time[$_flag] = $time : self::$spend_time[] = $time;
	}

	public static function spent() {
		self::total();
		$_result = '';
		foreach (self::$spend_time as $_key => $_node) {
			$_result .= '\'' . $_key . ': ' . $_node . 'ms\', ';
		}
		return $_result;
	}

	public static function total() {
		self::$spend_time['TOTAL'] = round((self::get_microtime() - self::$total_time) * 1000, 4);
	}

}