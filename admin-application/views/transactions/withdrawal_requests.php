<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

global $withdrawal_request_status;
//echo '<pre>' . print_r($arr_listing, true) . '</pre>';
?>

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Fund Withdrawal Requests</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2>Funds Withdrawal Requests</h2></div>
	
	<section class="box">
		<!--<div class="box_head"><h3>Search Request By</h3> </div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
		<?php
			global $post;
			$frmSearch->fill(array('keyword'=>$post['keyword'],'req_status'=>$post['req_status']));
			$frmSearch->setFieldsPerRow(3);
			$frmSearch->captionInSameCell(true);
			echo $frmSearch->getFormHtml();
		?>
		</div>
	</section>
	
	<section class="box">
		<div class="box_head"><h3>Withdrawal Requests List</h3></div>
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
					'user_screen_name'=>'User (User Email Id)',
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
							case 'user_screen_name':
								$td->appendElement('plaintext', array(), $row['user_screen_name'].' ('.$row['user_email'].')',true);
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
