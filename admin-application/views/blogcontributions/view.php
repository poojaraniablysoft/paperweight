<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<div class="box_head"><h3>Contribution Detail</h3></div>
    <div class="box filterbox">
       	<div class="borderContainer">
			<div class="sectionProfile">
				<section class="box innerbox">
					<div class="box_content clearfix toggle_container">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" class="tabledetails">
							<tbody>
								<tr>
									<td><strong>First Name:</strong> <?php echo ucfirst($contribution_data['contribution_author_first_name']);?></td>
									<td><strong>Last Name:</strong> <?php echo ucfirst($contribution_data['contribution_author_last_name']); ?></td>
									<td><strong>Email:</strong> <?php echo $contribution_data['contribution_author_email']; ?></td>
									
									<td rowspan="2" width="25%"><h2>Edit Contribution Status</h2><?php 
										echo $frmContributionStatusUpdate->getFormHtml(); ?>
									</td>
									
								</tr>
								
								<tr>
									<td><strong>Phone:</strong> <?php echo $contribution_data['contribution_author_phone']; ?></td>
									<td><strong>Added Date:</strong> <?php echo displayDate($contribution_data['contribution_date_time']); ?></td>
									<td><strong>Status:</strong> <?php
									if($contribution_data['contribution_status'] == 1){
										echo 'Approved';
									}elseif($contribution_data['contribution_status'] == 2){
										echo 'Posted/Published';
									}elseif($contribution_data['contribution_status'] == 3){
										echo 'Rejected';
									}else{
										echo 'Pending';
									}
									?>
									</td>
								</tr>
									<?php
									if(!empty($contribution_data['contribution_file_name'])){?>
										<tr>	
											<td><strong>File:</strong> <a class="a_class" href="<?php echo generateUrl('blogcontributions', 'download', array($contribution_data['contribution_file_name'], $contribution_data['contribution_file_display_name'])); ?>"><?php echo $contribution_data['contribution_file_display_name'];?> <input type="button" class="btn_cursor" name="download" id="download" value="Download"/></a></td>
									<?php
									echo '</tr>';
									}
									?>
							
						 
							</tbody>
						</table>
					</div>
				</section>
            </div>
		</div>
	</div>
</section>
