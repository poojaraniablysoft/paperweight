<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;
//echo "<pre>".print_r($files,true)."</pre>";exit;
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
		<li>Order Details</li>
	</ul>
	<?php echo Message::getHtml();?>
	
	<div class="title"><h2>Order Details</h2><a class="button green" href="<?php echo generateUrl('orders','milestones',array($order['task_id']))?>">View Milestones</a></div>
	<?php if($order['task_cancel_request'] == 1 ) { ?>
		<section class="box" id="quit">
			<div class="box_content">
			<h2>Writer has requested to Quit from the order <a href="javascript:void(0);" onclick="show_description('<?php echo addslashes($order['task_desc_cancel_req']);?>');return false;">Click here to See Description!</a></h2><?php 
		if($order['task_status'] != 4) {
					?><a href="javascript:void(0);" class="button small black" onclick="cancel_order(<?php echo $order['task_id'];?>,this);return false;">Cancel Order</a><?php	} ?>
			</div>
		</section>
	<?php } ?>
	<section class="box">
		<div class="box_head"><h3><?php echo '#'.$order['task_ref_id'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$order['task_topic'];?> </h3></div>
		<div class="box_content">
			
			<?php
						
				if($order['task_status']==1) {
					$status = 'textyellow';
				}
				elseif($order['task_status']==0 || $order['task_status']==4) {
					$status = 'textred';
				}
				elseif($order['task_status']==2 || $order['task_status']==3) {
					$status = 'textgreen';
				}
				
				if(empty($order['writer'])) {
					$writer = 'Not Yet';
				}else {
					$writer = $order['writer'];
				}
				
				$tbl = new HtmlElement('table',array('class'=>'colTable','width'=>'100%'));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Order Topic');
				$tr->appendElement('td',array(),html_entity_decode($order['task_topic']));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Customer Name');
				$tr->appendElement('td',array(),html_entity_decode($order['customer_first_name'].' '.$order['customer_last_name']));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Customer ID');
				$tr->appendElement('td',array(),html_entity_decode($order['user_screen_name']));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Awarded Writer');
				$tr->appendElement('td',array(),$writer);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Discipline');
				$tr->appendElement('td',array(),($order['discipline_name']!='')?html_entity_decode($order['discipline_name']):'No record provided by customer!');
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Citation Style');
				$tr->appendElement('td',array(),($order['citstyle_name']!='')?html_entity_decode($order['citstyle_name']):'No record provided by customer!');
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Type of paper');
				$tr->appendElement('td',array(),html_entity_decode($order['paptype_name']));
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Pages');
				#$tr->appendElement('td',array(),$order['task_pages'].' pages / '.CONF_WORDS_PER_PAGE.' words');
				$tr->appendElement('td',array(),$order['task_pages'].' pages / '.$order['task_pages']*$order['task_words_per_page'].' words');
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Type of service');
				#$tr->appendElement('td',array(),$order['task_service_type']==0 ? 'Writing from scratch':'Editing',true);
				$tr->appendElement('td',array(),$order['task_service_type'],true);
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Order Status');
				$tr->appendElement('td',array(),'<span class="'.$status.'">'.$order_status[$order['task_status']].'</span>',true);
				
				/* if(!empty($arr_reviews)) {
					foreach ($arr_reviews as $ele) {
						$tr =$tbl->appendElement('tr',array());
						$tr->appendElement('td',array(),'Review by :'.$ele['user_screen_name']);
						$tr->appendElement('td',array(),'<a href="javascript:void(0);" onclick="reviews('.$order['task_id'].','.$ele['rev_id'].')" class="button small black">Preview</a><a href="javascript:void(0);"onclick="updateReview('.$ele['rev_id'].','.$order['task_id'].');" class="button small green">Decline</a>',true);
					}
				} */
				if(!empty($arr_reviews)) {
					foreach ($arr_reviews as $ele) {
						if($ele['rev_status']==1) {
							$rev_status = '<a href="javascript:void(0);"onclick="declineReview('.$ele['rev_id'].','.$ele['rev_reviewer_id'].',$(this));" class="button small green">Decline</a>';
						}else {
							$rev_status = '<a class="button small red">Declined</a>';
						}
						$tr =$tbl->appendElement('tr',array());
						$tr->appendElement('td',array(),'Review by :'.$ele['user_screen_name']);
						$tr->appendElement('td',array(),'<a href="javascript:void(0);" onclick="reviews('.$order['task_id'].','.$ele['rev_id'].')" class="button small black">Preview</a>'.$rev_status,true);
					}
				}else{
					$tr =$tbl->appendElement('tr',array());
					$tr->appendElement('td',array('colspan'=>2),'Yet no review posted!');			
				}
				
				$add_mat = '';
				foreach($files as $file) {
					$add_mat .= '<div class="attachments"><a title="'.$file['file_download_name'].'" href="'. generateUrl('user', 'download_file', array($file['file_id'])).'" class="attachlink attachicon">'.((strlen($file['file_download_name']) < 20) ? $file['file_download_name'] : substr($file['file_download_name'],0,10).'....'.substr($file['file_download_name'],-6)).'</a></div>';
				 } 
				if (count($files) == 0) {$add_mat .= 'No files uploaded.';}
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Additional Materials');
				$tr->appendElement('td',array(''),$add_mat,true);
				
				$milestone_mat = '';
				foreach($milestone_files as $milestone_file) {
					$milestone_mat .= '<div class="attachments"><a title="'.$milestone_file['file_download_name'].'" href="'. generateUrl('user', 'download_file', array($file['file_id'])).'" class="attachlink attachicon">'.((strlen($milestone_file['file_download_name']) < 20) ? $milestone_file['file_download_name'] : substr($milestone_file['file_download_name'],0,10).'....'.substr($milestone_file['file_download_name'],-6)).'</a></div>';
				 } 
				if (count($milestone_files) == 0) {$milestone_mat .= 'No files uploaded.';}
				
				$tr =$tbl->appendElement('tr',array());
				$tr->appendElement('td',array(),'Milestones file');
				$tr->appendElement('td',array(''),$milestone_mat,true);							
				if($order['task_status']!=3) {
					$date = displayDate($order['task_due_date'],true,true,CONF_TIMEZONE);
					$tr =$tbl->appendElement('tr',array());
					$tr->appendElement('td',array(),'Deadline Date');
					$tr->appendElement('td',array(),displayDate($order['task_due_date'],true,true,CONF_TIMEZONE).' (<span '.(strtotime($order['task_due_date']) > time() ? 'class="green_Txt"' : 'class="red_Txt"') .'>'. time_diff($date) .'</span>)',true);
				}else {
					$date = displayDate($order['task_completed_on'],true,true,CONF_TIMEZONE);
					$tr =$tbl->appendElement('tr',array());
					$tr->appendElement('td',array(),'Order Completed On');
					$tr->appendElement('td',array(),displayDate($order['task_completed_on'],true,true,CONF_TIMEZONE),true);
				}
				echo $tbl->getHtml();
			?>
		</div>		
	</section>
</section>