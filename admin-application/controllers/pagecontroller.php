<?php
class PageController extends Controller {
	
	public function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
	}
	
	function error_404() {
		$this->_template->render(false,false);
    }
	
	function error_504() {
		$this->_template->render(false,false);
    }
}
?>