<?php
class CitationController extends Controller{
    
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
		$this->set('frmCitationSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmcitationSearch');
		$frm->setAction('citation');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'citstyle_active', array('Inactive', 'Active'), '', '', 'Does not matter');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($citstyle_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($citstyle_id > 0) $citstyle_id = intval($citstyle_id);
		
		$data = $this->Citation->getCitationById($citstyle_id);
		
		$this->set('frmCitation', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$citstyle_id = intval($post['citstyle_id']);
		
		$record = new TableRecord('tbl_citation_styles');	
			
		$record->setFldValue('citstyle_name', $post['citstyle_name']);
		$record->setFldValue('citstyle_active', $post['citstyle_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('citation','addupdate',array($citstyle_id));
			$this->addupdate($citstyle_id);
			return;
		}
		
		if($citstyle_id > 0) {
			if(!$record->update(array('smt'=>'citstyle_id = ?', 'vals'=>array($citstyle_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Citation Style Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Citation Style Added Successfully');
		}
		redirectUser(generateUrl('citation'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmCitation');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','citstyle_id',$data['citstyle_id'],'citstyle_id');
		
		$fld = $frm->addRequiredField('Citation Style:', 'citstyle_name',$data['citstyle_name'],'citstyle_name');
		$fld->setUnique('tbl_citation_styles','citstyle_name','citstyle_id','citstyle_id','citstyle_id');
		
		$frm->addSelectBox('Citation Style Status:', 'citstyle_active', array('Inactive','Active'),$data['citstyle_active']);
		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('citation', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_citation_styles');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('citstyle_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['citstyle_active'])) $srch->addCondition('citstyle_active', '=', intval($post['citstyle_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('citstyle_name');
				
		$srch->addMultipleFields(array('citstyle_id','citstyle_name','citstyle_active'));
				
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
	
	function update_citation_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_citation_styles');
		$srch->addCondition('citstyle_id', '=', $id);
		$srch->addFld('citstyle_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['citstyle_active'] == 0) {
			$citstyle_active = 1;
		} else {
			$citstyle_active = 0;
		}
			
		if (!$db->update_from_array('tbl_citation_styles', array('citstyle_active'=>$citstyle_active), array('smt'=>'citstyle_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Citation style status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($citstyle_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}