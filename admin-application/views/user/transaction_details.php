<?php 
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access. 
//echo '<pre>' . print_r($total_records,true) . '</pre>';exit;
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('user','user_listing');?>">User Management</a></li>
		<li>User Wallet Transactions</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title">
		<h2>User Wallet Transactions</h2>
		<a class="button green" href="<?php echo generateUrl('transactions', 'add_transaction', array($user_id)); ?>">Add wallet transaction</a>
	</div>
	<section class="box">
		<div class="box_head"><h3>Wallet Transactions List</h3> </div>
		<div class="box_content clearfix">
			<?php	
				
				$arr_flds = array(
				'listserial'=>'S.No.',
				'wtrx_date'=>'Date',
				'particulars'=>'Particulars',
				'debit'=>'Debit',
				'credit'=>'Credit',
				'balance'=>'Balance',
				'status'=>'Status',
				'wtrx_mode'=>'Mode'
				);

				//if ($write_permission) $arr_flds['action'] = 'Action';

				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {
					
					
					$e = $th->appendElement('th', array(), $val);
					
					
				}

				foreach ($arr_transactions as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'wtrx_date':
								$td->appendElement('plaintext', array(), $row['wtrx_date'], true);
								break;
							case 'particulars':								
								if ($row['wtrx_mode'] == 0) {
									if ($row['wtrx_amount'] < 0) {
										$td->appendElement('p', array(), 'Funds Paid for OrderID: ' . '<a href="' . generateUrl('orders', 'order_details', array($row['wtrx_task_id'])) . '"><u>#' . $row['task_ref_id'] . '</u></a>', true);
									}
									else {
										$td->appendElement('p', array(), 'Funds Received for Order ID: ' . '<a href="' . generateUrl('orders', 'order_details', array($row['wtrx_task_id'])) . '"><u>#' . $row['task_ref_id'] . '</u></a>', true);
									}
								}
								else if ($row['wtrx_mode'] == 1) {
									if ($row['wtrx_amount'] > 0) {
										$td->appendElement('p', array(), 'Funds Deposited, Transaction ID: ' . $row['trans_gateway_transaction_id'], true);
									}
									else {
										$td->appendElement('p', array(), 'Debit', true);
									}
								}
								else if ($row['wtrx_mode'] == 2) {									
									if ($row['req_status'] == '') { ?>
									<input type="hidden" name="comment" id='comment_<?php echo $sn+$start_record ?>' value='<?php echo addslashes($row['wtrx_comments']);?>'>
									<?php 
									$no = $sn+$start_record;
										$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>"view_comment($no)"), 'View comments', true);
										/* $td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'$.facebox(\'<div class="popup_msg information">' . addslashes($row['wtrx_comments']) . '</div>\');'), 'View comments', true); */
									}
									else {
										if ($row['wtrx_amount'] < 0) $td->appendElement('p', array(), 'Funds Withdrawn, Transaction ID: ' . $row['req_transaction_id'], true);
									}
								}
								break;
							case 'debit':								
								$td->appendElement('p', array(), $row['wtrx_amount'] < 0 ? CONF_CURRENCY . number_format(abs($row['wtrx_amount']),2) : '-', true);								
								break;
							case 'credit':								
								$td->appendElement('p', array(), $row['wtrx_amount'] < 0 ? '-' : CONF_CURRENCY . number_format($row['wtrx_amount'],2), true);								
								break;
							case 'balance':								
								$td->appendElement('p', array(), CONF_CURRENCY . number_format($row['wtrx_balance'],2), true);								
								break;
							case 'status':								
								if ($row['wtrx_cancelled'] == 1) {
									$td->appendElement('a', array('class'=>'button small red'), 'Cancelled', true);
								}
								else {
									if ($row['wtrx_mode'] == 0) {
										$td->appendElement('a', array('class'=>'button small green'), 'Completed', true);
									}
									else if ($row['wtrx_mode'] == 1) {
										if ($row['trans_status'] == 0) {
											$td->appendElement('a', array('class'=>'button small red'), 'Failed', true);
										}
										else {
											$td->appendElement('a', array('class'=>'button small green'), 'Completed', true);
										}
									}
									else {
										if ($row['req_status'] == '') {
											$td->appendElement('a', array('class'=>'button small green'), 'Completed', true);
										}
										else {
											if ($row['req_status'] == 1) {
												$td->appendElement('a', array('class'=>'button small green'), 'Completed', true);
											}
											else {
												$td->appendElement('a', array('class'=>'button small red'), 'Denied', true);
											}
										}
									}
								}
								
								break;
							case 'wtrx_mode':
									if ($row['wtrx_mode'] == 0) {
										$td->appendElement('plaintext', array(), 'Wallet', true);
									} else if ($row['wtrx_mode'] == 1) {
										$td->appendElement('plaintext', array(), 'PayPal', true);
									} else {
										$td->appendElement('plaintext', array(), 'System', true);
									}
									
								break;
							
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}

				if (count($arr_transactions) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				
			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
		</div>
	</section>
<section>
<script>
function view_comment(id)
{
	
	 var comment = $('#comment_'+id).val();	 
	 
	$.facebox('<div class="popup_msg information">'+comment+'</div>'); 
}
</script>