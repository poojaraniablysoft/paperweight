<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row whiteBg paddng strip">
		<div class="fix-container">
			<div class="login-pages">			 
				<div class="user-img">
				<?php
					$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
				?>
				<img src="<?php echo $logoUrl;?>">
				</div>			  
				<div class="gray-background">	
					<?php echo $frm->getFormtag(); ?>
						<table>
							<tr><td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Log_in_to_your_Account' ); ?></h2></td></tr>
							<tr><td colspan="2"><i class="icon email-icon"></i><?php echo $frm->getFieldHTML('user_email'); ?></td></tr>
							<tr><td colspan="2"><i class="password-icon"></i><?php echo $frm->getFieldHTML('user_password'); ?></td></tr>						 
							<tr><td colspan="2"><p><?php echo $frm->getFieldHTML('remember_me'); ?><?php echo Utilities::getLabel( 'L_Remember_me' ); ?></p> <?php echo $frm->getFieldHTML('forgot_password'); ?></td></tr>
							<tr class="gradient-greenbg">
								<td><?php echo $frm->getFieldHTML('btn_login');?></td>
								<td align="center"><a href="<?php echo generateUrl('user','writer_signup');?>" class="writer-Signup"><?php echo Utilities::getLabel( 'L_Writer_Sign_Up' ); ?></a></td>
							</tr>					 
						</table>
					</form><?php echo $frm->getExternalJS(); ?>					
				</div>			 
			</div>
		</div>
	</div>
</div>