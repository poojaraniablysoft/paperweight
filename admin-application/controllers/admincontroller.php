<?php
class AdminController extends Controller{
	private $Task;
    function __construct($model, $controller, $action){
        parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->Task = new Task();
    }
	
	function default_action() {
		if (!Admin::isLogged()) {
			redirectUser(generateUrl('admin', 'loginform'));
		} else {
			redirectUser(generateUrl('home'));
		}
    }
	
	function loginform(){
		if (Admin::isLogged()) {
			redirectUser(generateUrl(''));
		}
		$admin_user = array('username'=>'admin','password'=>'admin');
    	$frm = $this->getForm();
    	//$frm->fill($admin_user);
    	$this->set('frm', $frm);
    	$this->_template->render(false, false);
    }
    
    function login(){
		if (Admin::isLogged()) {
			redirectUser(generateUrl(''));
		}
    	$post = Syspage::getPostedVar();
    	
    	$frm = $this->getForm();
    	if (!$frm->validate($post)) {
    		Message::addError($frm->getValidationErrors());
    		redirectUser(generateUrl('admin', 'loginform'));
    		
    	}
    	
    	if (!$this->Admin->login($post['username'], $post['password'])) {
            
            Message::addErrorMessage($this->Admin->getError());

            $this->_template = new Template('admin', 'loginform');
            $this->loginform();

            return;
        }
    	redirectUser(generateUrl('home'));
    }
    
	function logout() {
        $this->Admin->logout();
		Message::addMessage('You are Logged out successfully.');
        redirectUser(generateUrl('admin', 'loginform'));
    }
	
    function getForm(){
    	$frm = new Form('frmLogin');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarPosition('none');
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_msg');
        $user = $frm->addRequiredField('', 'username', '', 'username','class="usericon" placeholder="USERNAME"');
		$user->requirements()->setCustomErrorMessage('Username is mandatory.');
        $pass = $frm->addPasswordField('', 'password', '', 'password','class="keyicon" placeholder="PASSWORD"');
		$pass->requirements()->setRequired();
		$pass->requirements()->setCustomErrorMessage('Password is mandatory.');
		//$frm->addHTML('<a href="'.generateUrl('admin', 'forgot_password').'">Forgot Password?</a>');
        $frm->addSubmitButton('', 'btn_submit', 'Login', '', 'class="login_btn"');
        
    	$frm->setAction(generateUrl('admin', 'login'));
    	
    	return $frm;
    }
	
	function password_change_form(){
		if (!Admin::isLogged()) {
			redirectUser(generateUrl('admin', 'loginform'));
		}
        $frm = new Form('frmPassword');
        
        $first = $frm->addPasswordField('Current Password:', 'current_password');
        $first->requirements()->setRequired();
        $first->captionCellExtra ="class='firstChild'";
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_msg');
        $fld = $frm->addPasswordField('New Password:', 'new_password');
        $fld->requirements()->setRequired();
        
        $fld1 = $frm->addPasswordField('Confirm New Password:', 'new_password1');
        
        $fld1->requirements()->setCompareWith('new_password', 'eq', 'New Password');
        
        $frm->addSubmitButton('', 'btn_submit', 'Submit', '','class="inputbuttons"');
        
        $frm->setAction(generateUrl('admin', 'change_password'));
        
        $this->set('frmPassword', $frm);
        
        $this->_template->render();
    }
    
    function change_password(){
        global $post;
        global $msg;
		if (!Admin::isLogged()) {
			redirectUser(generateUrl('admin', 'loginform'));
		}
        if ($this->Admin->changePassword($post['current_password'], $post['new_password'])){
            $msg->addMsg('Password change successful!');
        }
        else {
            $msg->addError($this->Admin->getError());
        }
        
        redirectUser(generateUrl('admin', 'password_change_form'));
    }
	
