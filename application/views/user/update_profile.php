<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($frm,true)."</pre>";

if (User::isUserLogged()) {
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>
<div class="my-profile">

	<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>	
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ). '</a>';}?>
			</p>
		</div>
	<?php  }?>
	
	<div class="white-box clearfix">
		<?php echo Message::getHtml(); ?>
		<div class="gray-box clearfix">
			<div class="sectionphoto">
				<figure class="photo">
					<img src="<?php echo generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),200,200));?>"  alt="img">
					<?php if(User::hasProfilePhoto()) { ?><a class="remove_profile"><?php echo Utilities::getLabel( 'L_Remove_Image' ); ?></a><?php }?>
				</figure>
				<a class="theme-btn fadeandscale_open" onclick="ChangeImage('<?php echo User::getLoggedUserAttribute('user_id');?>')" ><i class="icon ion-edit"></i><?php echo Utilities::getLabel( 'L_Change_Image' ); ?></a>
			</div>

			<div class="sectioninfo">
				<div class="grid_2">
					<h6><?php echo Utilities::getLabel( 'L_You_will_be_seen_as' ); ?></h6>
					<div class="userinfo clearfix">
						<div class="boxleft"><div class="photo"><img src="<?php echo generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),200,200));?>"  alt="img"></div></div>
						<div class="boxright">
							<?php $user_screen_name = User::getLoggedUserAttribute('user_screen_name');?>
							<span class="nameuser"><?php if(strlen($user_screen_name) > 22){ echo substr($user_screen_name,0,22)."...";}else {echo $user_screen_name;} ?></span>
							<?php if ($user_rating > 0) { ?>
								<div class="ratingsWrap ratingSection"><span class="p<?php echo $user_rating*2; ?>"></span></div>
								<span class="nrmltxt"><?php echo $user_rating; ?></span>
							<?php } else { ?>
								<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
							<?php } ?>
						</div>
						<div class="txt-box clearfix">
							<div class="grydtn-btn"><span><?php echo $orders_in_progress; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_In_Progress' ); ?></p></div>

							<div class="grydtn-btn"><span><?php echo $orders_completed; ?></span><p><?php echo Utilities::getLabel( 'L_Orders_Completed' ); ?></p></div>
						</div>
					</div>
					<?php if(!User::isUserReqDeactive()) { ?><a href="javascript:void(0);" onclick="if(confirm('Are you sure you want to deactivate your account?')) {document.location.href='<?php echo generateUrl('user', 'deactivate_account'); ?>';}" class="theme-btn"><?php echo Utilities::getLabel( 'L_Deactivate_Account' ); ?></a><?php }else { ?>
						<a class="theme-btn"><?php echo Utilities::getLabel( 'L_Account_is_deactivated' ); ?>!</a>
					<?php }?>
				</div>

				<div class="grid_1">
					<?php echo $frm->getFormHtml(); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="white-box clearfix padding_30">
		<h2><?php echo Utilities::getLabel( 'L_Change_Password' ); ?></h2>
		<div class="gray-box">
			<?php echo $frmChngPass->getFormHtml(); ?>
		</div>
	</div>
