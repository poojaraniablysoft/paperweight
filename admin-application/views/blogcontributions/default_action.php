<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<div class="title"><h2><?php echo 'Blog Contributions'; ?></h2></div>
	<section class="box search_filter">
		<div class="box_head"></div>
		<div class="box_content clearfix toggle_container">
			<?php echo $frmContributions->getFormHtml(); ?>
		</div>
	</section>
	<section class="box" id="contributions-type-list"></section>
</section>
