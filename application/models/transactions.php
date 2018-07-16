<?php
class Transactions extends Model {
	protected $error;
	private $transId;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function getTransId() {
		return $this->transId;
	}
	
	public function addTransaction($data) {
		$record = new TableRecord('tbl_transactions');
		
		$record->assignValues($data);
		
		$record->setFldValue('trans_mode', 1);
		$record->setFldValue('trans_date', 'mysql_func_NOW()', true);
		
		if (!$record->addNew()) {
			$this->error = $record->getError();
			return false;
		}
		
		$this->transId = $record->getId();
		
		return true;
	}
	
	public function addWithdrawalRequest($data) {
		$db = &Syspage::getdb();
		//echo "<pre>".print_r($data,true)."</pre>";exit;
		$srch = new SearchBase('tbl_withdrawal_requests');
		$srch->addMultipleFields(array('req_user_id','req_date','req_id'));
		$srch->setPageSize(1);
		$srch->addOrder('req_date','desc');
		$srch->addCondition('req_user_id','=',User::getLoggedUserAttribute('user_id'));
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		//$date = strtotime(date("Y M d", strtotime($row['req_date']." +7 days")));
		$date = strtotime($row['req_date']." +7 days");
		$rs1 = $db->query('Select count(*) as num from tbl_withdrawal_requests where req_user_id ='.User::getLoggedUserAttribute('user_id'));
		$res = $db->fetch($rs1);
		//echo date("Y M d").'<br>'.$date;
		//echo '<br>'.$res['num'];exit;
		if(mktime() <= $date && $res['num'] > 0) {
			$this->error = "You can not withdraw any amount before ".date('M d Y H:i',$date);
				return false;
			
		}else {
			
			$record = new TableRecord('tbl_withdrawal_requests');
			
			$record->assignValues($data);
			
			$record->setFldValue('req_user_id', User::getLoggedUserAttribute('user_id'));
			$record->setFldValue('req_amount', $data['amount']);
			$record->setFldValue('req_details', $data['message']);
			$record->setFldValue('req_date', 'mysql_func_NOW()', true);
			$record->setFldValue('req_status', 0);
			
			if (!$record->addNew()) {
				$this->error = $record->getError();
				return false;
			}
			return true;
		}
		
	}
	
	public function getUserTransactionHistory($page) {
		$db = &Syspage::getdb();
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		#$pagesize = 5;
		$pagesize = CONF_PAGINATION_LIMIT;
		
		$srch = new SearchBase('tbl_wallet_transactions', 'w');
		
		$srch->joinTable('tbl_transactions', 'LEFT OUTER JOIN', 'w.wtrx_reference_trx_id=trx.trans_id', 'trx');
		$srch->joinTable('tbl_withdrawal_requests', 'LEFT OUTER JOIN', 'w.wtrx_withdrawal_request_id=wr.req_id', 'wr');
		$srch->joinTable('tbl_tasks', 'LEFT OUTER JOIN', 'w.wtrx_task_id=t.task_id', 't');
		
		$srch->addCondition('w.wtrx_user_id', '=', User::getLoggedUserAttribute('user_id'));
		
		$srch->addOrder('w.wtrx_date', 'desc');
		
		$srch->addMultipleFields(array('w.*', 't.task_ref_id', 'trx.trans_gateway_transaction_id', 'trx.trans_status', 'wr.req_transaction_id', 'wr.req_status'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$rs = $srch->getResultSet();
		//echo $srch->getQuery();
		$transactions = $db->fetch_all($rs);
		
		foreach ($transactions as $key=>$arr) {
			//$trans_amount = abs($arr['wtrx_amount']);
			
			$transactions[$key]['wtrx_date']	= displayDate($arr['wtrx_date'], true, true, CONF_TIMEZONE);
			//$transactions[$key]['trans_amount']	= CONF_CURRENCY . number_format($trans_amount,2);
		}
		
		$rows['transactions'] 	= $transactions;
		$rows['pages']			= $srch->pages();
		$rows['page']			= $page;
		$rows['pagesize']		= $pagesize;
		
		return $rows;
	}
	
	public function getAmountWithdrawn() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_withdrawal_requests');
		
		$srch->addCondition('req_user_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('req_status', '=', 1);
		
		$srch->addFld('ROUND(IFNULL(SUM(req_amount),0), 2) AS amount_withdrawn');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['amount_withdrawn'];
	}
	
	public function getWithdrawalRequests($page,$pending = false) {
		$db = &Syspage::getdb();
		
		$arr_req = array();
		$page = intval($page);
		if ($page < 1) $page = 1;
		#$pagesize = 10;
		$pagesize = CONF_PAGINATION_LIMIT;
		
		$srch = new SearchBase('tbl_withdrawal_requests', 'w');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'w.req_user_id=u.user_id', 'u');
		
		$srch->addCondition('user_id','=',User::getLoggedUserAttribute('user_id'));
		
		if($pending == true) $srch->addCondition('req_status','=',0);
		
		$srch->addOrder('w.req_date', 'DESC');
		
		$srch->setPageSize($pagesize);
		
		$srch->setPageNumber($page);
		
		$srch->addMultipleFields(array('w.*', 'u.user_screen_name'));
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		foreach ($data as $key=>$arr) {
			
			$data[$key]['req_date']	= displayDate($arr['req_date'], true, true, CONF_TIMEZONE);
			
		}
		$arr_req['data']			= $data;
		$arr_req['total_records']	= $srch->recordCount();
		$arr_req['pagesize']		= $pagesize;
		$arr_req['pages']			= $srch->pages();
		$arr_req['page']			= $page;
		
		return $arr_req;
	}
}