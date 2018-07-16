<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

global $order_status;
global $invitation_status;

//echo '<pre>' . print_r($order_status,true) . '</pre>';exit;
?>

<script type="text/javascript">
	var filter = "<?php echo $filter; ?>";
</script>
	<script>
$(document).ready(function(){	
	$('.userdate').each(function() {		
	var val=parseInt($(this).attr('data-timestamp'))*1000;	
	var d = new Date(val);		
	var format='<?php echo CONF_DATE_FORMAT_PHP; ?>';
	
	var formatted;
	//alert(format)
	switch(format){
		case 'm-d-Y':		
		formatted = (d.getMonth() + 1) + '-' + d.getDate() + '-' +  d.getFullYear() +' '+ d.getHours() + ":" + d.getMinutes();
		break;
		
		case 'd/m/Y':
		formatted = d.getDate() + '/' + (d.getMonth() + 1) + '/' +  d.getFullYear() +' '+ d.getHours() + ":" + d.getMinutes();
		break;	

		case 'M d, Y':
		formatted = d.toUTCString().split(' ')[2] + ' ' + d.getDate() + ',' +  d.getFullYear() +' '+ d.getHours() + ":" + d.getMinutes();
		break;	
		
		case 'Y-m-d':
		formatted = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' +  d.getDate() +' '+ d.getHours() + ":" + d.getMinutes();
		break;				
	}
	$(this).html(formatted);
	});	
})
	</script>
