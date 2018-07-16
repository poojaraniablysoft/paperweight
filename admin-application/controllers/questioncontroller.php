<?php
class QuestionController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmqueSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmqueSearch');
		$frm->setAction('question');
		$frm->setExtra('class="siteForm"');
		$frm->captionInSameCell(true);
		$frm->setFieldsPerRow(3);
		$frm->setTableProperties('class="formTable " width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'quest_active', array('Inactive', 'Active'), '', '', 'Question Status');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		$frm->setOnSubmit('resetPage(this);return true;');
		return $frm;
	}
	
	function addupdate($id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$db = &Syspage::getdb();
		if($id > 0) {
		$id=intval($id);
		
		$srch = new SearchBase('tbl_grammar_questions');
		//$srch->addMultipleFields(array('quest_id','quest_question','quest_active'));
		$srch->addCondition('quest_id','=',$id);
		$rs = $srch->getResultSet();
		
		$this->set('result',$db->fetch($rs));
		}
		
		$post = Syspage::getPostedVar();
		if(isset($post['btn_submit']) && $post['btn_submit']=="Submit") {
			$result['quest_id'] = $post['quest_id'];
			$result['quest_question'] = $post['quest_question'];
			$result['quest_opt1'] = $post['quest_opt1'];
			$result['quest_opt2'] = $post['quest_opt2'];
			$result['quest_opt3'] = $post['quest_opt3'];
			$result['quest_opt4'] = $post['quest_opt4'];
			$result['quest_ans'] = $post['quest_ans'];
			$result['quest_active'] = $post['quest_active'];
			if($result['quest_id'] > 0) {
				$success = $db->update_from_array('tbl_grammar_questions', $result, array('smt'=>'quest_id = ?', 'vals'=>array($result['quest_id'])));
				Message::addMsg('Question Updated Successfully');
			}
			else {
				$success = $db->insert_from_array('tbl_grammar_questions', $result);
				$exfld_id = $db->insert_id();
				Message::addMsg('Question added Successfully');
			}
			if($success) {
				redirectUser(generateUrl('question'));
			}
		}
		$this->set('frmque', $this->getForm());
		$this->_template->render();
	}
	
	protected function getForm(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmque');
		$frm->setExtra('class="siteForm"');
		$frm->setFieldsPerRow(5);
		$frm->captionInSameCell(true);
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_msg');
		$frm->addHiddenField('','quest_id');
		
		$fld = $frm->addTextArea('Question:', 'quest_question');
		//$fld->captionCellExtra ="class='firstChild'";
		$fld->requirements()->setRequired();
		$frm->addTextArea('Option 1:', 'quest_opt1')->requirements()->setRequired();
		$frm->addTextArea('Option 2:', 'quest_opt2')->requirements()->setRequired();
		$frm->addTextArea('Option 3:', 'quest_opt3')->requirements()->setRequired();
		$frm->addTextArea('Option 4:', 'quest_opt4')->requirements()->setRequired();
		
		/* $fld = $frm->addIntegerField('Answer:', 'quest_ans');
		$fld->requirements()->setInt();
		$fld->requirements()->setRange(1,4);
		$fld->requirements()->setCustomErrorMessage('Please enter answer no. 1,2,3 or 4.'); */
		
		$frm->addSelectBox('Answer:', 'quest_ans', array('1'=>'Option 1','2'=>'Option 2','3'=>'Option 3','4'=>'Option 4'), '', '', 'Select Option', 'quest_ans')->requirements()->setRequired();
		
		$frm->addSelectBox('Question Status:', 'quest_active', array('Inactive','Active'),'','','','','');
		
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('question', 'addupdate'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_grammar_questions');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('quest_question', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['quest_active'])) $srch->addCondition('quest_active', '=', intval($post['quest_active']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('quest_question');
				
		$srch->addMultipleFields(array('quest_id','quest_question','quest_active'));
				
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
	
	function update_quest_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_grammar_questions');
		$srch->addCondition('quest_id', '=', $id);
		$srch->addFld('quest_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['quest_active'] == 0) {
			$quest_active = 1;
		} else {
			$quest_active = 0;
		}
			
		if (!$db->update_from_array('tbl_grammar_questions', array('quest_active'=>$quest_active), array('smt'=>'quest_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Question status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($quest_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
}	