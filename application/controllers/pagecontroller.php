<?php
class PageController extends LegitController {
	
	public function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
	}
	 
	function error_403() {
		$this->_template->render(false,false);
    }
	
	function error_404() {
		$this->_template->render(false,false);
    }
	
	function error_504() {
		$this->_template->render(false, false);
    }
	
	function deactivated() {
		$this->_template->render(false,false);
    }
}
?>