<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $bid_status;
global $order_status;
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
		<li><?php echo 'BIDS ON #'.$task_topic['task_ref_id'].' '.$task_topic['task_topic']; ?></li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2><?php echo 'BIDS ON <span class="task_refid">#'.$task_topic['task_ref_id'].'</span>'; ?></h2></div>
	<section class="box search_filter">
		<table width="100%" style="font-size:20px;">
			<tr>
				<td align="left"><b>Pages : </b><?php echo $task_topic['task_pages'];?></td>
				<td align="right"><b>Deadline : </b><?php echo displayDate($task_topic['task_due_date'],true,true).' ('.time_diff($task_topic['task_due_date']).')';?></td>			
			</tr>
		</table>
	</section>

	<section class="box">
		<div class="box_head"><h3>Bids Listing<h3></div>
		<div class="box_content clearfix toggle_container">
			<?php
				$arr_flds = array(
				'listserial'=>'S.No.',
				'user_screen_name'=>'Writers',
				'paptype_name'=>'Type of papers',
				'bid_price'=>'Price',
				'bid_status'=>'Bid Status',
				'action' => 'Action'
				);
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {					
					$th->appendElement('th', array(), $val);										
				}
				
				foreach ($order as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
							$td->appendElement('plaintext', array(), $sn+$start_record);
							break;
						case 'task_topic':
							$td->appendElement('plaintext', array(), $row['task_topic'] , true);
							break;
						case 'user_screen_name':
							$td->appendElement('plaintext', array(), $row['user_screen_name'] , true);
							break;
						case 'paptype_name':
							$td->appendElement('plaintext', array(), $row['paptype_name'] , true);
							break;
						case 'bid_price':
							$td->appendElement('plaintext', array(), CONF_CURRENCY.$row['bid_price'] , true);
							break;
						case 'task_due_date':
							$td->appendElement('plaintext', array(), $row['task_due_date'] , true);
							break;
						case 'bid_status':
							if($row['bid_status']==1) {
								$status = 'textgreen';
							}
							elseif($row['bid_status']==0) {
								$status = 'textyellow';
							}
							elseif($row['bid_status']==2 || $row['bid_status']==3) {
								$status = 'textred';
							}
							
							$td->appendElement('span', array('class'=>$status), $bid_status[$row['bid_status']] , true);
							break;
						case 'action':
							
							$td->appendElement('a', array('href'=>generateUrl('orders','bid_details',array($row['bid_id'])), 'class'=>'button small black', 'title'=>'Preview'),'Preview', true);
							$td->appendElement('a', array('href'=>generateUrl('orders','get_conversation',array($row['bid_id'])), 'class'=>'button small green', 'title'=>'View Conversation'),'View Conversation', true);
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
						}
					}
				}
				if (count($order) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
		</div>
	</section>
</section>