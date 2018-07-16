<?php
class Orders extends Model {
	protected $error;
	
    function __construct() {
        parent::__construct();
    }
	
	public function getError(){
        return $this->error;
    }
	
	public function Showbids($task_id) {
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		$page = intval($post['page']);                          // Current page
		$pagesize = 20;
		if (!($page > 0)) $page = 1;		
		
		$records = new SearchBase('tbl_tasks', 't');
		$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
		$records->joinTable('tbl_bids','LEFT OUTER JOIN','b.bid_task_id = t.task_id','b');
		$records->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id = b.bid_user_id','u');
		$records->addCondition('b.bid_task_id','=',$task_id);
		//$records->addCondition('t.task_status','=',1);
		$records->addMultipleFields(array('t.task_id','t.task_user_id','t.task_status','p.paptype_name','t.task_topic','t.task_pages','t.task_due_date','t.task_service_type','t.task_citstyle_id','t.task_posted_on','u.user_screen_name','b.bid_id','b.bid_price','b.bid_status'));
		$records->setPageNumber($page);
		$records->setPageSize($pagesize);
		$rs = $records->getResultSet();
		$data['result'] = $db->fetch_All($rs);
		
		
		
		$data['page'] =$page;
		$data['pages'] = $records->pages();
		
		$data['start_record']=($page-1)*$pagesize + 1;
		$data['pagesize']=$pagesize;
		
		$end_record = $page * $pagesize;
		$total_records = $records->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		
		$data['end_record'] = $end_record;
		$data['total_records'] = $total_records;
		
		return $data;
	}
	
	public function biddetail($bid_id) {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_tasks', 't');
		$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
		$records->joinTable('tbl_bids','LEFT OUTER JOIN','b.bid_task_id = t.task_id','b');
		$records->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id = b.bid_user_id','u');
		$records->joinTable('tbl_disciplines','LEFT OUTER JOIN','d.discipline_id = t.task_discipline_id','d');
		$records->joinTable('tbl_citation_styles','LEFT OUTER JOIN','c.citstyle_id = t.task_citstyle_id','c');
		$records->addCondition('b.bid_id','=',$bid_id);
		$records->addMultipleFields(array('t.task_id','t.task_ref_id','t.task_user_id','t.task_status','p.paptype_name','t.task_topic','t.task_pages','t.task_due_date','t.task_service_type','t.task_citstyle_id','t.task_posted_on','b.bid_id','b.bid_price','b.bid_status','u.user_screen_name','d.discipline_name','c.citstyle_name'));
		
		$rs = $records->getResultSet();
		return $db->fetch($rs);
	} 
	
	public function OrderDetails($id, $appendServiceName = true) {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_tasks','t');
		$records->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id = t.task_user_id','u');
		$records->joinTable('tbl_users','LEFT OUTER JOIN','u1.user_id = t.task_writer_id','u1');
		$records->joinTable('tbl_paper_types','LEFT OUTER JOIN','p.paptype_id = t.task_paptype_id','p');
		$records->joinTable('tbl_disciplines','LEFT OUTER JOIN','d.discipline_id = t.task_discipline_id','d');
		$records->joinTable('tbl_citation_styles','LEFT OUTER JOIN','c.citstyle_id = t.task_citstyle_id','c');
		$records->addMultipleFields(array('t.task_id','t.task_paptype_id','t.task_discipline_id','t.task_instructions','t.task_citstyle_id','t.task_ref_id','t.task_user_id','t.task_status','t.task_completed_on','p.paptype_name','t.task_topic','t.task_pages','t.task_due_date','t.task_service_type','t.task_service_type as task_service_type_id','c.citstyle_name','t.task_posted_on','u.user_screen_name','u.user_first_name as customer_first_name','u.user_last_name as customer_last_name','d.discipline_name','u1.user_screen_name as writer','t.task_desc_cancel_req','t.task_cancel_request','t.task_words_per_page','u.user_email as customer_email','u1.user_email as writer_email','u1.user_first_name as writer_first_name','u1.user_last_name as writer_last_name'));
		$records->addCondition('task_id','=',$id);
		$rs = $records->getResultSet();
		$row = $db->fetch($rs);
		if($row['task_service_type'] > 0)	{
			$srch_serv = new SearchBase('tbl_service_fields');
			$srch_serv->addOrder('service_name');
			$srch_serv->addMultipleFields(array('service_id', 'service_name'));
			//$srch_serv->addCondition('service_active','=',1);
			$srch_serv->addCondition('service_id','=',$row['task_service_type']);
			
			$rs_serv = $srch_serv->getResultSet();
			$serv = $db->fetch($rs_serv);
			//echo $row['task_service_type'];
			//echo "<pre>".print_r($serv,true)."</pre>";exit;
			if($row['task_service_type'] != $serv['service_id']){
				$row['task_service_type'] = 'Service is not available!';
				return $row;
			}
			$row['task_service_type'] = $serv['service_name'];
		}		
		
		#return $db->fetch($rs);
		return $row;
	}
	
