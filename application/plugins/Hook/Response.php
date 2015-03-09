<?php
/**
 * File    application\plugin\Ads.php
 * Desc    请求预处理插件模块
 * Manual  svn://svn.vop.com/api/manual/plugin/Process
 * version 1.0.0
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-22
 * Time    20:36
 */
namespace Hook;

class ResponsePlugin extends \Yaf\Plugin_Abstract {

    public function dispatchLoopShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

        if ($request->controller == 'Ads') {

            $__REQUEST          =   \Yaf\Registry::get('__REQUEST');
            $__RESPONSE         =   \Yaf\Registry::get('__RESPONSE');
            $__ECHO             =   '';

            switch ($__RESPONSE['CONTENT-TYPE']) {
                case TYPE_JSONP:
                    $_result = ($__RESPONSE['CALLBACK'] == NULL ? '' : $__RESPONSE['CALLBACK']) .'('.json_encode($__RESPONSE['DATA'], JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE).');';
                    break;

                case TYPE_MSGPACK:
                    $_ECHO = msgpack_pack($__RESPONSE['DATA']);
                    break;

                case TYPE_JSON:
                    $_ECHO = json_encode($__RESPONSE['DATA'], JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
                    break;

                default:

                    break;
            }

            \CORE\RESPONSE::initialize($response, RESPONSE_TYPE_YAF);
            \CORE\RESPONSE::set($__RESPONSE['HEADER'], RESPONSE_HEADER);
            \CORE\RESPONSE::set(               $_ECHO, RESPONSE_BODY  );
        }

    }
} 