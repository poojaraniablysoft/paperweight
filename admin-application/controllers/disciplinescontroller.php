<?php
class DisciplinesController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmdiscSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmdiscSearch');
		$frm->setAction('disciplines');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'discipline_active', array('Inactive', 'Active'), '', '', 'Does not matter');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function addupdate($discipline_id){
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		if ($discipline_id > 0) $discipline_id = intval($discipline_id);
		
		$data = $this->Disciplines->getDisciplinesById($discipline_id);
		$this->set('frmdisc', $this->getForm($data));
		$this->set( 'result', $data );
		
		$this->_template->render();
	}
	
	public function setup() {
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$post = Syspage::getPostedVar();
		
		$discipline_id = intval($post['discipline_id']);
				
		$record = new TableRecord('tbl_disciplines');	
			
		$record->setFldValue('discipline_name', $post['discipline_name']);
		$record->setFldValue('discipline_active', $post['discipline_active']);
		
		$frm = $this->getForm();
		
		if(!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
			foreach ($arr_errors as $val) Message::addErrorMessage($val);
			$this->_template = new Template('disciplines','addupdate',array($discipline_id));
			$this->addupdate($discipline_id);
			return;
		}
		
		if($discipline_id > 0) {
			if(!$record->update(array('smt'=>'discipline_id = ?', 'vals'=>array($discipline_id)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMsg('Discipline Field Updated Successfully');
		}else {
			if(!$record->addNew()) {
				$this->error = $db->getError();
				return false;
			}
			Message::addMsg('Discipline Field Added Successfully');
		}
		redirectUser(generateUrl('disciplines'));
	}
	
	protected function getForm($data){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmdisc');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		
		$frm->addHiddenField('Discipline ID','discipline_id',$data['discipline_id'],'discipline_id');
		
		$fld = $frm->addRequiredField('Discipline Name:','discipline_name',$data['discipline_id'],'discipline_name');
		$fld->setUnique('tbl_disciplines','discipline_name','discipline_id','discipline_id','discipline_id');
		$frm->addSelectBox('Discipline Status:', 'discipline_active', array('Inactive','Active'),$data['discipline_active']);
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('disciplines', 'setup'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_disciplines');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('discipline_name', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['discipline_active'])) $srch->addCondition('discipline_active', '=', intval($post['discipline_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('discipline_name');
				
		$srch->addMultipleFields(array('discipline_id','discipline_name','discipline_active'));
				
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
	
	function update_disciplines_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_disciplines');
		$srch->addCondition('discipline_id', '=', $id);
		$srch->addFld('discipline_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['discipline_active'] == 0) {
			$discipline_active = 1;
		} else {
			$discipline_active = 0;
		}
			
		if (!$db->update_from_array('tbl_disciplines', array('discipline_active'=>$discipline_active), array('smt'=>'discipline_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Discipline status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($discipline_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}