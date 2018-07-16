<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
		
<div class="login-pages">			 
	<div class="gray-background">	
		<?php echo Message::getHtml(); ?>
		<?php echo $frm->getFormtag(); echo $frm->getFieldHTML('task_id');echo $frm->getFieldHTML('invite_user_id');?>
			<table>
				<tr><td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Login_to_your_Account' ); ?></h2></td></tr>
				<tr><td colspan="2"><i class="icon email-icon"></i><?php echo $frm->getFieldHTML('user_email'); ?></td></tr>
				<tr><td colspan="2"><i class="password-icon"></i><?php echo $frm->getFieldHTML('user_password'); ?></td></tr>						 
				<tr><td colspan="2"><p><?php echo $frm->getFieldHTML('remember_me'); ?><?php echo Utilities::getLabel( 'L_Remember_me' ); ?></p> <?php echo $frm->getFieldHTML('forgot_password'); ?></td></tr>
				<tr class="gradient-greenbg">
					<td><?php echo $frm->getFieldHTML('btn_login');?></td>
					<td align="center"><a href="<?php echo generateUrl( 'user', 'writer_signup' );?>" class="writer-Signup"><?php echo Utilities::getLabel( 'L_Writer_Sign_Up' ); ?></a></td>
				</tr>					 
			</table>
		</form><?php echo $frm->getExternalJS( ); ?>					
	</div>			 
</div>