<?php
class MessagesController extends LegitController {
	private $bid;
	private $user;
	private $task;
    
	function __construct($model, $controller, $action){
        parent::__construct($model, $controller, $action);
		$this->bid = new Bid();
		$this->user = new User();
		$this->task = new Task();
    }
	
    public function default_action($bid_id) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (User::isWriter()) {if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$arr_bid			= array();
		$arr_conversation	= array();
		
		$bid_id = intval($bid_id);
		
		if ($bid_id > 0) {
			$arr_bid = $this->bid->getBidDetailsById($bid_id);
			
			if ($arr_bid['task_user_id'] != User::getLoggedUserAttribute('user_id') && $arr_bid['bid_user_id'] != User::getLoggedUserAttribute('user_id')) {
				die( Utilities::getLabel( 'E_Invalid_request' ) );
			}
		}
		
		$arr_users = $this->Messages->getPaginatedUsers('','',1);
		
		if(!User::isWriter()) {
			$first_order = Common::is_first_order_done();
			if($first_order == false) {
				redirectUser(generateUrl('task','my_orders'));
				return;
			}
		}
		
		if (!empty($arr_users['messages'])) {
			if ($bid_id < 1) {
				$bid_id		= $arr_users['messages'][0]['bid_id'];
				$arr_bid	= $this->bid->getBidDetailsById($bid_id);
			}
			
			//mark messages as read
			//$this->Messages->markMessagesAsRead($bid_id);
			//$arr_users[0]['unread_messages'] = 0;
			
			
			/* fetch last message sent date in a bid[ */
			foreach ($arr_users['messages'] as $key=>$ele) {
				$arr_users['messages'][$key]['last_updated_on'] = datesToInterval($ele['last_updated_on']);
				//$arr_search[$key]['last_updated_on'] = displayDate($ele['last_updated_on'], true, true, CONF_TIMEZONE);
				
				/* fetch last message sent date in a bid[ */
				$datesArr = explode(",",$ele['grouped_tskmsg_date']);
				rsort($datesArr);
				$lastMessageDate = $datesArr[0];
				$arr_users['messages'][$key]['last_updated_on'] = $lastMessageDate;
				/* ] */
				
				/* fetch last message in a bid[ */
				$lastMessageArr = explode( "######,", $ele['grouped_tskmsg_msg'] );
				$lastMessageArr = array_reverse($lastMessageArr);
				$lastMessage = str_replace("######", "", $lastMessageArr[0]);
				$arr_users['messages'][$key]['tskmsg_msg'] = $lastMessage;
				/* ] */
			}
			/* ] */
		}
		
		if ($bid_id > 0) {			
			//$arr_conversation	= $this->Messages->getConversationHistory($bid_id);
		}
		
		//last conversation held
		$arr_last_conversation['bid']			= $arr_bid;
		//$arr_last_conversation['conversation']	= $arr_conversation;
		
		$this->set('bid_id', $bid_id);
		$this->set('arr_users', $arr_users);
		$this->set('arr_last_conversation', $arr_last_conversation);
		$this->set('arr_unread_messages', $this->Messages->getUnreadMessageCount());
		
		$this->_template->render();
    }
	
	public function get_conversation_history() {
		$post = Syspage::getPostedVar();
		
		if ( !User::isUserLogged( ) ) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Session_Expire_Message' ) ) ) );
		
		$bid_id = intval($post['bid_id']);
		if ( $bid_id < 1 ) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Invalid_request' ) ) ) );
		
		$arr_bid = $this->bid->getBidDetailsById($bid_id);
		if ($arr_bid === false) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Invalid_request' ) ) ) );
		
		$arr_bid['task_due_date'] = displayDate($arr_bid['task_due_date'], true, true, CONF_TIMEZONE).' '.get_time_zone_abbr();
		 
		$arr_conversation = $this->Messages->getConversationHistory( $bid_id );
		
		if (count($arr_conversation) > 0) {
			foreach ($arr_conversation as $key=>$ele) {
				//$arr_conversation[$key]['tskmsg_date'] = displayDate($ele['tskmsg_date'], true, true, CONF_TIMEZONE);
				$arr_conversation[$key]['tskmsg_date'] = datesToInterval($ele['tskmsg_date']);
			}
		}
		
		$data['bid']			= $arr_bid;
		$data['conversation']	= $arr_conversation;
		
		$this->set('data', $data);
		
		$this->_template->render(false, false);
	}
	
	public function get_unread_message_count() {
	  
		if (!User::isUserLogged()) return;
 		if (!$arr = $this->Messages->getUnreadMessageCount()) return;
		
		die(convertToJson(array('unread_messages'=>intval($arr['unread_messages']), 'users'=>$this->Messages->getUsers())));
	}
	
