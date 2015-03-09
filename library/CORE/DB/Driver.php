<?php

namespace CORE\DB;

abstract class Driver {
	
	protected	$_charset		= 'UTF8';
	protected	$_collation		= 'utf8_general_ci';
	protected	$_bind_marker	= '?';
	protected	$_pconnect		= FALSE;
	protected	$_db_prefix		= '';
	protected	$_swap_pre		= 'DB_PREFIX';
	
	protected	$_RES			= NULL;
	
	public		$_conn_id		= FALSE;
	public		$_result_id		= FALSE;
	
	public		$_affect_rows	= 0;
	public		$_num_rows		= FALSE;
	public		$_result_array	= [];
	public		$_current_row	= 0;
	public		$_row_data		= NULL;
	
	function __construct($_options = []) {
			
		isset($_options['_config']->pconnect)	&& !empty($_options['_config']->pconnect)		? $this->_pconnect		= $_options['_config']->pconnect	: FALSE;
		isset($_options['_config']->charset)	&& !empty($_options['_config']->charset)		? $this->_charset		= $_options['_config']->charset		: FALSE;
		isset($_options['_config']->collation)	&& !empty($_options['_config']->collation)		? $this->_collation		= $_options['_config']->collation	: FALSE;
		isset($_options['_config']->bind_marker)&& !empty($_options['_config']->bind_marker)	? $this->_bind_marker	= $_options['_config']->bind_marker	: FALSE;
		isset($_options['_config']->db_prefix)	&& !empty($_options['_config']->db_prefix)		? $this->_db_prefix		= $_options['_config']->db_prefix	: FALSE;
		isset($_options['_config']->swap_pre)	&& !empty($_options['_config']->swap_pre)		? $this->_swap_pre		= $_options['_config']->swap_pre	: FALSE;
		
		$this->_conn_id = ($this->_pconnect == FALSE ? $this->connect($_options['_connect']) : $this->pconnect($_options['_connect']));
		
		if (isset($_options['_connect']['database']) && !empty($_options['_connect']['database']))
		{
			return $this->db_select($_options['_connect']['database']) && $this->db_set_charset($this->_charset, $this->_collation);
		}
	}
	
	function __destruct()
	{
		$this->close($this->_conn_id);
		$this->reset_select();
	}
	
	protected function reset_select()
	{
		$this->_RES			= NULL;
		$this->_affect_rows	= 0;
		$this->_num_rows	= FALSE;
		$this->_result_array= [];
		$this->_current_row	= 0;
		$this->_row_data	= NULL;
	}
	
	abstract public function connect($_params) ;
	
	abstract public function pconnect($_params) ;
	
	abstract public function close($_conn_id = NULL) ;
	
	abstract public function db_select($_db) ;
	
	abstract public function db_set_charset($_charset, $_collation) ;
	
	abstract public function version() ;
	
	abstract public function execute($sql) ;
	
	abstract public function num_rows() ;
	
