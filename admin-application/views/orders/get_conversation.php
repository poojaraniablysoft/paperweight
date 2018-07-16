<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');

//echo '<pre>'.print_r($arr_bids,true).'</pre>';
?>
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
		<li><a href="<?php echo generateUrl('orders','bid_details',array($arr_bids['bid_id']));?>"><?php echo 'BIDS ON #'.$arr_bids["task_ref_id"].' '.$arr_bids['task_topic']; ?></a></li>
		<li>Conversation History</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Conversation History</h2></div>
	
	<section class="box">
	
		<?php foreach ($conver as $ele) { ?>
			
			<div class="togglecontent convers">
				<p><?php echo $ele['tskmsg_msg']; ?></p><span class="right"><h4><?php echo $ele['username']?></h4><?php echo displayDate($ele['tskmsg_date'], true, true, CONF_TIMEZONE); ?><span>
			</div>
		<?php } ?>
		<?php if (count($conver) == 0) echo 'No Conversation.'; ?>
	
	</section>
</section>