<?php
class SampleController extends LegitController {
	private $common;
	private $User;
	private $Test;
	private $task;
	
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		
		$this->common	= new Common();
		$this->User		= new User();
		$this->CMS		= new Cms();
		$this->Test		= new Test();
		$this->task		= new Task();
    }
	function default_action() {
		
		if (!$this->User->isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter(User::getLoggedUserAttribute('user_id'))) { die( Utilities::getLabel( 'E_Invalid_request' ) );}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		//if (User::writerCanView()) { redirectUser(generateUrl('user','dashboard'));}
		if (!$this->Test->isUserPassed()) {
			redirectUser(generateUrl('test'));
		}
		
		if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {
			$frm = $this->getEssayForm();
			$this->set('frm',$frm);
		}else {
			redirectUser(generateUrl('user','dashboard'));
		}
		$this->_template->render();
	}
	
	private function getEssayForm() {
		if (!$this->User->isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter(User::getLoggedUserAttribute('user_id'))) { die( Utilities::getLabel( 'E_Invalid_request' ) );}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		//if (User::writerCanView()) { redirectUser(generateUrl('user','dashboard'));}	
		$frm = new Form('frmEssay','frmEssay');
		
		$frm->setExtra('class="siteForm"');
		//$frm->setOnSubmit('getEssay();return false;');
		$frm->setTableProperties('width="60%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(true);
		$frm->setLeftColumnProperties('width="40%"');
        $frm->setAction(generateUrl('sample', 'sample_essay'));
		$frm->setJsErrorDiv('common_error_msg');
		
		$fld = $frm->addFileUpload( Utilities::getLabel( 'L_Upload_file_for_Sample_Essay' ), 'sample_essay', 'sample_essay');
		$fld->requirements()->setRequired();
		
		$limit = ini_get( 'upload_max_filesize' );
		$frm->addHTML( '<small>' . sprintf( Utilities::getLabel( 'L_Upload_Essay_File_Extensions_Restrictions' ), $limit ) . '</small>' );
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Upload' ), 'btn_submit','class="greenBtn"');
		
		return $frm;
	}
	
	public function sample_essay() {
		$post = Syspage::getPostedVar();
		if (!$this->User->isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter(User::getLoggedUserAttribute('user_id'))) { die( Utilities::getLabel( 'E_Invalid_request' ) );}
		if (!User::isUserActive()) { 
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		if($_FILES['sample_essay']['name'] == ''){
			Message::addErrorMessage( Utilities::getLabel( 'E_Sample_Essay_Restrictions_Message' ) );
			$this->_template = new Template('sample', 'default_action');
			$this->setLegitControllerCommonData('', 'default_action');
			$this->default_action();
			return;
		}
		$num_files_uploaded = count($_FILES['sample_essay']['name']);
		$error = 0;
		if ($num_files_uploaded > 0) {
			$arr_valid_text_ext		= array('txt', 'pdf', 'doc', 'docx', 'xls');
			//$arr_valid_image_ext	= array('jpg', 'jpeg', 'png', 'gif');
		
			
			if (is_uploaded_file($_FILES['sample_essay']['tmp_name'])) {
				$path_parts = pathinfo($_FILES['sample_essay']['name']);
				
				$file_ext		= $path_parts['extension'];
				$base_name		= $path_parts['basename'];
				
				if($_FILES['sample_essay']['type'] == '' || $_FILES['sample_essay']['type'] == null) {
					Message::addErrorMessage( sprintf( Utilities::getLabel( 'L_Could_Not_Upload_File' ), $base_name ) );
					$this->_template = new Template('sample', 'default_action');
					$this->setLegitControllerCommonData('', 'default_action');
					$this->default_action();
					return;				
				}
				if (!in_array($file_ext,$arr_valid_text_ext)) {
					Message::addErrorMessage( sprintf( Utilities::getLabel( 'L_Could_Not_Upload_File' ), $base_name ) );
					$this->_template = new Template('sample', 'default_action');
					$this->setLegitControllerCommonData('', 'default_action');
					$this->default_action();
					return;
				}
				//print_r($_FILES['task_files']['size'][$i]);
				if ($_FILES['sample_essay']['size'] > 10695475 || $_FILES['sample_essay']['size'] == 0) {				//restrict file size to 10MB
					$limit = ini_get('upload_max_filesize');
					Message::addErrorMessage( sprintf( Utilities::getLabel( 'E_Could_Not_Upload_File_Size_Is_Big' ), $base_name, $limit ) );
					$this->_template = new Template('sample', 'default_action');
					$this->setLegitControllerCommonData('', 'default_action');
					$this->default_action();
					return;
				}				
				
				if (!saveFile($_FILES['sample_essay']['tmp_name'], $_FILES['sample_essay']['name'], $saved_file_name)){
					Message::addErrorMessage($saved_file_name);
					$this->_template = new Template('sample', 'default_action');
					$this->setLegitControllerCommonData('', 'default_action');
					$this->default_action();
					return;
				}
				
				$file_type = $_FILES['sample_essay']['type'];
				if (!$this->User->addEssay(array('file_user_id'=>User::getLoggedUserAttribute('user_id'), 'file_name'=>$saved_file_name, 'file_download_name'=>$base_name, 'file_type'=>$file_type))) {
					unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $saved_file_name);
					Message::addErrorMessage($this->User->getError());
					$this->_template = new Template('sample', 'default_action');
					$this->setLegitControllerCommonData('', 'default_action');
					$this->default_action();
					return;
				}					
				
			}
		
		}
		/* $this->task->sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'temp_num'=>53)); */		
		Message::addMessage('Sample Essay saved.');
		redirectUser(generateUrl('user','signup_message',array('3')));
		return;
	}

}
?>
