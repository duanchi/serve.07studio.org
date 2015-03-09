<?php
/**
 * Created by PhpStorm.
 * User: Chi
 * Date: 2014/9/27
 * Time: 22:44
 */

namespace CORE;


class STATUS {

    private static $__instance  = NULL;
    private static $__backtrace = [];

    public static function __initialize() {
        $_tmp_status    = get_yaf_config(ADS_STATUS);
        self::$__instance = $_tmp_status;
    }

    public static function set($_signal) {
        self::$__instance       = $_signal;

        return TRUE;
    }

    public static function get() {
        return end(self::$__backtrace);
    }

    public static function backtrace() {
        return self::$__backtrace;
    }

    public static function __callStatic ($_function , array $_arguments) {
        array_push( self::$__backtrace,
                    self::$__instance->{trim($_function, '_')}
        );
    }

} 