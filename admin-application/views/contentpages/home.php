<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
?>

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""></a></li>
		<li>Manage Home Page</li>
	</ul>
	
	<div class="title">
		<h2>Manage Home Page</h2>
		<div class="gap"></div>
		<span class="redtxt">Please do not alter the placeholders shown within square brackets [ ].</span>
	</div>
	
	<?php echo Message::getHtml(); ?>
	<?php echo $frm->getFormHtml(); ?>
</section>
