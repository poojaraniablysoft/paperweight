<?php
class Home extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	function order_listing() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_tasks','t');
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id=t.task_user_id','u');		
		/* if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('t.task_topic', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition('u.user_screen_name','LIKE','%' . $post['keyword'] . '%');
		} */
		
		$srch->addCondition('t.task_topic','!=','');
					
		$pagesize = 5;
		
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('t.task_id','DESC');
		$srch->addCondition('t.task_status','=',0);
				
		$srch->addMultipleFields(array('t.task_id','t.task_topic','t.task_ref_id','t.task_status','u.user_screen_name','t.task_due_date'));
				
		$rs = $srch->getResultSet();
		$row = $db->fetch_all($rs);
		foreach ($row as $key=>$arr) {
			#$row[$key]['task_due_date']		= displayDate($arr['task_due_date'], true);
			$row[$key]['task_due_date']		= $arr['task_due_date'];
		}
		return $row;
	}
	
	public function test_taker($noLimit = false) {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
					
		$srch = new SearchBase('tbl_users', 'u');
		
		$srch->joinTable('tbl_user_profile','INNER JOIN','up.userprofile_user_id = u.user_id','up');
		$srch->addCondition('u.user_type', '=', 1);
		$srch->addCondition('u.user_is_approved', '=', 0);
		$srch->addCondition('up.user_test_passed', '=', 1);
		
		if($noLimit == false) {
			$pagesize = 5;
		}else{
			$srch->doNotLimitRecords();
			$srch->doNotCalculateRecords();
		}
		
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('u.user_regdate','DESC');
		$srch->addOrder('u.user_last_activity','DESC');
		
		$srch->addMultipleFields(array('u.user_id','u.user_email','u.user_screen_name','u.user_first_name','u.user_last_name','u.user_sex','u.user_country_id','u.user_city','u.user_regdate','u.user_type','u.user_email_verified','u.user_active','u.user_is_featured','u.user_is_approved'));
		//$srch->joinTablesAutomatically();
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		
		return $data;
	}
	
	public function userDeleteReq() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
					
		$srch = new SearchBase('tbl_users', 'u');
		
		$srch->addCondition('u.user_active', '=', 1);
		$srch->addCondition('u.user_deactivate_request', '=', 1);
				
		$pagesize = 5;
		
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('u.user_regdate','DESC');
		$srch->addOrder('u.user_last_activity','DESC');
		
		$srch->addMultipleFields(array('u.user_id','u.user_email','u.user_type','u.user_active'));
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		
		return $data;
	}
	
	
	public function orderCancelReq() {
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
					
		$srch = new SearchBase('tbl_tasks', 't');
		
		$srch->addCondition('task_cancel_request','=',1);
		$srch->addCondition('task_status','=',2);
				
		$pagesize = 5;
		
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('t.task_posted_on','DESC');
		$srch->addOrder('t.task_topic','DESC');
		
		$srch->addMultipleFields(array('t.task_id','t.task_ref_id','t.task_topic','t.task_due_date','t.task_status','t.task_posted_on'));
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		
		return $data;
		
	}
	
	
}