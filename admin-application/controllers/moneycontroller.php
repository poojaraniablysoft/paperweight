<?php 
class MoneyController extends Controller {
	private $Wallet;
	private $Transactions;
	
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->Wallet	= new Wallet();
    }
	
	public function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$db = &Syspage::getdb();
		$pagesize = 5;			
		$srch = new SearchBase('tbl_withdrawal_requests', 'wr');
		$srch->joinTable('tbl_users', 'INNER JOIN', 'wr.req_user_id=u.user_id', 'u');		
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('u.user_email', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition("CONCAT(u.`user_first_name`, ' ', u.`user_last_name`)", 'like', '%' . $post['keyword'] . '%');
		} 
		$srch->addOrder('wr.req_date', 'DESC');
		
		$srch->addCondition('wr.req_status', '=', 0);			
		
		
		$srch->setPageSize($pagesize);
		
		
		$srch->addMultipleFields(array('wr.*', 'u.user_screen_name'));
		
		$rs = $srch->getResultSet();
		
		$arr_req['data']			= $db->fetch_all($rs);
		$this->set('arr_listing',$arr_req);
		$this->set('tc',$this->Money->getTotalCredit());
		$this->set('today_credit',$this->Money->getTotalCredit(true));
		$this->set('total_earned',$this->Money->getTotalEarning());
		$this->set('today_earned',$this->Money->getTotalEarning(true));
		$this->set('total_paid',$this->Money->getMoneyPaid());
		$this->set('today_paid',$this->Money->getMoneyPaid(true));
		$this->set('total_reserved',$this->Money->getReservedMoney());
		$this->set('today_reserved',$this->Money->getReservedMoney(true));
		$this->_template->render();
	}
	
	public function total_money_earn() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		//echo "<pre>".print_r($post,true)."</pre>";
		$data = $this->Money->getCommissiondetails($post);
		$arr_trans = $this->Money->getTransactiondetails($post);
		$this->set('total_earned',$this->Money->getTotalEarning());
		
		$this->set('frm',$this->getSearchForm());
		$this->set('arr_listing',$data);
		$this->set('arr_transaction',$arr_trans);
		$this->_template->render();
	}
	
	public function total_money_paid() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyPaiddetails($post);
		$this->set('total_paid',$this->Money->getMoneyPaid());
		$this->set('frm',$this->getSearchFrm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	
	public function total_money_credit() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyCreditdetails($post);
		$this->set('tc',$this->Money->getTotalCredit());
		$this->set('frm',$this->getSearchForm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	
	public function money_earn_current() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getCommissiondetails($post,true);
		$arr_trans = $this->Money->getTransactiondetails($post,true);
		$this->set('today_earned',$this->Money->getTotalEarning(true));
		$this->set('frm',$this->getSearchForm());
		$this->set('arr_listing',$data);
		$this->set('arr_transaction',$arr_trans);
		$this->_template->render();
	}
	
	public function money_paid_current() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyPaiddetails($post,true);
		$this->set('today_paid',$this->Money->getMoneyPaid(true));
		$this->set('frm',$this->getSearchFrm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	
	public function money_credit_current() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyCreditdetails($post,true);
		$this->set('today_credit',$this->Money->getTotalCredit(true));
		$this->set('frm',$this->getSearchForm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmSearch');
		$frm->setAction('');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name or Order Id)"');
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	private function getrResSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmResSearch');
		$frm->setAction('');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name or Order Id)"');
		$frm->addSelectBox('', 'task_status', array(2=>'In Progress',4=>'Cancelled'), '', '', 'Order Status');
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	private function getSearchFrm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmEmptplSearch');
		$frm->setAction('');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name)"');
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	function commission_detail() {
		if (!Admin::isLogged()) die('invalid request.');
		
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		$data['page'] = $page;
		//
		$filter = $post['filter'];
		
		if (!in_array($filter, array('commission', 'transaction'))) $filter = 'commission';
		
		if($filter == 'commission') {
			$arr_orders = (array) $this->Money->getCommissiondetails($data);
			
		}
		if($filter == 'transaction') {
			$arr_orders = (array) $this->Money->getTransactiondetails($data);
		}	
		
		$arr_orders['str_paging'] = generateNewPagingStringHtml($arr_orders['page'],$arr_orders['pages'],"earn('" . $filter . "',xxpagexx);",'',$arr_orders['total_records'],$arr_orders['pagesize']);
		die(convertToJson($arr_orders));
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render(false, false);
	}
	
	public function total_money_reserved() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyReserveddetails($post);
		$this->set('total_reserved',$this->Money->getReservedMoney());
		$this->set('frm',$this->getrResSearchForm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	
	public function today_money_reserved() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$data = $this->Money->getMoneyReserveddetails($post,true);
		$this->set('total_reserved',$this->Money->getReservedMoney(true));
		$this->set('frm',$this->getrResSearchForm());
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
}