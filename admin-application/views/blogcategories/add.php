<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<section class="rightPanel wide">
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2><?php echo 'Blog Category Form'; ?></h2></div>
	<section class="box filterbox">
		<div class="box_content toggle_container">
			<div class="borderContainer">
				<?php echo $frmAdd->getFormHtml(); ?>
			</div>
		</div>
	</section>
</section>