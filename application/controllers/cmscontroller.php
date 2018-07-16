<?php
class CmsController extends LegitController {
	private $common;
	private $User;
	private $SupportController;
	
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
		
		$this->common	= new Common();
		$this->User		= new User();
		$this->CMS		= new Cms();		
		$this->Task		= new Task();		
		$this->SupportController = new SupportController('support','default_action','getContactForm');
    }
	
	function page($page_slug=""){
		 
		//Syspage::addCss('css/jquery.fancybox.css');
		Syspage::addJs('js/jquery.fancybox.js');
		$data = $this->CMS->getCmsPage($page_slug);		
		if($data['cmspage_id'] == '' || $data['cmspage_active']==0) {
			redirectUser(generateUrl('page','error_404'));
		}elseif($data['cmspage_id'] == 6){
			redirectUser(generateUrl('cms','contact'));
		}elseif($data['cmspage_id'] == 19){
			redirectUser(generateUrl('reviews','user_reviews'));
		}
		$this->set('arr_listing',$data);
		$this->_template->render();
	}
	  
	function contact() {
		Syspage::addCss(array('css/inner.css'));
		if (User::isUserLogged()) {
			redirectUser(generateUrl('support'));
		}else {
			$Meta = new Meta();
			$meta = $Meta->getMetaById(8);
			
			$data=array('meta_title'=>$meta['meta_title'],
			'meta_keywords'=>$meta['meta_keywords'],
			'meta_description'=>$meta['meta_description']);
			
			$this->set('arr_listing',$data);			
			$data = $this->CMS->getCmsPage('contact_us');
			$this->set('content',$data);
			$this->set('frm',$this->getContactForm());
		}
		//$this->set('arr_listing',$this->CMS->getRecords(6));
		$this->_template->render();
	}
	
	private function getContactForm() {
		$frm = new Form('frmcontact', 'frmContact');
		
		//$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->captionInSameCell(false);
		$frm->setFieldsPerRow(1);
		$frm->setAction(generateUrl('support', 'setup_contact'));
        $frm->setJsErrorDiv('common_error_msg');
		
		
		$frm->addRequiredField( Utilities::getLabel( 'L_Your_Name' ), 'user_screen_name', '', 'user_screen_name');
		$frm->addEmailField( Utilities::getLabel( 'L_Your_Email' ), 'user_email', '', 'user_email');
		
		$frm->addRequiredField( Utilities::getLabel( 'L_Subject' ), 'subject', '', 'subject' );
		$fld = $frm->addRequiredField( Utilities::getLabel( 'L_Phone' ), 'phone', '', 'phone' );
		
		//$fld->requirements()->setInt();
		$fld_code = $frm->addRequiredField( Utilities::getLabel( 'L_Security_Code' ), 'security_code','','','placeholder="' . Utilities::getLabel( 'L_Security_Code' ) . '"');
		$fld_code->html_before_field='<table class="chapcha"><tr><td><img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="image"><a href="javascript:void(0);" onclick="document.getElementById(\'image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></i></a></td><td>';
    	$fld_code->html_after_field='</td></tr></table>';
		//$frm->addHTML('', 'code', '');
		$frm->addTextArea( Utilities::getLabel( 'L_Message' ), 'message', '', 'message' );
		$frm->addSubmitButton( '', 'btn_submit', Utilities::getLabel( 'L_Submit' ), 'btn_submit', 'class="greenBtn"' )->merge_rows = 2;
		
		return $frm;
	}
	
	
	
	function get_url_data($action,$query_string) {
		$db = Syspage::getdb();
		
		$arr_data = array();
		
		switch(strtolower($action)) {
			
			case 'page':
				
				$page_slug	= $query_string[0];
											
				if ($page_slug != '') {
					$data = $this->CMS->getCmsPage($page_slug);
				}
				
				if (!isset($data)) return false;
				$arr_data['type'] = $page_slug != '' ? 'cms' : '';				
				$arr_data['name'] = $page_slug != '' ? $page_slug : '';				
				break;
				
			case 'contact_us':
				
				$arr_data['type'] = 'cms';
				$arr_data['name'] = 'contact';
				break;
				
			
		}
		//echo "<pre>".print_r($action,true)."</pre>";
		return $arr_data;
	}
	
}
?>
