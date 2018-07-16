<?php
class ContentpagesController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->set('leftLink', 'home');
	}
	
    function default_action() {
		if (!Admin::isLogged()) {
				redirectUser(generateUrl('admin', 'loginform'));
				return false;
		}
		$post = Syspage::getPostedVar();
		$this->set('frmcontentSearch', $this->getSearchForm());
		$this->search();		
    	$this->_template->render();
    }
	
	public function home() {
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		
		global $mbs_tinymce_settings;
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$record = new TableRecord('tbl_cms');
			
			$record->setFldValue('cmspage_nav_id', 0);
			$record->setFldValue('cmspage_active', 1);
			$record->setFldValue('cmspage_content', $post['home_content']);
			
			if (!$record->update(array('smt'=>'cmspage_id = ?', 'vals'=>array(27), 'execute_mysql_functions'=>false))) {
				Message::addErrorMessage($record->getError());
			}
			
			Message::addMessage('Content successfully updated.');
		}
		
		/* Get home page content */
		$srch = new SearchBase('tbl_cms');
		
		$srch->addCondition('cmspage_id', '=', 27);
		
		$srch->addFld('cmspage_content');
		
		$rs = $srch->getResultSet();
		
		$row = $db->fetch($rs);
		
		/* Editor configurations */
		$mbs_tinymce_settings['valid_elements']		= '*[*]';
		$mbs_tinymce_settings['height']				= '700';
		$mbs_tinymce_settings['content_css']		= array(
															'http://fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic&subset=latin,latin-ext',
															CONF_WEBROOT_URL . 'css/skeleton.css',
															CONF_WEBROOT_URL . 'css/inner.css',
															CONF_WEBROOT_URL . 'css/base.css'
														);
		
		/* Content form */
		$frm = new Form('frmHomePage');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setAction(generateUrl('contentpages', 'home'));
		$frm->captionInSameCell(true);
		
		$frm->addHtmlEditor('', 'home_content', $row['cmspage_content']);
		
		$frm->addSubmitButton('', 'btn_submit', 'Save');
		
		$this->set('frm', $frm);
		
		$this->_template->render();
	}
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmcontentSearch');
		$frm->setAction('contentpages');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addTextBox('', 'keyword','','','Placeholder="Keyword (in name)"');
		
		$frm->addSelectBox('', 'cmspage_active', array('0'=>'Inactive', '1'=>'Active'), '', '', 'Does not matter');
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\'\'"');
		$fld_submit->attachField($fld_reset);
		//$frm->setOnSubmit('searchUser(this);return false;');
		return $frm;
	}
	
	function addupdate($id) {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$db = &Syspage::getdb();
		$frm = $this->getForm();
		if($id > 0) {
			$id=intval($id);
			
			$srch = new SearchBase('tbl_cms');
			//$srch->addMultipleFields(array('cmspage_id','cmspage_nav_id','cmspage_title','cmspage_name','cmspage_content','cmspage_active',''));
			$srch->addCondition('cmspage_id','=',$id);
			$rs = $srch->getResultSet();
			$row = $db->fetch($rs);
			$row['cmspage_title'] = html_entity_decode($row['cmspage_title']);
			
			$frm->fill($row);
			//$this->set('result',$row);
		}
		
		$post = Syspage::getPostedVar();
		//echo "<pre>".print_r($post,true)."</pre>";exit;
		if(isset($post['btn_submit']) && $post['btn_submit']=="Submit") {
			$result['cmspage_id'] = $post['cmspage_id'];
			//$result['cmspage_name'] = $post['cmspage_name'];
			$result['cmspage_title'] = strip_tags($post['cmspage_title']);
			$result['cmspage_name']	= strtolower($this->clean($post['cmspage_name']));
			$result['cmspage_nav_id'] = $post['cmspage_nav_id'];
			$result['cmspage_content'] = html_entity_decode($post['cmspage_content']);
			$result['cmspage_active'] = $post['cmspage_active'];
			$result['meta_title'] = strip_tags($post['meta_title']);
			$result['meta_keywords'] = strip_tags($post['meta_keywords']);
			$result['meta_description'] = strip_tags($post['meta_description']);
			$result['other_tags'] = $post['other_tags'];
			if($result['cmspage_id'] > 0) {
				$success = $db->update_from_array('tbl_cms', $result, array('smt'=>'cmspage_id = ?', 'vals'=>array($result['cmspage_id'])));
				Message::addMsg('CMS Page Updated Successfully');
			}
			else {
				$success = $db->insert_from_array('tbl_cms', $result);
				$cmspage_id = $db->insert_id();
				Message::addMessage('CMS Page Added Successfully');
			}
			if($success) {
				redirectUser(generateUrl('contentpages'));
			}
		}
		$this->set('frmcontent', $frm);
		$this->_template->render();
	}
	
	function clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-\_]/', '', $string); // Removes special chars.

	   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
	
	protected function getForm(){
	
		if (!Admin::isLogged()) die('Unauthorized Access!');
	
		$frm = new Form('frmcontent');
		
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable full_width_table" width="100%"');
		
		$frm->addHiddenField('','cmspage_id');
		
		//$frm->addRequiredField('CMS Page Name:', 'cmspage_name');
		$frm->addRequiredField('CMS Page Title', 'cmspage_title');
		
		$fld = $frm->addRequiredField('CMS Page Slug', 'cmspage_name');
		$fld->setUnique('tbl_cms','cmspage_name','cmspage_id','cmspage_id','cmspage_id');
		
		$fld = $frm->addSelectBox('Select Parent Navigation', 'cmspage_nav_id', array('1'=>'Company','2'=>'Services','3'=>'Quick Links 1','4'=>'Quick Links 2'),'','','Select Navigation','cmspage_nav_id');
		//$fld->requirements()->setRequired();
		//$fld->requirements()->setCustomErrorMessage('Please select parent navigation');
		
		$frm->addHtmlEditor('Page Content','cmspage_content');
		
		$frm->addTextBox('Meta Page Title', 'meta_title');
		$frm->addTextArea('Meta Keywords','meta_keywords');
		$frm->addTextArea('Meta Description','meta_description');
		$frm->addTextArea('Other tags(add custom tags & javascript code)','other_tags');
		$frm->addSelectBox('CMS Page Status', 'cmspage_active', array('0'=>'Inactive','1'=>'Active'));
		
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('contentpages', 'addupdate'));
		
		return $frm;
	}
	
	function search(){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
					
		$srch = new SearchBase('tbl_cms');
				
		if ($post['keyword'] != ''){
			$cnd = $srch->addCondition('cmspage_title', 'LIKE', '%' . $post['keyword'] . '%');
		}
		
		if (is_numeric($post['cmspage_active'])) $srch->addCondition('cmspage_active', '=', intval($post['cmspage_active']));
		$srch->addCondition('cmspage_id','!=',27);		
		$pagesize = 20;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addOrder('cmspage_id','DESC');
				
		$srch->addMultipleFields(array('cmspage_id','cmspage_title','cmspage_content','cmspage_active'));
				
		$rs = $srch->getResultSet();
		
		$this->set('arr_listing',$db->fetch_all($rs));
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize', $pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);
		
		//$this->set('canEdit', $canEdit);
		
		//$this->_template->render(false, false);
		
	}
	
	function update_page_status() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$db = &Syspage::getdb();
		$post = Syspage::getPostedVar();
		
		$id = intval($post['id']);
		$srch = new SearchBase('tbl_cms');
		$srch->addCondition('cmspage_id', '=', $id);
		$srch->addFld('cmspage_active');
		
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		
		if ($row['cmspage_active'] == 0) {
			$cmspage_active = 1;
		} else {
			$cmspage_active = 0;
		}
			
		if (!$db->update_from_array('tbl_cms', array('cmspage_active'=>$cmspage_active), array('smt'=>'cmspage_id = ?', 'vals'=>array($id)))){
			Message::addErrorMessage($db->getError());
			dieWithError(Message::getHtml());
		}
		else {
			Message::addMessage('CMS Page status updated!');
			$arr = array('status'=>1, 'msg'=>Message::getHtml(), 'linktext'=>(($cmspage_active == 1)?'Active':'Inactive'));
			die(convertToJson($arr));
		}
	}
	
	function service_tag() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_infotips');
		//$srch->addCondition('infotip_id','=',1);
		$srch->addFld(array('infotip_name','infotip_description'));
		$rs = $srch->getResultSet();
		$frmserv = $this->getServForm();
		$this->set('arr_tip',$db->fetch_all_assoc($rs));
		$this->set('frmserv', $frmserv);
		$this->set('leftLink', 'service_tag');
		
		$this->_template->render();
	}
	
	private function getServForm() {
		if (!Admin::isLogged()) die('Unauthorized Access!');
		
		$frm = new Form('frmServicetag');
		
		$frm->setTableProperties('class="formTable" width="100%"');
		$frm->setExtra('class="siteForm" width="100%"');
		
		$frm->addTextArea('Service Rate Tag','service_tip','')->requirements()->setRequired();
		$frm->addTextArea('Bid Rate Tag','bid_rate','')->requirements()->setRequired();
		$frm->addSubmitButton('', 'btn_submit', 'Submit','');
		
		$frm->setAction(generateUrl('contentpages', 'update_service_tag'));
		
		return $frm;
	}
	
	function update_service_tag(){
		$post = Syspage::getPostedVar();
		//print_r($_POST);exit;
		if (!Admin::isLogged()) die('Unauthorized Access!');
		
				
		$valid_fields = array('service_tip','what_next','bid_rate');
		
		/* @var $db Database */
		$db = &Syspage::getdb();
		foreach ($post as $key=>$val){
			if (!in_array($key, $valid_fields)) continue;
			
			if ($key == 'conf_admin_email_id') {
				$db->update_from_array('tbl_admin', array('admin_email'=>$val), array('smt'=>'admin_id = ?', 'vals'=>array(1)));
			}
			
			$db->update_from_array('tbl_infotips', array('infotip_description'=>$val), array('smt'=>'infotip_name = ?', 'vals'=>array($key)));
		}
		
		Message::addMessage('Infotips Updated');
		
		redirectUser(generateUrl('contentpages','service_tag'));
	}
}