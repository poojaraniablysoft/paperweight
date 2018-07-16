<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<div class="title"><h2><?php echo 'Blog Comments'; ?></h2></div>
	<section class="box search_filter">
		<div class="box_head"><h3></h3></div>
		<div class="box_content clearfix toggle_container">
			<?php echo Message::getHtml();?>
			<?php echo $frmComment->getFormHtml(); ?>
		</div>
	</section>
	<section class="box" id="comment-list"></section>
</section>
