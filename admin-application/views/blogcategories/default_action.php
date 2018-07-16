<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<div class="title"><h2><?php echo 'Blog Categories'; ?></h2><?php if($can_edit == true){ ?><a class="button green right" href="<?php echo generateUrl('blogcategories', 'add'); ?>">Add New Category</a><?php } ?></div>
	<section class="box search_filter">
		<div class="box_head"><h3>Blog Categories Filter</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php echo $frmCategory->getFormHtml(); ?>
		</div>
	</section>
	<section class="box" id="category-type-list"></section>
</section>
