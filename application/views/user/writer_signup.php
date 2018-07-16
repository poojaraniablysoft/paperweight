<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row whiteBg paddng strip">
		<div class="fix-container">
			  <!---register html start here---> 
			<div class="register">
			  <h1><?php echo Utilities::getLabel( 'L_Register_as_a_freelancer_writer' ); ?>: <span><?php echo Utilities::getLabel( 'L_Step_1_of_4' ); ?></span></h1>
		   
			  <div id="rootwizard" class="register-step">
				<ul>
				  <li class="active fill-up"> <a href="#tab1" data-toggle="tab">
					<figure class="figure">1</figure>
					<span><?php echo Utilities::getLabel( 'L_Submit_Email' ); ?></span> </a></li>
				  <li> <a href="#tab2" data-toggle="tab">
					<figure class="figure">2</figure>
					<span><?php echo Utilities::getLabel( 'L_Fill_in_Profile' ); ?></span></a></li>
				  <li> <a href="#tab3" data-toggle="tab">
					<figure class="figure">3</figure>
					<span><?php echo Utilities::getLabel( 'L_Pass_Grammar_Test' ); ?></span></a></li>
				  <li> <a href="#tab4" data-toggle="tab">
					<figure class="figure">4</figure>
					<span><?php echo Utilities::getLabel( 'L_Write_Sample_Essay' ); ?></span></a></li>
				</ul>
				<div id="bar" class="progress progress-striped active">
				  <div style="width: 1%;" class="bar"></div>
				</div>
			  </div>
			  <?php echo Message::getHtml(); ?>
			  <div class="register-form">
				  <?php echo $frm->getFormtag();?>
				  <div class="gray-background cols_1">
					<table>
					  <tr>
						<td colspan="2"><h2><?php echo Utilities::getLabel( 'L_Login_to_your_Account' ); ?></h2>
						<h3><?php echo Utilities::getLabel( 'L_Login_to_your_Account_Description' ); ?></h3>
						</td>
					  </tr>
					  <tr>
					  
					   <td align="right"><?php echo Utilities::getLabel( 'L_Email_Address' ); ?></td>
					   <td><?php echo $frm->getFieldHTML('user_email'); ?></td>
					  </tr>
					  <tr> 
						<td align="right"><?php echo Utilities::getLabel( 'L_Captcha' ); ?></td>  <td> <?php echo $frm->getFieldHTML('security_code'); ?></td>
					  </tr>
					  <tr>
						<td>&nbsp;</td>
						<td><?php echo $frm->getFieldHTML('code'); ?></td>
					  </tr>				  
					  <tr> 
						<td>&nbsp;</td>
						<td><?php echo $frm->getFieldHTML('btn_submit');?></td>
					  </tr>
					</table>
				  </div>
				  </form><?php echo $frm->getExternalJS(); ?>
				  
				  <div class="cols_2">
					<h2><?php echo Utilities::getLabel( 'L_Become_A_Freelance_Writer' ); ?></h2>
					<ul>
						<li><span>1</span><?php echo Utilities::getLabel( 'L_Submit_email' ); ?></li>
						<li><span>2</span><?php echo Utilities::getLabel( 'L_Fill_in_profile' ); ?></li>
						<li><span>3</span><?php echo Utilities::getLabel( 'L_Pass_grammar_test' ); ?></li>
						<li><span>4</span><?php echo Utilities::getLabel( 'L_Write_sample_essay' ); ?></li>
					</ul>			  
				  </div>
			  </div>
			</div>
		</div>
	</div>

</div>