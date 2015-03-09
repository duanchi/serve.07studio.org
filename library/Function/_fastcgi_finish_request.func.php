<?php
/**
 * Created by PhpStorm.
 * User: 翅
 * Date: 2014/9/17
 * Time: 10:24
 */

function _fastcgi_finish_request() {
    if (!IS_WINDOWS) {
        fastcgi_finish_request();
    }
}