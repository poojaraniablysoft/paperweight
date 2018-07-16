<?php
class AcademicController extends Controller{
    
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
		$this->set('frmAcademicSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmAcademicSearch');
		$frm->setAction('academic');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'deg_active', array('Inactive', 'Active'), '', '', 'Does not matter');
			
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($deg_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($deg_id > 0) $deg_id = intval($deg_id);
		
		$data = $this->Academic->getAcademicById($deg_id);
		
		$this->set('frmAcademic', $this->getForm($data));
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$deg_id = intval($post['deg_id']);
				
		$record = new TableRecord('tbl_academic_degrees');	
			
		$record->setFldValue('deg_name', $post['deg_name']);
		$record->setFldValue('deg_active', $post['deg_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('academic','addupdate',array($deg_id));
			$this->addupdate($deg_id);
			return;
		}
		
		if($deg_id > 0) {
			if(!$record->update(array('smt'=>'deg_id = ?', 'vals'=>array($deg_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Academic Degree Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Academic Degree Added Successfully');
		}
		redirectUser(generateUrl('academic'));
	}
	
	protected function getForm($data){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmAcademic');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('','deg_id',$data['deg_id'],'deg_id');
		
		$fld = $frm->addRequiredField('Academic Degree Name:', 'deg_name',$data['deg_name'],'deg_name');
		$fld->setUnique('tbl_academic_degrees','deg_name','deg_id','deg_id','deg_id');
		
		$frm->addSelectBox('Academic Degree Status:', 'deg_active', array('Inactive','Active'),$data['deg_active']);
				
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('academic', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_academic_degrees');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('deg_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['deg_active'])) $srch->addCondition('deg_active', '=', intval($post['deg_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('deg_display_order');
				
		$srch->addMultipleFields(array('deg_id','deg_name','deg_display_order','deg_active'));
				
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
	
	function update_academic_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_academic_degrees');
		$srch->addCondition('deg_id', '=', $id);
		$srch->addFld('deg_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['deg_active'] == 0) {
			$deg_active = 1;
		} else {
			$deg_active = 0;
		}
			
		if (!$db->update_from_array('tbl_academic_degrees', array('deg_active'=>$deg_active), array('smt'=>'deg_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Academic Degree status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($deg_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
	
	function reorder_menu() {
		$i=1;
		$db = &Syspage::getdb();
		$record = new TableRecord('tbl_academic_degrees');
		
		foreach ($_POST['degree_listing'] as $key=>$value) {
			$record->setFldValue('deg_display_order', $i);
			$record->update('deg_id=' . intval($value));
			$i++;
			
			if($db->rows_affected() > 0){
				$Count_rows = $db->rows_affected();
				$Count_rows++;
			}
		}
		
		$check_blank = array();
		$check_blank = $_POST['degree_listing'];
		
		if ($check_blank['1'] != "") {
			if ($i < 4) {			
				Message::addErrorMessage('Only one record exists.');				
			} else if($Count_rows==0) {			
				Message::addMessage('Drag the record to change the order.');			
			} else {			
				Message::addMessage('Display order updated.');			
			}
		} else {
			Message::addErrorMessage('No record found.');
		}
		echo Message::getHtml();
	}
}