<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

global $milestone_status;

//echo "<pre>" . print_r($arr_milestones,true) . "</pre>";
?>

<div id="body">    
    <div class="sectionInner clearfix">    
        <div class="pageTabs">
			<div class="fix-container"><?php echo getPageTabs('my_orders'); ?></div>
		</div>
        <div class="fix-container">
			<div class="sectionTitle">
				<h4><a href="<?php echo generateUrl('task', 'order_process', array($order_details['task_id'])); ?>">#<?php echo $order_details['task_ref_id']; ?></a> <?php echo $order_details['task_topic']; ?></h4>
			</div>            	
			<div class="historydetails">
				<?php foreach ($arr_milestones as $ele) { ?>
					<div class="togglehead">
						<span class="grid_1"><?php echo Utilities::getLabel( 'L_Page' ); ?> <?php echo $ele['mile_page']; ?> <span>(<?php echo $ele['mile_words']; ?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</span></span>
						<ul class="floatedList">
							<li><strong><?php echo Utilities::getLabel( 'L_Last_Updated_on' ); ?>:</strong> <?php echo displayDate($ele['mile_updated_on'], true, true, CONF_TIMEZONE); ?></li>
							<li>
								<strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong>
								<?php if (!User::isWriter() && $ele['mile_status'] == 0) { ?>
									<a href="<?php echo generateUrl('transactions', 'order_payment', array($ele['mile_id'])); ?>"><?php echo Utilities::getLabel( 'L_Approve' ); ?></a>
								<?php } else { ?>
									<span class="<?php if($ele['mile_status'] == 0){echo 'orgtag';}else if($ele['mile_status'] == 1){echo 'greentag';}else if($ele['mile_status'] == 2){echo 'redtag';} ?>"><?php echo $milestone_status[$ele['mile_status']]; ?></span>
								<?php } ?>
							</li>
						</ul>
					</div>
					<div class="togglecontent">
						<p><?php echo html_entity_decode($ele['mile_content']); ?></p>
					</div>
				<?php } ?>
				<?php if (count($arr_milestones) == 0) echo Utilities::getLabel( 'L_No_milestones_added_yet' ); ?>
			</div>
        </div>
    </div> 
</div>