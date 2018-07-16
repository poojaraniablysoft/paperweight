<?php
class SupportController extends LegitController{
	private $common;
	private $User;
	private $Task;
	private $CmsController;
	
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		
		$this->common	= new Common();
		$this->User		= new User();
		$this->Test		= new Test();
		$this->Task		= new Task();
		//$this->CmsController	= new CmsController('cms','cms','getContactForm');
    }
	
	public function default_action() {
		if (!$this->User->isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		//if (!User::writerCanView()) { redirectUser(generateUrl('user','dashboard'));}
		if ($this->Test->isUserFailed()) {
			$this->set('status', 'Fail');
		}
		$post = Syspage::getPostedVar();
		$frm = $this->getContactForm();
		$frm->fill($post);
		$this->set('frm',$frm);
		$this->_template->render();
	}
	
	private function getContactForm() {
		$frm = new Form('frmcontact', 'frmContact');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="10" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		$frm->setFieldsPerRow(1);
		$frm->setAction(generateUrl('support', 'setup'));
		//$frm->setLeftColumnProperties('width="24%"');
        $frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'user_id', User::getLoggedUserAttribute('user_id'), 'user_id');
		$frm->addHiddenField('', 'user_email', User::getLoggedUserAttribute('user_email'), 'user_email');
		
		$frm->addRequiredField( Utilities::getLabel( 'L_User_Name' ), 'user_screen_name', User::getLoggedUserAttribute('user_screen_name'), 'user_screen_name');
		$frm->addRequiredField( Utilities::getLabel( 'L_Subject' ),'subject','','subject');
		$fld = $frm->addRequiredField( Utilities::getLabel( 'L_Phone' ),'phone','','phone');
		
		//$fld->requirements()->setInt();
		$frm->addTextArea( Utilities::getLabel( 'L_Message' ),'message','','message');
		$frm->addSubmitButton( '','btn_submit', Utilities::getLabel( 'L_Submit' ), 'btn_submit','class="theme-btn"' );
		
		return $frm;
	}
	
	private function getContactFormOuter(){
		$frm = new Form('frmcontact', 'frmContact');
		
		//$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->captionInSameCell(false);
		$frm->setFieldsPerRow(1);
		$frm->setAction(generateUrl('support', 'setup_contact'));
        $frm->setJsErrorDiv('common_error_msg');
		
		
		$frm->addRequiredField( Utilities::getLabel( 'L_Your_Name' ), 'user_screen_name', '', 'user_screen_name');
		$frm->addRequiredField( Utilities::getLabel( 'L_Your_Email' ), 'user_email', '', 'user_email');
		
		$frm->addRequiredField( Utilities::getLabel( 'L_Subject' ),'subject','','subject');
		$fld = $frm->addRequiredField( Utilities::getLabel( 'L_Phone' ),'phone','','phone');
		
		//$fld->requirements()->setInt();
		$fld_code = $frm->addRequiredField( Utilities::getLabel( 'L_Security_Code' ), 'security_code','','','placeholder="' . Utilities::getLabel( 'L_Security_Code' ) . '"');
		$fld_code->html_before_field='<table class="chapcha"><tr><td><img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="image"><a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></i></a></td><td>';
    	$fld_code->html_after_field='</td></tr></table>';
		//$frm->addHTML('', 'code', '');
		$frm->addTextArea( Utilities::getLabel( 'L_Message' ),'message','','message');
		$frm->addSubmitButton('','btn_submit', Utilities::getLabel( 'L_Submit' ),'btn_submit','class="greenBtn"')->merge_rows = 2;
		
		return $frm;
	}
	
	public function setup() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		//if (User::writerCanView()) { redirectUser(generateUrl('user','dashboard'));}
		if ($this->Test->isUserFailed()) {
			$this->set('status', 'Fail');
		}
		
		$user_id = intval($post['user_id']);
		if ($user_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$frm = $this->getContactForm();
		
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('support','default_action');
			$this->setLegitControllerCommonData('','default_action');
            $this->default_action();
            return;
        }
		
		$arr_user = $this->User->getUserDetailsById($user_id);
		if (empty($arr_user)) die( Utilities::getLabel( 'E_Invalid_request' ) );
		$param = array('to'=>CONF_CONTACT_EMAIL_TO,'temp_num'=>9,'user_screen_name'=>$post['user_screen_name'],'user_email'=>$post['user_email'],'user_phone'=>$post['phone'],'message'=>$post['message']);
		/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 9");
		$row_tpl = $db->fetch($rs);
		$subject = $row_tpl['tpl_subject'];
		$body 	 = $row_tpl['tpl_body'];
			
		$arr_replacements = array(
			'{website_name}'=>CONF_WEBSITE_NAME,
			'{user_name}'=>$post['user_screen_name'],
			'{user_email}'=>$post['user_email'],
			'{user_phone}'=>$post['phone'],
			'{user_message}'=>$post['message']
		);
				
		foreach ($arr_replacements as $key=>$val){
			$subject	= str_replace($key, $val, $subject);
			$body		= str_replace($key, $val, $body);
		}
		
		$subject = $post['subject'].$subject; */
		if(!$this->Task->sendEmail($param,$post['subject'])) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Email_Not_Sent' ) );
		}
		else {
			Message::addMessage( Utilities::getLabel( 'S_Request_Submitted_Successfully' ) );
		}
		
		redirectUser(generateUrl('support'));
	}
	
	public function setup_contact() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		$frm = $this->getContactForm();
		
		require_once 'securimage/securimage.php';		
    	$img = new Securimage();		
    	if(!$img->check($_POST['security_code'])){
    		Message::addErrorMessage(  Utilities::getLabel( 'E_Invalid_Code_Entered' ) );
			$this->_template = new Template('cms', 'contact_us');
			/* $this->set('frm',$this->getContactFormOuter());
			return($this->_template->render(true, true, CONF_WEBROOT_URL . 'cms/contact.php', true));
			//$this->CmsController->contact_us();
			//return; */
    		redirectUser(generateUrl('cms', 'contact'));
    	}
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('cms','contact_us');
			redirectUser(generateUrl('cms', 'contact'));
        }
		$param = array('to'=>CONF_CONTACT_EMAIL_TO,'temp_num'=>9,'user_screen_name'=>$post['user_screen_name'],'user_email'=>$post['user_email'],'user_phone'=>$post['phone'],'message'=>$post['message']);
		
		if(!$this->Task->sendEmail($param,$post['subject'])) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Email_Not_Sent' ) );
		}
		else {
			Message::addMessage( Utilities::getLabel( 'S_Request_Submitted_Successfully' ) );
		}
		
		redirectUser(generateUrl('cms','contact_us'));
	}
}
?>