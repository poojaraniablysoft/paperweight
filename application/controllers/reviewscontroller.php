<?php
class ReviewsController extends LegitController {
	private $task;
	private $ratings;
	private $User;
	private $Common;
	private $order;
	
	public function __construct($model,$controller,$action) {
		parent::__construct($model, $controller, $action);
		$this->task 	= new Task();
		$this->ratings 	= new Ratings();
		$this->User		= new User();
		$this->Common	= new Common();
		$this->order	= new Order();
	}
	
	public function default_action($filter) {
		if (!User::isUserLogged()) redirectUser(generateUrl('user', 'signin'));
		if (!User::isUserActive()) {
			Message::addErrorMessage( Utilities::getLabel( 'E_User_Deactivate_Notification' ) );
			redirectUser(generateUrl('user', 'signin'));
		}
		if (!in_array($filter, array('posted', 'received'))) $filter = 'posted';
		if(!User::isWriter()) {
			$first_order = Common::is_first_order_done();
			if($first_order == false) {
				redirectUser(generateUrl('task','my_orders'));
				return;
			}
		}
		$posted = $this->Reviews->getReviewsPosted();
		
		foreach($posted['reviews'] as $key=>$val) {
			$posted['reviews'][$key]['user_rating'] = $this->ratings->getAvgUserRating($val['rev_reviewee_id']);
			$posted['reviews'][$key]['orders_completed'] = $this->order->getUserCompletedOrders($val['rev_reviewee_id']); 
			$posted['reviews'][$key]['posted_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewer_id']);
		}
		
		$feedreceived = $this->Reviews->getFeedbackReceived('',1);	
		
