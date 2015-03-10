<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/2/7
 * Time: 下午9:56
 */

class View implements \Yaf\View_Interface {

    const   VIEW_TYPE_BLITZ     =   'Blitz';
    const   VIEW_TYPE_SMARTY    =   'Smarty';
    const   VIEW_TYPE_NATIVE    =   'Native';

    private $__instance         =   NULL;
    private $__template_file    =   '';
    private $__engine           =   self::VIEW_TYPE_BLITZ;
    private $__config           =   [];

    public function __construct($_template_file, $_conf = NULL, $_spec = NULL) {

        $_config                =   \Yaf\Registry::get('config')->get('application')->get('view');

	    foreach($_config as $key => $node)
		    $_config[$key]      =   $node;

	    $_engine                =   constant('self::VIEW_TYPE_' . strtoupper($_config['engine']));
        $_class_name            =   '\\View\\' . $_engine;

        $this->__instance       =   new $_class_name(NULL, $_config);
        $this->__config         =   $_config;
        $this->_engine          =   $_engine;
        $this->setScriptPath($_template_file);
	    !empty($_spec) ? $this->assign($_spec) : FALSE;
    }

	static public function instance() {
		return self::$__instance;
	}

    /**
     * Return the template engine object
     *
     * @return Blitz
     */
    public function getEngine() {
        return $this->__engine;
    }

    /**
     * Set the path to the templates
     *
     * @param string $path The directory to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {

        $this->__instance->setScriptPath($path);
    }

    /**
     * Retrieve the current template directory
     *
     * @return string
     */
    public function getScriptPath()
    {
        $this->__instance->getScriptPath();
    }

    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing
     * an array of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or
     * array of key => value pairs)
     * @param mixed $value (Optional) If assigning a named variable,
     * use this as the value.
     * @return void
     */
    public function assign($spec, $value = null) {
        return $this->__instance->assign($spec, $value);
    }

    public function __set($property, $value) {
        return $this->assign($property, $value);
    }

	public function setRequest($_request) {
		return $this->__instance->setRequest($_request);
	}

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via
     * {@link assign()} or property overloading
     * ({@link __get()}/{@link __set()}).
     *
     * @return void
     */
    public function clearVars() {
        return $this->__instance->clearVars();
    }

    /**
     * Processes a template and returns the output.
     *
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name, $value = NULL) {
        return $this->display($name, $value = NULL);
    }

    public function display($name, $value = NULL) {
        return $this->__instance->display($name, $value);
    }

    public function version() {
        return $this->__instance->version();
    }
}