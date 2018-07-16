<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'navigation_header.php';
?>
<div id="body">
  <div class="row whiteBg strip">
    <div class="fix-container">
     <div class="blog-wrap">
		<!--left penal start here-->
		<div class="blog-left" id="archives-post-list"></div>     
		<!--left penal end here-->
     
		<?php include 'rightpanelblog.php'; ?>
		<?php echo $frmArchives->getFormHtml();?>
	  </div>
    </div>
  </div>
</div>
