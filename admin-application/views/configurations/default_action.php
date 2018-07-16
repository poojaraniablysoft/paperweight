<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Settings</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Settings</h2></div>
	<section class="box">
		
		<div class="box_content clearfix toggle_container">
			<?php 
				//echo $frmConf->getFormHtml();
				echo $frmConf->getFormTag();
			?>
			<div class="setting-form-wrapper">
				<h2>General Settings</h2>
				<div class="settings_form">
					<table width="100%" class="formTable">
						<tbody>
						<tr>						
						<td  >Website Name<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_website_name');?></td>
						</tr><tr>
						<td  >Website URL<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_site_url');?></td>
						</tr>
						<tr>
						<td>Admin Logo<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_admin_logo');?></td>
						</tr>
						<tr>
						<td>Website Logo<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_website_logo');?></td>
						</tr>	
						<tr>
						<td>Footer Logo<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_website_footer_logo');?></td>
						</tr>	
						<tr>
						<td>Website Favicon<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_website_fav_icon');?></td>
						</tr>		
						<tr>
						<td>Email Template Logo<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_email_template_logo');?></td>
						</tr>
						<tr>
						<td>Login Logo<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_login_logo');?></td>
						</tr>						
						<tr>
						<td  >Send Emails From Name</td>
						<td><?php echo $frmConf->getFieldHtml('conf_emails_from_name');?></td>
						</tr><tr>
						<td  >Contact Number</td>
						<td><?php echo $frmConf->getFieldHtml('conf_contact_phone');?></td>
						</tr><tr>
						<td  >Contact Address</td>
						<td><?php echo $frmConf->getFieldHtml('conf_contact_address');?></td>
						</tr><tr>
						<td  >Contact Location iFrame</td>
						<td><?php echo $frmConf->getFieldHtml('conf_contact_location');?></td>
						</tr><tr>
						<td  >Currency</td>
						<td><?php echo $frmConf->getFieldHtml('conf_currency');?></td>
						</tr><tr>
						<td  >Number of words per page:<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_words_per_page');?></td>
						</tr><tr>
						<td  >Sample Essay Topic<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_sample_essay_topic');?></td>
						</tr><tr>
						<td  >Video URL:<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_video_url');?></td>
						</tr><tr>
						<td  >Pagination Limit:<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('conf_pagination_limit');?></td>
						</tr>
						<tr>
						<td  >Use SSL:<span class="spn_must_field">*</span></td>
						<td><?php echo $frmConf->getFieldHtml('CONF_USE_SSL');?></td>
						</tr>
						</tbody>
					</table>
				</div>

				<h2>Email Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Administrator Email ID<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_admin_email_id');?></td>
				</tr><tr>
				<td  >Send Emails From<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_emails_from');?></td>
				</tr><tr>
				<td  >Contact Email Address<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_contact_email_to');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_email_send_via')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_email_send_via');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_smtp_mail_host')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_smtp_mail_host');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_smtp_mail_username')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_smtp_mail_username');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_smtp_mail_password')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_smtp_mail_password');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_smtp_mail_port')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_smtp_mail_port');?></td>
				</tr>
				<tr><td><?php echo $frmConf->getField('conf_smtp_mail_secure')->field_caption;?><span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_smtp_mail_secure');?></td>
				</tr>
				</tbody>
				</table>
				</div>			

				<h2>Paypal Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Paypal Merchant Id<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_paypal_merchant_id');?></td>
				</tr><tr>
				<td  >Paypal Currency<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_paypal_currency');?></td>
				</tr>
				<tr>
				<td>Paypal Mode</td>
				<td><?php echo $frmConf->getFieldHtml('conf_paypal_transaction_mode');?></td>
				</tr>				
				</tbody>
				</table>
				</div>

				<h2>Date Formats Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Date Format</td>
				<td><?php echo $frmConf->getFieldHtml('date_format');?></td>
				</tr><tr>
				<td  >Timezone</td>
				<td><?php echo $frmConf->getFieldHtml('conf_timezone');?></td>
				</tr>
				</tbody>
				</table>
				</div>

				<h2>Social Links Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Facebook URL<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_facebook');?></td>
				</tr><tr>
				<td  >Twitter URL<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_twitter');?></td>
				</tr><tr>
				<td  >Google+ URL<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_googleplus');?></td>
				</tr><tr>
				<td  >Linked In URL<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_linkedin');?></td>
				</tr>
				</tbody>
				</table>
				</div>

				<h2>Grammar Test Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Quiz Time (in Minutes):<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_quiz_time');?></td>
				</tr><tr>
				<td  >Number of question to display:<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_ques_display');?></td>
				</tr><tr>
				<td  >Required number of Correct Answer:<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_correct_ans_required');?></td>
				</tr></tbody>
				</table>
				</div>

				<h2>Wallet Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td>Wallet Limit<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_wallet_limit');?></td>
				</tr>
				<tr>
				<td>Minimum Withdraw Funds Limit<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_withdraw_funds_limit');?></td>
				</tr>
				</tbody>
				</table>
				</div>

				<h2>Commission Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Admin's Commision Percentage:<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_service_comission');?></td>
				</tr><tr>
				<td  >Service Charge for Customer (Fixed price):<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_service_charge');?></td>
				</tr></tbody></table>
				</div>
				
				<h2>Footer Settings</h2>
				<div class="settings_form">
				<table width="100%" class="formTable">
				<tbody>
				<tr>
				<td  >Copyright:<span class="spn_must_field">*</span></td>
				<td><?php echo $frmConf->getFieldHtml('conf_footer_copyright');?></td>
				</tr>
				</tbody>
				</table>
				</div>
				
				<table width="100%" class="formTable">
				<tr>
				<td  ></td>
				<td><?php echo $frmConf->getFieldHtml('btn_submit');?></td>
				</tr>
				</table>
				</div>
			</form><?php echo $frmConf->getExternalJs();?>
		</div>
	</section>
</section>
