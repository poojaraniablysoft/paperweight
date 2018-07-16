<?php
class User extends Model {
	private $user_id;
    private $attributes;
    protected $error;
	
	private $Task;
	private $test;
    
    function __construct($id) {
		$this->Task = new Task();
		$this->test = new Test();
		
        if (!is_numeric($id)) $id=0;
        
		$this->user_id = intval($id);
		
		if ($this->user_id < 1 && self::isUserLogged()){
            $this->user_id = self::getLoggedUserAttribute('user_id');
        }
		
        if (!($this->user_id > 0)){
            return;
        }
        
        $this->load_data();
    }
    
    function getUserId(){
    	return $this->user_id;
    }
    
    function getError(){
        return $this->error;
    }
    
	private function generateUserReferenceId() {
		$db = &Syspage::getdb();
		
		$sql = $db->query("SELECT user_ref_id FROM tbl_users");
		
		$arr_ref_id = $db->fetch_all_assoc($sql);
		$arr_ref_id = array_keys($arr_ref_id);
		
		do {
			$ref_id = substr(time()*rand(),0,6);
		} while (in_array($ref_id,$arr_ref_id));
		
		return $ref_id;
	}
	
    protected function load_data(){
		/* @var $db Database */
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_id', '=', $this->user_id);
		
		$rs = $srch->getResultSet();
		
        if (!$row = $db->fetch($rs)){
            $this->user_id = 0;
            return;
        }
        
        $this->attributes = $row;
    }
    
    function getAttribute($attr){
        if (!($this->user_id > 0)) {
            //throw new InvalidArgumentException('Invalid Request');
            return;
        }
        return $this->attributes[$attr];
    }
    
    function addUser($data) {
		$db = &Syspage::getdb();
		$user_id = intval($data['user_id']);
		$subscription = array();
        $record = new TableRecord('tbl_users');
		$record1 = new TableRecord('tbl_user_subscriptions');
		
        //$record->setFldValue('user_email',$data['user_email']);
		$record->assignValues($data);
		if ($user_id > 0) {
			if(!$record->update(array('smt'=>'user_id = ?', 'vals'=>array($user_id)))) {
				$this->error = $record->getError();
				return false;
			}
			
			$param = array('to'=>$data['user_email'],'user_screen_name'=>((User::isWriter($data['user_id']) == false)?$data['user_first_name']:User::getLoggedUserAttribute('user_screen_name')),'temp_num'=>55);
			$this->Task->sendEmail($param);
			
			if(!$delete = $db->deleteRecords('tbl_user_subscriptions',array('smt'=>'user_id = ?', 'vals'=>array($user_id)))) {
				$this->error = $delete->getError();
				return false;
			}
			
			if (count($data['subscription_id']) > 0) {
				foreach($data['subscription_id'] as $value) {
					$record1->setFldValue('user_id', User::getLoggedUserAttribute('user_id'));
					$record1->setFldValue('subscription_id',$value);
					
					if(!$record1->addnew()) {
						$this->error = $record1->getError();
						return false;
					}
				}
			}
			
			$this->user_id = $user_id;
		}
		else {
			$record->setFldValue('user_ref_id', $this->generateUserReferenceId());
			$record->setFldValue('user_regdate', 'mysql_func_NOW()', true);
			$record->setFldValue('user_is_featured', 0);
			$record->setFldValue('user_active', 1);
			$record->setFldValue('user_is_approved', 0);
			$record->setFldValue('user_test_end_time', 0);
			
			$record->assignValues($data);
			
			if (!isset($data['user_screen_name'])) $record->setFldValue('user_screen_name', $data['user_email']);
			
			if (!$record->addNew()){
				$this->error = $record->getError();
				return false;
			}
			
			$this->user_id = $record->getId();
		}
		
        $this->load_data();
        
        return true;
    }
	
