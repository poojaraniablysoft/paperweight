<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
?>
<div id="body">
	<div class="sectionInner clearfix">
		<div class="fix-container">
			<div class="sectionTitle"><h4><?php echo $arr_listing['cmspage_title']; ?></h4></div>
			<div class="borderArea">
				<?php
					echo Message::getHtml(); 
					echo html_entity_decode($arr_listing['cmspage_content']);
				?>
			</div>	
		</div>
	</div>
</div>