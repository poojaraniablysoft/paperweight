<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="row whiteBg paddng strip">
		<div class="fix-container">
			  <!---register html start here---> 
			<div class="register">
			<?php 
			if($msg == 'profile')
			{
				$step = 2;
			}
			else if($msg == 'sample'){
				$step = 4;
			}
			else {
				$step = 1;
			}
			?>
			  <h1><?php echo Utilities::getLabel( 'L_Register_as_a_freelance_writer' ); ?>: <span><?php echo Utilities::getLabel( 'L_Step' ); ?> <?php echo $step ;?> <?php echo Utilities::getLabel( 'L_of' ); ?> 4</span></h1>
		   
			  <div id="rootwizard" class="register-step">
				<ul>
				  <li class="active fill-up"> <a href="#tab1" data-toggle="tab">
					<figure class="figure">1</figure>
					<span><?php echo Utilities::getLabel( 'L_Submit_Email' ); ?></span> </a></li>
				  <li class="<?php if($msg == 'sample' || $msg == 'profile'){ echo "active fill-up";} ?>"> <a href="#tab2" data-toggle="tab">
					<figure class="figure">2</figure>
					<span><?php echo Utilities::getLabel( 'L_Fill_in_Profile' ); ?></span></a></li>
				  <li class="<?php if($msg == 'sample'){ echo "active fill-up";} ?>"> <a href="#tab3" data-toggle="tab">
					<figure class="figure">3</figure>
					<span><?php echo Utilities::getLabel( 'L_Pass_Grammar_Test' ); ?></span></a></li>
				  <li class="<?php if($msg == 'sample'){ echo "active fill-up";} ?>"> <a href="#tab4" data-toggle="tab">
					<figure class="figure">4</figure>
					<span><?php echo Utilities::getLabel( 'L_Write_Sample_Essay' ); ?></span></a></li>
				</ul>
				<div id="bar" class="progress progress-striped active">
				  <div style="width: <?php if($msg == 'sample'){ echo "100%";}elseif($msg == 'profile') { echo "67.66%";} ?>;" class="bar"></div>
				</div>
			  </div>
			  
			  <div class="register-form">
			  <?php if($msg == 'email') {?>
				<div class="cols_1 padding-right-none">
				  <div class="section_msg">
					<h4><?php echo Utilities::getLabel( 'L_Email_Congratulations' ); ?></h4>					
					<div class="congo-msg-div"><?php echo Utilities::getLabel( 'L_Email_Congratulations_Description' ); ?></div>
					<p><strong><?php echo Utilities::getLabel( 'L_Writers_Support_Team' ); ?></strong></p>
				  </div>
			    </div>				  
				<div class="cols_2">
				  <h2><?php echo Utilities::getLabel( 'L_Become_A_Freelance_Writer' ); ?></h2>
				  <ul>
					<li><span>1</span><?php echo Utilities::getLabel( 'L_Submit_email' ); ?></li>
					<li><span>2</span><?php echo Utilities::getLabel( 'L_Fill_in_profile' ); ?></li>
					<li><span>3</span><?php echo Utilities::getLabel( 'L_Pass_grammar_test' ); ?></li>
					<li><span>4</span><?php echo Utilities::getLabel( 'L_Write_sample_essay' ); ?></li>
				  </ul>				  
				</div>
			  <?php } elseif($msg == 'profile') {?>
				<div class="section_msg">    
					<h4><?php echo Utilities::getLabel( 'L_Profile_Congratulations' ); ?></h4>
					<h2><?php echo Utilities::getLabel( 'L_Thank_you' ); ?> <?php echo Utilities::getLabel( 'L_Your_profile_has_been_saved' ); ?></h2>
					<div class="congo-msg-div"><?php echo Utilities::getLabel( 'L_Profile_Congratulations_Description' ); ?></div>
					<a href="<?php echo generateUrl('test');?>" class="greenBtn"><?php echo Utilities::getLabel( 'L_Go_to_Step_3' ); ?></a>					
			    </div>
			  <?php }elseif($msg == 'sample'){?>
				<div class="section_msg full-width">
					<h4><?php echo Utilities::getLabel( 'L_Sample_Congratulations' ); ?></h4>
					<h2><?php echo Utilities::getLabel( 'L_Thank_you' ); ?> <?php echo Utilities::getLabel( 'L_Your_essay_file_has_been_saved' ); ?></h2>
					<div  class="congo-msg-div"><?php echo Utilities::getLabel( 'L_Sample_Congratulations_Description' ); ?></div>
					<a href="<?php echo generateUrl('user','dashboard');?>" class="greenBtn"><?php echo Utilities::getLabel( 'L_Go_to_Dashboard' ); ?></a>					
				</div>
			  <?php }?>
			  </div>
			</div>
		</div>		
      
    </div>
</div>
<?php //exit;?>