	function sendVerificationEmail($user_email){
    	$db = &Syspage::getdb();
        
        $verification_code = getRandomPassword(15);
		
		$db->query("DELETE FROM tbl_user_email_verification_codes WHERE uevc_email = '" . $user_email . "'");
        
        if (!$db->insert_from_array('tbl_user_email_verification_codes', array('uevc_email'=>$user_email, 'uevc_code'=>$verification_code))){
            $this->error = $db->getError();
            return false;
        }
        
        $url = 'http://' . $_SERVER['SERVER_NAME'] . generateUrl('user', 'verify_email', array(base64_encode($user_email) . '_' . $verification_code));
		
		$data = array('temp_num'=>2,'to'=>$user_email,'verification_url'=>$url);
		
		$this->Task->sendEmail($data);
	   
        return true;
    }
	
	function sendEmailVerificationCustomer($user_email,$new_user_email,$user_name){
		$db = &Syspage::getdb();
        
        $verification_code = getRandomPassword(15);
		
		$db->query("DELETE FROM tbl_user_email_verification_codes WHERE uevc_email = '" . $user_email . "'");
        
        if (!$db->insert_from_array('tbl_user_email_verification_codes', array('uevc_email'=>$user_email, 'uevc_code'=>$verification_code))){
            $this->error = $db->getError();
            return false;
        }
        
        $url = 'http://' . $_SERVER['SERVER_NAME'] . generateUrl('user', 'verify_email', array(base64_encode($user_email) . '_' . $verification_code . '_' . base64_encode($new_user_email),true));
		
		$data = array('temp_num'=>62,'to'=>$user_email,'verification_url'=>$url,'user_email'=>$new_user_email,'user_first_name'=>$user_name);
		
		$this->Task->sendEmail($data);
	   
        return true;
	}
	
    function verifyEmail($code ,$customerEmailVerification=false) {
		$db = &Syspage::getdb();
		
        $user_email = $this->isVerificationCodeValid($code,$new_user_email);
		//die($new_user_email);
		if ($user_email === false) {
			$this->error = 'Invalid Code';
            return false;
		}
		
		if($customerEmailVerification === false) {
			if(User::isUserLogged()){
				unset($_SESSION['logged_user']);
			}
			$_SESSION['reg_user']['email']				= $user_email;
			$_SESSION['reg_user']['is_email_verified']	= 1;
			Message::addMessage('Your email has been verified. Fill in your profile to continue.');
        }else {
			if(User::isUserLogged()){
				$_SESSION['logged_user']['user_email'] = $new_user_email;				
				$record = new TableRecord('tbl_users');
				$record->setFldValue('user_email',$new_user_email);
				if(!$record->update(array('smt'=>'user_email = ?', 'vals'=>array($user_email)))) {
					$this->error = $record->getError();
					return false;
				}
				Message::addMessage('Your email id is updated successfully!');
			}			
		}
        $db->deleteRecords('tbl_user_email_verification_codes', array('smt'=>'uevc_email = ?', 'vals'=>array($user_email)));        
        return true;
    }
	
	public function verifyReactivation($code){
		$db = &Syspage::getdb();
		
        $user_email = $this->isVerificationCodeValid($code);
		//die($new_user_email);
		if ($user_email === false) {
			$this->error = 'Invalid Code';
            return false;
		}
		if(!User::isUserLogged()){
			$record = new TableRecord('tbl_users');
			$record->setFldValue('user_deactivate_request',0);
			if(!$record->update(array('smt'=>'user_email = ?', 'vals'=>array($user_email)))) {
				$this->error = $record->getError();
				return false;
			}
			Message::addMessage('Your account is no more deactivated now! You can sign in with your details.');
		}			
		
        $db->deleteRecords('tbl_user_email_verification_codes', array('smt'=>'uevc_email = ?', 'vals'=>array($user_email)));        
        return true;
	}
	
	public function isVerificationCodeValid($code,&$new_user_email) {
		$arr_code = explode('_', $code, 3);
        
        $user_email = base64_decode($arr_code[0]);
		$new_user_email = base64_decode($arr_code[2]);
		if (filter_var($user_email, FILTER_VALIDATE_EMAIL) === false) return false;
        if(!empty($new_user_email)) if (filter_var($new_user_email, FILTER_VALIDATE_EMAIL) === false) return false;
        $db = &Syspage::getdb();
        
        $rs = $db->query("SELECT * FROM tbl_user_email_verification_codes WHERE uevc_email = '" . $user_email . "'");
		
        $row = $db->fetch($rs);
		
        if ($row['uevc_email'] != $user_email || $row['uevc_code'] != $arr_code[1]) {
            return false;
        }
		
		return $row['uevc_email'];
	}
    
