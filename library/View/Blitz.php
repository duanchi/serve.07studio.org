<?php
/**
 * Created by PhpStorm.
 * User: fate
 * Date: 15/2/7
 * Time: 下午10:18
 */

namespace View;

class Blitz extends \Blitz implements Api
{

    private $__template_file        =   '';
    private $__config               =   [];
	private $__plugin               =   NULL;
	private $__request              =   [
											'module'                =>  NULL,
											'controller'            =>  NULL,
											'action'                =>  NULL,
											'method'                =>  'GET',
											'params'                =>  NULL,
											'is_xml_http_request'   =>  FALSE,
										];
    private $__custom_function      =   [
                                            'inc'           =>  TRUE,
                                            //'strtoupper'    =>  TRUE
                                        ];

    function __construct($_template_file = NULL, $_conf = []) {
        parent::__construct($_template_file);
	    $this->__plugin             =   $this->init_plugin($this, $_conf);
        $this->__config             =   $_conf;
    }

    /**
     * Set the path to the templates
     *
     * @param string $path The directory to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {
        $this->__template_file      =   $path;
    }

    /**
     * Retrieve the current template directory
     *
     * @return string
     */
    public function getScriptPath()
    {
        $this->__template_file;
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
        if (is_array($spec)) {
            $this->set($spec);
            return;
        }

        $this->set([$spec=>$value]);
    }

	public function setRequest($_request) {
		$this->__request            =   $_request;
		$this->assign('_REQUEST', $_request);
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
        $this->clean();
    }

    public function display($name, $value = NULL) {

	    $_template_file             =   !empty($this->__template_file) ?
										    $this->__config['path'].
										    DIRECTORY_SEPARATOR.
										    $this->__request['controller'].
										    DIRECTORY_SEPARATOR.
										    $this->__request['action'].
										    $this->__config['suffix']
			                                :
		                                    $this->__template_file;

        $this->load(file_get_contents($_template_file));

        return parent::display();
    }

    public function version() {
        return '0.8.17';
    }

    /**
     * Plugins
     */

	private function init_plugin($_instance, $_conf) {
		return new Blitz\Plugins($_instance, $_conf);
	}

    public function __call($_function_name, $_arguments){
        if (!isset($this->__custom_function[$_function_name])) return FALSE;
        return $this->__plugin->{'__func_' . $_function_name}($_arguments, count($_arguments));
    }

}