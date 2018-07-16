<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row whiteBg strip">
		<div class="fix-container">
			<div class="blog-contribution">
				<div class="comment-form">
					<h3><?php echo Utilities::getLabel( 'L_Blog_Contribution' ); ?></h3>
					<?php $frmContribute->getFormHtml(); ?><?php echo $frmContribute->getFormTag(); ?>
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td><?php echo Utilities::getLabel( 'L_First_Name' ); ?><span class="mandatory">*</span></td>
								<td><?php echo $frmContribute->getFieldHtml('contribution_author_first_name'); ?></td>
							</tr>

							<tr>
								<td><?php echo Utilities::getLabel( 'L_Last_Name' ); ?><span class="mandatory">*</span></td>
								<td><?php echo $frmContribute->getFieldHtml('contribution_author_last_name'); ?></td>
							</tr>
							<tr>
								<td><?php echo Utilities::getLabel( 'L_Email' ); ?><span class="mandatory">*</span></td>
								<td><?php echo $frmContribute->getFieldHtml('contribution_author_email'); ?></td>
							</tr>

							<tr>
								<td><?php echo Utilities::getLabel( 'L_Phone_Number' ); ?></td>
								<td><?php echo $frmContribute->getFieldHtml('contribution_author_phone'); ?></td>
							</tr>

							<tr>
								<td><?php echo Utilities::getLabel( 'L_Upload_File' ); ?><span class="mandatory">*</span></td>
								<td><?php echo $frmContribute->getFieldHtml('contribution_file_name'); ?></td>
							</tr>
							<tr>
								<td><?php echo Utilities::getLabel( 'L_Verification' ); ?><span class="mandatory">*</span></td>
								<td>
									<table class="capcha">
										<tr>
											<td><?php echo $frmContribute->getFieldHtml('security_code'); ?></td>
											<td style="position:relative;"><?php echo $frmContribute->getFieldHtml('captcha'); ?> </td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2"> <?php echo $frmContribute->getFieldHtml('contribution_user_id'); ?>
						<?php echo $frmContribute->getFieldHtml('btn_submit'); ?> </td>
							</tr>
						</table>
					</form><?php echo $frmContribute->getExternalJs(); ?>
				</div>
			</div>
		</div>

	</div>
</div>
