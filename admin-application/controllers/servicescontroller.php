<?php
class ServicesController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmservicesSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmservicesSearch');
		$frm->setAction('services');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'service_active', array('Inactive', 'Active'), '', '', 'Service Field Status');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($service_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($service_id > 0) $service_id = intval($service_id);
		
		$data = $this->Services->getServicesById($service_id);
		
		$this->set('frmservices', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$service_id = intval($post['service_id']);
				
		$record = new TableRecord('tbl_service_fields');	
			
		$record->setFldValue('service_name', $post['service_name']);
		$record->setFldValue('service_active', $post['service_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('services','addupdate',array($service_id));
			$this->addupdate($service_id);
			return;
		}
		
		if($service_id > 0) {
			if(!$record->update(array('smt'=>'service_id = ?', 'vals'=>array($service_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Service Field Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Service Field Added Successfully');
		}
		redirectUser(generateUrl('services'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmservices');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','service_id',$data['service_id'],'service_id');
		
		$fld = $frm->addRequiredField('Service Field Name:', 'service_name',$data['service_name'],'service_name');
		$fld->setUnique('tbl_service_fields','service_name','service_id','service_id','service_id');
		$frm->addSelectBox('Service Field Status:', 'service_active', array('Inactive','Active'),$data['service_active']);
		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('services', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_service_fields');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('service_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['service_active'])) $srch->addCondition('service_active', '=', intval($post['service_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('service_name');
				
		$srch->addMultipleFields(array('service_id','service_name','service_active'));
				
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
	
	function update_services_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_service_fields');
		$srch->addCondition('service_id', '=', $id);
		$srch->addFld('service_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['service_active'] == 0) {
			$service_active = 1;
		} else {
			$service_active = 0;
		}
			
		if (!$db->update_from_array('tbl_service_fields', array('service_active'=>$service_active), array('smt'=>'service_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Services Field status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($service_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}