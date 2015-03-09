<?php
/**
 * Created by PhpStorm.
 * User: duanchi
 * Date: 14/12/24
 * Time: 下午5:43
 */
namespace IO;

/**
 * Class HTTP
 * @package IO
 *
 * @todo change throw exception to class level error code.
 */

class HTTP
{
    private static $_instances              =   [];
    private static $_requests               =   [];

    CONST SEPARATOR                         =   ' ';
    CONST CR                                =   "\r";
    CONST LF                                =   "\n";
    CONST CRLF                              =   self::CR . self::LF;
    CONST CONNECT_TIMEOUT                   =   0.5;
    CONST READ_TIMEOUT                      =   0.6;
    CONST CHUNK_FOOTER                      =   self::CRLF . '0' . self::CRLF;

    public  static function add_request($_options = []) {

        $_http_option                       =   [
                                                    'method'        =>  HTTP_GET,
                                                    'version'       =>  'HTTP/1.1',
                                                    'timeout'       =>  10,
                                                    'request-data'  =>  NULL,
                                                    'host'          =>  '',
                                                    'uri'           =>  '',
                                                    'headers'       =>  []
                                                ];
        $__RESULT                           =   make_uuid($_http_option['method']);
        $_CRLF                              =   self::CRLF;
        $_SEPARATOR                         =   self::SEPARATOR;

        foreach ($_http_option as $_key => $_value) isset($_options[$_key]) ? $_http_option[$_key] = $_options[$_key] : FALSE;

        //CHECK OPTOINS
        $_REQUEST_URI                       =   parse_url($_http_option['uri']);

        /*
         * CHECK NESS OPTONS
         *
         * */

        switch($_http_option['method']) {
            case HTTP_GET   : $_http_option['method']   =   'GET'     ; break;
            case HTTP_POST  : $_http_option['method']   =   'POST'    ; break;
            case HTTP_DELETE: $_http_option['method']   =   'DELETE'  ; break;
            case HTTP_HEAD  : $_http_option['method']   =   'HEAD'    ; break;
            case HTTP_PUT   : $_http_option['method']   =   'PUT'     ; break;
            case HTTP_PATCH : $_http_option['method']   =   'PATCH'   ; break;
        }

        $_request_path                      =   (   isset($_REQUEST_URI['path'])
                                                    and
                                                    !empty($_REQUEST_URI['path'])
                                                ) ?
                                                    $_REQUEST_URI['path'] : '/';

        //PACKAGE HTTP(S) REQUEST LINE
        $_socket_package                    =   $_http_option['method'] .
                                                $_SEPARATOR .
                                                $_request_path .
                                                $_SEPARATOR .
                                                $_http_option['version'] .
                                                $_CRLF;

        //PACKAGE HTTP(S) REQUEST HEADER
        $_socket_package                   .=   'Host: '.$_REQUEST_URI['host'] . $_CRLF;

        if (!empty($_http_option['headers'])) {
            foreach($_http_option['headers'] as $_header)
                $_socket_package           .=   $_header . $_CRLF;
        }

        $_socket_package                   .=    $_CRLF;


        self::$_requests[$__RESULT]         =   [
                                                    'host'      => $_http_option['host'],
                                                    'port'      => (    isset($_REQUEST_URI['port'])
                                                                            and
                                                                        !empty($_REQUEST_URI['port'])
                                                                    ) ?
                                                                    $_REQUEST_URI['port'] : 80,
                                                    'timeout'   => '10',
                                                    'package'   => $_socket_package
                                                ];
        //var_dump(self::$_requests);
        return $__RESULT;
    }

