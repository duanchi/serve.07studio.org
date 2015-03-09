<?php
class SecurityPlugin extends Yaf\Plugin_Abstract {

	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {

		if (!get_magic_quotes_gpc()) {
			foreach ($_GET as $k => $v) $_GET[$k] = addslashes($v);
		}

	}

}