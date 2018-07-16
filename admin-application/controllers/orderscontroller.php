<?php
class OrdersController extends Controller{
    
	private $admin;
	private $message;
	private $Task;
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->message = new Messages();
		$this->common	= new Common();
		$this->Task	= new Task();
	}
	
    function default_action() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$this->set('frmordersSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		global $order_status;
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmordersSearch');
		$frm->setAction('orders');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'task_status', $order_status, '', '', 'Order Status');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\''.generateUrl('orders').'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
		//Mark expired orders as cancelled
		$db->query("UPDATE tbl_tasks SET task_status = 4 WHERE task_due_date < NOW() AND task_status IN (0,1)");
		
		$srch = new SearchBase('tbl_tasks','t');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id=t.task_user_id','u');		
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('t.task_topic', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition('u.user_screen_name','LIKE','%' . $post['keyword'] . '%');
			$cnd->attachCondition('u.user_first_name','LIKE','%' . $post['keyword'] . '%');
			$cnd->attachCondition('u.user_last_name','LIKE','%' . $post['keyword'] . '%');
			$cnd->attachCondition('t.task_ref_id','LIKE','%' . $post['keyword'] . '%');
		}
		$srch->addCondition('t.task_topic', '!=', '');
		
		if (is_numeric($post['task_status'])) $srch->addCondition('t.task_status', '=', intval($post['task_status']));
				
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('t.task_id','DESC');
				
		$srch->addMultipleFields(array('t.task_id','t.task_topic','t.task_ref_id','t.task_status','u.user_screen_name','u.user_first_name','u.user_last_name','t.task_due_date','t.task_posted_on'));
				
		$rs = $srch->getResultSet();
		
		$this->set('arr_listing',$db->fetch_all($rs));
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
	
	function order_details($id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$data = $this->Orders->OrderDetails($id);
		$arr_reviews = $this->Orders->getReviews($id);
		$files = $this->Task->getOrderFiles($id,1);
		$milestone_files = $this->Task->getOrderFiles($id,2);
		$this->set('milestone_files', $milestone_files);
		$this->set('files', $files);
		$this->set('order',$data);
		$this->set('arr_reviews',$arr_reviews);
		$this->_template->render();
	}
	
	function bid_details($bid_id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$data = $this->Orders->biddetail($bid_id);
		$this->set('order',$data);
		$this->_template->render();
	}
	
	function bids($task_id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$task_id =  intval($task_id);
		$task_topic = $this->Orders->tasktopic($task_id);
		$this->set('task_topic',$task_topic);
		
		$data = $this->Orders->Showbids($task_id);
		$this->set('order',$data);
		 $this->set('order',$data['result']);
		$this->set('pages',$data['pages']);
		$this->set('page',$data['page']);
		$this->set('start_record',$data['start_record']);
		$this->set('pagesize',$data['pagesize']);
		$this->set('end_record',$data['end_record']);
		$this->set('total_records',$data['total_records']); 
		
		$this->set('task_id',$task_id);
		$this->_template->render();
	}
	
	function milestones($task_id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
	
		$task_id =  intval($task_id);
		$data = $this->Orders->getMilestones($task_id);
		$this->set('task_id',$task_id);
		$this->set('arr_milestones',$data);
		$this->_template->render();
	}
	
	function reviews($task_id,$rev_id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$task_id = intval($task_id);
		$rev_id	= intval($rev_id);
		$data = $this->Orders->getReviews($task_id,$rev_id);
		$this->set('arr_reviews',$data);
		$this->_template->render(false,false);
	}
	
	function mark_order_verified_by_admin(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_tasks');
		$srch->addCondition('task_id', '=', $id);
		$srch->addFld( array( 'task_status', 'task_ref_id', 'task_user_id' ) );
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
				
		if ($row['task_status'] == 0) {
			$task_status = 1;
		}		
			
		if (!$db->update_from_array('tbl_tasks', array('task_status'=>$task_status), array('smt'=>'task_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else { 
			$user_data = $db->fetch($db->query('SELECT * from tbl_users where user_id='.$row['task_user_id']));
			Message::addMessage('Order is Verified!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($task_status==1) ? '<a class="toggleswitch"></a>' : ''));
			$this->Task->sendEmail(array(
				'user_email' => $user_data['user_email'],
				'temp_num' => 56,
				'to' => $user_data['user_email'],
				'order_ref_id' => $row['task_ref_id'],
				'user_first_name' => $user_data['user_first_name']
			));
			$users_list = $db->fetch_all($db->query('select user_email,user_screen_name from tbl_users where user_type=1 and user_active=1 and user_is_approved=1'));
			foreach($users_list as $val) {
				
				$this->Task->sendEmail(array(
					'user_email'=>$val['user_email'],
					'temp_num'=>20,
					'to'=>$val['user_email'],
					'order_ref_id'=>$row['task_ref_id'],
					'user_screen_name'=>$val['user_screen_name'],
					'order_page_link'=>CONF_SITE_URL.'/bid/add/'.$id
				));
			}
			die(convertToJson($arr));
		}
	}
	
	function decline_Review(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$user_id = intval($post['user_email']);
		$srch = new SearchBase('tbl_reviews','r');
		$srch->joinTable('tbl_users','INNER JOIN','u.user_id ='.$user_id,'u');
		$srch->addCondition('r.rev_id', '=', $id);
		$srch->addCondition('r.rev_reviewer_id', '=', $user_id);
		$srch->addMultipleFields(array('r.rev_status','u.user_email'));
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
	 
		if ($row['rev_status'] == 1) {
			$rev_status = 2;
		}
		
		if ($row['rev_status'] == 0) {
			Message::addMessage('Review is already declined!');      
			dieWithError(Message::getHtml());
		}	
			
		if (!$db->update_from_array('tbl_reviews', array('rev_status'=>$rev_status), array('smt'=>'rev_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		} else {
			$param = array('to'=>$row['user_email'],'temp_num'=>11);
			if(!$this->Task->sendEmail($param)) {
				Message::addErrorMessage($db->getError());
				dieWithError(Message::getHtml());
			}else {
				Message::addMessage('Review is declined!');      
				$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>'Declined');
				die(convertToJson($arr));
			}
		}
	}
	
	function mark_review_featured() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		/* @var $db Database */
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
			
		$id = intval($post['rev_id']);
		$srch = new SearchBase('tbl_reviews');
		$srch->addCondition('rev_id', '=', $id);
		$srch->addFld('rev_is_featured');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
				
		if ($row['rev_is_featured'] == 0) {
			$rev_is_featured = 1;
		}else {
			$rev_is_featured = 0;
		}
			
		if (!$db->update_from_array('tbl_reviews', array('rev_is_featured'=>$rev_is_featured), array('smt'=>'rev_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('Review status is Updated!');      
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($rev_is_featured == 1)?'Featured':'Mark as featured'));
			die(convertToJson($arr));
			
		}
	}
	
	function get_conversation($bid_id) {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		//public $user_type;
		$bid_id = intval($bid_id);
		
		//echo "<pre>".print_r($this->Orders->getConversationHistory($bid_id),true)."</pre>";
		$data = $this->Orders->getConversationHistory($bid_id);
		$this->set('arr_bids',$this->Orders->biddetail($bid_id));
		$this->set('conver',$data);
		//echo "<pre>".print_r($data,true)."</pre>";
		$this->_template->render();
	}
	
	function cancel_order() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		$data = $this->Orders->OrderDetails($task_id);
		if (!$db->update_from_array('tbl_tasks', array('task_status'=>4), array('smt'=>'task_id = ?', 'vals'=>array($task_id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			       
			$param = array('to'=>$data['customer_email'],'temp_num'=>15,'user_first_name'=>$data['customer_first_name'],'user_request'=>$data['task_desc_cancel_req'],'order_ref_id'=>$data['task_ref_id']);
			
			if(!$this->Task->sendEmail($param)) {
				Message::addErrorMessage($db->getError());
				dieWithError(Message::getHtml());
			}else {
				Message::addMessage('Order cancelled, notification has been sent to Writer and Customer!');
				$arr = array('status'=>1, 'msg'=>Message::getHtml());
				die(convertToJson($arr));
			}
		}
	}
	function update($id) {
	
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$db = &Syspage::getdb();
		if($id<=0) die('Invalid Access');
		
		$order=array();
	
		$post = Syspage::getPostedVar();
		if(intval($id) > 0)	{
			$order_data=$this->Orders->OrderDetails($id, false);
			$order_data['task_service_type'] = $order_data['task_service_type_id'];
			
			$order_frm=$this->getOrderForm($id);
			$order_data['task_due_date'] = str_replace('-','/',$order_data['task_due_date']);

			$order_frm->fill($order_data);		
			$order_frm->getField('task_due_date')->value = date('d/m/Y H:i', strtotime($order_data['task_due_date']));
			$this->set('frmcontent',$order_frm );
			$files = $this->Task->getOrderFiles($id);
			$this->set('files',$files);
		} else	{
			$frm = $this->getOrderForm($id);			
		}
		$this->_template->render();
	}
	
	function getOrderForm()
	{
		$service_types = $this->common->getServices();
		$frm = new Form('frmTask');
		
		$frm->setExtra('class="siteForm" enctype="multipart/form-data"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable blockrepeat"');
		$frm->captionInSameCell(false);
		$frm->setLeftColumnProperties('width="30%" align="right"');
        $frm->setAction(generateUrl('orders', 'setup'));
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'task_id', '', 'task_id');
		$frm->addHiddenField('', 'invitee_ids', '', 'invitee_ids');	
		
		$frm->addHtml('', '', '<h2 class="bordertitle" align="left">Basic Information</h2>',true);
		
		$frm->addSelectBox('Type of paper', 'task_paptype_id', $this->common->getPaperTypes(), '', '', 'Select', 'task_paptype_id')->requirements()->setRequired();
		$frm->addRequiredField('Topic', 'task_topic', '', 'task_topic');
		
		$fld_pages = $frm->addRequiredField('Pages', 'task_pages', 1, 'task_pages', '');
		$fld_pages->requirements()->setRange(1,999);
		$tomorrow = new DateTime('tomorrow');			
		$end = new DateTime('9999-12-31 23:59:59');
		
		$fld_deadline = $frm->addDateTimeField('Deadline', 'task_due_date', '', 'task_due_date', 'readonly = "readonly" class="difftd iconcalender task_due_date_cal" style = "width:50%;margin-right:5px;"'); 
		$fld_deadline->requirements()->setRequired();
		//$fld_deadline->requirements()->setDateTime();
		$fld_deadline->requirements()->setRange($tomorrow->format( 'd/m/Y H:i:s'),$end->format('d/m/Y H:i:s'));
		$fld_deadline->requirements()->setCustomErrorMessage('Deadline date should be of tomorrow or after that.');	
		
		$frm->addHtml('', '', '<h2 class="bordertitle" align="left">Additional Information</h2>',true);
		
		$fld = $frm->addSelectBox('Discipline', 'task_discipline_id', $this->common->getDisciplines(), '', '', 'Select', 'task_discipline_id');
		$fld->requirements()->setRequired();
		
		$serv = $frm->addSelectBox('Type of service', 'task_service_type', $service_types, '','','Select','task_service_type');
		$serv->requirements()->setRequired();
		
		$cit = $frm->addSelectBox('Format or citation style', 'task_citstyle_id', $this->common->getCitationStyles(), '','','Select','task_citstyle_id');
		$cit->requirements()->setRequired();
				
		$fld = $frm->addSelectBox('Order Status', 'task_status', array('0'=>'Pending approval', '1'=>'Bidding', '2'=>'In Progress', '3'=>'Completed', '4'=>'Cancelled'), '', '', 'Select', 'task_status');
		$fld->requirements()->setRequired();
		
		$ins = $frm->addTextArea('Paper instructions', 'task_instructions', '', 'task_instructions');
		$ins->requirements()->setRequired();
		
		$fld_files = $frm->addFileUpload('Upload additional material', 'task_files[]', '', 'multiple="multiple"');
		$fld_files->html_after_field = '<br /><small>Max file upload limit for single file 10MB and for total size is 30MB<br />You can upload only .jpg .jpeg .gif .png .pdf .docx .doc .txt</small>';
		$fld_save	= $frm->addSubmitButton('', 'btn_submit', 'Publish for writers');
	
		return $frm;
	}
	public function setup() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}		
		$task_id = intval($post['task_id']);
		
		$data = $post;
		$frm = $this->getOrderForm();
		
		
		$fld = $frm->getField('task_due_date');
		$fld->requirements()->setRequired(false);
		 
		if (!$frm->validate($post)){
			$errors = $frm->getValidationErrors();
            redirectUser(generateUrl('order', 'update',array($post['task_id'])));
            return;
        }
		
		$invitee_id = intval($post['invitee_ids']);
			
		$srch = new SearchBase('tbl_tasks');
		$srch->addCondition('task_id','=',$task_id);
		$srch->addFld('task_due_date');
		$srch->addFld('task_status');
		$srch->addFld('task_user_id');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		//$data['task_type']	= $invitee_id > 0 ? 1 : 0;
		if(!isset($post['task_due_date'])) {
			$phpdate = strtotime(str_replace('/','-',$row['task_due_date']));
			$mysqldate = date('Y-m-d H:i:s', $phpdate);
			$data['task_due_date'] = $mysqldate;
			//$data['task_due_date'] = date(CONF_DATE_FORMAT_PHP.' H:i:s',$row['task_due_date']);
		}else {
			$phpdate = strtotime(str_replace('/','-',$data['task_due_date']));
			$mysqldate = date('Y-m-d H:i:s', $phpdate);
			$data['task_due_date'] = $mysqldate;
			//$data['task_due_date'] = date(CONF_DATE_FORMAT_PHP.' H:i:s',strtotime($data['task_due_date']));
		}
		
		
		//upload additional material
		$num_files_uploaded = count($_FILES['task_files']['name']);
		$error = 0;
		$file_size = 0;
		if ($num_files_uploaded > 0) {
			$arr_valid_text_ext		= array('txt', 'pdf', 'doc', 'docx', 'xls');
			$arr_valid_image_ext	= array('jpg', 'jpeg', 'png', 'gif');
			
			for ($i=0; $i<$num_files_uploaded; $i++) {
				$file_size += $_FILES['task_files']['size'][$i];
				if($file_size > 32086425) {
					Message::addErrorMessage('Could not upload above 30MB.');
					$this->_template = new Template('task', 'add');
					$this->setLegitControllerCommonData('', 'add');
					$this->add();
					return;
				}
				
				if (is_uploaded_file($_FILES['task_files']['tmp_name'][$i])) {
					$path_parts = pathinfo($_FILES['task_files']['name'][$i]);
					
					$file_ext		= strtolower($path_parts['extension']);
					$base_name		= $path_parts['basename'];
					
					if($_FILES['task_files']['type'][$i] == '' || $_FILES['task_files']['type'][$i] == null) {
						Message::addErrorMessage('Could not upload ' . $base_name . '. Invalid file format.');
						$error++;
						
						
					}
					if (!in_array($file_ext,$arr_valid_text_ext) && !in_array($file_ext,$arr_valid_image_ext)) {
						Message::addErrorMessage('Could not upload ' . $base_name . '. Invalid file format.');
						$error++;
						
					}
					
					if ($_FILES['task_files']['size'][$i] > 10695475 || $_FILES['task_files']['size'][$i] == 0) {				//restrict file size to 10MB
						Message::addErrorMessage('Could not upload ' . $base_name . '. File size too big, max allowed size 10MB.');
						$error++;
						
					}
					
					if (in_array($file_ext,$arr_valid_image_ext)) {
						$file_type = 2;
						
						if (!saveImage($_FILES['task_files']['tmp_name'][$i], $_FILES['task_files']['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							$error++;
							
						}
					}
					else {
						$file_type = 1;
						
						if (!saveFile($_FILES['task_files']['tmp_name'][$i], $_FILES['task_files']['name'][$i], $saved_file_name)){
							Message::addErrorMessage($saved_file_name);
							$error++;
							
						}
					}
					
				}
			}
		}
		if($error == 0) {
			
			if($data['task_status']=='3'){ //case of complete
				
				if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
				
				if (!$this->Task->changeOrderStatus($task_id,3,$row['task_user_id'])) {
					Message::addErrorMessage($this->Task->getError());
					redirectUser(generateUrl('orders', 'update',array($post['task_id'])));
				}		
				
				
			}			
			
			if (!$this->Orders->addTask($data)) {
				Message::addErrorMessage($this->Orders->getError());
				redirectUser(generateUrl('orders', 'update',array($post['task_id'])));
			}
			
			
		}
		if($error==0){
			for ($i=0; $i<$num_files_uploaded; $i++) {				
				if (is_uploaded_file($_FILES['task_files']['tmp_name'][$i])) {
					$path_parts = pathinfo($_FILES['task_files']['name'][$i]);
					
					$file_ext		= $path_parts['extension'];
					$base_name		= $path_parts['basename'];
					if (!$this->Task->addFile(array('file_task_id'=>$post['task_id'], 'file_name'=>$saved_file_name, 'file_download_name'=>$base_name, 'file_type'=>$file_type))) {
						unlink(CONF_INSTALLATION_PATH . 'user-uploads/' . $saved_file_name);
						Message::addErrorMessage($this->Task->getError());
					}
				}
			}				
		}
		
		
		if ($task_id > 0 && $error == 0) {
			Message::addMessage('Order successfully updated.');
		}
		
		
		redirectUser(generateUrl('orders', 'update',array($post['task_id'])));
	}
	public function download_file($fid) {
		$db = &Syspage::getdb();
		
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}		
		
		$fid = intval($fid);
		if ($fid < 1) die('Invalid request');
		
		$rs = $db->query("SELECT * FROM tbl_files WHERE file_id = " . $db->quoteVariable($fid));
        if (!$row = $db->fetch($rs)) die('Invalid request');
		
		$order_details = $this->Task->getTaskDetailsById($row['file_task_id']);
		
		if (!$order_details) {
			die('Invalid request');
		}
        
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
	
	function change_order_status() {
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		$task_status = intval($post['status']);
		
		if(!$this->Orders->changeOrderStatus($task_id,$task_status)) {
			Message::addErrorMessage($this->Orders->getError());
			die(convertToJson(array('status'=>0,'msg'=>Message::getHtml())));
		}
		Message::addMessage('Order status updated!');
		die(convertToJson(array('status'=>1,'msg'=>Message::getHtml())));
	}
	
}