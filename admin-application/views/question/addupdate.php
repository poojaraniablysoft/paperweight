<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""></a></li>
		<li><a href="<?php echo generateUrl('question');?>">Question And Answer</a></li>
		<li>Add/Update Question</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>ADD/UPDATE QUESTION</h2></div>
	<section class="box filterbox">
		<!--<div class="box_head"><h3>Question/Answer Form</h3></div>-->
		<div class="box_content clearfix toggle_container">
			<?php 
				$frmque->fill($result);
				echo $frmque->getFormHtml();
			?>
		</div>
	</section>
</section>