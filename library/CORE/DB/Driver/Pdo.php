<?php
namespace CORE\DB\Driver;
/**
 * PDO Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the active record
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class Pdo extends \CORE\DB\Driver {
	
	protected $_options = [];
	
	protected $_charset		= 'UTF8';
	protected $_collation	= 'utf8_general_ci';
	
	function __construct($_options = []) {
		parent::__construct($_options);
	}
	
	public function connect($_params) {
		
		$this->_options['PDO::MYSQL_ATTR_INIT_COMMAND'] = 'SET NAMES '.$this->_charset.'};';
		
		return new \PDO($_params['dsn'], $_params['user'], $_params['password'], $this->_options);
	}
	
	public function pconnect($_params) {
		
		$this->_options['PDO::ATTR_ERRMODE'] = \PDO::ERRMODE_SILENT;
		$this->_options['PDO::ATTR_PERSISTENT'] = TRUE;
		
		return new \PDO($_params['host'], $_params['user'], $_params['password'], $this->_options);
	}
	
	public function db_select($_db) {
		//PDO NO NEED TO SELECT DB;
		return TRUE;
	}
	
	public function db_set_charset($_charset, $_collation) {
		//PDO NO NEED TO SET CHARSET;
		return TRUE;
	}
	
	public function version() {
		return $this->_conn_id->getAttribute(\PDO::ATTR_CLIENT_VERSION);
	}
	
	public function close($_conn_id = NULL) {
		//PDO NO NEED TO CLOSE;
		$this->_conn_id = FALSE;
		$this->_result_id = FALSE;
		return TRUE;
	}
	
	public function execute($sql) {
		$result_id = $this->_conn_id->prepare($sql);
		$result_id->execute();
		
		if (is_object($result_id))
		{
			if (is_numeric(stripos($sql, 'SELECT')))
			{
				$this->_affect_rows = count($result_id->fetchAll());
				$result_id->execute();
			}
			else
			{
				$this->_affect_rows = $result_id->rowCount();
			}
		}
		else
		{
			$this->_affect_rows = 0;
		}
		return $result_id;
	}
	
	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */
	public function num_rows()
	{
		if (is_int($this->_num_rows))
		{
			return $this->_num_rows;
		}
		elseif (($this->_num_rows = $this->_result_id->rowCount()) > 0)
		{
			return $this->_num_rows;
		}
	
		$this->_num_rows = count($this->_result_id->fetchAll());
		$this->_result_id->execute();
		return $this->_num_rows;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Data Seek
	 *
	 * Moves the internal pointer to the desired offset.  We call
	 * this internally before fetching results to make sure the
	 * result set starts at zero
	 *
	 * @access	private
	 * @return	array
	 */
	protected function _data_seek($n = 0)
	{
		return FALSE;
	}
	
	protected function _fetch_assoc()
	{
		return $this->_result_id->fetch(\PDO::FETCH_ASSOC);
	}
	
}