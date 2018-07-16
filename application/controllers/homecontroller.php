<?php
class HomeController extends LegitController {
	private $common;
	private $order;
	private $user;
	private $Reviews;
	private $Ratings;
	private $Blog;
    
	function __construct($model, $controller, $action){
        parent::__construct($model, $controller, $action);
		
		$this->common	= new Common();
		$this->order	= new Order();
		$this->user		= new User();
		$this->Reviews	= new Reviews();
		$this->Ratings	= new Ratings();
		$this->cms		= new Cms();
		$this->Blog 	= new Blog();
    }
	
    function default_action() {
		$post = Syspage::getPostedVar();
		
		$video_id = isYoutubeVideoValid(CONF_VIDEO_URL);
		$content = $this->cms->getRecords(27);
		
		$home_1_content = Cmsblock::getContent('home_page_1', 'content');
		$this->set('home_1_content', $home_1_content);
		
		$home_1_title = Cmsblock::getContent('home_page_1', 'title');
		$this->set('home_1_title', $home_1_title);
		
		$home_page_form = Cmsblock::getContent('home_page_form', 'content');
		$this->set('home_page_form', $home_page_form);
		
		$home_page_form_title = Cmsblock::getContent('home_page_form', 'title');
		$this->set('home_page_form_title', $home_page_form_title); 
		
		$home_2_content = Cmsblock::getContent('home_page_2', 'content');
		$this->set('home_2_content', $home_2_content);
		
		$home_2_title = Cmsblock::getContent('home_page_2', 'title');
		$this->set('home_2_title', $home_2_title); 
			
		$home_3_content = Cmsblock::getContent('home_page_3', 'content');
		$this->set('home_3_content', $home_3_content);
		
		$home_3_title = Cmsblock::getContent('home_page_3', 'title');
		$this->set('home_3_title', $home_3_title); 
				
		$home_4_content = Cmsblock::getContent('home_page_4', 'content');
		$this->set('home_4_content', $home_4_content);
		
		$home_4_title = Cmsblock::getContent('home_page_4', 'title');
		$this->set('home_4_title', $home_4_title); 
						
		$home_5_content = Cmsblock::getContent('home_page_5', 'content');
		$this->set('home_5_content', $home_5_content);
		
		$home_5_title = Cmsblock::getContent('home_page_5', 'title');
		$this->set('home_5_title', $home_5_title); 
						
		$home_6_content = Cmsblock::getContent('home_page_6', 'content');
		$this->set('home_6_content', $home_6_content);
		
		$home_6_title = Cmsblock::getContent('home_page_6', 'title');
		$this->set('home_6_title', $home_6_title); 
							
		$home_7_content = Cmsblock::getContent('home_page_7', 'content');
		$this->set('home_7_content', $home_7_content);
		
		$home_7_title = Cmsblock::getContent('home_page_7', 'title');
		$this->set('home_7_title', $home_7_title); 
				
		$content['order_completed'] = $this->order->getCompletedOrdersCount();
		$content['writers_active'] = $this->user->getActiveWritersCount();
		$content['writers_online'] = $this->user->getCurrentOnlineWritersCount();
		$content['avg_user_ratings'] = $this->Ratings->getAvgSystemWriterRatings();
		
		$arr_writers = $this->user->getTopWriters(1,'','',6);
		foreach ($arr_writers['data'] as $key=>$ele) {
			$writer_activities = $this->order->getWriterActivityStats($ele['user_id']);
			$arr_writers['data'][$key]['orders_completed'] = $writer_activities['orders_completed'];
			$arr_writers['data'][$key]['orders_in_process'] = $writer_activities['orders_in_progress'];			
			$arr_writers['data'][$key]['user_languages'] = $this->user->getUserLanguages($ele['user_id']);			
		}
		$this->set('arr_writers', $arr_writers);
		
		$this->set('arr_reviews',$this->Reviews->getPostedReviews($page,0, true));	
		$this->set('arr_feedback',$this->Reviews->getReceivedFeedback($page,true));
		
		$video_id = isYoutubeVideoValid(CONF_VIDEO_URL);
		$this->set('video_url','https://www.youtube.com/embed/'.$video_id);
		
		$Meta = new Meta();
		$meta = $Meta->getMetaById(1);
		
		$data=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);
		$this->set('arr_listing',$data);
		
