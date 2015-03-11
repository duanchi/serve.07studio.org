<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/11
 * Time: 下午3:58
 */

namespace Data\Markdown;


class Serialdown {

	public function __construct() {

	}

	protected $__block_tags             =   [
		'* '    =>  'unordered-list'
	];

	protected $__inline_tags            =   [
		'[.*](.*)'  =>  'link'
	];

	public function serialize($_markdown_string) {
		$_markdown_lines                =   explode(
														"\n",
														trim(
																str_replace(
																				["\r\n", "\r"],
																				"\n",
																				$_markdown_string
																),
																"\n"
														)
													);

		$_markdown_array                =   $this->lines($_markdown_lines);
	}

	private function lines($_markdown_lines) {
		$_lines                         =   [];


		return $_lines;
	}
}