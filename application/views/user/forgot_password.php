<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row whiteBg paddng strip">
		<div class="fix-container">
		<?php echo Message::getHtml(); ?>
			<div class="login-pages">			 
				<div class="user-img">
					<?php
						$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
					?>
					<img src="<?php echo $logoUrl;?>">
				</div>			  
				<div class="gray-background">	
					<?php 
						$post = Syspage::getPostedVar();
						unset($post['security_code']);
						echo $frm->fill($post);
						echo $frm->getFormtag(); 
					?>
						<table>
							<tr><td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Forgot_Your_Password' ); ?></h2></td></tr>
							<tr><td colspan="2"><i class="icon email-icon"></i><?php echo $frm->getFieldHTML('user_email'); ?></td></tr>
							<tr><td colspan="2" class="secure"><?php echo $frm->getFieldHTML('reload'); echo $frm->getFieldHTML('security_code'); echo $frm->getFieldHTML('code'); ?></td></tr>						 
							<!--<tr><td colspan="2"><p>&nbsp;</p> <p class="txtwhite"><a href="<?php //echo generateUrl('user','signin');?>">Back to Login</a></p></td></tr>-->
							<tr class="gradient-greenbg">
								<td><?php echo $frm->getFieldHTML('btn_submit');?></td>
								<td align="center"><a href="<?php echo generateUrl('user','signin');?>" class="writer-Signup"><?php echo Utilities::getLabel( 'L_Back_to_Login' ); ?></a></td>
							</tr>					 
						</table>
					</form><?php echo $frm->getExternalJS(); ?>					
				</div>			 
			</div>
        	
        </div>
    
   </div> 
    
  </div>