	public function query($sql, $binds = FALSE, $return_object = TRUE, $_enable_debug = FALSE)
	{
	
		// Verify table prefix and replace if necessary
		if ( ($this->_db_prefix != '' && $this->_swap_pre != '') && ($this->_db_prefix != $this->_swap_pre) )
		{
			$sql = preg_replace("/(\W)".$this->_swap_pre."(\S+?)/", "\\1".$this->_db_prefix."\\2", $sql);
		}
	
		// Compile binds if needed
		if ($binds !== FALSE)
		{
			$sql = $this->_compile_binds($sql, $binds);
		}
		
		// Run the Query
		//if ($this->_RES != NULL)
		//{
		$this->reset_select();
		//}
		if ($_enable_debug) var_dump($sql);
		$RES = $this->execute($sql);
		$this->_result_id = $RES;
		// Return TRUE if we don't need to create a result object
		// Currently only the Oracle driver uses this when stored
		// procedures are used
		if ($return_object !== TRUE)
		{
			return TRUE;
		}
		
		$this->_num_rows = $this->num_rows();
	
		// Is query caching enabled?  If so, we'll serialize the
		// result object and save it to a cache file.
		
		return $RES;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Affected Rows
	 *
	 * @access	public
	 * @return	integer
	 */
	public function affected_rows()
	{
		return $this->_affect_rows;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Insert ID
	 *
	 * @access	public
	 * @return	integer
	 */
	public function insert_id()
	{
		return $this->conn_id->lastInsertId();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Count All" query
	 *
	 * Generates a platform-specific query string that counts all records in
	 * the specified database
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function count_all($table = ''){}
	
	/**
	 * Query result.  Acts as a wrapper function for the following functions.
	 *
	 * @access	public
	 * @param	string	can be "object" or "array"
	 * @return	mixed	either a result object or array
	 */
	public function result($type = 'array')
	{
		if ($type == 'array') return $this->result_array();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Query result.  "array" version.
	 *
	 * @access	public
	 * @return	array
	 */
	public function result_array()
	{
		if (count($this->_result_array) > 0)
		{
			return $this->_result_array;
		}
	
		// In the event that query caching is on the result_id variable
		// will return FALSE since there isn't a valid SQL resource so
		// we'll simply return an empty array.
		if ($this->_result_id === FALSE || $this->num_rows() == 0)
		{
			return [];
		}
		
		$this->_data_seek(0);
		while ($row = $this->_fetch_assoc())
		{
		$this->_result_array[] = $row;
		}
	
		return $this->_result_array;
	}
	
	// --------------------------------------------------------------------
	
		/**
		* Query result.  Acts as a wrapper function for the following functions.
		*
		* @access	public
		* @param	string
		* @param	string	can be "object" or "array"
		* @return	mixed	either a result object or array
		*/
	public function row($n = 0, $type = 'array')
	{
		if ( ! is_numeric($n))
		{
		// We cache the row data for subsequent uses
			if ( ! is_array($this->_row_data))
			{
				$this->_row_data = $this->row_array(0);
			}
			// array_key_exists() instead of isset() to allow for MySQL NULL values
			if (array_key_exists($n, $this->_row_data))
			{
				return $this->_row_data[$n];
			}
			// reset the $n variable if the result was not achieved
			$n = 0;
		}
		if ($type == 'array') return $this->row_array($n);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Assigns an item into a particular column slot
	 *
	 * @access	public
	 * @return	object
	 */
	 public function set_row($key, $value = NULL)
	{
	// We cache the row data for subsequent uses
		if ( ! is_array($this->_row_data))
		{
			$this->_row_data = $this->_row_array(0);
		}
	
		if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$this->_row_data[$k] = $v;
			}
	
			return;
		}
	
		if ($key != '' && ! is_null($value))
		{
			$this->_row_data[$key] = $value;
		}
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Returns the "first" row
	 *
	 * @access	public
	 * @return	object
	 */
	public function first_row($type = 'object')
	{
		$result = $this->result($type);
	
		if (count($result) == 0)
		{
			return $result;
		}
		return $result[0];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the "last" row
	 *
	 * @access	public
	 * @return	object
	 */
	public function last_row($type = 'object')
	{
		$result = $this->result($type);
	
		if (count($result) == 0)
		{
			return $result;
		}
		return $result[count($result) -1];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the "next" row
	 *
	 * @access	public
	 * @return	object
	 */
	public function next_row($type = 'object')
	{
		$result = $this->result($type);
	
		if (count($result) == 0)
		{
			return $result;
		}
	
		if (isset($result[$this->current_row + 1]))
		{
			++$this->current_row;
		}
	
		return $result[$this->current_row];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the "previous" row
	 *
	 * @access	public
	 * @return	object
	 */
	public function previous_row($type = 'object')
	{
		$result = $this->result($type);
	
		if (count($result) == 0)
		{
			return $result;
		}
	
		if (isset($result[$this->current_row - 1]))
		{
			--$this->current_row;
		}
		return $result[$this->current_row];
	}
	
	protected function _fetch_assoc()
	{
		return [];
	}
	
	protected function _data_seek($n = 0)
	{
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a single result row - array version
	 *
	 * @access	public
	 * @return	array
	 */
	public function row_array($n = 0)
	{
		$result = $this->result_array();
	
		if (count($result) == 0)
		{
			return $result;
		}
	
		if ($n != $this->_current_row && isset($result[$n]))
		{
			$this->_current_row = $n;
		}
	
		return $result[$this->_current_row];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compile Bindings
	 *
	 * @access	public
	 * @param	string	the sql statement
	 * @param	array	an array of bind data
	 * @return	string
	 */
	private function _compile_binds($sql, $binds)
	{
		if (strpos($sql,$this->_bind_marker) === FALSE)
		{
			return $sql;
		}
	
		if ( ! is_array($binds))
		{
			$binds = [$binds];
		}
	
		// Get the sql segments around the bind markers
		$segments = explode($this->_bind_marker, $sql);
	
		// The count of bind should be 1 less then the count of segments
		// If there are more bind arguments trim it down
		if (count($binds) >= count($segments)) {
			$binds = array_slice($binds, 0, count($segments)-1);
		}
	
		// Construct the binded query
		$result = $segments[0];
		$i = 0;
		foreach ($binds as $bind)
		{
			$result .= $this->_escape($bind);
			$result .= $segments[++$i];
		}
	
		return $result;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	private function _escape($str)
	{
		if (is_string($str))
		{
			$str = $this->_escape_str($str);
		}
		elseif (is_bool($str))
		{
			$str = ($str === FALSE) ? 0 : 1;
		}
		elseif (is_null($str))
		{
			$str = 'NULL';
		}
	
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Escape String
	 *
	 * @access	public
	 * @param	string
	 * @param	bool	whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	private function _escape_str($str)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->_escape_str($val);
			}
	
			return $str;
		}
		
		$like = FALSE;
		
		if (strpos(strtolower($str), 'like') !== FALSE)
		{
			$like = TRUE;
		}
	
		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			//@todo  add replace bind_marker while has like
		}
		
		//add '' if has no '';
		if (!preg_match('/\'.*\'/', $str))
		{
			$str = "'".$str."'";
		}
	
		return $str;
	}
}