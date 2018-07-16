<?php
defined('SYSTEM_INIT') or die('Invalid Usage');  
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('contentpages');?>">Content Management System</a></li>
		<li>Add/Update Content</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Add/Update Content</h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>Content Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php 
				//$result['cmspage_title'] = html_entity_decode($result['cmspage_title']);
				//$frmcontent->fill($result);
				echo $frmcontent->getFormHtml();
			?>
		</div>
	</section>
</section>
