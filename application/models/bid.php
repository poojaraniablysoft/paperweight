<?php
class Bid extends Model {
	protected $error;
	private $bidId;
	
	private $wallet;
	
	public function __construct() {
		parent::__construct();
		
		$this->bidId = 0;
		$this->wallet = new Wallet();
	}
	
	public function getError(){
        return $this->error;
    }
	
	public function getBidId() {
		return $this->bidId;
	}
	
	public function Showbids($filter,$page) {
		$db = &Syspage::getdb();
		
		global $bid_status;
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		//$pagesize = 5;
		$pagesize = CONF_PAGINATION_LIMIT;
		
		$records = new SearchBase('tbl_tasks', 't');
		
		$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
		$records->joinTable('tbl_bids','INNER JOIN','b.bid_task_id = t.task_id','b');
		$records->joinTable('tbl_users', 'INNER JOIN', 't.task_user_id=u.user_id', 'u');
		$records->addCondition('b.bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
		
		switch (strtolower($filter)) {
			case 'mine':
				$cnd = $records->addCondition('b.bid_status', '=', 0);
				$cnd->attachCondition('b.bid_status', '=', 2);
				$records->addCondition('t.task_status', 'IN', array(1,2,3));
				break;
			case 'ongoing':
				$records->addCondition('b.bid_status', '=', 1);
				$records->addCondition('t.task_status', '=', 2);
				break;
			case 'completed':
				$records->addCondition('t.task_status', '=', 3);
				$records->addCondition('b.bid_status', '=', 1);
				break;
			case 'cancelled':
				$cnd = $records->addCondition('b.bid_status', '=', 3);
				$cnd->attachCondition('t.task_status', '=', 4);
				break;
		}
		
		$records->addMultipleFields(array('t.task_id', 'task_ref_id', 't.task_user_id', 't.task_writer_id','u.user_first_name' ,'u.user_screen_name', 't.task_status', 'p.paptype_name', 't.task_topic', 't.task_pages', 't.task_words_per_page', 't.task_due_date', 't.task_service_type', 't.task_citstyle_id', 't.task_posted_on', 't.task_completed_on', 'b.bid_id', 'b.bid_user_id', 'b.bid_price', 'b.bid_status', 'COUNT(b.bid_id) AS total_bids'));
		
		$records->addGroupBy('t.task_id');
		$records->addOrder('b.bid_date','desc');
		
		$records->setPageNumber($page);
		$records->setPageSize($pagesize);
		
		$rs = $records->getResultSet();
		
		$orders = $db->fetch_all($rs);
		
		foreach ($orders as $key=>$arr) {
			
			#$orders[$key]['task_due_date']		= displayDate($arr['task_due_date'], true, true, CONF_TIMEZONE);
			$orders[$key]['task_due_date']		= $arr['task_due_date'];
			$orders[$key]['task_completed_on']	= displayDate($arr['task_completed_on'], true, true, CONF_TIMEZONE);
			$orders[$key]['time_left']			= html_entity_decode(time_diff($arr['task_due_date']));
			$orders[$key]['total_bid_amount']	= number_format(getAmountPayableToWriter($arr['bid_price']),2);
			$orders[$key]['amount_paid']		= $this->wallet->getTaskAmountPaid($arr['task_id'],$arr['bid_user_id']);
		}
		
		$rows['orders']	= $orders;
		$rows['page']	= $page;
		$rows['pages']	= $records->pages();
		
		return $rows;
	}
	
	public function bidsReceived() {		//bids received by a customer
		$db = &Syspage::getdb();
		
		$records = new SearchBase('tbl_tasks', 't');
		
		$records->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 'p.paptype_id = t.task_paptype_id', 'p');
		$records->joinTable('tbl_bids', 'INNER JOIN', 'b.bid_task_id = t.task_id', 'b');
		$records->joinTable('tbl_users', 'INNER JOIN', 'b.bid_user_id=u.user_id', 'u');
		
		$records->addCondition('t.task_user_id', '=', User::getLoggedUserAttribute('user_id'));
		$records->addCondition('t.task_status', '=', 1);
		
		$records->addOrder('b.bid_date', 'desc');
		$records->addGroupBy('t.task_id');
		
		$records->addMultipleFields( array ( 't.task_id', 'task_ref_id', 't.task_user_id', 'u.user_screen_name', 't.task_status', 'p.paptype_name', 't.task_topic', 't.task_pages', 't.task_due_date', 't.task_service_type', 't.task_citstyle_id', 't.task_posted_on', 't.task_words_per_page', 'b.bid_id', 'b.bid_status' ) );
		
		$rs = $records->getResultSet();
		
		return $db->fetch_all($rs);
	}
	
