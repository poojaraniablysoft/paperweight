<?php
class TransactionsController extends Controller {
	private $wallet;
	private $user;
	private $Money;
	private $task;
    
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->wallet	= new Wallet();
		$this->user		= new User();
		$this->Money	= new Money();
		$this->task		= new Task();
    }
	
	public function withdrawal_requests() {
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		
		$this->set('frmSearch',$this->getSearchForm());
		$this->search();
		//$arr_listing = $this->Transactions->getWithdrawalRequests($page);
		
		//$this->set('arr_listing', $arr_listing);
        
        $this->_template->render();
	}
	
	private function getSearchForm() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$frm = new Form('frmReqSearch');
		$frm->setAction('withdrawal_requests');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','placeholder="Keyword (in User email Or Username)"');
		
		//$frm->addSelectBox('User Type', 'user_type', array('Does not matter', 'Customer', 'Writer'), '', '', '', 'user_type');
		
		$frm->addSelectBox('', 'req_status', array('0'=>'Pending', '1'=>'Approved' , '2'=>'Declined'), '', '', 'Request Status');
		
		//$frm->addSelectBox('Is Email Verified', 'user_email_verified', array('No', 'Yes'), '', '', 'Does not matter');
			
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	public function search() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		$page = intval($post['page']);
		
		if ($page < 1) $page = 1;
		$pagesize = 10;			
		$srch = new SearchBase('tbl_withdrawal_requests', 'wr');
		$srch->joinTable('tbl_users', 'INNER JOIN', 'wr.req_user_id=u.user_id', 'u');		
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('u.user_email', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition("CONCAT(u.`user_first_name`, ' ', u.`user_last_name`)", 'like', '%' . $post['keyword'] . '%');
			$cnd->attachCondition("u.user_screen_name", 'like', '%' . $post['keyword'] . '%');
		} 
		$srch->addOrder('wr.req_date', 'DESC');
		switch ($post['req_status']) {
			case '0':
				$srch->addCondition('wr.req_status', '=', 0);
				break;
			case '1':
				$srch->addCondition('wr.req_status', '=', 1);
				break;
			case '2':
				$srch->addCondition('wr.req_status', '=', 2);
				break;
		}
		
		
		$srch->setPageSize($pagesize);
		
		$srch->setPageNumber($page);
		
		$srch->addMultipleFields(array('wr.*', 'u.user_screen_name','u.user_email'));
		
		$rs = $srch->getResultSet();
		
		$arr_req['data']			= $db->fetch_all($rs);
		$arr_req['total_records']	= $srch->recordCount();
		$arr_req['pagesize']		= $pagesize;
		$arr_req['pages']			= $srch->pages();
		$arr_req['page']			= $page;
		
		$this->set('arr_listing', $arr_req);
		/* $rs = $srch->getResultSet();
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
		$this->set('total_records', $total_records); */
		
		//$this->set('canEdit', $canEdit);
		
		//$this->_template->render(false, false);
	}
	public function get_withdrawal_request_details() {
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) die(convertToJson(array('status'=>0, 'msg'=>'Your session seems to have expired. Please log in to continue.')));
		
		$req_id = intval($post['req_id']);
		if ($req_id < 1) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request.')));
		
		$arr_req = $this->Transactions->getRequestDetails($req_id);
		if ($arr_req === false) die(convertToJson(array('status'=>0, 'msg'=>'Invalid request.')));
		
		die(convertToJson(array('status'=>1, 'msg'=>'', 'data'=>$arr_req)));
	}
	
	public function setup() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		$errors = array();
		
		//echo '<pre>' . print_r($post,true) . '</pre>'; exit;
		
		if (!Admin::isLogged()) die('Invalid request.');
		
		$req_id = intval($post['req_id']);
		if ($req_id < 1) die('Invalid request.');
		
		$arr_req = $this->Transactions->getRequestDetails($req_id);
		if ($arr_req === false || $arr_req['req_status'] != 0) die('Invalid request.');
		
		$amount_available = $this->wallet->getAvailableBalance($arr_req['req_user_id']);
		
		$frm = $this->getForm();
		
		if (!$frm->validate($post)) {
            $errors = $frm->getValidationErrors();
        }
		
		if ($arr_req['req_amount'] > $amount_available) {
			$errors[] = 'Insufficient amount in user\'s wallet. Amount available: ' . CONF_CURRENCY . $amount_available;            
		}
		
		if ($post['req_status'] == 1 && trim($post['trans_id']) == '') {
			$errors[] = 'Transaction ID is required.';
		}
		
		if (!empty($errors)) {
			foreach ($errors as $error) Message::addErrorMessage($error);
			$this->_template = new Template('transactions', 'update');
            $this->update($req_id);
            return;
		}
		
		if ($this->Transactions->update($post)) {
			$wtrx_err = '';
			
			if ($post['req_status'] == 1) {
				$arr_trx = array('wtrx_user_id'=>$arr_req['req_user_id'], 'wtrx_amount'=>'-' . $arr_req['req_amount'], 'wtrx_mode'=>2, 'wtrx_task_id'=>0, 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>$arr_req['req_id'], 'wtrx_cancelled'=>0);
				
				if (!$this->wallet->addTransaction($arr_trx)) $wtrx_err = $this->wallet->getError();
			}
			
			if (empty($wtrx_err)) {
				//send notification to the user
				/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 10");
				$row_tpl = $db->fetch($rs);
				
				$subject	= $row_tpl['tpl_subject'];
				$body 		= $row_tpl['tpl_body'];
				
				$status = $post['req_status'] == 1 ? 'Approved' : 'Declined';
				
				$arr_replacements = array(
					'{website_name}'=>CONF_WEBSITE_NAME,
					'{customer_name}'=>$arr_req['user_screen_name'],
					'{comments}'=>nl2br($post['req_comments']),
					'{status}'=>$status
				);
				
				foreach ($arr_replacements as $key=>$val){
					$subject	= str_replace($key, $val, $subject);
					$body		= str_replace($key, $val, $body);
				} */
				$status = $post['req_status'] == 1 ? 'Approved' : 'Declined';
				$param = array('to'=>$arr_req['user_email'],'temp_num'=>10,'comments'=>$post['req_comments'],'user_screen_name'=>(User::isWriter($arr_req['user_id'])==true)?$arr_req['user_screen_name']:$arr_req['user_first_name'],'status'=>$status);
				
				$this->task->sendEmail($param);
				
				Message::addMessage('Details successfully updated.');
			}
			else {
				Message::addErrorMessage($wtrx_err);
			}
		}
		else {
			Message::addErrorMessage($this->Transactions->getError());
		}
		
		redirectUser(generateUrl('transactions', 'withdrawal_requests'));
	}
	
	public function update($req_id) {
		global $withdrawal_request_status;
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$req_id = intval($req_id);
		if ($req_id < 1) die('Invalid request');
		
		$arr_req = $this->Transactions->getRequestDetails($req_id);
		if ($arr_req === false) die('Invalid request');
		
		$frm = $this->getForm();
		
		$fld_user = $frm->getField('user_ref_id');
		$fld_user->value = '#' . $arr_req['user_ref_id'];
		
		$fld_user = $frm->getField('user_name');
		$fld_user->value = $arr_req['user_screen_name'];
		
		$fld_amount_requested = $frm->getField('amount_requested');
		$fld_amount_requested->value = CONF_CURRENCY . $arr_req['req_amount'];
		
		if ($arr_req['req_status'] == 0) {
			$fld = $frm->addHTML('Amount available', 'amount_available', CONF_CURRENCY . $this->wallet->getAvailableBalance($arr_req['req_user_id']));
			$frm->addHiddenField('', 'req_id', $req_id, 'req_id');
			$fld_req_status = $frm->addSelectBox('Status', 'req_status', array(1=>'Approve', 2=>'Decline'), '', 'onchange="showTransactionId(this.value);"', 'Select', 'req_status');
			$fld_req_status->requirements()->setRequired();		
			$fld_trans = $frm->addRequiredField('Transaction ID', 'trans_id', '', 'trans_id');
			$fld_trans->html_before_field='<span id="trans_field">';
			$fld_trans->html_after_field='</span>';
			
			//$fld_req_status->attachField($fld_trans);
					
			$frm->addTextArea('Comments', 'req_comments', '', 'req_comments', 'columns="14" rows="8"');
			
			$frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit');
		}
		else {
			$frm->addHTML('Status', 'req_status', $withdrawal_request_status[$arr_req['req_status']]);
			
			$fld_trans_id = $frm->addHTML('Transaction ID', 'trans_id', '');
			$fld_trans_id->value = !empty($arr_req['req_transaction_id']) ? $arr_req['req_transaction_id'] : '-';
			
			$fld_comments = $frm->addHTML('Comments', 'req_comments', '');
			$fld_comments->value =  !empty($arr_req['req_comments']) ? nl2br($arr_req['req_comments']) : '-';
		}
		
		$this->set('frm', $frm);
		$this->set('req_status', $arr_req['req_status']);
        
        $this->_template->render();
	}
	
	function getTransactionIdField($val){
		global $db;
		if(!is_numeric($val)) die( 'Select Status first' );
        
		if($val==1)
		{	
			$fld=new FormField('text', 'trans_id', 'Transaction ID');  		
			$fld->requirements()->setRequired();
			$str=$fld->getHTML();
			
			$str .= '<script type="text/javascript">//<![CDATA[
			';
			$str .= 'frmWithdrawalRequest_validator_requirements.trans_id=' . json_encode($fld->requirements()->getRequirementsArray()) . ';';      
			$str .= 'frmWithdrawalRequest_validator.resetFields();';
			$str .= '
			//]]></script>';
		}else {
			$str = '<script type="text/javascript">//<![CDATA[
			';
			$fld=new FormField('text', 'trans_id', 'Transaction ID');   
			$fld->extra='disabled="disabled"';
			$str=$fld->getHTML();
			$str .= '<script type="text/javascript">//<![CDATA[
			';
			$str .= 'frmWithdrawalRequest_validator_requirements.trans_id=' . json_encode($fld->requirements()->getRequirementsArray()) . ';';      
			$str .= 'frmWithdrawalRequest_validator.resetFields();';
			$str .= '
			//]]></script>';
		}
        echo $str;         
	}
	
	private function getForm() {
		$frm = new Form('frmWithdrawalRequest', 'frmWithdrawalRequest');
		
		$frm->setTableProperties('width="100%" cellspacing="10" cellpadding="0" border="0" class="colTable"');
		$frm->setLeftColumnProperties('width="24%"');
        $frm->setJsErrorDisplay('afterfield');
		$frm->setAction(generateUrl('transactions', 'setup'));
		$frm->setExtra('class="siteForm"');
		
		$frm->addHTML('User ID', 'user_ref_id', '');
		$frm->addHTML('Username', 'user_name', '');
		$frm->addHTML('Amount requested', 'amount_requested', '');		
		
		return $frm;
	}
	
	public function add_transaction($user_id) {
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$user_id = intval($user_id);
		if ($user_id < 1) die('Invalid request.');
		
		$arr_user = $this->user->getUserDetailsById($user_id);
		if ($arr_user === false) die('Invalid request.');
		
		$frm = $this->getWalletTransactionForm($user_id);
		
		$fld_user_id = $frm->getField('user_id');
		$fld_user_id->value = $user_id;
		
		$fld_user_ref_id = $frm->getField('user_ref_id');
		$fld_user_ref_id->value = '#' . $arr_user['user_ref_id'];
		
		$fld_user_name = $frm->getField('user_name');
		$fld_user_name->value = $arr_user['user_screen_name'];
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$frm->fill($post);
		}
		
		$this->set('user_id', $user_id);
		$this->set('frm', $frm);
		
		$this->_template->render();
	}
	
	private function getWalletTransactionForm($user_id) {
		$frm = new Form('frmWalletTransaction', 'frmWalletTransaction');
		
		$frm->setTableProperties('width="100%" cellspacing="10" cellpadding="0" border="0" class="colTable"');
		$frm->setLeftColumnProperties('width="24%"');
        $frm->setJsErrorDisplay('afterfield');
		$frm->setAction(generateUrl('transactions', 'transaction_setup'));
		$frm->setExtra('class="siteForm"');
		
		$frm->addHiddenField('', 'user_id', '', 'user_id');
		
		$frm->addHTML('User ID', 'user_ref_id', '');
		$frm->addHTML('Username', 'user_name', '');
		
		$frm->addSelectBox('Transaction type', 'transaction_type', array(1=>'Debit', 2=>'Credit'), '', 'style="width:200px;"', 'Select', 'transaction_type')->requirements()->setRequired();
		
		$fld_amount = $frm->addTextBox('Amount', 'amount', '', 'amount', 'style="width:100px;"');
		$fld_amount->html_before_field = CONF_CURRENCY . ' ';
		$fld_amount->html_after_field = '  Current wallet amount : '. CONF_CURRENCY . $this->wallet->getAvailableBalance($user_id);
		$fld_amount->requirements()->setRequired();
		$fld_amount->requirements()->setFloatPositive();
		
		$fld_amount->requirements()->setRange(0.01,10000);
		
		$frm->addTextArea('Comments', 'comments', '', 'comments', 'style="width:500px; height:200px;"')->requirements()->setRequired();
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit');
		
		return $frm;
	}
	
	public function transaction_setup() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		$errors = array();
		
		//echo '<pre>' . print_r($post,true) . '</pre>'; exit;
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$user_id = intval($post['user_id']);
		if ($user_id < 1) die('Invalid request.');
		
		$arr_user = $this->user->getUserDetailsById($user_id);
		if ($arr_user === false) die('Invalid request.');
		
		$frm = $this->getForm();
		
		if (!$frm->validate($post)) {
			$errors = $frm->getValidationErrors();
			foreach ($errors as $error) Message::addErrorMessage($error);
			$this->_template = new Template('transactions', 'add_transaction');
            $this->add_transaction($user_id);
            return;
		}
		
		$available_amount = $this->wallet->getAvailableBalance($user_id);
		$range = 10000 - $available_amount;
		
		if ($post['transaction_type'] == 2) {
			if(!User::isWriter($user_id) && $post['amount'] > $range) {
				Message::addErrorMessage('Wallet limit for this customer is '.CONF_CURRENCY.$range.'. You can not Credit more than '.CONF_CURRENCY.$range.' amount to this customer wallet.'); 
				$this->_template = new Template('transactions', 'add_transaction');
				$this->add_transaction($user_id);
				return;
			}
		}
		if ($post['transaction_type'] == 1 && $post['amount'] > $this->wallet->getUserWalletBalance($user_id)) {
			Message::addErrorMessage('Amount must not exceed wallet balance. Max amount ' . CONF_CURRENCY . $this->wallet->getUserWalletBalance($user_id));
			$this->_template = new Template('transactions', 'add_transaction');
            $this->add_transaction($user_id);
            return;
		}
		
		if($post['transaction_type'] == 2 && $post['amount'] > $this->Money->getTotalEarning()) {
			Message::addErrorMessage('Amount must not exceed your earnings. Max amount ' . CONF_CURRENCY . $this->Money->getTotalEarning());
			$this->_template = new Template('transactions', 'add_transaction');
            $this->add_transaction($user_id);
            return;
		}
		
		$amount = $post['transaction_type'] == 1 ? '-' . $post['amount'] : $post['amount'];
			
		$arr_trx = array(
						'wtrx_user_id'=>$arr_user['user_id'],
						'wtrx_amount'=>$amount,
						'wtrx_mode'=>2,
						'wtrx_task_id'=>0,
						'wtrx_reference_trx_id'=>0,
						'wtrx_withdrawal_request_id'=>0,
						'wtrx_cancelled'=>0,
						'wtrx_cancel_notes'=>'',
						'wtrx_comments'=>$post['comments']
						);
			
		if (!$this->wallet->addTransaction($arr_trx)) {
			Message::addErrorMessage($this->wallet->getError());
		}
		else {
			//send notification to the user
			/* $rs = $db->query("SELECT * FROM tbl_email_templates WHERE tpl_id = 12");
			$row_tpl = $db->fetch($rs);
			
			$body = $row_tpl['tpl_body'];
			
			$arr_replacements = array(
				'{website_name}'=>CONF_WEBSITE_NAME,
				'{customer_name}'=>$arr_user['user_screen_name'],
				'{comments}'=>nl2br($post['comments'])
			);
			
			foreach ($arr_replacements as $key=>$val){				
				$body = str_replace($key, $val, $body);
			} */
			
			$param = array('to'=>$arr_user['user_email'],'temp_num'=>12,'user_screen_name'=>(User::isWriter($arr_user['user_id']) == true ?$arr_user['user_screen_name']:$arr_user['user_first_name']),'comments'=>$post['comments']);
			if ($post['transaction_type'] == 1) {
				$subject = CONF_CURRENCY . $post['amount'] . ' debited from your wallet.';
			}
			else {
				$subject = CONF_CURRENCY . $post['amount'] . ' credited to your wallet.';
			}
			
			$this->task->sendEmail($param,$subject);
			//sendMail($arr_user['user_email'], $subject, $body);
			
			Message::addMessage('Transaction successfully added.');
		}
		
		redirectUser(generateUrl('user', 'transaction_details', array($user_id)));
	}
	
	public function order_payment($task_id) {
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		if (!$this->Task->changeOrderStatus($task_id,3)) {
			Message::addErrorMessage($this->Task->getError());
		}		
		redirectUser($_SERVER['HTTP_REFERER']);
	}
	
	public function reserve_earnings($task_id,$user_id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$task_id = intval($task_id);
		$user_id = intval($user_id);
		if (!$this->wallet->reserveToEarnings($task_id,$user_id)) {
			Message::addErrorMessage($this->wallet->getError());
		}
		redirectUser(generateUrl('money','total_money_reserved'));
	}
}