<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('paper');?>">Manage Paper Type</a></li>
		<li>Add/Update Paper Type</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Manage Paper Type</h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>Add/Update Paper Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				$frmpaptype->fill($result);
				echo $frmpaptype->getFormHtml();
			?>
		</div>
	</section>
</section>
