<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2><?php echo 'Blog Comments'; ?></h2></div>
	<section class="box">
		<div class="box_head"><h3>Delete</h3></div>
		<div class="box filterbox">
			<div class="box_content clearfix toggle_container">
				<p>You can't recover "Comment" once deleted, are you sure to delete? <a class="edit" href="<?php echo $delete_link; ?>">Yes</a> / <a class="edit" href="<?php echo generateUrl('blogcomments'); ?>">Cancel</a></p>
			</div>
		</div>
	</section>
</section>
