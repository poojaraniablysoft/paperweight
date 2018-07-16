<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 
?>
<!-- <script>$.facebox('hello');</script> -->

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>User Management</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>User Management</h2></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Users</h3> </div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
		<?php
			global $post;
			$frmUserSearch->fill(array('keyword'=>$post['keyword'],'user_type'=>$post['user_type'],'user_active'=>$post['user_active'],'user_email_verified'=>$post['user_email_verified']));
			$frmUserSearch->setFieldsPerRow(5);
			$frmUserSearch->captionInSameCell(true);
			echo $frmUserSearch->getFormHtml();
		?>
		</div>
	</section>
	
	<section class="box">
		<div class="box_head"><h3>User List</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php	
				

				$arr_flds = array(
				'listserial'=>'S.No.',
				'user_name'=>'Name',
				'user_email'=>'Email',
				'user_type'=>'User Type',
				'arr_rating'=>'User Ratings',
				'user_is_featured'=>'Is User Featured',
				'user_active'=>'User Status',
				'user_email_verified'=>'Is Email Verified',
				'user_verified_by_admin'=>'Verified by Admin',
				'action' => 'Action'
				);

				//if ($write_permission) $arr_flds['action'] = 'Action';

				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {
					
					
					$e = $th->appendElement('th', array(), $val);
					
					
				}

				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						
						switch ($key){
							case 'listserial':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'user_name':
								$td = $tr->appendElement('td',array('width'=>'20%'));
								if($row['user_first_name'] != '' || $row['user_last_name'] != '') {
									$td->appendElement('plaintext', array(), $row['user_first_name'] . ' ' . $row['user_last_name'], true);
								}else {
									$td->appendElement('plaintext', array(), $row['user_screen_name'], true);							
								}
								break;
							case 'user_email':
								$td = $tr->appendElement('td',array('width'=>'20%'));
								$td->appendElement('plaintext',array(), $row['user_email'], true);
								break;
							case 'user_type':
								$td = $tr->appendElement('td');
								if ($row['user_type'] == 0) {
									
									$td->appendElement('p', array('id'=>'ml_'.$row['user_id']), 'Customer' . ' ', true);
									
								}
								
								if ($row['user_type'] == 1) {
									
									$td->appendElement('p', array('id'=>'ml_'.$row['user_id']),'Writer'. ' ', true);
									
								}
							
								break;
							case 'arr_rating':
								$td = $tr->appendElement('td');
								if($row['arr_rating']>0) {
									$td->appendElement('plaintext', array(), "<div class='ratingsWrap'><span class='p".($row['arr_rating']*2)."'></span></div>", true);
								}else {
									$td->appendElement('plaintext', array(), "No Rating!", true);
								}
								break;
							case 'user_active':
								$td = $tr->appendElement('td');
									$ul=$td->appendElement('ul',array('class'=>'iconbtns'));
									$li=$ul->appendElement('li');
									
									$img = $row['user_active'] == 1 ? '<img src="' . CONF_WEBROOT_URL . 'images/actives.png" alt="" class="whiteicon active_icon">' : '<img src="' . CONF_WEBROOT_URL . 'images/inactives.png" alt="" class="whiteicon inactive_icon">';
									
									$a = $li->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $row['user_id'], 'onclick'=>'updateUserStatus(' . $row['user_id'] . ', $(this))', 'title'=>($row['user_active'] == 1)?'Active':'Inactive'),$img, true);
								
								break;
								case 'user_is_featured':
									$td = $tr->appendElement('td');									
									$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $row['user_id'], 'onclick'=>'markFeatured(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle featured status','class'=>($row['user_is_featured']==1? 'toggleswitch':'toggleswitch actives')));
								
								break;
							case 'user_email_verified':
								$td = $tr->appendElement('td');
								if ($row['user_type']==1) {
									$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'verified_'. $row['user_id'], 'onclick'=>'updateEmailVerificationStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle email verification status'), (($row[$key]==1)?'<a class="toggleswitch"></a>':'<a class="toggleswitch actives"></a>'), true);
									}
									else {
										$td->appendElement('a', array(), '--', true);
									}
								break;
							case 'user_verified_by_admin':
								$td = $tr->appendElement('td');
								if(User::isWriter($row['user_id'])) {
									if($row['user_is_approved']!=1){
										$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'verified_'. $row['user_id'], 'onclick'=>'updateUserVerificationStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle User verification status','class'=>'toggleswitch actives'),'', true);			
									}else {
										$td->appendElement('a',array('class'=>'toggleswitch'),'',true);
									}
								}else {
									$td->appendElement('a',array('class'=>'toggleswitch'),'',true);
								}
															
								break;
							case 'action':
								$td = $tr->appendElement('td');
															
								if ($row['user_type'] == 1) $td->appendElement('a', array('href'=>generateUrl('user','preview_writer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
								
								if ($row['user_type'] == 0) $td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
								
								
							
								break;
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}

				if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				

			?>
			
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
			
		</div>
	</section>
</section>