<?php
class Controller {

	protected $_model;
	protected $_controller;
	protected $_action;
	protected $_template;

	function __construct($model, $controller, $action) {
		
		$this->_controller = $controller;
		$this->_action = $action;
		$this->_model = $model;
		
		if (file_exists(CONF_APPLICATION_PATH . 'models/' . strtolower($model) . '.php')){
			$this->$model = new $model;
		}
		else {
			$this->$model = new Model();
		}
		$this->_template = new Template($controller,$action);
	}

	function set($name,$value) {
		$this->_template->set($name,$value);
	}
	
	function default_action(){
		$this->_template->render();
	}

	function __destruct() {
			//$this->_template->render();
	}

}