    /* function validateLogin($email, $password, $passwordAlreadyEncrypted = false){
        $db = &Syspage::getdb();
        
        if (!$passwordAlreadyEncrypted) $password = encryptPassword($password);
        
        $srch = new SearchBase('tbl_users');
        $srch->addCondition('user_email', '=', $email);
        
        $rs = $srch->getResultSet();
        
        if (!$row = $db->fetch($rs)){
            $this->error = 'Invalid Email ID or Password';
            return false;
        }
        
        if (strtolower($row['user_email']) !== strtolower($email) || $row['user_password'] !== $password){
            $this->error = 'Invalid Email ID or Password';
            return false;
        }
        
        if ($row['user_type'] == 1 && $row['user_email_verified'] != 1){
            $this->error = 'Your email ID has not been verified. Please check your mailbox to know how to verify your email.';
            return false;
        }
        
        if ($row['user_active'] != 1){
            $this->error = 'Your account is not active. Please contact the administrator.';
            return false;
        }
		
		//if ($row['user_type'] == 1 && $row['user_is_approved'] != 1){
          //  $this->error = 'Your account has not been approved. Please contact the administrator.';
          //  return false;
        //}
        
        $this->user_id = $row['user_id'];
        
        $this->attributes = $row;
        
        $attrs_session = array('user_id', 'user_email', 'user_screen_name');
        
        $_SESSION['logged_user'] = array();
        
        foreach ($attrs_session as $key){
            $_SESSION['logged_user'][$key] = $this->getAttribute($key);
        }
		
        return true;
    } */
	
	function validateLogin($email, $password, $passwordAlreadyEncrypted = false, $admin_logged=false){
        $db = &Syspage::getdb();
        
        if (!$passwordAlreadyEncrypted) $password = encryptPassword($password);
        
        $srch = new SearchBase('tbl_users');
        $srch->addCondition('user_email', '=', $email);
        
        $rs = $srch->getResultSet();
        
        if (!$row = $db->fetch($rs)){
            $this->error = Utilities::getLabel( 'L_Invalid_Email_ID_or_Password' );
            return false;
        }
        
        if (strtolower($row['user_email']) !== strtolower($email) || $row['user_password'] !== $password){
            $this->error = Utilities::getLabel( 'L_Invalid_Email_ID_or_Password' );
            return false;
        }
        
        if ($row['user_type'] == 1 && $row['user_email_verified'] != 1){
            $this->error = Utilities::getLabel( 'E_Email_ID_Has_Not_Been_Verified' );
            return false;
        }
		if ($row['user_deactivate_request'] == 1){
			$this->error = Utilities::getLabel( 'L_Your_account_has_been_de-activated_on' ) . ' '.$row['user_deativate_request_date'];
            return false;
		}
        
		if ($row['user_active'] != 1){
			$this->error = Utilities::getLabel( 'E_User_Deactivate_Notification' );
			return false;
		}
	   
		if ($row['user_type'] == 1 && $this->test->isUserFailed($row['user_id'])) {
			$this->error = Utilities::getLabel( 'E_Not_Authorized_To_Login' );
            return false;
		}
		
		if ($row['user_type'] == 1 && Common::is_essay_submit($row['user_id']) == 2) {
			$this->error = Utilities::getLabel( 'E_Not_Authorized_To_Login' );
            return false;
		}
        
        $this->user_id = $row['user_id'];
        
        $this->attributes = $row;
        
        $attrs_session = array('user_id', 'user_email', 'user_screen_name');
        
        $_SESSION['logged_user'] = array();
        
        foreach ($attrs_session as $key){
			if ($key == 'user_screen_name') {
				if ($row['user_type'] == 0) {
					$_SESSION['logged_user'][$key] = empty($row['user_first_name']) ? $row['user_screen_name'] : $row['user_first_name'];
				}
				else {
					$_SESSION['logged_user'][$key] = $row['user_screen_name'];
				}
			}
			else {
				$_SESSION['logged_user'][$key] = $this->getAttribute($key);
			}
        }
		
        return true;
    }
	
