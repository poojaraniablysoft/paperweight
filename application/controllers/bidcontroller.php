<?php
class BidController extends LegitController {
	protected $error;
	private $common;
	private $Task;
	private $ratings;
	private $Order;
	private $messages;
	
	public function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->common = new Common();
		$this->Task = new Task();
		$this->ratings = new Ratings();
		$this->Order = new Order();
		$this->messages	= new Messages();
	}
	
	function default_action() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ) */
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		redirectUser(generateUrl('bid','my_bids'));
    	$this->_template->render();
    }
	
	public function getError() {
		return $this->error;
	}
	
	/* Bids made by writer */
	public function my_bids($filter='') {
		//echo "<pre>".print_r($_SESSION,true)."</pre>";
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::writerCanView()) {redirectUser(generateUrl('test'));return;}
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		if (!in_array($filter, array('mine', 'ongoing', 'completed', 'cancelled', 'invitations'))) $filter = 'mine';
		
		$this->set('filter', $filter);
		$this->set('arr_bids', $this->Bid->Showbids('mine',1));
		$this->set('arr_ongoing_orders', $this->Bid->Showbids('ongoing',1));
		$this->set('arr_completed_orders', $this->Bid->Showbids('completed',1));
		$this->set('arr_cancelled_orders', $this->Bid->Showbids('cancelled',1));
		$this->set('arr_invitations', $this->Task->getWriterInvitations());
		
    	$this->_template->render();
	}
	
	public function get_writer_bids() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$filter = $post['filter'];
		if (!in_array($filter, array('mine', 'ongoing', 'completed', 'cancelled', 'invitations'))) $filter = 'mine';
		
		$arr_orders = (array) $this->Bid->Showbids($filter,$page);
		
		$arr_orders['str_paging'] = generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],"bids('" . $filter . "',xxpagexx);");
		
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render(false, false);
	}
	
	public function get_writer_invitations() {
		$post = Syspage::getPostedVar();
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_orders = $this->Task->getWriterInvitations($page);
		
		$arr_orders['str_paging'] = generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],"invitations(xxpagexx);");
		
		$this->set('arr_orders', $arr_orders);
		
		$this->_template->render(false, false);
	}
	
	function add($task_id,$bid_id=0) {
		$post = Syspage::getPostedVar();
		
		global $bid_status;
		global $invitation_status;
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::writerCanView()) redirectUser(generateUrl('test'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		$task_id = intval($task_id);
		if ($task_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$bid_id = intval($bid_id);
		
		if ($bid_id < 1) {
			$bid_id = $this->Bid->writerCanBid($task_id);
		}
		
		/* if ($bid_id > 0) {
			$bid_details = $this->Bid->getBidDetailsById($bid_id);
			if ($bid_details === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		} */
		
		$order_details = $this->Task->getTaskDetailsById($task_id);
		
		if ($order_details === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		//echo '<pre>' . print_r($order_details,true) . '</pre>';exit;
		if($order_details['task_status'] == 2 && $order_details['task_writer_id'] != User::getLoggedUserAttribute('user_id')){
			Message::addErrorMessage( Utilities::getLabel( 'E_Order_Assigned_To_Other_Writer' ) );
			redirectUser(generateUrl('task','orders'));
		}
		if($order_details['task_status'] == 2 && $order_details['task_writer_id'] == User::getLoggedUserAttribute('user_id')){
			Message::addErrorMessage( Utilities::getLabel( 'E_Already_Order_Assigned_To_You' ) );
			redirectUser(generateUrl('task','orders'));
		}
		if($order_details['task_status'] == 4 && $order_detals['task_writer_id'] != User::getLoggedUserAttribute('user_id')){
			Message::addErrorMessage( Utilities::getLabel( 'E_Order_Is_Cancelled' ) );
			redirectUser(generateUrl('task','orders'));
		}
		$frmbid = $this->getfrmbid($task_id);
		
		$fld_bid_id = $frmbid->getField('bid_id');
		$fld_bid_id->value = $bid_id;
		
		$fld_task_id = $frmbid->getField('bid_task_id');
		$fld_task_id->value = $order_details['task_id'];
		
		$fld_pages = $frmbid->getField('task_pages');
		$fld_pages->value = $order_details['task_pages'];
		
		$fld_total_price	= $frmbid->getField('price_to_you');
/* 		$fld_bid_price		= $frmbid->getField('your_bid'); */
 		$fld_task_budget	= $frmbid->getField('order_budget');
		
		if($bid_id > 0) {
			$bid_details = $this->Bid->getBidDetailsById($bid_id);
			if ($bid_details === false || $bid_details['bid_user_id'] != User::getLoggedUserAttribute('user_id')) die( Utilities::getLabel( 'E_Invalid_request' ) );
			
			$fld_pages = $frmbid->getField('bid_price');
			$fld_pages->value = $bid_details['bid_price'];
			
			$fld_task_budget->value = CONF_CURRENCY . '<span id="order_budget">'. number_format($order_details['task_budget'],2).'</span>';
			/* $fld_total_price->value	= CONF_CURRENCY . '<span id="total_price">' . number_format(($bid_details['bid_price']*$bid_details['task_pages']),2) . '</span>';
			*/
			$fld_total_price->value	= CONF_CURRENCY . '<span id="total_price">' . number_format(getAmountPayableToWriter($bid_details['bid_price']),2) . '</span>'; 
			//$fld_bid_price->value	= CONF_CURRENCY . '<span id="bid_price">' . number_format($bid_details['bid_price'],2) . '</span>';
		}
		else {
			$fld_task_budget->value = CONF_CURRENCY . '<span id="order_budget">'. number_format($order_details['task_budget'],2).'</span>';
			$fld_total_price->value	= CONF_CURRENCY . '<span id="total_price">0.00</span>';
			//$fld_bid_price->value	= CONF_CURRENCY . '<span id="bid_price">0.00</span>';
		}
		
		if ($bid_id > 0 && $bid_details['bid_status'] != 0) {
			$fld_status = $frmbid->addHTML('', '', '<span class="' . ($bid_details['bid_status'] == 1 ? 'greentag' : 'redtag') . '">' . $bid_status[$bid_details['bid_status']] . '</span>',true);
			$fld_status->html_before_field = '<div class="rejected">';
			$fld_status->html_after_field = '</div>';
		}
		else {
			if ($order_details['inv_status'] == 2 && $order_details['inv_writer_id'] == User::getLoggedUserAttribute('user_id')) {
				//$frmbid->addHTML('', '', '<span class="redtag">' . $invitation_status[$order_details['inv_status']] . '</span>');
				$this->set('invite_status',2);
			}
			else {
				$fld_btn = $frmbid->addSubmitButton('', 'btn_submit', '', 'btn_submit', 'onclick="confirmBid();return false;" class="theme-btn"');	
				$fld_btn->value = $bid_id > 0 ? strtoupper( Utilities::getLabel( 'L_update_your_bid' ) ) : strtoupper( Utilities::getLabel( 'L_place_bid' ) );
				$fld_btn->merge_caption =2;
			}
			
			if ($order_details['task_type'] == 1 && $order_details['inv_status'] == 0 && $order_details['inv_writer_id'] == User::getLoggedUserAttribute('user_id')) {
				$fld_btn->html_after_field = '<a class="theme-btn red-bg" href="javascript:void(0);" onclick="if(confirm(\'' . Utilities::getLabel( 'L_Alert_For_Decline_This_Offer' ) . '\')){document.location.href=\'' . generateUrl('task', 'decline_invitation', array($order_details['task_id'])) . '\';}">' . Utilities::getLabel( 'L_Decline' ) . '</a>';
			}
		}
		
		$arr_files					= $this->Task->getOrderFiles($task_id,1);
		$user_rating				= $this->ratings->getAvgUserRating($order_details['task_user_id']);
		$customer_activity_stats	= $this->Order->getCustomerActivityStats($order_details['task_user_id']);
		
		$this->set('bid_id', $bid_id);
		$this->set('completed_orders', $customer_activity_stats['orders_completed']);
		$this->set('user_rating', $user_rating);
		$this->set('files', $arr_files);
		$this->set('order', $order_details);
		$this->set('frmbid', $frmbid);
		$this->set('bid_details', $bid_details);
		
		$this->_template->render();	
	}
	
	function make_bid() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::writerCanView()) redirectUser(generateUrl('test'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		$post = Syspage::getPostedVar();
		
		$bid_id		= intval($post['bid_id']);
		$task_id	= intval($post['bid_task_id']);
		
		if ($task_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		if ($bid_id < 1) {
			$bid_id = $this->Bid->writerCanBid($task_id);
		}
		
		$frm = $this->getfrmbid();
		
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('bid', 'add');
			$this->setLegitControllerCommonData('', 'add');
            $this->add($task_id,$bid_id);
            return;
        }
		
		$task_details = $this->Task->getTaskDetailsById($task_id);
		if ($task_details === false || $task_details['task_status'] != 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		if ($task_details['inv_status'] == 2 && $task_details['inv_writer_id'] == User::getLoggedUserAttribute('user_id')) {
			die( Utilities::getLabel( 'E_Invalid_request' ) );
		}
		
		if ($bid_id > 0) {
			$arr_bid = $this->Bid->getBidDetailsById($bid_id);		
			if ($arr_bid === false || $arr_bid['bid_user_id'] != User::getLoggedUserAttribute('user_id') || in_array($arr_bid['bid_status'],array(1,2,3))) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
			if (!$this->Bid->bid_update(array('bid_id'=>$bid_id,'bid_task_id'=>$task_id,'bid_price'=>$post['bid_price'],'old_bid_price'=>$arr_bid['bid_price'],'task_ref_id'=>$task_details['task_ref_id'],'task_user_id'=>$task_details['task_user_id'],'customer_email'=>$task_details['customer_email'],'customer_first_name'=>$task_details['customer_first_name']))) {
				Message::addErrorMessage($this->Bid->getError());
			} else {
				Message::addMessage( Utilities::getLabel( 'L_Bid_updated_successfully' ) );
			}
		}
		else {
			if (!$this->Bid->bid(array('bid_task_id'=>$task_id,'bid_price'=>$post['bid_price'],'task_ref_id'=>$task_details['task_ref_id'],'task_user_id'=>$task_details['task_user_id'],'customer_email'=>$task_details['customer_email'],'customer_first_name'=>$task_details['customer_first_name']))) {
				Message::addErrorMessage($this->Bid->getError());
			} else {
				if ($task_details['inv_writer_id'] == User::getLoggedUserAttribute('user_id')) {
					if (!$this->Task->updateInvitationStatus($task_id,1)) {
						Message::addErrorMessage($this->Task->getError());
					}
				}
				
				Message::addMessage( Utilities::getLabel( 'L_Bid_successfully_posted' ) );
			}
		}
		
		redirectUser(generateUrl('bid','my_bids'));
	}
	
	/* Function For bid details and preview form */
	function bid_details($bid_id) {
		
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		$bid_id = intval($bid_id);
		if ($bid_id < 1) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		
		$arr_bid = $this->Bid->getBidDetailsById($bid_id);
		
		if ($arr_bid === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if ($arr_bid['bid_user_id'] != User::getLoggedUserAttribute('user_id')) die( Utilities::getLabel( 'E_Invalid_request' ) );
		
		$data = $this->Task->getTaskDetailsById($arr_bid['bid_task_id']);
		
		$files = $this->Task->getOrderFiles($arr_bid['bid_task_id'],1);
		
		if($data['task_status'] == 2 && $data['task_writer_id'] != User::getLoggedUserAttribute('user_id')) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Bid_Rejected_Order_Is_Assigned_To_Other_Writer' ) );
			redirectUser(generateUrl('bid','my_bids'));
		}
		
		if($data['task_status'] == 2 && $data['task_writer_id'] == User::getLoggedUserAttribute('user_id')) {
			Message::addMessage( Utilities::getLabel( 'L_Order_is_already_assigned_to_you' ) );
			redirectUser(generateUrl('task','order_process',array($data['task_id'])));
		}
		
		$frm = $this->getPreviewForm();		//preview form
		 
		$fld_id = $frm->getField('bid_id');
		$fld_id->value = $bid_id;
		
		$fld_id = $frm->getField('preview_text');
		$fld_id->value = html_entity_decode($arr_bid['bid_preview']);
		$customer_activity_stats	= $this->Order->getCustomerActivityStats($data['task_user_id']);
		$this->set('bid_id', $bid_id);
		$this->set('files', $files);
		$this->set('order', $data);
		$this->set('frm', $frm);
		$this->set('arr_bid', $arr_bid);
		$this->set('arr_messages', $this->messages->getConversationHistory($bid_id));
		$this->set('customer_rating', $this->ratings->getAvgUserRating($data['task_user_id']));
		$this->set('customer_orders_completed', $customer_activity_stats['orders_completed']);		
		$this->_template->render();	
	}
	
	private function getfrmbid($task_id) {
		$db = &Syspage::getdb();
		
		$frm = new Form('frmbid', 'frmbid');
		
		//$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
		//$frm->captionInSameCell();
		$frm->setFieldsPerRow(1);
		//$frm->setLeftColumnProperties('width="24%"');
        $frm->setJsErrorDisplay('alertbox');
		$frm->setAction(generateUrl('bid', 'make_bid'));
		
		$frm->addHiddenField('', 'bid_task_id', '', 'bid_task_id');
		$frm->addHiddenField('', 'bid_id', '', 'bid_id');
		$frm->addHiddenField('', 'task_pages', '', 'task_pages');
		$frm->addHiddenField('', 'task_comission', CONF_SERVICE_COMISSION, 'task_comission');
		
		$frm->addHTML('', '', '<h3>' . Utilities::getLabel( 'L_Bidding' ) . '</h3>',true);
		$fld = $frm->addRequiredField( Utilities::getLabel( 'L_Bid_To_Order' ) . ':', 'bid_price', '', 'bid_price', 'autocomplete="off"');		
		$fld->html_before_field = CONF_CURRENCY;
		$fld->requirements()->setFloat();		
		$fld->requirements()->setRange(1,10000);
		/* $fld->html_after_field = '<span>Recommended bid : Bid should be Positive<span>'; */
		$frm->addHTML( Utilities::getLabel( 'L_Order_Budget' ) . ': ','order_budget','');
		$bid_rate = $this->Bid->bid_rate();
		$frm->addHTML( Utilities::getLabel( 'L_Paid_to_you' ) . ':&nbsp;<div class="qmark tooltips">?<span>'.$bid_rate['infotip_description'].'</span></div>', 'price_to_you', '');
		
		//$frm->addHTML('Your Bid:', 'your_bid', '');
		
		return $frm;
	}
	
	/* Function for bid remove by writer */
	function bid_remove($bid_id) { 
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));return false;
		}
		$bid_id = intval($bid_id);
		if ($bid_id < 1) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		
		$arr_bids = $this->Bid->getBidDetailsById($bid_id);
		$arr_task = $this->Task->getTaskDetailsById($arr_bids['bid_task_id']);
		//echo "<pre>".print_r($arr_task,true)."</pre>";exit;
		if ($arr_bids === false) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if ($arr_bids['bid_user_id'] != User::getLoggedUserAttribute('user_id')) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if ($arr_bids['bid_status']==1) {
			Message::addMessage( Utilities::getLabel( 'E_Customer_Assigned_Order_To_You_Bid_Can_Not_Removed' ) );
			redirectUser(generateUrl('task','order_process',array($arr_bids['bid_task_id'])));
		}
		if(!$this->Bid->bid_remove($bid_id)) {
			Message::addErrorMessage( Utilities::getLabel( 'L_Not_Deleted' ) );
		}
		else {
			$this->Task->sendEmail(array(
					'temp_num'=>24,
					'to'=>$arr_task['customer_email'],
					'user_screen_name'=>User::getLoggedUserAttribute('user_screen_name'),
					'order_ref_id'=>$arr_task['task_ref_id'],
					'bid_price'=>CONF_CURRENCY.$arr_bids['bid_price'],
					'user_email'=>$arr_task['customer_email'],
					'user_first_name'=>$arr_task['customer_first_name'],
					'order_page_link'=>CONF_SITE_URL.generateUrl('task','order_bids',array($arr_task['task_id']))
				));
			Message::addMessage( Utilities::getLabel( 'L_Bid_successfully_deleted' ) );
		}
		
		redirectUser(generateUrl('bid','my_bids'));
	}
	
	public function update_preview() {
		 
		if (!User::isUserLogged()){
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			dieJsonError(Message::getHtml());
		}
		if (!User::isWriter()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			dieJsonError(Message::getHtml());
		}
		
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			dieJsonError(Message::getHtml());
		}
		$post = Syspage::getPostedVar();
		$frm = $this->getPreviewForm();
		if (!$frm->validate($post)){
            $errors = $frm->getValidationErrors();
            foreach ($errors as $error) Message::addErrorMessage($error);
            $this->_template = new Template('bid', 'my_bids');
			$this->setLegitControllerCommonData('', 'my_bids');
            $this->my_bids();
            return;
        }
		$bid_id = intval($post['bid_id']);
		if ($bid_id < 1) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			dieJsonError(Message::getHtml());
		}
		
		if(User::isUserReqDeactive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_Can_Not_Preview_Due_To_Delete_Account_Request' ) );
			dieJsonError(Message::getHtml());		 
		}
		
		//$preview_text = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i", '<$1$2>', $post['preview_text']);
		//if (strlen(strip_tags($preview_text)) == 0) die(convertToJson(array('status'=>0, 'msg'=>'No words written.')));
		$preview_text = $post['preview_text'];
		$preview_text = encodeHtmlCharacters($preview_text);
		
		if (!$this->Bid->updatePreview($bid_id,$preview_text)) die(convertToJson(array('status'=>0, 'msg'=>$this->Bid->getError())));
		
		Message::addMessage( Utilities::getLabel( 'L_Preview_saved' ) );
		dieJsonSuccess(Message::getHtml());
		
	}
	
	public function getPreviewForm() {
		$frm = new Form('frmOrderPreview','frmOrderPreview');
		
		$frm->setExtra('class="siteForm"');
		$frm->setOnSubmit(' updateOrderPreview(); return false;');
		$frm->setAction(generateUrl('bid', 'update_preview'));
		$frm->setTableProperties('width="60%" cellspacing="0" cellpadding="0" border="0" class="formTable editor_tbl"');
		//$frm->captionInSameCell(true);
		$frm->setLeftColumnProperties('width="40%"');
		
		$frm->setJsErrorDisplay('afterfield');
		
		$frm->addHiddenField('', 'bid_id', '', 'bid_id');
		
		$fld_text = $frm->addHtmlEditor( Utilities::getLabel( 'L_Note' ) . ' ', 'preview_text')->requirements()->setRequired(true);
		$fld_text->id = 'preview_text';
		
		$fld = $frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Save' ), 'btn_submit','class="theme-btn"');
		$fld->merge_caption = 2;
		return $frm;
	}
	
	public function confirm_bid() {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isWriter()) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		$post = Syspage::getPostedVar();
		//die("<pre>".print_r($post,true)."</pre>");
		$task_id	= intval($post['bid_task_id']);
		$bid_price	= (float) $post['bid_price'];
		
		if ($task_id < 1) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));		
		if ( $bid_price <= 0 ) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'N_Bid_Price_Should_Be_numeric' ) ) ) );
		
		$task_details = $this->Task->getTaskDetailsById($task_id);
		if ($task_details === false || $task_details['task_status'] != 1) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		
		$data = '<div class="leftPanel place-bid-pop">
					<h2 class="pagetitle">' . Utilities::getLabel( 'L_Bid_for_this_order' ) . '</h2>
					<div>
						<table cellspacing="0" cellpadding="0" border="0" class="place-bid">
							<tr>
								<td colspan="2">' . Utilities::getLabel( 'L_Alert_For_Confirm_That_You_Will_Complete_The_Order' ) . '</td>
							</tr>
							<tr>
								<td><strong>Type of paper</strong></td>
								<td>' . $task_details["paptype_name"] . '</td>
							</tr>
							<tr>
								<td><strong>Topic</strong></td>
								<td>' . $task_details["task_topic"] . '</td>
							</tr>
							<tr>
								<td><strong>Pages</strong></td>
								<td>' . $task_details["task_pages"].' pages / ' . ($task_details["task_pages"]*$task_details["task_words_per_page"]) . ' words</td>
							</tr>
							<tr>
								<td><strong>Deadline</strong></td>
								<td>' . displayDate( $task_details[ "task_due_date" ],true, true, CONF_TIMEZONE ) .' '.get_time_zone_abbr( ).' (' . time_diff(  $task_details[ "task_due_date" ] ) . ')</td>
							</tr>
							<tr>
								<td><strong>Paid to you</strong></td>
								<td>' . CONF_CURRENCY . number_format( getAmountPayableToWriter( $bid_price ), 2 ) . '</td>
							</tr>
							<tr>
								<td><strong>Your Bid</strong></td>
								<td>' . CONF_CURRENCY . number_format( $bid_price, 2 ) . '</td>
							</tr>					  
							<tr>
								<td colspan="2"><button type="button" value="' . Utilities::getLabel( 'L_Confirm' ) . '" onclick="document.getElementById(\'frmbid\').submit();" class="theme-btn">' . Utilities::getLabel( 'L_Confirm' ) . '</button> <button type="button" value="' . Utilities::getLabel( 'L_Cancel' ) . '" onclick="jQuery(document).trigger(\'close.facebox\');" class="theme-btn">' . Utilities::getLabel( 'L_Cancel' ) . '</button></td>
							</tr>
						</table>
					</div>
				</div>';	
		
		$this->set('data', $data);
		
		$this->_template->render(false, false);		
	}
}