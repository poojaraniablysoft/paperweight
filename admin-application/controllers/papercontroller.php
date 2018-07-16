<?php
class PaperController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmpaptypeSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmpaptypeSearch');
		$frm->setAction('paper');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'paptype_active', array('Inactive', 'Active'), '', '', 'Paper Type Status');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($paptype_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($paptype_id > 0) $paptype_id = intval($paptype_id);
		
		$data = $this->Paper->getPaperById($paptype_id);
		
		$this->set('frmpaptype', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$paptype_id = intval($post['paptype_id']);
		
		$record = new TableRecord('tbl_paper_types');	
			
		$record->setFldValue('paptype_name', $post['paptype_name']);
		$record->setFldValue('paptype_active', $post['paptype_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('paper','addupdate',array($paptype_id));
			$this->addupdate($paptype_id);
			return;
		}
		
		if($paptype_id > 0) {
			if(!$record->update(array('smt'=>'paptype_id = ?', 'vals'=>array($paptype_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Paper Type Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Paper Type Added Successfully');
		}
		redirectUser(generateUrl('paper'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmpaptype');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','paptype_id',$data['paptype_id'],'paptype_id');
		
		$fld = $frm->addRequiredField('Paper Type Name:', 'paptype_name',$data['paptype_name'],'paptype_name');
		$fld->setUnique('tbl_paper_types','paptype_name','paptype_id','paptype_id','paptype_id');
		
		$frm->addSelectBox('Paper Type Status:', 'paptype_active', array('Inactive','Active'),$data['paptype_active']);		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('paper', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_paper_types');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('paptype_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['paptype_active'])) $srch->addCondition('paptype_active', '=', intval($post['paptype_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('paptype_name');
				
		$srch->addMultipleFields(array('paptype_id','paptype_name','paptype_active'));
				
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
	
	function update_paptype_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_paper_types');
		$srch->addCondition('paptype_id', '=', $id);
		$srch->addFld('paptype_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['paptype_active'] == 0) {
			$paptype_active = 1;
		} else {
			$paptype_active = 0;
		}
			
		if (!$db->update_from_array('tbl_paper_types', array('paptype_active'=>$paptype_active), array('smt'=>'paptype_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Paper Type status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($paptype_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}