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
class ToolsController extends Yaf\Controller_Abstract {
	
	public function indexAction() {
		//$this->forward('Index', 'Tools', 'shadowsocks');
		$this->redirect('/Tools/shadowsocks');
		$this->getView()->hello = 1;
		return TRUE;
	}

	public function shadowsocksAction() {

		$this->getView()->assign(   '_HOSTS',
			\IO\FILE::read(
				\Yaf\Registry::get('config')
					->application
					->conf
					->hosts_path
			)
		);

		return TRUE;
	}

	public function goagentsAction() {

		$this->getView()->assign(   '_HOSTS',
			\IO\FILE::read(
				\Yaf\Registry::get('config')
					->application
					->conf
					->hosts_path
			)
		);

		return TRUE;
	}
}