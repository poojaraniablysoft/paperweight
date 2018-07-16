<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                    
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('countries');?>">Country Management</a></li>
		<li>Add/Update Country</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Add/Update Country</h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>Country Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				$frmCountry->fill($result);
				echo $frmCountry->getFormHtml();
			?>
		</div>
	</section>
</section>