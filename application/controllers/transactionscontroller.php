<?php
class TransactionsController extends LegitController {
	private $user;
	private $task;
	private $wallet;
	private $bid;
	
	private $paypal_url;
	private $paypal_host_url;
    
	function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		$this->user 	= new User();
		$this->task 	= new Task();
		$this->bid 		= new Bid();
		$this->wallet 	= new Wallet();
		
		if (CONF_PAYPAL_TRANSACTION_MODE) {
			/* Production mode */
			$this->paypal_url		= "https://www.paypal.com/cgi-bin/webscr";
			$this->paypal_host_url	= "www.paypal.com";
		}
		else {
			/* Test mode */
			$this->paypal_url		= "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$this->paypal_host_url	= "www.sandbox.paypal.com";
		}
    }
	
	public function payments($filter='') {
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!in_array($filter, array('history', 'load_funds', 'withdraw_funds'))) $filter = 'history';
		
		if(isset($_SESSION['e_f_pp_return']['refrer_url'])){
			unset($_SESSION['e_f_pp_return']['refrer_url']); 
		}
		
		$first_order = Common::is_first_order_done();
		if($first_order == false) {
			redirectUser(generateUrl('task','my_orders'));
			return;
		}
		
		$available_balance = $this->wallet->getAvailableBalance();
		if(!User::isUserReqDeactive()) {
			$frm_load		= $this->getLoadWalletForm();
			$frm_withdraw	= $this->getWithdrawalRequestForm();
			$this->set('frm_load', $frm_load);
			$this->set('frm_withdraw', $frm_withdraw);
		}
		$msg = confirm_payment_loaded($_SERVER['REQUEST_URI']);
		if($msg !== false) Message::addMessage($msg);
		$this->set('filter', $filter);
		
		$this->set('arr_transactions', $this->Transactions->getUserTransactionHistory());
		$this->set('available_balance', $available_balance);
		$this->set('arr_request', $this->Transactions->getWithdrawalRequests());
		//echo "<pre>".print_r($this->Transactions->getUserTransactionHistory(),true)."</pre>";exit;
		$this->_template->render();
	}
	
	public function earnings($filter='') {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!in_array($filter, array('history', 'withdraw_funds'))) $filter = 'history';
		
		if(!User::isUserReqDeactive()) {
				
			$available_balance = $this->wallet->getAvailableBalance();
			
			$frm = $this->getWithdrawalRequestForm();
			$this->set('arr_request', $this->Transactions->getWithdrawalRequests());
			$this->set('frm', $frm);		
		}
		
		
		$this->set('filter', $filter);		
		$this->set('arr_transactions', $this->Transactions->getUserTransactionHistory());
		$this->set('available_balance', $available_balance);
		
		
		$this->_template->render();
	}
	
	public function get_transactions() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_transactions = $this->Transactions->getUserTransactionHistory($page);
		
		$arr_transactions['str_paging'] = generatePagingStringHtml($arr_transactions['page'],$arr_transactions['pages'],"getWalletTransactions(xxpagexx);");
		
		$this->set('arr_transactions', $arr_transactions);
		
		$this->_template->render(false, false);
	}
	public function get_withdrawal_transactions() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_transactions = $this->Transactions->getWithdrawalRequests($page);
		
		$arr_transactions['str_paging'] = generatePagingStringHtml($arr_transactions['page'],$arr_transactions['pages'],"getWithdrawalTransactions(xxpagexx);");
		
		$this->set('arr_transactions', $arr_transactions);
		
		$this->_template->render(false, false);
	}
	
	public function dopayment() {
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged() || User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$error = false;
		
		$mile_id = intval($post['mile_id']);
		if ($mile_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$arr_milestone = $this->task->getMilestoneById($mile_id);
		if ($arr_milestone === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$frm = $this->getPaymentForm();
		
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('transactions', 'order_payment');
			$this->setLegitControllerCommonData('', 'order_payment');
            $this->order_payment($mile_id);
            return;
        }
		
		$amount_available = $this->wallet->getAvailableBalance() + $this->wallet->liabilities(0,$arr_milestone['mile_task_id']);
		
		if (floatval($post['amount']) > $amount_available) {
			Message::addErrorMessage( Utilities::getLabel( 'L_Amount_must_not_exceed' ) . ' ' . CONF_CURRENCY . $amount_available);
			$this->_template = new Template('transactions', 'order_payment');
			$this->setLegitControllerCommonData('', 'order_payment');
            $this->order_payment($mile_id);
            return;
		}
		
		if (!$this->wallet->addTransaction(array('wtrx_amount'=>'-' . $post['amount'], 'wtrx_mode'=>0, 'wtrx_task_id'=>$arr_milestone['mile_task_id'], 'wtrx_user_id'=>$arr_milestone['customer_id'], 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
			Message::addErrorMessage($this->wallet->getError());
			$error = true;
		}
		
		if (!$error) {
			$writer_amount = getAmountPayableToWriter($post['amount'],$arr_milestone['bid_price'],$arr_milestone['task_pages']);		//amount payable to writer
			
			if (!$this->wallet->addTransaction(array('wtrx_amount'=>$writer_amount, 'wtrx_mode'=>0, 'wtrx_task_id'=>$arr_milestone['mile_task_id'], 'wtrx_user_id'=>$arr_milestone['writer_id'], 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
				Message::addErrorMessage($this->wallet->getError());
				$error = true;
			}
		}
		
		$param = array('me_cust_trans'=>$post['amount'],'me_writer_recv'=>$writer_amount,'me_task_id'=>$arr_milestone['mile_task_id'],'system_earned'=>($post['amount']-$writer_amount));
		
		$this->wallet->add_earned_money($param);
		
		
		if (!$error) {
			if (!$db->update_from_array('tbl_milestones', array('mile_amount'=>$post['amount'], 'mile_status'=>1), array('smt'=>'mile_id = ?', 'vals'=>array($mile_id)))) {
				Message::addErrorMessage($this->db->getError());
			}
		}
		
		redirectUser(generateUrl('task', 'order_process', array($arr_milestone['mile_task_id'])));
	}
	
	public function order_payment($mile_id) {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		
		$mile_id = intval($mile_id);
		if ($mile_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$arr_milestone = $this->task->getMilestoneById($mile_id);
		if ($arr_milestone === false || $arr_milestone['customer_id'] != User::getLoggedUserAttribute('user_id')) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$frm = $this->getPaymentForm();
		
		$fld_pg = $frm->getField('page_num');
		$fld_pg->value = $arr_milestone['mile_page'] . ' (' . $arr_milestone['mile_words'] . ' ' . Utilities::getLabel( 'L_words' ) . ')';
		
		$fld_pay_btn = $frm->getField('btn_pay');
		$fld_pay_btn->html_after_field = '&nbsp;<a href="' . generateUrl('task', 'order_process', array($arr_milestone['mile_task_id'])) . '">' . Utilities::getLabel( 'L_Cancel' ) . '</a>';
		
		$fld_id = $frm->getField('mile_id');
		$fld_id->value = $arr_milestone['mile_id'];
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) { 
            $frm->fill($post);
        }
		
		$this->set('arr_milestone', $arr_milestone);
		$this->set('frm', $frm);
		
		$this->_template->render();
	}
	
	private function getPaymentForm() {
		$frm = new Form('frmPayment');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('width="30%"');
		$frm->setJsErrorDisplay('summary');
		$frm->setAction(generateUrl('transactions', 'dopayment'));
		
		$frm->addHiddenField('', 'mile_id', '');
		
		$frm->addHTML( Utilities::getLabel( 'L_Page_No' ), 'page_num', '');
		
		$fld_amount = $frm->addTextBox( Utilities::getLabel( 'L_Amount_to_be_paid' ), 'amount', 0, 'amount');
		$fld_amount->html_before_field = '<div class="symbolfield"><span class="currency">' . CONF_CURRENCY . '</span>';
		$fld_amount->html_after_field = '</div>';		
		$fld_amount->requirements()->setRequired();
		$fld_amount->requirements()->setFloatPositive();
		$fld_amount->requirements()->setRange(0.01,10000);
		
		$frm->addSubmitButton('', 'btn_pay', 'Pay', 'btn_pay','onclick="if(confirm(\'' . Utilities::getLabel( 'L_Alert_Want_To_Pay_Writer' ) . '\')== true){return true;}else{return false;}"');
		
		$frm->addHTML('', '', '<small>' . Utilities::getLabel( 'L_Payment_Agree_1' ) . ' <a href="' . generateUrl('cms', 'money-back') . '" target="_blank">' . Utilities::getLabel( 'L_Payment_Agree_2' ) . '</a>, <a href="' . generateUrl('cms', 'terms-conditions') . '" target="_blank">' . Utilities::getLabel( 'L_Payment_Agree_3' ) . '</a> and <a href="' . generateUrl('cms', 'privacy-policy') . '" target="_blank">' . Utilities::getLabel( 'L_Payment_Agree_4' ) . '</a>.</small>');
		
		return $frm;
	}
	
    public function getLoadWalletForm( $task_id, $bid_price = 0 ) {
		
		if(isset($task_id)) $task_id = intval($task_id);
		if(isset($bid_price) & $bid_price != 0) $bid_price = $bid_price + CONF_SERVICE_CHARGE;
		
		$available_balance = 0;
		$available_balance = $this->wallet->getAvailableBalance();
		$frm = new Form('frmLoadWallet');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		//$frm->setLeftColumnProperties('width="30%"');
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv( 'common_error_msg' );
		$frm->setAction( $this->paypal_url );
		
		//$returnUrl = 'http://' .$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$_SESSION['e_f_pp_return']['refrer_url'] = 'http://' .$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$returnUrl = 'http://' . $_SERVER['SERVER_NAME'] . generateUrl('transactions', 'confirm');
		//$returnUrl = 'http://' . $_SERVER['SERVER_NAME'] . CONF_WEBROOT_URL . 'paid.php';
		
		$cancelUrl = 'http://' . $_SERVER['SERVER_NAME'] . generateUrl('user', 'dashboard');
		$notifyUrl = generateNoAuthUrl('transactions', 'validate_transaction');
		
		$frm->addHiddenField('', 'business', CONF_PAYPAL_MERCHANT_ID);
		$frm->addHiddenField('', 'currency_code', CONF_PAYPAL_CURRENCY);
		$frm->addHiddenField('', 'return', $returnUrl);
		$frm->addHiddenField('', 'cancel_return', $cancelUrl);
		$frm->addHiddenField('', 'notify_url', $notifyUrl);
		$frm->addHiddenField('', 'cmd', '_xclick');
		$frm->addHiddenField('', 'item_name', 'Loading amount to my wallet');
		$frm->addHiddenField('','rm',0);
		$frm->addHiddenField('', 'custom', $this->user->getAttribute('user_ref_id').'|'.$task_id);
		$frm->addHTML('<img src="'.CONF_WEBROOT_URL.'images/card_payments.jpg" class="cards"><h4>' . Utilities::getLabel( 'L_Load_money_to_your_current_balance' ) . '</h4><small>' . Utilities::getLabel( 'L_You_can_load_maximum' ) . ' $'.( CONF_WALLET_LIMIT - $available_balance ).' ' . Utilities::getLabel( 'L_amount_to_your_wallet_from_PayPal' ) . '!</small>','','',true);
		$frm->addRadioButtons	('Payment method', 'payment_method', array('PayPal'), '', 1);
		
		$fld_amount = $frm->addTextBox( Utilities::getLabel( 'L_Amount_to_be_loaded' ) , 'amount', $bid_price, 'amount');
		$fld_amount->html_before_field = '<div class="symbolfield"><span class="currency">' . CONF_CURRENCY . '</span>';
		$fld_amount->html_after_field = '</div>';		
		$fld_amount->requirements()->setRequired();
		//$fld_amount->requirements()->setFloatPositive();
		$fld_amount->requirements()->setRange( 0.1, ( CONF_WALLET_LIMIT - $available_balance ) );
		
		
		$fld_load_btn = $frm->addSubmitButton('', 'btn_load', Utilities::getLabel( 'L_Load' ), 'btn_load');
		
		//$fld_amount->attachField($fld_load_btn);
		
		$frm->addHTML('', 'extra', '<small class="align-left">' . Utilities::getLabel( 'L_Wallet_Agree_1' ) . ' <a href="' . generateUrl('cms', 'money-back') . '" target="_blank">' . Utilities::getLabel( 'L_Wallet_Agree_2' ) . '</a>, <a href="' . generateUrl('cms', 'terms-conditions') . '" target="_blank">' . Utilities::getLabel( 'L_Wallet_Agree_3' ) . '</a> and <a target="_blank" href="' . generateUrl('cms', 'privacy-policy') . '" target="_blank">' . Utilities::getLabel( 'L_Wallet_Agree_4' ) . '</a>.</small>',true);
		
		return $frm;
	}
	
	public function request_withdrawal() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$available_balance = $this->wallet->getAvailableBalance();
		$frm = $this->getWithdrawalRequestForm();
		
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('transactions', 'earnings');
			$this->setLegitControllerCommonData('', 'earnings');
            $this->earnings();
            return;
        }
		if($available_balance >= CONF_WITHDRAW_FUNDS_LIMIT) {
			$available_balance = $this->wallet->getAvailableBalance();
		}else {
			Message::addErrorMessage ( sprintf( Utilities::getLabel( 'E_Min_Amount_Required_In_Wallet_For_Withdraw' ), CONF_CURRENCY ) );
			
			$this->_template = new Template('transactions', 'earnings');
			$this->setLegitControllerCommonData('', 'earnings');
            $this->earnings();
			return;
		}
		if ($post['amount'] <= $available_balance) {
			if ($this->Transactions->addWithdrawalRequest($post)) {
				//Notify admin
				$data = $this->user->getUserDetailsById(User::getLoggedUserAttribute('user_id'));
				if($data['user_first_name'] == '')
				{
					$user_name = $data['user_screen_name'];
				}
				else
				{
					$user_name = $data['user_first_name'];
				}
				$param = array('to'=>CONF_ADMIN_EMAIL_ID,'temp_num'=>8,'user_first_name'=>$user_name,'amount'=>CONF_CURRENCY . $post['amount'],'user_request'=>$post['message']);
				$this->task->sendEmail($param);
				
				
				$this->task->sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'temp_num'=>45,'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'user_first_name'=>$data['user_first_name'],'request_date'=>date('Y-m-d'),'request_time'=>date('H:i:s'),'amount_to_receive'=>CONF_CURRENCY . $post['amount']));
				
				Message::addMessage( Utilities::getLabel( 'S_Request_Submitted_Will_Notified_Once_Completed' ) );
			}
			else {
				Message::addErrorMessage($this->Transactions->getError());
			}
		}
		else {
			Message::addErrorMessage( Utilities::getLabel( 'L_You_can_withdraw_a_maximum_amount_of' ) . ' ' . CONF_CURRENCY . number_format( $available_balance, 2 ) );
		}
		
		if (User::isWriter()) {
			redirectUser(generateUrl('transactions', 'earnings', array('withdraw_funds')));
		}
		else {
			redirectUser(generateUrl('transactions', 'payments', array('withdraw_funds')));
		}
	}
	
	private function getWithdrawalRequestForm() {
		$available_balance = $this->wallet->getAvailableBalance();
		
		$frm = new Form('frmWithdrawalRequest');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('width="40%"');
		$frm->setFieldsPerRow(3);
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_error_msg');
		$frm->captionInSameCell('true');
		$frm->setAction(generateUrl('transactions', 'request_withdrawal'));
		
		$fld_amount = $frm->addTextBox( Utilities::getLabel( 'L_Amount_to_be_withdrawn' ) , 'amount', 0, 'amount');
		$fld_amount->html_before_field = '<div class="symbolfield"><span class="currency">' . CONF_CURRENCY . '</span>';
		
		$fld_amount->requirements( )->setRequired( );
		//$fld_amount->requirements()->setFloatPositive( );
		
		$fld_amount->requirements()->setRange( CONF_WITHDRAW_FUNDS_LIMIT, $available_balance );
		$str = '';

		if($available_balance < CONF_WITHDRAW_FUNDS_LIMIT) { 
			$str = sprintf( Utilities::getLabel ( 'E_Min_Amount_Required_In_Wallet_For_Withdraw' ), CONF_CURRENCY . CONF_WITHDRAW_FUNDS_LIMIT, CONF_CURRENCY . number_format($available_balance,2)  );
		}

		$fld_amount->requirements()->setCustomErrorMessage($str);
		
		$fld_msg = $frm->addTextBox( Utilities::getLabel( 'L_Details' ), 'message', '', 'message', '');
		$fld_msg->requirements()->setRequired();
		$fld_msg->requirements()->setCustomErrorMessage( Utilities::getLabel( 'L_Details_are_mandatory' ) );
		
		$fld_btn = $frm->addSubmitButton( '', 'btn_submit', Utilities::getLabel( 'L_Request_withdrawal' ), 'btn_submit', 'class="theme-btn"' );
		$fld_btn->fldCellExtra='width="20%" class="padding-none"';
		
		return $frm;
	}
	
	public function confirm() {
		//$post = Syspage::getPostedVar();
		//echo "<pre>".print_r($_SESSION,true)."</pre>";exit;
		//if (!isset($_SESSION['e_f_pp_return']['refrer_url'])) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		//$this->set('payment_info', $_SESSION['e_f_pp_return']);
		$this->set('refrer_url', $_SESSION['e_f_pp_return']['refrer_url']);
		
		$this->_template->render();
	}
	
	public function validate_transaction() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		$arr_trx = array();
		
		if ($this->validate_ipn()) {
			$arr_trx['trans_status']			= strtoupper($post['payment_status']) == 'COMPLETED' ? 1 : 0;
			$arr_trx['trans_payment_status']	= $post['payment_status'];
		}
		else {
			$arr_trx['trans_status']			= 0;
			$arr_trx['trans_payment_status']	= $post['payment_status'];
			$arr_trx['trans_comments']			= 'Invalid IPN data';
		}
		
		$arr_trx['trans_gateway_transaction_id']	= $post['txn_id'];
		$arr_trx['trans_amount']					= $post['mc_gross'];
		$arr_trx['trans_gateway_response']			= serialize($post);
		
		if (!$this->Transactions->addTransaction($arr_trx)) return false;
		
		$custom = explode('|',$post['custom']);
		$user_ref_id = $custom[0];
		$custom_task_id = $custom[1];
		if(isset($custom[1]) && $custom[1]!='') {
			$custom_task_id = $custom[1];
		}else {
			$custom_task_id = 0;
		}
		/* get user ID */
		$srch = new SearchBase('tbl_users');
		
		$srch->addCondition('user_ref_id', '=', $user_ref_id);
		
		$srch->addFld(array('user_id','user_ref_id','user_email','user_first_name'));
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		if ($arr_trx['trans_status'] == 1) {
			if (!$this->wallet->addTransaction(array('wtrx_amount'=>$post['mc_gross'], 'wtrx_mode'=>1, 'wtrx_task_id'=>$custom_task_id, 'wtrx_user_id'=>$row['user_id'], 'wtrx_reference_trx_id'=>$this->Transactions->getTransId(), 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) { 
				return false ;
			}else{
				$this->task->sendEmail( array( 'to' => CONF_ADMIN_EMAIL_ID, 'temp_num' => 31, 'user_email'=>$row['user_email'],'user_ref_id'=>$row['user_ref_id'],'amount_load'=>CONF_CURRENCY.$post['mc_gross'],'user_first_name'=>$row['user_first_name']));
				$this->task->sendEmail( array( 'to' => $row['user_email'], 'temp_num' => 32, 'user_email' => $row['user_email'], 'user_ref_id' => $row['user_ref_id'], 'user_first_name' => $row['user_first_name'], 'amount_load' => CONF_CURRENCY.$post['mc_gross'],'transaction_details_link' => CONF_SITE_URL.generateUrl( 'transactions', 'payments' ) ) );
			};
			
		}
		
		return true;
	}
	
	public function validate_ipn() {
		$post = Syspage::getPostedVar();
		
		$req = 'cmd=' . urlencode('_notify-validate');
        
		foreach ($post as $key=>$value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
		
		if (function_exists('curl_init')) {
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $this->paypal_url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $this->paypal_host_url));
			$res = curl_exec($ch);
			curl_close($ch);
		}
		else {
			$res = file_get_contents($this->paypal_url . '?' . $req);
		}
		
		if (strcmp(strtoupper($res), "VERIFIED") == 0) {
			return true;
		}
		else {
			return false;				
		}
	}
	
	public function transfer_payment($task_id) {
		
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		
		$task_id = intval($task_id);
		
		$arr_task = $this->task->getTaskDetailsById($task_id);
		if($arr_task['task_user_id']!=User::getLoggedUserAttribute('user_id')) die(convertToJson( Utilities::getLabel( 'E_Invalid_request' ) ));
		
		$frm = new Form('transfer_payment');
		
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable"');
		$frm->setLeftColumnProperties('');
		$frm->setAction(generateUrl('transactions','transfer_setup'));
		$frm->addHiddenField('','task_id',$task_id);
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Transfer_Amount_to_Writer' ), 'btn_submit' ,'onclick="if(confirm(\'' . Utilities::getLabel( 'L_Alert_Transfer_Payment_To_Writer' ) . '\'));"');
		
		$this->set('arr_milestone',$arr_task);
		$this->set('frm',$frm);
		$this->_template->render(false,false);
	}
	
	public function transfer_setup() {
		$post = Syspage::getPostedVar();
		if (!User::isUserLogged()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		
		$task_id = intval($post['task_id']);
		
		$arr_task = $this->task->getTaskDetailsById($task_id);
		$arr_bid = $this->bid->getAssignedBidDetails($task_id);
		//echo "<pre>".print_r($arr_bid,true)."</pre>";exit;
		if($arr_task['task_user_id']!=User::getLoggedUserAttribute('user_id')) die(convertToJson( Utilities::getLabel( 'E_Invalid_request' ) ));
		
		//--------------------------------------------------------------
		
		$writer_amount = getAmountPayableToWriter($arr_bid['bid_price']);		//amount payable to writer
		
		if (!$this->wallet->addTransaction(array('wtrx_amount'=>$writer_amount, 'wtrx_mode'=>0, 'wtrx_task_id'=>$task_id, 'wtrx_user_id'=>$arr_bid['bid_user_id'], 'wtrx_reference_trx_id'=>0, 'wtrx_withdrawal_request_id'=>0, 'wtrx_cancelled'=>0))) {
			Message::addErrorMessage($this->wallet->getError());
			redirectUser(generateUrl('task','order_process',array($task_id)));
		}
	
		$param = array('me_cust_trans'=>$arr_bid['bid_price'],'me_writer_recv'=>$writer_amount,'me_task_id'=>$task_id,'system_earned'=>($arr_bid['bid_price']-$writer_amount));
		
		$this->wallet->add_earned_money($param);
		
		$this->wallet->update_reserve_amount(array('res_user_id'=>$arr_task['task_user_id'],'res_task_id'=>$task_id));
		//--------------------------------------------------------------
		
		Message::addMessage( Utilities::getLabel( 'S_Order_Process_Is_Completed' ) );
		redirectUser( generateUrl( 'task', 'order_process', array( $task_id ) ) );
	}
	
	/* cron functions */
	
	public function seven_day_payment() {
		$row = $this->wallet->getAutoDebitInfo();
		
		foreach($row as $val){			
			$writer_amount = getAmountPayableToWriter( $val[ 'bid_price' ] );
			
			$this->wallet->addTransaction( array( 'wtrx_amount' => $writer_amount, 'wtrx_mode'=>0, 'wtrx_task_id' => $val['file_task_id'], 'wtrx_user_id' => $val['bid_user_id'], 'wtrx_reference_trx_id' => 0, 'wtrx_withdrawal_request_id' => 0, 'wtrx_cancelled' => 0 ) );
						
			$this->wallet->add_earned_money( array( 'me_cust_trans' => $val['bid_price'], 'me_writer_recv' => $writer_amount, 'me_task_id' => $val['file_task_id'],'system_earned'=>($val['bid_price']-$writer_amount)));
		
			$this->wallet->update_reserve_amount(array('res_user_id'=>$val['task_user_id'],'res_task_id'=>$val['file_task_id'])); 
			
		}
	}
}