		$this->set('content', $content);		
		$frm = $this->getCustomerRegistrationForm();
        $this->get_latest_blogs();
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $frm->fill($post);
        }
		
		$this->set('frm', $frm);
    	$this->_template->render();
    }
	
	
	private function get_latest_blogs() {
		$post = Syspage::getPostedVar();
		$page = 1;
		if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
		else $post['page'] = $page;
		$pagesize = 3;
		#$pagesize = CONF_PAGINATION_LIMIT;
		$post['pagesize'] = $pagesize;
		$this->set('records', $this->Blog->getBlogPosts($post));
		$this->set('pages', $this->Blog->getTotalPages());
		$this->set('page', $page);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $this->Blog->getTotalRecords();
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);
		//return($this->_template->render(false, false, CONF_WEBROOT_URL . 'home/get_latest_blogs.php', true));		
	}
	
	function message_common() {
		$this->_template->render();
	}
	
	private function getCustomerRegistrationForm() {
		$frm = new Form('frmCustomerRegistration');
		
		$frm->setExtra('class="place-orderForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->captionInSameCell(true);
		$frm->setFieldsPerRow(5);
		
		//$frm->setOnSubmit('submitForm();');
			
		$frm->setJsErrorDisplay('summary');
		$frm->setJsErrorDiv('common_error_msg');
		$frm->setAction(generateUrl('user', 'register_customer'));
		
		$frm->setRequiredStarPosition('none');
		if(!User::isUserLogged() || !(User::isUserLogged() && !User::isWriter())) {
			$fld_email = $frm->addEmailField( Utilities::getLabel( 'L_Email' ), 'user_email', Utilities::getLabel( 'L_Email' ), 'user_email','onfocus="if(this.value==\'Email\') this.value=\' \';" onblur="if(this.value==\' \') this.value=\'Email\';"');
			$fld_email->setUnique('tbl_users', 'user_email', 'user_id', 'user_id', 'user_id');
			$fld_email->requirements()->setLength('5','255');
			#$fld_email->requirements()->setCustomErrorMessage( 'Please enter valid Email ID.');
		}else {
			$frm->addRequiredField( Utilities::getLabel( 'L_Order_Topic' ),'task_topic','','task_topic','placeholder="' . Utilities::getLabel( 'L_Order_Topic' ) . '"');
			/* if(User::isWriter()) {
				$fld_email = $frm->addHTML('','user_email',User::getLoggedUserAttribute('user_email'));
				$fld_email->html_before_field = '<div class="border">';
				$fld_email->html_after_field = '</div>';
			}else { */
			/* } */
		}
		//$fld_deadline = $frm->addDateTimeField('', 'task_due_date', 'Deadline', 'task_due_date', 'class="calender" readonly = "readonly" onfocus="if(this.value==\'Deadline\') this.value=\'\';" onblur="if(this.value==\'\') this.value=\'Deadline\';"');
		//$fld_deadline->requirements()->setRequired();
		//$fld_deadline->requirements()->setCustomErrorMessage( 'Deadline is mandatory.');
		
		/* $fld_deadline = $frm->addSelectBox('','task_due_date',array('3_h'=>'3 hours','6_h'=>'6 hours','11_n'=>'Next day by 11am','1_d'=>'Next Day','2_d'=>'2 Days','3_d'=>'3 Days','4_d'=>'4 Days','5_d'=>'5 Days','7_d'=>'7 Days','14_d'=>'14 Days','21_d'=>'21 Days','28_d'=>'28 Days'),'7_d','','Select Deadline'); */
		
		/* Update 5-sep-2014 */
		
	/* 	$fld_deadline = $frm->addSelectBox('Deadline','task_due_date',array('3_h'=>'3 hours','6_h'=>'6 hours','12_h'=>'12 hours','24_h'=>'1 Day','2_d'=>'2 Days','3_d'=>'3 Days','4_d'=>'4 Days','5_d'=>'5 Days','6_d'=>'6 Days','7_d'=>'7 Days','10_d'=>'10 Days','14_d'=>'14 Days','21_d'=>'21 Days','28_d'=>'28 Days'),'7_d','','Select Deadline');*/
		$tomorrow = new DateTime('tomorrow');			
		$end = new DateTime('9999-12-31 23:59:59');	
		
		$fld_deadline = $frm->addDateTimeField( Utilities::getLabel( 'L_Deadline' ),'task_due_date', Utilities::getLabel( 'L_Choose_Deadline' ), '', ' readonly class="task_due_date_cal"'); 
		/* $fld_deadline->requirements()->setRequired(true); */
		$fld_deadline->requirements()->setRange($tomorrow->format( 'd/m/Y H:i:s'),$end->format('d/m/Y H:i:s'));
		/* $fld_deadline->requirements()->setRange($tomorrow->format( CONF_DATE_FORMAT_PHP. ' H:i:s'),$end->format( CONF_DATE_FORMAT_PHP. ' H:i:s')); */
		$fld_deadline->requirements()->setCustomErrorMessage( Utilities::getLabel( 'E_Deadline_Restriction_Message' ));		
		
		$fld_paper = $frm->addSelectBox('', 'task_paptype_id', $this->common->getPaperTypes(), '', '', Utilities::getLabel( 'L_Type_of_paper' ), 'task_paptype_id', 'placeholder="' . Utilities::getLabel( 'L_Type_of_paper' ) . '"');
		$fld_paper->requirements()->setRequired();
		$fld_paper->requirements()->setCustomErrorMessage( Utilities::getLabel( 'E_Custom_Messsage_Select_Paper_Type' ) );
				
		$fld_pages = $frm->addRequiredField( Utilities::getLabel( 'L_Pages' ), 'task_pages', Utilities::getLabel( 'L_Pages(in numeric)' ), 'task_pages', 'onfocus="if(this.value==\'' . Utilities::getLabel( 'L_Pages(in numeric)' ) . '\') this.value=\' \';" onblur="if(this.value==\' \') this.value=\'Pages(in numeric)\';"');
		$fld_pages->requirements()->setIntPositive();
		$fld_pages->requirements()->setRange(1,999);
		
		$frm->addHTML('','','<div class="infoicon"><img src="images/infoicon.png" alt=""><span class="wrapover">' . Utilities::getLabel( 'L_275_pages' ) . '</span></div>',true)->extra='width="5%"';
		
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Continue' ), 'btn_submit');
		
		return $frm;
	}
	
	function what_next() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_infotips');
		$srch->addCondition('infotip_id','=',2);
		$srch->addFld('infotip_description');
		$rs = $srch->getResultSet();
		$this->set('arr_tip',$db->fetch($rs));
		$this->_template->render(false,false);
	}
	
	function smtp_mail_test(){
		var_dump(sendMail('rahul.mittal@ablysoft.com', 'test', 'test email'));
	}
}