<?php
class BlogController extends LegitController {
	
	function default_action(){
		$this->right_panel();
				
		$Meta = new Meta();
		$meta = $Meta->getMetaById(6);		
		
		$arr_listing=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);

		$this->set('arr_listing', $arr_listing);
		$this->_template->render();
    }
	
	function category($cat_slug){
		
		if(empty($cat_slug)){
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			redirectUser(generateUrl('blog'));
		}
		$frm = $this->getCategoryForm();
		$frm->getField('cat_slug')->value = $cat_slug;
		$meta_data_record = $this->Blog->getCategoryMetaDataByCatSlug($cat_slug);
		$meta_data = array(
					'meta_title'=>$meta_data_record['meta_title'],
					'meta_description'=>$meta_data_record['meta_description'],
					'meta_keywords'=>$meta_data_record['meta_keywords'],
					'other_tags'=>$meta_data_record['meta_others']
				);
		$this->set('arr_listing', $meta_data);
		
		$this->set('frmCategory', $frm);
		$this->right_panel();
		$this->_template->render();
    }
	
	function archives($year, $month){
		
		if($year<1 || $month<1){
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			redirectUser(generateUrl('blog'));
		}
		$frm = $this->getArchivesForm();
		$frm->getField('year')->value = $year;
		$frm->getField('month')->value = $month;
		$this->set('frmArchives', $frm);
		$this->right_panel();
		$this->_template->render();
    }
	
	function listArchivesPost(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;

			$pagesize = 10;
			#$pagesize = CONF_PAGINATION_LIMIT;
			$post['pagesize'] = $pagesize;
			$post_data = $this->Blog->getArchivesPost($post);
			$posts = array();
			if($post_data){
				foreach($post_data as $pd){
					/* get post categories[ */
					$pd['post_categories'] = array();
					$pd['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $pd['post_id']));
					/* ] */
					$posts[] = $pd;
				}
			}
			
			
			$this->set('records', $posts);
			$this->set('pages', $this->Blog->getTotalPages());
			$this->set('page', $page);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blog->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->right_panel();
			$this->_template->render(false,false);
		}
		die(0);
	}
	
	function listCatPost(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;

			$pagesize = 10;
			#$pagesize = CONF_PAGINATION_LIMIT;
			$post['pagesize'] = $pagesize;
			$posts_data = $this->Blog->getCatPosts($post);
			$posts = array();
			if( $posts_data ){
				foreach($posts_data as $pd){
					/* get post categories[ */
					$pd['post_categories'] = array();
					$pd['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $pd['post_id']));
					/* ] */
					$posts[] = $pd;
				}
			}
			$this->set('records', $posts);
			$this->set('pages', $this->Blog->getTotalPages());
			$this->set('page', $page);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blog->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->right_panel();
			$this->_template->render(false,false);
		}
		die(0);
	}
	
	private function right_panel(){
		$pagesize = 3;
		#$pagesize = CONF_PAGINATION_LIMIT;
		$post['pagesize'] = $pagesize;
		$this->set('recent_post', $this->Blog->getRecentPost($post)); 
		$all_categories = $this->Blog->getAllCategories(); 
		$sort_categories = $this->sortCategories($all_categories);
		$this->set('categories', $sort_categories); 
		$archives = $this->Blog->getArchives();
		$this->set('archives', $archives);
		$frm = $this->getSearchForm();
		$frm->setRequiredStarPosition('0');
		$this->set('frmSearchForm', $frm);
    }
	
	private function getSearchForm(){
		$frm = new Form('frmSearchPost', 'frmSearchPost');
		$frm->setAction(generateUrl('blog', 'search'));
		$frm->setExtra('class=""');
		$frm->setJsErrorDisplay('afterfield');
		$frm->setRequiredStarWith('None');
		$frm->addHiddenField('', 'page', 1);
		$frm->addRequiredField('' . Utilities::getLabel( 'L_Search' ) . '', 'search', '', 'search', 'title="' . Utilities::getLabel( 'L_Search_Text' ) . '" placeholder="' . Utilities::getLabel( 'L_Search' ) . '"');
		$frm->addSubmitButton('', 'btn_submit', '', 'btn_submit', ' class="search-btn" ');
		return $frm;
	}
	
	function search(){
		
		$this->right_panel();
		$frm = $this->getSearchForm();
		
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])){
			if(!$frm->validate($post)){
				Message::addErrorMessage($frm->getValidationErrors());
				redirectUser(generateUrl('blog'));
			}else{
				$frm->getfield('search')->value=$post['search'];
				$this->set('frmSearchForm', $frm);
				$page = 1;
				if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
				else $post['page'] = $page;

				#$pagesize = 10;
				$pagesize = CONF_PAGINATION_LIMIT;
				$post['pagesize'] = $pagesize;
			
				if($records = $this->Blog->getSearchPost($post)){
					if( $records ){
						foreach($records as $record){
							/* get post categories[ */
							$record['post_categories'] = array();
							$record['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $record['post_id']));
							/* ] */
							$posts[] = $record;
						}
					}
					$this->set('records', $posts);
				}
			}
		}
		$this->_template->render();
    }
	
	function contribution(){
		$frm = $this->getContributeForm();
		$post = Syspage::getPostedVar();
		if(User::getLoggedUserAttribute('user_id')){
			$frm->getField('contribution_author_first_name')->value = User::getLoggedUserAttribute('user_first_name');
			$frm->getField('contribution_author_last_name')->value = User::getLoggedUserAttribute('user_last_name');
			$frm->getField('contribution_author_email')->value = User::getLoggedUserAttribute('user_email');
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])){
			//echo 
			require_once 'securimage/securimage.php';
			$img = new Securimage();
			$security_code = $post['security_code'];
			if(!$img->check($security_code)){
				unset($_FILES['contribution_file_name']);
				unset($post['security_code']);
				$frm->fill($post);
				Message::addErrorMessage( Utilities::getLabel( 'L_Incorrect_Security_Code_Please_Try_Again' ) );
			}else{ 
				
				$fld = $frm->getField('contribution_file_name');
				$fld->requirements()->setRequired(False);
								
				if(!$frm->validate($post)){
					Message::addErrorMessage($frm->getValidationErrors());
					redirectUser(generateUrl('blog', 'contribution'));
				}else{
					/* echo "<pre>".filesize($_FILES['contribution_file_name']['tmp_name'])."</pre><br>".(1024 * 1024 * CONF_CONTRIBUTION_FILE_UPLOAD_SIZE);exit; */
					$error = 0;
					if(empty($_FILES['contribution_file_name']['name'])) {
						unset($post['security_code']);
						$frm->fill($post);
						Message::addErrorMessage( Utilities::getLabel( 'L_Please_upload_the_file' ) );
						$error = 1;
					}
					if(!empty($_FILES['contribution_file_name']['name']) && $_FILES['contribution_file_name']['size'] == 0) {
						unset($post['security_code']);
						$frm->fill($post);
						Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
						$error = 1;
					}
					
					if(!empty($_FILES['contribution_file_name']['name'])){
						if($_FILES['contribution_file_name']['size'] > (1024 * 1024 * CONF_CONTRIBUTION_FILE_UPLOAD_SIZE)){
							unset($post['security_code']);
							$frm->fill($post);
							Message::addErrorMessage( Utilities::getLabel( 'L_File_size_should_not_exceed' ) . CONF_CONTRIBUTION_FILE_UPLOAD_SIZE . " MB" );
							$error = 1;
						}else{
							if(is_uploaded_file($_FILES['contribution_file_name']['tmp_name'])){
								$saved_image_name = '';
								if(!uploadContributionFile($_FILES['contribution_file_name']['tmp_name'], $_FILES['contribution_file_name']['name'], $saved_image_name, 'contributions/')){
									Message::addErrorMessage($saved_image_name);
									unset($_FILES['contribution_file_name']);
									unset($post['security_code']);
									$frm->fill($post);
									$error = 1;
								}else{
									$post['contribution_file_display_name'] = $_FILES['contribution_file_name']['name'];
									$post['contribution_file_name'] = $saved_image_name;
								}
							}
						}
					}else{
						$error = 1;
						Message::addErrorMessage( Utilities::getLabel( 'L_Upload_File_is_mandatory' ) );
					}
					
					if($error == 0){
						if($this->Blog->addContributions($post)){
							Message::addMessage( Utilities::getLabel( 'L_Details_saved_successfully' ) );
							redirectUser(generateUrl('blog', 'contribution'));
						}else{
							Message::addErrorMessage( Utilities::getLabel( 'E_Details_Not_Saved' ) );
						}
					}	
				}
			}
		}
		
		$this->set('frmContribute', $frm);
		$frm->setRequiredStarPosition('0');
		$this->_template->render();
    }
	
	private function getContributeForm(){
		$frm = new Form('frmContribute', 'frmContribute');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="user-form-table"');
		$frm->setJsErrorDiv('common_error_msg');
		//$frm->setJsErrorDisplay('afterfield');
		$frm->setRequiredStarPosition('None');
		$frm->addRequiredField('', 'contribution_author_first_name', '', 'contribution_author_first_name', 'title="' . Utilities::getLabel( 'L_First_Name' ) . '" placeholder="' . Utilities::getLabel( 'L_First_Name' ) . '*"');
		$frm->addRequiredField('', 'contribution_author_last_name', '', 'contribution_author_last_name', 'title="' . Utilities::getLabel( 'L_Last_Name' ) . '" placeholder="' . Utilities::getLabel( 'L_Last_Name' ) . '*"');
		$frm->addEmailField('', 'contribution_author_email', '', 'contribution_author_email', 'title="' . Utilities::getLabel( 'L_Email' ) . '" placeholder="' . Utilities::getLabel( 'L_Email' ) . '*"');
		$frm->addTextBox('', 'contribution_author_phone', '', 'contribution_author_phone', 'title="' . Utilities::getLabel( 'L_Phone_Number' ) . '" placeholder="' . Utilities::getLabel( 'L_Phone_Number' ) . '"');
		$frm->addFileUpload( Utilities::getLabel( 'L_Upload_File' ), 'contribution_file_name', 'uploadBtn','class="input-type"')->requirements()->setRequired();
		$frm->addRequiredField('', 'security_code', '', '',  'title="' . Utilities::getLabel( 'L_Security_Code' ) . '"  placeholder="' . Utilities::getLabel( 'L_Security_Code' ) . '*"');
		$frm->addHTML('', 'captcha', '<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="captcha_images" height="35">
    	 &nbsp; <a href="javascript:void(0);" onclick="document.getElementById(\'captcha_images\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></i></a>');
		$frm->addHiddenField('', 'contribution_user_id', User::getLoggedUserAttribute('user_id'), 'contribution_user_id');
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Submit' ), 'btn_submit','class="theme-btn"');
		return $frm;
	}
	
	private function getCategoryForm(){
		$frm = new Form('frmCategory', 'frmCategory');
		$frm->addHiddenField('', 'page', 1);
		$frm->addHiddenField('', 'cat_slug', '', 'cat_slug');
		return $frm;
	}
	
	private function getArchivesForm(){
		$frm = new Form('frmArchives', 'frmArchives');
		$frm->addHiddenField('', 'page', 1);
		$frm->addHiddenField('', 'year', '', 'year');
		$frm->addHiddenField('', 'month', '', 'month');
		return $frm;
	}
	private function getCommentForm(){
		$frm = new Form('frmComment', 'frmComment');
		$frm->setExtra('class="siteForm"');
		$frm->setTableProperties('class="comment-frm"');
		//$frm->setJsErrorDisplay('afterfield');
		$frm->setJsErrorDiv('common_error_msg');
		
		$fld = $frm->addRequiredField('', 'comment_author_name', '', 'comment_author_name', 'title="Name"');
		// $fld->setRequiredStarWith('none');
		$frm->addEmailField( Utilities::getLabel( 'L_Email' ), 'comment_author_email', '', 'comment_author_email', 'title="' . Utilities::getLabel( 'L_Email' ) . '"');
		$frm->addRequiredField( Utilities::getLabel( 'L_Name' ), 'security_code', '', 'security_code',  'title="' . Utilities::getLabel( 'L_Security_Code' ) . '" placeholder="' . Utilities::getLabel( 'L_Security_Code' ) . '"');
		$frm->addHTML('', 'captcha', '<img src="' . CONF_WEBROOT_URL . 'securimage/securimage_show.php" id="captcha_image" height="35">
    	 &nbsp; <a href="javascript:void(0);" onclick="document.getElementById(\'captcha_image\').src = \'' . CONF_WEBROOT_URL . 'securimage/securimage_show.php?sid=\' + Math.random(); return false"><i class="icon ion-loop"></a>');
		$frm->addTextArea( Utilities::getLabel( 'L_Comment' ), 'comment_content', '', 'comment_content', 'class="textarea_resize" title="' . Utilities::getLabel( 'L_Comment' ) . '"')->requirements()->setRequired(true);
		$frm->addHiddenField('', 'comment_post_id', '', 'comment_post_id');
		$frm->addHiddenField('', 'comment_user_id', User::getLoggedUserAttribute('user_id'), 'comment_user_id');
		$frm->addSubmitButton('', 'btn_submit', Utilities::getLabel( 'L_Post_Comment' ), 'btn_submit','class="grydtn-btn"');
		$frm->setRequiredStarWith('none');
		//die($frm->getFormHtml(true));
		return $frm;
	}
	
	function listPost(){
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			$pagesize = 10;
			#$pagesize = CONF_PAGINATION_LIMIT;
			$post['pagesize'] = $pagesize;
			//echo "<pre>".print_r($this->Blog->getBlogPosts($post),true)."</pre>";
			$post_data = $this->Blog->getBlogPosts($post);
			if($post_data){
				foreach($post_data as $pd){
					/* get post categories[ */
					$pd['post_categories'] = array();
					$pd['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $pd['post_id']));
					/* ] */
					$posts[] = $pd;
				}
			}
			$this->set('records', $posts);
			$this->set('pages', $this->Blog->getTotalPages());
			$this->set('page', $page);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blog->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->_template->render(false,false);
		}
		die(0);
	}
	private function sortCategories(array $elements, $parentId = 0) {
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
	
	function post($post_slug)
	{
		
		if(empty($post_slug)){
			Message::addErrorMessage( Utilities::getLabel( 'E_Invalid_request' ) );
			redirectUser(generateUrl('blog'));
		}
		$frm = $this->getCommentForm();
		if(User::getLoggedUserAttribute('user_id')){
			$frm->removeField($frm->getField('comment_author_name'));
			$frm->removeField($frm->getField('comment_author_email'));
		}	
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($post['btn_submit'])){
			require_once 'securimage/securimage.php';
			$img = new Securimage();
			if(!$img->check($post['security_code'])){
				unset($post['security_code']);
				$frm->fill($post);
				Message::addErrorMessage( Utilities::getLabel( 'L_Incorrect_Security_Code_Please_Try_Again' ) );
			}
			else{
				if(!$frm->validate($post)){
					unset($post['security_code']);
					$frm->fill($post);
					Message::addErrorMessage($frm->getValidationErrors());
				}else{
					if(User::getLoggedUserAttribute('user_id')){
						$post['comment_author_name'] = User::getLoggedUserAttribute('user_screen_name');
						$post['comment_author_email'] = User::getLoggedUserAttribute('user_email');
					}
					//echo "<pre>".print_r($post,true)."</pre>";exit;
					if($this->Blog->addComment($post)){
						Message::addMessage( Utilities::getLabel( 'L_Your_comment_is_awaiting_moderation' ) );
						redirectUser(generateUrl('blog', 'post', array($post_slug)));
					}else{
						Message::addErrorMessage( Utilities::getLabel( 'E_Details_Not_Saved' ) );
					}
				}
			}
		}
		
		$this->right_panel();
		$post_data = $this->Blog->getPost($post_slug);		
		if(!$post_data)
		{			
			redirectUser(generateUrl('page','error_404'));		
		}
		$post_comment_count =  $this->Blog->getPostCommentCount($post_data['post_id']);
		$this->set('comment_count', $post_comment_count[0]['comment_count']);
		$meta_data = array(
					'meta_title'=>$post_data['meta_title'],
					'meta_description'=>$post_data['meta_description'],
					'meta_keywords'=>$post_data['meta_keywords'],
					'other_tags'=>$post_data['meta_others']
				);
		$this->set('arr_listing', $meta_data);
		if($post_data['post_id'] !=''){
			$frm->getField('comment_post_id')->value = $post_data['post_id'];
			$postid = $post_data['post_id'];
			if(empty($_SESSION['postid'])){
				$_SESSION['postid'] = $postid;
				$flag = 1;
			}else{
				$finalarray = explode(',',$_SESSION['postid']);
				if (in_array($postid, $finalarray)) {
					$flag = 0;
				}else{
					$_SESSION['postid'] .= ','.$postid;
					$flag = 1;
				}
			}
			if($flag == 1)
			{
				$this->Blog->setPostViewsCount($post_data['post_id']);
			}
			//echo CONF_THEME_PATH . 'blog/page-css/slider.css';
			Syspage::addJs(array('js/owl.carousel.min.js'), true);
			Syspage::addCss(array(CONF_THEME_PATH . 'blog/page-css/slider.css',CONF_THEME_PATH . 'blog/page-css/blog-slider.css'));
			/* get post categories[ */
			$post_data['post_categories'] = array();
			$post_data['post_categories'] = $this->Blog->getPostCategories(array('post_id' => $post_data['post_id']));
			/* ] */
			$this->set('post_data', $post_data);
			$post_slider_images = $this->Blog->getPostImages($post_slug);
			if(!empty($post_slider_images)){
				$this->set('slider_images', $post_slider_images);
			}	
		}
		
		$this->set('loggedUserId', User::getLoggedUserAttribute('user_id'));
		$this->set('frmComment', $frm);
		$this->_template->render();
	}
	
	function listCommentsOfPost()
	{
		$post = Syspage::getPostedVar();
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			if($post['post_id'] < 1){
				die(0);
			}
		
			$post = Syspage::getPostedVar();
			$page = 1;
			if(isset($post['page']) && intval($post['page']) > 0) $page = intval($post['page']); 
			else $post['page'] = $page;
			
			$pagesize = 10;
			#$pagesize = CONF_PAGINATION_LIMIT;
			$post['pagesize'] = $pagesize;
			$post['post_id'] = $post['post_id'];
			$this->set('records', $this->Blog->getPostComments($post));
			$this->set('pages', $this->Blog->getTotalPages());
			$this->set('page', $page);
			$this->set('start_record', ($page-1)*$pagesize + 1);
			$end_record = $page * $pagesize;
			$total_records = $this->Blog->getTotalRecords();
			if ($total_records < $end_record) $end_record = $total_records;
			$this->set('end_record', $end_record);
			$this->set('total_records', $total_records);
			$this->_template->render(false,false);
		}
		die(0);
	}	
	
}