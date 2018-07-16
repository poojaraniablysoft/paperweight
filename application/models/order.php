<?php
class Order extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getError(){
        return $this->error;
    }
	
	public function getCustomerActivityStats($user_id=0) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$srch = new SearchBase('tbl_tasks');
		
		$srch->addCondition('task_user_id', '=', $user_id);
		
		$srch->addMultipleFields(array('IFNULL(SUM(CASE WHEN task_status=1 THEN 1 ELSE 0 END),0) AS orders_in_bidding', 'IFNULL(SUM(CASE WHEN task_status=2 THEN 1 ELSE 0 END),0) AS orders_in_progress', 'IFNULL(SUM(CASE WHEN task_status=3 THEN 1 ELSE 0 END),0) AS orders_completed'));
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	}
	
	public function getWriterActivityStats($user_id=0) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$srch = new SearchBase('tbl_tasks', 't');
		
		$srch->joinTable('tbl_bids', 'LEFT OUTER JOIN', 't.task_id=b.bid_task_id', 'b');
		
		$srch->addCondition('b.bid_user_id', '=', $user_id);
		
		$srch->addMultipleFields(array(
										'IFNULL(SUM(CASE WHEN t.task_status=1 AND b.bid_status=0 THEN 1 ELSE 0 END),0) AS orders_in_bidding',
										'IFNULL(SUM(CASE WHEN t.task_status=2 AND b.bid_status=1 THEN 1 ELSE 0 END),0) AS orders_in_progress',
										'IFNULL(SUM(CASE WHEN t.task_status=3 AND b.bid_status=1 THEN 1 ELSE 0 END),0) AS orders_completed'
									));
		
		$rs = $srch->getResultSet();
		return $db->fetch($rs);
	}
	
	public function getUserCompletedOrders($user_id) {
		$arr_stats = User::isWriter($user_id) ? $this->getWriterActivityStats($user_id) : $this->getCustomerActivityStats($user_id);
		
		return $arr_stats['orders_completed'];
	}
	
	public function getUserInProgressOrders($user_id) {
		$arr_stats = User::isWriter($user_id) ? $this->getWriterActivityStats($user_id) : $this->getCustomerActivityStats($user_id);
		
		return $arr_stats['orders_in_progress'];
	}
	
	/* public function getUserOrdersStats() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_tasks', 't');
		
		$srch->joinTable('tbl_bids', 'LEFT OUTER JOIN', 't.task_id=b.bid_task_id', 'b');
		
		if (User::isWriter()) {
			$srch->addCondition('b.bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
		}
		else {
			$srch->addCondition('t.task_user_id', '=', User::getLoggedUserAttribute('user_id'));
		}
		
		if (User::isWriter()) {
			$srch->addMultipleFields(array('SUM(CASE WHEN t.task_status=1 AND b.bid_status=0 THEN 1 ELSE 0 END) AS orders_in_bidding', 'SUM(CASE WHEN t.task_status=2 AND b.bid_status=1 THEN 1 ELSE 0 END) AS orders_in_progress', 'SUM(CASE WHEN t.task_status=3 AND b.bid_status=1 THEN 1 ELSE 0 END) AS orders_completed'));
		}
		else {
			$srch->addMultipleFields(array('SUM(CASE WHEN t.task_status=1 THEN 1 ELSE 0 END) AS orders_in_bidding', 'SUM(CASE WHEN t.task_status=2 THEN 1 ELSE 0 END) AS orders_in_progress', 'SUM(CASE WHEN t.task_status=3 THEN 1 ELSE 0 END) AS orders_completed'));
		}
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	} */
	
	public function getCompletedOrdersCount() {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_tasks');
		
		$srch->addCondition('task_status', '=', 3);
		
		$srch->addFld('IFNULL(COUNT(*),0) AS orders_completed');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return $row['orders_completed'];
	}
}