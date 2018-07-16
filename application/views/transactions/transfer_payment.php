<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//echo "<pre>".print_r($arr_milestone,true)."</pre>";
?>

<div id="body">
    <div class="sectionInner clearfix">			
		<div class="fix-container">
            <div class="selected_content">                        	
				<div class="sectionTitle nomargin">
					<h4><?php echo Utilities::getLabel( 'L_Order_Payment' ); ?></h4>
				</div>
				<div class="gap"></div>
				<?php echo Message::getHtml(); ?>				
                <div class="sectionDetails nomargin_bottom">
					<div class="sectionbot clearfix">
						<h3 class="bluetitle"><a href="<?php echo generateUrl('task', 'order_process', array($arr_milestone['task_id'])); ?>">#<?php echo $arr_milestone['task_ref_id'] . ' ' . $arr_milestone['task_topic']; ?></a></h3>
						<div class="gap"></div>
						<small><?php echo Utilities::getLabel( 'L_Days_To_Pay_To_Writer' ); ?></small>
						<div class="gap"></div>
						<?php echo $frm->getformHtml(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>