	public function authenticatePersistentLogin() {
		$db = &Syspage::getdb();
		
		if (!isset($_COOKIE['LEGITAUTHID'])) return false;
		
		$srch = new SearchBase('tbl_user_auth_tokens');
		
		$srch->addCondition('token_string', '=', $_COOKIE['LEGITAUTHID']);
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		$user_data = $this->getUserDetailsById($row['token_user_id']);
		if ($user_data === false) return false;
		
		$this->validateLogin($user_data['user_email'],$user_data['user_password'],true);
	}
	
	public function setAuthToken() {
		$db = &Syspage::getdb();
		
		/* generate token */
		$sql = $db->query("SELECT token_string FROM tbl_user_auth_tokens");
		
		$arr_token_id = $db->fetch_all_assoc($sql);
		$arr_token_id = array_keys($arr_token_id);
		
		do {
			$token = md5(getRandomPassword(10));
		} while (in_array($token,$arr_token_id));
		/* ### */
		
		$record = new TableRecord('tbl_user_auth_tokens');
		
		$record->assignValues(array('token_user_id'=>User::getLoggedUserAttribute('user_id'), 'token_string'=>$token));
		
		if (!$record->addNew()) return false;
		
		return $token;
	}
	
	public function removeAuthToken() {
		$db = &Syspage::getdb();
		
		if (!$db->deleteRecords('tbl_user_auth_tokens', array('smt'=>'token_string = ?', 'vals'=>array($_COOKIE['LEGITAUTHID']), 'execute_mysql_functions'=>false))) return false;
		
		return true;
	}
	
	function validateUserVerification($email) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_users');
        $srch->addCondition('user_email', '=', $email);
        
