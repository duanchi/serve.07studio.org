<?php
/**
 * Created by PhpStorm.
 * User: lovemybud
 * Date: 13-12-26
 * Time: 23:18
 */

class LOG {

	private static $__log = [];
	private static $__date = '';

	public static function init() {
		self::$__date = date('Y-m-d H:i:s');
		self::$__log = [
			'request-time'      =>  self::$__date,
			'remote-address'    =>  $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'],
			'request-method'    =>  $_SERVER['REQUEST_METHOD'],
			'uri'       =>  $_SERVER['REQUEST_URI'],
		];
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			self::$__log['request'] = "\n".file_get_contents('php://input');
		}
	}

	public static function dump() {
		return self::$__log;
	}

	public static function set($_key = '', $_value = '', $_wrap = TRUE) {
		if (array_key_exists($_key, self::$__log)) {
			self::$__log[$_key] .= ($_wrap ? "\n\n" : "") . $_value;
		} else {
			self::$__log[$_key] = $_value;
		}
	}

	public static function record() {

		$_stream = "\n".'[request start]----------------------->>>-------------------------'."\n";
		foreach (self::$__log as $k => $v) {
			$_stream .= "[".$k.']:'.$v."\n";
		}
		$_stream .= '[request end]-------------------------<<<-------------------------'."\n";

		file_put_contents('/opt/local/var/log/vop/api/debug-'.explode(' ', self::$__date)[0].'.log', $_stream, FILE_APPEND);
	}

	public static function dumpfile($_mvnokey, $_log, $_spent = NULL, $_serial) {
		$_stream = '[serial: '.$_serial.']----------------------->>>-------------------------'."\n";
		foreach ($_log as $k => $v) {
			$_stream .= "[".$k.']:'.$v."\n";
		}

		if ($_spent != NULL) {
			$_stream .= "\n".'Timespent:'."\n";
			$_stream .= 'SYSTEM-PROCESS-TIME: '.($_spent['PROC-1'] + $_spent['PROC-2'])." ms\n";
			$_stream .= 'API-PROCESS-TIME: '.($_spent['API-PROC'])." ms\n";
		}

		file_put_contents(DUMP_FILE_PATH . DIRECTORY_SEPARATOR . $_mvnokey . DIRECTORY_SEPARATOR . $_serial . '.dump', $_stream);
	}
} 