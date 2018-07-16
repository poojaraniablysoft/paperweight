<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $withdrawal_request_status;
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Money Management</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Money Management</h2></div>
	
	
	
	<section class="section_threeCols">
		<h3>Transaction Summary </h3>
		<div>
			<aside class="col">
				<h5>Total Credit</h5><a href="<?php echo generateUrl('money','total_money_credit');?>"><span class="numcount"><?php echo CONF_CURRENCY . number_format($tc,2);?></span></a>	
				<p class="desc_text">Amount Loaded from Paypal to System</p>
			</aside>			
			<aside class="col">
				<h5>Total Money Paid</h5><a href="<?php echo generateUrl('money','total_money_paid');?>"><span class="numcount"><?php echo CONF_CURRENCY . number_format($total_paid,2);?></span></a>
				<p class="desc_text">Amount Paid to writer and user as withdrawal approved </p>
			</aside>
			<aside class="col">
				<h5>Total Earnings</h5><a href="<?php echo generateUrl('money','total_money_earn');?>"><span class="numcount"><?php if($total_earned >= 0) {echo CONF_CURRENCY . number_format($total_earned,2);}else {echo "-". CONF_CURRENCY . number_format(abs($total_earned),2);}?></span></a>
				<p class="desc_text">Total Commission Earned</p>
			</aside>
			<aside class="col">
				<h5>Reserved Amount</h5><a href="<?php echo generateUrl('money','total_money_reserved');?>"><span class="numcount"><?php if($total_reserved >= 0) {echo CONF_CURRENCY . number_format($total_reserved,2);}else {echo "-". CONF_CURRENCY . number_format(abs($total_reserved),2);}?></span></a>
				<p class="desc_text">Total Reserved Amount</p>
			</aside>
		</div>
	</section>
	<span class="gap"></span><span class="gap"></span>
	<span class="gap"></span><span class="gap"></span>
	<section class="section_threeCols">
		<h3>Transaction Summary - Last 24 Hrs  </h3>
		<div>
			<aside class="col">
				<h5>Total Credit</h5><a href="<?php echo generateUrl('money','money_credit_current');?>"><span class="numcount"><?php echo CONF_CURRENCY . number_format($today_credit,2);?></span></a>
				<p class="desc_text">Amount Loaded from Paypal to System</p>
			</aside>		
			<aside class="col">
				<h5>Total Money Paid</h5><a href="<?php echo generateUrl('money','money_paid_current');?>"><span class="numcount"><?php echo CONF_CURRENCY . number_format($today_paid,2);?></span></a>
				<p class="desc_text">Amount Paid to writer and user as withdrawal approved </p>
			</aside>
			<aside class="col">
				<h5>Total Earnings</h5><a href="<?php echo generateUrl('money','money_earn_current');?>"><span class="numcount"><?php if($today_earned >= 0){ echo CONF_CURRENCY . number_format($today_earned,2);}else{echo "-". CONF_CURRENCY . number_format(abs($today_earned),2);}?></span></a>
				<p class="desc_text">Total Commission Earned</p>
			</aside>
			<aside class="col">
				<h5>Reserved Amount</h5><a href="<?php echo generateUrl('money','today_money_reserved');?>"><span class="numcount"><?php if($today_reserved >= 0) {echo CONF_CURRENCY . number_format($today_reserved,2);}else {echo "-". CONF_CURRENCY . number_format(abs($today_reserved),2);}?></span></a>
				<p class="desc_text">Total Reserved Amount</p>
			</aside>
		</div>
	</section>
	<span class="gap"></span><span class="gap"></span>
	<span class="gap"></span><span class="gap"></span>
	<section class="box">
		<div class="box_head"><h3>Withdrawal Requests - Pending Approval</h3></div>
		<div class="box_content clearfix toggle_container">
			<?php
				/* if ($arr_listing['pages'] > 1) {
					$paging = new HtmlElement('table',array('width'=>'100%'));
					$tr = $paging->appendElement('tr');
					$tr->appendElement('td','',generateBackendPagingStringHtml($arr_listing['pagesize'],$arr_listing['total_records'],$arr_listing['page'],$arr_listing['pages']),true);
					echo $paging->getHtml();
				} */
				
				$arr_flds = array(
					'listserial'=>'S.No.',
					'user_screen_name'=>'User',
					'amount_requested'=>'Amount requested',
					'request_date'=>'Date',
					'status'=>'Status',
					'action'=>'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {
					$e = $th->appendElement('th', array(), $val);
				}
				
				foreach ($arr_listing['data'] as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					$start_record = (($arr_listing['page'] - 1)*$arr_listing['pagesize']) + 1;
					
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'amount_requested':
								$td->appendElement('plaintext', array(), CONF_CURRENCY . $row['req_amount'], true);
								break;
							case 'request_date':
								$td->appendElement('plaintext', array(), displayDate($row['req_date'], true, true, CONF_TIMEZONE), true);
								break;
							case 'status':
								if($row['req_status']==1) {
									$status = 'textgreen';
								}
								elseif($row['req_status']==0 || $row['req_status']==4) {
									$status = 'textyellow';
								}
								elseif($row['req_status']==2 || $row['req_status']==3) {
									$status = 'textred';
								}
								$td->appendElement('a', array('class'=>$status), $withdrawal_request_status[$row['req_status']], true);
								break;
							case 'action':
								$req_details = preg_replace('/[\r\n]+/', '<br/>', $row['req_details']);
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'$.facebox(\'<div class="popup_msg information">' . $req_details . '</div>\');', 'data-req-id'=>$row['req_id'], 'title'=>'View Details', 'class'=>'button small black'), createButton('View details'), true);
								
								//$td->appendElement('plaintext', array(), ' | ', true);
								$btn_val = $row['req_status'] == 0 ? 'Update' : 'View transaction summary';
								$td->appendElement('a', array('href'=>generateUrl('transactions', 'update', array($row['req_id'])), 'class'=>'button small green', 'title'=>$btn_val), createButton($btn_val), true);
								break;
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing['data']) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
			if ($arr_listing['pages'] > 1) {
			?>
			
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($arr_listing['pagesize'],$arr_listing['total_records'],$arr_listing['page'],$arr_listing['pages']);?></div>
			<?php }?>
		</div>
	</section>
	
	
</section>