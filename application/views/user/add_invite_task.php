<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>


<div id="body" >
    <div class="contentArea">
		<?php echo Message::getHtml(); ?>
        <div class="fullWrapper">
			<div class="leftPanel upload-profile">
            	<h2 class="pagetitle"><?php echo Utilities::getLabel( 'L_Register_as_a_customer' ); ?></h2>
                <div class="formWrap">
					<?php echo $frm->getFormTag(); echo $frm->getFieldHTML('invitee_ids'); ?>
						<table cellspacing="0" cellpadding="0" border="0">   
						  <tbody>
							<tr><td colspan="2"><?php echo $frm->getFieldHTML('user_email'); ?></td></tr>
							<tr><td width="50"><?php echo $frm->getFieldHTML('task_due_date'); ?></td><td width="50"><?php echo $frm->getFieldHtml('task_paptype_id'); ?></td></tr>		  
							<tr><td colspan="2"><?php echo $frm->getFieldHTML('task_pages'); ?></td></tr>
							<tr><td colspan="2"><?php echo $frm->getFieldHTML('btn_submit'); ?></td></tr>            
						  </tbody>  
						</table>
					</form><?php echo $frm->getExternalJS(); ?>
				</div>
			</div>
		</div>
	</div>
</div>



	