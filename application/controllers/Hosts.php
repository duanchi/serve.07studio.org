<?php
/**
 * File    application\controllers\Router.php
 * Desc    Api路由全流程处理模块
 * Manual  svn://svn.vop.com/api/manual/Controller/Router
 * version 1.1.2
 * User    duanchi <http://weibo.com/shijingye>
 * Date    2013-11-23
 * Time    17:38
 */

/**
 * @name    ApiController
 * @author  duanChi <http://weibo.com/shijingye>
 * @desc    API路由控制器
 * @see     http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class HostsController extends Yaf\Controller_Abstract {
	
	public function indexAction() {
		$_md        =   file_get_contents(APPLICATION_PATH . '/public/test.md');
		$_md_handle  =   new \Data\Markdown();

		t($_md_handle->parse($_md));

		return FALSE;
	}

    public function rawAction() {

        $this->getResponse()->setHeader( 'Content-Type', 'text/hosts');
        $this->getResponse()->response();

        \IO\FILE::output(\Yaf\Registry::get('config')
                            ->application
                            ->conf
                            ->hosts_path
                        );

        return FALSE;
    }
}