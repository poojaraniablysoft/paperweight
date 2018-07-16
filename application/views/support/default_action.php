<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>  

<div class="white-box">
	<?php echo Message::getHtml(); ?>
	<div class="gray-box">
		<div class="Support">
		<?php 
			echo $frm->getFormTag();
			echo $frm->getfieldHtml('user_id');
			//echo $frm->getfieldHtml('user_screen_name');
			echo $frm->getfieldHtml('user_email');
		?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                <td><table cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tbody>
                      <tr>
                        <td><?php echo Utilities::getLabel( 'L_User_Name' ); ?><span class="mandatory">*</span><br/>
                          <?php echo $frm->getfieldHtml('user_screen_name');?></td>
                      </tr>
                      <tr>
                        <td><?php echo Utilities::getLabel( 'L_Subject' ); ?><span class="mandatory">*</span><br/>
                          <?php echo $frm->getfieldHtml('subject');?></td>
                      </tr>
                      <tr>
                        <td><?php echo Utilities::getLabel( 'L_Phone' ); ?>:<span class="mandatory">*</span><br/>
                         <?php echo $frm->getfieldHtml('phone');?></td>
                      </tr>
                      
                      
                    </tbody>
                  </table></td>
                <td><?php echo Utilities::getLabel( 'L_Message' ); ?><br/>
                         <?php echo $frm->getfieldHtml('message');?></td>
              </tr>
              <tr>
            
                <td colspan="2"><?php echo $frm->getfieldHtml('btn_submit');?></td>
              </tr>
            </table>
		</form><?php echo $frm->getExternalJS(); ?>
		</div>
	</div>
</div>