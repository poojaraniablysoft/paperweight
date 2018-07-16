<?php
	class Task extends Model {
		protected $error;
		private $taskId;
		private $wallet;
		private $bid;
		private $user;
		
		public function __construct() {
			parent::__construct();
			
			$this->taskId = 0;
			$this->bid = new Bid();
			$this->wallet = new Wallet();
			//$this->user = new User();
		}
		
		public function getError(){
			return $this->error;
		}
		
		public function getTaskId() {
			return $this->taskId;
		}
		
		private function generateTaskReferenceId() {
			$db = &Syspage::getdb();
			
			$sql = $db->query("SELECT task_ref_id FROM tbl_tasks");
			
			$arr_ref_id = $db->fetch_all_assoc($sql);
			$arr_ref_id = array_keys($arr_ref_id);
			
			do {
				$ref_id = substr(time()*rand(),0,10);
			} while (in_array($ref_id,$arr_ref_id));
			
			return $ref_id;
		}
		
		function send_notification($task_id) {
			$db = &Syspage::getdb();
			
			$arr_task = $this->getTaskDetailsById($task_id);
			
			$cancel_order = new TableRecord('tbl_tasks');
			$cancel_order->setFldValue('task_status',4);
			
			if(!$cancel_order->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
				$this->error = $cancel_order->getError();
				return false;
			}
			
			$update_bids = new TableRecord('tbl_bids');
			$update_bids->setFldValue('bid_status',3);
			
			if(!$update_bids->update(array('smt'=>'bid_task_id = ?', 'vals'=>array($task_id)))) {
				$this->error = $record->getError();
				return false;
			}
			else {
				 
				$bidObj		= new Bid();
				$arr_bids = $bidObj->getOrderBids($task_id);	
				 
				foreach($arr_bids as $bids){				 
					$this->sendEmail(array('to'=>$bids['user_email'],'temp_num'=>67,'user_ref_id'=>$arr_task['customer_ref_id'],'user_screen_name'=>$bids['writer'],'user_first_name'=>$arr_task['customer_first_name'],'order_ref_id'=>$arr_task['task_ref_id']));
					 
				}
				$this->sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>67,'user_ref_id'=>$arr_task['customer_ref_id'],'user_screen_name'=>'Administrator','user_first_name'=>$arr_task['customer_first_name'],'order_ref_id'=>$arr_task['task_ref_id']));
				 
				/* $srch = new SearchBase('tbl_users','u');
				$srch->joinTable('tbl_bids','INNER JOIN','b.bid_user_id = u.user_id','b');
				$srch->addFld('user_email');
				$srch->addCondition('b.bid_task_id','=',$task_id);
				$rs = $srch->getResultSet();
				$row_user = $db->fetch_all($rs); */
				
				/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 6");
					$row_tpl = $db->fetch($rs);
					$subject = $row_tpl['tpl_subject'];
					$body 	 = $row_tpl['tpl_body'];
					
					$arr_replacements = array(
					'{website_name}'=>CONF_WEBSITE_NAME,
					'{website_url}'=>$_SERVER['SERVER_NAME']
					);
					
					foreach ($arr_replacements as $key=>$val){
					$subject	= str_replace($key, $val, $subject);
					$body		= str_replace($key, $val, $body);
				} */
				
				/* foreach($row_user as $user_email) { commented to avoid blank emails
					$data = array('temp_num'=>6,'to'=>$user_email['user_email']);
					$this->sendEmail($data);
				} */
			}
			
			return true;
		}
		
		public function addTask($data) {
			
			$db = &Syspage::getdb();
			$task_id = intval($data['task_id']);
			$task_topic = $db->fetch($db->query('SELECT task_topic from tbl_tasks where task_id='.$task_id));
			
			$data['task_topic'] = htmlentities(htmlentities($data['task_topic']));
			$res = $db->fetch($db->query('SELECT user_email, user_first_name from tbl_users where user_id='.$data['task_user_id']));
			
			$record = new TableRecord('tbl_tasks');       
			if($data['invitee_ids'] > 0) {
				$record->setFldValue('task_type', 1);
			}
			
			if ($task_id > 0) {		

				if(isset($data['task_topic']) && $data['task_status'] != 0) {
					
					$this->send_notification($task_id);
					unset($data['task_id']);
					
					$record->assignValues($data);
					$record->setFldValue('task_finished_from_writer', 0);
					$record->setFldValue('task_status',0);
					$record->setFldValue('task_posted_on', 'mysql_func_NOW()', true);
					$record->setFldValue('task_ref_id', $this->generateTaskReferenceId());
					$record->setFldValue('task_words_per_page', CONF_WORDS_PER_PAGE);
					$record->setFldValue('task_due_date', $data['task_due_date']);
					
					if (!$record->addNew()) {
						$this->error = $record->getError();
						return false;
						}else {
						$task_ref_id = $db->fetch($db->query('SELECT task_ref_id from tbl_tasks where task_id='.$task_id));
						$param = array(
						'order_ref_id'=>$task_ref_id['task_ref_id'],
						'user_email'=>$res['user_email'],
						'user_ref_id'=>User::getLoggedUserAttribute('user_screen_name'),
						'user_first_name'=>$res['user_first_name'],
						'to'=>User::getLoggedUserAttribute('user_email'),
						'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($record->getId())),
						'temp_num'=>19
						);
						$is_reg = $this->is_user_registered();										
						if($is_reg == true) { //check if user registered
							$this->sendEmail(array('user_email'=>$res['user_email'],'user_first_name'=>$res['user_first_name'],'temp_num'=>18,'to'=>CONF_ADMIN_EMAIL_ID));
						}
						$this->sendEmail($param);
						$task_id = $record->getId();
					}
					}else { // To complete order placement first time.			
					
					if($data['task_status'] == 0 && $task_topic['task_topic'] == null) {
						
						$update_user = new TableRecord('tbl_users');
						//$update_user->setFldValue('user_screen_name',User::getLoggedUserAttribute('user_email'));
						$update_user->setFldValue('user_first_name',$data['user_first_name']);
						$update_user->setFldValue('user_last_name',$data['user_last_name']);
						$update_user->setFldValue('user_country_id',$data['user_country_id']);
						
						if(!$update_user->update(array('smt'=>'user_id = ? and user_email = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'), User::getLoggedUserAttribute('user_email'))))) {
							$this->error = $update_user->getError();
							return false;
						}
						$_SESSION['logged_user']['user_screen_name'] = $data['user_first_name'];
					} 
					
						
					$record->assignValues($data);
					
					$record->setFldValue('task_due_date',$data['task_due_date']);
					$record->setFldValue('task_finished_from_writer', 0);
					$record->setFldValue('task_posted_on', 'mysql_func_NOW()', true);
					$record->setFldValue('task_words_per_page', CONF_WORDS_PER_PAGE);
					
					if(!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
						$this->error = $record->getError();
						return false;
						}else{
						$user_info = User::getUserDetailsById(User::getLoggedUserAttribute('user_id'));
						$param = array(
						'order_ref_id'=>$data['task_ref_id'],
						'user_email'=>$res['user_email'],
						'user_first_name'=>$user_info['user_first_name'],
						'user_ref_id'=>$user_info['user_ref_id'],
						'to'=>User::getLoggedUserAttribute('user_email'),
						'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($data['task_id'])),
						'temp_num'=>58
						);
						$is_reg = $this->is_user_registered();										
						if($is_reg == true) { //check if user registered
							$this->sendEmail(array('user_email'=>$res['user_email'],'user_first_name'=>$user_info['user_first_name'],'temp_num'=>18,'to'=>CONF_ADMIN_EMAIL_ID));
						}					
						$this->sendEmail($param);
					} 
				}			
				}else {			
				
				$record->assignValues($data);
				/* switch(CONF_DATE_FORMAT_PHP){
					case 'd/m/Y':
					$phpdate = strtotime(str_replace('/','-',$data['task_due_date']));				
					break;
					
					case 'm-d-Y':			
					$phpdate = strtotime(str_replace('-','/',$data['task_due_date']));				
					break;	
					
					
					default :
					$phpdate = strtotime($data['task_due_date']);				
					} */
				$phpdate = strtotime(str_replace('/','-',$data['task_due_date']));
				$mysqldate = date('Y-m-d H:i:s', $phpdate);
				/* $mysqldate = date('Y-m-d H:i:s', timeStampFromInputDate($data['task_due_date'])); */
				
				$record->setFldValue('task_due_date',$mysqldate);
				$record->setFldValue('task_finished_from_writer', 0);
				$record->setFldValue('task_posted_on', 'mysql_func_NOW()', true);
				$record->setFldValue('task_ref_id', $this->generateTaskReferenceId());
				$record->setFldValue('task_words_per_page', CONF_WORDS_PER_PAGE);
				
				if (! $record->addNew()) {
					$this->error = $record->getError();
					return false;
					}else {
						
					$task_ref_id = $db->fetch($db->query('SELECT task_ref_id from tbl_tasks where task_id='.$record->getId()));
					$user_info = User::getUserDetailsById(User::getLoggedUserAttribute('user_id'));
					$data = array(
					'order_ref_id'=>$task_ref_id['task_ref_id'],
					'user_first_name'=>$user_info['user_first_name'],
					'user_email'=>$res['user_email'],
					'user_ref_id'=>$user_info['user_ref_id'],
					'to'=>User::getLoggedUserAttribute('user_email'),
					'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($record->getId())),
					'temp_num'=>19
					);
					$is_reg = $this->is_user_registered();				
					if($is_reg == true) { //check if user registered
						$this->sendEmail(array('user_email'=>$res['user_email'],'user_first_name'=>($user_info['user_first_name'] != '') ? $user_info['user_first_name']: $res['user_email'],'temp_num'=>18,'to'=>CONF_ADMIN_EMAIL_ID));
					}
					$this->sendEmail($data);		
				}
				$task_id = $record->getId();
			}
			
			$this->taskId = $task_id;
			
			return true;
		}
		
		static function sendEmail($data,$subject) {
			
			$db = &Syspage::getdb();
			
			$rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = ".$data['temp_num']." AND tpl_active='1'");
			$row_tpl = $db->fetch($rs);
			if(!empty($row_tpl['tpl_id'])){
				$subject	= ($subject == '')?$row_tpl['tpl_subject']:$subject;
				$body		= $row_tpl['tpl_body'];
				
				$company_logo_url = generateAbsoluteUrl('image', 'logo',array(CONF_EMAIL_TEMPLATE_LOGO),'/');
				
				$arr_replacements = array( 
				'{Company_Logo_Url}'=>$company_logo_url,
				'{website_name}'=>CONF_WEBSITE_NAME,
				/* 
					0054 [
					'{website_url}'=>CONF_SITE_URL,
					]
				*/
				'{website_url}'=>$_SERVER['SERVER_NAME'],
				/* 
					0054 [ 
					'{website_domain}'=>CONF_WEBSITE_DOMAIN,
					]
				*/
				'{website_domain}'=>$_SERVER['SERVER_NAME'],
				'{user_screen_name}'=>$data['user_screen_name'],
				'{user_email}'=>$data['user_email'],
				'{user_phone}'=>$data['user_phone'],
				'{login_password}'=>$data['login_password'],
				'{order_ref_id}'=>$data['order_ref_id'],
				'{user_ref_id}'=>$data['user_ref_id'],
				'{bid_user_name}'=>$data['bid_user_name'],
				'{bid_price}'=>$data['bid_price'],
				'{old_bid_price}'=>$data['old_bid_price'],
				'{login_page_link}'=>CONF_SITE_URL.generateUrl('user','signin'),
				'{test_page_link}'=>CONF_SITE_URL.generateUrl('test'),
				'{submit_sample_essay_link}'=>CONF_SITE_URL.generateUrl('sample'),
				'{amount}'=>$data['amount'],
				'{amount_load}'=>$data['amount_load'],
				'{amount_reserved}'=>$data['amount_reserved'],
				'{amount_paid}'=>$data['amount_paid'],
				'{amount_earned}'=>$data['amount_earned'],
				'{amount_to_receive}'=>$data['amount_to_receive'],
				'{order_page_link}'=>$data['order_page_link'],
				'{user_first_name}'=>$data['user_first_name'],
				'{transaction_details_link}'=>$data['transaction_details_link'],
				'{approval_date}'=>$data['approval_date'],
				'{request_date}'=>$data['request_date'],
				'{request_time}'=>$data['request_time'],
				'{user_request}'=>$data['user_request'],
				'{comments}'=>$data['comments'],
				'{status}'=>$data['status'],
				'{message}'=>$data['message'],
				'{deadline_extension}'=>$data['deadline_extension'],
				'{new_deadline}'=>$data['new_deadline'],
				'{writer_pitch}'=>$data['writer_pitch'],
				//'{contact_url}'=>$data['contact_url'],
				'{contact_url}'=>CONF_SITE_URL.generateUrl('cms','contact'),
				'{sample_essay_topic}'=>($data['sample_essay_topic'] == '')?CONF_SAMPLE_ESSAY_TOPIC:$data['sample_essay_topic'],
				'{reset_url}'=>$data['reset_url'],
				'{verification_url}'=>$data['verification_url'],
				'{current_date}'=>displayDate(date('D, d M Y H:i:s'), true, true, CONF_TIMEZONE),
				'{CONF_FACEBOOK_URL}'=>CONF_FACEBOOK,
				'{CONF_TWITTER_URL}'=>CONF_TWITTER,
				'{CONF_LINKEDIN_URL}'=>CONF_LINKEDIN,
				'{CONF_GOOGLEPLUS_URL}'=>CONF_GOOGLEPLUS,
				'{admin_username}'=>$data['admin_username'],
				'{admin_email}'=>$data['admin_email'],
				'{admin_password}'=>$data['admin_password'],
				'{admin_status}'=>$data['admin_status'],
				'{COPYRIGHT}'=> sprintf(CONF_FOOTER_COPYRIGHT,date("Y"), CONF_WEBSITE_NAME),
				);
				
				foreach ($arr_replacements as $key=>$val){
					$subject = str_replace($key, $val, $subject);
					$body=str_replace($key, $val, $body);
				}			
				
				if(!sendMail($data['to'], $subject, $body)) { 
				
					/*if(!sendMandrillMail($data['to'], $subject, $body)) {*/
					Message::addErrorMessage( Utilities::getLabel( 'L_Failed_to_sent_mail' ) );
					return false;
				}
			}
			return true;
		}
		
		public function addTaskInvitees($data) {
			$db = &Syspage::getdb();
			
			if (!User::isWriter($data['inv_writer_id'])) return false;
			
			$record = new TableRecord('tbl_task_invitees');
			
			$record->assignValues($data);
			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
			}
			
			return true;
		}
		
		public function updateInvitationStatus($task_id,$status_to_be) {
			$db = &Syspage::getdb();
			
			$record = new TableRecord('tbl_task_invitees');
			
			$record->setFldValue('inv_status', $status_to_be);
			
			if (!$record->update(array('smt'=>'inv_task_id = ? AND inv_writer_id = ?', 'vals'=>array($task_id, User::getLoggedUserAttribute('user_id'))))) {
				$this->error = $record->getError();
				return false;
			}
			
			return true;
		}
		
		public function addFile($data) {
			$record = new TableRecord('tbl_files');
			
			$record->assignValues($data);
			
			$record->setFldValue('file_uploaded_by', User::getLoggedUserAttribute('user_id'));
			$record->setFldValue('file_uploaded_on', 'mysql_func_NOW()', true);
			$record->setFldValue('file_size', $data['file_size']);
			if (!isset($data['file_class'])) $record->setFldValue('file_class', 1);
			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
				}else {
				if($data['cus_req_to_upload_again'] == 1) {
					$rec = new TableRecord('tbl_tasks');
					$rec->setFldValue('task_review_request_status',2);
					
					if(!$rec->update(array('smt'=>'task_id = ?', 'vals'=>array($data['file_task_id'])))){
						$this->error = $rec->getError();
						return false;
					}
				}
			}
			
			return $record->getId();
		}
		
		public function updateProjectFile($data) {
			$db = &Syspage::getdb();
			
			$record = new TableRecord('tbl_tasks');
			
			$record->setFldValue('task_file_id', $data['file_id']);
			
			if (!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($data['task_id'])))) {
				$this->error = $record->getError();
				return false;
				}else{
				$arr_task = $this->getTaskDetailsById($data['task_id']);
				$this->sendEmail(array('temp_num'=>38,'to'=>$arr_task['customer_email'],'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
				$this->sendEmail(array('temp_num'=>38,'to'=>CONF_ADMIN_EMAIL_ID,'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
				$this->sendEmail(array('temp_num'=>39,'to'=>$arr_task['writer_email'],'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
			}
			
			return true;
		}
		
		public function addMilestone($data) {
			$db = &Syspage::getdb();
			
			$srch = new SearchBase('tbl_milestones');
			
			$srch->addCondition('mile_task_id', '=', $data['mile_task_id']);
			
			$rs = $srch->getResultSet();
			
			$mile_data = $db->fetch($rs);
			
			$record = new TableRecord('tbl_milestones');
			
			$record->assignValues($data);		
			$record->setFldValue('mile_updated_on', 'mysql_func_NOW()', true);        
			$arr_task = $this->getTaskDetailsById($data['mile_task_id']);
			if ($mile_data) { 
				if (!$record->update(array('smt'=>'mile_task_id = ? AND mile_page = ?', 'vals'=>array($data['mile_task_id'],$data['mile_page'])))) {
					$this->error = $record->getError();
					return false;
					}else {
					$this->sendEmail(array('temp_num'=>37,'to'=>$arr_task['customer_email'],'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
					$this->sendEmail(array('temp_num'=>37,'to'=>CONF_ADMIN_EMAIL_ID,'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
				}
			}
			else {
				$record->setFldValue('mile_status', 0);
				
				if (!$record->addNew()) {
					$this->error = $record->getError();
					return false;
					}else {
					$this->sendEmail(array('temp_num'=>37,'to'=>$arr_task['customer_email'],'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
					$this->sendEmail(array('temp_num'=>37,'to'=>CONF_ADMIN_EMAIL_ID,'user_ref_id'=>$arr_task['customer_ref_id'],'user_email'=>$arr_task['customer_email'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));
				}
			}
			
			return true;
		}
		
		public function getMilestones($task_id) {
			$db = &Syspage::getdb();
			
			$task_id = intval($task_id);
			if ($task_id < 1) return false;
			
			$srch = new SearchBase('tbl_milestones');
			
			$srch->addCondition('mile_task_id', '=', $task_id);
			
			$rs = $srch->getResultSet();
			
			return $db->fetch_all($rs);
		}
		
		public function getMilestoneById($mile_id) {
			$db = &Syspage::getdb();
			
			$mile_id = intval($mile_id);
			if ($mile_id < 1) return false;
			
			$srch = new SearchBase('tbl_milestones', 'm');
			
			$srch->joinTable('tbl_tasks', 'INNER JOIN', 'm.mile_task_id=t.task_id', 't');
			$srch->joinTable('tbl_bids', 'INNER JOIN', 'm.mile_task_id=b.bid_task_id', 'b');
			
			$srch->addCondition('mile_id', '=', $mile_id);
			
			$srch->addMultipleFields(array('m.*', 't.task_id', 't.task_ref_id', 't.task_topic', 't.task_user_id AS customer_id', 't.task_pages', 't.task_status', 'b.bid_user_id AS writer_id', 'b.bid_price'));
			
			$rs = $srch->getResultSet();
			
			if (!$row = $db->fetch($rs)) return false;
			
			return $row;
		}
		
		public function getTaskDetailsById($task_id) {
			$db = &Syspage::getdb();
			
			$task_id = intval($task_id);
			if ($task_id < 1) return false;
			
			$srch = new SearchBase('tbl_tasks', 't');
			
			$srch->joinTable('tbl_bids', 'LEFT OUTER JOIN', 'b.bid_task_id=t.task_id AND b.bid_status=1', 'b');
			$srch->joinTable('tbl_task_invitees', 'LEFT OUTER JOIN', 'i.inv_task_id=t.task_id', 'i');
			$srch->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 't.task_paptype_id=p.paptype_id', 'p');
			$srch->joinTable('tbl_disciplines','LEFT OUTER JOIN','d.discipline_id = t.task_discipline_id','d');
			$srch->joinTable('tbl_citation_styles','LEFT OUTER JOIN','c.citstyle_id = t.task_citstyle_id','c');
			$srch->joinTable('tbl_users','LEFT OUTER JOIN','u1.user_id = t.task_user_id','u1');
			$srch->joinTable('tbl_users','LEFT OUTER JOIN','u2.user_id = b.bid_user_id','u2');
			
			$srch->addCondition('t.task_id', '=', $task_id);
			
			$srch->addMultipleFields(array('t.*','b.bid_id', 'i.inv_writer_id', 'i.inv_status', 'p.paptype_name', 'c.citstyle_name', 'd.discipline_name', 'u1.user_screen_name', 'u2.user_screen_name AS writer','u1.user_email as customer_email','u1.user_first_name as customer_first_name','u1.user_ref_id as customer_ref_id','u2.user_email as writer_email'));
			
			$rs = $srch->getResultSet();
			if (!$row = $db->fetch($rs)) return false;
			
			if($row['task_service_type'] > 0)	{
				$srch_serv = new SearchBase('tbl_service_fields');
				$srch_serv->addOrder('service_name');
				$srch_serv->addMultipleFields(array('service_id', 'service_name'));
				//$srch_serv->addCondition('service_active','=',1);
				$srch_serv->addCondition('service_id','=',$row['task_service_type']);
				
				$rs_serv = $srch_serv->getResultSet();
				$serv = $db->fetch($rs_serv);
				//echo $row['task_service_type'];
				//echo "<pre>".print_r($serv,true)."</pre>";exit;
				if($row['task_service_type'] != $serv['service_id']){
					$row['task_service_type'] = 'Service is not available!';
					return $row;
				}
				$row['task_service_type'] = $serv['service_name'];
			}
			return $row;
		}
		function search($criteria){
			
			if (!Admin::isLogged()) die( Utilities::getLabel( 'L_Unauthorized_Access' ) );
			$post = Syspage::getPostedVar();
			$db = &Syspage::getdb();
			
			$srch = new SearchBase('tbl_tasks','t');
			$srch->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id=t.task_user_id','u');		
			
			#$pagesize = 20;
			$pagesize = CONF_PAGINATION_LIMIT;
			
			$page = intval($post['page']);
			if (!($page > 0)) $page = 1;
			
			$srch->setPageNumber($page);
			$srch->setPageSize($pagesize);
			
			$srch->addOrder('t.task_id','DESC');
			
			$srch->addMultipleFields(array('t.task_id','t.task_topic','t.task_ref_id','t.task_status','u.user_screen_name','t.task_due_date','t.task_posted_on'));
			
			foreach($criteria as $key=>$val) {
				
				if(strval($val)=='') continue;
				
				switch($key) {			
					case 'task_user_id':
					$srch->addCondition('task_user_id', '=', intval($val));
					break;
					case 'task_writer_id':
					$srch->addCondition('task_writer_id', '=', intval($val));
					break;
					
				}				
			}
			
			return $srch;
		}
		
		public function getOrderDetails($id) {
			$db = &Syspage::getdb();
			
			$srch = new SearchBase('tbl_tasks','t');
			
			$srch->joinTable('tbl_users','LEFT OUTER JOIN','u1.user_id = t.task_user_id','u1');
			
			$srch->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
			$srch->joinTable('tbl_disciplines','LEFT OUTER JOIN','d.discipline_id = t.task_discipline_id','d');
			$srch->joinTable('tbl_citation_styles','LEFT OUTER JOIN','c.citstyle_id = t.task_citstyle_id','c');
			
			$srch->joinTable('tbl_bids','LEFT OUTER JOIN','b.bid_task_id = t.task_id','b');
			$srch->joinTable('tbl_users','LEFT OUTER JOIN','u2.user_id = b.bid_user_id','u2');
			
			$srch->addMultipleFields(array('t.task_id','task_ref_id','t.task_user_id','t.task_status','p.paptype_name','t.task_topic','t.task_pages','t.task_words_per_page','t.task_due_date','t.task_completed_on','t.task_service_type','t.task_instructions','t.autodebit_on','c.citstyle_name','t.task_posted_on','t.task_file_id','t.task_finished_from_writer','t.task_review_request_status','u1.user_screen_name','u1.user_first_name','u1.user_email as customer_email','d.discipline_name','b.bid_id','b.bid_user_id', 'b.bid_price','b.bid_status','u2.user_screen_name AS writer','u2.user_email as writer_email','b.bid_preview','t.task_cancel_request','b.cus_req_to_upload_again'));
			
			$srch->addCondition('t.task_id', '=', $id);		
			$srch->addCondition('t.task_status', 'IN', array(2,3,4));		
			$srch->addCondition('b.bid_status', 'IN', array(1,3));
			
			$rs = $srch->getResultSet();
			
			if (!$row = $db->fetch($rs)) return false;
			if($row['task_service_type'] > 0)	{
				$srch_serv = new SearchBase('tbl_service_fields');
				$srch_serv->addOrder('service_name');
				$srch_serv->addMultipleFields(array('service_id', 'service_name'));
				//$srch_serv->addCondition('service_active','=',1);
				$srch_serv->addCondition('service_id','=',$row['task_service_type']);
				
				$rs_serv = $srch_serv->getResultSet();
				$serv = $db->fetch($rs_serv);
				//echo $row['task_service_type'];
				//echo "<pre>".print_r($serv,true)."</pre>";exit;
				if($row['task_service_type'] != $serv['service_id']){
					$row['task_service_type'] = 'Service is not available!';
					return $row;
				}
				$row['task_service_type'] = $serv['service_name'];
			}
			
			return $row;
		}
		
		public function getOrderFiles($task_id, $file_class=0) {
			$db = &Syspage::getdb();
			
			$file_class = intval($file_class);
			
			$arr_task = $this->getTaskDetailsById($task_id);
			if ($arr_task === false) return false;
			
			$srch = new SearchBase('tbl_files');
			
			$srch->addCondition('file_task_id', '=', $task_id);
			
			if ($file_class > 0) $srch->addCondition('file_class', '=', $file_class);
			
			$srch->addOrder('file_uploaded_on');
			
			$srch->addMultipleFields(array('file_id', 'file_download_name','file_download_status','file_task_id','file_size'));
			
			$rs = $srch->getResultSet();
			
			return $db->fetch_all($rs);
		}
		
		public function assignWriter($data) {
			$db = &Syspage::getdb();
			
			$srch = new SearchBase('tbl_users');
			
			$srch->addCondition('user_id', '=', $data['writer_id']);
			
			$srch->addFld(array('user_id','user_ref_id','user_email','user_screen_name','user_first_name'));
			
			$rs = $srch->getResultSet();
			
			if (!$row = $db->fetch($rs)) return false;	
			
			if (!$this->wallet->addTransaction(array(
			'wtrx_amount'=>'-' . $data['task_amount'],
			'wtrx_mode'=>0,
			'wtrx_task_id'=>$data['task_id'],
			'wtrx_user_id'=>User::getLoggedUserAttribute('user_id'),
			'wtrx_reference_trx_id'=>0,
			'wtrx_withdrawal_request_id'=>0,
			'wtrx_cancelled'=>0
			))) {
				$this->error = $this->wallet->getError();
				return false;
			}
			
			$param = array(
			'me_cust_trans'=>CONF_SERVICE_CHARGE,
			'me_writer_recv'=>0,
			'me_task_id'=>$data['task_id'],
			'system_earned'=>CONF_SERVICE_CHARGE
			);
			
			$this->wallet->add_earned_money($param);
			
			$this->wallet->reserved_transaction(array(
			'res_user_id'=>User::getLoggedUserAttribute('user_id'),
			'res_task_id'=>$data['task_id'],
			'res_amount'=>($data['task_amount']- CONF_SERVICE_CHARGE)
			));		
			
			if (!$db->update_from_array('tbl_bids', array('bid_status'=>1), array('smt'=>'bid_id = ?', 'vals'=>array($data['bid_id'])))) {
				$this->error = $db->getError();
				return false;
			}
			
			if (!$db->update_from_array('tbl_tasks', array('task_writer_id'=>$data['writer_id'], 'task_status'=>2), array('smt'=>'task_id = ?', 'vals'=>array($data['task_id'])))) {
				$this->error = $db->getError();
				return false;
				}else {
				$this->sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'temp_num'=>33,'amount_reserved'=>CONF_CURRENCY.($data['task_amount'] - CONF_SERVICE_CHARGE),'amount_paid'=>CONF_CURRENCY.CONF_SERVICE_CHARGE,'user_screen_name'=>$row['user_screen_name'],'user_first_name'=>User::getLoggedUserAttribute('user_screen_name'),'order_ref_id'=>$data['task_ref_id'],'user_email'=>User::getLoggedUserAttribute('user_email'),'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_process',array($data['task_id']))));
			}
			
			if (!$db->update_from_array('tbl_bids', array('bid_status'=>2), array('smt'=>'bid_task_id = ? and bid_id != ?', 'vals'=>array($data['task_id'],$data['bid_id'])))) {
				$this->error = $db->getError();
				return false;
				}else {
				$this->sendEmail(array(
				'to'=>$row['user_email'],
				'temp_num'=>34,
				'user_screen_name'=>$row['user_screen_name'],
				'order_ref_id'=>$data['task_ref_id'],
				'new_deadline'=>$data['task_due_date'],
				'bid_price'=>CONF_CURRENCY.($data['task_amount'] - CONF_SERVICE_CHARGE),
				'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_process',array($data['task_id']))
				));
				
				$arr_bids = $this->bid->getOrderBids($data['task_id']);
				foreach($arr_bids as $key=>$value){
					if($value['bid_user_id'] != $row['user_id']) {
						$bid_user[] = $value;
					}
				}
				foreach($bid_user as $val) {
					$this->sendEmail(array(
					'to'=>$val['user_email'],
					'temp_num'=>35,
					'user_screen_name'=>$val['writer'],
					'user_first_name'=>User::getLoggedUserAttribute('user_screen_name'),
					'order_ref_id'=>$data['task_ref_id']
					));
				}
			}		
			return true;
		}
		
		public function getCustomerOrders($filter,$page) {
			$db = &Syspage::getdb();
			
			global $order_status;
			
			/* Mark  expired orders as cancelled */
			$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)"); 		
			
			$page = intval($page);
			if ($page < 1) $page = 1;
			
			#$pagesize = 10;
			$pagesize = CONF_PAGINATION_LIMIT;
			
			$rows = array();
			
			$srch = new SearchBase('tbl_tasks', 't');
			
			$srch->joinTable('tbl_bids', 'LEFT OUTER JOIN', 't.task_id=b1.bid_task_id AND b1.bid_status != 3', 'b1');
			$srch->joinTable('(SELECT bid_id, bid_task_id, bid_user_id, bid_price FROM tbl_bids WHERE bid_status = 1)', 'LEFT OUTER JOIN', 't.task_id=b2.bid_task_id', 'b2');
			$srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'b2.bid_user_id=u.user_id', 'u');
			$srch->joinTable('tbl_disciplines', 'LEFT OUTER JOIN', 't.task_discipline_id=d.discipline_id', 'd');
			$srch->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 't.task_paptype_id=p.paptype_id', 'p');
			$srch->joinTable('tbl_wallet_transactions', 'LEFT OUTER JOIN', 't.task_id=w.wtrx_task_id AND t.task_user_id=w.wtrx_user_id', 'w');
			
			$srch->addCondition('t.task_user_id', '=', User::getLoggedUserAttribute('user_id'));
			//$srch->addCondition('t.task_type','=',0);
			
			switch (strtolower($filter)) {
				case 'mine':
				$srch->addCondition('t.task_type','=',0);
				$cnd = $srch->addCondition('t.task_status', '=', 0);
				$cnd->attachCondition('t.task_status', '=', 1);
				
				break;
				case 'ongoing':
				$srch->addCondition('t.task_status', '=', 2);
				break;
				case 'completed':
				$srch->addCondition('t.task_status', '=', 3);
				break;
				case 'cancelled':
				$srch->addCondition('t.task_status', '=', 4);
				break;
			}
			
			$srch->addOrder('t.task_posted_on', 'desc');
			$srch->addGroupBy('t.task_id'); 
			
			$srch->addMultipleFields(array('t.task_id', 't.task_ref_id', 't.task_topic', 't.task_user_id', 't.task_writer_id', 't.task_pages', 't.task_words_per_page', 't.task_status', 't.task_pages', 'IFNULL(d.discipline_name, "-") AS discipline_name', 't.task_due_date', 't.task_completed_on', 'COUNT(b1.bid_id) AS total_bids', 'IFNULL(b2.bid_id,0) AS bid_id', 'b2.bid_price', 'u.user_screen_name AS writer', 'IFNULL(p.paptype_name, "-") AS paper_type', 'ROUND(IFNULL(SUM(w.wtrx_amount),0),2) AS amount_paid'));
			
			$srch->setPageNumber($page);
			$srch->setPageSize($pagesize);
			
			$rs = $srch->getResultSet();
			
			$orders = $db->fetch_all($rs);
			
			foreach ($orders as $key=>$arr) {			
				$orders[$key]['task_status']		= $arr['task_status'];
				#$orders[$key]['task_due_date']		= displayDate($arr['task_due_date'], true, true, CONF_TIMEZONE);
				$orders[$key]['task_due_date']		= $arr['task_due_date'];
				$orders[$key]['task_completed_on']	= displayDate($arr['task_completed_on'], true, true, CONF_TIMEZONE);
				#$orders[$key]['time_left']			= html_entity_decode(time_diff(displayDate($arr['task_due_date'], true)));
				$orders[$key]['time_left']			= html_entity_decode(time_diff($arr['task_due_date']));
				$orders[$key]['order_amount']		= number_format(getOrderAmount($arr['bid_price'],$arr['task_pages']),2);
				$orders[$key]['amount_paid']		= $this->wallet->getTaskAmountPaidByCustomer($arr['task_id']);
			}
			
			$rows['orders'] = $orders;
			$rows['pages']	= $srch->pages();
			$rows['page']	= $page;
			
			return $rows;
		}
		
		public function getPrivateOrders($page) {
			$db = &Syspage::getdb();
			
			global $order_status;
			
			$page = intval($page);
			if ($page < 1) $page = 1;
			
			#$pagesize = 10;
			$pagesize = CONF_PAGINATION_LIMIT;
			//$pagesize = 1;
			$srch = new SearchBase('tbl_tasks', 't');
			
			$srch->joinTable('tbl_task_invitees', 'INNER JOIN', 't.task_id=i.inv_task_id', 'i');
			$srch->joinTable('tbl_bids', 'LEFT OUTER JOIN', 't.task_id=b1.bid_task_id AND b1.bid_status = 0', 'b1');
			$srch->joinTable('(SELECT bid_id, bid_task_id, bid_user_id, bid_price FROM tbl_bids WHERE bid_status = 1)', 'LEFT OUTER JOIN', 't.task_id=b2.bid_task_id', 'b2');
			$srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'i.inv_writer_id=u.user_id', 'u');
			$srch->joinTable('tbl_disciplines', 'LEFT OUTER JOIN', 't.task_discipline_id=d.discipline_id', 'd');
			$srch->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 't.task_paptype_id=p.paptype_id', 'p');
			
			$srch->addCondition('t.task_user_id', '=', User::getLoggedUserAttribute('user_id'));
			$srch->addCondition('t.task_type','=',1);
			//$srch->addCondition('t.task_type','!=',4);
			
			$cnd = $srch->addCondition('t.task_status', '=', 1);
			$cnd->attachCondition('t.task_status', '=', 0,'or'); 
			/* $cnd = $srch->addCondition('i.inv_status', '!=', 1);
			$cnd->attachCondition('t.task_status', '=', 1,'AND');*/
			
			$srch->addMultipleFields(array('t.task_id', 't.task_ref_id', 't.task_topic', 't.task_pages', 't.task_words_per_page', 't.task_status', 't.task_pages', 'd.discipline_name','t.task_due_date', 'COUNT(b1.bid_id) AS total_bids', /* 'IFNULL(b2.bid_id,0) AS bid_id', 'b2.bid_price', */ 'u.user_screen_name AS writer', 'IFNULL(p.paptype_name, "-") AS paper_type', 'i.inv_status'));
			
			$srch->addOrder('t.task_posted_on', 'desc');
			$srch->addGroupBy('t.task_id');
			
			$srch->setPageNumber($page);
			$srch->setPageSize($pagesize);
			//echo $srch->getQuery();exit;
			$rs = $srch->getResultSet();
			
			$orders = $db->fetch_all($rs);
			
			foreach ($orders as $key=>$arr) {
				//$orders[$key]['task_status']	= $order_status[$arr['task_status']];
				#$orders[$key]['task_due_date']	= displayDate($arr['task_due_date'], true);
				$orders[$key]['task_due_date']	= $arr['task_due_date'];
				$orders[$key]['time_left']		= html_entity_decode(time_diff($arr['task_due_date']));
			}
			
			$rows['orders'] = $orders;
			$rows['pages']	= $srch->pages();
			$rows['page']	= $page;
			
			return $rows;
		}
		
		/* public function ShowOrders($page) { */
		public function countAvailableOrders() { 
			$db = &Syspage::getdb();		
			//Mark expired orders as cancelled
			
			$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)");		
			
			/* get tasks on which a writer has already made a bid */
			$srch_1 = new SearchBase('tbl_bids');
			
			$srch_1->addCondition('bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
			$srch_1->addCondition('bid_status', '!=', 3);
			
			$srch_1->addFld('bid_task_id AS id');
			
			$srch_1->doNotCalculateRecords();
			$srch_1->doNotLimitRecords();
			
			$qry_1 = $srch_1->getQuery();
			
			$srch_2 = new SearchBase('tbl_task_invitees');
			
			$srch_2->addCondition('inv_writer_id', '=', User::getLoggedUserAttribute('user_id'));
			$srch_2->addCondition('inv_status', '!=', 0);
			
			$srch_2->addFld('inv_task_id AS id');
			
			$srch_2->doNotCalculateRecords();
			$srch_2->doNotLimitRecords();
			
			$qry_2 = $srch_2->getQuery();
			
			$sql = $db->query("(" . $qry_1 . ") UNION (" . $qry_2 . ")");
			
			$arr_task_ids = $db->fetch_all_assoc($sql);
			$arr_task_ids = array_keys($arr_task_ids);
			/* ### */
			
			$records = new SearchBase('tbl_tasks', 't');
			$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
			$records->joinTable('tbl_users', 'INNER JOIN', 't.task_user_id=u.user_id', 'u');
			
			if(!empty($arr_task_ids)) {
				$records->addCondition('t.task_id','NOT IN',$arr_task_ids); 
			}
			
			$records->addCondition('t.task_status','=',1);
			$records->addCondition('t.task_type','=',0);
			
			$records->addMultipleFields(array('t.task_id'));
			
			/* $records->setPageNumber($page);
				$records->setPageSize($pagesize);
				$records->addOrder('t.task_posted_on', 'desc');
			*/
			
			$rs = $records->getResultSet();
			
			$rows['count'] = $records->recordCount();
			
			return $rows['count'];
		}
		
		public function OrdersListing($data,$page) {
			$db = &Syspage::getdb();
			//die(convertToJson($data));
			$task_due_date = array();
			$task_due_date = explode('_',$data['filter_date']);
			
			switch ($task_due_date[1]) {
				case 'h' :
				$data['filter_date'] = strtotime('+'.$task_due_date[0].' hours',time());
				$data['filter_date'] = date('M d, Y H:i',$data['filter_date']);
				break;
				case 'n' :
				$data['filter_date'] = date('M d, Y '.$task_due_date[0].':00',strtotime('tomorrow'));
				break;
				case 'd' :
				$data['filter_date'] = strtotime('+'.$task_due_date[0].' days',time());
				$data['filter_date'] = date('M d, Y H:i',$data['filter_date']);
				break;
				
			} 
			//Mark expired orders as cancelled
			//$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)");
			
			#$pagesize = 10;
			$pagesize = CONF_PAGINATION_LIMIT;
			$page = intval($page);
			if(!$page){
				$page = 1;
			}
			/* get tasks on which a writer has already made a bid */
			$srch_1 = new SearchBase('tbl_bids');
			
			$srch_1->addCondition('bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
			$srch_1->addCondition('bid_status', '!=', 3);
			
			$srch_1->addFld('bid_task_id AS id');
			
			$srch_1->doNotCalculateRecords();
			$srch_1->doNotLimitRecords();
			
			$qry_1 = $srch_1->getQuery();
			
			$srch_2 = new SearchBase('tbl_task_invitees');
			
			$srch_2->addCondition('inv_writer_id', '=', User::getLoggedUserAttribute('user_id'));
			$srch_2->addCondition('inv_status', '!=', 0);
			
			$srch_2->addFld('inv_task_id AS id');
			
			$srch_2->doNotCalculateRecords();
			$srch_2->doNotLimitRecords();
			
			$qry_2 = $srch_2->getQuery();
			
			$sql = $db->query("(" . $qry_1 . ") UNION (" . $qry_2 . ")");
			
			$arr_task_ids = $db->fetch_all_assoc($sql);
			$arr_task_ids = array_keys($arr_task_ids);
			/* ### */
			
			$records = new SearchBase('tbl_tasks', 't');
			$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
			$records->joinTable('tbl_users', 'INNER JOIN', 't.task_user_id=u.user_id', 'u');
			$records->joinTable('tbl_bids', 'LEFT OUTER JOIN', 't.task_id=b1.bid_task_id', 'b1');
			$records->joinTable('(SELECT count(*) as num_bid,bid_task_id from tbl_bids group by bid_task_id)','LEFT OUTER JOIN','t.task_id = b.bid_task_id','b');
			
			if(!empty($arr_task_ids)) {
				$records->addCondition('t.task_id','NOT IN',$arr_task_ids); 
			}
			
			$records->addCondition('t.task_status','=',1);
			$records->addCondition('t.task_type','=',0);
			if(isset($data['paptype_name']) && !empty($data['paptype_name']))$records->addCondition('p.paptype_id','=',$data['paptype_name']);
			#if(isset($data['paptype_name']))$records->addCondition('p.paptype_id','LIKE','%'.$data['paptype_name'].'%');
				if(isset($data['filter_page']) && $data['filter_page'] > 0)$records->addCondition('t.task_pages','=',$data['filter_page']);
				$date = date('Y-m-d H:i',strtotime($data['filter_date'],time()));
				//die($date);
				//echo "<pre>".print_r($data,true)."</pre>";exit;
				if(date('Y-m-d H:s',strtotime($data['filter_date'])) > date("Y-m-d H:i")) $records->addCondition('t.task_due_date','<=',$date);
				
				$records->addMultipleFields(array('t.task_id','task_ref_id','t.task_user_id','t.task_budget','u.user_screen_name','u.user_first_name','u.user_last_name','t.task_status','p.paptype_name','t.task_topic','t.task_pages','t.task_due_date','t.task_service_type','t.task_citstyle_id','t.task_posted_on','b1.bid_task_id','b.num_bid'));
				
				$records->setPageNumber($page);
				$records->setPageSize($pagesize);
				
				$records->addOrder('t.task_posted_on', 'desc');
				$records->addGroupBy('t.task_id');
				$rs = $records->getResultSet();
				//echo $records->getQuery();
				$rows['orders'] = $db->fetch_all($rs);
				$rows['pages']	= $records->pages();
				$rows['page']	= $page;
				$rows['total_orders'] = count($rows['orders']);
				return $rows;
			}
			
			public function getLatestOrders($data,$page) {
				
				$db = &Syspage::getdb();
				//die(convertToJson($data));
				
				//Mark expired orders as cancelled
				//$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)");
				
				#$pagesize = 10;
				$pagesize = CONF_PAGINATION_LIMIT;  
				$page = intval($page);
				if(!$page){
					$page = 1;
				}	
				$filter = $data['filter'];
				$filter_val = intval($data['filter_value']);  
				/* ### */
				
				$records = new SearchBase('tbl_tasks', 't');
				$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
				$records->joinTable('tbl_users', 'INNER JOIN', 't.task_user_id=u.user_id', 'u');
				$records->joinTable('(SELECT count(*) as num_bid,bid_task_id from tbl_bids group by bid_task_id)','LEFT OUTER JOIN','t.task_id = b.bid_task_id','b');
				
				$records->addCondition('t.task_status','=',1);
				$records->addCondition('t.task_type','=',0);
				//if(isset($data['paptype_name']))$records->addCondition('p.paptype_id','LIKE','%'.$data['paptype_name'].'%');
				$date = date('Y-m-d H:i',strtotime($data['filter_date'],time()));
				if(isset($filter) && $filter != '') {
					switch ($filter) {
						case 'task_pages' :
						if($filter_val <= 5) {
							$records->addCondition('t.task_pages', '<=', $filter_val);
							}elseif($filter_val > 5 && $filter_val < 21){
							$records->addDirectCondition('t.task_pages between 6 AND 20');						
							}else {
							$records->addCondition('t.task_pages', '>=', $filter_val);	
						}
						$records->addOrder('t.task_pages', 'desc');
						break;
						case 'pep_type' :
						if($filter_val > 0) {
							$records->addCondition('t.task_paptype_id', '=', $filter_val);
						}
						break;
						case 'deadline' :
						if($filter_val >= 7) {
							$records->addDirectCondition('t.task_due_date between NOW()+ INTERVAL '.($filter_val - 7).' DAY AND NOW()+ INTERVAL '.$filter_val.' DAY');
							}else {
							$records->addDirectCondition('t.task_due_date between NOW() AND NOW()+ INTERVAL '.$filter_val.' DAY');
						}
						$records->addOrder('t.task_due_date', 'ASC');
						break;
					}
				}
				
				
				$records->addMultipleFields(array('t.task_id','task_ref_id','t.task_user_id','t.task_budget','u.user_screen_name','u.user_first_name','u.user_last_name','t.task_status','p.paptype_name','t.task_topic','t.task_pages','t.task_due_date','t.task_service_type','t.task_citstyle_id','t.task_posted_on','b.bid_task_id','b.num_bid'));
				
				$records->setPageNumber($page);
				$records->setPageSize($pagesize);
				
				$records->addOrder('t.task_posted_on', 'desc');
				$records->addGroupBy('t.task_id');
				$rs = $records->getResultSet();
				//echo $records->getQuery();
				$rows['orders'] = $db->fetch_all($rs);
				$rows['pages']	= $records->pages();
				$rows['page']	= $page;
				$rows['total_orders'] = count($rows['orders']);
				return $rows;
			}
			
			public function getWriterInvitations($page) {
				$db = &Syspage::getdb();
				$post = Syspage::getPostedVar();
				
				global $invitation_status;
				
				$rows = array();
				
				$page = intval($page);
				if ($page < 1) $page = 1;
				
				#$pagesize = 10;
				$pagesize = CONF_PAGINATION_LIMIT;
				
				$srch = new SearchBase('tbl_tasks', 't');
				
				$srch->joinTable('tbl_task_invitees', 'INNER JOIN', 't.task_id=i.inv_task_id', 'i');
				$srch->joinTable('tbl_users', 'INNER JOIN', 't.task_user_id=u.user_id', 'u');
				$srch->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 'p.paptype_id=t.task_paptype_id', 'p');
				
				$srch->addCondition('t.task_status', '=', 1);
				//$srch->addCondition('t.task_type', '=', 1);
				$srch->addCondition('i.inv_writer_id', '=', User::getLoggedUserAttribute('user_id'));
				$srch->addCondition('i.inv_status', '!=', 1);
				/* echo $srch->getQuery();
				die; */
				$srch->addMultipleFields(array('t.task_id', 't.task_ref_id', 't.task_user_id', 't.task_topic', 't.task_pages', 't.task_words_per_page', 't.task_due_date', 'u.user_first_name AS customer','u.user_first_name', 'p.paptype_name', 'i.inv_status'));
				
				$srch->addOrder('t.task_posted_on', 'desc');
				
				$srch->setPageNumber($page);
				$srch->setPageSize($pagesize);
				
				$rs = $srch->getResultSet();
				
				$orders = $db->fetch_all($rs);
				
				foreach ($orders as $key=>$arr) {
					//$orders[$key]['inv_status']		= $invitation_status[$arr['inv_status']];
					#$orders[$key]['task_due_date']	= displayDate($arr['task_due_date'], true);
					$orders[$key]['task_due_date']	= $arr['task_due_date'];
					$orders[$key]['time_left']		= html_entity_decode(time_diff($arr['task_due_date']));
				}
				
				$rows['orders']	= $orders;
				$rows['page']	= $page;
				$rows['pages']	= $srch->pages();
				
				return $rows;
			}
			
			public function changeOrderStatus($task_id,$status_to_be,$customer_id) 
			{
				if(!isset($customer_id) || empty($customer_id)){
					$customer_id=User::getLoggedUserAttribute('user_id');
				}
				
				global $order_status;
				
				$arr_status = array_keys($order_status);
				array_shift($arr_status);
				
				$db = &Syspage::getdb();
				
				$task_id 		= intval($task_id);
				$status_to_be 	= intval($status_to_be);
				
				if (User::isWriter()) {
					$this->error =  Utilities::getLabel( 'E_Invalid_request' ) ;
					return false;
				}
				
				if ($task_id < 1 || !in_array($status_to_be,$arr_status)) {
					$this->error =  Utilities::getLabel( 'E_Invalid_request' ) ;
					return false;
				}
				
				$arr_task = $this->getTaskDetailsById($task_id);
				
				if ($arr_task === false || $arr_task['task_user_id'] != $customer_id) {
					$this->error =  Utilities::getLabel( 'E_Invalid_request' ) ;
					return false;
				}
				
				/* Update status */
				$record = new TableRecord('tbl_tasks');
				$reserved_amount = $this->wallet->liabilities($customer_id,$tid);
				
				$record->setFldValue('task_status', $status_to_be);
				if ($status_to_be == 3) $record->setFldValue('task_completed_on', 'mysql_func_NOW()', true);
				
				if (!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
					$this->error = $record->getError();
					return false;
				}
				
				
				$rows = $this->wallet->getReservedAmount($task_id);
				
				if($rows['res_amount']!=0){			
					
					$arr_bid = $this->bid->getAssignedBidDetails($task_id);
					//echo "<pre>".print_r($arr_bid,true)."</pre>";exit;
					if($arr_task['task_user_id']!=$customer_id) die(convertToJson('Invalid Request!'));
					
					//--------------------------------------------------------------
					
					$writer_amount = getAmountPayableToWriter($arr_bid['bid_price']);		//amount payable to writer
					
					if (!$this->wallet->addTransaction(array('wtrx_amount'=>$writer_amount, 'wtrx_mode'=>0, 'wtrx_task_id'=>$task_id, 'wtrx_user_id'=>$arr_bid['bid_user_id'], 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
						Message::addErrorMessage($this->wallet->getError());
						redirectUser(generateUrl('task','order_process',array($task_id)));
						}else {
						$this->sendEmail(array('to'=>$arr_task['writer_email'],'temp_num'=>42,'user_first_name'=>$arr_task['customer_first_name'],'amount_paid'=>CONF_CURRENCY.$writer_amount,'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id']));				
						
						$this->sendEmail(array('to'=>$arr_task['writer_email'],'temp_num'=>54,'user_screen_name'=>$arr_task['writer'],'user_first_name'=>$arr_task['customer_first_name'],'order_ref_id'=>$arr_task['task_ref_id'],'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_process',array($arr_task['task_id']))));				
					}
					
					$param = array('me_cust_trans'=>$arr_bid['bid_price'],'me_writer_recv'=>$writer_amount,'me_task_id'=>$task_id,'system_earned'=>($arr_bid['bid_price']-$writer_amount));
					
					$this->wallet->add_earned_money($param);
					
					$this->wallet->update_reserve_amount(array('res_user_id'=>$arr_task['task_user_id'],'res_task_id'=>$task_id));
					
					$this->sendEmail(array('to'=>$arr_task['customer_email'],'temp_num'=>43,'user_first_name'=>$arr_task['customer_first_name'],'user_ref_id'=>$arr_task['customer_ref_id'],'amount_paid'=>CONF_CURRENCY.$arr_bid['bid_price'],'user_screen_name'=>$arr_task['writer'],'order_ref_id'=>$arr_task['task_ref_id'],'user_email'=>$arr_task['customer_email'],'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_process',array($arr_task['task_id']))));	
					
					$this->sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>44,'user_ref_id'=>$arr_task['customer_ref_id'],'amount_paid'=>CONF_CURRENCY.$writer_amount,'amount_earned'=>CONF_CURRENCY.($arr_bid['bid_price']-$writer_amount),'user_screen_name'=>$arr_task['writer'],'user_first_name'=>$arr_task['customer_first_name'],'user_email'=>$arr_task['customer_email'],'order_ref_id'=>$arr_task['task_ref_id']));				
				}
				
				
				/* if (!$this->wallet->addTransaction(array('wtrx_amount'=>'-' . $reserved_amount, 'wtrx_mode'=>2, 'wtrx_task_id'=>$task_id, 'wtrx_user_id'=>User::getLoggedUserAttribute('user_id'), 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
					$this->error = $this->wallet->getError();
					return false;
				} */
				
				/* $rec = new TableRecord('tbl_money_earned');		
					$rec->setFldValue('me_cust_trans',$reserved_amount);		
					$rec->setFldValue('me_writer_recv',0);
					$rec->setFldValue('me_task_id',$task_id);
					$rec->setFldValue('system_earned',$reserved_amount);
					$rec->setFldValue('me_trans_date','mysql_func_NOW()',true);
					if (!$rec->addNew()) {
					$this->error = $record->getError();
					return false;
				} */
				
				return true;
			}
			
			public static function getnumbids($id) {
				$srch = new SearchBase('tbl_bids');
				
				$srch->addCondition('bid_task_id','=',$id);
				//$srch->addCondition('bid_status','!=',3);
				
				$srch->getResultSet();
				
				return $srch->recordCount();				
			}
			
			function cancel_order($tid) {
				$db = &Syspage::getdb();
				
				$tid = intval($tid);
				if ($tid < 1) return false;
				
				$record = new TableRecord('tbl_tasks');
				
				$record->setFldValue('task_status', 4);
				
				$reserved_amount = $this->wallet->liabilities(User::getLoggedUserAttribute('user_id'),$tid);
				
				if (!$record->update(array('smt'=>'task_id = ? ', 'vals'=>array($tid)))) {
					$this->error = $record->getError();
					return false;
				}
				
				/* if (!$this->wallet->addTransaction(array('wtrx_amount'=>'-' . $reserved_amount, 'wtrx_mode'=>2, 'wtrx_task_id'=>$tid, 'wtrx_user_id'=>User::getLoggedUserAttribute('user_id'), 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
					$this->error = $this->wallet->getError();
					return false;
					}
					
					$rec = new TableRecord('tbl_money_earned');		
					$rec->setFldValue('me_cust_trans',$reserved_amount);		
					$rec->setFldValue('me_writer_recv',0);
					$rec->setFldValue('me_task_id',$tid);
					$rec->setFldValue('system_earned',$reserved_amount);
					$rec->setFldValue('me_trans_date','mysql_func_NOW()',true);
					if (!$rec->addNew()) {
					$this->error = $record->getError();
					return false;
				} */
				return true;
			}
			
			
			function send_notification_customer($data) {
				$db = &Syspage::getdb();
				$param = array(
				'to'=>$data['customer_email'],
				'temp_num'=>15,
				'user_first_name'=>User::getLoggedUserAttribute('user_screen_name'),
				'order_ref_id'=>$data['task_ref_id'],
				);
				$this->sendEmail($param);			
			}
			
			function activate_order($tid) {
				$db = &Syspage::getdb();
				$isassigned = $db->fetch($db->query('select task_writer_id from tbl_tasks where task_status=4 and task_id='.$tid));
				//echo '<pre>'.print_r($isassigned,true);exit;
				if($isassigned['task_writer_id'] == 0){
					$data['task_status'] = 1;
					}else {
					$data['task_status'] = 2;
				}
				
				$record = new TableRecord('tbl_tasks');
				$record->setFldValue('task_status',$data['task_status']);
				if ($tid > 0) {
					
					if (!$record->update(array('smt'=>'task_id = ? ', 'vals'=>array($tid)))) {
						$this->error = $record->getError();
						return false;
					}
					return true;
				}
			}
			
			public function makeTaskPublic($task_id) {
				$db = &Syspage::getdb();
				
				$record = new TableRecord('tbl_tasks');
				
				$record->setFldValue('task_type', 0);
				
				if (!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
					$this->error = $record->getError();
					return false;
				}
				
				return true;
			}
			
			public function updateDeadline($data) {
				$db = &Syspage::getdb();
				//print_r($data);exit;
				$record = new TableRecord('tbl_tasks');
				
				$record->setFldValue('task_due_date', date('Y-m-d H:i:s',strtotime($data['task_due_date'])));
				
				//die($record->getUpdateQuery(array('smt'=>'task_id = ?', 'vals'=>array($data['task_id']))));
				
				if (!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($data['task_id']), true))) {
					$this->error = $record->getError();
					return false;
				}
				
				return true;
			}
			
			public function cancelOrderRequest($data) {
				$db = &Syspage::getdb();
				//die(convertToJson($data));
				$data['task_id'] = intval($data['task_id']);
				if ($data['task_id'] < 1) return false;
				
				$record = new TableRecord('tbl_tasks');
				
				$record->setFldValue('task_cancel_request', 1);
				$record->setFldValue('task_desc_cancel_req', $data['task_desc_cancel_req']);
				
				if (!$record->update(array('smt'=>'task_id = ? ', 'vals'=>array($data['task_id'])))) {
					$this->error = $record->getError();
					return false;
				}
				
				return true;
			}
			
			public function updateTaskStatus() {
				$db = &Syspage::getdb();
				$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)");
			}
			
			public function taskFinishedFromWriter($task_id) {
				$db = &Syspage::getdb();
				
				$record = new TableRecord('tbl_tasks');		
				$record->setFldValue('task_finished_from_writer', 1);
				$autodebit_date = strtotime('+7 days',time());
				$record->setFldValue('task_completion_date_writer','mysql_func_NOW()',true);
				$record->setFldValue('autodebit_on', date("Y-m-d H:i",$autodebit_date));
				$record->setFldValue('task_review_request_status',0);
				
				if (!$record->update(array('smt'=>'task_id = ? ', 'vals'=>array($task_id)))) {
					$this->error = $record->getError();
					return false;
				}
				$data = $this->getTaskDetailsById($task_id);
				$param = array(
				'temp_num'=>48,
				'to'=>$data['customer_email'],
				'user_first_name'=>$data['customer_first_name'],
				'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
				'order_ref_id'=>$data['task_ref_id'],
				'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_process',array($data['task_id'])),
				'approval_date'=>$data['autodebit_on']
				);
				
				$this->sendEmail($param);
				
				return true;
			}
			
			function Revision_Request($data) {
				//echo "<pre>".print_r($data,true)."</pre>";exit;
				$db = &Syspage::getdb();
				$record = new TableRecord('tbl_tasks');
				$record->setFldValue('task_review_request_status',1);
				$record->setFldValue('task_review_description',$data['task_review_description']);
				$autodebit_date = strtotime('+1 days',strtotime($data['autodebit_on']));
				
				$record->setFldValue('autodebit_on', date("Y-m-d H:i",$autodebit_date));
				$record->setFldValue('task_revise_request_date','mysql_func_NOW()',true);
				
				if(!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($data['task_id']),true))) {			
					$this->error = $record->getError();
					return;
					}else{			
					$data['cus_req_to_upload_again'] = 1;
					if(!$this->bid->updatedMilestoneRequest($data)) {
						$this->error = $this->bid->getError();
					}			
				}
				
				return true;
			}
			
			public function getLast24hrs() {
				$db = &Syspage::getdb();
				$srch = new SearchBase('tbl_tasks','t');
				//$srch->addCondition('autodebit_on','<=','mysql_func_NOW()','',true);
				$srch->addDirectCondition('t.task_completion_date_writer > timestampadd(day, -1, now())');
				$srch->addCondition('t.task_completion_date_writer','!=','0000-00-00 00:00:00');
				$srch->addCondition('t.autodebit_on','!=','0000-00-00 00:00:00');
				$srch->joinTable('tbl_bids','INNER JOIN','t.task_writer_id = b.bid_user_id and b.bid_status = 1','b');
				$srch->joinTable('tbl_reserve_money','INNER JOIN','t.task_id = rm.res_task_id','rm');
				$srch->addCondition('rm.res_amount','!=',0);
				$srch->addCondition('t.task_status','!=',4);
				$srch->addGroupBy('t.task_id');
				$srch->addFld(array('t.task_id','b.bid_price','t.task_user_id','b.bid_user_id','t.autodebit_on','t.task_completion_date_writer'));
				$rs = $srch->getResultSet();
				//echo $srch->getQuery();
				$row = $db->fetch_all($rs);
				return $row;
			}
			
			public function get_orders_after_request_24hrs() {    // 
				$db = &Syspage::getdb();
				$srch = new SearchBase('tbl_tasks','t');
				$srch->addDirectCondition('DATE_ADD(t.task_revise_request_date, INTERVAL 1 day) < now()');
				$srch->addCondition('t.task_revise_request_date','!=','0000-00-00 00:00:00');
				$srch->joinTable('tbl_bids','INNER JOIN','t.task_writer_id = b.bid_user_id and b.bid_status = 1','b');
				$srch->joinTable('tbl_reserve_money','INNER JOIN','t.task_id = rm.res_task_id','rm');
				$srch->addCondition('rm.res_amount','!=',0);
				$srch->addCondition('t.task_status','!=',4);
				$srch->addGroupBy('t.task_id');
				$srch->addFld(array('t.task_id','b.bid_id','b.bid_price','t.task_user_id','b.bid_user_id','t.autodebit_on','t.task_completion_date_writer'));
				$rs = $srch->getResultSet();
				//echo $srch->getQuery();
				$row = $db->fetch_all($rs);
				return $row;
			}
			
			public function getExtend24hrs($task_id,$bid_id) {
				$db = &Syspage::getdb();
				$record = new TableRecord('tbl_tasks');
				$autodebit_date = strtotime('+1 days',strtotime($data['autodebit_on']));
				
				$record->setFldValue('autodebit_on', date("Y-m-d H:i",$autodebit_date));
				
				if(!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
					$this->error = $record->getError();
					return;
					}else{
					$data['bid_id'] = $bid_id;
					$data['cus_req_to_upload_again'] = 2;
					if(!$this->bid->updatedMilestoneRequest($data)) {
						$this->error = $this->bid->getError();
					}			
				}
				return true;
			}
			
			public function is_user_registered() { /*function added by shashank on 5august*/
				$db = &Syspage::getdb();
				$srch = new SearchBase('tbl_users');
				$srch->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
				$srch->addMultipleFields(array('user_first_name', 'user_last_name'));		
				$rs = $srch->getResultSet();
				if(!$row = $db->fetch($rs)) {
					$this->error = $db->getError();
					return false;
					}else {
					if($row['user_first_name']=='' || $row['user_last_name']=='') {
						return false;
					}
				}		
				return true;
			}	
		}		