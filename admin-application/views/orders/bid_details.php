<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;
global $bid_status;
//echo '<pre>'.print_r($order,true).'</pre>';
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
		<li><a href="<?php echo generateUrl('orders','bids',array($order['task_id']));?>"><?php echo 'BIDS ON #'.$order["task_ref_id"].' '.$order['task_topic']; ?></a></li>
		<li>Bid Detail</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2><?php echo '#'.$order['task_ref_id'].'&nbsp;&nbsp;'.$order['task_topic'];?></h2><a class="button green" href="<?php echo generateUrl('orders','get_conversation',array($order['bid_id']))?>">View Conversation</a></div>
	
	<section class="box">
		<div class="box_head"><h3>Bid Detail</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php
				if($order['task_status']==1) { 
					$status = 'button small orange'; 
				}
				elseif($order['task_status']==0 || $order['task_status']==4) {
					$status = 'button small red'; 
				}
				elseif($order['task_status']==2 || $order['task_status']==3) {
					$status = 'button small green'; 
				}	
				
				if($order['bid_status']==1) {
					$stat = 'button small green'; 
				}
				elseif($order['bid_status']==0) {
					$stat = 'button small orange'; 
				}
				elseif($order['bid_status']==2 || $order['bid_status']==3) {
					$stat = 'button small red'; 
				}
				
				$tbl = new HtmlElement('table',array('class'=>'colTable','width'=>'100%'));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Order Topic');
				$tr->appendElement('td',array(),$order['task_topic']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Bid Price');
				$tr->appendElement('td',array(),CONF_CURRENCY.$order['bid_price']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Writer Name');
				$tr->appendElement('td',array(),$order['user_screen_name']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Discipline');
				$tr->appendElement('td',array(),$order['discipline_name']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Citation Style');
				$tr->appendElement('td',array(),$order['citstyle_name']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Type of paper');
				$tr->appendElement('td',array(),$order['paptype_name']);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Pages');
				$tr->appendElement('td',array(),$order['task_pages'].' pages / '.CONF_WORDS_PER_PAGE.' words');
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Type of service');
				$tr->appendElement('td',array(),$order['task_service_type']==0 ? 'Writing from scratch':'Editing',true);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Bid Status');
				$tr->appendElement('td',array(),'<a class="'.$stat.'">'.$bid_status[$order['bid_status']].'</a>',true);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Order Status');
				$tr->appendElement('td',array(),'<a class="'.$status.'">'.$order_status[$order['task_status']].'</a>',true);
						
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Deadline Date');
				$tr->appendElement('td',array(),displayDate($order['task_due_date'],true,true,CONF_TIMEZONE).' ('.time_diff($order['task_due_date']).')',true);
				
				echo $tbl->getHtml();
			?>
		</div>
	</section>
</section>
