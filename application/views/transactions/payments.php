<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo '<pre>' . print_r($arr_transactions,true) . '</pre>';
?>

<script type="text/javascript">
	var filter = "<?php echo $filter; ?>";
</script>
<div class="user-list padding-top-none">
	<h2><?php echo Utilities::getLabel( 'L_Fund_Withdrawal_Requests' ); ?></h2>
	<div class="white-box" id="tab2">
		<div class="table-scroll">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th><?php echo Utilities::getLabel( 'L_S_No' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Amount_requested' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Date' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Comments' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
				</tr>
				<?php foreach($arr_request['data'] as $key=>$val) {?>
				<tr>
					<td><?php echo $key+1 . '.'; ?></td>									
					<td><?php echo CONF_CURRENCY.$val['req_amount'];?></td>
					<td><?php echo $val['req_date'];?></td>
					<td><?php echo ($val['req_comments'] != '') ? $val['req_comments']:'--';?></td>
					<td><?php if($val['req_status']==0){echo "<span class='orgtag'>" . Utilities::getLabel( 'L_Pending' ) . "</span>";}else if($val['req_status']==1){echo '<span class="greentag">' . Utilities::getLabel( 'L_Approved' ) . '</span>';}else{echo "<span class='redtag'>" . Utilities::getLabel( 'L_Declined' ) . "</span>";}?></td>
				</tr>
				<?php }?>
				<?php if (count($arr_request['data']) == 0) echo '<tr><td colspan="5">' . Utilities::getLabel( 'L_No_records_to_show_here' ) . '</td></tr>'; ?>
			</table>
		</div>
		<div class="pagination"> 
			<?php echo generatePagingStringHtml($arr_request['page'],$arr_request['pages'],"getWithdrawalTransactions(xxpagexx);"); ?>
		</div>
	</div>
</div>
<div class="funds user-list">
  <div class="white-box clearfix">
	<?php if(!User::isUserReqDeactive()) { ?>
	<div class="grid-4">
	  <div class="gray-box">
		<h2 class="trigger"><i class="icon earnigs-icon"></i><?php echo Utilities::getLabel( 'L_Load_Funds' ); ?></h2>
		
		<div class="blockpayments"><!-- <img class="cards" alt="" src="<?php// echo CONF_WEBROOT_URL;?>images/card_payments.jpg">-->
			<div class="innerpart">
				<?php echo $frm_load->getformHtml(); ?>
			</div>
		</div>
		
	  </div>
	</div>
	<div class="grid-5">
	  <div class="gray-box">
		<h2 class="trigger"> <i class="icon earnigs-icon"></i><?php echo Utilities::getLabel( 'L_Withdraw_Funds' ); ?></h2>
		<div class="blockpayments">
			<div class="withdrawal">
				<h4><?php echo Utilities::getLabel( 'L_Request_for_withdrawal' ); ?></h4>
			  <?php
				if ($available_balance > 0) {
					echo $frm_withdraw->getformTag();
			  ?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%" class="formTable">
					<tr>
						<td><?php echo Utilities::getLabel( 'L_Amount' ); ?><span class="mandatory">*</span></td><td>
						<div class="symbolfield"><span class="currency">$</span>
						<?php echo $frm_withdraw->getFieldHtml('amount');?>
						</div>
					</tr>
					<tr>
						<td><?php echo Utilities::getLabel( 'L_Details' ); ?><span class="mandatory">*</span></td>
						<td><?php 
							$fld = $frm_withdraw->getField('message');
							$frm_withdraw->removeField($fld);
							$fld_msg = $frm_withdraw->addTextArea( Utilities::getLabel( 'L_Details' ),'message','','','row="5"');
							$fld_msg->requirements()->setRequired();
							$fld_msg->requirements()->setCustomErrorMessage( Utilities::getLabel( 'L_Details_are_mandatory' ) );
							echo $frm_withdraw->getFieldHtml('message');
						?></td>
					</tr>
					<tr>
						<td></td>
						<td><small><?php echo Utilities::getLabel( 'L_Include_Wire_Transfer_Details' ); ?></small></td>
					</tr>
					<tr>
						<td></td>
						<td><?php echo $frm_withdraw->getFieldHtml('btn_submit');?></td>
					</tr>
				</table>
				</form><?php echo $frm_withdraw->getExternalJS(); ?>		
						
				<?php } else { ?>
					<div class="no_result fund_withdraw">
						<div class="no_result_icon">
							<img src="<?php echo CONF_WEBROOT_URL;?>images/small-logo.png">
						</div>
						<div class="no_result_text">
							<h5><?php echo Utilities::getLabel( 'L_Insufficient_amount_in_wallet' ); ?></h5>
							<p><?php echo sprintf( Utilities::getLabel( 'L_To_Withdraw_amount_from_your_wallet_you_must_have' ), CONF_CURRENCY . CONF_WITHDRAW_FUNDS_LIMIT ); ?></p>
						</div>
					</div>
				<?php } ?>
				
			</div>
		</div>
	  </div>
	</div>
	<?php }else { ?>
		<div class="user_deactive">You have been requested to deactive your account! Please Contact to Administrator.</div>
	<?php }?>
  </div>
</div>
<div class="user-list">
	<h2>Transaction history</h2>
	<div class="white-box" id="tab1">
		<div class="table-scroll">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<th width="8%">S. No.</th>
					<th>Date</th>
					<th>Particulars</th>
					<th>Debit</th>
					<th>Credit</th>
					<th>Balance</th>
					<th>Status</th>
					<th>Mode</th>
				</tr>
				<?php foreach ($arr_transactions['transactions'] as $key=>$ele) { ?>
				<tr>
					<td><?php echo $key+1 . '.'; ?></td>
					<td><?php echo $ele['wtrx_date']; ?></td>
					<td>
						<?php
						if ($ele['wtrx_mode'] == 0) {
							if ($ele['wtrx_amount'] < 0) {
								echo 'Funds Paid for OrderID: ' . '<a href="' . generateUrl('task', 'order_process', array($ele['wtrx_task_id'])) . '"><u>#' . $ele['task_ref_id'] . '</u></a>';
							}
							else {
								echo 'Funds Received for Order ID: ' . '<a href="' . generateUrl('task', 'order_process', array($ele['wtrx_task_id'])) . '"><u>#' . $ele['task_ref_id'] . '</u></a>';
							}
						}
						else if ($ele['wtrx_mode'] == 1) {
							if ($ele['wtrx_amount'] > 0) {
								echo 'Funds Deposited, Transaction ID: ' . $ele['trans_gateway_transaction_id'];
							}
							else {
								echo 'Debit';
							}
						}
						else if ($ele['wtrx_mode'] == 2) {
							if ($ele['req_status'] == '') {
								echo '<a href="javascript:void(0);" onclick="$.facebox(\'' . addslashes($ele['wtrx_comments']) . '\');">View comments</a>';
							}
							else {
								if ($ele['wtrx_amount'] < 0) echo 'Funds Withdrawn, Transaction ID: ' . $ele['req_transaction_id'];
							}
						}
						?>
					</td>
					<td><?php echo $ele['wtrx_amount'] < 0 ? CONF_CURRENCY . number_format(abs($ele['wtrx_amount']),2) : '-'; ?></td>
					<td><?php echo $ele['wtrx_amount'] < 0 ? '-' : CONF_CURRENCY . number_format($ele['wtrx_amount'],2); ?></td>
					<td><?php echo CONF_CURRENCY . number_format($ele['wtrx_balance'],2); ?></td>
					<td>
						<?php
						if ($ele['wtrx_cancelled'] == 1) {
							echo '<span class="redtag">Cancelled</span>';
						}
						else {
							if ($ele['wtrx_mode'] == 0) {
								echo '<span class="greentag">Completed</span>';
							}
							else if ($ele['wtrx_mode'] == 1) {
								if ($ele['trans_status'] == 0) {
									echo '<span class="redtag">Failed</span>';
								}
								else {
									echo '<span class="greentag">Completed</span>';
								}
							}
							else {
								if ($ele['req_status'] == '') {
									echo '<span class="greentag">Completed</span>';
								}
								else {
									if ($ele['req_status'] == 1) {
										echo '<span class="greentag">Completed</span>';
									}
									else {
										echo '<span class="redtag">Declined</span>';
									}
								}
							}
						}
						?>
					</td>
					<td>
						<?php
						if ($ele['wtrx_mode'] == 0) {
							echo 'Wallet';
						} else if ($ele['wtrx_mode'] == 1) {
							echo 'PayPal';
						} else {
							echo 'System';
						}
						?>
					</td>
				</tr>
				<?php } ?>
				<?php if (count($arr_transactions['transactions']) == 0) echo '<tr><td colspan="8">No records found.</td></tr>'; ?>
			</table>
		</div> 
		<div class="pagination"> 
			<?php echo generatePagingStringHtml($arr_transactions['page'],$arr_transactions['pages'],"getWalletTransactions(xxpagexx);"); ?>
		</div>
	</div>
</div>
