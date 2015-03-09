<?php
/**
 * Created by PhpStorm.
 * User: Chi
 * Date: 2014/9/19
 * Time: 0:17
 */

namespace CORE;

define('KEY_REGISTRY', 1);
define('KEY_STATIC',   2);
define('KEY_CACHE',    4);
define('KEY_MEMORY',   8);
define('KEY_STORAGE', 16);

define('DEFAULT_PREFIX', '_');
define('REGISTRY_KEY_PREFIX'    , 'KEY_');


class KEY {

    private static $_static_handle      =   [];
    private static $_registry_handle    =   [];
    private static $_cache_handle       =   [];
    private static $_memory_handle      =   ['instance'=>NULL,'db'=>0];

    public static function get($_key, $_scope = KEY_REGISTRY, $_prefix = DEFAULT_PREFIX) {

        $_result = FALSE;
        $_handle = self::_instance($_scope, $_prefix);

        switch($_scope) {
            case KEY_STORAGE:

                break;

            case KEY_MEMORY:

                break;

            case KEY_CACHE:

                $_result = $_handle->get($_key);
                break;

            case KEY_STATIC:
                $_result = $_handle[$_key];
                break;

            case KEY_REGISTRY:
            default:
                $_result = \Yaf\Registry::get(REGISTRY_KEY_PREFIX.$_prefix.$_key);
                break;
        }

        return $_result;
    }

    public static function set($_key, $_value, $_scope = KEY_REGISTRY, $_prefix = DEFAULT_PREFIX) {

        $_result = FALSE;
        $_handle = self::_instance($_scope, $_prefix);

        switch($_scope) {
            case KEY_STORAGE:

                break;

            case KEY_MEMORY:

                break;

            case KEY_CACHE:
                $_result = $_handle->set($_key, $_value);
                break;

            case KEY_STATIC:
                $_result = ($_handle[$_key] = $_value);
                break;

            case KEY_REGISTRY:
            default:
                $_result = \Yaf\Registry::set(REGISTRY_KEY_PREFIX.$_prefix.$_key, $_value);
                self::$_registry_handle[$_prefix][$_key] = TRUE;
                break;
        }

        return $_result;
    }

    private static function _instance($_scope, $_prefix = DEFAULT_PREFIX) {
        switch($_scope) {
            case KEY_STORAGE:

                break;

            case KEY_MEMORY:

                if (self::$_memory_handle['instance'] == NULL) {
                    self::$_memory_handle['instance']   =   new \Redis();
                    self::$_memory_handle['instance']->connect('127.0.0.1', 6379);
                }
                break;

            case KEY_CACHE:

                if (!isset(self::$_cache_handle[$_prefix]) || empty(self::$_cache_handle[$_prefix])) {
                    self::$_cache_handle[$_prefix] = new \Yac($_prefix);
                }

                return self::$_cache_handle[$_prefix];
                break;

            case KEY_STATIC:

                if (!isset(self::$_static_handle[$_prefix])) {
                    self::$_static_handle[$_prefix] = [];
                }

                return self::$_static_handle[$_prefix];
                break;

            case KEY_REGISTRY:
            default:

                return self::$_registry_handle[$_prefix];
                break;
        }
    }
}