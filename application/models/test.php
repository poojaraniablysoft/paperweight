<?php
class Test extends Model {
	protected $error;	
	function __construct() {
        parent::__construct();
    }    
    function getError() {
        return $this->error;
    }	
	public function getQuestions() {
		$db = &Syspage::getdb();		
		$data = array();		
		$srch = new SearchBase('tbl_grammar_questions');		
		$srch->addCondition('quest_active', '=', 1);		
		$rs = $srch->getResultSet();		
		$data = $db->fetch_all($rs);		
		return $data;
	}	
	public function setTestQuestions() {
		$db = &Syspage::getdb();		
		$db->query("DELETE FROM tbl_user_test where test_user_id = " . User::getLoggedUserAttribute('user_id'));
		$sql = $db->query("SELECT quest_id as test_question_id, quest_ans as test_correct_answer FROM tbl_grammar_questions where quest_active=1 ORDER BY RAND() LIMIT 0 ," . CONF_QUES_DISPLAY);				if (!$arr_ques = $db->fetch_all($sql)) return false;
		$i = 1;		
		foreach ($arr_ques as $arr) {
			$arr["test_user_id"]		= User::getLoggedUserAttribute('user_id');			$arr["test_quest_order"]	= $i++;			
			if (!$db->insert_from_array('tbl_user_test', $arr)) {
				$this->error = $db->getError();
				return false;			}
		}		
		return true;	}	
	public function updateRemainingTime($time_left) {
		$record = new TableRecord('tbl_users');		
		$record->setFldValue('user_test_end_time', $time_left);		
		if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {			$this->error = $record->getError();			return false;
		}		
		return true;
	}	
	public function showQuestion($ques_num) {
		$db = &Syspage::getdb();		
		$srch = new SearchBase('tbl_user_test','ut');		
		$srch->joinTable('tbl_grammar_questions','INNER JOIN','gq.quest_id = ut.test_question_id','gq');
		$srch->addCondition('ut.test_user_id', '=', User::getLoggedUserAttribute('user_id'));		$srch->addCondition('mysql_func_UNIX_TIMESTAMP(ut.test_replied_at)', '=', 0, 'AND', true);
		//$srch->addCondition('ut.test_replied_at', '=', 0, 'AND', true);		$srch->addCondition('test_quest_order', '=', $ques_num);
		$srch->addMultipleFields(array('quest_question','quest_id as test_question_id','quest_opt1','quest_opt2','quest_opt3','quest_opt4', 'ut.test_quest_order'));
		$rs = $srch->getResultSet();
		if (!$row = $db->fetch($rs)) {
			if ($ques_num < CONF_QUES_DISPLAY) {
				$ques_num += 1;
				return $this->showQuestion($ques_num);
			}
			else {
				return false;
			}
		}		
		return $row;
	}	
	private function validateQuestion($ques_id) {
		$db = &Syspage::getdb();		
		$srch = new SearchBase('tbl_user_test');		
		$srch->addCondition('test_user_id', '=', User::getLoggedUserAttribute('user_id'));		$srch->addCondition('test_question_id', '=', $ques_id);
		$rs = $srch->getResultSet();
		if (!$db->fetch($rs)) return false;
		return true;
	}	
	public function getQuestionDetails($ques_id) {
		$db = &Syspage::getdb();		
		$srch = new SearchBase('tbl_grammar_questions');		
		$srch->addCondition('quest_id', '=', $ques_id);		
		$rs = $srch->getResultSet();		
		if (!$data = $db->fetch($rs)) return false;		
		return $data;
	}	
	public function updateAns($ques_id,$ans) {
		$db = &Syspage::getdb();		
		if (!$this->validateQuestion($ques_id)) return false;		
		$arr_ques = $this->getQuestionDetails($ques_id);
		if ($arr_ques === false) return false;		
		$record = new TableRecord('tbl_user_test');		
		$record->setFldValue('test_answer', $ans);
		$record->setFldValue('test_answer_text', $arr_ques['quest_opt' . $ans]);				$record->setFldValue('test_question', $arr_ques['quest_question']);		$record->setFldValue('test_correct_answer_text', $arr_ques['quest_opt' . $arr_ques['quest_ans']]);		$record->setFldValue('test_replied_at', 'mysql_func_now()', true);
		if (!$record->update(array('smt'=>'test_user_id = ? and test_question_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'),$ques_id), 'execute_mysql_functions'=>true))) {			$this->error = $record->getError();
			return false;
		}
		return true;
	}	
	public function updateTestResult($status) {
		$record = new TableRecord('tbl_user_profile');
		$record->setFldValue('user_test_passed', $status);
		if (!$record->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {			$this->error = $record->getError();			return false;		}
		return true;
	}	
	public function getResult() {
		$db = &Syspage::getdb();		
		$correctanscount = 0;
		$srch = new SearchBase('tbl_user_test');		
		$srch->addCondition('test_user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addMultipleFields(array('test_correct_answer','test_answer'));
		$rs = $srch->getResultSet();
		if (!$row = $db->fetch_all($rs)) return false;
		foreach($row as $key=>$value){			if ($value['test_correct_answer'] == $value['test_answer']) {				$correctanscount += 1;			}					}		
		//update test result
		$status = $correctanscount < CONF_CORRECT_ANS_REQUIRED ? 2 : 1;	
		if (!$this->updateTestResult($status)) return false;
		if ($status == 2) {	$this->resetTest();}
		return $status;
	}
	function isUserFailed($user_id) {
		$db = &Syspage::getdb();		
		$user_id = intval($user_id);
		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');		
		$srch = new SearchBase('tbl_user_profile');	
		$srch->addCondition('userprofile_user_id', '=', $user_id);
		$srch->addCondition('user_test_passed', '=', 2);
		$rs = $srch->getResultSet();		
		if (!$row = $db->fetch($rs)) return false;		
		return true;		
	}
	function isUserPassed($user_id) {		$db = &Syspage::getdb();				$user_id = intval($user_id);		if ($user_id < 1) $user_id = User::getLoggedUserAttribute('user_id');				$srch = new SearchBase('tbl_user_profile');			$srch->addCondition('userprofile_user_id', '=', $user_id);		$srch->addCondition('user_test_passed', '=', 1);		$rs = $srch->getResultSet();				if (!$row = $db->fetch($rs)) return false;				return true;	}
	static function lastUpdatedId() { 
		$db = &Syspage::getdb();		
		$srch = new SearchBase('tbl_user_test');		
		$srch->addCondition('test_user_id', '=', User::getLoggedUserAttribute('user_id'));
		$srch->addCondition('mysql_func_UNIX_TIMESTAMP(test_replied_at)', '=', 0, 'AND', true);
		//$srch->addCondition('test_replied_at', '=', 0, 'AND', true);
		
		$srch->addOrder('test_quest_order', 'ASC');		
		$srch->setPageSize(1);		
		$rs = $srch->getResultSet();		
		if (!$row = $db->fetch($rs)) {
			return false;
		}	
		return $row['test_quest_order'];
	}
	public function getRemainingTime() {			/* time left to finish the test */		$db = &Syspage::getdb();				$srch = new SearchBase('tbl_users');				$srch->addCondition('user_id', '=', User::getLoggedUserAttribute('user_id'));				$srch->addFld('user_test_end_time');				$rs = $srch->getResultSet();				if (!$row = $db->fetch($rs)) {			return false;		}				return $row['user_test_end_time'];	}
	/** set remaining time to 0 **/
	public function resetTest() {
		$db = &Syspage::getdb();		
		$record = new TableRecord('tbl_users');		
		$record->setFldValue('user_test_end_time', 0);		
		if (!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
			$this->error = $record->getError();
			return false;
		}	
		return true;
	}
}
?>