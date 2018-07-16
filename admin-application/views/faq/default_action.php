<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<div class="title">
		<h2><?php echo 'FAQ\'s Management'; ?></h2>
		<a class="button green" href="<?php echo generateUrl('faq', 'form');?>">Add FAQ</a>
	</div>
	<section class="box search_filter">
		<div class="box_content clearfix toggle_container">
			<?php echo $frm->getFormHtml(); ?>
		</div>
	</section>
	<section class="box search_filter">
		<div class="box_content clearfix toggle_container">
		<form class="siteForm">		
		Show: <select id="type_id" title="" name="type_id"><option value="1" selected="selected">Customer</option><option value="2">Writer</option></select>
		</form>
		</div>	
	</section>	
	<section class="box" id="listing-div" data-href="<?php echo generateUrl('faq', 'listing'); ?>"></section>
</section>
