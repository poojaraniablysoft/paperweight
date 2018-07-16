<?php
class Money extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getError(){
        return $this->error;
    }
	
	public function getTotalCredit($filter=false) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_wallet_transactions');
				
		if($filter == true) {
			$srch->addMultipleFields(array('DATE(wtrx_date)','ROUND(IFNULL(SUM(wtrx_amount),0),2) AS balance'));
			$srch->addDirectCondition( 'wtrx_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
			
		}else {
			$srch->addFld('ROUND(IFNULL(SUM(wtrx_amount),0),2) AS balance');
		}
		$srch->addDirectCondition( 'wtrx_amount >= 0');
		$srch->addCondition('wtrx_mode','=',1);
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['balance'];
	}
	
	public function getTotalEarning($filter=false) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_money_earned');
		
		if($filter == true) {
			$srch->addDirectCondition( 'me_trans_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
		}
		$srch->addFld('ROUND(IFNULL(SUM(system_earned),0),2) AS balance');
		
		$rs = $srch->getResultSet();
		//echo $srch->getQuery();
		$row = $db->fetch($rs);
		
		
		$search = new SearchBase('tbl_wallet_transactions');
		if($filter == true) $search->addDirectCondition( 'wtrx_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
		$search->addFld('ROUND(IFNULL(SUM(wtrx_amount),0),2) AS balance');
		$search->addCondition('wtrx_mode','=',2);
		$search->addCondition('wtrx_task_id','=',0);
		$search->addCondition('wtrx_withdrawal_request_id','=',0);
		$res = $search->getResultSet();
		$amount = $db->fetch($res); 
		return ($row['balance']-$amount['balance']);
		//return $row['balance'];
	}
	
	public function getMoneyPaid($filter=false) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_withdrawal_requests');
		if($filter == true) {
			$srch->addDirectCondition( 'req_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
		}
		$srch->addFld('ROUND(IFNULL(SUM(req_amount),0),2) AS balance');
		
		$srch->addCondition('req_status','=',1);
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['balance'];
	}
	
	public function getTransactiondetails($data,$filter=false) {
		$db = &Syspage::getdb();
		$pagesize = 10;
		$page = intval($data['page']);
		if (!($page > 0)) $page = 1;
		$srch = new SearchBase('tbl_wallet_transactions','wr');
		//$srch->joinTable('tbl_tasks','LEFT OUTER JOIN','t.task_id=me.me_task_id','t');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','wr.wtrx_user_id = u.user_id','u');
		
		if ($data['keyword'] != ''){
			$srch->addCondition('u.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
		}
		$srch->addCondition('wtrx_mode','=',2);
		$srch->addCondition('wtrx_withdrawal_request_id','=',0);
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$srch->addOrder('wr.wtrx_date','desc');
		if($filter == true) {
			$srch->addDirectCondition( 'wr.wtrx_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');			
		}
		$srch->addMultipleFields(array('IF(u.user_type="1",u.user_screen_name,u.user_first_name) as user_name','wr.wtrx_amount','wr.wtrx_date as trans_date','wr.wtrx_comments'));
		
		$rs = $srch->getResultSet();
		
		if(!$row['data'] = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		foreach ($row['data'] as $key=>$arr) {
			#$row['data'][$key]['trans_date'] = displayDate($arr['trans_date'], true);			$row['data'][$key]['trans_date'] = $arr['trans_date'];
		}
		$row['pages'] = $srch->pages();
		$row['page'] = $page;
		$row['pagesize'] = $pagesize;
		$row['start_record'] = ($page-1)*$pagesize + 1;
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$row['end_record'] = $end_record;
		$row['total_records'] = $total_records;
		return $row;
	}
	public function getCommissiondetails($data,$filter=false) {
		$db = &Syspage::getdb();
		$pagesize = 10;
		$page = intval($data['page']);
		if (!($page > 0)) $page = 1;
		$srch = new SearchBase('tbl_money_earned','me');
		$srch->joinTable('tbl_tasks','LEFT OUTER JOIN','t.task_id=me.me_task_id','t');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','t.task_user_id = u1.user_id','u1');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','t.task_writer_id = u2.user_id','u2');
		
		if ($data['keyword'] != ''){
			$cnd = $srch->addCondition('u1.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u2.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_first_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_last_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u2.user_first_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u2.user_last_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('t.task_ref_id', 'LIKE', '%' . $data['keyword'] . '%');
		}
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$srch->addOrder('me_trans_date','desc');
		if($filter == true) {
			$srch->addFld('DATE(me_trans_date)');
			$srch->addDirectCondition( 'me_trans_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');			
		}
		$srch->addMultipleFields(array('t.task_id','t.task_ref_id','u1.user_first_name as customer_name','u1.user_id as customer_id','u2.user_screen_name as writer_name','u2.user_id as writer_id','system_earned AS system_amount','me_trans_date as earn_date','me_cust_trans','me_writer_recv'));
		//echo $srch->getQuery();
		$rs = $srch->getResultSet();
		
		if(!$row['data'] = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		foreach ($row['data'] as $key=>$arr) {
			#$row['data'][$key]['earn_date'] = displayDate($arr['earn_date'], true);			$row['data'][$key]['earn_date'] = $arr['earn_date'];
		}
		$row['pages'] = $srch->pages();
		$row['page'] = $page;
		$row['pagesize'] = $pagesize;
		$row['start_record'] = ($page-1)*$pagesize + 1;
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$row['end_record'] = $end_record;
		$row['total_records'] = $total_records;
		return $row;
	}
	
	public function getMoneyCreditdetails($data,$filter=false) {
		$db = &Syspage::getdb();
		
		$pagesize = 10;
		$page = intval($data['page']);
		if (!($page > 0)) $page = 1;
		
		$srch = new SearchBase('tbl_wallet_transactions','wt');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','wt.wtrx_user_id = u.user_id','u');
		$srch->joinTable('tbl_tasks','LEFT OUTER JOIN','wt.wtrx_task_id = t.task_id','t');
		
		 if ($data['keyword'] != ''){
			$cnd = $srch->addCondition('u.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u.user_first_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u.user_last_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('t.task_ref_id', 'LIKE', '%' . $data['keyword'] . '%');
		} 
		$srch->addMultipleFields(array('wt.*','u.user_screen_name','u.user_first_name','u.user_last_name','u.user_id','t.task_ref_id','t.task_id'));		
		if($filter == true) {
			$srch->addFld('DATE(wtrx_date)');
			$srch->addDirectCondition( 'wtrx_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
			
		}else {
			
		}
		$srch->addDirectCondition( 'wtrx_amount >= 0');
		$srch->addCondition('wtrx_mode','=',1);
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$srch->addOrder('wtrx_date','desc');
		$rs = $srch->getResultSet();
		
		if(!$row['data'] = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		$row['pages'] = $srch->pages();
		$row['page'] = $page;
		$row['pagesize'] = $pagesize;
		$row['start_record'] = ($page-1)*$pagesize + 1;
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$row['end_record'] = $end_record;
		$row['total_records'] = $total_records;
		return $row;
	}
	
	public function getMoneyPaiddetails($data,$filter=false) {
		$db = &Syspage::getdb();
		$pagesize = 10;
		$page = intval($data['page']);
		if (!($page > 0)) $page = 1;
		$srch = new SearchBase('tbl_withdrawal_requests','wr');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u1.user_id = wr.req_user_id','u1');
				
		if ($data['keyword'] != ''){
			$cnd = $srch->addCondition('u1.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_first_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_last_name', 'LIKE', '%' . $data['keyword'] . '%');
		}
		$srch->addCondition('wr.req_status','=',1);
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$srch->addOrder('wr.req_approved_on','desc');
		if($filter == true) {
			$srch->addDirectCondition( ' wr.req_approved_on > DATE_SUB( NOW(), INTERVAL 24 HOUR)');			
		}
		$srch->addMultipleFields(array('IF(u1.user_type="1",u1.user_screen_name,CONCAT_WS(" ",u1.user_first_name,u1.user_last_name)) as user_name','wr.req_amount AS withdrawal_amount','wr.req_details','wr.req_approved_on as withdrawal_date','wr.req_comments','u1.user_id'));
		
		$rs = $srch->getResultSet();
		
		if(!$row['data'] = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		$row['pages'] = $srch->pages();
		$row['page'] = $page;
		$row['pagesize'] = $pagesize;
		$row['start_record'] = ($page-1)*$pagesize + 1;
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$row['end_record'] = $end_record;
		$row['total_records'] = $total_records;
		return $row;
	}
	
	public function getReservedMoney($filter = false) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_reserve_money');
		
		if($filter == true) {
			$srch->addDirectCondition( 'res_amount_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');
		}
		$srch->addFld('ROUND(IFNULL(SUM(res_amount),0),2) AS balance');
		
		$rs = $srch->getResultSet();
		//echo $srch->getQuery();
		$row = $db->fetch($rs);
		return $row['balance'];
	}
	
	public function getMoneyReserveddetails($data,$filter=false) {
		$db = &Syspage::getdb();
		$pagesize = 10;
		$page = intval($data['page']);
		if (!($page > 0)) $page = 1;
		$srch = new SearchBase('tbl_reserve_money','rm');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u1.user_id = rm.res_user_id','u1');
		$srch->joinTable('tbl_tasks','LEFT OUTER JOIN','t.task_id = rm.res_task_id','t');
				
		if ($data['keyword'] != ''){
			$cnd = $srch->addCondition('u1.user_screen_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_first_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('u1.user_last_name', 'LIKE', '%' . $data['keyword'] . '%');
			$cnd->attachCondition('t.task_ref_id', 'LIKE', '%' . $data['keyword'] . '%');
		}
		if (isset($data['task_status']) && $data['task_status'] != null) {
			$srch->addCondition('t.task_status', '=', $data['task_status']);
		}
		//$srch->addCondition('wr.req_status','=',1);
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$srch->addOrder('rm.res_amount_date','desc');
		if($filter == true) {
			$srch->addDirectCondition( ' rm.res_amount_date > DATE_SUB( NOW(), INTERVAL 24 HOUR)');			
		}
		
		
		
		$srch->addMultipleFields(array('IF(u1.user_type="1",u1.user_screen_name,CONCAT_WS(" ",u1.user_first_name,u1.user_last_name)) as user_name','u1.user_id','rm.res_amount AS reserved_amount','rm.res_task_id','rm.res_amount_date as reserve_date','t.task_ref_id','t.task_status','t.task_user_id','t.task_id'));
		$srch->addCondition('rm.res_amount','>',0);
		$rs = $srch->getResultSet();
		
		if(!$row['data'] = $db->fetch_all($rs)) {
			$this->error = $db->getError();
			return false;
		}
		//echo "<pre>".print_r($row['data'],true)."</pre>";exit;
		$row['pages'] = $srch->pages();
		$row['page'] = $page;
		$row['pagesize'] = $pagesize;
		$row['start_record'] = ($page-1)*$pagesize + 1;
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$row['end_record'] = $end_record;
		$row['total_records'] = $total_records;
		return $row;
	}
}
?>