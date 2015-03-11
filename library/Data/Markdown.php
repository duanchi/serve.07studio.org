<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/10
 * Time: 下午3:52
 */

namespace Data;


class Markdown {

	const METHOD_PARSEDOWN                      =   'Parsedown';
	const METHOD_SERIALDOWN                     =   'Serialdown';

	static private $__parsedown_instance        =   NULL;
	static private $__markdown_instance         =   NULL;

	static protected function __instance($_scope = self::METHOD_PARSEDOWN) {
		switch($_scope) {
			case self::METHOD_PARSEDOWN:
				if (self::$__parsedown_instance == NULL) self::$__parsedown_instance    =   new Markdown\ParsedownExtra();
				break;

			case self::METHOD_MARKDOWN:
				if (self::$__markdown_instance == NULL) self::$__markdown_instance      =   new Markdown\Markdown();
				break;
		}

	}

	public function html($_md_string) {
		return $this->__instance(self::METHOD_PARSEDOWN)->text($_md_string);
	}

	public function serialize($_md_string) {
		return $this->__instance(self::METHOD_SERIALDOWN)->serialize($_md_string);
	}
}