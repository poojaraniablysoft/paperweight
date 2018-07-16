<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>
<div id="body">
	<div class="row whiteBg paddng strip">
	  <div class="fix-container">
		  <!---register html start here---> 
		<div class="register">
		  <h1><?php echo Utilities::getLabel( 'L_Register_as_a_freelancer_writer' ); ?>: <span><?php echo Utilities::getLabel( 'L_Step_3_of_4' ); ?></span></h1>
	   
		  <div id="rootwizard" class="register-step">
			<ul>
			  <li class="fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Alert_Email_Verified_Please_Be_Continued_With_Test' ); ?></div>');" data-toggle="tab">
				<figure class="figure">1</figure>
				<span><?php echo Utilities::getLabel( 'L_Submit_Email' ); ?></span> </a></li>
			  <li class="fill-up"> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Alert_Profile_Done_Please_Be_Continued_With_Test' ); ?></div>');" data-toggle="tab">
				<figure class="figure">2</figure>
				<span><?php echo Utilities::getLabel( 'L_Fill_in_Profile' ); ?></span></a></li>
			  <li class="active"> <a href="<?php echo generateUrl('test','test_page');?>" data-toggle="tab">
				<figure class="figure">3</figure>
				<span><?php echo Utilities::getLabel( 'L_Pass_Grammar_Test' ); ?></span></a></li>
			  <li> <a href="javascript:void(0);" onclick="$.facebox('<div class=\'popup_msg information\'><?php echo Utilities::getLabel( 'L_Alert_Pass_Grammar_Test_To_Move_To_This_Step' ); ?></div>');" data-toggle="tab">
				<figure class="figure">4</figure>
				<span><?php echo Utilities::getLabel( 'L_Write_Sample_Essay' ); ?></span></a></li>
			</ul>
			<div id="bar" class="progress progress-striped active">
			  <div style="width: 66.66%;" class="bar"></div>
			</div>
		  </div>
		  
		  <div class="grammar-test gray-background">		  
			<div class="row_12">
				<?php if ($is_test_available) { ?>
					<h2><?php echo $latest_title; ?></h2>
					<p><?php echo $latest_content; ?></p>
				
				
					<div class="gray-background"><?php echo sprintf( Utilities::getLabel( 'E_Alert_Take_Test_Seriously' ), CONF_QUIZ_TIME ); ?></div>
				
					<a href="<?php echo generateUrl('test','test_page');?>" class="greenBtn"><?php if($remaining_time == 0){ echo Utilities::getLabel( 'L_Start_Test_Now' ); } else { echo Utilities::getLabel( 'L_Resume_Test' ); } ?></a>
				<?php } else { ?>
					<p><?php echo Utilities::getLabel( 'E_Grammar_Test_Is_Not_Available' ); ?></p>
				<?php } ?>
			</div>		  
		  </div>
		</div>
	  </div>
	</div>    
</div> 