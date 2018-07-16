<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$post = Syspage::getPostedVar();
global $withdrawal_request_status;
//echo "<pre>".print_r($arr_transaction,true)."</pre>";exit;
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('money');?>">Money Management</a></li>
		<li>Total Earning - 24 Hrs</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Total Earning - 24 Hrs : <span><?php if($today_earned >= 0) {echo CONF_CURRENCY . $today_earned;}else {echo "-". CONF_CURRENCY . abs($today_earned);}?></span></h2></div>
	
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Request By</h3> </div><span class="toggles"></span>-->
		<div class="box_content clearfix">
		<?php
			$frm->fill($post);
			$frm->setFieldsPerRow(2);;
			$frm->captionInSameCell(true);
			echo $frm->getFormHTML();
		?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Commission List</h3></div>
		<div class="box_content clearfix">
			<?php
				$arr_flds = array(
					'listserial'=>'S.No.',
					'task_ref_id'=>'Order Id',
					'customer_name'=>'Customer Name',
					'writer_name'=>'Writer Name',
					'me_cust_trans'=>'Paid By Customer',
					'me_writer_recv'=>'Writer\'s Amount',
					'system_amount'=>'Amount Earned',
					'earn_date'=>'Date'
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
							case 'task_ref_id':
								$td->appendElement('a', array('href'=>generateUrl('orders','order_details',array($row['task_id']))), '#'. $row['task_ref_id'], true);
								break;
							case 'customer_name':
								$td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['customer_id']))), $row['customer_name'], true);
								break;
							case 'writer_name':
								$td->appendElement('a', array('href'=>generateUrl('user','preview_writer_profile',array($row['writer_id']))), $row['writer_name'], true);
								break;								
							case 'system_amount':
								$td->appendElement('plaintext', array(), CONF_CURRENCY . $row['system_amount'], true);
								break;	
							case 'earn_date':
								$td->appendElement('plaintext', array(), displayDate($row['earn_date'], true, true, CONF_TIMEZONE), true);
								break;	
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing['data']) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				
			?>
			
			<div class="paginationwrap"><?php echo generateNewPagingStringHtml($arr_listing['page'],$arr_listing['pages'],"earn('commission',xxpagexx);",'',$arr_listing['total_records'],$arr_listing['pagesize']);?><?php// echo generateBackendPagingStringHtml($arr_listing['pagesize'],$arr_listing['total_records'],$arr_listing['page'],$arr_listing['pages']);?></div>
			
			
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Admin's Transaction History</h3></div>
		<div class="box_content clearfix">
			<?php
				$arr_flds = array(
					'listserial'=>'S.No.',
					'user_name' => 'Username',
					'wtrx_comments'=>'Notes',
					'debit'=>'Debit',
					'credit'=>'Credit',
					'trans_date'=>'Transaction Date'
				);
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {
					$e = $th->appendElement('th', array(), $val);
				}
				
				foreach ($arr_transaction['data'] as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					$start_record = (($arr_listing['page'] - 1)*$arr_listing['pagesize']) + 1;
					
					foreach ($arr_flds as $key=>$val){
						$td = $tr->appendElement('td');
						switch ($key){
							case 'listserial':
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'user_name':
								$td->appendElement('plaintext', array(), $row['user_name'], true);
								break;
							case 'wtrx_comments':
								$td->appendElement('plaintext', array(), $row['wtrx_comments'], true);
								break;
							case 'debit':
								$td->appendElement('plaintext', array(), $row['wtrx_amount'] < 0 ? CONF_CURRENCY . number_format(abs($row['wtrx_amount']),2) : '-', true);
								break;	
							case 'credit':
								$td->appendElement('plaintext', array(), $row['wtrx_amount'] < 0 ? '-' : CONF_CURRENCY . number_format($row['wtrx_amount'],2), true);
								break;									
							case 'trans_date':
								$td->appendElement('plaintext', array(), displayDate($row['trans_date'], true, true, CONF_TIMEZONE), true);
								break;	
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_transaction['data']) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				
			?>
			
			<div class="paginationwrap"><?php echo generateNewPagingStringHtml($arr_transaction['page'],$arr_transaction['pages'],"earn('transaction',xxpagexx);",'',$arr_transaction['total_records'],$arr_transaction['pagesize']);?><?php// echo generateBackendPagingStringHtml($arr_transaction['pagesize'],$arr_transaction['total_records'],$arr_transaction['page'],$arr_transaction['pages']);?></div>
			
			
		</div>
	</section>
</section>