</div>
<?php }
else {
include CONF_THEME_PATH . 'navigation_header.php';
	if (isset($_SESSION['reg_user']) && $_SESSION['reg_user']['is_email_verified'] === 1) { ?>
		<div id="body">
			<div class="row whiteBg paddng strip">
				<div class="fix-container">
					<?php echo Message::getHtml(); ?>
					<div class="register">
						<h1><?php echo Utilities::getLabel( 'L_Register_as_a_freelancer_writer' ); ?>: <span><?php echo Utilities::getLabel( 'L_Step_2_of_4' ); ?></span></h1>
					   
						<div id="rootwizard" class="register-step">
							<ul>
							  <li class="active fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<?php echo Utilities::getLabel( 'L_Alert_Proceed_Profile_After_Email_Verified' ); ?>');"data-toggle="tab">
								<figure class="figure">1</figure>
								<span><?php echo Utilities::getLabel( 'L_Submit_Email' ); ?></span> </a></li>
							  <li class="active"> <a href="#tab2" data-toggle="tab">
								<figure class="figure">2</figure>
								<span><?php echo Utilities::getLabel( 'L_Fill_in_Profile' ); ?></span></a></li>
							  <li> <a href="#tab3" data-toggle="tab">
								<figure class="figure">3</figure>
								<span><?php echo Utilities::getLabel( 'L_Pass_Grammar_Test' ); ?></span></a></li>
							  <li> <a href="#tab4" data-toggle="tab">
								<figure class="figure">4</figure>
								<span><?php echo Utilities::getLabel( 'L_Write_Sample_Essay' ); ?></span></a></li>
							</ul>
							<div id="bar" class="progress progress-striped active">
							  <div style="width: 33.33%;" class="bar"></div>
							</div>
						</div>
					</div>
					
					<?php echo $frm->getFormtag();?>
						<div class="register-form">
							<div class="gray-background cols_1 full-width">
								<table>
								  <tr>
									<td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Personal_Information' ); ?></h2>
									<h3><?php echo Utilities::getLabel( 'L_Personal_Information_Description' ); ?></h3>
									</td>
								  </tr>
								  <tr>
									<td align="right"><?php echo Utilities::getLabel( 'L_Email_&_Login' ); ?></td>
									<td><strong><?php echo $frm->getFieldHTML('user_email'); ?></strong></td>
								  </tr>
								  <tr>									
									<td  align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Username' ); ?></td>
									<td><?php echo $frm->getFieldHTML('user_screen_name'); ?></td>
								  </tr>
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Password' ); ?></td>  <td> <?php echo $frm->getFieldHTML('user_password'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Confirm_Password' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_password_1'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_First_Name' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_first_name'); ?></td>
								  </tr>
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Last_Name' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_last_name'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Gender' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_sex'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Cell_Phone' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_mobile'); ?></td>
								  </tr>
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Country' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_country_id'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_City' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_city'); ?></td>
								  </tr>
								  <tr> 
									<td align="right"><?php echo Utilities::getLabel( 'L_Zip' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_zip'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"><?php echo Utilities::getLabel( 'L_Ques_Work_For_Any_Other_Online_Academic' ); ?></td>  
									<td> <?php echo $frm->getFieldHTML('user_is_experienced'); ?></td>
								  </tr>
								  <tr class="hide">
									<td align="right"><span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Company_Name' ); ?></td>
									<td><span id="companyname"></span></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Citation_Styles_Familiar_With' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('citations'); ?> <span id="error_citation"></span></td>
								  </tr>							   
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Ques_Your_Native_Language' ); ?></td>  
									<td><?php echo $frm->getFieldHTML( 'user_lang' ); ?><span id="error_lang"></span></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Ques_Highest_Verifiable_Academic_Degree' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('user_deg_id'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Additional_Profile_Information' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('user_more_cv_data'); ?></td>
								  </tr>								
								  <tr>
									<td colspan="2"><h2 class="top-margin15"><?php echo Utilities::getLabel( 'L_Education' ); ?></h2>    
									</td>
								  </tr>					  
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Name_of_university' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('education[usered_university]'); ?></td>
								  </tr>
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Degree' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('education[usered_degree]'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right"> <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Graduation_year' ); ?></td>  
									<td><?php echo $frm->getFieldHTML('education[usered_year]'); ?></td>
								  </tr>								
								  <tr> 
									<td align="right" valign="top" > <span class="mandatory">*</span><?php echo Utilities::getLabel( 'L_Certificate' ); ?></td>  
									<td> 
										<?php echo $frm->getFieldHTML('edu_diploma'); ?><br/>
										<small><?php echo Utilities::getLabel( 'L_Certificate_Upload_File_Extensions_Restriction' ); ?></small><br/>
										<small><?php 
											$limit = ini_get('upload_max_filesize');
											echo sprintf( Utilities::getLabel( 'L_Upload_File_Size_Limit' ), $limit );
										?></small> 
									</td>
								  </tr>
								  <tr>
									<td></td>
									<td><p><?php echo Utilities::getLabel( 'L_Highest_Degree_Certificate_Copy_Upload_Note' ); ?></p></td>
								  </tr>
								  <tr> 
									<td></td>
									<td ><?php echo $frm->getFieldHTML('btn_submit'); ?></td>
								  </tr>
								</table>
							</div>					  
						</div>
					</form><?php echo $frm->getExternalJS(); ?>				
				</div>
			</div>
		</div>
	<?php }
	else {
		redirectUser(generateUrl('user', 'signin'));
	}
}
?>