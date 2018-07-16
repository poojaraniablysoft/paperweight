<?php
class HowitworksController extends Controller{

	public $howitworks_model;
	
	function __construct($model, $controller, $action){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		
		if (!is_object($this->howitworks_model)) {
			$this->howitworks_model = new Howitworks();
		}
		$this->set('leftLink', 'howitworks');
	}
	
	function default_action(){
		$post = Syspage::getPostedVar();
		$frm = $this->getSearchFrm();
		if(isset($post) && $post['btn_submit'] == 'Submit') {
			$frm->fill($post);
		}
		
		if(isset($post) && $post['sort'] == 'Re Order') {
			array_pop($post);
			$this->howitworks_model->reOrder($post);
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
        $frm->addTextBox('', 'step_title','','','placeholder="Search Step Title..."');
        /* $frm->addSelectBox('', 'step_type_id', Applicationconstants::$arr_cats, '', '', 'Please Select User type', 'step_type_id'); */
        $frm->addHiddenField('', 'step_type_id', 1, 'step_type_id');
        
        $fld4 = $frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit', 'class="opt-button"');
        $fld5 = $frm->addButton('', 'btn_cancel', 'Reset', 'btn_cancel', 'class="opt-button opt-button-orange" onclick="document.location.href=\'\'"');
        $fld4->html_after_field = ' &nbsp; ';
        $fld4->attachField($fld5);
        $frm->setAction(generateUrl('howitworks'));
        //$frm->setOnSubmit('searchLabels(this); return(false);');
        return $frm;
    }
	
	function form($id) {
        
		$id = intval($id);
        $frm = $this->getForm($id);
        if ($id > 0) {
            $srch = howitworks::search();
			$srch->addCondition('step_id', '=', $id);
			$rs = $srch->getResultSet();
			$db = &Syspage::getDb();
			$data = $db->fetch($rs);
			
			$imageUrl = generateUrl('image', 'step', array( 'thumb', $data['step_image'] ));
			$data['show_step_image'] = '<div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';			
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
        
		$srch = Howitworks::search(array('step_id', 'step_type_id', 'step_title', 'step_image', 'step_display_order'));
		if(isset($post['step_type_id'])){
			if(!empty($post['step_type_id']))
				$srch->addCondition('step_type_id', '=', $post['step_type_id']);
		}
		if(isset($post['step_title'])){
			if($post['step_title'] != '')
				$srch->addCondition('step_title', 'LIKE', '%'.$post['step_title'].'%');
		}
		$srch->addCondition('step_delete', '!=', 1);
		$srch->addOrder('step_id', 'DESC');
		
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
		$step_id = (isset($post['step_id']))?intval($post['step_id']):0;
		$frm = $this->getForm($step_id);
		$data = $post;
		
		if( $_FILES['step_image']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedStepFiles($_FILES, $image_names)){
				$data['step_image'] = $image_names;
			}
		}
		if (!$frm->validate($data)){
    		Message::addErrorMessage($frm->getValidationErrors());
    		redirectUser(generateUrl('howitworks', 'form'));
    	}
		
        $response = $this->howitworks_model->setup($data);
		if($response)
			redirectUser(generateUrl('howitworks'));
		else
			redirectUser(generateUrl('howitworks', 'form'));
		
    }
    
    function getForm($step_id){
		global $db;
        $frm = new Form('Frmhowitworks');	
		$frm->setAction(generateUrl('howitworks','setup'));
		$arr_CategoryRs = Applicationconstants::$arr_cats;
		
		$frm->setTableProperties("class='formTable edit_form' width='100%'");
		$frm->setJsErrorDisplay('afterfield');
        $frm->addHiddenField('', 'step_id', $step_id);
		$frm->addSelectBox('User Type', 'step_type_id', $arr_CategoryRs ,'' ,'','Select User type')->requirements()->setRequired();
		$frm->addRequiredField('Step Title', 'step_title', '', 'step_title')->requirements()->setRequired();
		$frm->addTextArea('Step Description', 'step_description', '')->requirements()->setRequired();
		$frm->addIntegerField('Display order', 'step_display_order', '', 'step_display_order')->requirements()->setRequired();
		$fld1 = $frm->addFileUpload('Step Image', 'step_image', 'step_image');
		
		if(intval($step_id) <= 0)
			$fld1->requirements()->setRequired();
		
		$fld2 = $frm->addHtml('', 'show_step_image', '');
		$fld2->html_before_field = ' <b class="b_size_class">Best fit image size: 1584 X 729</b>';
        $fld1->attachField($fld2);
		
		$title = (intval($step_id) > 0)?'Update':'Submit';
        $fld1=$frm->addSubmitButton('', 'btn_submit', $title, 'btn_submit','style="cursor:pointer;"');
		$fld2=$frm->addButton('', 'cancel', 'Cancel', '', 'class="cancel_form reset_button" style="margin-left:10px; cursor:pointer;" onclick="location.href=\''.generateUrl('howitworks').'\'"');
		$fld1->attachField($fld2);
        $frm->setValidatorJsObjectName('howitworksValidator');
        
        return $frm;
    }
	
	function delete($step_id){
		if(is_numeric($step_id) && intval($step_id)>0){
			$this->howitworks_model->deletestep($step_id);
		} else {
			Message::addErrorMessage('Invalid Request');
		}
		redirectUser(generateUrl('howitworks'));
	}
	
	private function saveUploadedStepFiles(&$files, &$image_names){
		if(is_uploaded_file($files['step_image']['tmp_name'])){
			$image_names = '';
			if(saveImage($files['step_image']['tmp_name'], $files['step_image']['name'], $image_names)){
				return true;
			}else{
				Message::addErrorMessage($files['step_image']['name'] . ': ' .$image_names);
			}
		}
		return false;
	}
}