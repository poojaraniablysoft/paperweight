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
	
	public function update($data) {
        $record = new TableRecord('tbl_withdrawal_requests');
		
        $record->setFlds(array('req_status'=>$data['req_status'], 'req_transaction_id'=>$data['trans_id'], 'req_comments'=>$data['req_comments']));
		$record->setFldValue( 'req_approved_on', 'mysql_func_now()', true);
        
		if (!$record->update(array('smt'=>'req_id = ?', 'vals'=>array($data['req_id'])))) {
			$this->error = $record->getError();
			return false;
		}
        
        return true;
	}
	
	public function getWithdrawalRequests($page) {
		$db = &Syspage::getdb();
		
		$arr_req = array();
		
		$pagesize = 10;
		
		$srch = new SearchBase('tbl_withdrawal_requests', 'w');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'w.req_user_id=u.user_id', 'u');
		
		$srch->addOrder('w.req_date', 'DESC');
		
		$srch->setPageSize($pagesize);
		
		$srch->setPageNumber($page);
		
		$srch->addMultipleFields(array('w.*', 'u.user_screen_name'));
		
		$rs = $srch->getResultSet();
		
		$arr_req['data']			= $db->fetch_all($rs);
		$arr_req['total_records']	= $srch->recordCount();
		$arr_req['pagesize']		= $pagesize;
		$arr_req['pages']			= $srch->pages();
		$arr_req['page']			= $page;
		
		return $arr_req;
	}
	
	public function getRequestDetails($req_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_withdrawal_requests', 'w');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'w.req_user_id=u.user_id', 'u');
		
		$srch->addCondition('w.req_id', '=', $req_id);
		
		$srch->addMultipleFields(array('w.*', 'u.user_ref_id', 'u.user_screen_name', 'u.user_first_name', 'u.user_email'));
		
		$rs = $srch->getResultSet();
		
		if (!$row = $db->fetch($rs)) return false;
		
		return $row;
	}
}