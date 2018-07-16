<?php
class BlogcommentsController extends Controller{
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
		$this->set('frmComment', $frm);
		$this->_template->render();
    }
	
	private function getSearchForm(){
		$frm = new Form('frmComment', 'frmComment');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable threeCol" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setFieldsPerRow(4);
		$frm->captionInSameCell(true);
		$frm->addTextBox('', 'comment_author_name', '', 'comment_author_name', 'placeholder="Search Comment Author Name..."');
		$frm->addSelectBox('', 'comment_status', array(0=>'Pending', 1=>'Approved', 2=>'Deleted'), '', '', 'Select Comment Status', 'comment_status');
		$fld = $frm->addSubmitButton('', 'btn_submit', 'Search', 'btn_submit');
		$fld1 = $frm->addButton('', 'btn_reset', 'Reset', 'btn_reset', 'onclick="document.location.href=\''.generateUrl('blogcomments').'\'" class="btn_reset" style="cursor:pointer; margin-left:10px;"');
		$fld->attachField($fld1);
		$frm->addHiddenField('', 'page', 1);
		$frm->setOnSubmit('searchPost(this); return false;');
		return $frm;
	}
	
	function listComments($data)
	{	
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_submit'])){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			
			$pagesize = 10;
			$post['pagesize'] = $pagesize;
			$this->set('records', $this->Blogcomments->getBlogComments($post));
			$this->set('pages', $this->Blogcomments->getTotalPages());
			$this->set('page', $page);
			$this->set('pagesize', $pagesize);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blogcomments->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->_template->render(false,false);
		}
		die(0);
	}
	
	function view($comment_id){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$comment_id = intval($comment_id);
		if($comment_id < 1){
			Message::addErrorMessage('Invalid request !!');
			redirectUser(generateUrl('blogcomments'));
		}
		$comment_data =  $this->Blogcomments->getComment($comment_id);
		//echo "<pre>".print_r($comment_data,true)."</pre>";
		$status_array = array('0'=>'Pending', '1'=>'Approved', '2'=>'Deleted');
		//unset($status_array[$comment_data['comment_status']]);
		$frm = $this->getCommentStatusUpdateForm();
		$frm->getField('comment_status')->options = $status_array;
		//$frm->getField('comment_status')->value = $status_array[$comment_data['comment_status']];
		$frm->getField('comment_id')->value = $comment_data['comment_id'];
		$frm->fill(array('comment_status'=>$comment_data['comment_status']));
		$this->set('frmComment', $frm);
		$this->set('comment_data', $comment_data);
		$this->_template->render();
	}
	
	private function getCommentStatusUpdateForm()
	{
		$frm = new Form('frmBlogComments', 'frmBlogComments');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setExtra('class="siteForm"');
		$frm->addHiddenField('', 'comment_id', '');
		$status_array = array('0'=>'Pending', '1'=>'Approved', '2'=>'Deleted');
		$frm->addSelectBox('Status', 'comment_status', $status_array, '', '', 'Select', 'comment_status');
		$frm->addSubmitButton('', 'update', 'Update', 'update');
		$frm->setOnSubmit('updateStatus(this); return false;');
		return $frm;
	}
	
	function updateStatus()
	{
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $post['comment_status'] !=''){
			if($this->Blogcomments->updateStatus($post)){
				Message::addMessage('Status updated successfully.');
				dieJsonSuccess(Message::getHtml());
			}else{
				Message::addErrorMessage($this->Blogcomments->getError());
				dieJsonError(Message::getHtml());
			}
		}
		Message::addErrorMessage('Invalid request!!');
		dieJsonError(Message::getHtml());
		exit(0);
	}
	
	function delete($comment_id, $token = ''){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$comment_id = intval($comment_id);
		if($comment_id < 1){
			Message::addErrorMessage('Invalid request!!');
			redirectUser(generateUrl('blogcomments',''));
		}
		if(isset($_SESSION['stc_f_comment_del_token']) && $token != '' && $token === $_SESSION['stc_f_comment_del_token']){
			unset($_SESSION['stc_f_comment_del_token']);
			if($this->Blogcomments->deleteComment($comment_id)){
				Message::addMessage('Blog comment deleted successfully.');
				redirectUser(generateUrl('blogcomments'));
			}else{
				Message::addErrorMessage($this->Blogcomments->getError());
				redirectUser(generateUrl('blogcomments'));
			}
		}
		$_SESSION['stc_f_comment_del_token'] = substr(md5(microtime()), 3, 15);
		$this->set('delete_link', generateUrl('blogcomments', 'delete', array($comment_id, $_SESSION['stc_f_comment_del_token'])));
		$this->_template->render();
	}
	
}