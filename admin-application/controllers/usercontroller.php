<?php
class UserController extends Controller{
    
	private $admin;
	private $Users;
	private $user;
	private $ratings;
	private $Task;
	private $order;
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->Users = new Users();
		$this->user = new User();
		$this->ratings = new Ratings();
		$this->orders = new Orders();
		$this->order = new Order();
		$this->Task = new Task();
	}
	
    function default_action() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$this->set('frmUserSearch', $this->getSearchForm());
		
    	$this->_template->render();
    }
    
    public function user_listing($id=-1) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$this->set('frmUserSearch', $this->getSearchForm());
		$this->search();
		$this->_template->render();
	}
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmUserSearch');
		$frm->setAction('user_listing');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in email and name)"');
		
		$frm->addSelectBox('', 'user_type', array('Select User Type','Customer', 'Writer'), '', '','', 'user_type');
		
		$frm->addSelectBox('', 'user_active', array('Inactive', 'Active'), '', '', 'User Status');
		
		$frm->addSelectBox('', 'user_email_verified', array('No', 'Yes'), '', '', 'Is Email Verified');
			
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_users', 'u');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('u.user_email', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition("CONCAT(u.`user_first_name`, ' ', u.`user_last_name`)", 'like', '%' . $post['keyword'] . '%');
			$cnd->attachCondition("u.user_screen_name", 'like', '%' . $post['keyword'] . '%');
		}
		
		if ($post['user_type'] == 1){
			//die('customer');
			$srch->addCondition('user_type', '=', 0);
		} else if($post['user_type'] == 2) {
			//die('writer');
			$srch->addCondition('user_type', '=', 1);
		}
		
		
		if (is_numeric($post['user_active'])) $srch->addCondition('u.user_active', '=', intval($post['user_active']));
		
		if (is_numeric($post['user_email_verified'])) $srch->addCondition('u.user_email_verified', '=', intval($post['user_email_verified']));
		
		$pagesize = 10;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('u.user_regdate','desc');
		$srch->addOrder('u.user_first_name');
		$srch->addOrder('u.user_last_name');
		
		$srch->addMultipleFields(array('u.user_id','u.user_email','u.user_screen_name','u.user_first_name','u.user_last_name','u.user_sex','u.user_country_id','u.user_city','u.user_regdate','u.user_type','u.user_email_verified','u.user_active','u.user_is_featured','u.user_is_approved'));
		//$srch->joinTablesAutomatically();
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		foreach($data as $key=>$arr_data) {
			$data[$key]['arr_rating'] = $this->ratings->getAvgUserRating($arr_data['user_id']);
		}
		
		$this->set('arr_listing',$data);
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
	
	function update_user_status(){
		/* @var $db Database */
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id', '=', $id);
		$srch->addFld(array('user_active','user_deactivate_request'));
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		
		if ($row['user_active'] == 0) {
			if($row['user_deactivate_request'] == 1) {
				$db->query('update tbl_users set user_deactivate_request="0" where user_active="0" and user_id='.$id);
			}			
			$user_active = 1;
		} else {
			$user_active = 0;
		}
		
		$img = $user_active == 1 ? '<img src="' . CONF_WEBROOT_URL . 'images/actives.png" alt="" class="whiteicon active_icon">' : '<img src="' . CONF_WEBROOT_URL . 'images/inactives.png" alt="" class="whiteicon inactive_icon">';
			
		if (!$db->update_from_array('tbl_users', array('user_active'=>$user_active), array('smt'=>'user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());		
		}
		else {
			Message::addMessage('User status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>($img));
			die(convertToJson($arr));
		}
	}
	
	function update_email_verification_status(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		if($id < 1) dieWithError('Invalid Request!');
		/* $srty = new SearchBase('tbl_users');
		$srty->addCondition('user_id', '=', $id);
		$srty->addFld('user_type');
		
		$result = $srty->getResultSet();
		$type = $db->fetch($result);
		 */
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id', '=', $id);
		$srch->addCondition('user_type','=',1);
		$srch->addFld('user_email_verified');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['user_email_verified'] == 0) {
			$user_email_verified = 1;
		} else {
			$user_email_verified = 0;
		}
			
		if (!$db->update_from_array('tbl_users', array('user_email_verified'=>$user_email_verified), array('smt'=>'user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('User email verification status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($user_email_verified == 1)?'Yes':'No'));
			die(convertToJson($arr));
		}
	}
	
	function test_passed() {
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		
		$srch = new SearchBase('tbl_user_profile');
		$srch->addCondition('userprofile_user_id', '=', $id);
		$srch->addFld('user_test_passed');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		/* if ($row['user_test_passed'] == 2) {
			$user_passed = 0;			
		}elseif($row['user_test_passed'] == 0) {
			$user_passed = 1;			
		} else {
			$user_passed = 1;			
		} */
		//die($row['user_test_passed']);
		if($row['user_test_passed'] == 0) {
			$user_passed = '1';			
		} else if($row['user_test_passed'] == 2) {
			$user_passed = '1';			
		} else{
			$user_passed = 0;
		}
		
		if (!$db->update_from_array('tbl_user_profile', array('user_test_passed'=>$user_passed), array('smt'=>'userprofile_user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {			
			Message::addMessage('Test status updated!');			
			$arr = array('status'=>1, 'msg'=>Message::getHtml());
			die(convertToJson($arr));
		}
	}
	
	function mark_featured(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id', '=', $id);
		$srch->addFld('user_is_featured');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['user_is_featured'] == 0) {
			$user_featured = 1;
		} else {
			$user_featured = 0;
		}
		
		if (!$db->update_from_array('tbl_users', array('user_is_featured'=>$user_featured), array('smt'=>'user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Featured status updated!');	
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($user_featured == 1)?'<a class="toggleswitch"></a>':'<a class="toggleswitch actives"></a>'));
			die(convertToJson($arr));
		}
	}
	
	
	function mark_experienced(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		
		$srch = new SearchBase('tbl_user_profile');
		$srch->addCondition('userprofile_user_id', '=', $id);
		$srch->addFld('user_is_experienced');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['user_is_experienced'] == 2) {
			$user_is_experienced = 1;
		} else {
			$user_is_experienced = 2;
		}
		
		if (!$db->update_from_array('tbl_user_profile', array('user_is_experienced'=>$user_is_experienced), array('smt'=>'userprofile_user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Featured status updated!');	
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($user_is_experienced == 1)?'<a class="toggleswitch"></a>':'<a class="toggleswitch actives"></a>'));
			die(convertToJson($arr));
		}
	}	
	
	function mark_user_verified_by_admin(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
			
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_users');
		$srch->addCondition('user_id', '=', $id);
		//$srch->addFld('user_is_approved');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);	
		
		if (!$db->update_from_array('tbl_users', array('user_is_approved'=>1), array('smt'=>'user_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('User is Verified!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>'<span class="toggleswitch"></span>');
			
			$this->Task->sendEmail(array('to'=>$row['user_email'],'user_screen_name'=>$row['user_screen_name'],'temp_num'=>28));
			die(convertToJson($arr));
		}
	}
	
	function preview_customer_profile($id){
	
		global $db;
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$rs=$this->Users->preview_user($id);		
		$rs['user_country_id'] = $this->admin->getCountry($rs['user_country_id']);
		$rs['user_languages'] = $this->user->getUserLanguages($id);
		$criteria=array('task_user_id'=>$id);
		
		$srch=$this->Task->search($criteria);
		
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		
		$orders = $srch->getResultSet();
		$this->set('arr_listing',$db->fetch_all($orders));
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize', $pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$frm = $this->reset_password();
		if(isset($post) && $post['btn_submit'] != null){	
			if(!$frm->validate($post)) {
				$errors = $frm->getValidationErrors();
				Message::addErrorMessage($errors);
			}
			$record = new TableRecord('tbl_users');
			$record->setFldValue('user_password',encryptPassword($post['user_password']));
			
			if(!$record->update(array('smt'=>'user_id = ?', 'vals'=>array($id)))) {
				Message::addErrorMessage($record->getError());
			}else{
				Message::addMessage('Password is changed!');
			}
			
		}
		$this->set('frm', $frm);
		$this->set('orders_completed', $this->order->getUserCompletedOrders($id));
		$this->set('user_rating', $this->ratings->getAvgUserRating($id));
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);	
		$this->set('rs',$rs);
        $this->_template->render();		
	}
	
	function preview_writer_profile($id){
	
		global $db;
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$rs=$this->Users->preview_user($id);
		$rs['user_country_id'] = $this->admin->getCountry($rs['user_country_id']);
		$rs['user_languages'] = $this->user->getUserLanguages($id);
		$criteria=array('task_writer_id'=>$id);
		
		$srch=$this->Task->search($criteria);
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		
		$orders = $srch->getResultSet();
		$this->set('user_rating', $this->ratings->getAvgUserRating($id));
		$this->set('orders_completed', $this->order->getUserCompletedOrders($id));
		$this->set('arr_listing',$db->fetch_all($orders));
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize', $pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$frm = $this->reset_password();
		if(isset($post) && $post['btn_submit'] != null){	
			if(!$frm->validate($post)) {
				$errors = $frm->getValidationErrors();
				Message::addErrorMessage($errors);
			}
			$record = new TableRecord('tbl_users');
			$record->setFldValue('user_password',encryptPassword($post['user_password']));
			
			if(!$record->update(array('smt'=>'user_id = ?', 'vals'=>array($id)))) {
				Message::addErrorMessage($record->getError());
			}else{
				Message::addMessage('Password is changed!');
			}
			
		}
		$this->set('frm', $frm);
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);		
		
		
		//$this->set('orders', $orders);
        $this->set('rs',$rs);
        $this->_template->render();		
	}
	
	function reset_password(){    	
    	
    	$frm = new Form('frmResetPassword');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('class="firstChild"');
		$frm->setJsErrorDisplay('afterfield');
    	
    	$fld = $frm->addPasswordField('New Password', 'user_password');
    	$fld->requirements()->setRequired();
    	$fld->requirements()->setLength(6,20);
    	$fld1 = $frm->addPasswordField('Confirm New Password', 'user_password1');
    	$fld1->requirements()->setRequired();
    	$fld1->requirements()->setCompareWith('user_password');
    	 
    	$frm->addSubmitButton('', 'btn_submit', 'Submit');
    	
    	//$frm->setAction(generateUrl('user', 'password_reset'));
    	
    	return $frm;
    }
	
	
		
	
	
	function photo($id, $w=0, $h=0){
	    global $db;
		
		$id = intval($id);
		if ($id < 1) die('Invalid request.');
	    
	    $srch = new SearchBase('tbl_users');
		
	    $srch->addCondition('user_id', '=', $id);
		
		$srch->addFld('user_photo');
	    
	    $rs = $srch->getResultSet();
		
	    $row = $db->fetch($rs);
		
		if (!$row || $row['user_photo'] == '') {
			if(!User::isWriter($id)) {
				$user_photo =  CONF_INSTALLATION_PATH . 'public/images/Default-Images_cli.jpg';
			}else {
				$user_photo =  CONF_INSTALLATION_PATH . 'public/images/Default-Images_wr.jpg';
			}
		}
		else {
			$user_photo = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['user_photo'];
		}
		
		getImage($user_photo,100,100);
    }
	
	public function transaction_details($user_id) {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Users->getUserTransactionHistory($post['page'],$user_id);
		//echo '<pre>'.print_r($data,true).'</pre>';exit;
		/* $this->set('pages',$data['pages']);
		$this->set('page',$data['page']);
		$this->set('pagesize',$data['pagesize']);
		$this->set('total_records',$data['total_records']); */
		$this->set('pages', $data['pages']);
		$this->set('page', $data['page']);
		$this->set('pagesize', $data['pagesize']);
		$this->set('start_record', ($data['page']-1)*$data['pagesize'] + 1);
		$end_record = $data['page'] * $data['pagesize'];
		$total_records = $data['total_records'];
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);
		
		$this->set('arr_transactions', $data['transactions']);
		$this->set('user_id', $user_id);
		
    	$this->_template->render();
	}
	
	public function user_impersonation($user_id) {
		if (!Admin::isLogged()) {
			redirectUser(generateUrl('admin', 'loginform'));
			return false;
		}
		
		$rs = $this->Users->preview_user($user_id);
		if(!$validate = $this->user->validateLogin($rs['user_email'], $rs['user_password'],true, true)) {
			die('Record Not Found');
			return false;
		}
				
		redirectUser(generateUrl('user','dashboard',array(),CONF_WEBROOT_URL));
		exit;
		
	}
	
	public function show_image($file_name='',$w=0,$h=0,$crop) {
		$photo = CONF_INSTALLATION_PATH . 'user-uploads/' .$file_name;
		getImage($photo,$w,$h,'',$crop);
	}
	public function download_certificate($user_id) {
		$db = &Syspage::getdb();
		
		if (!Admin::isLogged()) die('Invalid Request');
		$srch = new SearchBase('tbl_user_education');
		$srch->addCondition('usered_user_id','=',$user_id);
		$srch->addFld('usered_file');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		$file_real_path = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['usered_file'];
        if (!is_file($file_real_path)) die('Invalid request');
        
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

        header("Content-Disposition: attachment; filename=\"" . $row['file_download_name']."\";" );

        header("Content-Transfer-Encoding: binary");

        header("Content-Length: " . filesize($file_real_path));
		
        readfile($file_real_path);
		
        exit();
	}
	
	public function download_file($fid) {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) die('Invalid Request');
		
		$fid = intval($fid);
		if ($fid < 1) die('Invalid request');
		
		$rs = $db->query("SELECT * FROM tbl_files WHERE file_id = " . $db->quoteVariable($fid));
        if (!$row = $db->fetch($rs)) die('Invalid request');
		
		$order_details = $this->Task->getOrderDetails($row['file_task_id']);
		
		/* if ($order_details !== false && $order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $order_details['task_user_id'] != User::getLoggedUserAttribute('user_id')) {
			die('Invalid request');
		} */
		
		//$task_details = $this->Task->getTaskDetailsById($row['file_task_id']);
        
        $file_real_path = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['file_name'];
        if (!is_file($file_real_path)) die('Invalid request');
        
        $fname = $row['file_download_name'];
		
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

        header("Content-Disposition: attachment; filename=\"" . $row['file_download_name']."\";" );

        header("Content-Transfer-Encoding: binary");

        header("Content-Length: " . filesize($file_real_path));
		
        readfile($file_real_path);
		
        exit();
    }
	
	public function download_essay_file($fid) {
		$db = &Syspage::getdb();
		if (!Admin::isLogged()) die('Invalid Request');
		//die('1');
		
		$fid = intval($fid);
		if ($fid < 1) die('Invalid request');
		
		$rs = $db->query("SELECT * FROM tbl_sample_essay WHERE file_user_id = " . $db->quoteVariable($fid));
        if (!$row = $db->fetch($rs)) die('Invalid request');
		
		//$order_details = $this->Task->getOrderDetails($row['file_user_id']);
		
		/* if ($order_details !== false && $order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $order_details['task_user_id'] != User::getLoggedUserAttribute('user_id')) {
			die('Invalid request');
		} */
		
		//$task_details = $this->Task->getTaskDetailsById($row['file_task_id']);
        
        $file_real_path = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['file_name'];
        if (!is_file($file_real_path)) die('Invalid request');
        
        $fname = $row['file_download_name'];
		
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

        header("Content-Disposition: attachment; filename=\"" . $row['file_download_name']."\";" );

        header("Content-Transfer-Encoding: binary");

        header("Content-Length: " . filesize($file_real_path));
		
        readfile($file_real_path);
		
        exit();
    }
	
