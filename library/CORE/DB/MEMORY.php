<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/1/27
 * Time: 下午3:29
 */

namespace CORE\DB;


class MEMORY {

    private static $__INSTANCE  =   NULL;
    private static $__DB_INDEX  =   0;

    public  static function get($_key, $_db_index = 0) {
        return self::__instance($_db_index)->get($_key);
    }

    public  static function set($_key, $_value, $_db_index = 0) {
        return self::__instance($_db_index)->set($_key, $_value);
    }

    private static function __instance($__db_index = 0) {
        $_instance              =   self::$__INSTANCE;
        $_db_index              =   self::$__DB_INDEX;

        if ($_instance == NULL) {
            $_host              =   isset(\Yaf\Registry::get('config')->application->db->memory->host) ? \Yaf\Registry::get('config')->application->db->memory->host : '127.0.0.1';
            $_port              =   isset(\Yaf\Registry::get('config')->application->db->memory->port) ? \Yaf\Registry::get('config')->application->db->memory->port : 6379;
            $_instance          =   new \Redis();

            $_instance->connect($_host, $_port);
        }

        if (($_db_index != $__db_index) and $_instance->select($__db_index)) $_db_index =   $__db_index;

        self::$__INSTANCE       =   $_instance;
        self::$__DB_INDEX       =   $_db_index;

        return $_instance;
    }

}