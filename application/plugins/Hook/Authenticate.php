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

class AuthenticatePlugin extends \Yaf\Plugin_Abstract {

	public function routerShutdown(\Yaf\Request_Abstract $request, \Yaf\Response_Abstract $response) {

		if ($request->controller == 'Ads') {

            $__REQUEST = \Yaf\Registry::get('__REQUEST');

			//AUTHENTICATE START -->
			//应用认证,appkey,appsecret,ip,count from authorize_config.ini
			$__APP = \Process\AuthorizeModel::authenticate($__REQUEST['access-token'], $__REQUEST['client-ip'], $__REQUEST['client-id'], $__REQUEST['client-token']);

			if (empty($__APP) || $__APP == FALSE) {
                //throw new \Exception('AUTHENTICATE_FAILURE');
                \CORE\STATUS::__UNAUTHORIZED__();
            }
            else {
                \Yaf\Registry::set('__IS_AUTHORIZED', TRUE);
                \Yaf\Registry::set('__APP', $__APP);
            }


			//AUTHENTICATE END <--
		}

	}
}