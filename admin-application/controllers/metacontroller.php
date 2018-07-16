<?php
class MetaController extends Controller{
	function __construct($model, $controller, $action){
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
	}

    function default_action() {
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
        $this->_template->render();
    }	

    function listing($page) {
        if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
        
        $post = Syspage::getPostedVar();

        $page = intval($page);
        if ($page < 1)
            $page = 1;

        $pagesize = 10;
		$Meta = new Meta();		
        $srch = $Meta->getMetas();
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);

        $rs = $srch->getResultSet();

        $db = &Syspage::getDb();

        $this->set('arr_listing', $db->fetch_all($rs));
        $this->set('pages', $srch->pages());
        $this->set('page', $page);
        $this->set('start_record', ($page - 1) * $pagesize + 1);
        $end_record = $page * $pagesize;
        $total_records = $srch->recordCount();
        if ($total_records < $end_record)
            $end_record = $total_records;
        $this->set('end_record', $end_record);
        $this->set('total_records', $total_records);
		$this->_template->render();        
    }	

    function form($id) {
        $id = intval($id);

        if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}

        $frm = $this->getForm();
		
		$post = Syspage::getPostedVar();
		if(isset($post) && $post['btn_submit'] != null){			
		
			$db = &Syspage::getdb();

			if($id>0) {				
				$record = new TableRecord('tbl_meta_tags');
				$record->setFldValue('meta_title',$post['meta_title']);
				$record->setFldValue('meta_description',$post['meta_description']);
				$record->setFldValue('meta_keywords',$post['meta_keywords']);
				
				if(!$record->update(array('smt'=>'page_id = ?', 'vals'=>array($id)))) {
					Message::addErrorMessage($record->getError());
				}else{
					Message::addMessage('Meta tags have been updated successfully.');
				}				
			}							
		}	

        if ($id > 0) {
			$Meta = new Meta();		
            $data = $Meta->getMetaById($id);
            $frm->fill($data);
        }

        $this->set('frm', $frm);
        $this->set('page_title', $data['page_title']);

        $this->_template->render();
    }	

    protected function getForm() {
        $frm = new Form('frmMeta');		
		$frm->setTableProperties('class="formTable" width="100%" cellspacing="0" cellpadding="0" border="0"');        
        $frm->addTextBox('Meta Title', 'meta_title','','meta_title','style="width:300px;"');
        $frm->addHtmlEditor('Meta Description', 'meta_description');		
        $frm->addHtmlEditor('Meta Keywords', 'meta_keywords');		
		$frm->addSubmitButton('', 'btn_submit', 'Submit');        
        return $frm;
    }	
}