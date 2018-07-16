<?php
class CountriesController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmCountrySearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmcountrySearch');
		$frm->setAction('countries');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'country_active', array('Inactive', 'Active'), '', '', 'Does not matter');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($country_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($country_id > 0) {
			$country_id = intval($country_id);
			$data = $this->Countries->getCountryById($country_id);
		}
		
		$this->set('frmCountry', $this->getForm($data));
		
		$this->_template->render();
	}	
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();

		$country_id = intval($post['country_id']);
		
		$record = new TableRecord('tbl_countries');	
			
		$record->setFldValue('country_name', $post['country_name']);
		$record->setFldValue('country_active', $post['country_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('countries','addupdate',array($country_id));
			$this->addupdate($country_id);
			return;
		}
		
		if($country_id > 0) {
			if(!$record->update(array('smt'=>'country_id = ?', 'vals'=>array($country_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Country Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Country Added Successfully');
		}
		redirectUser(generateUrl('countries'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmCountries');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','country_id',$data['country_id'],'');
		
		$fld = $frm->addRequiredField('Country Name:', 'country_name',$data['country_name'],'country_name');
		$fld->setUnique('tbl_countries','country_name','country_id','country_id','country_id');
		
		$frm->addSelectBox('Country Status:', 'country_active', array('Inactive','Active'),$data['country_active']);		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('countries', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_countries', 'c');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('c.country_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['country_active'])) $srch->addCondition('c.country_active', '=', intval($post['country_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('c.country_name');
				
		$srch->addMultipleFields(array('c.country_id','c.country_name','c.country_active'));
				
		$rs = $srch->getResultSet();
		
		$this->set('arr_listing',$db->fetch_all($rs));
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize',$pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize; 
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);
		
		//$this->set('canEdit', $canEdit);
		
		//$this->_template->render(false, false);
		
	}
	
	function update_country_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_countries');
		$srch->addCondition('country_id', '=', $id);
		$srch->addFld('country_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['country_active'] == 0) {
			$country_active = 1;
		} else {
			$country_active = 0;
		}
			
		if (!$db->update_from_array('tbl_countries', array('country_active'=>$country_active), array('smt'=>'country_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Country status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($country_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}