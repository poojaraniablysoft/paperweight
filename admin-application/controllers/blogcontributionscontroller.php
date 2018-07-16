<?php
class BlogcontributionsController extends Controller{
	private $permission_id;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		//$this->permission_id = 12;
		$this->set('can_edit',true);
	}

    function default_action(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$frm = $this->getSearchForm();
		$this->set('frmContributions', $frm);
		$this->_template->render();
    }
	
	function listContributions(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_submit'])){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			
			$pagesize = 10;
			$post['pagesize'] = $pagesize;
			$this->set('records', $this->Blogcontributions->getContributionsData($post));
			$this->set('pages', $this->Blogcontributions->getTotalPages());
			$this->set('page', $page);
			$this->set('pagesize', $pagesize);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blogcontributions->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->_template->render(false,false);
		}
		die(0);
	}
	
	private function getSearchForm(){
		$frm = new Form('frmSearch', 'frmSearch');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable threeCol" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setFieldsPerRow(4);
		$frm->captionInSameCell(true);
		$frm->addTextBox('', 'contribution_author_first_name', '', 'contribution_author_first_name', 'placeholder="Search Author First Name..."');
		$frm->addSelectBox('', 'contribution_status', array(0=>'Pending', 1=>'Approved', 2=>'Posted/Published', 3=>'Rejected'), '', '', 'Select Contribution Status', 'contribution_status');
		$fld = $frm->addSubmitButton('', 'btn_submit', 'Search', 'btn_submit');
		$fld1 = $frm->addButton('', 'btn_reset', 'Reset', 'btn_reset', 'onclick="document.location.href=\''.generateUrl('blogcontributions').'\'" class="btn_reset" style="cursor:pointer; margin-left:10px;"');
		$fld->attachField($fld1);
		$frm->addHiddenField('', 'page', 1);
		$frm->setOnSubmit('searchContributions(this); return false;');
		return $frm;
	}
	
	private function getContributionStatusUpdateForm()
	{
		$frm = new Form('frmBlogContributions', 'frmBlogContributions');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->addHiddenField('', 'contribution_id', '');
		$status_array = array(0=>'Pending', 1=>'Approved', 2=>'Posted', 3=>'Rejected');
		$frm->addSelectBox('Contribution Status', 'contribution_status', $status_array, '', '', 'Select', 'contribution_status');
		$frm->addSubmitButton('', 'update', 'Update', 'update');
		$frm->setOnSubmit('updateStatus(this); return false;');
		return $frm;
	}
	
	function view($contribution_id){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$contribution_id = intval($contribution_id);
		if($contribution_id < 1){
			Message::addErrorMessage('Invalid request !!');
			redirectUser(generateUrl('blogcontributions'));
		}
		$contribution_data = $this->Blogcontributions->getContributionPost($contribution_id);
		$status_array = array('0'=>'Pending', '1'=>'Approved', '2'=>'Posted/Published', '3'=>'Rejected');
		//unset($status_array[$contribution_data['contribution_status']]);
		$frm = $this->getContributionStatusUpdateForm();
		$frm->getField('contribution_status')->options = $status_array;
		$frm->getField('contribution_id')->value = $contribution_data['contribution_id'];
		$frm->fill(array('contribution_status'=>$contribution_data['contribution_status']));
		$this->set('frmContributionStatusUpdate', $frm);
		$this->set('contribution_data', $contribution_data);
		$this->_template->render();
	}
	
	function updateStatus()
	{
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$post = Syspage::getPostedVar();
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $post['contribution_status'] !=''){
			if($this->Blogcontributions->updateStatus($post)){
				$contribution_status = $post['contribution_status'];
				if($contribution_status == '2'){
					$contribution_id = $post['contribution_id'];
					$record = $this->Blogcontributions->getContributionPost($contribution_id);
					$data = array(
									'{user_name}'=>ucfirst($record['contribution_author_first_name']) . ' ' . ucfirst($record['contribution_author_last_name']),
									'{blog_url}'=>CONF_WEBSITE_URL.'/blog/'
									/* 'xxsite_namexx'=>CONF_WEBSITE_NAME,
									'xxsite_urlxx'=>getAbsoluteUrl('', '', array(), '/') */
								
							);
					/* if(!$this->mergeFieldsWithTemplate($data)){
						return false;
					}  */
							
					if(!sendMailTpl($record['contribution_author_email'], 16, $data)){					
						Message::addErrorMessage('Error in email sending, please try again later.');
						dieJsonSuccess(Message::getHtml());
					}
				}
				Message::addMessage('Status updated successfully.');
				dieJsonSuccess(Message::getHtml());
			}else{
				Message::addErrorMessage($this->Blogcontributions->getError());
				dieJsonError(Message::getHtml());
			}
		}
		Message::addErrorMessage('Invalid request!!');
		dieJsonError(Message::getHtml());
		exit(0);
	}
	
	function download($filename, $display_name)
	{
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$file = $filename;
		$filename = CONF_INSTALLATION_PATH . 'user-uploads/contributions/'.$filename;
		$finfo = finfo_open(FILEINFO_MIME_TYPE); //will return mime type
		$file_mime_type = finfo_file($finfo, $filename);
		$accepted_files = array(
				'application/pdf', 
				'application/octet-stream', 
				'application/msword', 
				'text/plain', 
				'application/zip',
				'application/x-rar'
			);
		if(!in_array(trim($file_mime_type), $accepted_files)){
			return false;
		}
		
		header('Content-Type: ' . $file_mime_type);
		header('Content-Disposition: attachment; filename="'.$display_name.'"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filename));
		echo file_get_contents($filename, true);
		exit(0);
	}
	
	function delete($contribution_id, $contribution_file_name = '', $token = ''){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$contribution_id = intval($contribution_id);
		if($contribution_id < 1){
			Message::addErrorMessage('Invalid request!!');
			redirectUser(generateUrl('blogcontributions',''));
		}
		if(isset($_SESSION['stc_f_contribution_del_token']) && $token != '' && $token === $_SESSION['stc_f_contribution_del_token']){ 
			unset($_SESSION['stc_f_contribution_del_token']);
			if($this->Blogcontributions->deleteContribution($contribution_id)){
				if(isset($contribution_file_name) && is_string($contribution_file_name) && strlen($contribution_file_name) > 1){
					unlink(CONF_INSTALLATION_PATH . 'user-uploads/contributions/' .$contribution_file_name);
				}
				
				Message::addMessage('Contribution deleted successfully.');
				redirectUser(generateUrl('blogcontributions'));
			}else{
				Message::addErrorMessage($this->Blogcontributions->getError());
				redirectUser(generateUrl('blogcontributions'));
			}
		}
		$_SESSION['stc_f_contribution_del_token'] = substr(md5(microtime()), 3, 15);
		$this->set('delete_link', generateUrl('blogcontributions', 'delete', array($contribution_id, $contribution_file_name, $_SESSION['stc_f_contribution_del_token'])));
		$this->_template->render();
	}
	
}