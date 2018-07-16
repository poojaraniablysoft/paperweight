<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('emailtemplate');?>">Email Template</a></li>
		<li>Add/Update Email Templates</li>
	</ul>
	<div class="title"><h2>Add/Update Email Templates</h2></div>
	<?php echo Message::getHtml(); ?>
	<section class="box filterbox">
		<div class="box_head"><h3>Template Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				//$result['tpl_body'] = html_entity_decode($result['tpl_body']);
				//$frmEmailTemplate->fill($result);
				echo $frmEmailTemplate->getFormHtml();
			?>
		</div>
	</section>
</section>