	public function get_bid_updates() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) return;
		
		$arr_updates = $this->Messages->getBidUpdates($post);
		if ($arr_updates === false) return;
		
		foreach ($arr_updates as $key=>$ele) {
			//$arr_updates[$key]['tskmsg_date'] = displayDate($ele['tskmsg_date'], true, true, CONF_TIMEZONE);
			$arr_updates[$key]['tskmsg_date'] = datesToInterval($ele['tskmsg_date']);
		}
		
		$this->set('arr_updates', $arr_updates);
		
		$this->_template->render(false, false);
		
		//die(convertToJson(array('status'=>1, 'data'=>$arr_updates)));
	}
	
	public function post_message() {
		$post = Syspage::getPostedVar();
		
		$bid_id = intval($post['bid_id']);
		if ($bid_id < 1) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Invalid_request' ) ) ) );
		if(User::isUserReqDeactive()) {
			die( convertToJson( array('status' => 0, 'msg' => Utilities::getLabel( 'S_Deactivate_Account_Requested' ) ) ) );
		}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			die( convertToJson( array( 'status' => 0, 'msg' => Message::getHtml( ) ) ) );
		}
		$message = trim($post['message']);
		
		if ($message == '') die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'L_No_message_written' ) ) ) );
		
		$bid_details = $this->bid->getBidDetailsById($bid_id);
		
		if ($bid_details === false) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Invalid_request' ) ) ) );
		
		if (User::getLoggedUserAttribute('user_id') != $bid_details['bid_user_id'] && User::getLoggedUserAttribute('user_id') != $bid_details['task_user_id']) {
			die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		}
		
		if(User::getLoggedUserAttribute('user_id') == $bid_details['task_user_id']) {
			
			if (!$arr = $this->user->getUserDetailsById($bid_details['bid_user_id'])) return;
			$user_email = $arr['user_email'];
			$user_name = $arr['user_screen_name'];
			
		}else {
			if (!$arr = $this->user->getUserDetailsById($bid_details['task_user_id'])) return;
			$user_email = $arr['user_email'];
			$user_name = $arr['user_first_name'];
		}
		$online_status = strtotime($arr['user_last_activity']) < (time() - 60) ? 0 : 1;
		if($online_status != 1) {
			//die(convertToJson(array('status'=>0, 'msg'=>)));
			$param = array('to'=>$user_email,'temp_num'=>3,'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),'user_first_name'=>$user_name,'message'=>$post['message'],'order_ref_id'=>$bid_details['task_ref_id']);
			$this->task->sendEmail($param);
		}
				
		if (!$this->Messages->addMessage($post)) {
			die(convertToJson(array('status'=>0, 'msg'=>$this->Messages->getError())));
		}
		
		
		$msg_details = $this->Messages->getMessageDetails($this->Messages->getMsgId());
		
		$msg_details['tskmsg_date']	= datesToInterval($msg_details['tskmsg_date']);
		$msg_details['tskmsg_msg']	= htmlentities($msg_details['tskmsg_msg']);
		
		//die(convertToJson(array('status'=>1, 'data'=>$msg_details)));
		
	
		
		
		die(convertToJson(array('status'=>1, 'data'=>$msg_details))); 
	}
	
	public function search_conversations() {
		$post = Syspage::getPostedVar();
		
		$keyword = str_replace('#', '', $post['keyword']);
		$status  = $post['status'];
		$page  = $post['page'];
		$arr_search = $this->Messages->getPaginatedUsers($keyword,$status,$page);
		/* echo '<pre>';
		print_r( $arr_search );
		echo '</pre>'; die(); */
		
		if (count($arr_search['messages']) > 0) {
			foreach ($arr_search['messages'] as $key=>$ele) {
				$arr_search[$key]['last_updated_on'] = datesToInterval($ele['last_updated_on']);
				//$arr_search[$key]['last_updated_on'] = displayDate($ele['last_updated_on'], true, true, CONF_TIMEZONE);
				
				/* fetch last message sent date in a bid[ */
				$datesArr = explode(",",$ele['grouped_tskmsg_date']);
				rsort($datesArr);
				$lastMessageDate = $datesArr[0];
				$arr_search['messages'][$key]['last_updated_on'] = datesToInterval($lastMessageDate);
				/* ] */
				
				/* fetch last message in a bid[ */
				$lastMessageArr = explode( "######,", $ele['grouped_tskmsg_msg'] );
				$lastMessageArr = array_reverse($lastMessageArr);
				$lastMessage = str_replace("######", "", $lastMessageArr[0]);
				$arr_search['messages'][$key]['tskmsg_msg'] = $lastMessage;
				/* ] */
			}
		}
		
		$arr_search['str_paging'] = generatePagingStringHtml($arr_search['page'],$arr_search['pages'],"all_msg('" . $status . "',xxpagexx);");
		
		$this->set('arr_search', $arr_search);
		$this->_template->render(false, false);
	}
	
	public function mark_starred() {
		if ( !User::isUserLogged( ) ) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		if ( !User::isUserActive( ) ) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			die(convertToJson(array('status'=>0, 'msg'=>Message::getHtml())));			
		}
		$post = Syspage::getPostedVar();
		$bid_id = intval($post['conversation_id']);
		if($bid_id == 0) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		if(!$star = $this->Messages->updateStarStatus($bid_id)) {
			die(convertToJson(array('status'=>0, 'msg'=>$this->Messages->getError())));
		}
		
		die(convertToJson(array('status'=>1)));
	}
}