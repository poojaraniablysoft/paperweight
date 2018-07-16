<?php
class LanguageController extends Controller{
    
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
		$this->set('frmlangSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmlangSearch');
		$frm->setAction('language');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'lang_active', array('Inactive', 'Active'), '', '', 'Does not matter');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		return $frm;
	}
	
	public function addupdate($lang_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($lang_id > 0) $lang_id = intval($lang_id);
		
		$data = $this->Language->getLanguageById($lang_id);
		
		$this->set('frmlang', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$lang_id = intval($post['lang_id']);
		
		$record = new TableRecord('tbl_languages');	
			
		$record->setFldValue('lang_name', $post['lang_name']);
		$record->setFldValue('lang_active', $post['lang_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('language','addupdate',array($lang_id));
			$this->addupdate($lang_id);
			return;
		}
		
		if($lang_id > 0) {
			if(!$record->update(array('smt'=>'lang_id = ?', 'vals'=>array($lang_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Language Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Language Added Successfully');
		}
		redirectUser(generateUrl('language'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmlang');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setAction(generateUrl('language', 'setup'));
		
		$frm->addHiddenField('Language ID','lang_id',$data['lang_id'],'lang_id');
		
		$fld = $frm->addRequiredField('Language Name:', 'lang_name',$data['lang_name'], 'lang_name');
		$fld->setUnique('tbl_languages', 'lang_name', 'lang_id', 'lang_id', 'lang_id');
		$frm->addSelectBox('Language Status:', 'lang_active', array('Inactive','Active'),$data['lang_active']);
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit');		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_languages');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('lang_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['lang_active'])) $srch->addCondition('lang_active', '=', intval($post['lang_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('lang_name');
				
		$srch->addMultipleFields(array('lang_id','lang_name','lang_active'));
				
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
	
	function update_lang_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_languages');
		$srch->addCondition('lang_id', '=', $id);
		$srch->addFld('lang_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['lang_active'] == 0) {
			$lang_active = 1;
		} else {
			$lang_active = 0;
		}
			
		if (!$db->update_from_array('tbl_languages', array('lang_active'=>$lang_active), array('smt'=>'lang_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Language status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($lang_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}