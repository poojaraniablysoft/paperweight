<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
/* @var $frm Form */
?>

<td class="right-portion">
    <div class="content-area">
		<div class="box">
			<div class="title"><?php echo t_lang('PHRASE_ADMIN_SETUP_FORM'); ?></div>
			<div class="content">
				<div class="form"><?php echo $frm->getFormHtml(); ?></div>
			</div>
		</div>
	</div>
</td>