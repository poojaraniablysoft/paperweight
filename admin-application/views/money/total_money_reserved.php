<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$post = Syspage::getPostedVar();
global $withdrawal_request_status;
global $order_status;

//echo "<pre>".print_r($order_status[4],true)."</pre>";exit;
?>
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('money');?>">Money Management</a></li>
		<li>Total Reserved Amount</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Total Reserved Amount : <span><?php echo CONF_CURRENCY.$total_reserved;?></span></h2></div>
	
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Request By</h3> </div><span class="toggles"></span>-->
		<div class="box_content clearfix">
		<?php
			$frm->fill($post);
			$frm->setFieldsPerRow(3);;
			$frm->captionInSameCell(true);
			echo $frm->getFormHTML();
		?>
		</div>
	</section>
	<section class="box">
		<div class="box_head"><h3>Reserved Amount details List</h3></div>
		<div class="box_content clearfix">
			<?php
				$arr_flds = array(
					'listserial'=>'S.No.',
					'res_task_id'=>'Order Id',
					'user_name'=>'Customer Name',
					//'wtrx_comments'=>'Comments',
					'reserved_amount'=>'Reserved Amount',
					'task_status'=>'Order Status',
					'reserve_date'=>'Date',
					'Action'=>'Action'
					
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
							case 'res_task_id':
								$td->appendElement('a', array('href'=>generateUrl('orders','order_details',array($row['task_id']))), ($row['task_ref_id'] != null) ? '#'. $row['task_ref_id'] : '--', true);
								break;
							case 'user_name':
								$td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['user_id']))), $row['user_name'], true);
								break;
							case 'wtrx_comments':
								$td->appendElement('plaintext', array(), ($row['wtrx_comments'] != null) ? $row['wtrx_comments']:'--', true);
								break;								
							case 'reserved_amount':
								$td->appendElement('plaintext', array(), CONF_CURRENCY . $row['reserved_amount'], true);
								break;									
							case 'task_status':
								$td->appendElement('plaintext', array(), $order_status[$row['task_status']], true);
								break;	
							case 'reserve_date':
								$td->appendElement('plaintext', array(), displayDate($row['reserve_date'], true, true, CONF_TIMEZONE), true);
								break;
							case 'Action':
								
								if($row['task_status'] == 4 && $row['reserved_amount'] != 0) {
									$td->appendElement('a', array('class'=>'button small black','onclick'=>'if(confirm("Do you really want to transfer amount to earnings?")) {return true;}else{return false;}','href'=>generateUrl('transactions','reserve_earnings',array($row['res_task_id'],$row['task_user_id']))),'Transfer to Earnings',true);
								}else {
									$td->appendElement('plaintext',array(),'--');
								}
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing['data']) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				
			?>
			
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($arr_listing['pagesize'],$arr_listing['total_records'],$arr_listing['page'],$arr_listing['pages']);?></div>
			
			
		</div>
	</section>
</section>