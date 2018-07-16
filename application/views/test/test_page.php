<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<script type="text/javascript">
	var remaining_time = <?php echo $remaining_time*1000; ?>;
	var quenum = "<?php echo Test::lastUpdatedId() === false ? 1 : Test::lastUpdatedId(); ?>";
	var totalque = <?php echo CONF_QUES_DISPLAY; ?>;
</script>

<!-- body -->
<div id="body">
	<div class="row whiteBg paddng strip">
	  <div class="fix-container"> 
		<?php echo Message::getHtml(); ?>
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
			  <div class="row_12" id="status">
				<div class="time-sect">
				  <div class="grid-1"><?php echo Utilities::getLabel( 'L_Question' ); ?> <span id="num"></span> <?php echo Utilities::getLabel( 'L_of' ); ?> <?php echo CONF_QUES_DISPLAY; ?></div>
				  <div class="grid-2"> <?php echo Utilities::getLabel( 'L_Time_left' ); ?> : <span id='timer'></span></span> </div>
				</div>
				<div id="loading" align="center"><img src="<?php echo CONF_WEBROOT_URL.'images/loadinfo.gif'; ?>" /></div>
				<div class="qWrap" id="queshow"></div>	
			  </div>
		  </div>
		</div>
	  </div>
	</div>
</div>