		foreach($feedreceived['feedback'] as $key=>$val) {
			$feedreceived['feedback'][$key]['user_rating'] = $this->ratings->getAvgUserRating($val['rev_reviewer_id']);
			$feedreceived['feedback'][$key]['orders_completed'] = $this->order->getUserCompletedOrders($val['rev_reviewer_id']);
			$feedreceived['feedback'][$key]['feed_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewer_id']);
		}		
		
		$this->set('filter', $filter);
		$this->set('posted',$posted);
		$this->set('page',$page);
		$this->set('pages',$pages);
		$this->set('feedreceived',$feedreceived);
		
		$this->_template->render();
	}
	
	public function setup() {
		$post = Syspage::getPostedVar();
		
		$task_id = intval($post['task_id']);
		if ($task_id < 1) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if($_SESSION['token_rev'] != $post['token']) {
			Message::addMessage( Utilities::getLabel( 'S_Review_&_Rating_Posted' ) );
			redirectUser(generateUrl('task', 'order_process', array($task_id)));
			return false;
		}
		unset($_SESSION['token_rev']);
		$arr_order = $this->task->getOrderDetails($task_id);
		
		if ($arr_order === false || $arr_order['task_status'] != 3) die( Utilities::getLabel( 'E_Invalid_request' ) );
		if (!User::isWriter() && $arr_order['task_user_id'] != User::getLoggedUserAttribute('user_id')) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		if (User::isWriter() && intval($arr_order['bid_user_id']) != User::getLoggedUserAttribute('user_id')) redirectUser(generateUrl('page_not_found'));/* die( Utilities::getLabel( 'E_Invalid_request' ) ); */
		
		$reviewee_id = User::isWriter() ? $arr_order['task_user_id'] : $arr_order['bid_user_id'];	//User being reviewed
		
		$post['reviewee_id'] = $reviewee_id;
		
		if (!$this->Reviews->addReview($post)) {
			Message::addErrorMessage($this->Reviews->getError());
			return false;
		}
		if(!$this->ratings->addRatings($post)) {
			Message::addErrorMessage($this->ratings->getError());
			return false;
		}
		Message::addMessage( Utilities::getLabel( 'S_Review_&_Rating_Posted' ) );
		
		redirectUser(generateUrl('task', 'order_process', array($task_id)));
		
		return false;
	}
	
	public function get_feedback() {
		Syspage::addCss(array('css/dashboard.css','css/responsive-dashboard.css'));
		$post = Syspage::getPostedVar();
		
		if ( !User::isUserLogged( ) ) die( convertToJson( array( 'status' => 0, 'msg' => Utilities::getLabel( 'E_Must_Logged_In' ) ) ) );
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_feedback = $this->Reviews->getFeedbackReceived('', $page);
		if ($page > $arr_feedback['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_feedback['feedback'] as $key=>$ele) {
			$arr_feedback['feedback'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewer_id']);
			$arr_feedback['feedback'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewer_id']);
			$arr_feedback['feedback'][$key]['posted_rating']	= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewer_id']);
		}
		
		$arr_feedback['str_paging'] = generatePagingStringHtml($arr_feedback['page'], $arr_feedback['pages'], 'getFeedback(xxpagexx);');
		
		$this->set('arr_feedback', $arr_feedback);
		
		$this->_template->render(false, false);
	}
	
	public function get_reviews() {
		$post = Syspage::getPostedVar();
		
		if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Must_Logged_In' ) )));
		
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_reviews = $this->Reviews->getReviewsPosted('', $page);
		if ($page > $arr_reviews['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_reviews['reviews'] as $key=>$ele) {
			$arr_reviews['reviews'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewee_id']);
			$arr_reviews['reviews'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
			$arr_reviews['reviews'][$key]['posted_rating']		= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewer_id']);
		}
		if(User::isWriter())
			{
				$user_type = User::isWriter();
			}
			else{
				$user_type =0;
			}
		$arr_reviews['str_paging'] = generatePagingStringHtml($arr_reviews['page'], $arr_reviews['pages'], 'getReviews(xxpagexx,"'.$user_type.'");');
		
		$this->set('arr_reviews', $arr_reviews);
		
		$this->_template->render(false, false);
	}
	
	public function testimonials() {
		$data=$this->Reviews->getTestimonials();
		
		foreach($data as $key=>$val) {
			$data[$key]['complete_order']=$this->order->getUserCompletedOrders($val['reviewee_id']);
			$data[$key]['user_rating'] = $this->ratings->getAvgUserRating($val['reviewee_id']);
		}
		$this->set('arr_tesimonials',$data);
		$this->_template->render();
	}
	
	public function user_reviews($filter = '') {
		$post = Syspage::getPostedVar();
		
		//if (User::isUserLogged()) redirectUser(generateUrl('reviews'));
		if (!in_array($filter, array('reviews', 'feedback'))) $filter = 'reviews';
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_reviews = $this->Reviews->getPostedReviews($page,CONF_PAGINATION_LIMIT);
		
		//if ($page > $arr_reviews['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_reviews['review'] as $key=>$ele) {
			$arr_reviews['review'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewee_id']);
			$arr_reviews['review'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
			$arr_reviews['review'][$key]['posted_rating']		= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewer_id']);
			$arr_reviews['review'][$key]['complete_order']		=$this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
		}
		$arr_feedback = $this->Reviews->getReceivedFeedback($page,false,CONF_PAGINATION_LIMIT);
		//if ($page > $arr_feedback['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_feedback['feedbacks'] as $key=>$ele) {
			$arr_feedback['feedbacks'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['posted_rating']	= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['complete_order']	=$this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
		}
		
		//$arr_feedback['str_paging'] = generatePagingStringHtml($arr_feedback['page'], $arr_feedback['pages'], 'getFeedback(xxpagexx);');
		
		
		//$arr_reviews['str_paging'] = generatePagingStringHtml($arr_reviews['page'], $arr_reviews['pages'], 'getReviews(xxpagexx);');
		
		//echo "<pre>".print_r($arr_feedback,true)."</pre>";exit;
		//echo "<pre>".print_r($arr_reviews,true)."</pre>";exit;
		$this->set('filter',$filter);
		$this->set('arr_feedback', $arr_feedback);
		$this->set('arr_reviews', $arr_reviews);
		
		$Meta = new Meta();
		$meta = $Meta->getMetaById(7);		
		$data=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);
		$this->set('arr_listing',$data);			
		
		$this->_template->render();
	}
	
	public function search_reviews() {
		$post = Syspage::getPostedVar();
		$user_id = intval($post['user_id']);
		$disp_id = intval($post['disp_id']);
		$rating = intval($post['rating']);
		$page = intval($post['page']);
		$user_feedbacks = $this->Reviews->getFeedbackReceived($user_id,$page,array('disp_id'=>$disp_id,'rating'=>$rating));
		foreach($user_feedbacks['feedback'] as $key=>$val){
			$user_feedbacks['feedback'][$key]['review'] = $this->Reviews->getTaskReview($val['task_id'],$val['rev_reviewee_id']);			
			$user_feedbacks['feedback'][$key]['reviewee_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewee_id']);			
			$user_feedbacks['feedback'][$key]['reviewer_rating'] = $this->ratings->getTaskRating($val['task_id'],$val['rev_reviewer_id']);			
		}
		$this->set('arr_user',$user_id);
		$this->set('user_reviews',$user_feedbacks);
		$this->_template->render(false,false);
	}
	
	/* public function user_review($filter = '') {
		$post = Syspage::getPostedVar();
		
		//if (!User::isUserLogged()) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Must_Logged_In' ) )));
		if (!in_array($filter, array('reviews', 'feedback'))) $filter = 'reviews';
		$page = intval($post['page']);
		if ($page < 1) $page = 1;
		
		$arr_reviews = $this->Reviews->getPostedReviews($page);
		
		if ($page > $arr_reviews['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_reviews['review'] as $key=>$ele) {
			$arr_reviews['review'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewee_id']);
			$arr_reviews['review'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
			$arr_reviews['review'][$key]['posted_rating']		= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewer_id']);
			$arr_reviews['review'][$key]['complete_order']		=$this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
		}
		$arr_feedback = $this->Reviews->getReceivedFeedback($page);
		if ($page > $arr_feedback['pages']) die(convertToJson(array('status'=>0, 'msg'=> Utilities::getLabel( 'E_Invalid_request' ) )));
		
		foreach ($arr_feedback['feedbacks'] as $key=>$ele) {
			$arr_feedback['feedbacks'][$key]['user_rating']		= $this->ratings->getAvgUserRating($ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['orders_completed']	= $this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['posted_rating']	= $this->ratings->getTaskRating($ele['task_id'],$ele['rev_reviewee_id']);
			$arr_feedback['feedbacks'][$key]['complete_order']	=$this->order->getUserCompletedOrders($ele['rev_reviewee_id']);
		}
		
		$arr_feedback['str_paging'] = generatePagingStringHtml($arr_feedback['page'], $arr_feedback['pages'], 'getFeedback(xxpagexx);');
		
		
		$arr_reviews['str_paging'] = generatePagingStringHtml($arr_reviews['page'], $arr_reviews['pages'], 'getReviews(xxpagexx);');
		
		//echo "<pre>".print_r($arr_feedback,true)."</pre>";exit;
		//echo "<pre>".print_r($arr_reviews,true)."</pre>";exit;
		//$this->set('filter',$filter);
		//$this->set('arr_feedback', $arr_feedback);
		//$this->set('arr_reviews', $arr_reviews);
		$review[] = $arr_feedback;
		$review[] = $arr_reviews;
		die(convertToJson($review));
	} */
}