<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/10
 * Time: 下午3:52
 */

namespace Data;


class Markdown {

	private $__instance     =   NULL;

	public function __construct() {
		$this->__instance   =   new Markdown\ParsedownExtra();
	}

	public function html($_md_string) {
		return $this->__instance->text($_md_string);
	}
}