<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo "<pre>".print_r($arr_milestone,true)."</pre>";
?>

<div id="body">
    <div class="sectionInner clearfix">		
		<div class="pageTabs">
			<div class="fix-container"><?php echo getPageTabs('my_orders'); ?></div>
		</div>
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
						<?php echo $frm->getformHtml(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>