    public  static function handle() {

        $__RESULT                           =   FALSE;
        $_socket_instances                  =   [];
        $_connect_timeout                   =   self::CONNECT_TIMEOUT;
        $_read_timeout                      =   self::READ_TIMEOUT;

        if (empty(self::$_requests)) ;
        else {
            foreach (self::$_requests as $_key => $_request) {

                $_socket_handle             =   new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
                $_connect_status            =   $_socket_handle->connect($_request['host'], $_request['port'], $_connect_timeout, 0);

                if(!$_connect_status) {
                    file_put_contents(APPLICATION_PATH. '/cache/cache.log', "Connect Server fail.errCode=".$_socket_handle->errCode."\r\n\r\n\r\n", FILE_APPEND);
                    exit();
                } else {
                    $_socket_handle->send($_request['package']);
                    $_socket_instances[$_key]=  $_socket_handle;
                }
            }

            while(!empty($_socket_instances)) {

                $_write                     =   [];
                $_error                     =   [];
                $_client_left               =   swoole_client_select($_socket_instances, $_write, $_error, $_read_timeout);

                if($_client_left > 0) {

                    foreach($_socket_instances as $_key => $_instance) {
                        $__RESULT[$_key]                = self::execute_receive($_instance);
                        unset($_socket_instances[$_key]);
                    }
                }
            }
        }

        return $__RESULT;
    }

    private static function execute_receive($_instance) {
        $__RESULT                           =   [
                                                    'line'      => '',
                                                    'header'    => [],
                                                    'version'   => '',
                                                    'status'    => '',
                                                    'body'      => '',
                                                    'body-length'   => 0
                                                ];
        $_GZIPPED                           =   FALSE;
        //EXPLODE STREAM HEADER AND BODY
        $_tmp_stream                        =   explode(self::CRLF.self::CRLF, $_instance->recv(), 2);


        if (count($_tmp_stream) == 2) {

            $_tmp_response_header           =   $_tmp_stream[0];
            $_tmp_response_body             =   $_tmp_stream[1];

        } else \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

        $__RESULT                           =   array_merge(
                                                    $__RESULT,
                                                    self::execute_header($_tmp_response_header)
                                                );

        // COMPLETE HTTP RESPONSE

        $__RESULT                           =   array_merge(
                                                    $__RESULT,
                                                    self::execute_response_package($_tmp_response_body, $_instance, $__RESULT['header'])
                                                );

        $_GZIPPED                           =   (
                                                    isset($__RESULT['header']['content-encoding'])
                                                    and
                                                    $__RESULT['header']['content-encoding'] == 'gzip'
                                                ) ?
                                                    TRUE : FALSE;

        if ($_GZIPPED) {
            $__RESULT['body']               =   self::decompress($__RESULT['body']);
            $__RESULT['body-length']        =   strlen($__RESULT['body']);
        }

        return $__RESULT;
    }

    private static function execute_header($_stream) {

        $__RESULT                           =   [
                                                    'line'      => '',
                                                    'header'    => [],
                                                    'version'   => '',
                                                    'status'    => '',
                                                    'status-code'   =>  '',
                                                ];
        $_tmp_header                        =   explode(self::CRLF, $_stream);
        $__RESULT['line']                   =   array_shift($_tmp_header);
        $_tmp_line                          =   explode(' ', $__RESULT['line'], 3);

        if (count($_tmp_line) == 3) {

            $__RESULT['version']            =   $_tmp_line[0];
            $__RESULT['status-code']        =   $_tmp_line[1];
            $__RESULT['status']             =   $_tmp_line[2];

        } else \CORE\STATUS::__MALFORMED_RESPONSE__(EXIT);

        foreach ($_tmp_header as $_value) {

            $_tmp_header                    =   explode(': ', $_value, 2);

            if (count($_tmp_header) == 2) {
                $_header_key                =   strtolower($_tmp_header[0]);

                do {
                    if (
                        isset($__RESULT['header'][$_header_key])
                        and
                        is_array($__RESULT['header'][$_header_key])
                    ) {
                        $__RESULT['header'][$_header_key][] =   $_tmp_header[1];

                        break;
                    }
                    if (
                        isset($__RESULT['header'][$_header_key])
                        and
                        !is_array($__RESULT['header'][$_header_key])
                    ) {
                        $__RESULT['header'][$_header_key]   =   [
                                                                    $__RESULT['header'][$_header_key],
                                                                    $_tmp_header[1]
                                                                ];

                        break;
                    }
                    if (!isset($__RESULT['header'][$_header_key])) {
                        $__RESULT['header'][$_header_key]   =   $_tmp_header[1];

                        break;
                    }
                } while (TRUE);

            } else {
                $__RESULT['header'][strtolower($_tmp_header[0])]   =   strtolower($_tmp_header[0]);
            }

        }

        return $__RESULT;
    }

