<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH.'/_partials/common/left_cms_links.php';
?>
<section class="rightPanel">
	<div class="title"><h2><?php echo 'Home Page Management'; ?></h2></div>
	<section class="box filterbox">
		<div class="box_head"><h3>Home Page Block Form</h3></div>
		<div class="box_content clearfix toggle_container">
			<div class="form"><?php $frm->setExtra('class="siteForm"'); echo $frm->getFormHtml(); ?></div>
		</div>
		<div class="keywords-wrap">
			<h2>Grammer Test Block Keywords</h2>
			<ul>
				<li><strong>{quiz_time}</strong>: Quiz Time.</li>
				<li><strong>{ques_display}</strong>: No of question display.</li>
				<li><strong>{correct_ans_required}</strong>: No of correct answer required.</li>
			</ul>
		</div>
	</section>
</section>