<?php
class HomeController extends Controller{
    
	private $admin;
	private $Transactions;
	private $user;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->Transactions = new Transactions;
		$this->user = new User();
	}
	
    function default_action() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		
		$this->withdrawal_requests();
		$order_listing = $this->Home->order_listing();
		$this->set('arrTestTaker',$this->Home->test_taker());
		$this->set('arrOrderCancelReq',$this->Home->orderCancelReq());
		$this->set('arruserdelete',$this->Home->userDeleteReq());
		//echo "<pre>".print_r($this->admin->getRegisteredUsers(),true)."</pre>";
		$this->set('completed',$this->admin->getOrdersCompleted());
		$this->set('active',$this->admin->getActiveWriters());
		$this->set('registered',$this->admin->getRegisteredUsers());
		$this->set('online',$this->user->getCurrentOnlineWritersCount());
		$this->set('order_listing',$order_listing);
		
    	$this->_template->render();
    }
    
    function orders_listing() {
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$data  = $this->Home->order_listing();
		$order_listing['order_listing'] = $data;
		$this->set('order_listing',$order_listing);
		$this->_template->render(false,false);
	}
	
	public function withdrawal_requests() {
		$post = Syspage::getPostedVar();
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$this->set('arr_listing', $this->admin->getWithdrawalRequests($page));
       
	}
	
	public function verify_user() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$data  = $this->Home->test_taker();
		$arrTestTaker['arrTestTaker'] = $data;
		$this->set('arrTestTaker',$arrTestTaker);
		$this->_template->render(false,false);
	}
	
	public function user_delete_req() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$post = Syspage::getPostedVar();
		$data['arruserdelete'] = $this->Home->userDeleteReq();
		$this->set('arruserdelete',$data);
		$this->_template->render(false,false);
	}
	
	public function order_cancel_req() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$post = Syspage::getPostedVar();
		$data['arrOrderCancelReq'] = $this->Home->orderCancelReq();
		$this->set('arrOrderCancelReq',$data);
		$this->_template->render(false,false);
	}
	
	
	function message_common() {
		$this->_template->render();
	}
}