<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
require_once( CONF_THEME_PATH . '_partials/common/left.php' );
?>
<section class="rightPanel">
	<div class="title"><h2><?php echo 'Meta Tags of '.$page_title; ?></h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>Meta Tags Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<div class="form"><?php $frm->setExtra('class="siteForm"'); echo $frm->getFormHtml(); ?></div>
		</div>
	</section>
</section>