	public function tasktopic($task_id) {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_tasks');
		$records->addMultipleFields(array('task_topic','task_pages','task_due_date','task_ref_id'));
		$records->addCondition('task_id','=',$task_id);
		$rs = $records->getResultSet();
		return $db->fetch($rs);
	}
	
	public function getMilestones($task_id) {
		$db = &Syspage::getdb();
		
		$records = new SearchBase('tbl_milestones');
		$records->addMultipleFields(array('mile_page','mile_words','mile_content','mile_updated_on','mile_status'));
		$records->addCondition('mile_task_id','=',$task_id);
		$rs = $records->getResultSet();
		return $db->fetch_all($rs);
	}
	
	public function getReviews($task_id,$rev_id) {
		$db = &Syspage::getdb();
		
		$records = new SearchBase('tbl_reviews','r');
		$records->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id = r.rev_reviewer_id','u');
		$records->addMultipleFields(array('r.rev_id','r.rev_reviewer_id','r.rev_task_id','r.rev_posted_on','r.rev_comments','u.user_screen_name','r.rev_status'));
		$records->addCondition('r.rev_task_id','=',$task_id);
		if($rev_id>0) {
		$records->addCondition('r.rev_id','=',$rev_id);
		}
		$rs = $records->getResultSet();
		return $db->fetch_all($rs);
	}
	
	public function updateReview($data) {
		$db = &Syspage::getdb();
		
		$records = new TableRecord('tbl_reviews');
		$records->setFldValue('rev_comments',$data['rev_comments']);
		
		if(!$records->update(array('smt'=>'rev_id = ?', 'vals'=>array($data['rev_id'])))) {
			$this->error = $db->getError();
			Message::addErrorMessage($this->error);
			return false;
		}
		return true;
	}
	
	public function getConversationHistory($bid_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_task_updates', 'm');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'm.tskmsg_user_id=u.user_id', 'u');
		
		$srch->addCondition('tskmsg_bid_id', '=', $bid_id);
		
		$srch->addOrder('tskmsg_date');
		
		$srch->addMultipleFields(array('m.*','u.user_screen_name as username'));
		
		$rs = $srch->getResultSet();
		
		return $db->fetch_all($rs);
	}
	
	public function addTask($data) {
		$task_id = intval($data['task_id']);
		//echo "<pre>".print_r($data,true)."</pre>";exit;
        $record = new TableRecord('tbl_tasks');       
        
		$record->assignValues($data);
		$record->setFldValue('task_finished_from_writer', 0);
		$record->setFldValue('task_due_date',$data['task_due_date']);
		//$record->setFldValue('task_posted_on', 'mysql_func_NOW()', true);
	
		$record->setFldValue('task_words_per_page', CONF_WORDS_PER_PAGE);
		
		if(!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
			$this->error = $record->getError();
			return false;
		}
		return true;
						
	}
		
	public function changeOrderStatus($task_id,$task_status) {
		
		$record = new TableRecord('tbl_tasks');       
       
		$record->setFldValue('task_status',$task_status);
		
		if(!$record->update(array('smt'=>'task_id = ?', 'vals'=>array($task_id)))) {
			$this->error = $record->getError();
			return false;
		}
		return true;
	}     	
	
}