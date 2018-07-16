<?php
class FaqController extends Controller{

	public $faq_model;
	
	function __construct($model, $controller, $action){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		
		if (!is_object($this->faq_model)) {
			$this->faq_model = new Faq();
		}
		$this->set('leftLink', 'faq');
	}
	
	function default_action(){
		$post = Syspage::getPostedVar();
		$frm = $this->getSearchFrm();
		if(isset($post) && $post['btn_submit'] == 'Submit') {
			$frm->fill($post);
		}
		
		if(isset($post) && $post['sort'] == 'Re Order') {
			array_pop($post);
			$this->faq_model->reOrder($post);
		}
		
        $this->set('frm', $frm);
        $this->_template->render();
	}
	
	protected function getSearchFrm() {
		
		$post = Syspage::getPostedVar();
		$page_id = (isset($post['page']))?$post['page']:1;
		
        $frm = new Form('frmSearch');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable threeCol" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setFieldsPerRow(3);
		$frm->captionInSameCell(true);
		$frm->addHiddenField('', 'page', $page_id, 'page');
        $frm->addTextBox('', 'faq_title','','','placeholder="Search FAQ Title..."');
        /* $frm->addSelectBox('', 'faq_type_id', Applicationconstants::$arr_cats, '', '', 'Please Select User Type', 'faq_type_id'); */
        $frm->addHiddenField('', 'faq_type_id', 1, 'faq_type_id');
        
        $fld4 = $frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit', 'class="opt-button"');
        $fld5 = $frm->addButton('', 'btn_cancel', 'Reset', 'btn_cancel', 'class="opt-button opt-button-orange" onclick="document.location.href=\'\'"');
        $fld4->html_after_field = ' &nbsp; ';
        $fld4->attachField($fld5);
        $frm->setAction(generateUrl('faq'));
        //$frm->setOnSubmit('searchLabels(this); return(false);');
        return $frm;
    }
	
	function form($id) {
        
		$id = intval($id);
        $frm = $this->getForm($id);
        if ($id > 0) {
            $srch = Faq::search();
			$srch->addCondition('faq_id', '=', $id);
			$rs = $srch->getResultSet();
			$db = &Syspage::getDb();
			$data = $db->fetch($rs);
            $frm->fill($data);
        }else{
			$post = Syspage::getPostedVar();
			if(!empty($post)){
				$frm->fill($post);
			}
		}
		
        $this->set('frm', $frm);

        $this->_template->render();
		
    }
	
	function listing($page=0) {
		$post = Syspage::getPostedVar();
		
        $page = (isset($post['page']))?intval($post['page']):1;
        if ($page < 1)
            $page = 1;

        $pagesize = 10;
        
		$srch = Faq::search(array('faq_id', 'faq_type_id', 'faq_title', 'faq_display_order'));
		if(isset($post['faq_type_id'])){
			if(!empty($post['faq_type_id']))
				$srch->addCondition('faq_type_id', '=', $post['faq_type_id']);
		}
		if(isset($post['faq_title'])){
			if($post['faq_title'] != '')
				$srch->addCondition('faq_title', 'LIKE', '%'.$post['faq_title'].'%');
		}
		$srch->addCondition('faq_delete', '!=', 1);
		$srch->addOrder('faq_id', 'DESC');
		
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();
        $db = &Syspage::getDb();
        $this->set('arr_listing', $db->fetch_all($rs));
        $this->set('pages', $srch->pages());
        $this->set('page', $page);
        $this->set('pagesize', $pagesize);
        $this->set('start_record', ($page - 1) * $pagesize + 1);
        $end_record = $page * $pagesize;
        $total_records = $srch->recordCount();
        if ($total_records < $end_record)
            $end_record = $total_records;
        $this->set('end_record', $end_record);
        $this->set('total_records', $total_records);
        $this->_template->render(false, false);
	}
	
    function setup(){
		
		global $msg;
        $post = Syspage::getPostedVar();		
		$frm = $this->getForm();
    	if (!$frm->validate($post)){
    		Message::addErrorMessage($frm->getValidationErrors());
    		redirectUser(generateUrl('faq'));
    	}		
		$data = $post;
        $response = $this->faq_model->setup($data);
		if($response)
			redirectUser(generateUrl('faq'));
		else
			redirectUser(generateUrl('faq', 'form'));
		
    }
    
    function getForm($faq_id){
		global $db;
        $frm = new Form('FrmFaq');	
		$frm->setAction(generateUrl('faq','setup'));
		$arr_CategoryRs = Applicationconstants::$arr_cats;
		
		$frm->setTableProperties("class='formTable edit_form' width='100%'");
		$frm->setJsErrorDisplay('afterfield');
        $frm->addHiddenField('', 'faq_id', $faq_id);
		$frm->addSelectBox('User Type', 'faq_type_id', $arr_CategoryRs ,'' ,'','Select User Type')->requirements()->setRequired();
		$frm->addRequiredField('Question\'s Description', 'faq_title', '', 'faq_title')->requirements()->setRequired();
		$frm->addTextArea('Answer', 'faq_description', '')->requirements()->setRequired();
		$frm->addIntegerField('Display order', 'faq_display_order', '', 'faq_display_order')->requirements()->setRequired();
		
        $fld1=$frm->addSubmitButton('', 'btn_submit', 'Submit','btn_submit','style="cursor:pointer;"');
		$fld2=$frm->addButton('', 'cancel', 'Cancel', '', 'class="cancel_form reset_button" style="margin-left:10px; cursor:pointer;" onclick="location.href=\''.generateUrl('faq').'\'"');
		$fld1->attachField($fld2);
        $frm->setValidatorJsObjectName('faqValidator');
        
        return $frm;
    }
	
	function delete($faq_id){
		if(is_numeric($faq_id) && intval($faq_id)>0){
			$this->faq_model->deletefaq($faq_id);
		} else {
			Message::addErrorMessage('Invalid Request');
		}
		redirectUser(generateUrl('faq'));
	}
}