    private static function execute_response_package($_stream, $_sock_instance, $_header) {

        $__RESULT                           =   [
                                                    'body'          =>  $_stream,
                                                    'body-length'   =>  0
                                                ];

        $__KEEP_ALIVE                       =   (   isset($_header['connection'])
                                                    and
                                                    strtolower($_header['connection']) == 'keep-alive'
                                                ) ?
                                                    1 : 0;
        $__CHUNKED                          =   (   isset($_header['transfer-encoding'])
                                                    and
                                                    strtolower($_header['transfer-encoding']) == 'chunked'
                                                ) ?
                                                    2 : 0;
        $__CONTENT_LENGTH                   =   isset($_header['content-length'])
                                                  ?
                                                    $_header['content-length'] : FALSE;


        switch($__CHUNKED + $__KEEP_ALIVE) {

            case    3://$__CHUNKED + $__KEEP_ALIVE

                execute_response_keep_alive_and_chunked :

                if (!preg_match('/[\r\n]0[\r\n]/', $__RESULT['body'])) {

                    $__RESULT['body']      .=   $_sock_instance->recv();
                    goto execute_response_keep_alive_and_chunked;

                } else {

                    list ($__RESULT['body'], $__RESULT['chunk-footer']) =   self::parse_chunk_footer($__RESULT['body']);

                }
                $__RESULT['body']           =   preg_replace('/[\r\n]?[0-9A-Fa-f]+[\r\n]/', '', $__RESULT['body']);

                break;

            case    2://$__CHUNKED

                execute_response_chunked :

                if ($_sock_instance != FALSE) {

                    $__RESULT['body']      .=   $_sock_instance->recv();;
                    goto execute_response_chunked;

                }

                $__RESULT['body']           =   preg_replace('/[\r\n]?[0-9A-Fa-f]+[\r\n]/', '', $__RESULT['body']);

                break;

            case    1://$__KEEP_ALIVE

                if ($__CONTENT_LENGTH !== FALSE) {

                    $__RESULT['body']       =   $_stream;
                    $_tmp_length            =   strlen($_stream);
                    $_tmp_stream            =   '';

                    execute_response_with_content_length:

                    if ($__CONTENT_LENGTH > $_tmp_length) {



                        $_tmp_stream        =   $_sock_instance->recv();
                        $_tmp_length       +=   strlen($_tmp_stream);
                        $__RESULT['body']  .=    $_tmp_stream;

                        goto execute_response_with_content_length;
                    }

                    $__RESULT['body-length']=   $_tmp_length;
                }

                break;

            case    0://$__NONE

                $__RESULT['body']           =   $_stream;
                $__RESULT['body-length']    =   strlen($_stream);
                break;
        }

        $__RESULT['body-length']            =   strlen($__RESULT['body']);

        return $__RESULT;
    }

    private static function decompress($_stream, $_compress_type = COMPRESS_TYPE_GZIP) {
        $__RESULT                           =   FALSE;

        switch ($_compress_type) {
            case COMPRESS_TYPE_ZIP:

                break;

            case COMPRESS_TYPE_GZIP:
            default:
                $__RESULT                   =   gzdecode($_stream);
                break;
        }

        return $__RESULT;
    }

    private static function parse_chunk_footer($_stream) {
        $__RESULT                           =   explode(self::CHUNK_FOOTER, $_stream, 2);

        return $__RESULT;
    }
}