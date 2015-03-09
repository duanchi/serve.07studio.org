<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/9
 * Time: 上午10:04
 */

namespace View\Blitz;


class Plugins {

	private $__blitz_instance       =   NULL;
	private $__config               =   NULL;

	public function __construct($_blitz_instance, $_conf) {
		$this->__blitz_instance     =   $_blitz_instance;
		$this->__config             =   $_conf;
	}

	/*public function __func_strtoupper($_arguments, $_arg_count) {
		return strtoupper($_arguments[0]);
	}*/

	public function __func_inc($_arguments, $_arg_count) {
		if ($_arg_count == 1) {
			$_arguments[1]          =   [];
		}

		return $this->__blitz_instance->include($this->__config->include_path . DIRECTORY_SEPARATOR . $_arguments[0] . $this->__config->include_suffix, $_arguments[1]);
	}
}