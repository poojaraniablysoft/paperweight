<?php
class RatingsController extends LegitController {
	private $task;
	
	public function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		
		$this->task = new Task();
	}
	
	public function rate_user() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			die(convertToJson(array('status'=>0, 'msg'=>Message::getHtml())));
		}
		$task_id	= intval($post['task_id']);
		$rating		= intval($post['rating']);
		
		$arr_order = $this->task->getOrderDetails($task_id);
		
		if ($arr_order === false || $arr_order['task_status'] != 3) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if (!User::isWriter() && $arr_order['task_user_id'] != User::getLoggedUserAttribute('user_id')) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if (User::isWriter() && intval($arr_order['bid_user_id']) != User::getLoggedUserAttribute('user_id')) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if ($rating < 1 || $rating > 5) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		$user_id = User::isWriter() ? $arr_order['task_user_id'] : $arr_order['bid_user_id'];	//User being rated
		
		$post['user_id'] = $user_id;
		
		if (!$this->Ratings->rateUser($post)) {
			die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Error_Message' ) )));
		}
		else {
			die(convertToJson(array('status'=>1, 'msg'=>'')));
		}
	}
}