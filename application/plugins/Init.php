<?php
class InitPlugin extends Yaf\Plugin_Abstract {

	function __construct() {
	}
	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function dispatchLoopStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
		/*$this->__init(
						'\View',

						\Yaf\Registry::get('config')->application->view->path.
						DIRECTORY_SEPARATOR.
						$request->controller.
						DIRECTORY_SEPARATOR.
						$request->action.
						\Yaf\Registry::get('config')->application->view->suffix,

						\Yaf\Registry::get('config')->application->view,

						\Yaf\Registry::get('config')->application->view->engine
					);*/

		\CORE\INSTANCE::set(new \View(
			\Yaf\Registry::get('config')->application->view->path.
			DIRECTORY_SEPARATOR.
			$request->controller.
			DIRECTORY_SEPARATOR.
			$request->action.
			\Yaf\Registry::get('config')->application->view->suffix,

			NULL
		));
	}
	
	public function preDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function postDispatch(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	}
	
	public function preResponse(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
	
	}


    private function __init($_instance, $_argument1 = NULL, ...$_arguments) {
        call_user_func([$_instance, '__initialize'], $_argument1, ...$_arguments);
    }
}

?>