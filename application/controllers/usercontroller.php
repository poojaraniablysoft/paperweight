<?php
class UserController extends LegitController {
	private $common;
	private $task;
	private $Bid;
	private $wallet;
	private $ratings;
	private $order;
	private $transactions;
	private $reviews;
	private $messages;
    
    public function __construct($model, $controller, $action){
        parent::__construct($model, $controller, $action);
		
		$this->common		= new Common();
		$this->task			= new Task();
		$this->Bid			= new Bid();
		$this->wallet		= new Wallet();
		$this->ratings		= new Ratings();
		$this->order		= new Order();
		$this->transactions	= new Transactions();
		$this->reviews		= new Reviews();
		$this->messages		= new Messages();
    }
	
	public function dashboard() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		
		if(isset($_SESSION['e_f_pp_return']['refrer_url'])){
			unset($_SESSION['e_f_pp_return']['refrer_url']);
			redirectUser($_SESSION['e_f_pp_return']['refrer_url']);
		}
		
		$this->set('wallet_balance', $this->wallet->getUserWalletBalance());
		if (User::isWriter()) {
			if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}
			$this->set('amount_withdrawn', $this->transactions->getAmountWithdrawn());
			$this->set('amount_in_progress', $this->wallet->getIncomingAmount());
			$this->set('writer_activity_stats', $this->order->getWriterActivityStats());
			$this->set('arr_writer_bids', $this->Bid->Showbids('mine'));
			$this->set('arr_writer_ongoing_orders', $this->Bid->Showbids('ongoing'));
			$this->set('arr_writer_completed_orders', $this->Bid->Showbids('completed'));
			$this->set( 'available_orders', $this->task->countAvailableOrders( ) );
		}else {
			$first_order = Common::is_first_order_done();
			if($first_order == false) {
				redirectUser(generateUrl('task','my_orders'));
				return;
			}
		
			$this->set('customer_activity_stats', $this->order->getCustomerActivityStats());
			$this->set('arr_bids_received', $this->Bid->bidsReceived());
			$this->set('reserved_amount',$this->wallet->getReservedAmountByUserId(User::getLoggedUserAttribute('user_id')));
			$this->set('arr_customer_ongoing_orders', $this->task->getCustomerOrders('ongoing'));
			$this->set('arr_customer_completed_orders', $this->task->getCustomerOrders('completed'));
			$this->set('arr_request', $this->transactions->getWithdrawalRequests($page,true));
			$this->set('available_writers', $this->User->getActiveWritersCount());
		}
		$this->set('user_rating', $this->ratings->getAvgUserRating());		
		$arr_feedback = $this->reviews->getFeedbackReceived();
		
		foreach ($arr_feedback['feedback'] as $key=>$ele) {
			$arr_feedback['feedback'][$key]['reviewer_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewer_id']);
			$arr_feedback['feedback'][$key]['posted_rating']		= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewer_id']);
			$arr_feedback['feedback'][$key]['orders_completed']		= $this->order->getUserCompletedOrders($ele['rev_reviewer_id']);
		}
		$this->set('user_info',$this->User->getUserDetailsById(User::getLoggedUserAttribute('user_id')));
		$this->set('arr_conversations',$this->messages->getUsers());		
		$this->set('arr_feedback', $arr_feedback);
		
		$this->_template->render();
	}
	
	public function login() {
		$post = Syspage::getPostedVar();
		
		//echo '<pre>' . print_r($post,true) . '</pre>'; exit;
		if(is_array($post) && !empty($post)){
        if (!$this->User->validateLogin($post['user_email'], $post['user_password'])){
            Message::addErrorMessage($this->User->getError());
            $this->_template = new Template('user', 'signin');
            $this->signin();
			return;
        }
	   }
		if (isset($post['remember_me']) && $post['remember_me'] == 1) {
			$token = $this->User->setAuthToken();
			
			if ($token !== false) {
				setcookie('LEGITAUTHID', $token, time()+30*24*60*60, '/');
			}
		}
		if(intval($post['task_id']) > 0 && User::isWriter()) {
			redirectUser(generateUrl('bid','add',array($post['task_id'])));
			return false;
		}
		
		if(intval($post['invite_user_id']) > 0 && User::isWriter() == false) {
			redirectUser(generateUrl('task','add',array(intval($post['task_id']),intval($post['invite_user_id']))));
			return false;
		}
		
		redirectUser(generateUrl('user','dashboard'));
	}
	
	public function validate_user(){
		$db = Syspage::getDB();
		$post = Syspage::getPostedVar();
        $srch = new SearchBase('tbl_users');
        $srch->addCondition('user_email', '=', $post['user_email']);        
        $rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			if(!isset($post)) dieWithError('Invalid Request!');
			die(convertToJson(array('status'=>2)));
		}
		if ($row['user_deactivate_request'] == 1){
			$row['user_deativate_request_date'] = displayDate($row['user_deativate_request_date']);
			$this->set('user_info',$row);
			$this->_template->render(false,false);
		}else{
			die(convertToJson(array('status'=>0)));
		}
		return false;
	}
	
	public function signin($status) {
		$post = Syspage::getPostedVar();
		if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));
		
		$status = intval($status);

		$writer_login 	= array('user_email'=>'samuel@dummyid.com','user_password'=>'samuel2013');
		$customer_login = array('user_email'=>'nikol@dummyid.com','user_password'=>'nikol2016');
		
		$frm = $this->getLoginForm();
        //$frm = $this->getLoginForm();
        
		if($status == 1) {
			$frm->fill($writer_login);
		}elseif($status == 2) {
			$frm->fill($customer_login);
		}
		else{
			$frm->fill($customer_login);
		}
        
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			unset($post['btn_login']);
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
        $this->set('status', $status);
		
        $this->_template->render();
	}
	
	public function sign_in($task_id) {
		$post = Syspage::getPostedVar();
		if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));
		
		$task_id = intval($task_id);
		
        $frm = $this->getLoginForm();
        if($task_id > 0) {
			$fld = $frm->getField('task_id');
			$fld->value = $task_id;
		}
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			unset($post['btn_login']);
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
        //$this->set('status', $task_id);
		$this->_template->render(false,false);
	}
	
	public function getLoginForm() {
		$frm = new Form('frmLogin');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" border="0" cellpadding="0" cellspacing="0"');
		$frm->captionInSameCell(true);
		//$frm->setLeftColumnProperties('align="right"');
        $frm->setAction(generateUrl('user', 'login'));
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_error_msg');
		$frm->addHiddenField('','task_id','');
		$frm->addHiddenField('','invite_user_id','');
		$frm->addEmailField( Utilities::getLabel( 'L_Email_Address' ), 'user_email', '', 'user_email','placeholder="' . Utilities::getLabel( 'L_Email_Address' ) . '*"')->requirements()->setEmail();		
		#$frm->addPasswordField('Password', 'user_password', '', 'user_password','placeholder="Password*"')->requirements()->setRequired();		
		
		$fld_pass = $frm->addPasswordField( Utilities::getLabel( 'L_Password' ), 'user_password', '', 'user_password','placeholder="' . Utilities::getLabel( 'L_Password' ) . '*"');
		$fld_pass->requirements()->setRequired();
		$fld_pass->requirements()->setLength(6,20);	
		
		$frm->addCheckBox('', 'remember_me', 1, 'remember_me', '');
		$frm->addHTML('', 'forgot_password', '<a href="' . generateUrl('user', 'forgot_password') . '" class="forgot-pass">' . Utilities::getLabel( 'L_Forgot_password' ) . '</a>');
		
		$frm->addSubmitButton('', 'btn_login', '' . Utilities::getLabel( 'L_Log_In' ) . '', 'btn_login',' class="theme-btn" ');
		$frm->setOnSubmit('return validateUser(this);');
		
		return $frm;
	}
	
	function logout(){
		if (isset($_SESSION['logged_admin']) && $_SESSION['logged_admin']['admin_id'] > 0) {
			unset($_SESSION['logged_user']);
		}
		else {
			session_destroy();
		}
		
		/* unset cookie */
		$this->User->removeAuthToken();
		setcookie('LEGITAUTHID', '', time()-3600, '/');
		
    	redirectUser(generateUrl('user', 'signin'));
    }
	
	public function register_customer() {
		if(User::isUserLogged()){
			$first_order = Common::is_first_order_done();
			if($first_order == false) {		
					$data = $this->task->getCustomerOrders('mine',1);
					foreach($data['orders'] as $value) {
						Message::addMsg( Utilities::getLabel( 'E_Complete_Your_First_order' ) . ' <a href='.generateUrl('task','add',array($value['task_id'])).'>' . Utilities::getLabel( 'L_click_here' ) . '</a>');
					}			
				redirectUser(generateUrl('home', ''));		
			}	
		}	
		
		$post = Syspage::getPostedVar();
       
		$invitee_id = intval($post['invitee_ids']);
		
        $frm = $this->getCustomerRegistrationForm();
		
		if(User::isUserLogged() && !User::isWriter()) {
			$post['user_email'] = User::getLoggedUserAttribute('user_email');
		}
		
		
        if (!$frm->validate($post) && !User::isUserLogged()){
			
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('user', 'customer_signup');
			$this->setLegitControllerCommonData('', 'customer_signup');
            $this->customer_signup();
            return;
        }
		else if($post['task_due_date'] == '' || $post['task_due_date'] =='Choose Deadline') {	
			Message::addErrorMessage( Utilities::getLabel( 'E_Deadline_Restriction_Message' ) );
            $this->_template = new Template('user', 'customer_signup');
			$this->setLegitControllerCommonData('', 'customer_signup');
            $this->customer_signup();  
            return;
			
		}
		/* $task_due_date = array();
		$task_due_date = explode('_',$post['task_due_date']);
		
		switch ($task_due_date[1]) {
			case 'h' :
				$post['task_due_date'] = strtotime('+'.$task_due_date[0].' hours',time());
				$post['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
				break;
			case 'n' :
				$post['task_due_date'] = date('Y-m-d '.$task_due_date[0].':00',strtotime('tomorrow'));
				break;
			case 'd' :
				$post['task_due_date'] = strtotime('+'.$task_due_date[0].' days',time());
				$post['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
				break;
			default :
				$post['task_due_date'] = strtotime('+7 days',time());
				$post['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
				break;
		}  */
		
		
		/* if(!strtotime($post['task_due_date']) || strtotime($post['task_due_date']) == 'Deadline') {
			Message::addErrorMessage('Enter a Valid date.');
            $this->_template = new Template('user', 'customer_signup');
			$this->setLegitControllerCommonData('', 'customer_signup');
            $this->customer_signup();
            return;
		}
		
		if (strtotime($post['task_due_date']) <= time()) {
			Message::addErrorMessage('Enter a future date.');
            $this->_template = new Template('user', 'customer_signup');
			$this->setLegitControllerCommonData('', 'customer_signup');
            $this->customer_signup();
            return;
		} */
		// var_dump($post);
		// var_dump(strtotime($post['task_due_date']));
		// var_dump(date('Y-m-d', $post['task_due_date']));
		/* var_dump($post['task_due_date']);
		
		exit; */
		
		$data = $post;	
		if(!User::isUserLogged() || (User::isUserLogged() && User::isWriter())) {
			if(User::isWriter()) {
				if (isset($_SESSION['logged_admin']) && $_SESSION['logged_admin']['admin_id'] > 0) {
					unset($_SESSION['logged_user']);
				}
				else {
					session_destroy();
				}
		
				
				$this->User->removeAuthToken();
				setcookie('LEGITAUTHID', '', time()-3600, '/');
			}
			$pass = getRandomPassword(10);
			$task_type	= $invitee_id > 0 ? 1 : 0;
			$data['user_password']			= encryptPassword($pass);
			$data['user_type']				= 0;
			$data['user_email_verified']	= 0;
			unset($data['task_topic']);
			//die('writer');
			
			if (!$this->User->addUser($data)){
				Message::addErrorMessage($this->User->getError());
				$this->_template = new Template('user', 'customer_signup');
				$this->customer_signup();
				return;
			} 
		}
		//echo $this->User->getUserId();exit;
		if (!$this->task->addTask(array('task_user_id'=>$this->User->getUserId(),'task_topic'=>$data['task_topic'] ,'task_paptype_id'=>$data['task_paptype_id'], 'task_pages'=>$data['task_pages'], 'task_due_date'=>$data['task_due_date'],'task_type'=>$task_type))) {
			Message::addErrorMessage($this->task->getError());
		}
		
		if ($invitee_id > 0) {
			if ($this->task->addTaskInvitees(array('inv_task_id'=>$this->task->getTaskId(), 'inv_writer_id'=>$invitee_id, 'inv_status'=>0))) {
				//send notification to the writer
				global $db;
				$objUser = new User($invitee_id);
				/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 7");
				$row_tpl = $db->fetch($rs);
				
				$subject	= $row_tpl['tpl_subject'];
				$body 		= $row_tpl['tpl_body'];
				
				
				$arr_replacements = array(
					'{website_name}'=>CONF_WEBSITE_NAME,
					'{writer_name}'=>$objUser->getAttribute('user_screen_name'),
					'{customer_name}'=>User::getLoggedUserAttribute('user_screen_name'),
					'{task_url}'=>'http://' . $_SERVER['SERVER_NAME'] . generateUrl('bid', 'add', array($this->task->getTaskId()))
				);
				
				foreach ($arr_replacements as $key=>$val){
					$subject	= str_replace($key, $val, $subject);
					$body		= str_replace($key, $val, $body);
				}
				
				sendMail($objUser->getAttribute('user_email'), $subject, $body); */
				$param = array('to'=>$objUser->getAttribute('user_email'),'temp_num'=>7,'user_first_name'=>User::getLoggedUserAttribute('user_screen_name'),'user_screen_name'=>$objUser->getAttribute('user_screen_name'),'order_page_link'=>CONF_SITE_URL.generateUrl('bid', 'add', array($this->task->getTaskId())));
				$this->task->sendEmail($param);
			}
		}
		
		
		if(!User::isUserLogged()) {
			$this->sendConfirmationEmail($data['user_email'],$pass);
			if (!$this->User->validateLogin($data['user_email'],$pass)) {
				Message::addErrorMessage($this->User->getError());
				redirectUser(generateUrl('user', 'signin'));
			}
			else {
				Message::addMessage( Utilities::getLabel( 'S_Registration_Notification' ) );
				redirectUser(generateUrl('task', 'add', array($this->task->getTaskId())));
			}
		}
		redirectUser(generateUrl('task', 'add', array($this->task->getTaskId())));
	}
	
	public function customer_signup() {
		$post = Syspage::getPostedVar();
		
		if (User::isUserLogged()) redirectUser(generateUrl('home'));
		
        $frm = $this->getCustomerRegistrationForm();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){            
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
		
        $this->_template->render();
    }
	
	private function getCustomerRegistrationForm() {
		$frm = new Form('frmCustomerRegistration');

		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('align="right" width="30%"');
        $frm->setAction(generateUrl('user', 'register_customer'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('','invitee_ids','');
		$fld_email = $frm->addEmailField( Utilities::getLabel( 'L_Email' ), 'user_email', '', 'user_email'," placeholder='" . Utilities::getLabel( 'L_Email' ) . "'");
		$fld_email->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
		$frm->addSelectBox( Utilities::getLabel( 'L_Type_of_paper' ), 'task_paptype_id', $this->common->getPaperTypes(), '', '', Utilities::getLabel( 'L_Select' ), 'task_paptype_id')->requirements()->setRequired();
		$fld_pages = $frm->addRequiredField( Utilities::getLabel( 'L_Pages' ), 'task_pages', '', 'task_pages', ' placeholder="' . Utilities::getLabel( 'L_Pages_(in_numeric)' ) . '"' );
		$fld_pages->requirements()->setIntPositive();
		$fld_pages->requirements()->setRange(1,999);
		$tomorrow = new DateTime('tomorrow');			
		$end = new DateTime('9999-12-31 23:59:59');	
		
		$fld_deadline = $frm->addDateTimeField( Utilities::getLabel( 'L_Deadline' ), 'task_due_date', Utilities::getLabel( 'L_Choose_Deadline' ), '', ' readonly class="task_due_date_cal"'); 
		/* $fld_deadline->requirements()->setRequired(true); */
		$fld_deadline->requirements()->setRange($tomorrow->format( 'd/m/Y H:i:s'),$end->format('d/m/Y H:i:s'));
		/* $fld_deadline->requirements()->setRange($tomorrow->format( CONF_DATE_FORMAT_PHP. ' H:i:s'),$end->format( CONF_DATE_FORMAT_PHP. ' H:i:s')); */
		$fld_deadline->requirements()->setCustomErrorMessage( Utilities::getLabel( 'E_Deadline_Restriction_Message' ) );		
		//$fld_deadline = $frm->addSelectBox('','task_due_date',array('3_h'=>'3 hours','6_h'=>'6 hours','11_n'=>'Next day by 11am','1_d'=>'Next Day','2_d'=>'2 Days','3_d'=>'3 Days','4_d'=>'4 Days','5_d'=>'5 Days','7_d'=>'7 Days','14_d'=>'14 Days','21_d'=>'21 Days','28_d'=>'28 Days'),'7_d','');
		/* $fld = $frm->addDateTimeField('Deadline', 'task_due_date', '', 'task_due_date', 'readonly="readonly" style="width:200px; margin-right:5px;"');
		$fld->requirements()->setRequired(); */
		//$fld->html_after_field='<img id="cal_trigger_task_due_date" align="absmiddle" style="cursor: pointer;" alt="Calendar" src="/images/iocn_clender.gif">';
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Continue' ), 'btn_submit','class="greenBtn"');
		
		return $frm;
	}
	
	public function register_writer() {
		$post = Syspage::getPostedVar();
        
        $frm = $this->getWriterRegistrationForm();
		require_once 'securimage/securimage.php';
		
    	$img = new Securimage();
		
    	if(!$img->check($_POST['security_code'])){
    		Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_Code_Entered' ) );
    		$this->_template = new Template('user', 'writer_signup');
			$this->setLegitControllerCommonData('', 'writer_signup');
            $this->writer_signup();
            return;
    	}
		
        if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('user', 'writer_signup');
			$this->setLegitControllerCommonData('', 'writer_signup');
            $this->writer_signup();
            return;
        }
		
		if ($this->User->sendVerificationEmail($post['user_email'])) {
			Message::addMessage( Utilities::getLabel( 'S_Notification_To_Proceed_Registration_Process' ) );
			redirectUser(generateUrl('user', 'signup_message',array(1)));
			return;
		}
		else {
			Message::addErrorMessage($this->User->getError());
		}
		
		redirectUser(generateUrl('user', 'writer_signup'));
	}
	
	public function writer_signup() {
		$post = Syspage::getPostedVar();
		
		if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));
		
        $frm = $this->getWriterRegistrationForm();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){            
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);  
		
        $this->_template->render();
    }
	
	private function getWriterRegistrationForm() {
		$frm = new Form('frmWriterRegistration');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('align="right"');
        $frm->setAction(generateUrl('user', 'register_writer'));
		$frm->setJsErrorDisplay('afterfield');
		
		$fld_email = $frm->addEmailField( Utilities::getLabel( 'L_Email' ), 'user_email', '', 'user_email');
		$fld_email->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
		$frm->addRequiredField( Utilities::getLabel( 'L_Security Code' ), 'security_code');
    	$frm->addHTML('', 'code', '<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="captcha_image">&nbsp;&nbsp;
    	<a href="javascript:void(0);" onclick="document.getElementById(\'captcha_image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></i></a>
    	');
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Continue' ), 'btn_submit','class="greenBtn"');
		
		return $frm;
	}
	
	private function sendConfirmationEmail($email,$pass) {
    	$db = &Syspage::getdb();
        
		$arr_acc_details = array('to' => $email,'temp_num' => 4,'login_page_link'=>CONF_SITE_URL.generateUrl('user','signin'),'login_password' =>$pass,'user_email'=>$email);
		$this->task->sendEmail( $arr_acc_details );
		$arr_new_user = array( 'to' => CONF_ADMIN_EMAIL_ID, 'temp_num' => 17, 'user_email' => $email );
		$this->task->sendEmail( $arr_new_user );		
        
        return true;
    }
	
	public function verify_email($code,$customerEmailVerification=false) {
		if (!$this->User->verifyEmail($code,$customerEmailVerification)){
			Message::addErrorMessage($this->User->getError());
			redirectUser(generateUrl('user', 'writer_signup'));
			return false;
		}		
		redirectUser(generateUrl('user', 'update_profile'));
    }
	
	public function submit_writer_profile() {
		$post = Syspage::getPostedVar();
		/* $post['user_lang']['user_lang_id'] = $post['user_lang_id'];  */
		
		$user_id = intval($post['user_id']);
		if (User::isUserLogged() && $user_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$frm = $this->getWriterProfileForm($post['user_lang_id']);
		
		//$fld_gvt_id = $frm->getField('user_govt_id');
		//$fld_gvt_id->requirements()->setRequired(false);
		
		$fld_edu_diploma = $frm->getField('edu_diploma');
		$fld_edu_diploma->requirements()->setRequired(false);
		
		if (User::isUserLogged()) {
			 if (!User::isUserActive()) {
				Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
				redirectUser(generateUrl('user', 'signin'));
			}
			$fld_pass = $frm->getField('user_password');
			$fld_pass->requirements()->setRequired(false);
			$fld_pass->requirements()->setLength(0,0);
			
			$fld_pass1 = $frm->getField('user_password_1');
			$fld_pass1->requirements()->setRequired(false);
			$fld_pass1->requirements()->setLength(0,0);
		}
		
		$arr_citation_styles = (array) $post['citations'];
		$arr_user_languages = (array) $post['user_lang_id'];
		
         if (!$frm->validate($post)) {
            $errors = $frm->getValidationErrors();
			//echo '<pre>' . print_r($errors,true) . '</pre>'; exit;
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('user', 'update_profile');
			$this->setLegitControllerCommonData('', 'update_profile');
            $this->update_profile();
            return;
        } 
				
		if (empty($arr_citation_styles)) {
			Message::addErrorMessage( Utilities::getLabel( 'E_select_at_least_one_citation_style' ) );
			$this->_template = new Template( 'user', 'update_profile' );
			$this->setLegitControllerCommonData( '', 'update_profile' );
            $this->update_profile( );
            return;
		}
		
		if (empty($arr_user_languages)) {
			Message::addErrorMessage( Utilities::getLabel( 'L_You_must_select_at_least_one_language' ) );
			$this->_template = new Template( 'user', 'update_profile' );
			$this->setLegitControllerCommonData( '', 'update_profile' );
            $this->update_profile();
            return;
		}
		
		if (User::isUserLogged()) {
			if (isset($post['user_email'])) unset($post['user_email']);
			if (isset($post['user_screen_name'])) unset($post['user_screen_name']);
		}
		
		$data = $post;
		//upload diploma
		if(!isset($_FILES)) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Certificate_is_mandatory' ) );
			$this->_template = new Template('user', 'update_profile');
			$this->setLegitControllerCommonData('', 'update_profile');
			$this->update_profile();
			return;
		}
		if($_FILES['edu_diploma']['size'] > 10695475) {
				Message::addErrorMessage( sprintf( Utilities::getLabel( 'E_File_Size_Exceeds' ), ini_get('upload_max_filesize') ) );
				$this->_template = new Template('user', 'update_profile');
				$this->setLegitControllerCommonData('', 'update_profile');
				$this->update_profile();
				return;
		}
		if (is_uploaded_file($_FILES['edu_diploma']['tmp_name'])) {
			$saved_image_name = '';
			
			if (saveImage($_FILES['edu_diploma']['tmp_name'], $_FILES['edu_diploma']['name'], $saved_image_name)){
				$data['education']['usered_file'] = $saved_image_name;
			}
			else {
				Message::addErrorMessage($saved_image_name);
				$this->_template = new Template('user', 'update_profile');
				$this->setLegitControllerCommonData('', 'update_profile');
				$this->update_profile();
				return;
			}
		}
		
		if (!User::isUserLogged()) {
			$data['user_email']		= $_SESSION['reg_user']['email'];
			$data['user_password'] 	= encryptPassword($data['user_password']);
		}
		if(User::isUserLogged()) {
			$data['user_email']		= $_SESSION['logged_user']['user_email'];
			//$data['user_password'] 	= encryptPassword($data['user_password']);
		}
		//echo "<pre>".print_r($data,true)."</pre>";exit;
		if (!$this->User->addWriterProfile($data)) {
            Message::addErrorMessage($this->User->getError());
            $this->_template = new Template('user', 'update_profile');
			$this->setLegitControllerCommonData('', 'update_profile');
            $this->update_profile();
            return;
        }
		
		
		
		if (User::isUserLogged()) {
			Message::addMessage( Utilities::getLabel( 'L_Profile_updated' ) );		
			redirectUser(generateUrl('user', 'update_profile'));
		}
		else {
			if(!$this->User->validateLogin($data['user_email'], $post['user_password'])) {
				Message::addErrorMessage($this->User->getError()); 
				$this->_template = new Template('user', 'signin');
				$this->setLegitControllerCommonData('', 'signin');
				$this->signin();
				return;
			}
			unset($_SESSION['reg_user']);
			$param = array('to'=>$data['user_email'],'temp_num'=>50,'user_screen_name'=>$data['user_screen_name']);
			//echo "<pre>".print_r($param,true)."</pre>";exit;
			if(!$this->task->sendEmail($param)){
				Message::addErrorMessage( $this->task->getError( ) );
			} else { 
				Message::addMessage( Utilities::getLabel( 'S_Profile_Submitted_For_Approval' ) );
			}
			redirectUser(generateUrl('user', 'signup_message',array(2)));
		}
	}
	
	public function writer_profile() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged() && !isset($_SESSION['reg_user']) && $_SESSION['reg_user']['is_email_verified'] !== 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
        $frm = $this->getWriterProfileForm();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
		
        $this->_template->render();
	}
	
	private function getWriterProfileForm($user_lang_arr=array()) {
		$post = Syspage::getPostedVar();
		$arr_months = array();
		$arr_years	= array();
		
		for ($i=1;$i<=12;$i++) {
			$arr_months[$i] = $i < 10 ? '0' . $i : $i;
		}
		
		for ($i=date('Y');$i>=(date('Y')-100);$i--) $arr_years[$i] = $i;	
		$frm = new Form('frmWriterProfile');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="'.((User::isUserLogged())?'infoTable':'formTable').'"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width="40%"');
        $frm->setAction(generateUrl('user', 'submit_writer_profile'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'user_id', '', 'user_id');
		$frm->addHiddenField('', 'reg_email', $_SESSION['reg_user']['email'], 'reg_email');
		
		$fld_email = $frm->addHtml( Utilities::getLabel( 'L_Email' ), 'user_email', $_SESSION['reg_user']['email']);
		$fld_email->html_before_field = '<div class="border">';
		$fld_email->html_after_field = '</div>';
			
		$fld_pass = $frm->addPasswordField( Utilities::getLabel( 'L_Password' ), 'user_password', '', 'user_password');
		$fld_pass->requirements()->setRequired();
		$fld_pass->requirements()->setLength(6,20);
		
		$fld_conf_pass = $frm->addPasswordField( Utilities::getLabel( 'L_Confirm_Password' ) , 'user_password_1', '', 'user_password_1');
		$fld_conf_pass->requirements()->setRequired();
		$fld_conf_pass->requirements()->setLength(6,20);
		$fld_conf_pass->requirements()->setCompareWith('user_password');
		
		if (User::isUserLogged()) {
			$fld_user = $frm->addHtml( Utilities::getLabel( 'L_Username' ), 'user_screen_name', '');
			$fld_user->html_before_field = '<div class="border">';
			$fld_user->html_after_field = '</div>';
		}
		else {
			$fld_screen_name = $frm->addRequiredField( Utilities::getLabel( 'L_Username' ), 'user_screen_name', '', 'user_screen_name');
			$fld_screen_name->setUnique('tbl_users', 'user_screen_name', 'user_id', 'user_id', 'user_id');
			$fld_screen_name->requirements()->setUsername();
		}
		
		$frm->addRequiredField( Utilities::getLabel( 'L_First_Name' ), 'user_first_name', '', 'user_first_name');
		$frm->addRequiredField( Utilities::getLabel( 'L_Last_Name' ), 'user_last_name', '', 'user_last_name');
		$frm->addSelectBox( Utilities::getLabel( 'L_Gender' ), 'user_sex', array(1=>'Male', 0=>'Female'), '', '', 'Select', 'user_sex')->requirements()->setRequired();
		$frm->addSelectBox( Utilities::getLabel( 'L_Country' ), 'user_country_id', $this->common->getCountries(), '', '', Utilities::getLabel( 'L_Select' ), 'user_country_id')->requirements()->setRequired();
		$frm->addRequiredField( Utilities::getLabel( 'L_City' ), 'user_city', '', 'user_city');
		$frm->addTextBox( Utilities::getLabel( 'L_Zip' ), 'user_zip', '', 'user_zip');
		$frm->addRequiredField( Utilities::getLabel( 'L_Cell_Phone' ), 'user_mobile', '', 'user_mobile');
		$frm->addSelectBox( Utilities::getLabel( 'L_Time_zone' ), 'user_timezone', '', '', '', 'Select', 'user_timezone');
		
		$fld_work = $frm->addRadioButtons( Utilities::getLabel( 'L_Ques_Work_For_Any_Other_Online_Academic' ), 'user_is_experienced', array( 1 => Utilities::getLabel( 'L_Yes' ), 2 => Utilities::getLabel( 'L_No' ) ), 2, 2, 'class="radio_buttons"','onclick="showCompanyName(this.value);"');
		$fld_comp_name = $frm->addTextBox( '', 'uexp_company_name','','','disabled="disabled" style="display:none"');
		$fld_comp_name->html_before_field='<span class="checklist" id="companyname">';
		$fld_comp_name->html_after_field='</span>';
		
		
		$frm->addCheckBoxes( Utilities::getLabel( 'L_Citation_Styles_Familiar_With' ) . '<span class="spn_must_field">*</span>', 'citations', $this->common->getCitationStyles(), array(), 4,'class="check_buttons"');
		
		//$cit->requirements()->setRequired();
		//$cit->requirements()->setCustomErrorMessage('Citation Styles is mandatory.');
		$user_languages = $this->User->getUserLanguages(User::getLoggedUserAttribute('user_id'));
		//$user_lang_arr = array();
		if(isset($user_languages) && empty($user_lang_arr)) {
			foreach($user_languages as $ele) {
				$user_lang_arr[] = $ele['lang_id'];
			}
		}
		$lang = '<ul class="check_list">';
		foreach($this->common->getLanguages() as $key=>$lg) {
			$lang .= '<li><input type="checkbox" name="user_lang_id[]" value="'.$key.'" '.(in_array($key,$user_lang_arr)?'checked="checked"':'').'> '.$lg.'</li>';
		}
		$lang.= '</ul>';
		//echo "<pre>".print_r($lang,true)."</pre>";
		$frm->addHTML( Utilities::getLabel( 'L_Ques_Your_Native_Language' ), 'user_lang', $lang );
		//$frm->addSelectBox('What is your native language?', 'user_lang_id[]', $this->common->getLanguages(), $user_lang_arr, 'multiple="multiple"', 'Select', 'user_lang_id');
		$frm->addSelectBox(Utilities::getLabel( 'L_Ques_Highest_Verifiable_Academic_Degree' ), 'user_deg_id', $this->common->getAcademicDegrees(), '', '', Utilities::getLabel( 'L_Select' ), 'user_deg_id')->requirements()->setRequired();
		$frm->addHTML('', '', '<h3>' . Utilities::getLabel( 'L_Education' ) . '</h3>',true);
		$frm->addRequiredField( Utilities::getLabel( 'L_Name_of_the_University' ), 'education[usered_university]', '', 'edu_university');
		$frm->addRequiredField( Utilities::getLabel( 'L_Degree' ), 'education[usered_degree]', '', 'edu_degree');
		$frm->addSelectBox( Utilities::getLabel( 'L_Graduation_year' ), 'education[usered_year]', $arr_years, '', '', Utilities::getLabel( 'L_Select' ), 'edu_year' )->requirements()->setRequired();
		$fld = $frm->addFileUpload( Utilities::getLabel( 'L_Certificate' ), 'edu_diploma', 'edu_diploma')->requirements()->setRequired( );
		$fld->html_after_field = ' <small>' . Utilities::getLabel( 'L_Certificate_Upload_File_Extensions_Restriction' ) . '</small>';
		
		$frm->addHTML('', '', '<h3>' . Utilities::getLabel( 'L_Additional_Information' ) . '</h3>',true);
		$frm->addTextArea( Utilities::getLabel( 'L_Additional_CV_information' ), 'user_more_cv_data', '', 'user_more_cv_data')->requirements()->setRequired();
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Save_Profile' ), 'btn_submit','class="greenBtn"');
		$frm->setValidatorJsObjectName('signup_validator');
		$frm->setOnSubmit('check_box_validation( this )');
		return $frm;
	}
	
	public function submit_customer_profile( ) {
		$post = Syspage::getPostedVar( );
		
		$frm = $this->getCustomerProfileForm( );
		
		if ( !$frm->validate( $post ) ) { 
			$arr_errors = $frm->getValidationErrors();
			
            foreach ($arr_errors as $val) Message::addErrorMessage($val);
            $this->_template = new Template('user', 'update_profile');
			$this->setLegitControllerCommonData('', 'update_profile');
            $this->update_profile();
            return;
		}
		
		if (isset($post['user_screen_name'])) unset($post['user_screen_name']);
		
		$data = $post;
		if($_SESSION['logged_user']['is_customer_email_verified'] != 1 && $data['user_email'] != User::getLoggedUserAttribute('user_email')) {
			if($this->User->sendEmailVerificationCustomer(User::getLoggedUserAttribute('user_email'),$data['user_email'],$data['user_first_name'])){
				$data['user_email'] = User::getLoggedUserAttribute('user_email');
				Message::addMessage( Utilities::getLabel( 'S_Check_Inbox_To_Verify_Email' ) );
			}
		}
		//echo '<pre>'.print_r($data,true).'</pre>'; exit;
		
		/* if (is_uploaded_file($_FILES['user_photo']['tmp_name'])){
			if (saveImage($_FILES['user_photo']['tmp_name'], $_FILES['user_photo']['name'], $saved_image_name, true)){
				$data['user_photo'] = $saved_image_name;
			}
			else {
				Message::addErrorMessage($saved_image_name);
			}
		} */
		
		if (!$this->User->addUser($data)) {
			Message::addErrorMessage($this->User->getError());
		}
		else {
			Message::addMessage( Utilities::getLabel( 'L_Profile_updated' ) );
		}
		
		redirectUser(generateUrl('user', 'update_profile'));
	}
	
	public function update_profile() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) {
			if (isset($_SESSION['reg_user']) && $_SESSION['reg_user']['is_email_verified'] === 1) {
				$frm = $this->getWriterProfileForm($post['user_lang_id']);
				$fld_timezone = $frm->getField('user_timezone');
				$frm->removeField($fld_timezone);
			}
			else {
				redirectUser(generateUrl('user', 'signin'));
			}
		}
		else {
			if(User::isWriter()){
				if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}
			}
			if (!User::isUserActive()) {
				Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
				redirectUser(generateUrl('user', 'signin'));
			}
			$arr_account_info = $this->User->getUserDetailsById(User::getLoggedUserAttribute('user_id'));
			unset($arr_account_info['user_password']);
			
			if ($arr_account_info['user_type'] == 0) {
				//echo "<pre>".print_r($_SESSION,true)."</pre>";
				$first_order = Common::is_first_order_done();
				if($first_order == false) {
					redirectUser(generateUrl('task','my_orders'));
					return;
				}
				$frm = $this->getCustomerProfileForm();
				
				$fld_sub = $frm->getField('subscription_id');
				$fld_sub->value = $this->User->getUserSubscriptions();
			}
			else {
				$frm = $this->getWriterProfileForm();
				
				$fld_pass = $frm->getField('user_password');
				$fld_pass->requirements()->setRequired(false);
				$frm->removeField($fld_pass);
				
				$fld_pass1 = $frm->getField('user_password_1');
				$fld_pass1->requirements()->setRequired(false);
				$frm->removeField($fld_pass1);
				
				
				
				/* writer profile data */
				$user_profile_data = $this->User->getWriterProfileData(User::getLoggedUserAttribute('user_id'));				
				$frm->addHiddenField('', 'experience[uexp_id]', $user_profile_data['uexp_id']);
				$frm->addHiddenField('', 'education[usered_id]', $user_profile_data['usered_id']);
				
				
				$arr_account_info += $user_profile_data;
				if(isset($arr_account_info['education']['usered_file'])) {
					$fld_certi = $frm->getField('edu_diploma');
					$fld_certi->requirements()->setRequired(false);
					$download_file = '';
					$fld_certi->html_after_field = '<div class="attachments"><a href="'.generateUrl('user', 'download_file', array($file['file_id'])).'" class="attachlink attachicon">'.$arr_account_info['education']['usered_file'].'</a></div> <small>' . Utilities::getLabel( 'L_Certificate_Upload_File_Extensions_Restriction' ) . '</small>';
				}
				$user_languages = $this->User->getUserLanguages(User::getLoggedUserAttribute('user_id'));
				
				foreach($user_languages as $ele) {
					$user_lang_arr[] = $ele['lang_id'];
				}
				$arr_account_info['user_lang_id'] = $user_lang_arr;
				
			}
			
			/** get time zones **/
			$arr_timezone_identifiers = timezone_identifiers_list();
			$arr_timezones = array();
			
			foreach ($arr_timezone_identifiers as $val) {
				$arr_timezones[$val] = $val;
			}
			
			$fld_timezone = $frm->getField('user_timezone');
			$fld_timezone->options = $arr_timezones;
			$frm->fill($arr_account_info);
			
			$this->set('user_type', $arr_account_info['user_type']);
		}
		//echo $frm->getFieldHTML('user_lang');
		$frmChngPass = $this->getChangePasswordForm();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $frm->fill($post);
        }
		$this->set('frmChngPass',$frmChngPass);
        $this->set('frm', $frm);
		
		$this->set('orders_in_progress', $this->order->getUserInProgressOrders());
		$this->set('orders_completed', $this->order->getUserCompletedOrders());
		$this->set('user_rating', $this->ratings->getAvgUserRating());
		
        $this->_template->render();
	}
	
	private function getCustomerProfileForm() {
		$subscription = $this->common->getSubscriptions();
		$frm = new Form('frmCustomerProfile');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="infoTable"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width="30%"');
        $frm->setAction(generateUrl('user', 'submit_customer_profile'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'user_id', User::getLoggedUserAttribute('user_id'), 'user_id');
		
		$fld = $frm->addHtml( Utilities::getLabel( 'L_User_ID' ),'user_ref_id','');
		$fld->html_before_field='<div class="border">#';
		$fld->html_after_field='</div>';
		
		/* $fld_email = $frm->addHtml('Email', 'user_email', $_SESSION['reg_user']['email']);
		$fld_email->html_before_field = '<div class="border">';
		$fld_email->html_after_field = '</div>'; */
		
		$user_email = $frm->addEmailField( Utilities::getLabel( 'L_Email_ID' ), 'user_email', '', 'user_email');
		$user_email->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
		
		$frm->addRequiredField( Utilities::getLabel( 'L_First_Name' ),'user_first_name','','user_first_name');
		$frm->addRequiredField( Utilities::getLabel( 'L_Last_Name' ),'user_last_name','','user_last_name');
		
		
		$frm->addSelectBox( Utilities::getLabel( 'L_Time_zone' ), 'user_timezone', '', '', '', Utilities::getLabel( 'L_Select' ), 'user_timezone');
		
		$frm->addCheckBoxes( Utilities::getLabel( 'L_Subscription' ), 'subscription_id', $subscription,array(),1);
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Save_Profile' ) );
		
		return $frm;
	}
	
	public function add_photo() {
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$arr_valid_ext	= array('jpg', 'jpeg', 'png','JPG','JPEG','PNG');
				
		$path_parts = pathinfo($_FILES['user_photo']['name']);
		
		$file_ext	= $path_parts['extension'];
		$base_name	= $path_parts['basename'];
		
		
		if (is_uploaded_file($_FILES['user_photo']['tmp_name'])){
			if (!in_array($file_ext,$arr_valid_ext)) {
				Message::addErrorMessage( sprintf( Utilities::getLabel( 'L_Could_Not_Upload_File' ), $base_name ) );
				redirectUser(generateUrl('user', 'update_profile'));
			}
				
			if ($_FILES['user_photo']['size'] > 10695475) {				//restrict file size to 10MB
				$limit = ini_get('upload_max_filesize');
				Message::addErrorMessage( sprintf( Utilities::getLabel( 'E_Could_Not_Upload_File_Size_Is_Big' ), $base_name, $limit ) );
				redirectUser(generateUrl('user', 'update_profile'));
			}
			
			if (saveImage($_FILES['user_photo']['tmp_name'], $_FILES['user_photo']['name'], $saved_image_name, true)){
				if (!$this->User->addPhoto($saved_image_name)) {
					unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $saved_image_name);
					Message::addErrorMessage($this->User->getError());
				}
				else {
					Message::addMessage( Utilities::getLabel( 'L_Image_uploaded' ) );
				}
			}
			else {
				Message::addErrorMessage($saved_image_name);
			}
		}else {
			Message::addErrorMessage( Utilities::getLabel( 'L_No_file_uploaded' ) );
			
		}
		
		redirectUser(generateUrl('user', 'update_profile'));
	}
	
	public function upload_photo() {
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$frm = new Form('frmImageUpload');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
		//$frm->captionInSameCell(true);
		//$frm->setLeftColumnProperties('width="40%"');
        $frm->setAction(generateUrl('user', 'add_photo'));
		$frm->setJsErrorDisplay('beforefield');
		$frm->setRequiredStarPosition('none');
		$frm->setFieldsPerRow(2);
		$fld_image = $frm->addHtml('','user_photo','<img src="' . generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),140,140)) . '" alt="Profile Image" />',true);
		$fld_photo = $frm->addFileUpload('', 'user_photo','user_photo','title="' . Utilities::getLabel( 'L_Upload_Profile_Image' ) . '" class="input-filetype"' );
		$fld_photo->requirements()->setRequired();
		
		$fld1 = $frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Upload' ),'','class="theme-btn"');
		$limit = ini_get('upload_max_filesize');
		$fld1->html_after_field = "<br><span>" . Utilities::getLabel( 'L_Certificate_Upload_File_Extensions_Restriction' ) . "</span><br><span>" . sprintf( Utilities::getLabel( 'L_Upload_File_Size_Limit' ), $limit ) . "</span>";
		
		$fld_photo->attachField($fld1);
		
		$this->set('frm', $frm);
    	
    	$this->_template->render(false,false);
	}
	
	function forgot_password() {
    	if (User::isUserLogged()) redirectUser(generateUrl('home'));
    	
    	$frm = new Form('frmForgotPassword');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('style="text-align:right;"');
		$frm->setJsErrorDiv('common_error_msg');
		
    	$frm->addEmailField( Utilities::getLabel( 'L_Email' ), 'user_email','','','placeholder="' . Utilities::getLabel( 'L_Email' ) . '"');
    	$frm->addRequiredField( Utilities::getLabel( 'L_Security_Code' ), 'security_code','','','placeholder="' . Utilities::getLabel( 'L_Security_Code' ) . '"');
    	$frm->addHTML('', 'code', '<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="image">');
    	$frm->addHTML('','reload','<a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></i></a>');
    	$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Submit' ),'','class="theme-btn"');
    	
    	$frm->setAction(generateUrl('user', 'email_password_instructions'));
    	
    	$this->set('frm', $frm);
    	
    	$this->_template->render();    	
    }
    
    function email_password_instructions(){
    	/* @var $db Database */
    	$db = &Syspage::getdb();
    	$post = Syspage::getPostedVar();
    	
    	if (User::isUserLogged()) redirectUser(generateUrl('home', 'default_action'));
    	
    	require_once 'securimage/securimage.php';
		
    	$img = new Securimage();
		
    	if(!$img->check($_POST['security_code'])){
    		Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_Code_Entered' ) );
			$this->_template = new Template('user', 'forgot_password');
			$this->forgot_password();
			return;
    		//redirectUser(generateUrl('user', 'forgot_password'));
    	}
    	
    	$srch = new SearchBase('tbl_users');
    	$srch->addCondition('user_email', '=', $post['user_email']);
    	$srch->addMultipleFields(array('user_id', 'user_email','user_first_name'));
    	$rs = $srch->getResultSet();
    	
    	if (!$row_user = $db->fetch($rs)) {
    		Message::addErrorMessage( Utilities::getLabel( 'L_Invalid_Email_ID' ) );
    		redirectUser(generateUrl('user', 'forgot_password'));
    	}
    	
    	$db->query("delete from tbl_user_password_reset_requests where uprr_expiry < '" . date('Y-m-d H:i:s') . "'");
    	$rs = $db->query("select * from tbl_user_password_reset_requests where uprr_user_id = " . $row_user['user_id']);
		
    	if ($row_request = $db->fetch($rs)){
    		Message::addErrorMessage( Utilities::getLabel( 'S_Reset_Password_Request_Already_Sent' ) );
    		redirectUser(generateUrl('user', 'forgot_password'));
    	}
    	
    	$token = encryptPassword(getRandomPassword(20));
		
    	$db->insert_from_array('tbl_user_password_reset_requests', array(
			'uprr_user_id'=>$row_user['user_id'],
			'uprr_token'=>$token,
			'uprr_expiry'=>date('Y-m-d H:i:s', strtotime("+1 DAY"))
    	));
    	
    	$reset_url = generateUrl('user', 'reset_password', array($row_user['user_id'] . '.' . $token));
		//die($reset_url);
		$data = array(
						'temp_num'=>1,
						'to'=>$row_user['user_email'],
						'reset_url'=>$reset_url,
						'user_first_name'=>$row_user['user_first_name']
		);
		
		$this->task->sendEmail($data);
    	
    	Message::addMessage( Utilities::getLabel( 'S_Reset_Password_Instruction_Sent' ) );
    	
    	redirectUser(generateUrl('user', 'forgot_password'));
    }
    
    function reset_password($token){
    	if (User::isUserLogged()) redirectUser(generateUrl('dashboard'));
    	
    	if (!$this->validatetoken($token)) die('Invalid token');
    	
    	$frm = new Form('frmResetPassword');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('style="text-align:right;"');
		$frm->setJsErrorDisplay('afterfield');
		
    	$frm->addHiddenField('', 'token', $token);
    	
    	$fld = $frm->addPasswordField( Utilities::getLabel( 'L_New_Password' ), 'user_password','','user_password','placeholder="' . Utilities::getLabel( 'L_New_Password' ) . '*"');
    	$fld->requirements()->setRequired();
    	$fld->requirements()->setLength(6,20);
    	$fld1 = $frm->addPasswordField( Utilities::getLabel( 'L_Confirm_New_Password' ), 'user_password1','','user_password1','placeholder="' . Utilities::getLabel( 'L_Confirm_New_Password' ) . '*"');
    	$fld1->requirements()->setRequired();
    	$fld1->requirements()->setCompareWith('user_password');
    	 
    	$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Submit' ),'','class="theme-btn"');
    	
    	$frm->setAction(generateUrl('user', 'password_reset'));
    	
    	$this->set('frm', $frm);
    	
    	$this->_template->render();
    }
    
    function password_reset(){
    	/* @var $db Database */
    	$db = &Syspage::getdb();
    	$post = Syspage::getPostedVar();
    	
    	if (User::isUserLogged()) redirectUser(generateUrl('dashboard'));
		
    	$frm = new Form('frmResetPassword');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('style="text-align:right;"');
		$frm->setJsErrorDisplay('afterfield');
		
    	$frm->addHiddenField('', 'token', $token);
    	
    	$fld = $frm->addPasswordField( Utilities::getLabel( 'L_New_Password' ), 'user_password');
    	$fld->requirements()->setRequired();
    	$fld->requirements()->setLength(6,20);
    	$fld1 = $frm->addPasswordField( Utilities::getLabel( 'L_Confirm_New_Password' ), 'user_password1');
    	$fld1->requirements()->setRequired();
    	$fld1->requirements()->setCompareWith('user_password');
    	 
    	$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Submit' ) );
    	
    	$frm->setAction(generateUrl('user', 'password_reset'));
		
		if(!$frm->validate($post)) {
			Message::addErrorMessage($this->getError);
			$errors = $frm->getValidationErrors();
			foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('user', 'reset_password',array($post['token']));
			$this->setLegitControllerCommonData('', 'reset_password');
            $this->reset_password($post['token']);
            return;
		}
    	if (!$this->validatetoken($post['token'])) die( Utilities::getLabel( 'L_Invalid_token' ) );
    	
    	$arr = explode('.', $post['token']);
    	$user_id = intval($arr[0]);
    	
    	if (!$db->update_from_array('tbl_users', array('user_password'=>encryptPassword($post['user_password'])), array('smt'=>'user_id = ?', 'vals'=>array($user_id)))) dieWithError($db->getError());
    	
    	$db->query("delete from tbl_user_password_reset_requests where uprr_user_id = " . $user_id);
		
		$data = $this->User->getUserDetailsById($user_id);
		$param = array('to'=>$data['user_email'],'user_first_name'=>$data['user_first_name'],'contact_url'=>CONF_SITE_URL.'/support','temp_num'=>49);
		
    	$this->task->sendEmail($param);
		
    	Message::addMessage( Utilities::getLabel( 'S_Password_successfully_updated' ) );
    	redirectUser(generateUrl('user', 'signin'));
    }
    
    protected function validatetoken($token){
    	/* @var $db Database */
    	$db = &Syspage::getdb();
    	
    	$arr = explode('.', $token);
    	
    	if (!is_numeric($arr[0])){
    		die( Utilities::getLabel( 'E_Invali_Request_Try_Exact_Link_In_Email' ) );
    	}
    	
    	$arr[0] = intval($arr[0]);
    	
    	$db->query("delete from tbl_user_password_reset_requests where uprr_expiry < '" . date('Y-m-d H:i:s') . "'");
    	$rs = $db->query("select * from tbl_user_password_reset_requests where uprr_user_id = " . $arr[0]);
    	$row = $db->fetch($rs);
    	if ( !$row || $row[ 'uprr_token' ] != $arr[1] ) { 
    		die( Utilities::getLabel( 'E_Invalid_Token_For_Reset_Password' ) );
    	}
    	return true;
    }
	
	public function photo($user_id, $w=0, $h=0) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) return;
		
		$sql = $db->query('SELECT user_photo FROM tbl_users WHERE user_id = ' . $user_id);
		
		$row = $db->fetch($sql);
		
		if (!$row || $row['user_photo'] == '') {
			if(!User::isWriter($user_id)) {
				$user_photo = 'images/Default-Images_cli.jpg';
			}else {
				$user_photo = 'images/Default-Images_wr.jpg';
			}
		}
		else {
			$user_photo = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['user_photo'];
		}
		
		getImage($user_photo,$w,$h);
	}
	
	public function change_password() {
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$frm = $this->getChangePasswordForm();
		
		$this->set('frm', $frm);		
		$this->_template->render();
	}
	
	public function update_password() {
		$post 	= Syspage::getPostedVar();
		$db		= &Syspage::getdb();
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$frm = $this->getChangePasswordForm();
		
		if (!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
            foreach ($arr_errors as $val) Message::addErrorMessage($val);
            $this->_template = new Template('user', 'change_password');
			$this->setLegitControllerCommonData('', 'change_password');
            $this->change_password();
            return;
		}
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('user_password', '=', encryptPassword($post['current_password']));
		
		$rs = $srch->getResultSet();
		
		if (!$db->fetch($rs)) {
			Message::addErrorMessage( Utilities::getLabel( 'L_Invalid_current_password' ) );
			redirectUser(generateUrl('user', 'update_profile'));
			exit;
		}
		
		if (!$this->User->updatePassword($post['new_password'])) {
			Message::addErrorMessage($this->User->getError());
		}
		else {
			Message::addMessage( Utilities::getLabel( 'L_Password_updated' ) );
		}
		
		redirectUser( generateUrl( 'user', 'update_profile' ) );
	}
	
	private function getChangePasswordForm() {
		$frm = new Form('frmResetPassword');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('align="right"');
        $frm->setAction(generateUrl('user', 'update_password'));
		$frm->setJsErrorDisplay('afterfield');
    	
		$fld1 = $frm->addPasswordField( Utilities::getLabel( 'L_Enter_current_password' ), 'current_password', '', 'current_password');
    	$fld1->requirements()->setRequired();
		$fld1->html_after_field="<td><small>" . Utilities::getLabel( 'N_Required_For_Changing_Current_Password' ) . "</small></td>";
		
    	$fld2 = $frm->addPasswordField( Utilities::getLabel( 'L_Choose_new_password' ), 'new_password', '', 'new_password');
    	$fld2->requirements()->setRequired();
    	$fld2->requirements()->setLength(6,20);
		$fld2->html_after_field="<td><small>" . Utilities::getLabel( 'M_Fill_The_Field_To_Change_Curretn_Password' ) . "</small></td>";
		
    	$fld3 = $frm->addPasswordField( Utilities::getLabel( 'L_Re-enter_new_password' ), 'confirmation_password', '', 'confirmation_password');
    	$fld3->requirements()->setRequired();
    	$fld3->requirements()->setCompareWith('new_password');
    	$fld3->html_after_field="<td><small>" . Utilities::getLabel( 'M_Fill_The_Field_To_Change_Curretn_Password' ) . "</small></td>";
		
    	$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Save' ) );
    	
    	$this->set( 'frm_password', $frm );
    	
    	return $frm;
	}
	
	public function experience_listing() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$this->set('arr_listing', $this->User->getWriterExperiences(User::getLoggedUserAttribute('user_id')));
    	
    	$this->_template->render();
	}
	
	public function add_experience() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$exp_id = intval($post['uexp_id']);
		
		if ($exp_id > 0) {
			$arr_exp = $this->User->getExperienceDetailsById($exp_id);
			if ($arr_exp === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		}
		
		unset($post['btn_submit']);
		
		$frm = $this->getWorkExperienceForm();
		
		if (!$frm->validate($post)) {
			$arr_errors = $frm->getValidationErrors();
            foreach ($arr_errors as $val) Message::addErrorMessage($val);
            $this->_template = new Template('user', 'manage_experience');
			$this->setLegitControllerCommonData('', 'manage_experience');
            $this->manage_experience();
            return;
		}
		
		if (!$this->User->addWriterExperience(User::getLoggedUserAttribute('user_id'),$post)) {
            Message::addErrorMessage($this->User->getError());
            $this->_template = new Template('user', 'manage_experience');
			$this->setLegitControllerCommonData('', 'manage_experience');
            $this->manage_experience();
            return;
        }
		
		if ($exp_id > 0) {
			Message::addMessage( Utilities::getLabel( 'L_Work_experience_updated' ) );
		}
		else {
			Message::addMessage( Utilities::getLabel( 'L_Work_experience_added' ) );
		}
		
		redirectUser(generateUrl('user', 'experience_listing'));
	}
	
	public function manage_experience($exp_id) {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$exp_id = intval($exp_id);
		
		if ($exp_id > 0) {
			$arr_exp = $this->User->getExperienceDetailsById($exp_id);
			if ($arr_exp === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
			
			$arr_from_date = explode('-', $arr_exp['uexp_from']);
			$arr_till_date = explode('-', $arr_exp['uexp_upto']);
			
			$arr_exp['from_month']	= $arr_from_date[1];
			$arr_exp['from_year']	= $arr_from_date[0];
			
			$arr_exp['till_month']	= $arr_till_date[1];
			$arr_exp['till_year']	= $arr_till_date[0];
		}
		
		$frm = $this->getWorkExperienceForm();
		
		if ($exp_id > 0) {
			$frm->fill($arr_exp);
		}
		
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $frm->fill($post);
        }
		
        $this->set('exp_id', $exp_id);
        $this->set('frm', $frm);
		
        $this->_template->render();
	}
	
	private function getWorkExperienceForm() {
		$arr_months = array();
		$arr_years	= array();
		
		for ($i=1;$i<=12;$i++) {
			$arr_months[$i] = $i < 10 ? '0' . $i : $i;
		}
		
		for ($i=date('Y');$i>=(date('Y')-100);$i--) $arr_years[$i] = $i;
		
		$frm = new Form('frmWorkExperience');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width="40%"');
        $frm->setAction(generateUrl('user', 'add_experience'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'uexp_id', '', 'uexp_id');
		
		$frm->addRequiredField( Utilities::getLabel( 'L_Name_of_the_company' ), 'uexp_company_name', '', 'exp_company');
		$frm->addSelectBox( Utilities::getLabel( 'L_Field_of_work' ), 'uexp_exfld_id', $this->common->getWorkFields( ), '', '', Utilities::getLabel( 'L_Select' ), 'exp_field' )->requirements( )->setRequired( );
		$frm->addRequiredField( Utilities::getLabel( 'L_Position' ), 'uexp_position', '', 'exp_position');
		
		$fld_exp_from_month = $frm->addSelectBox( Utilities::getLabel( 'L_From' ), 'from_month', $arr_months, '', 'style="width:100px;"', Utilities::getLabel( 'L_Month' ), 'exp_from_month');
		$fld_exp_from_year = $frm->addSelectBox('', 'from_year', $arr_years, '', 'style="width:100px; margin-left:10px;"', Utilities::getLabel( 'L_Year' ), 'exp_from_year');
		
		$fld_exp_from_month->attachField( $fld_exp_from_year );
		
		$fld_exp_till_month = $frm->addSelectBox( Utilities::getLabel( 'L_Till' ), 'till_month', $arr_months, '', 'style="width:100px;"', Utilities::getLabel( 'L_Month' ), 'exp_till_month');	
		$fld_exp_till_year = $frm->addSelectBox('', 'till_year', $arr_years, '', 'style="width:100px; margin-left:10px;"', Utilities::getLabel( 'L_Year' ), 'exp_till_year');
		
		$fld_exp_till_month->attachField( $fld_exp_till_year );
		
		$fld_save	= $frm->addSubmitButton( '', 'btn_submit', Utilities::getLabel( 'L_Save' ), 'btn_submit' );
		$fld_cancel	= $frm->addButton('', 'btn_cancel', Utilities::getLabel( 'L_Cancel' ), 'btn_cancel', 'onclick="document.location.href=\'' . generateUrl('user', 'experience_listing') . '\';" style="margin-left:10px;"');
		
		$fld_save->attachField( $fld_cancel );
		
		return $frm;
	}
	
	public function delete_experience($exp_id) {
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$exp_id = intval($exp_id);
		if ($exp_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$arr_exp = $this->User->getExperienceDetailsById($exp_id);
		if ($arr_exp === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		if (!$this->User->deleteUserExperience($exp_id)) {
			Message::addErrorMessage( $this->User->getError( ) );
		}
		else {
			Message::addMessage( Utilities::getLabel( 'L_Record_deleted' ) );
		}
		
		redirectUser(generateUrl('user', 'experience_listing'));
	}
	
	public function deactivate_account() {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$user_details = $this->User->getUserDetailsById(User::getLoggedUserAttribute('user_id'));
		if ($user_details === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		if(!$this->User->deactivateAccountRequest()) {
			die( Utilities::getLabel( 'E_Invalid_request' ) );
		}
		
		$param = array('to'=>$user_details['user_email'],'temp_num'=>5,'user_screen_name'=>(User::isWriter()== true)?$user_details['user_screen_name']:$user_details['user_first_name']);
		$this->task->sendEmail($param);
		unset($_SESSION['logged_user']);
		Message::addMessage( Utilities::getLabel( 'L_Your_acccout_has_been_deactivated_now' ) );
		
		redirectUser(generateUrl('user', 'update_profile'));
	}
	
	function re_activate_account($user_id) {
		$db = Syspage::getDB();
		if(intval($user_id) == 0) {
			die( Utilities::getLabel( 'L_Invalid_Request' ) );
		}
		
		$user_details = $this->User->getUserDetailsById($user_id);
		if ($user_details === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$srch = new SearchBase('tbl_user_email_verification_codes');
		$srch->addCondition('uevc_email','=',$user_details['user_email']);
		$srch->addFld('uevc_added_on');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		if(mktime() < strtotime($row['uevc_added_on']) ) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Verification_Email_Sent_To_Verify_Reactivation' ) );
			redirectUser(generateUrl('user','signin'));
		}
		$verification_code = getRandomPassword(15);
		
		$db->query("DELETE FROM tbl_user_email_verification_codes WHERE uevc_email = '" . $user_details['user_email'] . "'");
        
        if (!$db->insert_from_array('tbl_user_email_verification_codes', array('uevc_email'=>$user_details['user_email'], 'uevc_code'=>$verification_code,'uevc_added_on'=>'mysql_func_DATE_ADD(now(), INTERVAL 24 HOUR)'),true)){
            Message::addErrorMessage($db->getError());
			redirectUser(generateUrl('user','signin'));
        }
		
		$verification_url = 'http://' . $_SERVER['SERVER_NAME'] . generateUrl('user', 'verify_reactivation', array(base64_encode($user_details['user_email']) . '_' . $verification_code));
		$param = array('to'=>$user_details['user_email'],'temp_num'=>64,'verification_url'=>$verification_url,'user_screen_name'=>$user_details['user_first_name']);
		if(!$this->task->sendEmail($param)) {
			die($verification_url);
			Message::addErrorMessage($this->task->getError());
			redirectUser(generateUrl('user','signin'));
		}
		Message::addMessage( Utilities::getLabel( 'S_check_your_email_to_confirm_reactivation' ) );
		redirectUser(generateUrl('user','signin'));
	}
	
	public function verify_reactivation($code){
		if (!$this->User->verifyReactivation($code)){
			Message::addErrorMessage($this->User->getError());
			redirectUser(generateUrl('user', 'signin'));
			return false;
		}		
		redirectUser(generateUrl('user', 'update_profile'));
	}
	
	function signup_message($msg){
		if(Message::getMessageCount() > 0) {
			if($msg == 2) {
				if (!User::isUserLogged()) {die( Utilities::getLabel( 'E_Invalid_request' ) );return;}
				$var = 'profile';	//variable to verify that user have filled profile.			
			} elseif($msg == 1) {
				$var = 'email';		//variable to verify that user have verified email.
			} elseif($msg == 3) {
				$var = 'sample';
			} 
			$this->set('msg',$var);
			$this->_template->render();
			
		} else {
			redirectUser(generateUrl('user', 'writer_signup'));
		}
	}
	
	public function update_last_activity() {
		if (!User::isUserLogged()) return false;		
		if (!$this->User->updateUserLastActivity()) return false;
		
		return true;
	}
	
	public function get_online_status() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) return;
		
		$user_id = intval($post['user_id']);
		if ($user_id < 1) return;
		
		if (!$arr = $this->User->getUserDetailsById($user_id)) return;
	
		$online_status = strtotime($arr['user_last_activity']) < (time() - 60) ? 0 : 1;
		
		die(convertToJson(array('online_status'=>$online_status)));
	}
	
	public function search_writers() {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged() || User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_screen_name', 'LIKE', '%' . $_GET['term'] . '%');
		$srch->addCondition('user_type', '=', 1);
		$srch->addCondition('user_email_verified', '=', 1);
		$srch->addCondition('user_active', '=', 1);
		$srch->addCondition('user_is_approved', '=', 1);
		
		$srch->addMultipleFields(array('user_id', 'user_screen_name'));
		
		$rs = $srch->getResultSet();
		
		$arr_users = $db->fetch_all($rs);
		
		$invitees = array();
		
		foreach ($arr_users as $key=>$arr) {
			$invitees[$key]['id']		= $arr['user_id'];
			$invitees[$key]['label']	= $arr['user_screen_name'];
			$invitees[$key]['value']	= $arr['user_screen_name'];
		}
		
		$this->set('invitees', $invitees);
		
		$this->_template->render(false, false);
	}
	
	function user_wallet_summary() {
		die( Utilities::getLabel( 'E_Invalid_request' ) );
		$this->_template->render(false, false);
	}
	
	public function download_file() {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		/* $fid = intval($fid);
		if ($fid < 1) die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		
		$rs = $db->query("SELECT * FROM tbl_user_education WHERE usered_user_id = " . User::getLoggedUserAttribute('user_id'));
        if (!$row = $db->fetch($rs)) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		
		
		//$task_details = $this->Task->getTaskDetailsById($row['file_task_id']);
        
        $file_real_path = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['usered_file'];
        if (!is_file($file_real_path)) die( Utilities::getLabel( 'E_Invalid_request' ) );
        
        $fname = $row['usered_file'];
		
        $file_extension = strtolower(strrchr($fname, '.'));
		
        switch ($file_extension) {
			case '.txt':  $ctype='application/octet-stream'; break;
			
            case '.pdf': $ctype='application/pdf'; break;

            case '.doc':

            case '.docx':

                $ctype = 'application/msword';

                break;

            case '.xls':

            case '.xlsx':

                $ctype = 'application/vnd.ms-excel';

                break;

            case '.gif': $ctype = 'image/gif'; break;

            case '.png': $ctype = 'image/png'; break;

            case '.jpeg':

            case '.jpg': $ctype = 'image/jpg'; break;

            default: $ctype = 'application/force-download';
        }

        header("Pragma: public"); // required

        header("Expires: 0");

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

        header("Cache-Control: private",false); // required for certain browsers

        header("Content-Type: $ctype");

        header("Content-Disposition: attachment; filename=\"" . $row['usered_file']."\";" );

        header("Content-Transfer-Encoding: binary");

        header("Content-Length: " . filesize($file_real_path));
		
        readfile($file_real_path);
		
        exit();
    }
	
	function getCompanyField($usertype_id){
		global $db;
		
		$exp = $this->User->getWriterExperiences(User::getLoggedUserAttribute('user_id'));
		if(!is_numeric($usertype_id)) die( 'Select option first' );
        
		if($usertype_id==1)
		{
        $fld=new FormField('text', 'uexp_company_name', 'Company Name');  
		//if($lang_id==2) { $fld->requirements()->setRegularExpressionToValidate( '^[^\x00-\x80][^\x00-\x80][^\0x0020]{1,250}$');	 } 
		//else{ 
		//$fld->requirements()->setRegularExpressionToValidate( '^[A-Za-z][A-Za-z0-9 ]{1,250}$');	
		//}
		$fld->requirements()->setRequired();
		if(User::isUserLogged()) {
			$fld->value = $exp['uexp_company_name'];
			$fld->id = 'uexp_company_name';
		}
        $str=$fld->getHTML();
        
        $str .= '<script type="text/javascript">//<![CDATA[
        ';
        $str .= 'signup_validator_requirements.uexp_company_name=' . json_encode($fld->requirements()->getRequirementsArray()) . ';';      
		$str .= 'signup_validator.resetFields();';
        $str .= '
        //]]></script>';
        }
		else
		{
		$str = '<script type="text/javascript">//<![CDATA[
        ';
		$fld=new FormField('text', 'uexp_company_name', 'Company Name','');   
		$fld->extra='disabled="disabled"';
		$str=$fld->getHTML();
		$str .= '<script type="text/javascript">//<![CDATA[
        ';
		$str .= 'signup_validator_requirements.uexp_company_name=' . json_encode($fld->requirements()->getRequirementsArray()) . ';'; 
		$str .= 'signup_validator.resetFields();';
        $str .= '
        //]]></script>';
		}
        echo $str;         
	}
	
	public function facebook_login() {
		$this->_template->render(false,false);
	}
	
	public function add_invite_task($user_id) {
		$post = Syspage::getPostedVar();
		//if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));
		
		$user_id = intval($user_id);
		
        $frm = $this->getCustomerRegistrationForm();
        if($user_id > 0) {
			$fld = $frm->getField('invitee_ids');
			$fld->value = $user_id;
		}
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			unset($post['btn_submit']);
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
		$this->_template->render(false,false);
	}
	
	public function top_writers() {
		//if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));		
		$post = Syspage::getPostedVar();
		
		$page = (intval($post['page']) > 0) ? intval($post['page']) : 1 ;
		$uri=$_SERVER['REQUEST_URI'];
		$arr=explode('/',$uri);	
		
		$filter='';
		$filter_value='';
		if(isset($arr[2]) && !empty($arr[2])){
			#$filter='pep_type';
			$current_paper=$this->common->getPaperTypesByurl($arr[2]);						
			#$filter_value=$current_paper['paptype_id'];
		}		
		$arr_writers = $this->User->getTopWriters($page,$filter,$filter_value,15,true,$current_paper['paptype_id']);
		foreach ($arr_writers['data'] as $key=>$ele) {
			$writer_activities = $this->order->getWriterActivityStats($ele['user_id']);
			$arr_writers['data'][$key]['orders_completed'] = $writer_activities['orders_completed'];
			$arr_writers['data'][$key]['orders_in_progress'] = $writer_activities['orders_in_progress'];
			$arr_writers['data'][$key]['user_languages'] = $this->User->getUserLanguages($ele['user_id']);
		}
		//echo "<pre>".print_r($arr_writers,true)."</pre>";
		$this->set('arr_writers', $arr_writers);	
		
											
		$latest_content = Cmsblock::getContent('top_writer_1', 'content');
		$this->set('page_content', $latest_content);
		
		$latest_title = Cmsblock::getContent('top_writer_1', 'title');
		$this->set('page_title', $latest_title); 
		
		$Meta = new Meta();
		$meta = $Meta->getMetaById(4);		
		
		$arr_listing=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);

		$this->set('arr_listing', $arr_listing);		
		
		$this->set('paper_id', $current_paper['paptype_id']);
		$this->set('paper_types', $this->common->getPaperTypes());
		$this->_template->render();
	}
	
	public function writers() {
		//if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));		
		$post = Syspage::getPostedVar();
		$page = (intval($post['page']) > 0) ? intval($post['page']) : 1 ;
		$uri=$_SERVER['REQUEST_URI'];
		$arr=explode('/',$uri);	
		$filter='';
		$filter_value='';
		if(isset($arr[2]) && !empty($arr[2])){
			#$filter='pep_type';
			$current_paper=$this->common->getPaperTypesByurl($arr[2]);						
			#$filter_value=$current_paper['paptype_id'];
		}				
		$arr_writers = $this->User->getTopWriters($page,$filter,$filter_value,15,false,$current_paper['paptype_id']);
		foreach ($arr_writers['data'] as $key=>$ele) {
			$writer_activities = $this->order->getWriterActivityStats($ele['user_id']);
			$arr_writers['data'][$key]['orders_completed'] = $writer_activities['orders_completed'];
			$arr_writers['data'][$key]['orders_in_progress'] = $writer_activities['orders_in_progress'];
			$arr_writers['data'][$key]['user_languages'] = $this->User->getUserLanguages($ele['user_id']);
		}
		//echo "<pre>".print_r($arr_writers,true)."</pre>";
		$this->set('arr_writers', $arr_writers);
		$this->set('paper_id', $current_paper['paptype_id']);
		$this->set('paper_types', $this->common->getPaperTypes());
		$this->_template->render();
	}
	
	public function writers_discipline() {
		//if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));		
		$post = Syspage::getPostedVar();
		$page = (intval($post['page']) > 0) ? intval($post['page']) : 1 ;
		$uri=$_SERVER['REQUEST_URI'];
		$arr=explode('/',$uri);	
		$filter='';
		$filter_value='';
		if(isset($arr[2]) && !empty($arr[2])){
			#$filter='pep_type';
			$current_discipline=$this->common->getDisciplinesByurl($arr[2]);						
			#$filter_value=$current_paper['paptype_id'];
		}				
		$arr_writers = $this->User->getTopWriters($page,$filter,$filter_value,15,false,0,$current_discipline['discipline_id']);
		foreach ($arr_writers['data'] as $key=>$ele) {
			$writer_activities = $this->order->getWriterActivityStats($ele['user_id']);
			$arr_writers['data'][$key]['orders_completed'] = $writer_activities['orders_completed'];
			$arr_writers['data'][$key]['orders_in_progress'] = $writer_activities['orders_in_progress'];
			$arr_writers['data'][$key]['user_languages'] = $this->User->getUserLanguages($ele['user_id']);
		}
		//echo "<pre>".print_r($arr_writers,true)."</pre>";
		$this->set('arr_writers', $arr_writers);
		$this->set('discipline_id', $current_discipline['discipline_id']);
		$this->set('disciplines', $this->common->getDisciplines());
		$this->_template->render();
	}		
	
	public function search_top_writers() {
		//if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));		
		$post = Syspage::getPostedVar();		
		$data = $post;
		$page = (intval($post['page']) > 0) ? intval($post['page']) : 1 ;
		
		$arr_writers = $this->User->getTopWriters($page,$post['filter'],intval($post['filter_value']),15,$post['featured'],$post['paper_id'],$post['discipline_id']);
		foreach ($arr_writers['data'] as $key=>$ele) {
			$writer_activities = $this->order->getWriterActivityStats($ele['user_id']);
			$arr_writers['data'][$key]['orders_completed'] = $writer_activities['orders_completed'];
			$arr_writers['data'][$key]['orders_in_progress'] = $writer_activities['orders_in_progress'];
			$arr_writers['data'][$key]['user_languages'] = $this->User->getUserLanguages($ele['user_id']);
		}
		$this->set('filter',$post['filter']);
		$this->set('filter_value',intval($data['filter_value']));
		$this->set('arr_writers', $arr_writers);
		$this->_template->render(false,false);
	}
	
	public function writer($user_id) {
		/* if(User::isWriter()) {
			redirectUser(generateUrl('page','error_404'));
			return false;
		} */
		$uri=$_SERVER['REQUEST_URI'];
		$arr=explode('/',$uri);		
		$arr_user_id=$this->User->getUserIdByUserName($arr[2]);		
		$user_id=$arr_user_id['user_id'];
		if(empty($user_id)){
			Message::addErrorMessage( Utilities::getLabel( 'E_Writer_Does_Not_Exists' ) );
			redirectUser(generateUrl('top-writers'));
		}
		$user_info = $this->User->getUserDetailsById($user_id);
		$user_info = array_merge($user_info,$this->User->getWriterProfileData($user_id));
		$user_info['user_rating'] = $this->ratings->getAvgUserRating($user_id);
		$user_info['activities'] = $this->order->getWriterActivityStats($user_id);
		$user_info['user_languages'] = $this->User->getUserLanguages($user_id);
		$disciplines = $this->common->getDisciplines();
		
		foreach($disciplines as $key=>$descipline){
			$user_feedbacks_count = $this->reviews->getFeedbackReceived($user_id,1,array('disp_id'=>$key));									
			if(count($user_feedbacks_count['feedback'])<1){
				unset($disciplines[$key]);							
			}
		}	
/* 		$last=end($arr);
		$discipline=$this->common->getDisciplinesByurl($last);				
		$user_feedbacks='';
		if(!empty($discipline))
		{ */		
			$user_feedbacks = $this->reviews->getFeedbackReceived($user_id,$page);
			foreach($user_feedbacks['feedback'] as $key=>$val){
				$user_feedbacks['feedback'][$key]['review'] = $this->reviews->getTaskReview($val['task_id'],$val['rev_reviewee_id']);			
				$user_feedbacks['feedback'][$key]['reviewee_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewee_id']);			
				$user_feedbacks['feedback'][$key]['reviewer_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewer_id']);			
			}
			//echo "<pre>".print_r($user_feedbacks,true)."</pre>";
		//}
		$this->set('arr_user',$user_info);
		$this->set('disciplines',$disciplines);
		$this->set('user_feedbacks',$user_feedbacks);
		$this->_template->render();
	}
	
	function remove_profile_image( ) { 
		if (!User::isUserLogged()) dieWithError( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		if(!$this->User->removeProfilePhoto()) {
			die(convertToJson(array('status'=>0,'msg'=>$this->User->getError(),'user_id'=>User::getLoggedUserAttribute('user_id'))));
		}
		die( convertToJson( array( 'status' => 1,'msg' => Utilities::getLabel( 'L_Your_profile_photo_is_removed' ), 'user_id' => User::getLoggedUserAttribute( 'user_id' ) ) ) );
	}
}