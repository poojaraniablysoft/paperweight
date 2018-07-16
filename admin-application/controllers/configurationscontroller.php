<?php
class ConfigurationsController extends Controller{
	private $arr_date_format_php;
	private $arr_date_format_mysql;
	private $arr_date_format_jquery;
	private $Home;
	function __construct($model, $controller, $action){
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->Home = new Home();
		$this->arr_date_format_php=array(
		'Y-m-d',
		'd-m-Y',
		'm-d-Y',
		'M d, Y'
		);

		$this->arr_date_format_mysql=array(
		'%Y-%m-%d',
		'%d-%m-%Y',
		'%m-%d-%Y',
		'%b %d, %Y'
		);

		$this->arr_date_format_jquery=array(
		'%Y-%m-%d',
		'%d-%m-%Y',
		'%m-%d-%Y',
		'%b %d, %Y'
		);
	}
	
	function default_action(){
	
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		
		$this->set('frmConf', $this->getForm());		
		$this->_template->render();
	}
	
	function update(){
		$post = Syspage::getPostedVar();
		//print_r($_POST);exit;
		if (!Admin::isLogged()) { redirectUser(generateUrl('admin', 'loginform'));return false; }
		$frm = $this->getForm();
		$post['conf_date_format_jquery'] = $this->arr_date_format_jquery[$post['date_format']];
		$post['conf_date_format_mysql'] = $this->arr_date_format_mysql[$post['date_format']];
		$post['conf_date_format_php'] = $this->arr_date_format_php[$post['date_format']];
		
		$valid_fields = array(
		'conf_date_format_jquery', 'conf_date_format_mysql', 'conf_date_format_php','conf_timezone',
		'conf_site_url','conf_currency','conf_emails_from_name','conf_contact_location','conf_contact_phone', 'conf_website_name','conf_words_per_page', 
		 'conf_admin_email_id','conf_contact_address', 'conf_emails_from', 'conf_help_email_to', 'conf_help_improve_email_to', 'conf_contact_email_to','conf_mandrill_api_key','conf_paypal_merchant_id','conf_paypal_currency','conf_paypal_transaction_mode',
		 'conf_email_send_via', 'conf_mandrill_api_key', 'conf_postmark_api_key','conf_smtp_mail_host', 'conf_smtp_mail_username', 'conf_smtp_mail_password', 'conf_smtp_mail_port', 'conf_smtp_mail_secure',
		'conf_twitter_username', 'conf_twitter_list','conf_fb_liked_url',
		'conf_transaction_trial_validity_period',
		'conf_facebook', 'conf_googleplus', 'conf_twitter', 'conf_linkedin',
		'conf_service_comission','conf_service_charge','conf_wallet_limit', 'conf_withdraw_funds_limit',
		'conf_correct_ans_required','conf_ques_display','conf_quiz_time',
		'conf_video_url','conf_sample_essay_topic','conf_pagination_limit','CONF_USE_SSL','conf_website_logo','conf_website_fav_icon','conf_website_footer_logo','conf_admin_logo','conf_email_template_logo','conf_login_logo', 'conf_footer_copyright');
		
		if( $_FILES['conf_email_template_logo']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_email_template_logo')){
				$post['conf_email_template_logo'] = $image_names;
			}
		}
		
		if(empty($post['conf_email_template_logo'])){
			$key = array_search('conf_email_template_logo', $post);
			unset($valid_fields[$key]);
		}
		
