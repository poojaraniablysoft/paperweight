<?php
defined('SYSTEM_INIT') or die('Invalid Usage');                     
?>
<section class="rightPanel wide" style="min-height:300px;">
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Update Review Comment</h2></div>	
	<section class="box">		
		<div class="box_content clearfix toggle_container">
			<?php 
				$frm->fill($pre_review);
				echo $frm->getFormHtml();
			?>
		</div>
	</section>
</section>
