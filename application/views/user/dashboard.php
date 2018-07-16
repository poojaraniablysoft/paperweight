<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
global $bid_status;
global $order_status;
//echo "<pre>".print_r($arr_writer_bids['orders'],true)."</pre>";
?>
<!--<h1 class="mob-show">Dashboard</h1>-->

<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
	<div class="order_page">
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ). '</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>	
<div class="white-box order-wrapper">
	<div class="browser-order <?php if(!User::isWriter()) {echo "blue-bg";}?>">
		<?php if(!User::isWriter()) { ?>
			<?php echo sprintf( Utilities::getLabel( 'L_Need_An_Expert_Writer' ), $available_writers ); ?>
			&nbsp; <a href="<?php echo generateUrl('task','add'); ?>" class="order"><?php echo Utilities::getLabel( 'L_Place_New_Order' ); ?></a>
		<?php 
			} else {  
			if($available_orders > 0) {
		?>
			<?php echo sprintf( Utilities::getLabel( 'L_Want_to_Earn_More' ), $available_orders ); ?>
			<a href="<?php echo generateUrl('task','orders');?>" class="order"><?php echo Utilities::getLabel( 'L_Browse_Orders' ); ?></a>
		<?php }else {
				echo Utilities::getLabel( 'L_No_project_is_available_this_time' );
			}
		}?>
		
	</div>

	<ul class="list-order <?php if(!User::isWriter()) {echo "green-icon";}?>">
		
	<?php if(!User::isWriter()) { ?>
		<li><div class="gray-box"><a href="<?php echo generateUrl('task', 'my_orders'); ?>"><i class="icon bidding-icon"></i><p><?php echo Utilities::getLabel( 'L_Orders_in_Bidding' ); ?></p> <span><?php echo $customer_activity_stats['orders_in_bidding'];?></span></a></div></li>
		<li><div class="gray-box"><a href="<?php echo generateUrl('task', 'my_orders'); ?>"><i class="icon progress-icon"></i><p><?php echo Utilities::getLabel( 'L_Orders_in_Progress' ); ?></p> <span><?php echo $customer_activity_stats['orders_in_progress'];?></span></a></div></li>
		<li><div class="gray-box"><a href="<?php echo generateUrl('task', 'my_orders'); ?>"><i class="icon complete-order"></i><p><?php echo Utilities::getLabel( 'L_Completed_Orders' ); ?></p> <span><?php echo $customer_activity_stats['orders_completed'];?></span></a></div></li>
		
		<li><div class="gray-box">
			<i class="icon wallets-icon"></i><p><?php echo Utilities::getLabel( 'L_My_Wallet_Credit' ); ?></p>
			<span><?php echo CONF_CURRENCY . $wallet_balance; ?></span>
		</div></li>
		<li><div class="gray-box">
			<i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Reserved_Amount' ); ?></p>
			<span><?php echo $reserved_amount['res_amount'];?></span>
			</div>
		</li>
	<?php } else { ?>
		<li><div class="gray-box"><a href="<?php echo generateUrl('bid', 'my_bids'); ?>"><i class="icon bidding-icon"></i><p><?php echo Utilities::getLabel( 'L_Orders_in_Bidding' ); ?></p> <span><?php echo $writer_activity_stats['orders_in_bidding']; ?></span></a></div></li>
		<li><div class="gray-box"><a href="<?php echo generateUrl('bid', 'my_bids'); ?>" class="count"><i class="icon progress-icon"></i><p><?php echo Utilities::getLabel( 'L_Orders_in_Progress' ); ?></p> <span><?php echo $writer_activity_stats['orders_in_progress']; ?></span></a></div></li>
		<li><div class="gray-box"><a href="<?php echo generateUrl('bid', 'my_bids'); ?>" class="count"><i class="icon complete-order"></i><p><?php echo Utilities::getLabel( 'L_Completed_Orders' ); ?></p><span><?php echo $writer_activity_stats['orders_completed']; ?></span></a></div></li>
		<li><div class="gray-box">
			<i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_My_Earnigs' ); ?></p>
			<span>
			<?php 
				if(($wallet_balance + $amount_withdrawn)<1000000) {
					echo CONF_CURRENCY . number_format($wallet_balance + $amount_withdrawn,2);
				}else if(($wallet_balance + $amount_withdrawn)>=1000000000){
					echo CONF_CURRENCY . number_format((($wallet_balance + $amount_withdrawn)/1000000000),0) . Utilities::getLabel( 'L_Billion+' );
				}else {
					echo CONF_CURRENCY . number_format((($wallet_balance + $amount_withdrawn)/1000000),0) . Utilities::getLabel( 'L_Million+' );
				}
			?>
			</span>
		</div></li>
	<?php }?>
	</ul>
	<div class="clearfix"></div>
	
