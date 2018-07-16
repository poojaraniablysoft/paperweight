<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">	<ul class="breadcrumb">		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>		<li><a href="<?php echo generateUrl('blogcomments');?>">Blog Comments</a></li>		<li>View Comment</li>	</ul>
	<section class="box">
		<div class="title"><h2>View</h2></div>
		<div class="box filterbox">
			<div class="borderContainer">
			<?php echo Message::getHtml();?>
				<div class="sectionProfile">
					<section class="box innerbox">
						<div class="box_content clearfix toggle_container">
							<table cellspacing="0" cellpadding="0" width="100%" border="0" class="tabledetails">
								<tbody>
									<tr>
										<td><strong>Name:</strong> <?php echo ucfirst($comment_data['comment_author_name']);?></td>
										<td><strong>Email:</strong> <?php echo $comment_data['comment_author_email']; ?></td>
										<td><strong>IP Address:</strong> <?php echo $comment_data['comment_ip'];?>
										</td>
										<td rowspan="2" width="25%"><h2>Edit Comment Status</h2><?php 
											echo $frmComment->getFormHtml(); ?>
										</td>
									</tr>
									
									<tr>
										<td><strong>Status:</strong> <?php
										if($comment_data['comment_status'] == 1){
											echo 'Approved';
											}elseif($comment_data['comment_status'] == 2){
												echo 'Deleted';
											}	
											else{
												echo 'Pending';
											}
										?> </td>
										
										<td><strong>Date:</strong> <?php echo displayDate($comment_data['comment_date_time']); ?></td>
										
										<td width="28%"><strong>User Agent:</strong> <?php echo $comment_data['comment_user_agent']; ?></td>
									</tr>
									<tr>	
										<td><strong>Comment:</strong></td>
										<td width="30%" style="text-align:justify"><?php echo $comment_data['comment_content'];?></td>
									</tr>	
								</tbody>
							</table>
						</div>
					</section>
					<div class="gap"></div>
				</div>
			</div>
		</div>
	</section>
</section>