        $rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)){
            $this->error = Utilities::getLabel( 'L_Invalid_Email_ID' );
            return false;
        }
		
		if ($row['user_email_verified'] == 1){
            $this->error = Utilities::getLabel( 'S_Email_Already_Verified' );
            return false;
        }
		
		return $row['user_id'];
	}
    
    static function getLoggedUserAttribute($attr){
        return $_SESSION['logged_user'][$attr];
    }
    
    static function isUserLogged($consider_admin_approval = false){
        if (!($_SESSION['logged_user']['user_id'] > 0)) return false;
        if ($consider_admin_approval){
        	if (self::getLoggedUserAttribute('user_verified_by_admin')==0) return false;
        }
        return true;
    }
	
	public static function isWriter($user_id) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = self::getLoggedUserAttribute('user_id');
		
		if ($user_id < 1) return false;
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_id', '=', $user_id);
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		if ($row['user_type'] != 1) return false;
		
		return true;
	}
	
	public function updatePassword($password) {
		$record = new TableRecord('tbl_users');
		
        $record->setFldValue('user_password', encryptPassword($password));
        
        if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(self::getLoggedUserAttribute('user_id'))))){
            $this->error = $record->getError();
            return false;
        }
		
		$param = array('to'=>self::getLoggedUserAttribute('user_email'),'temp_num'=>49,'user_first_name'=>'User','contact_url'=>CONF_SITE_URL.'/support');
		$this->Task->sendEmail($param);
		
        return true;
	}
	
	public function addPhoto($img) {
		$record = new TableRecord('tbl_users');
		
		$record->setFldValue('user_photo', $img);
        
        if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(self::getLoggedUserAttribute('user_id'))))){
            $this->error = $record->getError();
            return false;
        }
		
        return true;
	}
	
	public function getUserDetailsById($uid) {
		$db = &Syspage::getdb();
		
		$uid = intval($uid);		
		if ($uid < 1) return false;
		
		$srch = new SearchBase('tbl_users','u');
		
		$srch->addCondition('u.user_id', '=', $uid);
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		return $row;
	}
	
	public function getUserIdByUserName($user_name){
		$db = &Syspage::getdb();		
		
		if ($user_name == '') return false;
		
		$srch = new SearchBase('tbl_users','u');
		
		$srch->addCondition('u.user_screen_name', '=', $user_name);
		
		$srch->addFld('user_id');
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		return $row;
		
	}
	
	public function getUserSubscriptions() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_subscriptions');
		
		$srch->addCondition('user_id', '=', self::getLoggedUserAttribute('user_id'));
		
		$srch->addFld('subscription_id');
		
		$rs = $srch->getResultSet();
		
		$arr = $db->fetch_all_assoc($rs);
		
		return array_keys($arr);
	}
	
	public function addWriterProfile($data) {
		$user_id = intval($data['user_id']);
		
		if ($user_id < 1) {
			$data['user_type']				= 1;
			$data['user_email_verified']	= 1;
			$data['user_test_passed']		= 0;
			$data['user_sample_essay']		= 0;
		}
		
		if (!$this->addUser($data)) return false;
		
		$user_profile_data = $this->getWriterProfileData($this->user_id);		
		$record = new TableRecord('tbl_user_profile');		
        $record->assignValues($data);        
		if ($user_profile_data === false) {
			$record->setFldValue('userprofile_user_id', $this->user_id);			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
			}
		}
		else {			
			if (!$record->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array($user_profile_data['userprofile_user_id'])))) {
				$this->error = $record->getError();
				return false;
			}
		}
		
		$this->addWriterEducation($this->user_id, $data['education']);
		$exp['experience'] = array('uexp_id'=>$data['experience']['uexp_id'],'uexp_company_name'=>$data['uexp_company_name']);
		$this->addWriterExperience($this->user_id, $exp['experience']);		
		$this->addUserCitationStyles($this->user_id, $data['citations']);
		$this->addUserLanguages($this->user_id, $data['user_lang_id']);
        
        return true;
	}
	
	public function addWriterEducation($user_id, $data) {
        $record = new TableRecord('tbl_user_education');
		
        $record->setFlds($data);
        
		if (intval($data['usered_id']) > 0) {
			if (!$record->update(array('smt'=>'usered_id = ?', 'vals'=>array($data['usered_id'])))) {
				$this->error = $record->getError();
				return false;
			}
		}
		else {
			$record->setFldValue('usered_user_id', $user_id);
			
			if (!$record->addNew()){
				$this->error = $record->getError();
				return false;
			}
		}
		
		return true;
	}
	
	public function addWriterExperience($user_id, $data) {
		
        $record = new TableRecord('tbl_user_experience');		
        $record->setFldvalue('uexp_company_name',$data['uexp_company_name']);
		
		if (intval($data['uexp_id']) > 0) {
			if (!$record->update(array('smt'=>'uexp_id = ?', 'vals'=>array($data['uexp_id'])))) {
				$this->error = $record->getError();
				return false;
			}
		}
		else {
			$record->setFldValue('uexp_user_id', $user_id);			
			if (!$record->addNew()){
				$this->error = $record->getError();
				return false;
			}
		}		
		return true;
	}
	
	public function addUserCitationStyles($user_id, $arr_cs) {
		$db = &Syspage::getdb();
		
		$arr_cs = (array) $arr_cs;
		if (count($arr_cs) == 0) return false;
		
		$db->deleteRecords('tbl_user_citation_styles', array('smt'=>'ucitstyle_user_id = ?', 'vals'=>array($user_id)));
		
		$record = new TableRecord('tbl_user_citation_styles');
		
		foreach ($arr_cs as $val) {
			$record->setFldValue('ucitstyle_user_id', $user_id);
			$record->setFldValue('ucitstyle_citstyle_id', $val);
			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
			}
		}
		
		return true;
	}
	
	private function addUserLanguages($user_id, $arr_lang) {
		$db = &Syspage::getdb();
		
		$arr_lang = (array) $arr_lang;
		if (count($arr_lang) == 0) return false;
		
		$db->deleteRecords('tbl_user_languages', array('smt'=>'userlang_user_id = ?', 'vals'=>array($user_id)));
		
		$record = new TableRecord('tbl_user_languages');
		
		foreach ($arr_lang as $val) {
			$record->setFldValue('userlang_user_id', $user_id);
			$record->setFldValue('userlang_lang_id', $val);
			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
			}
		}		
		return true;
	}
	
	public function deleteUserExperience($exp_id) {
		$db = &Syspage::getdb();
		
		if (!$db->deleteRecords('tbl_user_experience', array('smt'=>'uexp_id = ?', 'vals'=>array($exp_id)))) {
			$this->error = $db->getError();
			return false;
		}
		
		return true;
	}
	
	public function getWriterProfileData($uid) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_profile');
		
		$srch->addCondition('userprofile_user_id', '=', $uid);
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		$row['citations']	= $this->getUserCitationStyles($uid);
		$row['education']	= $this->getWriterEducationDetails($uid);
		$row['experience'] = $this->getWriterExperiences($uid);
		return $row;
	}
	
	public function getWriterEducationDetails($uid) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_education');
		
		$srch->addCondition('usered_user_id', '=', $uid);
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		return $row;
	}
	
	public function getWriterExperiences($uid) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_experience', 'e');
		
		//$srch->joinTable('tbl_experience_fields', 'INNER JOIN', 'e.uexp_exfld_id=f.exfld_id', 'f');
		
		$srch->addCondition('uexp_user_id', '=', $uid);
		
		$srch->addOrder('uexp_id');
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	}
	
	public function getExperienceDetailsById($exp_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_experience');
		
		$srch->addCondition('uexp_id', '=', $exp_id);
		$srch->addCondition('uexp_user_id', '=', self::getLoggedUserAttribute('user_id'));
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		return $row;
	}
	
	public function getUserCitationStyles($uid) {
		$db = &Syspage::getdb();
		
		$arr_citation_styles = array();
		
		$srch = new SearchBase('tbl_user_citation_styles');
		
		$srch->addCondition('ucitstyle_user_id', '=', $uid);
		
		$rs = $srch->getResultSet();
		
		while ($row = $db->fetch($rs)) {
			$arr_citation_styles[] = $row['ucitstyle_citstyle_id'];
		}
		
		return $arr_citation_styles;
	}
	
	public static function writerCanView() {
		$db = &Syspage::getdb();
		if (!self::isUserLogged()) return false;
		
		$srch = new SearchBase('tbl_user_profile');
		
		$srch->addCondition('userprofile_user_id', '=', User::getLoggedUserAttribute('user_id'));		
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		if ($row['user_test_passed'] != 1 /* || $row['user_sample_essay'] != 1 */) return false;
		
		return true;
	}
	
	public static function writerCanBid($writer_id) {
		$db = &Syspage::getdb();
		
		if (!self::isUserLogged()) return false;
		
		$writer_id = intval($writer_id);
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_id', '=', $writer_id);		
		$srch->addCondition('user_active','=',1);
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		if ($row['user_is_approved'] != 1) return false;
		
		return true;
	}
	
	public function updateUserLastActivity() {
		$db = &Syspage::getdb();
		
		if (!$db->update_from_array('tbl_users', array('user_last_activity'=>'mysql_func_NOW()'), array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))), true)) {
			return false;
		}
		
		return true;
	}
	
	public function getActiveWritersCount() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_type', '=', 1);
		$srch->addCondition('user_active', '=', 1);
		
		$srch->addFld('IFNULL(COUNT(*),0) AS active_writers');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['active_writers'];
	}
	
	public function getCurrentOnlineWritersCount() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_type', '=', 1);
		$srch->addCondition('mysql_func_UNIX_TIMESTAMP(user_last_activity)', '>', 'mysql_func_UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 60 SECOND))', 'AND', true);
		
		$srch->addFld('IFNULL(COUNT(*),0) AS current_online_writers');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['current_online_writers'];
	}
	
	public function getUserOnlineStatus($user_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_id', '=', $user_id);
		$srch->addCondition('mysql_func_UNIX_TIMESTAMP(user_last_activity)', '>', 'mysql_func_UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 60 SECOND))', 'AND', true);
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs) ? 'Online' : 'Offline';
	}
	
	/*** Selects featured writers or ones having a minimum rating of 4. ***/
	public function getTopWriters($page = 1, $filter = '', $filter_val,$pagesize=15,$featured=true,$paper_id,$disp_id) {
		$page = intval($page);
		$filter_val = intval($filter_val);
		$db = &Syspage::getdb();
		$pagesize = intval($pagesize);	
		#$pagesize = CONF_PAGINATION_LIMIT;
		$srch = new SearchBase('tbl_users', 'u');
		$srch->joinTable('tbl_user_profile','LEFT OUTER JOIN','up.userprofile_user_id = u.user_id','up');
		$srch->joinTable('(SELECT r1.rt_user_id, IFNULL((r1.rt_delivery_rating + r1.rt_communication_rating + r1.rt_quality_rating)/3,0) AS rating FROM tbl_ratings r1)', 'LEFT OUTER JOIN', 'u.user_id=r2.rt_user_id', 'r2');
		$srch->joinTable('(SELECT IFNULL(SUM(CASE WHEN t.task_status=3 AND b.bid_status=1 THEN 1 ELSE 0 END),0) AS orders_completed, b.bid_user_id, t.task_paptype_id AS task_paptype_id from tbl_bids b INNER JOIN tbl_tasks t on b.bid_task_id = t.task_id where b.bid_status = "1" and t.task_status="3" GROUP BY b.bid_user_id)','LEFT OUTER JOIN','u.user_id = bid.bid_user_id','bid');
		//$srch->joinTable('tbl_paper_types','LEFT OUTER JOIN','pt.peptype_id = bid.task_paptype_id','pt');
		if(intval($disp_id) > 0) {
			$srch->joinTable('tbl_tasks','LEFT OUTER JOIN','u.user_id = t.task_writer_id AND task_finished_from_writer="1"','t');	
		}		
		$srch->addCondition('u.user_type', '=', 1);
		$srch->addCondition('u.user_active', '=', 1);
		$srch->addCondition('u.user_is_approved', '=', 1);
		if($filter != '') {
			switch ($filter) {
				case 'completed_orders' :
					if($filter_val > 0) {
						$srch->addCondition('bid.orders_completed', '>=', $filter_val);
					}else {
						$srch->addDirectCondition('bid.orders_completed IS NULL','OR');
						$srch->addCondition('bid.orders_completed', '>=', $filter_val);
					}
					$srch->addOrder('bid.orders_completed','ASC');
					$srch->addOrder('user_rating','DESC');
				break;
 				case 'pep_type' :
					if($filter_val > 0) {
						$srch->addCondition('bid.task_paptype_id', '=', $filter_val);
					}
					$srch->addOrder('user_rating','DESC');
					$srch->addOrder('bid.orders_completed','ASC');
					break; 
				case 'by' :
					if($filter_val == 1) {
						$srch->addOrder('user_rating','DESC');
						$srch->addOrder('bid.orders_completed','ASC');
					}else {
						$srch->addOrder('bid.orders_completed','DESC');
						$srch->addOrder('user_rating','DESC');
					}
			}		
		} else {
			$srch->addOrder('user_rating','DESC');
			$srch->addOrder('bid.orders_completed','DESC');
		}	

		if(intval($paper_id) > 0) {
			$srch->addCondition('bid.task_paptype_id', '=', $paper_id);
			
			$srch->addOrder('user_rating','DESC');
			$srch->addOrder('bid.orders_completed','ASC');			
		}		
		
		if(intval($disp_id) > 0) {
			$srch->addCondition('t.task_discipline_id','=',intval($disp_id));
		}
		
		$srch->addGroupBy('u.user_id');
		if($featured){
			$cnd = $srch->addHaving('u.user_is_featured', '=', 1);
			$cnd->attachCondition('user_rating', '>=', 4);
		}
		 
		$srch->addMultipleFields(array('u.user_id', 'u.user_screen_name', 'u.user_is_featured','up.user_more_cv_data', 'IFNULL(TRUNCATE(ROUND(((AVG(r2.rating))*2))/2, 1), 0) AS user_rating','bid.orders_completed'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
	
		
		$rs = $srch->getResultSet();
		$rows['data'] 			=  $db->fetch_all($rs);
		$rows['pages']			= $srch->pages();
		$rows['page']			= $page;
		$rows['pagesize']		= $pagesize;
		
		return $rows;
	}
	
	public static function isUserActive() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_active', '=', 1);
		$srch->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
		$rs = $srch->getResultSet();
		if(!$db->fetch($rs)) {
			unset($_SESSION['logged_user']);
			return false;
		}else {
			return true;
		}
	}
	
	public static function isUserReqDeactive() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_deactivate_request', '=', 1);
		$srch->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
		$rs = $srch->getResultSet();
		if(!$db->fetch($rs)) {
			return false;
		}else {
			return true;
		}
	}
	
	public function addEssay($data) {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_user_profile');
		$srch->addCondition('userprofile_user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('user_sample_essay','=','0');
		$rs = $srch->getResultSet();	
		if(!$db->fetch($rs)){	
			$this->error = $db->getError();
			return false;
		}else {
			$record = new TableRecord('tbl_sample_essay');
			$record->setFlds($data);
			$record->setFldValue('file_uploaded_on', 'mysql_func_now()',true);
			if(!$record->addnew()) {
				$this->error = $record->getError();
				return false;
			}else {
				Task::sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>46,'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'user_email'=>User::getLoggedUserAttribute('user_email')));
				Task::sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'temp_num'=>47,'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'user_email'=>User::getLoggedUserAttribute('user_email')));
			}
			$record1 = new TableRecord('tbl_user_profile');
			$record1->setFldValue('user_sample_essay',1);
			if (!$record1->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array($data['file_user_id'])))) {
				$this->error = $record1->getError();
				return false;
			}
		}		
		return true;
	}
	
	public function getUserLanguages($user_id) {
		if(intval($user_id) == 0) {
			$user_id = User::getLoggedUserAttribute('user_id');
		}
		$db = Syspage::getdb();
		$srch = new SearchBase('tbl_users','u');
		$srch->joinTable('tbl_user_languages','LEFT OUTER JOIN','ul.userlang_user_id = u.user_id','ul');
		$srch->joinTable('tbl_languages','LEFT OUTER JOIN','lang.lang_id = ul.userlang_lang_id','lang');
		$srch->addCondition('user_id','=',$user_id);
		$srch->addFld(array('lang.lang_name','lang.lang_id'));
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		return $row;
	}
	
	public function deactivateAccountRequest() {
		$db = &Syspage::getdb();
		$record = new TableRecord('tbl_users');
		$record->setFldValue('user_deactivate_request',1);
		$record->setFldValue('user_deativate_request_date','mysql_func_NOW()',true);
		if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
			$this->error = $record->getError();
			return false;
		}		
		return true;
	}
	
	public function reactivateAccountRequest($user_id){
		$db = &Syspage::getdb();
		$user_id = intval($user_id);
		if($user_id == 0) die('Invalid Request!');
		$record = new TableRecord('tbl_users');
		$record->setFldValue('user_deactivate_request',0);
		$record->setFldValue('user_deativate_request_date','mysql_func_NOW()',true);
		if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array($user_id)))) {
			$this->error = $record->getError();
			return false;
		}	
		
		return true;
	}
	
	static public function hasProfilePhoto(){
		$db = Syspage::getdb();
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addFld('user_photo');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			$this->error = $db->getError();
			return false;
		}
		if($row['user_photo'] == '') {			
			return false;
		}
		return true;
	}
	
	function removeProfilePhoto() {
		if(!$user_photo = $this->hasProfilePhoto()) {
			$this->error = Utilities::getLabel( 'L_No_photo_found' );
			return false;
		}		
		
		$record = new TableRecord('tbl_users');
		$record->setFldValue('user_photo','');
		if(!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
}