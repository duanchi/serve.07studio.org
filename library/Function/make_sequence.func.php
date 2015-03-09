<?php
/**
 * Created by PhpStorm.
 * User: shijy33
 * Date: 13-11-12
 * Time: 17:26
 */

function make_sequence($_prefix = NULL) {
	return $_prefix . str_replace('.', '', microtime(TRUE)) . str_pad(mt_rand(0,9999), 4 ,0);
}