/* 	public function customer_order_listing($user_id) {
		if (!Admin::isLogged()) {
			redirectUser(generateUrl('admin', 'loginform'));
			return false;
		}
	} */
	
	public function decline_essay_sample() {
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$user_id = intval($post['user_id']);
		if ($user_id < 0) {
			Message::addErrorMessage('Invalid request.');
			die(convertToJson(array('status'=>0, 'msg'=>Message::getHtml())));
		}
		
		$user_data = $this->user->getUserDetailsById($user_id);		
		if ($user_data === false) {
			Message::addErrorMessage('Invalid request.');
			die(convertToJson(array('status'=>0, 'msg'=>Message::getHtml())));
		}
		
		$record = new TableRecord('tbl_user_profile');
		
		$record->setFldValue('user_sample_essay', 2);
		
		if (!$record->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array($user_id), 'execute_mysql_functions'=>false))) {
			Message::addErrorMessage($record->getError());
			die(convertToJson(array('status'=>0, 'msg'=>Message::getHtml())));
		}
		
		/** notify writer **/
		$param = array('to'=>$user_data['user_email'],'temp_num'=>59,'user_screen_name'=>$user_data['user_screen_name']);
    	
    	$this->Task->sendEmail($param);
		Message::addMessage('Sample essay successfully declined!');
		die(convertToJson(array('status'=>1, 'msg'=>Message::getHtml())));
	}
}