<div class="my-order" id="tab1">
	<div class="user-list padding-top-none">
		<?php echo Message::getHtml();?>
		<h2><?php echo Utilities::getLabel( 'L_My_Orders' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
							<th width="20%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Bids' ); ?></th>
							<?php if (User::isUserActive()) {?>
							<th><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
							
							<?php $colspan = '8';}else{ $colspan="7";}?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($arr_orders['orders'] as $key=>$arr) { ?>
						<tr>
							<?php if(!User::isWriter()) {
								$first_order = Common::is_first_order_done();
								if($first_order == false) {
									echo '<td><a href="'.generateUrl("task","add",array($arr["task_id"])).'">#'. $arr['task_ref_id'].'</td>';
									$action = '<td class="dataTable"><a href="'.generateUrl("task","add",array($arr["task_id"])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>';
								}else {
									if (!User::isUserActive()) {?>
									<td><?php echo '#' . $arr['task_ref_id']; ?></td>
									<?php }else {?>
									<td><a href="<?php echo generateUrl('task', 'order_bids', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
									<?php
										$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_bids', array($arr['task_id'])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>';
									}
								}
							}?>
							
							<td><?php echo $arr['task_topic']; ?>	</td>
							<td><span class="<?php if(in_array($arr['task_status'],array(0,1,2))){echo 'orgtag';}else if($arr['task_status'] == 3){echo 'greentag';}else if($arr['task_status'] == 4){echo 'redtag';} ?>"><?php echo $order_status[$arr['task_status']]; ?>	</span></td>
							<td><?php echo $arr['paper_type']; ?></td>
							<td><?php echo $arr['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $arr['task_pages']*$arr['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<?php							
								$d = new DateTime( $arr['task_due_date'], new DateTimeZone( 'America/Phoenix' ) );
								//$d = new DateTime($arr['task_due_date'], new DateTimeZone('Asia/Kolkata'));
								//$d = new DateTime($arr['task_due_date'], new DateTimeZone(CONF_TIMEZONE));
							?>							
							<td><?php  
								print_time_zone( );
								echo '-'.displayDate( $arr['task_due_date'], true, true, CONF_TIMEZONE );							
							?><br /><span class="userdate" data-timestamp="<?php echo $d->getTimestamp( ); ?>">
							<?php #echo displayDate($arr['task_due_date'],true, true, CONF_TIMEZONE); ?></span><br />
							<small <?php if( strtotime( $arr['task_due_date'] ) > time( ) ){ echo 'class="green_Txt"'; } else { echo 'class="red_Txt"'; }?>>(<?php echo html_entity_decode( $arr['time_left'] ); ?>)</small></td>
							<td><?php echo $arr['bid_id'] > 0 ? $arr['writer'] : $arr['total_bids'] . ' '; ?></td>
							<?php echo $action;?>
						</tr>
					<?php } ?>
					<?php if (count($arr_orders['orders']) == 0) echo '<tr><td colspan="'.$colspan.'"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],"bids('mine',xxpagexx);"); ?></div>
		</div>
	</div>
	</div>
	<div class="user-list" id="tab2">
		<h2><?php echo Utilities::getLabel( 'L_In_progress' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
							<th width="20%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
							<?php if (User::isUserActive()) {?>
							<th><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
							<?php $colspan = '8';}else{ $colspan="7";}?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($arr_ongoing_orders['orders'] as $key=>$arr) { ?>
						<tr>
							<?php if (!User::isUserActive()) {?>
							<td><?php echo '#' . $arr['task_ref_id']; ?></td>
							<?php }else {?>
							<td><a href="<?php echo generateUrl('task', 'order_process', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
							<?php
								$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_process', array($arr['task_id'])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>';
							}?>
							<td><?php echo $arr['task_topic']; ?>	</td>
							<td><?php echo $arr['paper_type']; ?></td>
							<td><?php echo $arr['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $arr['task_pages']*CONF_WORDS_PER_PAGE;?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo displayDate($arr['task_due_date'],true, true, CONF_TIMEZONE); ?> <br><?php 
							print_time_zone()							
							 ?><br><small <?php if(strtotime($arr['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo html_entity_decode($arr['time_left']); ?>)</small></td>
							<td><?php echo CONF_CURRENCY . $arr['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $arr['order_amount']; ?></td>
							<td><?php echo strlen($arr['writer']) > 10 ? substr($arr['writer'],0,10) . '...' : $arr['writer']; ?></td>
							<?php echo $action;?>
						</tr>
					<?php } ?>
					<?php if (count($arr_ongoing_orders['orders']) == 0) echo '<tr><td colspan="'.$colspan.'"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo generatePagingStringHtml($arr_ongoing_orders['page'],$arr_ongoing_orders['pages'],"bids('ongoing',xxpagexx);"); ?></div>
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
							<th width="20%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Completed_on' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Amount_Paid' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
							<?php if (User::isUserActive()) {?>
							<th><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
							<?php $colspan = '8';}else{ $colspan="7";}?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($arr_completed_orders['orders'] as $key=>$arr) { ?>
						<tr>
							<?php if (!User::isUserActive()) {?>
							<td><?php echo '#' . $arr['task_ref_id']; ?></td>
							<?php }else {?>
							<td><a href="<?php echo generateUrl('task', 'order_process', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
							<?php
								$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_process', array($arr['task_id'])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>';
							}?>
							<td><?php echo $arr['task_topic']; ?>	</td>
							<td><?php echo $arr['paper_type']; ?></td>
							<td><?php echo $arr['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small <?php if(strtotime($arr['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo $arr['task_pages']*CONF_WORDS_PER_PAGE;?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo $arr['task_completed_on']; ?></td>
							<td><?php echo CONF_CURRENCY . $arr['amount_paid'] . ' ' . Utilities::getLabel( 'L_of' ) . ' ' . CONF_CURRENCY . $arr['order_amount']; ?></td>
							<td><?php echo strlen($arr['writer']) > 10 ? substr($arr['writer'],0,10) . '...' : $arr['writer']; ?></td>
							<?php echo $action;?>
						</tr>
					<?php } ?>
					<?php if (count($arr_completed_orders['orders']) == 0) echo '<tr><td colspan="'.$colspan.'"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>					  </tbody>
				</table>
			</div>
			<div class="pagination"><?php echo generatePagingStringHtml($arr_completed_orders['page'],$arr_completed_orders['pages'],"bids('completed',xxpagexx);"); ?></div>
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
						<th><?php echo Utilities::getLabel( 'L_Type_of_Paper' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Pages_Words' ); ?></th>
						<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
						<?php if (User::isUserActive()) {?>
						<th><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
						<?php $colspan = '6';}else{ $colspan="5";}?>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($arr_cancelled_orders['orders'] as $key=>$arr) { ?>
						<tr>
							<?php if (!User::isUserActive()) {?>
								<td><?php echo '#' . $arr['task_ref_id']; ?></td>
							<?php }else {?>
								<?php if ($arr['task_status'] == 2 || $arr['task_status'] == 3 || ($arr['task_status'] == 4 && $arr['task_writer_id'] > 0)) { ?>
									<td><a href="<?php echo generateUrl('task', 'order_process', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
								<?php 
									$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_process', array($arr['task_id'])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>';
								} else { ?>
									<td><a href="<?php echo generateUrl('task', 'order_bids', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
								<?php
									$action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_bids', array($arr['task_id'])).'" class="theme-btn">View Order</a></td>';
								} ?>
							<?php }?>
							<td><?php echo $arr['task_topic']; ?>	</td>
							<td><?php echo $arr['paper_type']; ?></td>
							<td><?php echo $arr['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?> <br><small>(<?php echo $arr['task_pages']*CONF_WORDS_PER_PAGE;?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo displayDate($arr['task_due_date'],true, true,CONF_TIMEZONE); ?><br><?php 
							print_time_zone()							
							 ?><br><small>(<?php echo html_entity_decode($arr['time_left']); ?>)</small></td>
							<!--<td><?php echo $arr['total_bids']; ?></td>-->
							<?php echo $action;?>
						</tr> 
					<?php } ?>
					<?php if (count($arr_cancelled_orders['orders']) == 0) echo '<tr><td colspan="'.$colspan.'"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo generatePagingStringHtml($arr_cancelled_orders['page'],$arr_cancelled_orders['pages'],"bids('cancelled',xxpagexx);"); ?></div>
		</div>
	</div>
	<div class="user-list" id="tab5">
		<h2><?php echo Utilities::getLabel( 'L_Invites_Sent' ); ?></h2>
		<div class="white-box">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="invite">
					<thead>
						<tr>
							<th><?php echo Utilities::getLabel( 'L_Order_No' ); ?></th>
							<th width="20%"><?php echo Utilities::getLabel( 'L_Topic' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Pages_words' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Deadline' ); ?></th>
							<th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
							<th style="text-align:center;" colspan="2"><?php echo Utilities::getLabel( 'L_Action' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($arr_invites['orders'] as $key=>$arr) { ?>
						<tr>
							<?php if (!User::isUserActive()) {?>
								<td><?php echo '#' . $arr['task_ref_id']; ?></td>
							<?php }else {?>
								<?php if ($arr['task_status'] != 2 && $arr['task_status'] != 3) { ?>
									<td><a href="<?php echo generateUrl('task', 'order_bids', array($arr['task_id'])); ?>"><?php echo '#' . $arr['task_ref_id']; ?></a></td>
								<?php $action = '<td class="dataTable"><a href="'.generateUrl('task', 'order_bids', array($arr['task_id'])).'" class="theme-btn">' . Utilities::getLabel( 'L_View_Order' ) . '</a></td>'; }?>
							<?php }?>
							<td><?php echo $arr['task_topic']; ?></td>
							<td>
								<?php if ($arr['task_status'] == 0) { ?>
									<span class="orgtag"><?php echo $order_status[$arr['task_status']]; ?></span>
								<?php } else { ?>
									<span class="<?php if($arr['inv_status']==0){echo 'orgtag';}else if($arr['inv_status'] == 1){echo 'greentag';}else if($arr['inv_status'] == 2){echo 'redtag';} ?>"><?php echo $invitation_status[$arr['inv_status']]; ?></span>
								<?php } ?>
							</td>
							<td><?php echo $arr['paper_type']; ?></td>
							<td><?php echo $arr['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?>  <br><small>(<?php echo $words = $arr['task_pages']*CONF_WORDS_PER_PAGE;?> <?php echo Utilities::getLabel( 'L_words' ); ?>)</small></td>
							<td><?php echo displayDate($arr['task_due_date'],true, true, CONF_TIMEZONE); ?> <br><?php 
							print_time_zone()							
							 ?><br><small  <?php if(strtotime($arr['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>(<?php echo html_entity_decode($arr['time_left']); ?>)</small></td>
							<td><?php echo $arr['writer']; ?></a></td>
							<?php if ($arr['task_status'] == 0 || $arr['task_status'] == 1) { ?>
								<td class="dataTable"><a href="<?php echo generateUrl('task', 'order_bids', array($arr['task_id'])); ?>" class="theme-btn"><?php echo Utilities::getLabel( 'L_View_Order' ); ?></a></td>
								<td class="dataTable"><a href="javascript:void(0);" class="theme-btn" onclick="makeTaskPublic($(this),<?php echo $arr['task_id']; ?>);"><?php echo Utilities::getLabel( 'L_Make_public' ); ?></a></td>
							<?php } else { ?>
								<td>-</td>
							<?php }  ?>
							
						</tr>
					<?php } ?>
					<?php if ( count( $arr_invites['orders'] ) == 0 ) echo '<tr><td colspan="8"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Order_Found' ) . '</p></div></div></td></tr>'; ?>
					</tbody>
				</table>
			</div>
			<div class="pagination"><?php echo generatePagingStringHtml($arr_invites['page'],$arr_invites['pages'],"invites(xxpagexx);"); ?></div>
		</div>
	</div>
