<?php
class Test extends Model {
	private $user_id;
	private $test_id;
	protected $error;
	
	function __construct($id) {
        if (!is_numeric($id)) $id=0;
        
		$this->user_id = intval($id);
		
		if ($this->user_id < 1 && User::isUserLogged()){
            $this->user_id = User::getLoggedUserAttribute('user_id');
        }
		
        if (!($this->user_id > 0)){
            return;
        }
        
    }
	
	function getUserId(){
    	return $this->user_id;
    }
	
	function getTestId(){
    	return $this->test_id;
    }
    
    function getError(){
        return $this->error;
    }
	
	public function getTestQuesions($user_id){
		$db = &Syspage::getdb();
		
		$sql = $db->query("DELETE FROM tbl_user_test where test_user_id = $user_id");
		
		 $sql = $db->query("SELECT quest_id as test_question_id ,quest_ans as test_correct_answer FROM tbl_grammar_questions where quest_active=1 ORDER BY RAND() LIMIT 0 ,".CONF_QUES_DISPLAY); 
		/* $sql = $db->query("SELECT quest_id as test_question_id ,quest_ans as test_correct_answer FROM tbl_grammar_questions ORDER BY RAND() LIMIT 0 , 5"); */
		
		
		
		if(!$arr_que_id = $db->fetch_all($sql)) return false;
		$i = 1;
		foreach($arr_que_id as $value) {
			$value["test_user_id"] = $user_id;
			$value["test_quest_order"] = $i++;
			$insert_ques = $db->insert_from_array('tbl_user_test',$value);
		}
		
	}
	
	public function update_timer($time) {
		$record = new TableRecord('tbl_users');
		$record->setFldValue('user_test_end_time',$time);
			if(!$record->update(array('smt'=>'user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
				$this->error = $record->getError();
				return false;
			} 
			return true;
	}
	
	public function showQuetion($user_id,$ques_num) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_user_test','ut');
		
		$srch->joinTable('tbl_grammar_questions','INNER JOIN','gq.quest_id = ut.test_question_id','gq');
		
		$srch->addCondition('ut.test_user_id','=',$user_id);
		$srch->addMultipleFields(array('quest_question','quest_id as test_question_id','quest_opt1','quest_opt2','quest_opt3','quest_opt4'));
		
		$srch->addOrder('test_quest_order','ASC');
		
		$srch->setPageNumber($ques_num);
		$srch->setPageSize(1);
		
		$rs = $srch->getResultSet();
		
		$questions = $db->fetch_all($rs);
		
		$rows['question'] = $questions;
		$rows['pages']	  = $srch->pages();
		$rows['page']	  = $ques_num;
		
		return $rows;
	}
	
	public function updateAns($question_num,$ans=0) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_grammar_questions');
		$srch->addCondition('quest_id','=',$question_num);
		if($ans >0 && $ans <5) {
			$srch->addMultipleFields(array('quest_id','quest_question','quest_active','quest_opt'.$ans,'quest_opt1','quest_opt2','quest_opt3','quest_opt4','quest_ans'));
		}else {
			$srch->addMultipleFields(array('quest_id','quest_question','quest_active','quest_opt1','quest_opt2','quest_opt3','quest_opt4','quest_ans'));
		}
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			$this->error = $record->getError();
			return false;
		}
		
		$record = new TableRecord('tbl_user_test');
		if($ans >0 && $ans <5) {
			$record->setFldValue('test_answer',$ans);
			$record->setFldValue('test_answer_text',$row['quest_opt'.$ans]);
		}
		$record->setFldValue('test_question',$row['quest_question']);
		$record->setFldValue('test_correct_answer_text',$row['quest_opt'.$row['quest_ans']]);
		$record->setFldValue('test_replied_at','mysql_func_now()',true);
		if(!$record->update(array('smt'=>'test_user_id = ? and test_question_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'), $question_num), 'execute_mysql_functions'=>false))) {
			$this->error = $record->getError();
			return false;
		}
		return true;
	}
	
	public function getResult(){
		$db = &Syspage::getdb();
		$correctanscount = 0;
		$srch = new SearchBase('tbl_user_test');
		$srch->addMultipleFields(array('test_correct_answer','test_answer'));
		$srch->addCondition('test_user_id','=',User::getLoggedUserAttribute('user_id'));
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch_all($rs)) {
			return false;
		}
		foreach($row as $key=>$value){
			if($value['test_correct_answer']===$value['test_answer']){
				$correctanscount += 1;
			}			
		}
		
		if($correctanscount < CONF_CORRECT_ANS_REQUIRED) {
			$record = new TableRecord('tbl_user_profile');
			$record->setFldValue('user_test_passed',2);
			if(!$record->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
				$this->error = $record->getError();
				return false;
			} 
			return false;
		}else {
			$record = new TableRecord('tbl_user_profile');
			$record->setFldValue('user_test_passed',1);
			if(!$record->update(array('smt'=>'userprofile_user_id = ?', 'vals'=>array(User::getLoggedUserAttribute('user_id'))))) {
				$this->error = $record->getError();
				return false;
			} 
			return true;
		}
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
	
	function lastUpdatedId(){
		$db = &Syspage::getdb();
			
		$srch = new SearchBase('tbl_user_test');		
		$srch->addCondition('test_user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addDirectCondition('test_replied_at IS NULL');
		$srch->addOrder('test_quest_order','ASC');
		$srch->setPageSize(1);
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			return false;
		}
		return $row['test_quest_order'];
	}
}
?>