<?php  
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
global $bid_status;
global $invitation_status;

//echo "<pre>".print_r($arr_bids,true)."</pre>";exit;
//echo "<pre>" . print_r($arr_completed_orders['orders'],true) . "</pre>";
?>

<script type="text/javascript">
	var filter = "<?php echo $filter; ?>";
</script>
<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
	<div class="order_page">
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ) . '</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>
<?php echo Message::getHtml(); ?>
<div class="my-order" id="tab1">
	<div class="user-list padding-top-none" >
		<h2><?php echo Utilities::getLabel( 'L_My_Bids' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
						<th width="25%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Bids' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($arr_bids['orders'] as $key=>$ele) { ?>
						<tr>
							<td><a href="<?php echo generateUrl('bid','bid_details',array($ele['bid_id']),CONF_WEBROOT_URL);?>"><?php echo '#' . $ele['task_ref_id']; ?></a></td>
							<td><?php echo $ele['task_topic']; ?>	</td>
							<td><span class="<?php if($ele['bid_status'] == 0){echo 'orgtag';}else if($ele['bid_status'] == 1){echo 'greentag';}else if($ele['bid_status'] == 2 || $ele['bid_status'] == 3){echo 'redtag';} ?>"><?php echo $bid_status[$ele['bid_status']]; ?></span></td>
							<td><?php echo $ele['paptype_name'];?></td>
							<td><?php echo $ele['task_pages'] . ' ' . Utilities::getLabel( 'L_pages' );  ?> <br><small>(<?php echo ( $ele['task_pages']*$ele['task_words_per_page'] ) . ' ' . Utilities::getLabel( 'L_words' ); ?> )</small></td>
							<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone();?><small <?php if(strtotime($ele['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo html_entity_decode($ele['time_left']); ?>)</small></td>
							<td><?php echo Task::getnumbids($ele['task_id']); ?></td>
							<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
						</tr>
					<?php } ?>
					<?php if (count($arr_bids['orders']) == 0) echo '<tr><td colspan="8"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
				</tbody>
				</table>
				<div class="gap"></div>
				<div class="pagination"><?php echo generatePagingStringHtml($arr_bids['page'],$arr_bids['pages'],"bids('mine',xxpagexx);"); ?></div>
			</div>
		</div>
	</div>
	</div>
	<div class="user-list" id="tab2">
		<h2><?php echo Utilities::getLabel( 'L_In_Progress' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
						<th width="25%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($arr_ongoing_orders['orders'] as $key=>$ele) { ?>
						<tr>
							<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id']));?>"><?php echo '#'. $ele['task_ref_id'];?></a></td>
							<td><?php echo $ele['task_topic'];?></td>
							<td><?php echo $ele['paptype_name'];?></td>
							<td><?php echo $ele['task_pages'] . ' ' . Utilities::getLabel( 'L_pages' ); ?><br><small>(<?php echo ( $ele['task_pages']*$ele['task_words_per_page'] ) . ' ' . Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?> <br><?php print_time_zone();?><small <?php if(strtotime($ele['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo html_entity_decode($ele['time_left']); ?>)</small></td>
							<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' of ' . CONF_CURRENCY . $ele['total_bid_amount']; ?></td>
							<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
						</tr>
					<?php } ?>
					<?php if (count($arr_ongoing_orders['orders']) == 0) echo '<tr><td colspan="7"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
				</table>
				<div class="gap"></div>
				<div class="pagination"><?php echo generatePagingStringHtml($arr_ongoing_orders['page'],$arr_ongoing_orders['pages'],"bids('ongoing',xxpagexx);"); ?></div>
			</div>
		</div>
	</div>
	<div class="user-list" id="tab3">
		<h2><?php echo Utilities::getLabel( 'L_Completed' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
						<th width="25%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($arr_completed_orders['orders'] as $key=>$ele) { ?>
						<tr>
							<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id'])); ?>"><?php echo '#'. $ele['task_ref_id']; ?></a></td>
							<td><?php echo $ele['task_topic'];?></td>
							<td><?php echo $ele['paptype_name'];?></td>
							<td><?php echo $ele['task_pages'] . ' ' . Utilities::getLabel( 'L_pages' ); ?><br><small>(<?php echo ( $ele['task_pages']*$ele['task_words_per_page'] ) . ' ' . Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo $ele['task_completed_on']; ?></td>
							<td><?php echo CONF_CURRENCY . $ele['amount_paid'] . ' of ' . CONF_CURRENCY . $ele['total_bid_amount']; ?></td>
							<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
						</tr>
					<?php } ?>
					<?php if (count($arr_completed_orders['orders']) == 0) echo '<tr><td colspan="7"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
			</table>
				<div class="gap"></div>
				<div class="pagination"><?php echo generatePagingStringHtml($arr_completed_orders['page'],$arr_completed_orders['pages'],"bids('completed',xxpagexx);"); ?></div>
			</div>
		</div>
	</div>
	<div class="user-list" id="tab4">

		<h2><?php echo Utilities::getLabel( 'L_Cancelled' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
						<th width="30%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Bids' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($arr_cancelled_orders['orders'] as $key=>$ele) {?>
						<tr>
							<?php if ($ele['bid_status'] == 1 || ($ele['bid_status'] == 3 && $ele['task_writer_id'] > 0)) { ?>
								<td><a href="<?php echo generateUrl('task','order_process',array($ele['task_id']));?>"><?php echo '#'.$ele['task_ref_id'];?></a></td>
							<?php } else { ?>
								<td><a href="<?php echo generateUrl('bid','bid_details',array($ele['bid_id']));?>"><?php echo '#'.$ele['task_ref_id'];?></a></td>
							<?php } ?>
							
							<td><?php echo $ele['task_topic'];?></td>
							<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);
							 echo ' ';
							 print_time_zone();
							?></td>
							<td><?php echo Task::getnumbids($ele['task_id']);?></td>
							<td><span title="<?php echo $ele['user_first_name']; ?>"><?php echo strlen($ele['user_first_name']) > 10 ? substr($ele['user_first_name'],0,10) . '...' : $ele['user_first_name']; ?></span></td>
						</tr>
					<?php } ?>
					<?php if (count($arr_cancelled_orders['orders']) == 0) echo '<tr><td colspan="5"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
				</tbody>
				</table>
				<div class="gap"></div>
				<div class="pagination"><?php echo generatePagingStringHtml($arr_cancelled_orders['page'],$arr_cancelled_orders['pages'],"bids('cancelled',xxpagexx);"); ?></div>
			</div>
		</div>
	</div>

	<div class="user-list" id="tab5">
		<h2><?php echo Utilities::getLabel( 'L_Invites_Received' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
						<th width="25%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Client' ); ?></th>
					</tr>
				</thead>
				<tbody>

					<?php foreach ($arr_invitations['orders'] as $key=>$ele) { ?>
						<tr>
							<?php if ($ele['task_status'] != 2 && $ele['task_status'] != 3) { ?>
								<td><a href="<?php echo generateUrl('bid', 'add', array($ele['task_id'])); ?>"><?php echo '#' . $ele['task_ref_id']; ?></a></td>
							<?php } else { ?>
								<td><a href="<?php echo generateUrl('task', 'order_process', array($ele['task_id'])); ?>"><?php echo '#' . $ele['task_ref_id']; ?></a></td>
							<?php } ?>
							<td><?php echo $ele['task_topic']; ?></td>
							<td><span class="<?php if($ele['inv_status'] == 0){echo 'orgtag';}else if($ele['inv_status'] == 1){echo 'greentag';}else if($ele['inv_status'] == 2){echo 'redtag';} ?>"><?php echo $invitation_status[$ele['inv_status']]; ?></span></td>
							<td><?php echo $ele['paptype_name']; ?></td>
							<td><?php echo $ele['task_pages'] . ' ' . Utilities::getLabel( 'L_pages' ); ?><br><small>(<?php echo ( $ele['task_pages']*$ele['task_words_per_page'] ) . ' ' . Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE); ?> <br><?php print_time_zone();?><br><small <?php if(strtotime($ele['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo html_entity_decode($ele['time_left']); ?>)</small></td>
							<td><span title="<?php echo $ele['customer']; ?>"><?php echo strlen($ele['customer']) > 10 ? substr($ele['customer'],0,10) . '...' : $ele['customer']; ?></span></td>
						</tr>
					<?php } ?>
					<?php if (count($arr_invitations['orders']) == 0) echo '<tr><td colspan="8"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
				</tbody>
				</table>
				<div class="gap"></div>
				<div class="pagination"><?php echo generatePagingStringHtml($arr_invitations['page'],$arr_invitations['pages'],"invitations(xxpagexx);"); ?></div>
			</div>
		</div>
	</div>


