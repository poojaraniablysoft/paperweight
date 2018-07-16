<?php
class CustomhtmlController extends Controller{
	function __construct($model, $controller, $action){
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
	}
	
	function default_action(){
	
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		
		$this->set('frmCust', $this->getForm());		
		$this->_template->render();
	}
	
	function update(){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();		
		$frm = $this->getForm();
			
		/* @var $db Database */
		$db = &Syspage::getdb();
		foreach ($post as $key=>$val){			
			$db->update_from_array('tbl_configurations', array('conf_val'=>$val), array('smt'=>'conf_name = ?', 'vals'=>array($key)));
		}
		
		Message::addMessage('Html Tags Updated');
		
		redirectUser(generateUrl('customhtml'));
	}
	
	protected function getForm(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		
		$frm = new Form('frmCustomHtml');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarWith('caption');
		$frm->setJsErrorDiv('common_msg');
		//$frm->setFieldsPerRow(2);
		$frm->setLeftColumnProperties('width="30%"');
		
		$frm->addHTML('<h2>Header: Custom HTML Tags</h2>');		

		$frm->addTextArea('Custom HTML for header', 'custom_html_head', CUSTOM_HTML_HEAD,'custom_html_head', 'style="width:400px;"');
				
		$frm->addHTML('<h2>Footer: Custom HTML Tags</h2>','','');
		$frm->addTextArea('Custom HTML for footer', 'custom_html_footer', CUSTOM_HTML_FOOTER,'custom_html_footer', 'style="width:400px;"');			
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('customhtml', 'update'));
		
		return $frm;
	}
}