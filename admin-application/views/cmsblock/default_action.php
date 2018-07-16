<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<div class="title"><h2><?php echo 'Page Blocks Management'; ?></h2></div>
	<section class="box search_filter">
		<div class="box_content clearfix toggle_container">
			<?php echo $frm->getFormHtml(); ?>
		</div>
	</section>
	<section class="box" id="listing-div" data-href="<?php echo generateUrl('cmsblock', 'listing'); ?>"></section>
</section>