	function forgot_password(){
		
    	if ($this->Admin->isLogged()) redirectUser(generateUrl('admin'));
    	
    	$frm = new Form('frmForgotPassword');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarPosition('none');
		$frm->setJsErrorDiv('common_msg');
    	$user = $frm->addEmailField('', 'admin_email','','','class="input-normal" placeholder="Registered Email ID" title="Registered Email ID"');
		//$user->requirements()->setCustomErrorMessage('Email Id is mandatory.');
    	$security = $frm->addRequiredField('', 'security_code','','','class="input-normal" placeholder="Security Code"');
		$security->requirements()->setCustomErrorMessage('Security Code is mandatory.');
    	$frm->addHTML('<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="image" class="captcha"/>
    	<a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><img src="' . CONF_WEBROOT_URL . 'images/reload.png" alt="" class="reload" width="40" height="40"></a>', '', '',true);
    	
    	$frm->addSubmitButton('', 'btn_submit', 'Submit', '', 'class="login_btn"');
    	
    	$frm->setAction(generateUrl('admin', 'email_password_instructions'));
    	
    	$this->set('frm_password', $frm);
    	
    	$this->_template->render(false, false);
    }
	
	function email_password_instructions(){
		
    	/* @var $db Database */
    	global $db;
    	global $post;
    	
    	if ($this->Admin->isLogged()) redirectUser(generateUrl('admin'));
    	$frm = new Form('frmForgotPassword');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarPosition('none');
    	$user = $frm->addEmailField('', 'admin_email','','','class="input-normal" placeholder="Registered Email ID"');
		$user->requirements()->setCustomErrorMessage('Username is mandatory.');
    	$security = $frm->addRequiredField('', 'security_code','','','class="input-normal" placeholder="Security Code"');
		$security->requirements()->setCustomErrorMessage('Security Code is mandatory.');
    	$frm->addHTML('<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="image" class="captcha"/>
    	<a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><img src="' . CONF_WEBROOT_URL . 'images/reload.png" alt="" class="reload" width="40" height="40"></a>', '', '',true);
    	
    	$frm->addSubmitButton('', 'btn_submit', 'Submit', '', 'class="login_btn"');
    	
    	$frm->setAction(generateUrl('admin', 'email_password_instructions'));
		if(!$frm->validate($post)){
			$errors = $frm->getValidationErrors();
			foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('admin', 'forgot_password');
            $this->forgot_password();
            return;
		}
    	require_once '../../public/securimage/securimage.php';
    	$img = new Securimage();
    	if(!$img->check($_POST['security_code'])){
    		Message::addErrorMessage('You entered incorrect Security Code. Please Try Again.');
    		redirectUser(generateUrl('admin', 'forgot_password'));
    	}
    	
    	/* if (!$post['admin_email'] = CONF_ADMIN_EMAIL_ID) {
    		Message::addErrorMessage('Invalid Email ID!');
    		redirectUser(generateUrl('admin', 'forgot_password'));
    	} */
		
		$sql = $db->query("SELECT * FROM tbl_admin WHERE admin_email = '" . $post['admin_email'] . "'");
		
		if (!$row = $db->fetch($sql)) {
			Message::addErrorMessage('Invalid Email ID!');
    		redirectUser(generateUrl('admin', 'forgot_password'));
		}
		
		$admin_id = $row['admin_id'];
    	
    	$db->query("delete from  tbl_admin_password_reset_requests where aprr_expiry < '" . date('Y-m-d H:i:s') . "'");
    	$rs = $db->query("select * from  tbl_admin_password_reset_requests where appr_admin_id = " . $admin_id);
    	if ($row_request = $db->fetch($rs)){
    		Message::addErrorMessage('Your request to reset password has already been placed within last 15 minutes. Please check your emails or retry after 15 minutes of your previous request');
    		redirectUser(generateUrl('admin', 'forgot_password'));
    	}
    	
    	$tocken = encryptPassword(getRandomPassword(20));
		
    	$db->insert_from_array('tbl_admin_password_reset_requests', array(
		'appr_admin_id'=>$admin_id,
    	'aprr_token'=>$tocken,
    	'aprr_expiry'=>date('Y-m-d H:i:s', strtotime("+ 15 MINUTE"))
    	));
    	
    	
    	$reset_url = generateUrl('admin', 'reset_password', array($tocken,$admin_id));
    	/* $message = 'It seems that you have used forgot password option at ' . CONF_WEBSITE_NAME  . ' (' . $_SERVER['SERVER_NAME'] . ')' . '
    	
    	Please visit the link given below to reset your password. <strong>Please note that the link is valid for next 24 hours only. </strong>
    	
    	Password reset url: <a href="' . $reset_url . '">' . $reset_url . '</a>
    	
    	<i>Please ignore this email if you did not use the forgot password option.</i>
    	';
    	
    	$message = nl2br($message);
    	
    	 */
		/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 1");
		
        $row_tpl = $db->fetch($rs);
        
        $subject = $row_tpl['tpl_subject'];
        $body 	 = $row_tpl['tpl_body'];
		
        $arr_replacements = array(
        		'{website_name}'=>CONF_WEBSITE_NAME,
                '{website_url}'=>$_SERVER['SERVER_NAME'],
				'{reset_url}'=>$reset_url,
				'{user_first_name}'=>'Administrator'
        );
		
        foreach ($arr_replacements as $key=>$val){
        	$subject	= str_replace($key, $val, $subject);
        	$body		= str_replace($key, $val, $body);
        }
    	
		$subject  = 'Password reset instructions at ' . CONF_WEBSITE_NAME;
    	
    	sendMail($post['admin_email'], $subject, $body); */
		$data = array(
						'temp_num'=>1,
						'to'=>$post['admin_email'],
						'reset_url'=>$reset_url,
						'user_first_name'=>'Administrator'
		);
		
		$this->Task->sendEmail($data);
    	Message::addMessage('Your password reset instructions have been sent to your email. Please check your spam folder if you do not find it in your inbox. Please mind that this request is valid only for next 15 minutes.');
    	
    	redirectUser(generateUrl('admin', 'forgot_password'));
    }
	
	function reset_password($tocken,$admin_id){
    	if ($this->Admin->isLogged()) redirectUser(generateUrl('admin'));
    	
    	if (!$this->validateTocken($tocken)) die('Invalid Tocken');
    	
    	$frm = new Form('frmResetPassword');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarPosition('none');
    	$frm->addHiddenField('', 'tocken', $tocken);
    	
    	$fld = $frm->addPasswordField('', 'admin_password','','','class="input-normal" placeholder="New Password" title="New Password"');
    	$fld->requirements()->setRequired();
    	$fld->requirements()->setLength(4, 20);
    	$fld1 = $frm->addPasswordField('', 'admin_password1','','','class="input-normal" placeholder="Confirm New Password" title="Confirm New Password"');
    	$fld1->requirements()->setRequired();
    	$fld1->requirements()->setCompareWith('admin_password');
    	 
    	$frm->addSubmitButton('', 'btn_submit', 'Submit', '', 'class="login_btn"');
    	
    	$frm->setAction(generateUrl('admin', 'password_reset', array($admin_id)));
    	
    	$this->set('frm_password', $frm);
    	
    	$this->_template->render(false, false);
    }
	
	function password_reset($admin_id){
    	/* @var $db Database */
    	global $db;
    	global $post;
    	
    	if ($this->Admin->isLogged()) redirectUser(generateUrl('admin'));
    	
    	if (!$this->validateTocken($post['tocken'])) die('Invalid tocken!');
    	
    	if (!$db->update_from_array('tbl_admin', array('admin_password'=>encryptPassword($post['admin_password'])), array('smt'=>'admin_id = ?', 'vals'=>array($admin_id)))) dieWithError($db->getError());
    	
    	$db->query("delete from tbl_admin_password_reset_requests where appr_admin_id = " .$admin_id);
    	
    	Message::addMessage('Password successfully updated! Please login now with your new password.');
    	redirectUser(generateUrl('admin', 'loginform'));
    	
    }
	
	protected function validateTocken($tocken){
    	/* @var $db Database */
    	global $db;
    	
    	$db->query("delete from tbl_admin_password_reset_requests where aprr_expiry < '" . date('Y-m-d H:i:s') . "'");
    	$rs = $db->query("select * from tbl_admin_password_reset_requests where aprr_token = '" . $tocken . "'");
    	$row = $db->fetch($rs);
    	
		if (!$row){
    		die('Invalid Tocken! You are using invalid link. <br>Please note that password reset requests are valid for 24 hours only.');
    	}
    	return true;
    }
}
?>
