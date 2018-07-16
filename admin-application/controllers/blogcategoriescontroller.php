<?php
class BlogcategoriesController extends Controller{
	private $permission_id;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		//$this->permission_id = 12;
		$this->set('can_edit',2);
	}
	
    function default_action(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		//if(!Admin::canAdminEdit($this->permission_id)) die('Unauthorized Access!');
		$frm = $this->getSearchForm();
		$this->set('frmCategory', $frm);
		$this->_template->render();
    }
	
	function listBlogCategories(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_submit'])){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			
			$pagesize = 10;
			$post['pagesize'] = $pagesize;
			$this->set('records', $this->Blogcategories->getBlogcategoriesData($post));
			$this->set('pages', $this->Blogcategories->getTotalPages());
			$this->set('page', $page);
			$this->set('pagesize', $pagesize);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blogcategories->getTotalRecords();
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
		$frm->addTextBox('', 'category_title', '', 'category_title', 'Placeholder="Search Category Title..."');
		$frm->addSelectBox('', 'category_status', array(0=>'Inactive', 1=>'Active'), '', '', 'Select Category Status', 'category_status');
		$fld = $frm->addSubmitButton('', 'btn_submit', 'Search', 'btn_submit');
		$fld1 = $frm->addButton('', 'btn_reset', 'Reset', 'btn_reset', 'onclick="document.location.href=\''.generateUrl('blogcategories').'\'" class="btn_reset" style="cursor:pointer; margin-left:10px;"');
		$fld->attachField($fld1);
		$frm->addHiddenField('', 'page', 1);
		$frm->setOnSubmit('searchBlogCatogries(this); return false;');
		return $frm;
	}
	
	function add(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$frm = $this->getCategoryForm();
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])){
			if(!$frm->validate($post)){
				$frm->fill($post);
				Message::addErrorMessage($frm->getValidationErrors());
			}else{
				if($this->Blogcategories->addUpdate($post)){
					Message::addMessage('Category added successfully.');
					redirectUser(generateUrl('Blogcategories', ''));
				}else{
					Message::addErrorMessage($this->Blogcategories->getError());
				}
			}
		}
		$this->set('frmAdd', $frm);
		$this->_template->render();
	}
	
	function edit($blog_category_id){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$blog_category_id = intval($blog_category_id);
		if($blog_category_id < 1){
			Message::addErrorMessage('Invalid request !!');
			redirectUser(generateUrl('blogcategories'));
		}
		
		$frm = $this->getCategoryForm();
		$frm->getField('category_title')->extra = '';
		$post = Syspage::getPostedVar();
		
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])){
			if(!$frm->validate($post)){
				$frm->fill($post);
				Message::addErrorMessage($frm->getValidationErrors());
			}else{
				if($post['category_id'] != $blog_category_id){
					Message::addErrorMessage('Invalid request!!');
					redirectUser(generateUrl('blogcategories', ''));
				}else{
					if($this->Blogcategories->addUpdate($post)){
						Message::addMessage('Category updated successfully.');
						redirectUser(generateUrl('blogcategories', ''));
					}else{
						Message::addErrorMessage($this->Blogcategories->getError());
					}
				}
			}
		}
		$categories = $this->Blogcategories->getAllCategories($blog_category_id);
		$frm->getField('category_parent')->options = $categories;
		$blog_category = $this->Blogcategories->getBlogcategory($blog_category_id);
		$frm->fill($blog_category);
		$this->set('frmEdit', $frm);
		Syspage::addJs(array(CONF_THEME_PATH . 'blogcategories/page-js/add.js'));
		$this->_template->render();
	}
	
	private function getCategoryForm(){
		$frm = new Form('frmBlogCategory', 'frmBlogCategory');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setRequiredStarWith('caption');
		$frm->setTableProperties('class="formTable" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setLeftColumnProperties('width="25%"');
		$fld = $frm->addRequiredField('Category Title', 'category_title', '', 'category_title', 'onblur="setSeoName(this, category_seo_name)"');
		$fld->setUnique('tbl_blog_post_categories', 'category_title', 'category_id', 'category_id', 'category_id');
		$fld_cat = $frm->addRequiredField('Category SEO Name', 'category_seo_name', '', 'category_seo_name', 'onblur="setSeoName(this, category_seo_name)"');
		$fld_cat->setUnique('tbl_blog_post_categories', 'category_seo_name', 'category_id', 'category_id', 'category_id');
		/*$frm->addTextArea('Category Description', 'category_description', '', 'category_description');*/
		$frm->addSelectBox('Category Status', 'category_status', array(0=>'Inactive', 1=>'Active'), '0', '', '', 'category_status');
		$categories = $this->Blogcategories->getAllCategories();
		$frm->addSelectBox('Category Parent', 'category_parent', $categories, 0, '', 'Select', 'category_parent');
		$frm->addTextBox('Meta Title', 'meta_title', '', 'meta_title');
		$frm->addTextArea('Meta Keywords', 'meta_keywords', '', 'meta_keywords');
		$frm->addTextArea('Meta Description', 'meta_description', '', 'meta_description');
		$fld2=$frm->addTextArea('Meta Others', 'meta_others', '', 'meta_others');
		$fld2->html_after_field = '<em>Note: Meta Others are HTML meta tags, e.g &lt;meta name="example" content="example" /&gt;. We are not validating these tags, please take care of this.</em>';
		$frm->addHiddenField('', 'category_id', '', 'category_id');
		$frm->addHiddenField('', 'meta_id', '', 'meta_id');
		$frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit');
		return $frm;
	}
	
	function delete($category_id, $token = ''){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$category_id = intval($category_id);
		if($category_id < 1){
			Message::addErrorMessage('Invalid request!!');
			redirectUser(generateUrl('blogcategories',''));
		}
		if(isset($_SESSION['stc_f_category_del_token']) && $token != '' && $token === $_SESSION['stc_f_category_del_token']){ 
			unset($_SESSION['stc_f_category_del_token']);
			if($this->Blogcategories->chkPostOfCategory($category_id)){
				if($this->Blogcategories->deleteCategory($category_id)){
					Message::addMessage('Category deleted successfully.');
					redirectUser(generateUrl('blogcategories'));
				}else{
					Message::addErrorMessage($this->Blogcategories->getError());
					redirectUser(generateUrl('blogcategories'));
				}
			}else{
				Message::addErrorMessage('You cannot delete this category, there are posts associated with this category.');
				redirectUser(generateUrl('blogcategories'));
			}
			
		}
		$_SESSION['stc_f_category_del_token'] = substr(md5(microtime()), 3, 15);
		$this->set('delete_link', generateUrl('blogcategories', 'delete', array($category_id, $_SESSION['stc_f_category_del_token'])));
		$this->_template->render();
	}
	
}