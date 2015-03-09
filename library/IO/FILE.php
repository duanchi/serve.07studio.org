<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/3/7
 * Time: 上午8:44
 */

namespace IO;


class FILE {

    static public function read ($_file_name) {
        return file_exists($_file_name) ? file_get_contents($_file_name) : FALSE;
    }

    static public function write ($_file_name, $_data = NULL, $_mode = FILE_APPEND) {

        $_file_exists       =   file_exists($_file_name);
        do {

            if (!$_file_exists and empty($_data)) {
                return touch($_file_name);
            }

            return file_put_contents($_file_name, $_data, $_mode);

        } while (FALSE);
    }

    static public function touch($_file_name) {
        return touch($_file_name);
    }

    static public function output($_file_name) {
        return file_exists($_file_name) ? readfile($_file_name) : FALSE;
    }
}