	function bid($data) {
						
		$record = new TableRecord('tbl_bids');
		
        $record->setFldValue('bid_user_id', User::getLoggedUserAttribute('user_id'));
        $record->setFldValue('bid_task_id', $data['bid_task_id']);
		$record->setFldValue('bid_price', $data['bid_price']);
		$record->setFldValue('bid_date', 'mysql_func_NOW()', true);
			
		if (!$record->addNew()) {
			$this->error = $record->getError();
			return false;
		}else {
			Task::sendEmail(array(
				'temp_num'=>21,
				'to'=>CONF_ADMIN_EMAIL_ID,
				'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
				'order_ref_id'=>$data['task_ref_id'],
				'user_email'=>User::getLoggedUserAttribute('user_email')
			));
			
			Task::sendEmail(array(
				'temp_num'=>22,
				'to'=>$data{'customer_email'},
				'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
				'order_ref_id'=>$data['task_ref_id'],
				'user_first_name'=>$data{'customer_first_name'},
				'user_email'=>$data{'customer_email'},
				'bid_price'=>CONF_CURRENCY.$data['bid_price'],
				'writer_pitch'=>CONF_CURRENCY.getAmountPayableToWriter($data['bid_price']),
				'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($data['bid_task_id']))
			));
		}
		
		return true;
	}
	
	function bid_update($data) {
        $record = new TableRecord('tbl_bids');
		
		$record->setFldValue('bid_price', $data['bid_price']);
		$record->setFldValue('bid_edited', 'mysql_func_NOW()', true);
		
		if (!$record->update(array('smt'=>'bid_id = ?', 'vals'=>array($data['bid_id'])))) {
			$this->error = $record->getError();
			return false;
		}else {
			Task::sendEmail(array(
				'temp_num'=>26,
				'to'=>$data['customer_email'],
				'user_first_name'=>$data['customer_first_name'],
				'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
				'order_ref_id'=>$data['task_ref_id'],
				'bid_price'=>CONF_CURRENCY.$data['bid_price'],
				'old_bid_price'=>CONF_CURRENCY.$data['old_bid_price'],
				'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($data['bid_task_id']))
			));
		}
		
        return true;
	}
	
	function bid_remove($bid_id) {                     // Delete a bid
		$db = &Syspage::getdb();
		return $db->deleteRecords('tbl_bids',array('smt'=>'bid_id = ? ', 'vals'=>array($bid_id)));
	}
	
