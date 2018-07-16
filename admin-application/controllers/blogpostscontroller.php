<?php
class BlogpostsController extends Controller{
	private $permission_id;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		/* $this->permission_id = 12;*/
		$this->set('can_edit',2); 
	}
	
    function default_action(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$frm = $this->getSearchForm();
		$this->set('frmCategory', $frm);
		$this->_template->render();
    }
	
	function listBlogPosts(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_submit'])){
			$this->Blog = new Blog();
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			
			$pagesize = 10;
			$post['pagesize'] = $pagesize;
			$post_data = $this->Blogposts->getBlogpostsData($post);
			foreach($post_data as $pd){
				/* get post categories[ */
				$pd['post_categories'] = array();
				$pd['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $pd['post_id']));
				/* ] */
				$posts[] = $pd;
			}
		
			$this->set('records', $posts);
			$this->set('pages', $this->Blogposts->getTotalPages());
			$this->set('page', $page);
			$this->set('pagesize', $pagesize);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blogposts->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->_template->render(false,false);
		}
		die(0);
	}
	
	private function getSearchForm(){
		$Blogcategories_model = new Blogcategories(); 
		$frm = new Form('frmSearch', 'frmSearch');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setFieldsPerRow(4);
		$frm->captionInSameCell(true);
		$frm->addTextBox('', 'post_title', '', 'post_title', 'placeholder="Search Post Title..."');
		$frm->addSelectBox('', 'post_category',$Blogcategories_model->getAllCategories() , '', '', 'Select Post Category', 'post_category');
		$frm->addSelectBox('', 'post_status', array(0=>'Draft', 1=>'Published'), '', '', 'Select Post Status', 'post_status');
		$fld = $frm->addSubmitButton('', 'btn_submit', 'Search', 'btn_submit');
		$fld1 = $frm->addButton('', 'btn_reset', 'Reset', 'btn_reset', 'onclick="document.location.href=\''.generateUrl('blogposts').'\'" class="btn_reset" style="cursor:pointer; margin-left:10px;"');
		$fld->attachField($fld1);
		$frm->addHiddenField('', 'page', 1);
		$frm->setOnSubmit('searchPost(this); return false;');
		return $frm;
	}
	
	function add(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$frm = $this->getPostForm();
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			
			$post['post_seo_name'] = seoUrl($post['post_seo_name']);
			$post['post_content'] = $post['post_content'];
			if(!$frm->validate($post)){
				Message::addErrorMessage($frm->getValidationErrors());
				$this->set('relation_category_id', $post['relation_category_id']);
				$frm->fill($post);
			}else{	
				if(empty($post['relation_category_id'])){
					$post['relation_category_id'] = array(6);
				}
				if(!empty($_FILES['post_image_file_name']['name'])){
					$image_names = array();
					if($this->saveUploadedPostFiles($_FILES, $image_names)){
						$post['post_image_file_name'] = $image_names;
					}
					//echo "<pre>".print_r($post['post_image_file_name'],true)."</pre>";exit;				
				}
				//$post['post_comment_status'] = CONF_POST_COMMENT_STATUS;
				if(isset($post['btn_submit'])){
					if($this->Blogposts->addUpdate($post)){
						Message::addMessage('Post added successfully.');
						redirectUser(generateUrl('Blogposts', ''));
					}else{
						Message::addErrorMessage($this->Blogposts->getError());
					}
				}
				elseif(isset($post['btn_publish'])){
					$post['post_status'] = 1;
					if($this->Blogposts->addUpdate($post)){
						Message::addMessage('Post added successfully.');
						redirectUser(generateUrl('Blogposts', ''));
					}else{
						Message::addErrorMessage($this->Blogposts->getError());
					}
					if($this->Blogposts->addUpdate($post)){
						Message::addMessage('Post added successfully.');
						redirectUser(generateUrl('Blogposts', ''));
					}else{
						Message::addErrorMessage($this->Blogposts->getError());
					}
				}
			}	
		}
		$frm->getField('post_image_file_name[]')->html_after_field = '<a class="button small green" id="add_more_field">Add More</a> <b class="b_size_class">Image Size Should be 800x300px</b>';
		$categories = $this->Blogposts->getAllCategories();
		$categories1 = $this->sortCategories($categories);
		//printR($categories1);
		$this->set('categories', $categories1);
		$this->set('frmAdd', $frm);
		$this->_template->render();
	}
	
	function edit($blog_post_id){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$blog_post_id = intval($blog_post_id);
		if($blog_post_id < 1){
			Message::addErrorMessage('Invalid request !!');
			redirectUser(generateUrl('Blogposts'));
		}
		$post_images = $this->Blogposts->getPostImages($blog_post_id);
		$frm = $this->getPostForm();
		$frm->getField('category_title')->extra = '';
		$post = Syspage::getPostedVar();
		
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$post['post_seo_name'] = seoUrl($post['post_seo_name']);
			$post['post_content'] = $post['post_content'];
			if(!$frm->validate($post)){
				$this->set('relation_category_id', $post['relation_category_id']);
				$frm->fill($post);
				Message::addErrorMessage($frm->getValidationErrors());
			}else{
				if(empty($post['relation_category_id'])){
						$post['relation_category_id'] = array(6);
					}
				if(!empty($_FILES['post_image_file_name']['name'])){
					$image_names = array();
					if($this->saveUploadedPostFiles($_FILES, $image_names)){
						$post['post_image_file_name'] = $image_names;
					}
				}
				if(isset($post['btn_submit'])){
					if($post['post_id'] != $blog_post_id){
						Message::addErrorMessage('Invalid request!!');
						redirectUser(generateUrl('Blogposts', ''));
					}else{
						$remove = array();
						
						if(isset($post['post_removed_images']) && strlen($post['post_removed_images']) > 0 && $this->setRemoveImageData($post['post_removed_images'], $post_images['imgs'], $remove)){
							$post['remove_post_images'] = $remove;
						}
						//echo "<pre>".print_r($post['post_removed_images'],true)."</pre>";exit;
						if($this->Blogposts->addUpdate($post)){
							Message::addMessage('Post updated successfully.');
							redirectUser(generateUrl('Blogposts', ''));
						}else{
							Message::addErrorMessage($this->Blogposts->getError());
						}
					}
				}
				elseif(isset($post['btn_publish'])){
					$post['post_status'] = 1;
					$remove = array();
					if(isset($post['post_removed_images']) && strlen($post['post_removed_images']) > 0 && $this->setRemoveImageData($post['post_removed_images'], $post_images['imgs'], $remove)){
						$post['remove_post_images'] = $remove;
					}
					if($this->Blogposts->addUpdate($post)){
						Message::addMessage('Post added successfully.');
						redirectUser(generateUrl('Blogposts', ''));
					}else{
						Message::addErrorMessage($this->Blogposts->getError());
					}
				}
			}
		}
		else{
			$post_data = $this->Blogposts->getBlogPost($blog_post_id);
			$relation_category_ids = $post_data['relation_category_ids'];
			$relation_category_ids = explode(',', $relation_category_ids);
			$this->set('relation_category_id', $relation_category_ids);
			$frm->fill($post_data);
		}
		$frm->getField('post_image_file_name[]')->html_after_field = '<a class="button small green" id="add_more_field">Add More</a> <b class="b_size_class">Image Size Should be 800x300px</b>';
		if(isset($post_images['imgs']) && is_array($post_images['imgs']) && sizeof($post_images['imgs']) > 0){
			$photo_html = '<a class="button small green" id="add_more_field">Add More</a> <b class="b_size_class">Image Size Should be 800x300px</b>';
			$photo_html .= '<div class="photosrow" id="post_imgs">';
			$photo_html .= $this->getImagesHtml($post_images, $blog_post_id, 'post');
			$photo_html .= '</div>';
			$frm->getField('post_image_file_name[]')->html_after_field = $photo_html;
		}
		
		$frm->getField('post_title')->extra = '';
		$categories = $this->Blogposts->getAllCategories();
		$categories1 = $this->sortCategories($categories);
		$this->set('post_data', $post_data);
		$this->set('categories', $categories1);
		$this->set('frmEdit', $frm);
		//Syspage::addJs(array(CONF_THEME_PATH . 'blogposts/page-js/add.js'));
		$this->_template->render();
	}
	
	private function getImagesHtml($product_images, $product_id, $image_folder){
		$photo_html = '';
		if(isset($product_images['imgs']) && is_array($product_images['imgs']) && sizeof($product_images['imgs']) > 0){
			foreach($product_images['imgs'] as $id=>$img){
				$photo_html .= '<div class="photosquare"><img alt="" src="' . generateUrl('image', $image_folder, array('thumb', $img)) . '"> <a class="crossLink" href="javascript:void(0)" onclick="return removeImage(this, ' . intval($id) . ');">Remove Image</a>';
				if(!(isset($product_images['main_img']) && $product_images['main_img'] == $id)){
					$photo_html .= '<a class="linkset button small black" href="javascript:void(0)" onclick="setMainImage(this, ' . intval($id) . ', ' . intval($product_id) . ');">Set Main Image</a>';
				}
				$photo_html .= '</div>';
			}
		}
		return $photo_html;
	}
	
	private function getPostForm(){
		//if(Admin::canAdminEdit($this->permission_id) != 2) die('Unauthorized Access!');
		$frm = new Form('frmPost', 'frmPost');
		$frm->setExtra('class="siteForm"');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setTableProperties('class="formTable" width="100%" cellspacing="0" cellpadding="0" border="0"');
		$frm->setLeftColumnProperties('width="25%"');
		$frm->setRequiredStarWith('caption');
		$frm->addRequiredField('Post Title', 'post_title', '', 'post_title', 'onblur="setSeoName(this, post_seo_name)"');
		$fld_cat = $frm->addRequiredField('Post SEO Name', 'post_seo_name', '', 'post_seo_name', 'onblur="setSeoName(this, post_seo_name)"');
		$fld_cat->setUnique('tbl_blog_post', 'post_seo_name', 'post_id', 'post_id', 'post_id');
		$frm->addTextBox('Contributor Name', 'post_contibutor_name', '', 'post_contibutor_name');
		$frm->addTextArea('Post Short Description', 'post_short_description', '', 'post_short_description');
		$frm->addHtmlEditor('Post Content', 'post_content', '')->requirements()->setRequired(true);
		$frm->addSelectBox('Post Status', 'post_status', array(0=>'Draft', 1=>'Published'), '0', '', '', 'post_status');
		$frm->addSelectBox('Post Comment Status', 'post_comment_status', array(0=>'Not Open', 1=>'Open'),CONF_POST_COMMENT_STATUS, '', '', 'post_comment_status');
		$frm->addFileUpload('Post Image', 'post_image_file_name[]', '', 'accept="image/*"');
		$frm->addTextBox('Meta Title', 'meta_title', '', 'meta_title');
		$frm->addTextArea('Meta Keywords', 'meta_keywords', '', 'meta_keywords');
		$frm->addTextArea('Meta Description', 'meta_description', '', 'meta_description');
		$fld=$frm->addTextArea('Meta Others', 'meta_others', '', 'meta_others');
		$fld->html_after_field = '<em>Note: Meta Others are HTML meta tags, e.g &lt;meta name="example" content="example" /&gt;. We are not validating these tags, please take care of this.</em>';
		$frm->addHiddenField('', 'post_id', '', 'post_id');
		$frm->addHiddenField('', 'meta_id', '', 'meta_id');
		$frm->addHiddenField('', 'post_removed_images', '', 'post_removed_images');
		$frm->addSubmitButton('', 'btn_submit', 'Submit', 'btn_submit');
		//$frm->addSubmitButton('', 'btn_publish', 'Publish', 'btn_publish');
		return $frm;
	}
	
	private function sortCategories(array $elements, $parentId = 0) {
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$branch = array();
		foreach ($elements as $element) {
			if ($element['category_parent'] == $parentId) {
				$children = $this->sortCategories($elements, $element['category_id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
	
	private function saveUploadedPostFiles(&$files, &$image_names){
		if(is_array($files['post_image_file_name']['name'])){
			foreach($files['post_image_file_name']['name'] as $id => $filename){
				if(is_uploaded_file($files['post_image_file_name']['tmp_name'][$id])){
					$saved_image_name = '';
					if(saveImage($files['post_image_file_name']['tmp_name'][$id], $files['post_image_file_name']['name'][$id], $saved_image_name, false, 'post-images/')){
						$image_names[] = $saved_image_name;
					}else{
						Message::addErrorMessage($files['post_image_file_name']['name'][$id] . ': ' .$saved_image_name);
					}
				}
			}
		}elseif(is_uploaded_file($files['post_image_file_name']['tmp_name'])){
			$saved_image_name = '';
			if(saveImage($files['post_image_file_name']['tmp_name'], $files['post_image_file_name']['name'], $saved_image_name, 'post-images/')){
				$image_names[] = $saved_image_name;
			}else{
				Message::addErrorMessage($files['post_image_file_name']['name'] . ': ' .$saved_image_name);
			}
		}
		if(sizeof($image_names) > 0) return true;
		return false;
	}
	
	function setMainImage(){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['imgid']) && intval($post['imgid']) > 0){
			$blog_post_id = intval($post['blog_post_id']);
			if($this->Blogposts->setMainImage(intval($post['imgid']), $blog_post_id)){
				//die('<pre>'.print_r($post,true).'<pre>');
				$post_images = $this->Blogposts->getPostImages($blog_post_id);
				die($this->getImagesHtml($post_images, $blog_post_id, 'post'));
			}
		}
		Message::addErrorMessage('Invalid request!!');
		die(Message::getHtml());
	}
	function delete($post_id, $relation_category_id, $token = ''){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$post_id = intval($post_id);
		$relation_category_id = intval($relation_category_id);
		if($post_id < 1 || $relation_category_id < 1){
			Message::addErrorMessage('Invalid request!!');
			redirectUser(generateUrl('blogposts',''));
		}
		if(isset($_SESSION['stc_f_post_del_token']) && $token != '' && $token === $_SESSION['stc_f_post_del_token']){
			unset($_SESSION['stc_f_post_del_token']);
			if($this->Blogposts->deletePost($post_id, $relation_category_id)){
				Message::addMessage('Post deleted successfully.');
				redirectUser(generateUrl('blogposts'));
			}else{
				Message::addErrorMessage($this->Blogposts->getError());
				redirectUser(generateUrl('blogposts'));
			}
		}
		$_SESSION['stc_f_post_del_token'] = substr(md5(microtime()), 3, 15);
		$this->set('delete_link', generateUrl('blogposts', 'delete', array($post_id, $relation_category_id, $_SESSION['stc_f_post_del_token'])));
		$this->_template->render();
	}	
	
	function setRemoveImageData(&$img_id_str, &$saved_imgs, &$imgs_to_remove){
		if (!Admin::isLogged()) {	redirectUser(generateUrl('admin', 'loginform'));	return false;	}
		$img_ids = explode(',', $img_id_str);
		if(is_array($img_ids) && sizeof($img_ids) > 0){
			foreach($img_ids as $img_id){
				if(isset($saved_imgs[$img_id]) && strlen($saved_imgs[$img_id]) > 3){
					$imgs_to_remove[$img_id] = $saved_imgs[$img_id];
				}
			}
			if(sizeof($imgs_to_remove) > 0){
				return true;
			}
		}
		return false;
	}
}