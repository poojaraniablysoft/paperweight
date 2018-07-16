<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2><?php echo 'Blog Posts'; ?></h2><a class="button green right" href="<?php echo generateUrl('blogposts', 'add'); ?>">Add New Post</a></div>
	<section class="box search_filter">
		<div class="box_head"><h3><?php echo 'Blog Posts Filters'; ?></h3></div>
		<div class="box_content clearfix toggle_container">
			<?php echo $frmCategory->getFormHtml(); ?>
		</div>
	</section>
	<section class="box" id="post-type-list"></section>
</section>