</div>
<div class="grid_12">
	<div class="cols_1">
		<div class="writer-info white-box">
			<figure class="img-sect"><img src="<?php echo generateUrl('user', 'photo', array($user_info['user_id'],130,130)); ?>" alt=""></figure>
			 <h5><?php echo $user_info['user_first_name'].' '.$user_info['user_last_name'];?></h5>
			
			 <div class="average"> <?php if ($user_rating > 0) { ?>
					<div class="ratingsWrap"><span class="p<?php echo $user_rating*2; ?>"></span></div>
				<?php } else { ?>
					<span class="nrmltxt"><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
				<?php } ?>  </div>
			<p><?php echo Utilities::getLabel( 'L_Registered' ); ?>: <?php echo displayDate($user_info['user_regdate'])?></p>
			<p class="email"><i class="icon ion-email"></i> <a href="mailto:<?php echo $user_info['user_email'];?>"><?php echo $user_info['user_email'];?></a></p>
		
		</div>

	</div>

	<div class="cols_1 right-aling">
		<div class="recent-messages white-box">
			<h2><?php echo Utilities::getLabel( 'L_Recent_Messages' ); ?></h2>
			<?php if(count($arr_conversations) > 0) { ?>
			<ul>
			  <?php foreach($arr_conversations as $ele) { ?>
				<li><a href="<?php echo generateUrl('messages');?>" style="color: #3e434a;">
					<figure><img src="<?php echo generateUrl('user', 'photo', array($ele['user_id'],30,30)); ?>" alt="img"></figure>
					<div class="right-side">
					<p> <?php echo ((strlen($ele['tskmsg_msg']) > 80)? substr($ele['tskmsg_msg'],0,79).'...<a href="'.generateUrl('messages').'" class="read-more">' . Utilities::getLabel( 'L_read_more' ) . '</a>' : $ele['tskmsg_msg']);?> </p>
					<p class="author-name"><?php echo Utilities::getLabel( 'L_by' ); ?> 
						<a><?php echo ($ele['tskmsg_user_id']==User::getLoggedUserAttribute('user_id') ? Utilities::getLabel( 'L_Me' ):ucfirst($ele['user_screen_name']));?></a>
					</p>
					<span class="time"><?php echo displayDate($ele['last_updated_on'],true);?></span>				
					
					</div></a>
				</li>
			  <?php } ?>
		   </ul>
		   <?php }else { ?>
				<div class="no_result">
					<div class="no_result_icon">
						<img src="<?php echo CONF_WEBROOT_URL;?>images/small-logo.png">
					</div>
					<div class="no_result_text">
						<h5><?php echo Utilities::getLabel( 'L_No_Message_Found' ); ?></h5>
					</div>
				</div>
		   <?php }?>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<?php if(User::isWriter()) { ?>
<div class="user-list">
	<h2><?php echo Utilities::getLabel( 'L_Pending_Approval_from_Customers' ); ?></h2> 
	
	<div class="white-box">
		<div class="table-scroll">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
				<th><?php echo Utilities::getLabel( 'L_Order_Topic' ); ?></th>
				<th><?php echo Utilities::getLabel( 'L_Amount' ); ?></th>
				<th><?php echo Utilities::getLabel( 'L_Date' ); ?></th>
				<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
				<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>				
			  </tr>
			  
			  <?php foreach ($arr_writer_bids['orders'] as $key=>$ele) { 
					if($ele['bid_status'] == 2 || $ele['bid_status'] == 3) {
						continue;
					}
			  ?>
				  <tr>
					<td><a href="<?php echo generateUrl('bid','bid_details',array($ele['bid_id']),CONF_WEBROOT_URL);?>"><?php echo '#' . $ele['task_ref_id']; ?></a></td>
					<td><?php echo $ele['task_topic'];?></td>
					<td><?php echo CONF_CURRENCY . $ele['bid_price'] ; ?></td>
					<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone(); ?><br><small>(<?php echo html_entity_decode($ele['time_left']); ?>)</small></td>
					<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
					<td><span class="<?php if($ele['bid_status'] == 0){echo 'orgtag';}else if($ele['bid_status'] == 1){echo 'greentag';}else if($ele['bid_status'] == 2 || $ele['bid_status'] == 3){echo 'redtag';} ?>"><?php echo $bid_status[$ele['bid_status']]; ?></span></td>
				  </tr>
				  <?php if ($key == 2) break; ?>
			  <?php } ?>
			  <?php if (count($arr_writer_bids['orders']) == 0) echo '<tr><td colspan="7"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
			</table>
		</div>
	</div>
</div>
<?php }else { ?>
<div class="user-list">
	<h2><?php echo Utilities::getLabel( 'L_Latest_Activity' ); ?></h2> 
	<div class="white-box">
	  <div class="table-scroll">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Pages_(Words)' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
			<th><?php echo Utilities::getLabel( 'L_Bids' ); ?></th>
			<th><?php if(User::isWriter()) { echo Utilities::getLabel( 'L_Client' );}else{echo Utilities::getLabel( 'L_Writer' );}?></th>
		  </tr>
		  <?php $orders_list = $arr_bids_received; ?>
		  <?php foreach ($orders_list as $key=>$ele) { ?>
			  <tr>
				<?php if (!User::isUserActive()) {?>
					<td><?php echo '#' . $ele['task_ref_id']; ?></td>
				<?php }else {?>
					<td><a href="<?php echo generateUrl('task', 'order_bids', array($ele['task_id'])); ?>"><?php echo '#' . $ele['task_ref_id']; ?></a></td>
				<?php }?>
				<td><?php echo $ele['task_topic']; ?>	</td>
				<td><span class="<?php if($ele['bid_status'] == 0){echo 'orgtag';}else if($ele['bid_status'] == 1){echo 'greentag';}else if($ele['bid_status'] == 2 || $ele['bid_status'] == 3){echo 'redtag';} ?>"><?php echo $bid_status[$ele['bid_status']]; ?></span></td>
				<td><?php echo $ele['paptype_name'];?></td>
				<td><?php echo $ele['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $ele['task_pages']*$ele['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
				<?php 
					$timeLeft = '';
					if(html_entity_decode($ele['time_left']) != '') {
						$timeLeft = '<small>('. html_entity_decode($ele['time_left']) . ')</small>'; 
					}
				?>
				<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone(); ?><?php echo $timeLeft; ?></td>
				<td><?php echo Task::getnumbids($ele['task_id']); ?></td>
				<td><a target="_blank" href="<?php echo generateUrl('writer',seoUrl($ele['user_screen_name']));?>" class="client-name"><?php echo strlen($ele['user_screen_name']) > 10 ? substr($ele['user_screen_name'],0,10) . '...' : $ele['user_screen_name']; ?></a></td>
			  </tr>
		  <?php /* if ($key == 2) break; */ ?>
		  <?php } ?>
		  <?php if (count($orders_list) == 0) echo '<tr><td colspan="8"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
		</table>
	  </div>
	</div>
</div>
<?php }?>
<div class="user-list">

	<h2><?php echo Utilities::getLabel( 'L_Pending_Payments' ); ?></h2> 

	<div class="white-box">
		<div class="table-scroll">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th><?php echo Utilities::getLabel( 'L_S_No' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Amount_requested' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Date' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Comments' ); ?></th>
				</tr>
				<?php foreach($arr_request['data'] as $key=>$val) {?>
				<tr>
					<td><?php echo $key+1 . '.'; ?></td>									
					<td><?php echo CONF_CURRENCY.$val['req_amount'];?></td>
					<td><?php echo displayDate($val['req_date'],true);?></td>
					<td><?php echo ($val['req_comments'] != '') ? $val['req_comments']:'--';?></td>					
				</tr>
				<?php }?>
				<?php if (count($arr_request['data']) == 0) echo '<tr><td colspan="5"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
			</table>
		</div>
	</div>
</div>
<?php// echo "<pre>".print_r($arr_customer_completed_orders['orders'],true)."</pre>";?>
<div class="user-list">
	<h2><?php echo Utilities::getLabel( 'L_Orders' ); ?></h2> 
    <div class="responsivetabs">
		<ul class="completed-order">
			<li><a href="#view1" class="selected"><?php echo Utilities::getLabel( 'L_Completed' ); ?></a></li>
			<li><a href="#view2"><?php echo Utilities::getLabel( 'L_In_Progress' ); ?></a></li>
		</ul>
		<div class="white-box tabs_content" id="view1">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				 <tr>
					<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Pages_(Words)' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
					<?php if( User::isWriter() ) { ?>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					<?php } else { ?>
						<th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
					<?php } ?>
				  </tr>
				  <?php if(User::isWriter()){$order_completed = $arr_writer_completed_orders['orders'];}else{$order_completed = $arr_customer_completed_orders['orders'];}?>
				  <?php foreach ($order_completed as $key=>$ele) { ?>
					<tr>
						<?php if( !User::isWriter() ){ ?>
							<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id'])); ?>"><?php echo '#'. $ele['task_ref_id']; ?></a></td>
							<td><?php echo $ele['task_topic']; ?></td>
							<td><?php echo $ele['paper_type'];?></td>
							<td><?php echo $ele['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?> <br><small>(<?php echo $ele['task_pages']*$ele['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $ele['order_amount']; ?></td>
							<td><a href="#" class="client-name"><?php echo strlen($ele['writer']) > 10 ? substr($ele['writer'],0,10) . '...' : $ele['writer']; ?></a></td>
						<?php } else { ?>
							<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id'])); ?>"><?php echo '#'. $ele['task_ref_id']; ?></a></td>
							<td><?php echo $ele['task_topic']; ?></td>
							<td><?php echo $ele['paptype_name']; ?></td>
							<td><?php echo $ele['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?> <br><small>(<?php echo $ele['task_pages']*$ele['task_words_per_page']; ?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $ele['total_bid_amount']; ?></td>
							<td><a href="#" class="client-name"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></a></td>
						<?php } ?>
					</tr>
					<?php if ($key == 2) break; ?>
				  <?php } ?>
				  <?php if (count($order_completed) == 0) echo '<tr><td colspan="7"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>

				</table>
			</div>
		</div>

		<div class="white-box tabs_content" id="view2">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
					<!--th>Status</th-->
					<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Pages_(Words)' ); ?></th>
					<?php if( !User::isWriter() ) { ?>
						<th><?php echo Utilities::getLabel( 'L_Due_Date' ); ?></th>
					<?php } ?>
					
					<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
					<?php if( User::isWriter() ) { ?>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					<?php } else { ?>
						<th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
					<?php } ?>
				  </tr>
				<?php 
					$order_ongoing = User::isWriter()?$arr_writer_ongoing_orders['orders']:$arr_customer_ongoing_orders['orders'];
					foreach ($order_ongoing as $key=>$ele) { 
				?>
					<tr>
						<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id']));?>"><?php echo '#'. $ele['task_ref_id'];?></a></td>
						<td><?php echo $ele['task_topic'];?></td>
						<td><?php if( User::isWriter() ){ echo $ele['paptype_name']; } else { echo $ele['paper_type']; } ?></td>
						<td><?php echo $ele['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $ele['task_pages']*$ele['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
						<?php if( !User::isWriter() ) { ?>
							<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone(); ?><br><small>(<?php echo html_entity_decode($ele['time_left']); ?>)</small></td>
						<?php } ?>
						<?php if(User::isWriter()) { ?>
						<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $ele['total_bid_amount']; ?></td>
						<td><a href="javascript:void(0);" class="client-name"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></a></td>
						<?php } else { ?>
						<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $ele['order_amount']; ?></td>
						<td><a target="_blank" href="<?php echo generateUrl('writer',seoUrl($ele['writer']));?>" class="client-name"><?php echo strlen($ele['writer']) > 10 ? substr($ele['writer'],0,10) . '...' : $ele['writer']; ?></a></td>	
						<?php } ?>
					</tr>
					<?php if ($key == 2) break; ?>
				<?php } ?>
				<?php if (count($order_ongoing) == 0) echo '<tr><td colspan="7"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>									
				</table>
			</div>
		</div>
    </div>
</div>