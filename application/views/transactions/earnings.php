<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo '<pre>' . print_r($arr_request['data'],true) . '</pre>';
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
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ). '</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>
<div class="earnigs-page">
	<div class="user-list padding-top-none"> 
		<?php echo Message::getHtml(); ?>
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
		  
				  <?php if(count($arr_request['data']) > 0) { 
					foreach($arr_request['data'] as $key=>$val) { ?>
						<tr>
							<td><?php echo $key+1 . '.'; ?></td>									
							<td><?php echo CONF_CURRENCY.$val['req_amount'];?></td>
							<td><?php echo $val['req_date'];?></td>
							<td><?php echo ( $val['req_comments'] != '' ) ? $val['req_comments']:'--';?></td>
							<td><?php if($val['req_status']==0){ echo "<span class='orgtag'>" . Utilities::getLabel( 'L_Pending' ) . "</span>"; } else if($val['req_status']==1){ echo '<span class="greentag">' . Utilities::getLabel( 'L_Approved' ) . '</span>';}else{echo "<span class='redtag'>" . Utilities::getLabel( 'L_Declined' ) . "</span>";}?></td>
						</tr>
					<?php }
				  }else {
					echo '<td colspan="5"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Record_Found' ) . '</p></div></div></td>';
				  } ?>
		  
				</table>
			</div> 
			<div class="pagination"> 
			<?php echo generatePagingStringHtml($arr_request['page'],$arr_request['pages'],"getWithdrawalTransactions(xxpagexx);"); ?>
		</div>
		</div>
	</div>
	<div class="user-list"> 
		<h2><?php echo Utilities::getLabel( 'L_Withdraw_funds' ); ?></h2>
		<div class="white-box">
			<?php if ($available_balance > 0) { ?>
				<div class="blockpayments gray-box">
					<div class="withdrawal-writer">
						<h4><?php echo Utilities::getLabel( 'L_Request_for_withdrawal' ); ?></h4>
						<small><?php echo Utilities::getLabel( 'L_Request_for_withdrawal_Description' ); ?></small>
						<?php if(User::isUserActive()) { ?>
							<div class="innerpart">								
								<?php echo $frm->getformHtml(); ?>
							</div>
						<?php 
							} else {
								echo Utilities::getLabel( 'L_Not_Able_To_Withdrawal_Account_Deactivated' );
							}
						?>						
					</div>
				</div>
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
	<div class="user-list">
		<h2><?php echo Utilities::getLabel( 'L_Transaction_history' ); ?></h2>
		<div class="white-box" id="tab1">
			<div class="table-scroll">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				  <tr>
					<th><?php echo Utilities::getLabel( 'L_S_No' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Date' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Particulars' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Debit' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Credit' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Balance' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
					<th><?php echo Utilities::getLabel( 'L_Mode' ); ?></th>
				  </tr>
				  <?php foreach ($arr_transactions['transactions'] as $key=>$ele) { ?>
				  <tr>
					<td><?php echo $key+1 . '.'; ?></td>
					<td><?php echo $ele['wtrx_date']; ?></td>
					<td>
						<?php
						if ($ele['wtrx_mode'] == 0) {
							if ($ele['wtrx_amount'] < 0) {
								echo Utilities::getLabel( 'L_Funds_Paid_for_OrderID' ) . ': ' . '<a href="' . generateUrl('task', 'order_process', array($ele['wtrx_task_id'])) . '"><u>#' . $ele['task_ref_id'] . '</u></a>';
							}
							else {
								echo Utilities::getLabel( 'L_Funds_Received_for_Order_ID' ) . ': ' . '<a href="' . generateUrl('task', 'order_process', array($ele['wtrx_task_id'])) . '"><u>#' . $ele['task_ref_id'] . '</u></a>';
							}
						}
						else if ($ele['wtrx_mode'] == 1) {
							if ($ele['wtrx_amount'] > 0) {
								echo Utilities::getLabel( 'L_Funds_Deposited_Transaction_ID' ) . ': ' . $ele['trans_gateway_transaction_id'];
							}
							else {
								echo Utilities::getLabel( 'L_Debit' );
							}
						}
						else if ($ele['wtrx_mode'] == 2) {
							if ($ele['req_status'] == '') { ?>
							<input type="hidden" name="comm" id='comment_<?php echo $key+1 ?>' value='<?php echo addslashes($ele['wtrx_comments'])?>'>
							<a href="javascript:void(0);" onclick="viewcomment(<?php echo $key+1 ?>);"><?php echo Utilities::getLabel( 'L_View_comments' ); ?></a>
							<?php }
							else {
								if ($ele['wtrx_amount'] < 0) echo Utilities::getLabel( 'L_Funds_Withdrawn_Transaction_ID' ) . ': ' . $ele['req_transaction_id'];
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
							echo '<span class="redtag">' . Utilities::getLabel( 'L_Cancelled' ) . '</span>';
						}
						else {
							if ($ele['wtrx_mode'] == 0) {
								echo '<span class="greentag">' . Utilities::getLabel( 'L_Completed' ) . '</span>';
							}
							else if ($ele['wtrx_mode'] == 1) {
								if ($ele['trans_status'] == 0) {
									echo '<span class="redtag">' . Utilities::getLabel( 'L_Failed' ) . '</span>';
								}
								else {
									echo '<span class="greentag">' . Utilities::getLabel( 'L_Completed' ) . '</span>';
								}
							}
							else {
								if ($ele['req_status'] == '') {
									echo '<span class="greentag">' . Utilities::getLabel( 'L_Completed' ) . '</span>';
								}
								else {
									if ($ele['req_status'] == 1) {
										echo '<span class="greentag">' . Utilities::getLabel( 'L_Completed' ) . '</span>';
									}
									else {
										echo '<span class="redtag">' . Utilities::getLabel( 'L_Declined' ) . '</span>';
									}
								}
							}
						}
						?>
					</td>
					<td>
						<?php
						if ($ele['wtrx_mode'] == 0) {
							echo Utilities::getLabel( 'L_Wallet' );
						} else if ($ele['wtrx_mode'] == 1) {
							echo Utilities::getLabel( 'L_PayPal' );
						} else {
							echo Utilities::getLabel( 'L_System' );
						}
						?>
					</td>
				  </tr>
				<?php } ?>
				<?php if (count($arr_transactions['transactions']) == 0) echo '<tr><td colspan="8"><div class="no_result"><div class="no_result_text"><p>' . Utilities::getLabel( 'L_No_Record_Found' ) . '</p></div></div></td></tr>'; ?>
				  
				</table>
			</div>
			<div class="pagination"> 
			<?php echo generatePagingStringHtml($arr_transactions['page'],$arr_transactions['pages'],"getWalletTransactions(xxpagexx);"); ?>
		</div>
		</div>
	</div>
</div>
<script>
function viewcomment( id ) { 
	var comment = $( '#comment_' + id ).val( );
	$.facebox( '<div class="popup_msg information">' + comment + '</div>' );
}
</script>


