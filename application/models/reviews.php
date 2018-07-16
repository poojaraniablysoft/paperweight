<?php
class Reviews extends Model {
	protected $error;
	private $task;
	
	public function __construct() {
		parent::__construct();
		
		$this->task = new Task();
	}
	
	public function getError() {
		return $this->error;
	}
	
	public function addReview($data) {
		$record = new TableRecord('tbl_reviews');
		
		$record->assignValues(array('rev_reviewee_id'=>$data['reviewee_id'], 'rev_reviewer_id'=>User::getLoggedUserAttribute('user_id'), 'rev_task_id'=>$data['task_id'],'rev_status'=>1, 'rev_posted_on'=>'mysql_func_NOW()', 'rev_comments'=>$data['comments'], 'rev_is_featured'=>0));
		
		if (!$record->addNew()) {
			$this->error = $record->getError();
			return false;
		}
		
		return true;
	}
	
	public function getTaskReview($task_id,$user_id) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$srch = new SearchBase('tbl_reviews');
		
		$srch->addCondition('rev_task_id', '=', $task_id);
		$srch->addCondition('rev_reviewer_id', '=', $user_id);
		$srch->addCondition('rev_status', '=', 1);
		
		$srch->addMultipleFields(array('rev_comments AS comments', 'rev_posted_on AS posted_on'));
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	}
	
	public function getReviewsPosted($user_id,$page) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		#$pagesize = 10;
		$pagesize = CONF_PAGINATION_LIMIT;
		
		$srch = new SearchBase('tbl_reviews','rv');
		$srch->addCondition('rv.rev_reviewer_id', '=', $user_id);
		
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id=rv.rev_reviewee_id','u');
		$srch->joinTable('tbl_tasks','INNER JOIN','t.task_id=rv.rev_task_id','t');
		$srch->joinTable('tbl_paper_types','LEFT OUTER JOIN','t.task_paptype_id=pt.paptype_id','pt');
		
		$srch->addCondition('rv.rev_status', '=', 1);
		$srch->addOrder('rv.rev_posted_on', 'desc');		
		
		$srch->addMultipleFields(array('rv.rev_comments AS comments', 'rv.rev_posted_on AS posted_on','rv.rev_reviewee_id','rv.rev_reviewer_id','u.user_screen_name as reviewee','t.task_status','t.task_pages','t.task_topic','t.task_ref_id','pt.paptype_name','t.task_id','rv.rev_status'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		//echo $srch->getQuery();
		$rs = $srch->getResultSet();
		$rows['reviews'] = $db->fetch_all($rs);
		$rows['pages']	= $srch->pages();
		$rows['page']	= $page;
		return $rows;
	}
	
	public function getPostedReviews($page,$pagesize,$is_featured=false) {
		$db = &Syspage::getdb();
		
		/* $user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id'); */
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		$pagesize = (intval($pagesize) == 0)?10:$pagesize;		
		
		$srch = new SearchBase('tbl_reviews','rv');
		//$srch->addCondition('rv.rev_reviewer_id', '=', $user_id);
		
		$srch->joinTable('tbl_users','LEFT OUTER JOIN','u.user_id=rv.rev_reviewer_id','u');
		$srch->joinTable('tbl_tasks','INNER JOIN','t.task_id=rv.rev_task_id','t');
		$srch->joinTable('tbl_paper_types','LEFT OUTER JOIN','t.task_paptype_id=pt.paptype_id','pt');
		
		if( $is_featured == true ) $srch->addCondition('rev_is_featured','=',1);
		$srch->addCondition('rv.rev_status', '=', 1);
		$srch->addOrder('rand()');
		
		$srch->addMultipleFields(array('rv.rev_comments AS comments', 'rv.rev_posted_on AS posted_on','rv.rev_reviewee_id','rv.rev_reviewer_id','u.user_id', 'u.user_screen_name as reviewee','u.user_first_name as customer','t.task_status','t.task_pages','t.task_topic','t.task_ref_id','pt.paptype_name','t.task_id','rv.rev_status'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		//echo $srch->getQuery();
		$rs = $srch->getResultSet();
		$rows['reviews'] = $db->fetch_all($rs);
		foreach($rows['reviews'] as $key=>$value) {
			
			if(!User::isWriter($value['rev_reviewer_id'])) {
				$rows['review'][] = $value;
			}
			
		}
		
		//echo "<pre>".print_r($rows['review'],true)."</pre>";exit;
		$rows['pages']	= $srch->pages();
		$rows['page']	= $page;
		return $rows;
	}
	
	public function getFeedbackReceived($user_id,$page,$param = array()) {
		$db = &Syspage::getdb();
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		//$pagesize = 2;
		$pagesize = CONF_PAGINATION_LIMIT;
		
		$srch = new SearchBase('tbl_reviews','rv');
		$srch->addCondition('rv.rev_reviewee_id', '=', $user_id);
		
		$srch->joinTable('tbl_users','INNER JOIN','u.user_id=rv.rev_reviewer_id','u');
		$srch->joinTable('tbl_tasks','INNER JOIN','t.task_id=rv.rev_task_id','t');
		$srch->joinTable('tbl_paper_types','INNER JOIN','t.task_paptype_id=pt.paptype_id','pt');
		$srch->joinTable('tbl_ratings','INNER JOIN','r.rt_user_id=rv.rev_reviewee_id and r.rt_task_id = rv.rev_task_id','r');
		//echo intval($param['disc_id']);exit;
		
		$srch->addCondition('rv.rev_status', '=', 1);
		if(intval($param['disp_id']) > 0) {
			$srch->addCondition('t.task_discipline_id','=',intval($param['disp_id']));
		}
		
		if($param['rating'] > 0) {
			if($param['rating'] > 4) {
				$srch->addDirectCondition('IFNULL(TRUNCATE(ROUND((((`rt_delivery_rating`+`rt_communication_rating`+`rt_quality_rating`)/3)*2))/2, 1), 0) > 4');
			}elseif($param['rating'] > 3){
				$srch->addDirectCondition('IFNULL(TRUNCATE(ROUND((((`rt_delivery_rating`+`rt_communication_rating`+`rt_quality_rating`)/3)*2))/2, 1), 0) < 4','AND');
				$srch->addDirectCondition('IFNULL(TRUNCATE(ROUND((((`rt_delivery_rating`+`rt_communication_rating`+`rt_quality_rating`)/3)*2))/2, 1), 0) > 3 ','AND');
			}else {
				$srch->addDirectCondition('IFNULL(TRUNCATE(ROUND((((`rt_delivery_rating`+`rt_communication_rating`+`rt_quality_rating`)/3)*2))/2, 1), 0) <= 3');
			}
		}
		
		$srch->addOrder('rv.rev_posted_on', 'DESC');
		$srch->addMultipleFields(array('rv.rev_comments AS comments', 'rv.rev_posted_on AS posted_on','rv.rev_reviewee_id','rv.rev_reviewer_id','IF(u.user_type=1,u.user_screen_name,u.user_first_name) as reviewee','t.task_status','t.task_pages','t.task_topic','t.task_ref_id','pt.paptype_name','t.task_id','rv.rev_status','IFNULL(TRUNCATE(ROUND((((`rt_delivery_rating`+`rt_communication_rating`+`rt_quality_rating`)/3)*2))/2, 1), 0) as average_rating'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		
		$rows['feedback'] 	= $db->fetch_all($rs);
		$rows['pages']		= $srch->pages();
		$rows['page']		= $page;
		$rows['total_records']		= $srch->recordCount();
		
		return $rows;
	}
	
	public function getReceivedFeedback($page,$is_featured=false,$pagesize) {
		$db = &Syspage::getdb();
		
	/* 	$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id'); */
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		$pagesize = (intval($pagesize) == 0)?10:$pagesize;		
		
		$srch = new SearchBase('tbl_reviews','rv');
		//$srch->addCondition('rv.rev_reviewee_id', '=', $user_id);
		
		#$srch->joinTable('tbl_users','INNER JOIN','u.user_id=rv.rev_reviewer_id','u');
		$srch->joinTable('tbl_users','INNER JOIN','u.user_id=rv.rev_reviewer_id','u');
		$srch->joinTable('tbl_tasks','INNER JOIN','t.task_id=rv.rev_task_id','t');
		$srch->joinTable('tbl_paper_types','INNER JOIN','t.task_paptype_id=pt.paptype_id','pt');
		
		if( $is_featured == true ) $srch->addCondition('rev_is_featured','=',1);
		
		$srch->addCondition('rv.rev_status', '=', 1);
		$srch->addOrder('rand()');
		#CONCAT(u.user_first_name, " ",u.user_last_name)
		$srch->addMultipleFields(array('rv.rev_comments AS comments', 'rv.rev_posted_on AS posted_on','rv.rev_reviewee_id','u.user_screen_name as reviewee','u.user_id', 't.task_status','t.task_pages','t.task_topic','t.task_ref_id','pt.paptype_name','t.task_id','rv.rev_status'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$rs = $srch->getResultSet();
		
		$rows['feedback'] 	= $db->fetch_all($rs);
		foreach($rows['feedback'] as $key=>$value) {
			
			if(!User::isWriter($value['rev_reviewee_id'])) {
				$rows['feedbacks'][$key] = $value;
			}
			
		}
		//echo "<pre>".print_r($rows['feedbacks'],true)."</pre>";exit;
		$rows['pages']		= $srch->pages();
		$rows['page']		= $page;
		
		return $rows;
	}
	
	public function getTestimonials($featured = false,$user_type=0) {			//Featured reviews
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_reviews', 'rv');
		
		$srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'u.user_id=rv.rev_reviewer_id', 'u');
		$srch->joinTable('tbl_users', 'LEFT OUTER JOIN', 'u1.user_id=rv.rev_reviewee_id','u1');
		$srch->joinTable('tbl_tasks', 'LEFT OUTER JOIN', 't.task_id=rv.rev_task_id', 't');
		$srch->joinTable('tbl_paper_types', 'LEFT OUTER JOIN', 't.task_paptype_id=pt.paptype_id', 'pt');
		if($featured == true) {
			$srch->addCondition('rv.rev_is_featured', '=', 1);
		}
		$srch->addCondition('rv.rev_status', '=', 1);
		$srch->addCondition('u.user_type', '=', $user_type);
		$srch->addOrder('rv.rev_posted_on','DESC');
		$srch->addGroupBy('t.task_id');
		$srch->addMultipleFields(array('rv.rev_comments','t.task_ref_id','u.user_id','u.user_screen_name','u1.user_screen_name as writer','t.task_topic','t.task_pages','pt.paptype_name','u1.user_id as reviewee_id','rv.rev_status'));
		
		//$srch->addOrder('rand()');
		$srch->setPageSize(CONF_PAGINATION_LIMIT);
		
		//echo $srch->getQuery();
		
		$rs = $srch->getResultSet();
		
		return $db->fetch_all($rs);
	}
}