	function reject_bid($bid_id) {
		$db = &Syspage::getdb();
		
		$bid_id = intval($bid_id);
		if ($bid_id < 1) return false;
		
        $record = new TableRecord('tbl_bids');
		
		$record->setFldValue('bid_status', 2);
		
		if (!$record->update(array('smt'=>'bid_id = ?', 'vals'=>array($bid_id)))) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
	
	static function getnumbids($id) {
		$srch = new SearchBase('tbl_bids');
		
		$srch->addCondition('bid_task_id', '=', $id);
		$srch->addCondition('bid_status', '!=', 3);
		
		$srch->getResultSet();
		
		return $srch->recordCount();
	}
	
	/* Function to fetch bids for a particular order */
	public function getOrderBids($task_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_bids', 'b');
		
		$srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'b.bid_user_id=u.user_id', 'u');
		
		$srch->addCondition('b.bid_task_id', '=', $task_id);
		
		$srch->addOrder('b.bid_edited', 'desc');
		$srch->addOrder('b.bid_date', 'desc');
		
		$srch->addMultipleFields(array('b.bid_id', 'b.bid_user_id', 'b.bid_price', 'b.bid_preview', 'b.bid_status', 'u.user_screen_name AS writer','u.user_email'));
		
		$rs = $srch->getResultSet();
		
		return $db->fetch_all($rs);
	}
	
	/* Cancel all the bids in case an order is cancelled. */
	public function cancelOrderBids($task_id) {
		$db = &Syspage::getdb();
		
		$task_id = intval($task_id);
		if ($task_id < 1) return false;
		
        $record = new TableRecord('tbl_bids');
		
		$record->setFldValue('bid_status', 3);
		
		if (!$record->update(array('smt'=>'bid_task_id = ?', 'vals'=>array($task_id)))) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
	
	/* Function to fetch details of particular bid */
	public function getBidDetailsById($bid_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_bids', 'b');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'b.bid_user_id=u.user_id', 'u');
		$srch->joinTable('tbl_tasks', 'INNER JOIN', 'b.bid_task_id=t.task_id', 't');
		
		$srch->addCondition('b.bid_id','=',$bid_id);
		
		$srch->addMultipleFields(array('b.bid_id','b.bid_price','b.bid_preview','b.bid_user_id', 'b.bid_status', 'u.user_screen_name AS writer','u.user_email AS writer_email','b.bid_task_id','t.task_topic','t.task_due_date','t.task_pages','t.task_ref_id','t.task_user_id','t.task_status','t.task_words_per_page','t.task_instructions'));
		
		$rs = $srch->getResultSet();
		
		if(!$arr_bid = $db->fetch($rs)) return false;
		
		$arr_bid['time_left'] = time_diff($arr_bid['task_due_date']);
		
		return $arr_bid;
	}
	
	public function updatePreview($bid_id,$preview_text) {
		$db = &Syspage::getdb();
		
		$record = new TableRecord('tbl_bids');
		
        $record->setFldValue('bid_preview', $preview_text);
		
		if (!$record->update(array('smt'=>'bid_id = ?', 'vals'=>array($bid_id)))) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
	
	public function writerCanBid($task_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_bids');
		
		$srch->addCondition('bid_task_id', '=', $task_id);
		$srch->addCondition('bid_user_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('bid_status', 'IN', array(0,1,2));
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return intval($row['bid_id']);
	}
	
	function bid_rate() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_infotips');
		$srch->addCondition('infotip_id','=',3);
		$srch->addFld('infotip_description');
		$rs = $srch->getResultSet();
		return $db->fetch($rs);	
	}
	
	function getAssignedBidDetails($task_id,$user_id = 0) {
		$db = &Syspage::getdb();
		if($user_id==0){
			$user_id = User::getLoggedUserAttribute('user_id');
		}
		$task_id = intval($task_id);
		$srch = new SearchBase('tbl_bids');
		
		
		$srch->addCondition('bid_task_id','=',$task_id);
		$srch->addCondition('bid_status','=',1);
		
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)){
			$this->error = $db->getError();
			return;
		}
		
		return $row;
	}
	
	function updatedMilestoneRequest($data) {
		
		$db = &Syspage::getdb();
		$record = new TableRecord('tbl_bids');
		$record->setFldValue('cus_req_to_upload_again',$data['cus_req_to_upload_again']);
		
		if(!$record->update(array('smt'=>'bid_id = ?', 'vals'=>array($data['bid_id'])))) {
			$this->error = $record->getError();
			return;
		}
		return true;
	}
}