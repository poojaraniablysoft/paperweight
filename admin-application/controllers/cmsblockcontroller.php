<?php

class CmsblockController extends Controller {

    function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->set('leftLink', 'cmsblock');
    }

    function default_action() {
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$post = Syspage::getPostedVar();
		$frm = $this->getSearchFrm();
		if(isset($post) && $post['btn_submit'] == 'Submit') {
			$frm->fill($post);
		}
        $this->set('frm', $frm);
        $this->_template->render();
    }

    function form($id) {
        $id = intval($id);

        if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}

        $frm = $this->getForm();

        if ($id > 0) {
            $data = Cmsblock::getData($id);
            $frm->fill($data);
        }

        $this->set('frm', $frm);

        $this->_template->render();
    }
    
    protected function getSearchFrm() {
        $frm = new Form('frmSearch');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable threeCol" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setFieldsPerRow(3);
		$frm->captionInSameCell(true);
        $frm->addTextBox('', 'block_title','','','placeholder="Search Block Title..."');
        $frm->addTextBox('', 'page_name','','','placeholder="Search Page Name..."');
        
        $fld4 = $frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit', 'class="opt-button"');
        $fld5 = $frm->addButton('', 'btn_cancel', 'Reset', 'btn_cancel', 'class="opt-button opt-button-orange" onclick="document.location.href=\'\'"');
        $fld4->html_after_field = ' &nbsp; ';
        $fld4->attachField($fld5);
        $frm->setAction(generateUrl('cmsblock'));
        //$frm->setOnSubmit('searchLabels(this); return(false);');
        return $frm;
    }

    function listing($page) {
        if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
        
        $post = Syspage::getPostedVar();

        $page = intval($page);
        if ($page < 1)
            $page = 1;

        $pagesize = 20;

        $criteria = array(
            'block_title' => $post['block_title'],            
            'page_name' => $post['page_name'],            
        );
        
        $srch = Cmsblock::search($criteria);
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

        $this->_template->render(false, false);
    }

    protected function getForm() {

        $frm = new Form('frmCmsblck');
		
		$frm->setTableProperties('class="formTable" width="100%" cellspacing="0" cellpadding="0" border="0"');
        $frm->addHiddenField('', 'block_id');
        $frm->addRequiredField('Title', 'block_title','','block_title','style="width:300px;"');
        $fld = $frm->addHtmlEditor('Description', 'block_content');
		// $fld->requirements()->setRequired(true);

		$frm->addSubmitButton('', 'btn_submit', 'Submit');
        $frm->setAction(generateUrl('cmsblock', 'setup'));
        $frm->setExtra('rel="" validator="cmsblckValidator"');
        $frm->setValidatorJsObjectName('cmsblckValidator');

        return $frm;
    }

    function setup() {
		try {
			$post = Syspage::getPostedVar();
			$id = intval($post['block_id']);
			$db = &Syspage::getdb();
			
			if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}

			$frm = $this->getForm();

			if (!$frm->validate($post)) {
				Message::addErrorMessage($frm->getValidationErrors());
				throw new Exception('');
			}

			if($id>0!=true) {
				$rs = $db->insert_from_array('tbl_content_blocks', array(
				'block_title'=>$post['block_title'],
				'block_content'=>$post['block_content'],
				), true);
			} else {
				$rs = $db->update_from_array('tbl_content_blocks', array(
				'block_title'=>$post['block_title'],
				'block_content'=>$post['block_content'],
				), array(
				'smt'=>'block_id=?', 'vals'=>array($id)
				));
			}
			if(!$rs) {
				Message::addErrorMessage($db->getError());
				throw new Exception('');
			}
			
			Message::addMessage('Content block have been updated successfully.');
			throw new Exception('');
		} catch (Exception $e) {
			redirectUser(generateUrl('cmsblock'));
		}
    }

    function delete($id) {
        $db = &Syspage::getdb();
        $id = intval($id);
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}

        if (!$db->deleteRecords('tbl_content_blocks', array('smt' => 'block_id = ?', 'vals' => array($id))))
            dieJsonError($db->getError());

        dieJsonSuccess('Content block have been deleted.');
    }

}