		if( $_FILES['conf_login_logo']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_login_logo')){
				$post['conf_login_logo'] = $image_names;
			}
		}
		
		if(empty($post['conf_login_logo'])){
			$key = array_search('conf_login_logo', $post);
			unset($valid_fields[$key]);
		}
		
		if( $_FILES['conf_admin_logo']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_admin_logo')){
				$post['conf_admin_logo'] = $image_names;
			}
		}
		
		if(empty($post['conf_admin_logo'])){
			$key = array_search('conf_admin_logo', $post);
			unset($valid_fields[$key]);
		}
		
		if( $_FILES['conf_website_logo']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_website_logo')){
				$post['conf_website_logo'] = $image_names;
			}
		}
		
		if(empty($post['conf_website_logo'])){
			$key = array_search('conf_website_logo', $post);
			unset($valid_fields[$key]);
		}
		
		if( $_FILES['conf_website_footer_logo']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_website_footer_logo')){
				$post['conf_website_footer_logo'] = $image_names;
			}
		}
		
		if(empty($post['conf_website_footer_logo'])){
			$key = array_search('conf_website_footer_logo', $post);
			unset($valid_fields[$key]);
		}
				
		if( $_FILES['conf_website_fav_icon']['name'] != '' ){
			$image_names = '';
			if($this->saveUploadedLogo($_FILES, $image_names, 'conf_website_fav_icon')){
				$post['conf_website_fav_icon'] = $image_names;
			}
		}
		
		if(empty($post['conf_website_fav_icon'])){
			$key = array_search('conf_website_fav_icon', $post);
			unset($valid_fields[$key]);
		}		
	
		/* @var $db Database */
		$db = &Syspage::getdb();
		
		foreach ($post as $key=>$val){
			if (!in_array($key, $valid_fields)) continue;
			if($key == 'conf_video_url') {
				if (!$video_id = isYoutubeVideoValid($post['conf_video_url'])) {
					Message::addErrorMessage('Please update valid Youtube URL!');
					$this->_template = new Template('configurations','default_action');
					$this->default_action();
					return;
				}
				$post['conf_video_url'] = $video_id;
			}
			if ($key == 'conf_admin_email_id') {
				$db->update_from_array('tbl_admin', array('admin_email'=>$val), array('smt'=>'admin_id = ?', 'vals'=>array(1)));
			}
			if ($key == 'conf_sample_essay_topic' && $val != CONF_SAMPLE_ESSAY_TOPIC) {
				$this->Task = new Task();
				$rows = $this->Home->test_taker(true);
				foreach($rows as $row){
					$param = array('to'=>$row['user_email'],'temp_num'=>63,'user_screen_name'=>$row['user_screen_name'],'sample_essay_topic'=>$val);
					$this->Task->sendEmail($param);
				}
			}
			
			$db->update_from_array('tbl_configurations', array('conf_val'=>$val), array('smt'=>'conf_name = ?', 'vals'=>array($key)));
		}
		 
		Message::addMessage('Setting Updated');
		
		redirectUser(generateUrl('configurations'));
	}
	
	protected function getForm(){
		global $binary_status;
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$arr=DateTimeZone::listIdentifiers();
		$arr=array_combine($arr, $arr);
		
		$frm = new Form('frmConfigurations');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setRequiredStarWith('caption');
		$frm->setJsErrorDiv('common_msg');
		//$frm->setFieldsPerRow(2);
		$frm->setLeftColumnProperties('width="30%"');
		
		$frm->addHTML('<h2>General Settings</h2>');
		
		$frm->addRequiredField('Website Name', 'conf_website_name', CONF_WEBSITE_NAME,'conf_website_name', 'style="width:400px;"');
		$frm->addRequiredField('Website URL', 'conf_site_url', CONF_SITE_URL,'conf_site_url', 'style="width:400px;"');		
		
		$fld=$frm->addFileUpload('Admin Logo', 'conf_admin_logo', 'conf_admin_logo');
		$imageUrl = generateUrl('image', 'logo', array( 'medium', CONF_ADMIN_LOGO ));
		$fld->html_after_field= '<div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';
		
		$fld=$frm->addFileUpload('Website Logo', 'conf_website_logo', 'conf_website_logo');
		$imageUrl = generateUrl('image', 'logo', array( 'medium', CONF_WEBSITE_LOGO ));
		$fld->html_after_field= '<div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';
		
		$fld=$frm->addFileUpload('Website Footer Logo', 'conf_website_footer_logo', 'conf_website_footer_logo');
		$imageUrl = generateUrl('image', 'logo', array( 'medium', CONF_WEBSITE_FOOTER_LOGO ));
		$fld->html_after_field= '<div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';
		
		$fld=$frm->addFileUpload('Email Template Logo', 'conf_email_template_logo', 'conf_email_template_logo');
		$imageUrl = generateUrl('image', 'logo', array( 'medium', CONF_EMAIL_TEMPLATE_LOGO ));
		$fld->html_after_field= '<div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';
				
		$fld=$frm->addFileUpload('Login Logo', 'conf_login_logo', 'conf_login_logo');
		$imageUrl = generateUrl('image', 'logo', array( 'full', CONF_LOGIN_LOGO ));
		$fld->html_after_field= '<span>Recommended dimensions are 120x120.</span><div id="post_imgs" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';
		

		$fld=$frm->addFileUpload('Website Favicon', 'conf_website_fav_icon', 'conf_website_fav_icon');
		$imageUrl = generateUrl('image', 'fav_icon', array( CONF_WEBSITE_FAV_ICON ));
		$fld->html_after_field= '<div id="post_imgs_fav" class="photosrow"><div class="photosquare"><img src="'.$imageUrl.'" /></div></div>';		
		
		$frm->addTextBox('Send Emails From Name', 'conf_emails_from_name', CONF_EMAILS_FROM_NAME,'conf_emails_from_name', 'style="width:400px;"');
		$frm->addTextBox('Contact Number', 'conf_contact_phone', CONF_CONTACT_PHONE,'conf_contact_phone', 'style="width:400px;"');
		$frm->addTextArea('Contact Address', 'conf_contact_address', CONF_CONTACT_ADDRESS,'conf_contact_address', 'style="width:400px;"');
		$frm->addTextBox('Contact Location iFrame', 'conf_contact_location', CONF_CONTACT_LOCATION,'conf_contact_location', 'style="width:400px;"');
		$frm->addTextBox('Currency', 'conf_currency', CONF_CURRENCY,'conf_currency', 'style="width:400px;"');
		$frm->addRequiredField('Number of words per page:', 'conf_words_per_page', CONF_WORDS_PER_PAGE,'', 'style="width:400px;"');
		$frm->addRequiredField('Sample Essay Topic', 'conf_sample_essay_topic', CONF_SAMPLE_ESSAY_TOPIC,'conf_sample_essay_topic', 'style="width:400px;"');
		$frm->addRequiredField('Video URL:', 'conf_video_url', CONF_VIDEO_URL,'conf_video_url', 'style="width:400px;"');
		$frm->addRequiredField('Pagination Limit:', 'conf_pagination_limit', CONF_PAGINATION_LIMIT,'conf_pagination_limit', 'style="width:400px;"');
		$frm->addRadioButtons('Use SSL:', 'CONF_USE_SSL',$binary_status,CONF_USE_SSL,2, 'style="width:400px;"');
		
		$frm->addHTML('<h2>Email Settings</h2>','','');
		$frm->addEmailField('Administrator Email ID', 'conf_admin_email_id', CONF_ADMIN_EMAIL_ID,'conf_admin_email_id', 'style="width:400px;"');	
		$frm->addEmailField('Send Emails From', 'conf_emails_from', CONF_EMAILS_FROM,'conf_emails_from', 'style="width:400px;"');
		$frm->addEmailField('Contact Email Address', 'conf_contact_email_to', CONF_CONTACT_EMAIL_TO,'conf_contact_email_to', 'style="width:400px;"');
		$frm->addRequiredField('Mandrill API Key', 'conf_mandrill_api_key', CONF_MANDRILL_API_KEY,'conf_mandrill_api_key', 'style="width:400px;"');
		
		$frm->addRadioButtons('Email Send By', 'conf_email_send_via', Applicationconstants::$mail_sending_options, CONF_EMAIL_SEND_VIA, 2, 'style="width:800px;"');
		$frm->addTextBox('SMTP Host:', 'conf_smtp_mail_host', CONF_SMTP_MAIL_HOST, 'conf_smtp_mail_host', 'style="width:400px;"');
		$frm->addTextBox('SMTP username:', 'conf_smtp_mail_username', CONF_SMTP_MAIL_USERNAME, 'conf_smtp_mail_username', 'style="width:400px;"');
		$frm->addPasswordField('SMTP Password:', 'conf_smtp_mail_password', CONF_SMTP_MAIL_PASSWORD, 'conf_smtp_mail_password', 'style="width:400px;"');
		$frm->addTextBox('SMTP Port:', 'conf_smtp_mail_port', CONF_SMTP_MAIL_PORT, 'conf_smtp_mail_port', 'style="width:400px;"');
		$frm->addRadioButtons('SMTP Secure:', 'conf_smtp_mail_secure', array(0=>'ssl', 1=>'tls'), CONF_SMTP_MAIL_SECURE, 2, 'style="width:400px;"');
		
		$frm->addHTML('<h2>Paypal Settings</h2>','','');
		$frm->addRequiredField('Paypal Merchant Id', 'conf_paypal_merchant_id', CONF_PAYPAL_MERCHANT_ID,'conf_paypal_merchant_id', 'style="width:400px;"');
		$frm->addRequiredField('Paypal Currency', 'conf_paypal_currency', CONF_PAYPAL_CURRENCY,'conf_paypal_currency', 'style="width:400px;"');	
		$modes=array('Sandbox','Live');
		$frm->addSelectBox('Paypal Mode', 'conf_paypal_transaction_mode', $modes, CONF_PAYPAL_TRANSACTION_MODE, 'style="width:400px;"', '');		
		
		
		$frm->addHTML('<h2>Date Formats Settings</h2>','','');
		$frm->addSelectBox('Date Format', 'date_format', $this->arr_date_format_php, array_keys($this->arr_date_format_php, CONF_DATE_FORMAT_PHP), 'style="width:400px;"', '');		
		$fld=$frm->addSelectBox('Timezone', 'conf_timezone', $arr, CONF_TIMEZONE, 'class="input" style="width:400px;"', '');
		$fld->html_after_field=' <br>Time According To ' . CONF_TIMEZONE . ' = ' . displayDate(date('Y-m-d H:i:s'), true, true, CONF_TIMEZONE);
		
		$frm->addHTML('<h2>Social Links Settings</h2>','','');
		$frm->addRequiredField('Facebook URL', 'conf_facebook', CONF_FACEBOOK,'conf_facebook', 'style="width:400px;"');		
		$frm->addRequiredField('Twitter URL', 'conf_twitter', CONF_TWITTER,'conf_twitter', 'style="width:400px;"');		
		$frm->addRequiredField('Google+ URL', 'conf_googleplus', CONF_GOOGLEPLUS,'conf_googleplus', 'style="width:400px;"');		
		$frm->addRequiredField('Linked In URL', 'conf_linkedin', CONF_LINKEDIN,'conf_linkedin', 'style="width:400px;"');
		
		$frm->addHTML('<h2>Grammar Test Settings</h2>','','');
		$fld = $frm->addRequiredField('Quiz Time (in Minutes):', 'conf_quiz_time', CONF_QUIZ_TIME,'', 'style="width:400px;"');
		$fld->requirements()->setInt();
		$fld = $frm->addRequiredField('Number of question to be display:', 'conf_ques_display', CONF_QUES_DISPLAY,'', 'style="width:400px;"');
		$fld->requirements()->setInt();
		$fld = $frm->addRequiredField('Number of Correct Answer Require:', 'conf_correct_ans_required', CONF_CORRECT_ANS_REQUIRED,'', 'style="width:400px;"');
		$fld->requirements()->setInt();
		
		$frm->addHTML('<h2>Wallet Settings</h2>','','');
		$frm->addRequiredField('Wallet Limit', 'conf_wallet_limit', CONF_WALLET_LIMIT,'conf_wallet_limit', 'style="width:400px;"')->requirements()->setFloatPositive();

		$frm->addRequiredField('Minimum Withdraw Funds Limit', 'conf_withdraw_funds_limit', CONF_WITHDRAW_FUNDS_LIMIT,'conf_withdraw_funds_limit', 'style="width:400px;"')->requirements()->setFloatPositive();
		
		$frm->addHTML('<h2>Commision Settings</h2>','','');
		$fld = $frm->addRequiredField('Admin\'s Commision Percentage', 'conf_service_comission', CONF_SERVICE_COMISSION,'', 'style="width:400px;"');
		$fld->requirements()->setInt();
		$frm->addRequiredField('Service Charge for Customer (Fixed price)', 'conf_service_charge', CONF_SERVICE_CHARGE, 'conf_service_charge','style="width:400px;"');
		
		$frm->addHTML('<h2>Footer Settings</h2>','','');
		$fld = $frm->addRequiredField('Copyright:', 'conf_footer_copyright', CONF_FOOTER_COPYRIGHT,'', 'style="width:400px;"');
		 
		
		//$frm->addRequiredField('Facebook Liked Url:', 'conf_fb_liked_url', CONF_FB_LIKED_URL);
		
		//$frm->addRequiredField('Transaction Trial Validity Period', 'conf_transaction_trial_validity_period', CONF_TRANSACTION_TRIAL_VALIDITY_PERIOD);
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('configurations', 'update'));
		
		return $frm;
	}
	
	private function saveUploadedLogo(&$files, &$image_names, $key = ""){
		  
		if(is_uploaded_file($files[$key]['tmp_name'])){
			$image_names = '';
			if(saveImage($files[$key]['tmp_name'], $files[$key]['name'], $image_names)){
				return true;
			}else{
				Message::addErrorMessage($files[$key]['name'] . ': ' .$image_names);
			}
		}
		return false;
	}	
}
