<?php
 defined('SYSTEM_INIT') or die('Invalid Usage');
 include CONF_THEME_PATH . 'navigation_header.php';
// include CONF_THEME_PATH . 'page_bar.php';
?>  

<div id="body">
	<div class="row whiteBg paddng strip">
	  <div class="fix-container">
		  <!---register html start here---> 
		<div class="register">
		  <h1><?php echo Utilities::getLabel( 'L_Freelance_Writer_Steps' ); ?></h1>
	   
		  <div id="rootwizard" class="register-step">
			<ul>
			  <li class="fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Email_Verified_Popup_For_Essay_File' ); ?></div>');" data-toggle="tab">
				<figure class="figure">1</figure>
				<span><?php echo Utilities::getLabel( 'L_Submit_Email' ); ?></span> </a></li>
			  <li class="fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Profile_Done_Popup_For_Essay_File' ); ?></div>');" data-toggle="tab">
				<figure class="figure">2</figure>
				<span><?php echo Utilities::getLabel( 'L_Fill_in_Profile' ); ?></span></a></li>
			  <li class="fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Cleared_Grammar_Test_Popup_For_Essay_File' ); ?></div>');" data-toggle="tab">
				<figure class="figure">3</figure>
				<span><?php echo Utilities::getLabel( 'L_Pass_Grammar_Test' ); ?></span></a></li>
			  <li class="active"> <a href="<?php echo generateUrl('sample');?>" data-toggle="tab">
				<figure class="figure">4</figure>
				<span><?php echo Utilities::getLabel( 'L_Write_Sample_Essay' ); ?></span></a></li>
			</ul>
			<div id="bar" class="progress progress-striped active">
			  <div style="width: 100%;" class="bar"></div>
			</div>
		  </div>
		  

		  <div class="default-action">
		  <h2><?php echo Utilities::getLabel( 'L_Start_Writing_Sample_Essay' ); ?></h2>
		  <?php echo Message::getHtml(); ?>
		  <?php echo $frm->getFormTag();?>
			<table>
				<tr>					
					<td colspan="2"><p><span class="essay-topice"><?php echo Utilities::getLabel( 'L_Essay_Topic' ); ?></span><span class="topic-txt"> <?php echo CONF_SAMPLE_ESSAY_TOPIC;?></span></p></td>
				</tr>
				<tr>
				  <td align="right" valign="top"><?php echo Utilities::getLabel( 'L_Upload_File ' ); ?><span class="mandatory">*</span></td><td align="left"><?php echo $frm->getFieldHTML('sample_essay'); ?></td>				  
				</tr>						
			</table>
			<small><?php 
				$upload_max = ini_get('upload_max_filesize');
				echo sprintf( Utilities::getLabel( 'L_Upload_Essay_File_Extensions_Restrictions' ), $upload_max );
			?></small><br/>
			<?php echo $frm->getFieldHTML('btn_submit'); ?>	  
		  </form><?php echo $frm->getExternalJS(); ?>
		  </div>
		  </div>
		  
		
	  </div>
	</div>
</div>