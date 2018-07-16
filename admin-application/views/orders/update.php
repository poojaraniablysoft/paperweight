<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;
?>
<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('orders');?>" class="selected">Orders Management</a></li>
		<li><a href="<?php echo generateUrl('rating');?>">Ratings & Reviews Management</a></li>		
		<!--<li><a href="javascript:void(0);" >Manage Order</a></li>		-->
	</ul>
</section>
<!--leftPanel end here--> 

<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Edit Order</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Update Order</h2></div>
	<!--leftPanel end here--> 

	
	
	<section class="box">
		<div class="box_content clearfix toggle_container">
			<?php 
			
			//	echo $frmcontent->getFormHtml();
			echo $frmcontent->getFormTag();
			echo $frmcontent->getFieldHtml('task_id');
			echo $frmcontent->getFieldHtml('invitee_ids');
			?>
			<div class="setting-form-wrapper">
				<h2>Basic Information</h2>
				<div class="settings_form">
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable blockrepeat">
					<tbody>
					 <tr>
					  <td>Type of paper<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_paptype_id');?></td>
					</tr>
					<tr>
					  <td>Topic<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_topic');?></td>
					</tr>
					<tr>
					  <td>Pages<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_pages');?></td>
					</tr>
					<tr>
					  <td>Deadline<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_due_date');?></td>
					</tr>
					</tbody>
					</table>
				</div>
				<h2>Additional Information</h2>  
					
				<div class="settings_form">
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable blockrepeat">
					<tbody>
				   
					<tr>
					  <td>Discipline<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_discipline_id');?></td>
					</tr>
					<tr>
					  <td>Type of service<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_service_type');?></td>
					</tr>
					<tr>
					  <td>Format or citation style<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_citstyle_id');?></td>
					</tr>
					<tr>
					  <td>Order Status<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_status');?></td>
					</tr>
					<tr>
					  <td>Paper instructions<span class="spn_must_field">*</span></td>
					  <td><?php echo $frmcontent->getFieldHtml('task_instructions');?></td>
					</tr>
					<tr>
					  <td>Upload additional material</td>
					  <td><?php echo $frmcontent->getFieldHtml('task_files[]');?></td>
					</tr>					
					</tbody>
					</table>
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable blockrepeat">
					<tr>
					  <td></td>
					  <td><?php echo $frmcontent->getFieldHtml('btn_submit');?></td>
					</tr>
				</table>
				
			</div>
			</form><?php echo $frmcontent->getExternalJs();?>
			
			
		</div>
		<?php if(count($files)>0): ?>
		<div class="box_content clearfix">
			<table class="formTable blockrepeat" width="100%" border="0" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td width="30%" align="right">Files</td>
						<td>
							<?php
							foreach($files as $file)
							{
							?>
								<div class="attachments">
								<a title="<?php echo $file['file_download_name'];?>" href="<?php echo generateUrl('orders', 'download_file', array($file['file_id'])); ?>" class="attachlink attachicon"><?php if(strlen($file['file_download_name']) < 20) {echo $file['file_download_name'];}else { echo 
									substr($file['file_download_name'],0,10).'....'.substr($file['file_download_name'],-6);}?></a>
								</div>
							<?php
							}?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php endif;?>
	
</section>