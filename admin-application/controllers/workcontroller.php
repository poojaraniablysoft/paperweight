<?php
class WorkController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$this->set('frmworkSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmworkSearch');
		$frm->setAction('work');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'exfld_active', array('Inactive', 'Active'), '', '', 'Work Field Status');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($work_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($work_id > 0) $work_id = intval($work_id);
		
		$data = $this->Work->getWorkById($work_id);
		
		$this->set('frmwork', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$work_id = intval($post['exfld_id']);

		$record = new TableRecord('tbl_experience_fields');	
			
		$record->setFldValue('exfld_name', $post['exfld_name']);
		$record->setFldValue('exfld_active', $post['exfld_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('work','addupdate',array($work_id));
			$this->addupdate($work_id);
			return;
		}
		
		if($work_id > 0) {
			if(!$record->update(array('smt'=>'exfld_id = ?', 'vals'=>array($work_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Work field Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Work field Added Successfully');
		}
		redirectUser(generateUrl('work'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmwork');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','exfld_id',$data['exfld_id'],'exfld_id');
		
		$fld = $frm->addRequiredField('Work Field Name:', 'exfld_name',$data['exfld_name'],'exfld_name');
		$fld->setUnique('tbl_experience_fields','exfld_name','exfld_id','exfld_id','exfld_id');
		
		$frm->addSelectBox('Work Field Status:', 'exfld_active', array('Inactive','Active'),$data['exfld_active']);		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit');
		
		$frm->setAction(generateUrl('work', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_experience_fields');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('exfld_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['exfld_active'])) $srch->addCondition('exfld_active', '=', intval($post['exfld_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('exfld_name');
				
		$srch->addMultipleFields(array('exfld_id','exfld_name','exfld_active'));
				
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
	
	function update_work_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_experience_fields');
		$srch->addCondition('exfld_id', '=', $id);
		$srch->addFld('exfld_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['exfld_active'] == 0) {
			$exfld_active = 1;
		} else {
			$exfld_active = 0;
		}
			
		if (!$db->update_from_array('tbl_experience_fields', array('exfld_active'=>$exfld_active), array('smt'=>'exfld_id = ?', 'vals'=>array($id)))){
			dieWithError($db->getError());
			
		}
		else {
			$arr = array('status'=>1, 'msg'=>'Work Field status updated!', 'linktext'=>(($exfld_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}