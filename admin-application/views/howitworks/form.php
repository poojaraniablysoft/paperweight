<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
/* @var $frm Form */
?>
<section class="rightPanel">
	<div class="title"><h2><?php echo 'How It Works Steps Management'; ?></h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>How It Works Step Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<div class="form"><?php $frm->setExtra('class="siteForm"'); echo $frm->getFormHtml(); ?></div>
		</div>
	</section>
</section>