<?php
class Ratings extends Model {
	private $task;
	
	public function __construct() {
		parent::__construct();
		
		$this->task = new Task();
	}
	
	public function addRatings($data) {
		$record = new TableRecord('tbl_ratings');
		
		$record->setFldValue('rt_delivery_rating', $data['delivery']);	
		$record->setFldValue('rt_communication_rating', $data['communication']);	
		$record->setFldValue('rt_quality_rating', $data['quality']);
		$record->setFldValue('rt_user_id', $data['reviewee_id']);
		$record->setFldValue('rt_rater_id', User::getLoggedUserAttribute('user_id'));
		$record->setFldValue('rt_task_id', $data['task_id']);
		
		if (!$record->addNew()) return false;
		return true;
	}
	
	public function rateUser($data) {
		$record = new TableRecord('tbl_ratings');
		
		switch (strtolower($data['attribute'])) {
			case 'delivery':
				$record->setFldValue('rt_delivery_rating', $data['rating']);
				break;
			case 'communication':
				$record->setFldValue('rt_communication_rating', $data['rating']);
				break;
			case 'quality':
				$record->setFldValue('rt_quality_rating', $data['rating']);
				break;
		}
		
		 if ($this->hasAlreadyRatedUser('', $data) > 0) {
			if ($this->hasAlreadyRatedUser($data['attribute'],$data) > 0) {
				if (!$record->update(array('smt'=>'rt_id = ?', 'vals'=>array($this->hasAlreadyRatedUser($data['attribute'],$data))))) return false;
			}
			 else {
				return false;
			} 
		}
		else {
			$record->setFldValue('rt_user_id', $data['user_id']);
			$record->setFldValue('rt_rater_id', User::getLoggedUserAttribute('user_id'));
			$record->setFldValue('rt_task_id', $data['task_id']);
			
			if (!$record->addNew()) return false;
		}
		
		return true;
	}
	
	public function hasAlreadyRatedUser($attr,$data) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_ratings');
		
		$srch->addCondition('rt_user_id', '=', $data['user_id']);
		$srch->addCondition('rt_rater_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('rt_task_id', '=', $data['task_id']);
		
		/* if ($attr != '') {
			switch (strtolower($attr)) {
				case 'delivery':
					$srch->addCondition('rt_delivery_rating', '=', 0);
					break;
				case 'communication':
					$srch->addCondition('rt_communication_rating', '=', 0);
					break;
				case 'quality':
					$srch->addCondition('rt_quality_rating', '=', 0);
					break;
			}
		} */
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		return intval($row['rt_id']);
	}
	
	public function getTaskRating($task_id,$user_id) {
		$db = &Syspage::getdb();
		
		$task_id = intval($task_id);
		if ($task_id < 1) return;
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$arr_order = $this->task->getOrderDetails($task_id);
		
		if ($arr_order === false || $arr_order['task_status'] != 3) return false;
		
		if (User::isWriter($user_id) && $arr_order['bid_user_id'] != $user_id) return false;
		if (!User::isWriter($user_id) && $arr_order['task_user_id'] != $user_id) return false;		
		
		$srch = new SearchBase('tbl_ratings');
		
		$srch->addCondition('rt_task_id', '=', $task_id);
		
		if (User::isWriter($user_id)) {
			$srch->addCondition('rt_rater_id', '=', $arr_order['bid_user_id']);
		}
		else {
			$srch->addCondition('rt_rater_id', '=', $arr_order['task_user_id']);
		}
		
		$srch->addMultipleFields(array('IFNULL(TRUNCATE(ROUND((((rt_delivery_rating + rt_communication_rating + rt_quality_rating)/3)*2))/2, 1), 0) AS average_rating', 'IFNULL(rt_delivery_rating, 0) AS delivery_rating', 'IFNULL(rt_communication_rating, 0) AS communication_rating', 'IFNULL(rt_quality_rating, 0) AS quality_rating'));
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	}
	
	public function getAvgUserRating($user_id) {
		$db = &Syspage::getdb();
		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');
		
		$srch = new SearchBase('tbl_ratings', 'r');
		
		$srch->addCondition('r.rt_user_id', '=', $user_id);
		
		$srch->addFld('IFNULL((r.rt_delivery_rating + r.rt_communication_rating + r.rt_quality_rating)/3, 0) AS rating');
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		
		$qry = $db->query("SELECT IFNULL(TRUNCATE(ROUND(((AVG(dr.rating))*2))/2, 1), 0) AS avg_rating FROM (" . $srch->getQuery() . ") dr");
		
		$data = $db->fetch($qry);
		
		return $data['avg_rating'];
	}		function getAvgSystemWriterRatings() {		$db = &Syspage::getdb();		$srch = new SearchBase('tbl_ratings', 'r');			$srch->addFld('IFNULL((r.rt_delivery_rating + r.rt_communication_rating + r.rt_quality_rating)/3, 0) AS rating');				$srch->doNotCalculateRecords();		$srch->doNotLimitRecords();					$qry = $db->query("SELECT IFNULL(TRUNCATE(((AVG(dr.rating)*2))/2, 1), 0) AS avg_rating FROM (" . $srch->getQuery() . ") dr");				$data = $db->fetch($qry);			return $data['avg_rating'];	}
}