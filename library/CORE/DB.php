<?php
namespace CORE;

class DB {
	
	protected static $_driver		= 'pdo';
	protected static $_save_queries	= TRUE;
	
	protected static $_benchmark	= 0;
	protected static $_query_count	= 0;
	protected static $_queries		= [];
	protected static $_query_times	= [];
	
	private   static $__instance	= NULL;
	
	public static function initialize() {
		
		if (!is_object(self::$__instance))
		{
			$_config = self::_get_config();
			self::$_driver = $_config['_config']->driver;
			
			//connect to database
			$_class = '\CORE\DB\Driver\\'.ucfirst(self::$_driver);
			$_options = [
				'_config'		=>	$_config['_config'],
				'_connect'		=>	$_config['_connect'],
			];
			self::$__instance = new $_class($_options);
			if (!is_object(self::$__instance) || self::$__instance->_conn_id == FALSE) return FALSE;
		}
		
		return TRUE;
	}
	
	public static function connect($_params) {
		return self::$__instance->connect($_params);
	}
	
	public static function pconnect($_params) {
		return self::$__instance->pconnect($_params);
	}
	
	public static function close() {
		self::$__instance->close();
		self::$__instance = NULL;
		return TRUE;
	}
	
	public static function db_select($_db) {
		return self::$__instance->db_select($_db);
	}
	
	public static function db_set_charset($_charset, $_collation) {
		return self::$__instance->db_set_charset($_charset, $_collation);
	}
	
	public static function query($sql, $binds = FALSE, $return_object = TRUE, $enable_debug = FALSE) {
		return self::$__instance->query($sql, $binds, $return_object, $enable_debug);
	}
	
	public static function result_array() {
		return self::$__instance->result_array();
	}

	public static function num_rows() {
		return self::$__instance->num_rows();
	}
	
	public static function row($n) {
		return self::$__instance->row($n);
	}
	
	public static function row_array($n = 0) {
		return self::$__instance->row_array($n);
	}
	
	public static function next_row() {
		return self::$__instance->next_row();
	}
	
	public static function previous_row() {
		return self::$__instance->previous_row();
	}
	
	public static function first_row() {
		return self::$__instance->first_row();
	}
	
	public static function last_row() {
		return self::$__instance->last_row();
	}
	
	public static function set_row($key, $value = NULL) {
		return self::$__instance->set_row($key, $value);
	}
	
	private static function _get_config() {
		$_config = get_config('db');
		$_driver = $_config->{$_config->driver};
	
		/*
		 * check for configs
		*/
		if  (isset($_driver->dsn)) {
			if (($dsn = parse_url($_driver->dsn)) === FALSE)
			{
				error('Invalid DB Connection String');
			}
			$params = [
				'engine'	=> (isset($dsn['scheme'])) ? rawurldecode($dsn['scheme']) : 'mysql',
				'host'		=> (isset($dsn['host'])) ? rawurldecode($dsn['host']) : '',
				'port'		=> (!isset($dsn['port']) || $dsn['port'] == '80') ? '3306' : rawurldecode($dsn['port']),
				'user'		=> (isset($dsn['user'])) ? rawurldecode($dsn['user']) : '',
				'password'	=> (isset($dsn['pass'])) ? rawurldecode($dsn['pass']) : '',
				'database'	=> (isset($dsn['path'])) ? rawurldecode(substr($dsn['path'], 1)) : ''
			];
			$params['dsn'] = self::_get_dsn($params);
		} else {
			$params = [
				'engine'	=> (isset($_driver->engine)) ? $_driver->engine : 'mysql',
				'host'		=> (isset($_driver->host)) ? $_driver->host : '',
				'port'		=> (isset($_driver->port)) ? rawurldecode($_driver->port) : '3306',
				'user'		=> (isset($_driver->user)) ? $_driver->user : '',
				'password'	=> (isset($_driver->password)) ? $_driver->password : '',
				'database'	=> (isset($_driver->database)) ? $_driver->database : ''
			];
		}
		return ['_config'=>$_config,'_connect'=>$params];
	}
	
	private static function _get_dsn ($_params) {
		$dsn = $_params['engine'].':';
		$dsn .= 'host='.$_params['host'].';';
		$dsn .= 'port='.$_params['port'].';';
		isset($_params['database']) ? $dsn .= 'dbname='.$_params['database'].';' : FALSE;
	
		return $dsn;
	}

}