<?php
class TaskController extends LegitController {
	private $common;
	private $bid;
	private $wallet;
	private $transactionsController;
	private $ratings;
	private $reviews;
	private $messages;
	private $Order;
	private $userController;
	
	public function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->common	= new Common();
		$this->bid		= new Bid();
		$this->wallet	= new Wallet();
		$this->ratings	= new Ratings();
		$this->reviews	= new Reviews();
		$this->messages	= new Messages();
		$this->Order	= new Order();
		
		$this->transactionsController = new TransactionsController('transactions', 'transactions', 'getLoadWalletForm');
		$this->userController = new UserController('user', 'user', 'getLoginForm');
	}
	
	/* Function to list customer's orders */
	public function my_orders($filter='') {
		
		$post = Syspage::getPostedVar();
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die('Invalid request'); */
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!in_array($filter, array('mine', 'ongoing', 'completed', 'cancelled', 'invites'))) $filter = 'mine';
		
		$first_order = Common::is_first_order_done();
			if($first_order == false) {
				$data = $this->Task->getCustomerOrders('mine',1);
				foreach($data['orders'] as $value) {
					Message::addMessage('To access your profile area, we request you to please complete the order process <a href='.generateUrl('task','add',array($value['task_id'])).'>click here</a>');
				}
			}
		$this->set('filter', $filter);
		$this->set('arr_orders', $this->Task->getCustomerOrders('mine',1));
		$this->set('arr_ongoing_orders', $this->Task->getCustomerOrders('ongoing',1));
		$this->set('arr_completed_orders', $this->Task->getCustomerOrders('completed',1));
		$this->set('arr_cancelled_orders', $this->Task->getCustomerOrders('cancelled',1));
		$this->set('arr_invites', $this->Task->getPrivateOrders());
		
    	$this->_template->render();
    }
	
	/* function to get private orders */
	public function invites() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_orders = $this->Task->getPrivateOrders($page);
		
		$arr_orders['str_paging'] = generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],"invites(xxpagexx);");
		
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render(false, false);
	}
	
	public function bids() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$filter = $post['filter'];
		if (!in_array($filter, array('mine', 'ongoing', 'completed', 'cancelled', 'invites'))) $filter = 'mine';
		
		$arr_orders = (array) $this->Task->getCustomerOrders($filter,$page);
		foreach($arr_orders['orders'] as $key=>$arr)
		{
			if(!User::isWriter()) {
				$first_order = Common::is_first_order_done();
				if($first_order == false) {
					//echo '<td><a href="'.generateUrl("task","add",array($arr["task_id"])).'">#'. $arr['task_ref_id'].'</td>';
					$action = 1;
					//$action = '<td class="dataTable"><a href="'.generateUrl("task","add",array($arr["task_id"])).'" class="theme-btn">View Order</a></td>';
				}else {
					if (!User::isUserActive()) {					
					 }else {
						 $action = 1;
						//$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_bids', array($arr['task_id'])).'" class="theme-btn">View Order</a></td>';
					}
				}
			}
			$arr_orders['orders'][$key]['action'] = $action;
		}
		
		$arr_orders['str_paging'] = generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],"bids('" . $filter . "',xxpagexx);");
		
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render(false, false);
	}
	
	/* Function to list bids for a particular order */
	public function order_bids($task_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die('Invalid request'); */
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$task_id = intval($task_id);
		if ($task_id < 1) die('Invalid request');
		//echo $task_id;
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		//echo '<pre>'. print_r($arr_task,true);exit;
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		$arr_bids = $this->bid->getOrderBids($task_id);
		
		if (count($arr_bids) > 0) {
			foreach($arr_bids as $key=>$ele) {				
				$arr_bids[$key]['bidder_rating']	= $this->ratings->getAvgUserRating($ele['bid_user_id']);
				$arr_bids[$key]['orders_completed']	= $this->Order->getUserCompletedOrders($ele['bid_user_id']);
			}
		}
		
		$files = $this->Task->getOrderFiles($task_id,1);
		
		$this->set('files', $files);
		$this->set('order', $arr_task);
		$this->set('arr_bids', $arr_bids);
		
    	$this->_template->render();
	}
	
	public function setup() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged() || User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$task_id = intval($post['task_id']);
		$post['task_paptype_id'] = intval($post['task_paptype_id']);
		
		$paptype = Common::getPaperTypes();
		$paptype = array_keys($paptype);
		
		if(isset($post['btn_submit']) && !in_array($post['task_paptype_id'],$paptype) ) {
			Message::addErrorMessage('Invalid paper type. Please select correct paper type.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
		}
		$discipline = Common::getDisciplines();
		$discipline = array_keys($discipline);
		
		if(isset($post['btn_submit']) && !in_array($post['task_discipline_id'],$discipline)) {
			Message::addErrorMessage('Invalid discipline. Please select correct discipline.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add($post['task_id']);exit;
            return;
		}
		
		$services = Common::getServices();
		$services = array_keys($services);
		
		if(isset($post['btn_submit']) && !in_array($post['task_service_type'],$services)) {
			Message::addErrorMessage('Invalid service type. Please select correct service type.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
		}
		
		$citation_style = Common::getCitationStyles();
		$citation_style = array_keys($citation_style);
		
		if(isset($post['btn_submit']) && !in_array($post['task_citstyle_id'],$citation_style)) {
			Message::addErrorMessage('Invalid citation style. Please select correct citation style.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
		}
		
		/* if(isset($post['btn_submit']) && $post['task_due_date']=='') {
			Message::addErrorMessage('Deadline date should be of tomorrow or after that.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
		} */
		
		$frm = $this->getTaskForm();
		
		if ($task_id > 0) {
			$fld = $frm->getField('task_due_date');
			$fld->requirements()->setRequired(false);
		}
		else{
			if(isset($post['btn_submit']) && ($post['task_due_date']=='' || $post['task_due_date']=='Choose Deadline') ) {
			Message::addErrorMessage('Deadline date should be of tomorrow or after that.');
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
		}
		}
		if(isset($data['task_topic']) && $data['task_topic']!='' && $task_id > 0) {
			$fld_set = $frm->setOnSubmit('return confirmation();');
		}
         
		
		if($tid < 1){				
			$cust = $frm->getField('user_first_name');
			$cust->requirements()->setRequired(false);
			$user = $frm->getField('user_last_name');
			$user->requirements()->setRequired(false);
			$country = $frm->getField('user_country_id');
			$country->requirements()->setRequired(false);
		}
		
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
			foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add();
            return;
        }
		
		$invitee_id = intval($post['invitee_ids']);
		
		$data = $post;
		
		$data['task_user_id']	= User::getLoggedUserAttribute('user_id');
		if($task_id > 0){
			
			$srch = new SearchBase('tbl_tasks');
			$srch->addCondition('task_id','=',$task_id);
			$srch->addFld('task_due_date');
			$srch->addFld('task_status');
			$rs = $srch->getResultSet();
			
			$row = $db->fetch($rs);
			
			$data['task_due_date'] = date('Y-m-d H:i:s',strtotime($row['task_due_date']));						
			$data['task_status']= $row['task_status'];
			
			$srch1 = new SearchBase('tbl_users');
			$srch1->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
			$srch1->addFld('user_ref_id');
			$rs1 = $srch1->getResultSet();
			$row1= $db->fetch($rs1);
			
			
			$data['user_ref_id']= $row1['user_ref_id'];
		}
		
		if ($task_id == 0) {
			
			$data['task_type']	= $invitee_id > 0 ? 1 : 0;
			$data['task_status']	= 0;//changed on 15/may/2014 by Aksheer
			/* $task_due_date = array();
			$task_due_date = explode('_',$post['task_due_date']);
		
			switch ($task_due_date[1]) {
				case 'h' :
					$post['task_due_date'] = strtotime('+'.$task_due_date[0].' hours',time());
					$data['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
					break;
				case 'n' :
					$data['task_due_date'] = date('Y-m-d H:i:s'.$task_due_date[0].':00',strtotime('tomorrow'));
					break;
				case 'd' :
					$post['task_due_date'] = strtotime('+'.$task_due_date[0].' days',time());
					$data['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
					break;
				default :
					$post['task_due_date'] = strtotime('+7 days',time());
					$data['task_due_date'] = date('Y-m-d H:i:s',$post['task_due_date']);
				break;
			} */ 			
		}
		
		/** upload additional material **/
		$arr_uploaded_files = $this->upload_files($_FILES['task_files']);
		
		if ($arr_uploaded_files === false) {
			$this->_template = new Template('task', 'add');
			$this->setLegitControllerCommonData('', 'add');
			$this->add($task_id);
			return;
		}
		//echo "<pre>".print_r($data,true)."</pre>";exit;
		if (!$this->Task->addTask($data)) {
			Message::addErrorMessage($this->Task->getError());
			redirectUser(generateUrl('task', 'my_orders'));
		}
	
		//die($invitee_id);
		if ($invitee_id > 0) {
			if ($this->Task->addTaskInvitees(array('inv_task_id'=>$this->Task->getTaskId(), 'inv_writer_id'=>$invitee_id, 'inv_status'=>0))) {
				$objUser = new User($invitee_id);
				
				$param = array('to'=>$objUser->getAttribute('user_email'),'temp_num'=>7,'user_screen_name'=>$objUser->getAttribute('user_screen_name'),'user_first_name'=>User::getLoggedUserAttribute('user_screen_name'),'order_page_link'=>CONF_SITE_URL.generateUrl('bid', 'add', array($this->Task->getTaskId())));
				$this->Task->sendEmail($param);
			}
		}
		
		//add files into db
		if (!empty($arr_uploaded_files)) {
			foreach ($arr_uploaded_files as $ele) {
				if (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name'])) {
					if (!$this->Task->addFile(array('file_task_id'=>$this->Task->getTaskId(), 'file_name'=>$ele['file_name'], 'file_download_name'=>$ele['file_download_name'], 'file_type'=>$ele['file_type'],'file_size'=>$ele['file_size'], 'file_class'=>1))) {
						unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name']);
						Message::addErrorMessage($this->Task->getError());
					}
				}
			}
		}
		
		if ($task_id > 0) {
			Message::addMessage('Order successfully updated.');
		}
		else {
			Message::addMessage('Your order has been successfully submitted for approval.');
		}
		
		redirectUser(generateUrl('task', 'my_orders'));
	}
	
	public function update_project_file() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged() || !User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die('Invalid request');
		
		$order_details = $this->Task->getOrderDetails($task_id);		
		if ($order_details === false || $order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		//echo "<pre>".print_r($_FILES['task_files'],true)."</pre>";exit;
		$arr_uploaded_files = $this->upload_files($_FILES['task_files'],false);
		
		if ($arr_uploaded_files === false) {
			redirectUser(generateUrl('task', 'order_process', array($task_id)));
		}
		
		if (!empty($arr_uploaded_files)) {
			foreach ($arr_uploaded_files as $ele) {
				if (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name'])) {
					if (!$this->Task->addFile(array('file_task_id'=>$task_id, 'file_name'=>$ele['file_name'], 'file_download_name'=>$ele['file_download_name'], 'file_type'=>$ele['file_type'],'file_size'=>$ele['file_size'], 'file_class'=>2, 'cus_req_to_upload_again'=>$order_details['cus_req_to_upload_again']))) {
						unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name']);
						Message::addErrorMessage($this->Task->getError());
					}
				}
			}
			
			Message::addMessage('File(s) uploaded successfully.');
		}else {
			Message::addErrorMessage('No file(s) Selected!');
		}
		
		redirectUser(generateUrl('task', 'order_process', array($task_id)));
	}
	
	private function upload_files($arr_files,$can_upload_images=true) {
		$error = 0;
		$total_file_size = 0;
		$num_files_uploaded = count($arr_files['name']);
		getMIMETypeInfo();			
		
		
		$arr_uploaded_files = array();
		
		if ($num_files_uploaded > 0) {
			$arr_valid_text_file_types	= array(
												'text/plain',
												'application/msword',
												'application/pdf',
												'application/vnd.ms-excel',
												'application/msword',
												'application/zip',
								'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			$arr_valid_image_file_types	= array('image/jpeg', 'image/png');
			
			$arr_valid_file_types = $can_upload_images === false ? $arr_valid_text_file_types : array_merge($arr_valid_text_file_types,$arr_valid_image_file_types);
			
			/** throw error if total file size exceeds 30MB. **/
			$total_file_size = array_sum($arr_files['size']);
			
			if ($total_file_size > 32086425) {
				Message::addErrorMessage('Total file size must not exceed 30MB.');
				return false;
			}
			
			for ($i=0; $i<$num_files_uploaded; $i++) {
				if (is_uploaded_file($arr_files['tmp_name'][$i])) {
					$path_parts = pathinfo($arr_files['name'][$i]);
					
					$base_name	= $path_parts['basename'];
					
					if ($arr_files['error'][$i] != 0) {
						Message::addErrorMessage('Error uploading ' . $base_name);
						$error = 1;
						break;
					}
					$mime = getMIMETypeInfo($arr_files['tmp_name'][$i]);
					//echo "<pre>".print_r($mime,true)."</pre>";exit;
					if (!in_array($mime, $arr_valid_file_types)) {
						Message::addErrorMessage('Could not upload ' . $base_name . '. Invalid file format.');
						$error = 1;
						break;
					}
					
					if ($arr_files['size'][$i] > 10695475) {				//restrict file size to 10MB
						Message::addErrorMessage('Could not upload ' . $base_name . '. File size too big, max allowed size 10MB.');
						$error = 1;
						break;
					}
					
					if (in_array($arr_files['type'][$i], $arr_valid_image_file_types)) {
						$file_type = 2;
						
						if (!saveImage($arr_files['tmp_name'][$i], $arr_files['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							$error = 1;
							break;
						}
					}
					else {
						$file_type = 1;
						
						if (!saveFile($arr_files['tmp_name'][$i], $arr_files['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							$error = 1;
							break;
						}
					}
					
					$arr_uploaded_files[$i]['file_name']			= $saved_file_name;
					$arr_uploaded_files[$i]['file_download_name']	= $base_name;
					$arr_uploaded_files[$i]['file_type']			= $file_type;
					$arr_uploaded_files[$i]['file_size']			= $arr_files['size'][$i];
				}
			}
		}
		
		if ($error == 1) return false;
		
		return $arr_uploaded_files;
	}
	
	public function download_file($fid) {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) die('Invalid Request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$fid = intval($fid);
		if ($fid < 1) die('Invalid request');
		
		$rs = $db->query("SELECT * FROM tbl_files WHERE file_id = " . $db->quoteVariable($fid));
        if (!$row = $db->fetch($rs)) die('Invalid request');
		
		if (!user::isWriter()){
			$rows = $this->wallet->getReservedAmount($row['file_task_id']);
			
			if($rows['res_amount']!=0){
				$rs = $db->query('Select file_download_status from tbl_files where file_id='.$fid);
				$status = $db->fetch($rs);
				
				if($status['file_download_status'] != 1) {
					$records = new TableRecord('tbl_files');
					$records->setFldValue('file_download_status',1);
					$records->setFldValue('file_download_on','mysql_func_NOW()',true);
					
					if(!$records->update(array('smt'=>'file_task_id = ? AND file_id = ?', 'vals'=>array($row['file_task_id'],$fid)))) {
						$this->error = $records->getError();
						die(convertToJson(array('status'=>'0','msg'=>$error)));
						return false;
					}		
				}
			}
		}
				
		$order_details = $this->Task->getOrderDetails($row['file_task_id']);
		
		if ($order_details !== false && $order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $order_details['task_user_id'] != User::getLoggedUserAttribute('user_id')) {
			die('Invalid request');
		}
		
		//$task_details = $this->Task->getTaskDetailsById($row['file_task_id']);
        
        $file_real_path = CONF_INSTALLATION_PATH . 'user-uploads/' . $row['file_name'];
        if (!is_file($file_real_path)) die('Invalid request');
        
        $fname = $row['file_download_name'];
		
        $file_extension = strtolower(strrchr($fname, '.'));
		
        switch ($file_extension) {
			case '.txt':  $ctype='application/octet-stream'; break;
			
            case '.pdf': $ctype='application/pdf'; break;

            case '.doc':
				$ctype = 'application/msword';
				break;

            case '.docx':

                $ctype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

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
	
	
	public function download_milestone(){
		$post = Syspage::getPostedVar();
		//die(convertToJson($post));
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!user::isWriter()){
			$rs = $db->query('Select file_download_status from tbl_files where file_id='.$post['file_id']);
			$status = $db->fetch($rs);
			
			if($status['file_download_status'] != 1) {
				$records = new TableRecord('tbl_files');
				$records->setFldValue('file_download_status',1);
				$records->setFldValue('file_download_on','mysql_func_NOW()',true);
				
				if(!$records->update(array('smt'=>'file_task_id = ? and file_id = ?', 'vals'=>array($post['task_id'],$post['file_id'])))) {
					$this->error = $records->getError();
					die(convertToJson(array('status'=>'0','msg'=>$error)));
					return false;
				}		
			}
			$row = $this->wallet->getReservedAmount($post['task_id']);
			if($row['res_amount']==0){
				$transfer_status = true;
			}else{
				$transfer_status = false;
			}
			die(convertToJson(array('status'=>'1','data'=>$transfer_status)));
		
			$this->_template->render(false,false);
		}		
		return true;
	}
	
	/* Function for bid preview for customer by bid_id */
	public function bid_preview($bid_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$bid_id = intval($bid_id);		
		if ($bid_id < 1) die('Invalid request');
		
		$arr_bid = $this->bid->getBidDetailsById($bid_id);
		$arr_bid['bid_preview'] = html_entity_decode($arr_bid['bid_preview']);
		
		if ($arr_bid === false) die('Invalid request');		
		if ($arr_bid['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		$data = $this->Task->getTaskDetailsById($arr_bid['bid_task_id']);
		$files = $this->Task->getOrderFiles($arr_bid['bid_task_id'],1);
		/* echo "<pre>".print_r($arr_bid,true)."</pre>";
		echo $bid_id;exit; */
		/* if($data['task_status']== 2 ) {
			Message::addMessage('This order is already assigned.');
			if ($arr_bid['task_user_id'] == User::getLoggedUserAttribute('user_id')) {
				redirectUser('task','order_process',array($arr_bid['bid_task_id']));
			}
			redirectUser(generateUrl('task','my_orders'));
		} */
		
		if($data['task_status']== 2 || $data['task_status']== 3) {
			Message::addMessage('This order is already assigned.');
			if ($arr_bid['task_user_id'] == User::getLoggedUserAttribute('user_id')) {
				redirectUser(generateUrl('task','order_process',array($arr_bid['bid_task_id'])));
			}
			redirectUser(generateUrl('task','my_orders'));
		}
		
		$writer_activity = $this->Order->getWriterActivityStats($arr_bid['bid_user_id']);
		$writer_rating = $this->ratings->getAvgUserRating($arr_bid['bid_user_id']);
		
		$customer_activity_stats = $this->Order->getCustomerActivityStats($data['task_user_id']);		
		
		$this->set('writer_completed_orders', $writer_activity['orders_completed']);
		$this->set('writer_rating', $writer_rating);
		$this->set('files', $files);		
		$this->set('order', $data);
		$this->set('arr_bid', $arr_bid);
		$this->set('arr_messages', $this->messages->getConversationHistory($bid_id));
		$this->set('customer_rating', $this->ratings->getAvgUserRating($data['task_user_id']));		
		$this->set('customer_orders_completed', $customer_activity_stats['orders_completed']);		
		
		$this->_template->render();
	}
	
	function order_process($task_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter() && !User::writerCanView()) redirectUser(generateUrl('test'));
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		
		$task_id = intval($task_id);
		if($task_id < 1) die('invalid request.');
		
		$total_amount_paid = 0;
		
		$data = $this->Task->getOrderDetails($task_id);
		
		if ($data === false || ($data['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $data['task_user_id'] != User::getLoggedUserAttribute('user_id'))) {
			die('Invalid Request.');
		}
		
		$arr_milestones = $this->Task->getMilestones($task_id);
		
		$ongoing_milestone = end($arr_milestones);
		
		if ($ongoing_milestone['mile_status'] == 1 && $ongoing_milestone['mile_page'] < $data['task_pages']) {
			$tier_milestone = array('mile_page'=>(intval($ongoing_milestone['mile_page']) + 1), 'mile_words'=>0, 'mile_amount'=>0.00, 'mile_content'=>'', 'mile_status'=>0);
			array_push($arr_milestones, $tier_milestone);			
			$ongoing_milestone = $tier_milestone;
		}
		
		if (!empty($ongoing_milestone)) $ongoing_milestone['mile_content'] = html_entity_decode($ongoing_milestone['mile_content']);
		
		$frm = $this->getMilestoneForm();
		
		$fld_task_id = $frm->getField('mile_task_id');
		$fld_task_id->value = $task_id;
		
		$fld_task_id = $frm->getField('mile_page');
		$fld_task_id->value = empty($ongoing_milestone) ? 1 : $ongoing_milestone['mile_page'];
		
		/* $fld_page = $frm->getField('page_num');
		$fld_page->value = empty($ongoing_milestone) ? 1 : $ongoing_milestone['mile_page']; */
		
		if (count($arr_milestones) > 0) {
			$frm->fill($ongoing_milestone);
			
			foreach ($arr_milestones as $key=>$ele) {
				$total_amount_paid += getAmountPayableToWriter($ele['mile_amount'],$data['bid_price'],$data['task_pages']);
				
				if (User::isWriter()) {
					$arr_milestones[$key]['mile_amount'] = getAmountPayableToWriter($ele['mile_amount'],$data['bid_price'],$data['task_pages']);
				}
				else {
					$arr_milestones[$key]['mile_amount'] = $ele['mile_amount'];
				}
			}
		}
		
		$files = $this->Task->getOrderFiles($task_id,1);
		
		if ($data['task_status'] == 3 || $data['task_status'] == 4 || ($data['task_status'] == 2 && $data['task_pages'] == $ongoing_milestone['mile_page'] && $ongoing_milestone['mile_status'] == 1)) {
			$milestones_completed = 1;
		}
		else {
			$milestones_completed = 0;
		}
		
		$customer_activity_stats	= $this->Order->getCustomerActivityStats($data['task_user_id']);
		$writer_activity_stats		= $this->Order->getWriterActivityStats($data['bid_user_id']);
		
		$_SESSION['token_rev'] = encryptPassword(getRandomPassword(5));
		$frm_review = $this->getReviewForm($_SESSION['token_rev']);
		
		$fld = $frm_review->getField('task_id');
		$fld->value = $task_id;
		
		if (User::isWriter()) $this->set('frm', $frm);
		$fr =$this->Task->getOrderFiles($task_id,2);
		//echo "<pre>".print_r($fr,true)."</pre>";exit;
		$this->set('file_uploaded',$fr);
		$this->set('files', $files);
		$this->set('order', $data);		
		$this->set('arr_milestones', $arr_milestones);
		$this->set('total_amount_paid', $total_amount_paid);
		$this->set('ongoing_milestone', $ongoing_milestone);
		$this->set('milestones_completed', $milestones_completed);		
		$this->set('arr_rating', $this->ratings->getTaskRating($task_id));
		$this->set('frm_review', $frm_review);
		$this->set('arr_posted_review', $this->reviews->getTaskReview($task_id));
		$this->set('arr_messages', $this->messages->getConversationHistory($data['bid_id']));
		$this->set('customer_rating', $this->ratings->getAvgUserRating($data['task_user_id']));
		$this->set('writer_rating', $this->ratings->getAvgUserRating($data['bid_user_id']));
		$this->set('customer_orders_completed', $customer_activity_stats['orders_completed']);
		$this->set('writer_orders_completed', $writer_activity_stats['orders_completed']);
		
		$this->_template->render();
	}
	
	public function order_history($task_id) {
		$task_id = intval($task_id);
		if ($task_id < 1) die('Invalid request');
		
		$order_details = $this->Task->getOrderDetails($task_id);
		
		if ($order_details === false || ($order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $order_details['task_user_id'] != User::getLoggedUserAttribute('user_id'))) {
			die('Invalid Request');
		}
		
		$arr_milestones = $this->Task->getMilestones($task_id);
		
		foreach ($arr_milestones as $key=>$ele) {
			$arr_milestones[$key]['mile_content'] = html_entity_decode($ele['mile_content']);
		}
		
		$this->set('order_details', $order_details);
		$this->set('arr_milestones', $arr_milestones);
		
		$this->_template->render();
	}
	
	public function get_order_files() {
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		
		$order_details = $this->Task->getOrderDetails($task_id);
		
		if ($order_details === false || ($order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id') && $order_details['task_user_id'] != User::getLoggedUserAttribute('user_id'))) {
			die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		}
		
		$files = $this->Task->getOrderFiles($task_id,2);
		
		die(convertToJson(array('status'=>1, 'msg'=>'', 'data'=>array('files'=>$files))));
	}
	
	public function add_milestone() {
		$post = Syspage::getPostedVar();
		//die(convertToJson(array('status'=>0, 'msg'=>$post)));
		/* Validate posted data */
		if (!User::isWriter()) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		
		$task_id	= intval($post['mile_task_id']);
		//$mile_page	= intval($post['mile_page']);
		
		if ($task_id < 1 ) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		/* if ($task_id < 1 || $mile_page < 1) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request'))); */
		
		$order_details = $this->Task->getOrderDetails($task_id);
		
		if ($order_details === false || $order_details['task_status'] != 2 || $order_details['bid_user_id'] != User::getLoggedUserAttribute('user_id')) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		/* if ($mile_page > $order_details['task_pages']) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request'))); */
		
		$arr_milestones = $this->Task->getMilestones($task_id);
		
		/* if (!empty($arr_milestones)) {
			$ongoing_milestone = end($arr_milestones);
			$cur_page = $ongoing_milestone['mile_status'] == 1 ? ($ongoing_milestone['mile_page'] + 1) : $ongoing_milestone['mile_page'];
		}
		else {
			$cur_page = 1;
		}
		
		if ($mile_page != $cur_page) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request'))); */
		
		$mile_content = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '<$1$2>', $post['mile_content']);
		
		if (strlen(strip_tags($post['mile_content'])) == 0) die(convertToJson(array('status'=>0, 'msg'=>'No words written.')));
		
		
		/* --------- */
		
		/* $word_count = 0;
		
		//Calculate the number of words in the string
		$content = str_replace("\n", ' ', $post['mile_content']);
		$content = str_replace("<br>", ' ', $post['mile_content']);
		$content = str_replace("<br/>", ' ', $post['mile_content']);
		$content = str_replace("<br />", ' ', $post['mile_content']);
		
		$content = strip_tags($content);
		
		$arr_txt = explode(' ', $content);
		
		foreach($arr_txt as $key=>$val) {
			if (preg_match('/\p{L}/', $val)) $word_count++;
		} */
		
		$post['mile_words']		= getWordCount($mile_content);
		$post['mile_content']	= encodeHtmlCharacters($post['mile_content']);
		/* ------ */
		
		if (!$this->Task->addMilestone($post)) die(convertToJson(array('status'=>0, 'msg'=>$this->Task->getError())));
		
		die(convertToJson(array('status'=>1, 'msg'=>'Content saved.')));
	}
	
	private function getMilestoneForm() {
		//global $mbs_tinymce_settings;
		
		$frm = new Form('frmMilestone','frmMilestone');
		
		$frm->setExtra('class="siteForm"');
		$frm->setOnSubmit('setMilestone(); return false;');
		$frm->setTableProperties('width="60%" cellspacing="0" cellpadding="0" border="0" class="formTable editor_tbl"');
		$frm->setLeftColumnProperties('width="27%"');
		$frm->setJsErrorDisplay('afterfield');
		
		//$frm->addHiddenField('', 'mile_id', '', 'mile_id');
		$frm->addHiddenField('', 'mile_task_id', '', 'mile_task_id');
		//$frm->addHiddenField('', 'mile_words', '', 'mile_words');
		$frm->addHiddenField('', 'mile_page', '', 'mile_page');
		
		//$frm->addRequiredField('Content for page number', 'mile_page', '', 'mile_page', 'style="width:100px;"');
		//$frm->addHTML('Preview of complete content', 'page_num', '', true);
		$frm->addHTML('To send Customer a preview of text, type or paste the text here then click save.', 'page_num', '', true);
		//$mbs_tinymce_settings['plugins'] = 'wordcount';
		
		$fld_prev = $frm->addHtmlEditor('', 'mile_content');
		$fld_prev->merge_caption = true;
		
		$fld_btn = $frm->addSubmitButton('', 'btn_submit', 'Save', 'btn_submit');
		$fld_btn->merge_caption = true;
		
		return $frm;
	}
	
	/* Function for add and edit task */
	public function add($tid,$uid) {
		$post = Syspage::getPostedVar();		
		global $arr_page_js;
		global $arr_page_css;
		
		$arr_page_css[]	= 'css/jquery-ui.css';
		$arr_page_js[]	= 'js/jquery-ui.js';
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if(!isset($tid)) {
			$first_order = Common::is_first_order_done();
			if($first_order == false) {
				redirectUser(generateUrl('task','my_orders'));
				return;
			}
		}
		
		if(User::isUserReqDeactive()) { 
			Message::addErrorMessage('You can not place a new Order. You have requested to delete your account.');
			redirectUser(generateUrl('user','dashboard'));
		}
		
		$tid = intval($tid);
		$uid = intval($uid);
		
        $frm = $this->getTaskForm();
		$frm->addHiddenField('','task_ref_id',$arr_task['task_ref_id']);
		$data = $this->Task->getTaskDetailsById($tid);
		$arr_task = array();
		if ($tid > 0) {	
			$fld_html = $frm->getfield('request');
			$frm->removeField($fld_html);
			
			$arr_task = $this->Task->getTaskDetailsById($tid);
			
			if ($arr_task === false || in_array($arr_task['task_status'],array(2,3,4))) die('Invalid request');
			
			if ($arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
			$task_service_type = $this->common->get_service_type_id($arr_task['task_service_type']);
			$arr_task['task_service_type'] = $task_service_type['service_id'];
		}
		
		if((isset($data['task_topic']) && $data['task_topic']!= '') && $tid > 0) {
			
			$fld_invitees = $frm->getField('invitees');
			//$frm->removeField($fld_invitees);
			
			/* $fld_invitee_ids = $frm->getField('invitee_ids');
			$frm->removeField($fld_invitee_ids); */
			
			$fld_term_cond = $frm->getField('term_cond');
			$fld_term_cond->extra = 'checked="checked"';
			
			$fld_task_due_date = $frm->getField('task_due_date');
			$frm->removeField($fld_task_due_date);					
			if($data['task_status'] != 0) $frm->setOnSubmit('return confirmation();');			
		}
		
		if(intval($data['inv_writer_id']) > 0 || $uid > 0) {
			
			$fld_invitee_ids = $frm->getField('invitees');
			$frm->removeField($fld_invitee_ids);
		}
		
		if(($tid < 1) || ($tid > 0 && $data['task_topic'] != null)){				
					
			$fld_fname = $frm->getField('user_first_name');
			$frm->removeField($fld_fname);
			
			$fld_lname = $frm->getField('user_last_name');
			$frm->removeField($fld_lname);
			
			$fld_country = $frm->getField('user_country_id');
			$frm->removeField($fld_country);
		}  
		
		
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $frm->fill($post);
        }
		if($uid > 0) {
			$arr_task['task_type'] = 1;
		}
		$arr_task['invitee_ids'] = $uid;
		//echo "<pre>".print_r($arr_task,true)."</pre>";exit;
		if(intval($data['inv_writer_id']) > 0) {
			$uid = intval($data['inv_writer_id']);
		}
		//echo $arr_task['task_ref_id'];
		$frm->fill($arr_task);
        $this->set('frm', $frm);
		$this->set('task_id', $tid);
		$this->set('task_ref_id', $arr_task['task_ref_id']);
		$this->set('invitee_id', $uid);
		$this->set('task_topic',$data['task_topic']);
		
        $this->_template->render();
	}
	
	private function getTaskForm() {
		$service_types = $this->common->getServices();
		$frm = new Form('frmTask');		
		$frm->setExtra('enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable blockrepeat"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width="30%" align="right"');
        $frm->setAction(generateUrl('task', 'setup'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'task_id', '', 'task_id');
		$frm->addHiddenField('', 'invitee_ids', '', 'invitee_ids');
		$frm->addRequiredField('First name', 'user_first_name', '', 'user_first_name');
		$frm->addRequiredField('Last name', 'user_last_name', '', 'user_last_name');
		$frm->addSelectBox('Country', 'user_country_id', $this->common->getCountries(), '', '', 'Select Country', 'user_country_id')->requirements()->setRequired();
		
		$frm->addSelectBox('Type of paper', 'task_paptype_id', $this->common->getPaperTypes(), '', '', 'Select', 'task_paptype_id')->requirements()->setRequired();
		$frm->addRequiredField('Topic', 'task_topic', '', 'task_topic');
		
		$fld_pages = $frm->addRequiredField('Pages', 'task_pages', 1, 'task_pages', '');
		$fld_pages->requirements()->setRange(1,999);
		
		$fld_budget = $frm->addRequiredField('Order Budget<small>(in USD)</small>', 'task_budget', 1, 'task_budget', '');
		$fld_budget->requirements()->setRange(1,10000);
			
		/* $fld_deadline = $frm->addSelectBox('Deadline','task_due_date',array('3_h'=>'3 hours','6_h'=>'6 hours','12_h'=>'12 hours','24_h'=>'1 Day','2_d'=>'2 Days','3_d'=>'3 Days','4_d'=>'4 Days','5_d'=>'5 Days','6_d'=>'6 Days','7_d'=>'7 Days','10_d'=>'10 Days','14_d'=>'14 Days','21_d'=>'21 Days','28_d'=>'28 Days'),'7_d','','Select Deadline'); */

		$tomorrow = new DateTime('tomorrow');			
		$end = new DateTime('9999-12-31 23:59:59');
		$fld_deadline = $frm->addDateTimeField('Deadline','task_due_date','Choose Deadline', '', ' readonly class="task_due_date_cal"');
		$fld_deadline->requirements()->setRequired(); 	
		//$fld_deadline->requirements()->setRange(strtotime($tomorrow->format(CONF_DATE_FORMAT_PHP .' H:i:s')),strtotime($end->format(CONF_DATE_FORMAT_PHP .' H:i:s')));
		$fld_deadline->requirements()->setRange($tomorrow->format( 'd/m/Y H:i:s'),$end->format('d/m/Y H:i:s'));
		$fld_deadline->requirements()->setCustomErrorMessage('Deadline date should be of tomorrow or after that.');
		$fld_deadline->html_after_field = '<br><strong>In Timezone ' . get_time_zone_abbr() . ' Now: ' . displayDate(date('Y-m-d H:i:s'),true, true, CONF_TIMEZONE) . '</strong>';		
		 
		$frm->addHtml('', '', '<h2 class="bordertitle" align="left">Additional Information</h2>',true);
		
		$fld = $frm->addSelectBox('Discipline', 'task_discipline_id', $this->common->getDisciplines(), '', '', 'Select', 'task_discipline_id');
		$fld->requirements()->setRequired();
		//$fld->options ="";
		$service = $frm->addSelectBox('Type of service', 'task_service_type', $service_types, '','','Select','task_service_type');
		$service->requirements()->setRequired();
		
		$cite = $frm->addSelectBox('Format or citation style', 'task_citstyle_id', $this->common->getCitationStyles(), '','','Select','task_citstyle_id');
		$cite->requirements()->setRequired();
		
		
		$ins = $frm->addTextArea('Paper instructions', 'task_instructions', '', 'task_instructions');
		$ins->requirements()->setRequired();
		$fld_files = $frm->addFileUpload('Upload additional material', 'task_files[]', 'task_file');
		
		
		$frm->addHtml('','request','<h2 class="bordertitle" align="left">Request a specific writer</h2>',true);
		
		$fld_invitees = $frm->addTextBox('Invite writer', 'invitees', '', 'invitees', 'class="ui-autocomplete-input" autocomplete="off"');
		$fld_invitees->html_before_field = '<div class="ui-widget">';
		$fld_invitees->html_after_field = '</div><ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all" id="ui-id-1" tabindex="0" style="display: none;"></ul>';
		
		$chk=$frm->addCheckBox('Term','term_cond','1','term_cond');
		$chk->Requirements()->setRequired();
		$chk->html_after_field = '  I agree to Term & Conditions';
		$chkbox_html=$frm->addHTML('','abc','');
		$chkbox_html->attachField($chk);
		
		$fld_save	= $frm->addSubmitButton('', 'btn_submit', 'Publish for writers','','class="greenBtn"');
		
		//$fld_save->html_after_field = '<br><br><i>By clicking on "Publish for writers" button you are agreeing to our <a href="' . generateUrl('cms', 'terms_conditions') . '" target="_blank">Terms and Conditions</a></i>';
		return $frm;
	}
	
	private function getReviewForm($token) {
		$frm = new Form('frmReview','frmReview');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
        $frm->setAction(generateUrl('reviews', 'setup'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'task_id', '', '');
		$frm->addHiddenField('', 'user_id', '', '');
		$frm->addhiddenfield('','token',$token);
		$frm->addRadioButtons( 'Delivery', 'delivery', array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>''),'1', 5, 'width="" ','class="star"');
		$frm->addRadioButtons( 'Communication', 'communication', array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>''),'1', 5, 'width="" ','class="star"');
		$frm->addRadioButtons( 'Quality', 'quality', array('1'=>'','2'=>'','3'=>'','4'=>'','5'=>''),'1', 5, 'width="" ','class="star"');
		//$fld->html_after_field='<span class="star-rating-control"></span>';
		
		$frm->addHTML('', '', '<h5>Write a Review</h5>', true);
		
		$fld_text = $frm->addTextArea('', 'comments', '', 'comments', 'maxlength="300" onkeyup="limitTextCharacters(this,300);"');
		$fld_text->html_after_field = '<br /><span class="caption"><label id="comments_counter">0</label> / 300 characters max</span>';
		$fld_text->requirements()->setRequired();
		$fld_text->merge_caption = true;
		$fld_text->requirements()->setCustomErrorMessage('No message written.');
		
		$fld_btn = $frm->addSubmitButton('', 'btn_post_review', 'Post','btn_post_review','class="theme-btn"');
		$fld_btn->merge_caption = true;
		//$fld_btn->html_after_field = '&nbsp;&nbsp;<a class="cancelLink" href="">Cancel</a>';
		
		return $frm;
	}
	
	/* Form Function for searching new orders */
	private function getSearchForm($data,$page){
		
		
		$frm = new Form('frmordersSearch');
		$frm->setAction('orders');
		//$frm->setExtra('class="browseOrderForm"');
		$frm->captionInSameCell(true);
		for($i=1;$i <= $data['pages']; $i++) {
			$pages[$i] = $i;
		}
		$pap = $this->common->getPaperTypes();
		$frm->addTextBox('filter By Pages', 'filter_page','','filter_page','class="inputbox"');
		//$frm->addSelectBox('Filter By Deadline', 'filter_date', '', 'filter_date');
		$date_formats = array(
							'3_h'=>'3 hours',
							'6_h'=>'6 hours',
							'11_n'=>'Next day by 11am',
							'1_d'=>'Next Day',
							'2_d'=>'2 Days',
							'3_d'=>'3 Days',
							'4_d'=>'4 Days',
							'5_d'=>'5 Days',
							'7_d'=>'7 Days',
							'14_d'=>'14 Days',
							'21_d'=>'21 Days',
							'28_d'=>'28 Days'
						);
		$fld_deadline = $frm->addSelectBox('Filter By Deadline','filter_date',$date_formats,'filter_date','class="inputbox"');
		
		$frm->addSelectBox('Type of Papers', 'paptype_name',$pap,'paptype_name','class="inputbox"');
		
		$frm->addSubmitButton('', 'btn_submit', 'Search','','class="theme-btn"');
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	/* Function to list orders for writers */
	public function orders() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page','error_404'));/* die('Invalid request'); */
		if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		//$arr_orders = $this->Task->ShowOrders($page);
		$data = $post;
		$arr_orders = $this->Task->OrdersListing($data,$page);
		//echo "<pre>".print_r($arr_orders,true)."</pre>";
		
		$frm = $this->getSearchForm($arr_orders,$page);
		$frm->fill($post);
		$this->set('frm',$frm);
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render();
	}
	
	/* Function for rejection of bid by Customer */
	function reject_writer($bid_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$bid_id = intval($bid_id);
		if ($bid_id < 1) die('Invalid request');
		
		$arr_bid = $this->bid->getBidDetailsById($bid_id);
		if ($arr_bid === false || $arr_bid['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		if(!$this->bid->reject_bid($bid_id)) {
			Message::addError($this->bid->getError());
			redirectUser(generateUrl('task','bid_preview',array($bid_id)));
			return false;
		}
		
		Message::addMessage('Bid successfully rejected.');
		redirectUser(generateUrl('task','order_bids',array($arr_bid['bid_task_id'])));
		return true;
	}
	
	public function assign_writer() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=>'You must be logged in.')));
		
		if (User::isWriter()) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		
		$bid_id = intval($post['bid_id']);
		if ($bid_id < 1) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		
		$arr_bid = $this->bid->getBidDetailsById($bid_id);
		if ($arr_bid === false || $arr_bid['bid_status'] != 0 || $arr_bid['task_user_id'] != User::getLoggedUserAttribute('user_id')) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request')));
		
		$task_amount		= getOrderAmount($arr_bid['bid_price'],$arr_bid['task_pages']);
		$available_amount	= $this->wallet->getAvailableBalance();
		
		if ($available_amount < $task_amount) die(convertToJson(array('status'=>2, 'msg'=>'Insufficient amount in wallet. Max amount available for use ' . CONF_CURRENCY . number_format($available_amount,2) . '.')));
		
		if (!$this->Task->assignWriter(array('task_id'=>$arr_bid['bid_task_id'],'task_ref_id'=>$arr_bid['task_ref_id'], 'bid_id'=>$bid_id, 'writer_id'=>$arr_bid['bid_user_id'], 'task_amount'=>$task_amount,'task_due_date'=>$arr_bid['task_due_date']))) {
			die(convertToJson(array('status'=>0, 'msg'=>$this->Task->getError())));
		}
		
		die(convertToJson(array('status'=>1, 'msg'=>'', 'data'=>array('task_id'=>$arr_bid['bid_task_id']))));
	}
	
	function assign_task($bid_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$bid_id = intval($bid_id);
		if ($bid_id < 1) die('Invalid request');
		
		$arr_bid = $this->bid->getBidDetailsById($bid_id);
		if ($arr_bid === false || $arr_bid['bid_status'] != 0 || $arr_bid['task_status'] != 1 || $arr_bid['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		$msg = confirm_payment_loaded($_SERVER['REQUEST_URI']);
		if($msg !== false) Message::addMessage($msg);
		
		$writer_rating = $this->ratings->getAvgUserRating($arr_bid['bid_user_id']);
		$writer_activity_stats = $this->Order->getWriterActivityStats($arr_bid['bid_user_id']);
		
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_infotips');
		$srch->addCondition('infotip_id','=',1);
		$srch->addFld('infotip_description');
		$rs = $srch->getResultSet();
		$this->set('arr_tip',$db->fetch($rs));
		
		$this->set('writer_completed_orders', $writer_activity_stats['orders_completed']);
		$this->set('writer_rating', $writer_rating);
		$this->set('arr_bid', $arr_bid);
		$this->set('frm', $this->transactionsController->getLoadWalletForm( $arr_bid['bid_task_id'], $arr_bid['bid_price']));
		
		/* @DD Start */
		$this->set( 'availableWalletAmount', $this->wallet->getAvailableBalance() );
		$this->set( 'walletLimit', CONF_WALLET_LIMIT );
		$this->set( 'serviceCharges', CONF_SERVICE_CHARGE );
		/* @DD End */
		
		$this->_template->render();
	}
	
	/* private function getAssignTaskForm() {
		if (!User::isUserLogged() || User::isWriter()) die('Invalid request');
		
		$frm = new Form('frmcheckout');
		
		$frm->setOnSubmit("if(!confirm('Are you sure you want to proceed?')) {return false;}");
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width=20%');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setAction(generateUrl('task', 'assign_writer'));
		
		$frm->addSubmitButton('', 'btn_submit', 'Reserve Money & Start Work Process', 'btn_submit');
		
		return $frm;
	} */
	
	public function uploadSetup() {
		if (!User::isUserLogged()) die('Invalid request.');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die('Invalid request.');
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request.');
		
		//$frm = $this->getAdditionalForm();
		
		/** upload additional material **/
		$arr_uploaded_files = $this->upload_files($_FILES['task_files']);
		
		if ($arr_uploaded_files === false) {
			redirectUser($_SERVER['HTTP_REFERER']);
		}
		
		if (!empty($arr_uploaded_files)) {
			foreach ($arr_uploaded_files as $ele) {
				if (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name'])) {
					if (!$this->Task->addFile(array('file_task_id'=>$task_id, 'file_name'=>$ele['file_name'], 'file_download_name'=>$ele['file_download_name'], 'file_type'=>$ele['file_type'],'file_size'=>$ele['file_size'], 'file_class'=>1))) {
						unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $ele['file_name']);
						Message::addErrorMessage($this->Task->getError());
					}
				}
			}
		}
		/* ####### */
		
		/* $num_files_uploaded = count($_FILES['task_files']['name']);
		
		if ($num_files_uploaded > 0) {
			$arr_valid_text_ext		= array('txt', 'pdf', 'doc', 'docx', 'xls');
			$arr_valid_image_ext	= array('jpg', 'jpeg', 'png', 'gif');
			
			
			for ($i=0; $i<$num_files_uploaded; $i++) {
				$file_size += $_FILES['task_files']['size'][$i];
				if($file_size > 32086425 ) {
					Message::addErrorMessage('Could not upload above 30MB.');
					redirectUser($_SERVER['HTTP_REFERER']);
				}
			}
			
			for ($i=0; $i<$num_files_uploaded; $i++) {
				if (is_uploaded_file($_FILES['task_files']['tmp_name'][$i])) {
					$path_parts = pathinfo($_FILES['task_files']['name'][$i]);
					
					$file_ext		= strtolower($path_parts['extension']);
					$base_name		= $path_parts['basename'];
					
					if (!in_array($file_ext,$arr_valid_text_ext) && !in_array($file_ext,$arr_valid_image_ext)) {
						Message::addErrorMessage('Could not upload ' . $base_name . '. Invalid file format.');
						redirectUser($_SERVER['HTTP_REFERER']);
					}
					
					if ($_FILES['task_files']['size'][$i] > 10695475) {				//restrict file size to 10MB
						Message::addErrorMessage('Could not upload ' . $base_name . '. File size too big, max allowed size 10MB.');
						redirectUser($_SERVER['HTTP_REFERER']);
					}
					
					if (in_array($file_ext,$arr_valid_image_ext)) {
						$file_type = 2;
						
						if (!saveImage($_FILES['task_files']['tmp_name'][$i], $_FILES['task_files']['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							redirectUser($_SERVER['HTTP_REFERER']);
						}
					}
					elseif(in_array($file_ext,$arr_valid_text_ext)) {
						$file_type = 1;
						
						if (!saveFile($_FILES['task_files']['tmp_name'][$i], $_FILES['task_files']['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							redirectUser($_SERVER['HTTP_REFERER']);
						}
					}
					else {
						Message::addErrorMessage('Could not upload ' . $base_name . '. Invalid file format.');
						redirectUser($_SERVER['HTTP_REFERER']);
					}
					
					if (!$this->Task->addFile(array('file_name'=>$saved_file_name, 'file_download_name'=>$base_name, 'file_class'=>1, 'file_type'=>$file_type , 'file_task_id'=>$task_id,'task_status'=>$arr_task['task_status'],'user_ref_id'=>$arr_task['customer_ref_id'],'task_ref_id'=>$arr_task['task_ref_id'],'file_size'=>$_FILES['task_files']['size'][$i]))) {
						unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $saved_file_name);
						Message::addErrorMessage($this->Task->getError());
					}
				}else {
					Message::addErrorMessage('No File(s) uploaded.');
					redirectUser($_SERVER['HTTP_REFERER']);
				}
			}
		} */
		
		if($arr_task['task_status'] == 2) {
			
			$this->Task->sendEmail(array('to'=>$arr_task['writer_email'],'temp_num'=>36,'user_ref_id'=>$arr_task['customer_ref_id'],'user_screen_name'=>$arr_task['writer'],'user_first_name'=>$arr_task['customer_first_name'],'order_ref_id'=>$arr_task['task_ref_id']));
			$this->Task->sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>36,'user_ref_id'=>$arr_task['customer_ref_id'],'user_screen_name'=>'Administrator','user_first_name'=>$arr_task['customer_first_name'],'order_ref_id'=>$arr_task['task_ref_id']));
		}
		Message::addMessage('File(s) uploaded successfully.');
		
		redirectUser($_SERVER['HTTP_REFERER']);
	}
	
	/* Function to upload additional material by customer on particular task */
	function upload_additional($tid) {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die('You must be logged in.');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$tid = intval($tid);
		
		$arr_task = $this->Task->getTaskDetailsById($tid);		
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request.');
		
        $frm = $this->getAdditionalForm();
	
		$frm->addHiddenField('','task_id',$tid);
		
		$this->set('frm', $frm);
		$this->set('task_id', $tid);
		
        $this->_template->render(false,false);
	}
	
	private function getAdditionalForm() {
		if (!User::isUserLogged()) die('Invalid request');
		$frm = new Form('frmadditionalupload');
		
		$frm->setExtra('class="siteForm" enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		//$frm->setLeftColumnProperties('width="40%"');
        $frm->setAction(generateUrl('task', 'uploadSetup'));
		$frm->setJsErrorDisplay('afterfield');
		
		$fld_files = $frm->addFileUpload('', 'task_files[]', 'task_file', 'multiple="multiple" class="input-filetype"');
		$fld_files->requirements()->setRequired();
		$fld_files->requirements()->setCustomErrorMessage('No files selected');
		
		$fld_files->setRequiredStarWith('none');
		
		$frm->addHTML('', '', '<div class="gap"></div>');
		
		$frm->addSubmitButton('','submit', 'Upload');
		
		return $frm;
	}
	
	/* Function to Extend deadline for Task. */
	public function extend_deadline($task_id) {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die('You must be logged in.');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$task_id = intval($task_id);
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);		
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request.');
		
		$frm = $this->getExtendForm();
		$frm->addHiddenField('','task_id',$task_id);
		$frm->addSubmitButton('','submit', 'Update', ''/* ,'onclick="checkDate('.$task_id.');return false;"' */);
		$this->set('frm', $frm);
		$this->set('arr_task', $arr_task);
		
		$this->_template->render(false,false);
	}
	
	private function getExtendForm() {
		if (!User::isUserLogged()) die('Invalid request');
		$frm = new Form('frmextenddeadline');
		
		$frm->setExtra('class="siteForm" enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		
        $frm->setOnSubmit('checkDate(this);return false;');
		$frm->setJsErrorDisplay('afterfield');
				
		/* $fld_deadline = $frm->addSelectBox('Deadline','task_due_date',array('3_h'=>'3 hours','6_h'=>'6 hours','12_h'=>'12 hours','24_h'=>'1 Day','2_d'=>'2 Days','3_d'=>'3 Days','4_d'=>'4 Days','5_d'=>'5 Days','6_d'=>'6 Days','7_d'=>'7 Days','10_d'=>'10 Days','14_d'=>'14 Days','21_d'=>'21 Days','28_d'=>'28 Days'),'','','Select to add','task_due_date');  */		
		$fld_deadline = $frm->addSelectBox('Deadline','task_due_date',array('30_m'=>'30 Minutes','1_h'=>'1 hour','2_h'=>'2 hours','4_h'=>'4 hours','6_h'=>'6 hours','8_h'=>'8 hours','12_h'=>'12 hours','24_h'=>'24 hours','2_d'=>'2 Days'),'','','Select to add','task_due_date'); 		
		
		$frm->addHTML('', '', '<div class="gap"></div>');
		
		
		
		return $frm;
	}
	
	public function extendsetup() {
		
		if (!User::isUserLogged()) die('You must be logged in.');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();	
		$task_id=intval($post['task_id']);
		if ($task_id < 1) die('Invalid request.');
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);		
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request.');
		
		$frm = $this->getExtendForm();
		
		$task_due_date = array();
		$task_due_date = explode('_',$post['task_due_date']);
		$data = $post;
		switch ($task_due_date[1]) {
			case 'm' :
				$data['task_due_date'] = strtotime('+'.$task_due_date[0].' minutes',strtotime($arr_task['task_due_date']));
				$data['task_due_date'] = date('Y-m-d H:i:s',$data['task_due_date']);
				$deadline_extention  = $task_due_date[0].' minutes';
				break;
			case 'h' :
				$data['task_due_date'] = strtotime('+'.$task_due_date[0].' hours',strtotime($arr_task['task_due_date']));
				$data['task_due_date'] = date('Y-m-d H:i:s',$data['task_due_date']);
				$deadline_extention  = $task_due_date[0].' hours';
				break;
			case 'n' :
				$data['task_due_date'] = date('Y-m-d H:i:s'.$task_due_date[0].':00',strtotime('tomorrow'));
				$deadline_extention  = $task_due_date[0].' hours';
				break;
			case 'd' :
				$data['task_due_date'] = strtotime('+'.$task_due_date[0].' days',strtotime($arr_task['task_due_date']));
				$data['task_due_date'] = date('Y-m-d H:i:s',$data['task_due_date']);
				$deadline_extention  = $task_due_date[0].' days';
				break;
			default :
				$data['task_due_date'] = strtotime('+7 days',time());
				$data['task_due_date'] = date('Y-m-d H:i:s',$data['task_due_date']);
				$deadline_extention  = '7 days';
				break;
		} 
		
		
		//$post['task_due_date'] = $post['task_due_date'] . ' ' . intval($post['task_time']) . ':00:00';
		if (strtotime($data['task_due_date']) <= time()) {
			//Message::addErrorMessage('Please Select Future Date');
			
			die(convertToJson(array('status'=>0, 'msg'=>'Please enter future date.')));
			return false;
		} 
		if (!$this->Task->updateDeadline($data)) {
		
		die(convertToJson(array('status'=>0, 'msg'=>$this->Task->getError())));
			//Message::addErrorMessage($this->Task->getError());
		}
		else {
			$this->Task->sendEmail(array('to'=>$arr_task['customer_email'],'temp_num'=>30,'order_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_first_name'=>$arr_task['customer_first_name'],'deadline_extension'=>$deadline_extention,'new_deadline'=>date('Y-m-d H:i:s',strtotime($data['task_due_date']))));
			$this->Task->sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>29,'user_screen_name'=>'Administrator','order_ref_id'=>$arr_task['task_ref_id'],'deadline_extension'=>$deadline_extention,'new_deadline'=>date('Y-m-d H:i:s',strtotime($data['task_due_date']))));
			if($arr_task{'task_status'} != 2) {
				$bi = $this->bid->getOrderBids($task_id);						
			
				foreach($bi as $key=>$value) {				
					Task::sendEmail(array('temp_num'=>29,'to'=>$value['user_email'],'user_screen_name'=>$value['user_screen_name'],'order_ref_id'=>$arr_task['task_ref_id'],'deadline_extension'=>$deadline_extention,'new_deadline'=>date('Y-m-d H:i:s',strtotime($data['task_due_date']))));
				}
			}else {
				Task::sendEmail(array('temp_num'=>29,'to'=>$arr_task['writer_email'],'user_screen_name'=>$value['user_screen_name'],'order_ref_id'=>$arr_task['task_ref_id'],'deadline_extension'=>$deadline_extention,'new_deadline'=>date('Y-m-d H:i:s',strtotime($data['task_due_date']))));
			}
			die(convertToJson(array('status'=>1, 'msg'=>'Deadline extended successfully.')));
			//Message::addMessage('Deadline extended successfully.');
		}
		return false;
		//redirectUser($_SERVER['HTTP_REFERER']);
	}
	
	public function mark_as_finished($task_id) {
		if (!User::isUserLogged()) redirectUser('user', 'signin');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!$this->Task->changeOrderStatus($task_id,3)) {
			Message::addErrorMessage($this->Task->getError());
		}
		
		redirectUser($_SERVER['HTTP_REFERER']);
	}
	
	/* Function to cancel task by customer */
	function cancel_order($tid) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$tid = intval($tid);
		if ($tid < 1) die('Invalid request');
		
		$arr_task = $this->Task->getTaskDetailsById($tid);
						
		//echo "<pre>".print_r($arr_task,true)."</pre>";exit;
		if ($arr_task === false) die('Invalid request');
		if ($arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		if ($this->Task->cancel_order($tid)) {
			/* if (!$this->bid->cancelOrderBids($tid)) {
				Message::addError($this->bid->getError());
			} */
			$this->Task->send_notification_customer($arr_task);
			$this->Task->sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>16,'website_url'=>$_SERVER['SERVER_NAME'],'order_ref_id'=>$arr_task['task_ref_id'],'user_first_name'=>$arr_task['customer_first_name'],'user_screen_name'=>'Administrator'));
			if($arr_task['task_status'] != 2) {
				$bi = $this->bid->getOrderBids($tid);
				foreach($bi as $val) {
					$this->Task->sendEmail(array('to'=>$val['user_email'],'temp_num'=>16,'website_url'=>$_SERVER['SERVER_NAME'],'user_screen_name'=>$val['writer'],'order_ref_id'=>$arr_task['task_ref_id'],'user_ref_id'=>$arr_task['customer_ref_id'],'user_first_name'=>$arr_task['customer_first_name']));
				}
			}else {
				$this->Task->sendEmail(array('to'=>$arr_task['writer_email'],'temp_num'=>16,'website_url'=>$_SERVER['SERVER_NAME'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id'],'user_first_name'=>$arr_task['customer_first_name']));
			}
		}
		else {
			Message::addError($this->Task->getError());
		}
		
		Message::addMessage('Your order has been cancelled.Please check your email inbox!');
		
		redirectUser(generateUrl('task','my_orders'));
	}
	
	/* Function to request cancel task by writer */
	function cancel_order_request($tid) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		//if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$tid = intval($tid);
		if ($tid < 1) die('Invalid request');
		
		$arr_task = $this->Task->getTaskDetailsById($tid);
		
		if ($arr_task === false) die('Invalid request');
		//
		if ($arr_task['task_writer_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		$frm = $this->getCancelOrderReqFrm();
		$frm->addHiddenField('','task_id',$task_id);
		
		$this->set('frm', $frm);
		$this->set('arr_task', $arr_task);
		
		$this->_template->render(false,false);
		
	}
	
	function cancel_order_request_setup() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		//if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();
		
		$post['task_id'] = intval($post['task_id']);
		if ($post['task_id'] < 1) die('Invalid request');
		
		$arr_task = $this->Task->getTaskDetailsById($post['task_id']);
		
		if ($arr_task === false) die('Invalid request');
		//
		if ($arr_task['task_writer_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		if (!$this->Task->cancelOrderRequest($post)) {
			Message::addErrorMessage($this->Task->getError());			
		}else {
			$arr = array(
					'to'=>CONF_ADMIN_EMAIL_ID,
					'temp_num'=>57,
					'user_first_name'=>	'Administrator',
					'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
					'order_ref_id'=>$arr_task['task_ref_id'],
					'user_request'=>$post['task_desc_cancel_req'],
					'order_page_link'=>CONF_SITE_URL.'/manager/orders/order_details/'.$arr_task['task_id']
					);
			$param = array(
					'to'=>$arr_task['customer_email'],
					'temp_num'=>57,
					'user_first_name'=>	$arr_task['customer_first_name'],
					'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
					'order_ref_id'=>$arr_task['task_ref_id'],
					'user_request'=>$post['task_desc_cancel_req'],
					'order_page_link'=>CONF_SITE_URL.'/order_process/'.$arr_task['task_id']
					);
			$this->Task->sendEmail($arr);
			$this->Task->sendEmail($param);
/* 			$db = &Syspage::getdb();
			$rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 14");
			$row_tpl = $db->fetch($rs);
        	$subject = $row_tpl['tpl_subject'];
			$body 	 = $row_tpl['tpl_body'];
				
			$arr_replacements = array(
				'{website_name}'=>CONF_WEBSITE_NAME,
				'{website_url}'=>$_SERVER['SERVER_NAME'],
				'{user_name}'=>User::getLoggedUserAttribute('user_screen_name'),
				'{user_request}'=>$post['task_desc_cancel_req'],
				'{order_ref_id}'=>$arr_task['task_ref_id'],
				'{order_page_link}'=>CONF_SITE_URL.generateUrl('task','order_process',array($post['task_id']))
			);
					
			foreach ($arr_replacements as $key=>$val){
				$subject	= str_replace($key, $val, $subject);
				$body		= str_replace($key, $val, $body);
			}
			
			if(sendMail(CONF_ADMIN_EMAIL_ID, $subject, $body)){
 */				Message::addMessage('Your request has been sent to Administrator! ');
			/* }else{
				Message::addErrorMessage('Failed to Send Email to Administrator!');
			}	 */		
		}
		redirectUser(generateUrl('task','order_process',array($post['task_id'])));
	}
	
	private function getCancelOrderReqFrm(){
		if (!User::isUserLogged()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$frm = new Form('frmcancelorderreq');
		
		$frm->setExtra('class="siteForm" enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		//$frm->setLeftColumnProperties('width="40%"');
		$frm->setAction(generateUrl('task', 'cancel_order_request_setup'));
        
		$frm->setJsErrorDisplay('afterfield');
		
		
		$fld = $frm->addTextArea('Description for Cancel Order','task_desc_cancel_req','','task_desc_cancel_req');
		$fld->requirements()->setRequired();
		$frm->addHTML('', '', '<div class="gap"></div>');
		
		$frm->addSubmitButton('','btn_submit', 'Send Request', '');
		
		return $frm;
	}	
	
	/* Function to activate cancelled order */
	function activate_order($tid) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if(!$this->Task->activate_order($tid)) {
			Message::addError($this->Task->getError());
			redirectUser(generateUrl('task','my_orders'));
			return false;
		}
		Message::addMessage('Order is activated!');
		redirectUser(generateUrl('task','my_orders'));
		return true;
	}
	
	public function make_task_public() {
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request.')));
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		if ($arr_task === false || $arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id') || in_array($arr_task['task_status'],array(2,3,4))) {
			die(convertToJson(array('status'=>0, 'msg'=>'Invalid request.')));
		}
		
		if (!$this->Task->makeTaskPublic($task_id)) {
			die(convertToJson(array('status'=>0, 'msg'=>$this->Task->getError())));
		}
		
		die(convertToJson(array('status'=>1, 'msg'=>'')));
	}
	
	public function decline_invitation($task_id) {
		$task_id = intval($task_id);
		if ($task_id < 1) die('Invalid request.');
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		//echo '<pre>' . print_r($arr_task,true) . '</pre>';
		
		if ($arr_task === false || $arr_task['inv_writer_id'] != User::getLoggedUserAttribute('user_id') || $arr_task['inv_status'] != 0) {
			die('Invalid request.');
		}
		
		if (!$this->Task->updateInvitationStatus($task_id,2)) {
			Message::addErrorMessage($this->Task->getError());
		}
		
		Message::addMessage('Invitation declined.');
		
		redirectUser(generateUrl('bid', 'my_bids', array('invitations')));
	}
	
	
	function revision_request($task_id){
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		$task_id = intval($task_id);
		if ($task_id < 1) die('Invalid request');
		
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		
		if ($arr_task === false) die('Invalid request');
		
		if ($arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		$frm = $this->getRequestToReviewForm($arr_task);
		$frm->addHiddenField('','task_id',$task_id);
		//$frm->addHiddenField('','bid_id',$task_id);
		
		$this->set('frm', $frm);
		$this->set('arr_task', $arr_task);
		
		$this->_template->render(false,false);
		
	}
	
	
	function revision_request_setup() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		//if (User::isWriter()) die('Invalid request');
		$post = Syspage::getPostedVar();
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die('Invalid request');
		unset($post['btn_submit']);
		$data = $post;
		unset($post);
		//echo '<pre>'.print_r($data,true);exit;
		$arr_task = $this->Task->getTaskDetailsById($task_id);
		
		if ($arr_task === false) die('Invalid request');
		//
		$data['autodebit_on'] = $arr_task['autodebit_on'];
		//echo "<pre>".print_r($data,true)."</pre>";exit;
		
		if ($arr_task['task_user_id'] != User::getLoggedUserAttribute('user_id')) die('Invalid request');
		
		if (!$this->Task->Revision_Request($data)) {
			Message::addErrorMessage($this->Task->getError());			
		}else {
			$db = &Syspage::getdb();
			$param = array('to'=>CONF_ADMIN_EMAIL_ID,'user_screen_name'=>$arr_task['writer'],'user_first_name'=>$arr_task['customer_first_name'],'user_ref_id' => $arr_task['customer_ref_id'],'user_request'=>$data['task_review_description'],'order_ref_id'=>$arr_task['task_ref_id'],'temp_num'=>14);
			
			/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 14");
			$row_tpl = $db->fetch($rs);
        	$subject = $row_tpl['tpl_subject'];
			$body 	 = $row_tpl['tpl_body'];
				
			$arr_replacements = array(
				'{website_name}'=>CONF_WEBSITE_NAME,
				'{website_url}'=>$_SERVER['SERVER_NAME'],
				'{user_screen_name}'=>$arr_task['writer'],
				'{user_first_name}'=>$arr_task['customer_first_name'],
				'{user_ref_id}' => $arr_task['customer_ref_id'],
				'{user_request}'=>$data['task_desc_cancel_req'],
				'{order_ref_id}'=>$arr_task['task_ref_id']
			);
					
			foreach ($arr_replacements as $key=>$val){
				$subject	= str_replace($key, $val, $subject);
				$body		= str_replace($key, $val, $body);
			} */
			
			
			
			if($this->Task->sendEmail($param)){
				Message::addMessage('Email sent to writer and administrator. Deadline for auto-debit is extended for 24hrs.');
			}else{
				Message::addErrorMessage('Failed to Send Email to Administrator!');
			}	
			
			$param = array(
						'to'=>$arr_task['writer_email'],
						'temp_num'=>40,'user_screen_name'=>$arr_task['writer'],
						'user_first_name'=>$arr_task['customer_first_name'],
						'user_ref_id'=>$arr_task['customer_ref_id'],
						'user_request'=>$data['task_review_description'],
						'request_time'=>'24hrs','order_ref_id'=>$arr_task['task_ref_id']
						);
			
			$this->Task->sendEmail($param);		
		} 
		redirectUser(generateUrl('task','order_process',array($task_id)));
	}
	
	private function getRequestToReviewForm($arr_task){
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		$frm = new Form('requesttoreview');
		
		$frm->setExtra('class="siteForm" enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->captionInSameCell(false);
		//$frm->setLeftColumnProperties('width="40%"');
		$frm->setAction(generateUrl('task', 'revision_request_setup'));
        
		$frm->setJsErrorDisplay('afterfield');
		//$frm->addHiddenField('','autodebit_on',$arr_task['autodebit_on'],'autodebit_on');
		$frm->addHiddenField('','bid_id',$arr_task['bid_id']);
		
		//$fld = $frm->addTextArea('Description to request','task_review_description','','task_review_description');
		$fld = $frm->addTextArea('Describe in detail what to revise.','task_review_description','','task_review_description');
		$fld->requirements()->setRequired();
		$frm->addHTML('', '', '<div class="gap"></div>');
		
		$frm->addSubmitButton('','btn_submit', 'Send Request', '');
		
		return $frm;
	}
	
	public function markOrderCompleteByWtr($task_id) {
		$post = Syspage::getPostedVar();
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die('Invalid request.');
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die('Invalid request');
		if (!User::isUserActive()) {
			Message::addErrorMessage('Your account has been De-Activated by '.CONF_WEBSITE_NAME.' team. Please feel free to write to us(using contact form) for more info about this problem you are facing.');
			redirectUser(generateUrl('user', 'signin'));
		}
		if(!$this->Task->taskFinishedFromWriter($task_id)) {
			die('<div class="popup_msg failure">'.$this->Task->getError().'</div>');
		}
		die('<div class="popup_msg success">Order is marked as finished by you.</div>');
	}
	
	public function test() {
		echo 'Hello';
		
		while(1) {
			echo "In loop!\n";
		}
		$this->_template->render();
	}
	
	/* Cron functions ---------------------------------------------------------------*/
	
	function update_order_status() {
		$this->Task->updateTaskStatus();
	}
	
	public function send_notification_to_cust_about_upoaled_file() {           // function created for those orders where 24hrs are completed after writer has uploaded project files.
				
		$data = $this->Task->getLast24hrs();
		
		foreach($data as $key=>$val) {
			$arr_task = $this->Task->getTaskDetailsById($val['task_id']);
			$arr_email = array(
							'temp_num'=>60,
							'to'=>$arr_task['customer_email'],
							'order_ref_id'=>$arr_task['task_ref_id'],
							'user_first_name'=>$arr_task['customer_first_name']
							);
			
			$this->Task->sendEmail($arr_email);
		}
		
	}	
	
	public function extend_autodebit_time_24hrs(){
		$row = $this->Task->get_orders_after_request_24hrs();
		foreach($row as $key=>$val) {
						
			if(!$this->Task->getExtend24hrs($val['task_id'],$row['bid_id'])){
				return false;
			}else {
				$arr_task = $this->Task->getTaskDetailsById($val['task_id']);
				
				$arr_email = array(
							'temp_num'=>61,
							'to'=>$arr_task['writer_email'],
							'order_ref_id'=>$arr_task['task_ref_id'],
							'user_screen_name'=>$arr_task['writer']
							);
			
				$this->Task->sendEmail($arr_email);
			}
		}
	}
	
	public function latest_orders() {
		if(User::isUserLogged()) {
			if(User::isWriter()) {
				redirectUser(generateUrl('task','orders'));
			}else {
				redirectUser(generateUrl('user','dashboard'));
			}
		}
		
		$post = Syspage::getPostedVar();		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		
		$data = $post;
		$arr_orders = $this->Task->OrdersListing($data,$page);
		
		
		$frm = $this->getSearchForm($arr_orders,$page);
		$frm->fill($post);
		$this->set('frm',$frm);
		$this->set('paper_types', $this->common->getPaperTypes());
		$this->set('arr_orders', $arr_orders);
		
									
		$latest_content = Cmsblock::getContent('latest_writer_1', 'content');
		$this->set('page_content', $latest_content);
		
		$latest_title = Cmsblock::getContent('latest_writer_1', 'title');
		$this->set('page_title', $latest_title); 
				
		$Meta = new Meta();
		$meta = $Meta->getMetaById(3);		
		
		$arr_listing=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);

		$this->set('arr_listing', $arr_listing);
		
		$this->_template->render();
	}
	
	public function search_orders() {
		$post = Syspage::getPostedVar();		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$data = $post;
		$arr_orders = $this->Task->getLatestOrders($data,$page);
				
		//$frm = $this->getSearchForm($arr_orders,$page);
		//$frm->fill($post);
		$this->set('filter',$post['filter']);
		$this->set('filter_value',$post['filter_value']);
		$this->set('arr_orders', $arr_orders);
		$this->_template->render(false,false);
	}
	
	public function request_writer($user_id) {
		$post = Syspage::getPostedVar();
		if (User::isUserLogged()) redirectUser(generateUrl('user', 'dashboard'));
		
		$user_id = intval($user_id);
		
        $frm = $this->userController->getLoginForm();
        if($user_id > 0) {
			$fld = $frm->getField('invite_user_id');
			$fld->value = $user_id;
		}
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			unset($post['btn_login']);
            $frm->fill($post);
        }
		
        $this->set('frm', $frm);
        //$this->set('status', $task_id);
		$this->_template->render(false,false);
	}
	
	
}