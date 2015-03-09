<?php
/**
 * Created by PhpStorm.
 * User: lovemybud
 * Date: 14/12/29
 * Time: 23:56
 */

function make_uuid($prefix = '') {
    $_base_key  = md5(uniqid($prefix . mt_rand(), TRUE));

    $_uuid      =   substr($_base_key, 0, 8) . '-'.
                    substr($_base_key, 8, 4) . '-'.
                    substr($_base_key,12,4) . '-'.
                    substr($_base_key,16,4) . '-'.
                    substr($_base_key,20,12);
    return $_uuid;
}