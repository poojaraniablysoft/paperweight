<?php
class EmailTemplateController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->set('leftLink', 'emailtemplate');
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmEmptplSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmEmptplSearch');
		$frm->setAction('emailtemplate');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('Keyword (in name)', 'keyword');
		
		
		
		$frm->addHiddenField('', 'page', 1);
		$frm->addSubmitButton('', 'btn_submit', 'Search');
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	function addupdate($id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$db = &Syspage::getdb();
		if($id > 0) {
		$id=intval($id);
		
		$srch = new SearchBase('tbl_email_templates');
		$srch->addMultipleFields(array('tpl_id','tpl_subject', 'tpl_body', 'tpl_replacements','tpl_active'));
		$srch->addCondition('tpl_id','=',$id);
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		//$row['tpl_body'] = nl2br($row['tpl_body']);
		$row['tpl_replacements'] = nl2br($row['tpl_replacements']);
		
		$this->set('result',$row);
		}
		
		$post = Syspage::getPostedVar();
		if(isset($post['btn_submit']) && $post['btn_submit']=="Submit") {
			$result['tpl_id'] = $post['tpl_id'];
			$result['tpl_subject'] = $post['tpl_subject'];
			$result['tpl_body'] = $post['tpl_body'];			
			$result['tpl_active'] = $post['tpl_active'];			
			if($result['tpl_id'] > 0) {
				$success = $db->update_from_array('tbl_email_templates', $result, array('smt'=>'tpl_id = ?', 'vals'=>array($result['tpl_id'])));
				Message::addMsg('Email Template Updated Successfully');
			}
			if($success) {
				redirectUser(generateUrl('emailtemplate'));
			}
		}
		
		$frm = $this->getForm();
		$frm->fill($row);
		
		$this->set('frmEmailTemplate', $frm);
		$this->_template->render();
	}
	
	protected function getForm(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmEmailTemplates');
		
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setExtra('class="siteForm" width="100%"');
		
		$frm->addHiddenField('','tpl_id');

		$frm->addRequiredField('Email Template Subject:', 'tpl_subject','','','size="75"');
		$frm->addHtmlEditor('Email Template Body:', 'tpl_body');
		$frm->addHTML('Email Template Replacement:', 'tpl_replacements');	
		$frm->addSelectBox('Email Template Status', 'tpl_active', array('0'=>'Inactive','1'=>'Active'));		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('emailtemplate', 'addupdate'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_email_templates');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('tpl_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('tpl_name');
				
		$srch->addMultipleFields(array('tpl_id','tpl_name','tpl_subject','tpl_active'));
				
		$rs = $srch->getResultSet();
		
		$this->set('arr_listing',$db->fetch_all($rs));
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize', $pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);
		
		//$this->set('canEdit', $canEdit);
		
		//$this->_template->render(false, false);
		
	}
	
	function update_template_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_email_templates');
		$srch->addCondition('tpl_id', '=', $id);
		$srch->addFld('tpl_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['tpl_active'] == 0) {
			$tpl_active = 1;
		} else {
			$tpl_active = 0;
		}
			
		if (!$db->update_from_array('tbl_email_templates', array('tpl_active'=>$tpl_active), array('smt'=>'tpl_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Email template status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($tpl_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}	
	
}