<?php 
class TestController extends LegitController {
	
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
    }
	
	public function default_action() {
		$db = &Syspage::getdb();
		
		$is_test_available = 1;
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (User::writerCanView()) redirectUser(generateUrl('user','dashboard'));
		if ($this->Test->isUserPassed()) redirectUser(generateUrl('sample'));
		if ($this->Test->isUserFailed()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$arr_questions = $this->Test->getQuestions();
		if (count($arr_questions) < CONF_QUES_DISPLAY) $is_test_available = 0;
				
		$test_time_left = $this->Test->getRemainingTime();		
		$remaining_time = (intval($test_time_left) > 0) ? $test_time_left : 0; 
		
		$latest_title = Cmsblock::getContent('grammar_test', 'title');
		$latest_content = Cmsblock::getContent('grammar_test', 'content');
		$latest_content = str_replace( array( '{quiz_time}', '{ques_display}', '{correct_ans_required}' ), array( CONF_QUIZ_TIME, CONF_QUES_DISPLAY, ( CONF_QUES_DISPLAY-CONF_CORRECT_ANS_REQUIRED ) ), $latest_content );
		
		$this->set('latest_content', $latest_content);
		$this->set('latest_title', $latest_title);
		
		$this->set('remaining_time', $remaining_time);
		$this->set('is_test_available', $is_test_available);
		
		$this->_template->render();
	}
	
	public function test_page() {
		$db = &Syspage::getdb();
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (User::writerCanView()) redirectUser(generateUrl('user','dashboard'));
		if ($this->Test->isUserPassed()) redirectUser(generateUrl('sample'));
		if ($this->Test->isUserFailed()) die( Utilities::getLabel( 'E_Invalid_request' ) );

		$arr_questions = $this->Test->getQuestions();
		if (count($arr_questions) < CONF_QUES_DISPLAY) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$test_time_left = $this->Test->getRemainingTime();			
		
		$end_time = (CONF_QUIZ_TIME*60) + time();
		
		if (!isset($_SESSION['test_end_time'])) {
			$_SESSION['test_end_time'] = $end_time;
		}
		
		if ($test_time_left > 0) {
			$remaining_time = $test_time_left;			
		}
		else {
			$remaining_time = $_SESSION['test_end_time'] - time();
			
			if (!$this->Test->setTestQuestions()) {
				unset($_SESSION['test_end_time']);
				
				Message::addErrorMessage($this->Test->getError());
				redirectUser(generateUrl('test'));
			}
		}
		
		$this->set('remaining_time', $remaining_time);
		
		$this->_template->render();
	}
	
	public function start_test() {
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$arr_questions = $this->Test->getQuestions();
		if (count($arr_questions) < CONF_QUES_DISPLAY) die(convertToJson(array('status'=>2, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		$ques_num = intval($post['quest']);
		if ($ques_num < 1 || $ques_num > CONF_QUES_DISPLAY) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Must_Logged_In' ) )));
		if (!User::isWriter()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		//if ($this->Test->isUserFailed()) die(convertToJson(array('status'=>0, 'msg'=>'You have failed in the grammar test.')));
		if (User::writerCanView()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if ($_SESSION['test_end_time'] < time()) {
			unset($_SESSION['test_end_time']);
			$this->Test->getResult();
			die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'L_Time_out' ) ) ) );
		}
		
		$data = $this->Test->showQuestion( $ques_num );
		
		if ($data === false) {
			if ($ques_num == 1) {
				die( convertToJson( array( 'status' => 2, 'msg' => Utilities::getLabel( 'L_Grammar_test_not_available' ) ) ) );
			}
			else {
				die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'L_No_more_questions' ) ) ) );
			}
		}
		
		die(convertToJson(array('status'=>1, 'msg'=>'', 'data'=>$data)));
	}
	
	public function get_result() {
		/* if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Must_Logged_In' ) )));
		if (!User::isWriter()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if (User::writerCanView()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		 */
		$res = $this->Test->getResult();
		
		if ($res === false) {
			die(convertToJson(array('status'=>0, 'msg'=>$this->Test->getError())));
		}
		
		if ($res == 2) {			//fail
			session_destroy();
			Task::sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'temp_num'=>52));
			die(convertToJson(array('status'=>2)));
		}
		else {
			unset($_SESSION['test_end_time']);
			
			Task::sendEmail(array('to'=>User::getLoggedUserAttribute('user_email'),'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'temp_num'=>51));
			Task::sendEmail(array('to'=>CONF_ADMIN_EMAIL_ID,'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'temp_num'=>27));
			
			die(convertToJson(array('status'=>1)));
		}
	}
	
	public function update_ans() {
		$post = Syspage::getPostedVar();
		
		$ques_id	= intval($post['ques_id']);
		$ans		= intval($post['ans']);
		
		if ($ques_id == 0 || $ans > 4) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Must_Logged_In' ) )));
		if (!User::isWriter()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		//if ($this->Test->isUserFailed()) die(convertToJson(array('status'=>0, 'msg'=>'You have been failed in the grammar test.')));
		if (User::writerCanView()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if ( $_SESSION['test_end_time'] < time( ) ) { 
			unset( $_SESSION['test_end_time'] );
			$this->Test->getResult( );
			die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'L_Time_out!' ) ) ) );
		}
		
		if (!$this->Test->updateAns($ques_id,$ans)) {
			die( convertToJson( array( 'status' => 0, 'msg' => $this->Test->getError( ) ) ) );
		}
		
		die( convertToJson( array( 'status' => 1, 'msg' => '' ) ) );
	}
	
	public function update_remaining_time( ) { 
		$post = Syspage::getPostedVar( );
		
		$remaining_time = intval( $post['remaining_time'] );
		
		if ( !User::isUserLogged( ) ) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Must_Logged_In' ) ) ) );
		if (!User::isWriter()) die( convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if (User::writerCanView()) die( convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));		
		
		if (!$this->Test->updateRemainingTime($remaining_time)) return false;
		
		return true;
	}
}