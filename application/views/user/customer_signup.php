<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>

<div id="body">
	<div class="sectionInner clearfix">
		<div class="fix-container">
		 <div class="container--bordered">
			<?php echo Message::getHtml(); ?>
			<div class="sectionTitle"><h4><?php echo Utilities::getLabel( 'L_Register_as_a_customer' ); ?></h4></div>
			<div class="borderArea">
				<div class="repeatModule">
					<?php echo $frm->getFormHtml(); ?>
				</div>
			</div>
		</div>
		</div>
	</div>
</div>