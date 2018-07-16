<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $milestone_status;
//echo "<pre>".print_r($arr_milestones,true)."</pre>";
?>
<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('orders');?>" class="selected">Orders Management</a></li>
		<li><a href="<?php echo generateUrl('rating');?>">Ratings & Reviews Management</a></li>				
	</ul>
</section>
<!--leftPanel end here--> 

<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('orders');?>">Orders Management</a></li>
		<li><a href="<?php echo generateUrl('orders','order_details',array($task_id));?>">Order Details</a></li>
		<li>Milestones</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Milestones</h2></div>
	
	<section class="box">
	
		<?php foreach ($arr_milestones as $ele) { ?>
			<div class="togglehead">
				<span class="grid_1">Words Count : <span><?php echo $ele['mile_words']; ?></span></span>
				<ul class="floatedList">
					<li><strong>Last Updated on:</strong> <?php echo displayDate($ele['mile_updated_on'], true, true, CONF_TIMEZONE); ?></li>
					<!--<li>
						<strong>Status: </strong>
						<?php// if ($ele['mile_status'] == 0) { ?>
							<a href="<?php// echo generateUrl('transactions', 'order_payment', array($ele['mile_id'])); ?>">Approve</a>
						<?php// } else { ?>
							<span class="<?php// if($ele['mile_status'] == 0){echo 'orgtag';}else if($ele['mile_status'] == 1){echo 'greentag';}else if($ele['mile_status'] == 2){echo 'redtag';} ?>"><?php// echo $milestone_status[$ele['mile_status']]; ?></span>
						<?php// } ?>
					</li>-->
				</ul>
			</div>
			<div class="togglecontent">
				<p><?php echo html_entity_decode(html_entity_decode($ele['mile_content'])); ?></p>
			</div>
		<?php } ?>
		<?php if (count($arr_milestones) == 0) echo 'No milestones added yet.'; ?>
	
	</section>
</section>
