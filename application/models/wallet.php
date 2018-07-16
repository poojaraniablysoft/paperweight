<?php
class Wallet extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getError(){
        return $this->error;
    }
	
	public function addTransaction($data) {
		$record = new TableRecord('tbl_wallet_transactions');
		//$data['wtrx_amount'] = $data['wtrx_amount']-($data['wtrx_amount']*CONF_SERVICE_COMISSION/100); 
		$record->assignValues($data);
		
		$record->setFldValue('wtrx_date', 'mysql_func_NOW()', true);
		
		if (!$record->addNew()) {
			$this->error = $record->getError();
			return false;
		}
		
		if (!$this->updateWalletBalance($record->getId(),$data['wtrx_user_id'])) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
	
	private function updateWalletBalance($wtrx_id,$user_id) {
		$record = new TableRecord('tbl_wallet_transactions');
		
		$record->setFldValue('wtrx_balance', $this->getUserWalletBalance($user_id));
        
		if (!$record->update(array('smt'=>'wtrx_id = ?', 'vals'=>array($wtrx_id)))) {
			$this->error = $record->getError();
			return false;
		}
        
        return true;
	}
	
	public function getUserWalletBalance($user_id) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		//if ($user_id < 1 && !User::isUserLogged()) return false;
		
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$srch = new SearchBase('tbl_wallet_transactions');
		
		$srch->addCondition('wtrx_user_id', '=', $user_id);
		
		$srch->addFld('ROUND(IFNULL(SUM(wtrx_amount),0),2) AS balance');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['balance'];
	}
	
	public function getAvailableBalance($user_id) {
		$available_bal = round($this->getUserWalletBalance($user_id), 2);
		//$available_bal = round($this->getUserWalletBalance($user_id) - $this->liabilities($user_id), 2);
		
		return $available_bal > 0 ? $available_bal : 0;
	}
	
	public function liabilities($user_id=0,$task_id=0) {			/* Balance due */
		$db = &Syspage::getdb();
		
		if (User::isWriter($user_id)) return false;
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$task_id = intval($task_id);
		
		$srch = new SearchBase('tbl_tasks', 't');
		
		$srch->joinTable('tbl_bids', 'INNER JOIN', 't.task_id=b.bid_task_id', 'b');
		$srch->joinTable('tbl_wallet_transactions', 'LEFT OUTER JOIN', 't.task_id=w.wtrx_task_id AND t.task_user_id=w.wtrx_user_id', 'w');
		
		$srch->addCondition('t.task_user_id', '=', $user_id);
		$srch->addCondition('t.task_status', '=', 2);
		$srch->addCondition('b.bid_status', '=', 1);
		
		if ($task_id > 0) {
			$srch->addCondition('t.task_id', '=', $task_id);
		}
		/* 
		$srch->addMultipleFields(array('IFNULL((b.bid_price_per_page*t.task_pages) + ((b.bid_price_per_page*t.task_pages*' . CONF_SERVICE_COMISSION . ')/100), 0) AS debt', 'IFNULL(SUM(w.wtrx_amount),0) AS paid')); */
		$srch->addMultipleFields(array('IFNULL((b.bid_price) + ((b.bid_price*' . CONF_SERVICE_COMISSION . ')/100), 0) AS debt', 'IFNULL(SUM(w.wtrx_amount),0) AS paid'));
		
		$srch->addGroupBy('t.task_id');
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		
		$sql = $db->query('SELECT ROUND(SUM(dr.debt) - ABS(SUM(dr.paid)),2) AS total_debt FROM (' . $srch->getQuery() . ') dr WHERE ABS(dr.paid) <= dr.debt');
		
		$row = $db->fetch($sql);
		
		return $row['total_debt'];
	}
	
	public function getIncomingAmount() {			/* writer's amount in progress */
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged() || !User::isWriter()) return false;
		
		$srch = new SearchBase('tbl_tasks', 't');
		
		$srch->joinTable('tbl_bids', 'INNER JOIN', 't.task_id=b.bid_task_id', 'b');
		$srch->joinTable('tbl_wallet_transactions', 'LEFT OUTER JOIN', 't.task_id=w.wtrx_task_id AND w.wtrx_user_id=b.bid_user_id', 'w');
		
		$srch->addCondition('b.bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('t.task_status', '=', 2);
		$srch->addCondition('b.bid_status', '=', 1);
		/* 
		$srch->addMultipleFields(array('IFNULL((b.bid_price_per_page*t.task_pages), 0) AS order_amount', 'IFNULL(SUM(w.wtrx_amount),0) AS paid')); */
		$srch->addMultipleFields(array('IFNULL((b.bid_price), 0) AS order_amount', 'IFNULL(SUM(w.wtrx_amount),0) AS paid'));
		
		$srch->addGroupBy('t.task_id');
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		
		$sql = $db->query('SELECT ROUND(IFNULL(SUM(dr.order_amount) - SUM(dr.paid),0),2) AS balance FROM (' . $srch->getQuery() . ') dr WHERE dr.paid <= dr.order_amount');
		
		$row = $db->fetch($sql);
		
		return $row['balance'];
	}
	
	public function getTaskAmountPaid($task_id,$user_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_wallet_transactions');
		
		$srch->addCondition('wtrx_task_id', '=', $task_id);
		$srch->addCondition('wtrx_user_id', '=', $user_id);
		
		$srch->addFld('ROUND(IFNULL(abs(SUM(wtrx_amount)),0),2) AS amount_paid');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['amount_paid'];
	}
	
	public function getTaskAmountPaidByCustomer($task_id){
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_money_earned');
		
		$srch->addCondition('me_task_id', '=', $task_id);
				
		$srch->addFld('ROUND(IFNULL(abs(SUM(me_cust_trans)),0),2) AS amount_paid');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['amount_paid'];
	}
	
	public function add_earned_money($data) {
	
		$rec = new TableRecord('tbl_money_earned');		
		$rec->setFldValue('me_cust_trans',$data['me_cust_trans']);		
		$rec->setFldValue('me_writer_recv',$data['me_writer_recv']);
		$rec->setFldValue('me_task_id',$data['me_task_id']);
		$rec->setFldValue('system_earned',$data['system_earned']);
		$rec->setFldValue('me_trans_date','mysql_func_NOW()',true);
		if (!$rec->addNew()) {
			$this->error = $rec->getError();
			return false;
		}
	}
	
	public function reserved_transaction($data) {
		
		$rec = new TableRecord('tbl_reserve_money');		
		$rec->setFldValue('res_user_id',$data['res_user_id']);		
		$rec->setFldValue('res_task_id',$data['res_task_id']);
		$rec->setFldValue('res_amount',$data['res_amount']);
		//$rec->setFldValue('system_earned',$data['system_earned']);
		$rec->setFldValue('res_amount_date','mysql_func_NOW()',true);
		
		if (!$rec->addNew()) {			
			$this->error = $rec->getError();
			return false;
		} 
		
	}
	
	public function update_reserve_amount($data) {
		
		$rec = new TableRecord('tbl_reserve_money');		
		$rec->setFldValue('res_user_id',$data['res_user_id']);		
		$rec->setFldValue('res_task_id',$data['res_task_id']);
		$rec->setFldValue('res_amount',0);
		//$rec->setFldValue('system_earned',$data['system_earned']);
		$rec->setFldValue('res_amount_date','mysql_func_NOW()',true);
		
		if(!$rec->update(array('smt'=>'res_user_id = ? and res_task_id = ?', 'vals'=>array($data['res_user_id'],$data['res_task_id'])))) {
			$this->error = $rec->getError();
			return false;
		}
		
	}
	
	public function getReservedAmountByUserId($user_id){
		$db = &Syspage::getdb();			
		$srch = new SearchBase('tbl_reserve_money');
		$srch->addCondition('res_user_id','=',$user_id);
		$srch->addFld('ROUND(IFNULL(abs(SUM(res_amount)),0),2) AS res_amount');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		return $row;
	}
	
	public function getReservedAmount($task_id) {
		$db = &Syspage::getdb();			
		$srch = new SearchBase('tbl_reserve_money');
		$srch->addCondition('res_task_id','=',$task_id);
		$srch->addFld('res_amount');
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		return $row;
	}
	
	public function getAutoDebitInfo() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_tasks','t');
		$srch->addCondition('autodebit_on','<=','mysql_func_NOW()','',true);
		$srch->addCondition('autodebit_on','!=','0000-00-00 00:00:00');
		$srch->joinTable('tbl_bids','INNER JOIN','t.task_writer_id = b.bid_user_id and b.bid_status = 1','b');
		$srch->joinTable('tbl_reserve_money','INNER JOIN','t.task_id = rm.res_task_id','rm');
		$srch->addCondition('rm.res_amount','!=',0);
		$srch->addCondition('t.task_status','!=',4);
		$srch->addFld(array('t.task_id','b.bid_price','t.task_user_id','b.bid_user_id','t.autodebit_on'));
		$rs = $srch->getResultSet();
		$row = $db->fetch_all($rs);
		return $row;
	}
	
	public function reserveToEarnings($task_id,$user_id) {
		$db = &Syspage::getdb();
		
		$rs = $this->getReservedAmount($task_id);
		$reserved_amount = $rs['res_amount'];
		
		$data = array('res_task_id'=>$task_id,'res_user_id'=>$user_id);
		 
		$rec = new TableRecord('tbl_money_earned');		
		$rec->setFldValue('me_cust_trans',$reserved_amount);		
		$rec->setFldValue('me_writer_recv',0);
		$rec->setFldValue('me_task_id',$task_id);
		$rec->setFldValue('system_earned',$reserved_amount);
		$rec->setFldValue('me_trans_date','mysql_func_NOW()',true);
		if (!$rec->addNew()) {
			$this->error = $record->getError();
			return false;
		} 
		$this->update_reserve_amount($data);
		return true;
	}
}