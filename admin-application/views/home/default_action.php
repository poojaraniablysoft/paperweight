<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;

global $withdrawal_request_status;

//$st = array('0'=>'Pending Approval','1'=>'Bidding','2'=>'In Progress','3'=>'Finished','4'=>'Cancelled');
?>
<script type="text/javascript">
var order_status = <?php echo convertToJson($order_status);?>;
//$.facebox(order_status[0]);
</script>
 <!--rightPanel start here-->    
<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Dashboard</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Dashboard </h2></div>
	
	<div class="fullrow">
		<div class="borderBox greenbox">
			<div class="grid_1"><img src="<?php echo CONF_WEBROOT_URL;?>images/front_icon1.png" alt=""></div>
			<div class="grid_2">
				<h3><?php echo $completed['completeorders'];?><span>Orders completed</span></h3>
			</div>
		</div>
		
		<div class="borderBox orgbox">
			<div class="grid_1"><img src="<?php echo CONF_WEBROOT_URL;?>images/front_icon2.png" alt=""></div>
			<div class="grid_2">
				<h3><?php echo $active['active'];?> <span>Writers active</span></h3>
			</div>
		</div>
		
		<div class="borderBox yellowbox">
			<div class="grid_1"><img src="<?php echo CONF_WEBROOT_URL;?>images/front_icon5.png" alt=""></div>
			<div class="grid_2">
				<h3><?php echo $online;?> <span>Writers Online Now</span></h3>
			</div>
		</div>
		
		<div class="borderBox bluebox">
			<div class="grid_1"><img src="<?php echo CONF_WEBROOT_URL;?>images/front_icon3.png" alt=""></div>
			<div class="grid_2">
				<h3><?php echo $registered['registered'];?> <span>Registered Users</span></h3>
			</div>
		</div>
		
	</div>  
	
	<div class="gap"></div>
	
	<section class="box" id="orders_listing">
		<div class="box_head"><a class="button small red right" href="<?php echo generateUrl('orders');?>">View All</a><h3>ORDERS AWAITING APPROVAL</h3></div>
		<div class="box_content clearfix">
			<?php		
				$arr_flds = array(
				'listserial'=>'S.No.',
				'task_ref_id'=>'Order ID',
				'task_topic'=>'Orders Topic',
				//'user_screen_name'=>'Customer Name',
				'task_due_date'=>'Deadline Date',
				'task_is_approved'=>'Approved By Admin',
				'task_status'=>'Status',
				'action' => 'Action'
				);
				
				$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
				$th = $tbl->appendElement('thead')->appendElement('tr');
				foreach ($arr_flds as $val) {				
					$e = $th->appendElement('th', array(), $val);			
				}
				$sn = 1;
				foreach ($order_listing as $row){
					$tr = $tbl->appendElement('tr');
					if ($row['task_status'] == 0) $tr->addValueToAttribute('class', 'inactive');
					
					foreach ($arr_flds as $key=>$val){
						
						switch ($key){
							case 'listserial':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $sn++);
								break;
							case 'task_topic':
								$td = $tr->appendElement('td', array('width'=>'20%'));
								$td->appendElement('plaintext', array(), $row['task_topic'] , true);
								break;
							case 'user_screen_name':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $row['user_screen_name'] , true);
								break;
							case 'task_due_date':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), displayDate($row['task_due_date'],true,true,CONF_TIMEZONE) , true);
								break;
							case 'task_ref_id':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), '#'.$row['task_ref_id'] , true);
								break;
							case 'task_is_approved':
								$td = $tr->appendElement('td');
								if($row['task_status']==0){
									$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'verified_'. $row['task_id'], 'onclick'=>'updateOrderVerificationStatus(' . $row['task_id'] . ', $(this))', 'title'=>'Toggle Order verification status','class'=>'toggleswitch actives'), '', true);			
								}else {
									$td->appendElement('a',array('class'=>'toggleswitch'),'',true);
								}
								break;
							case 'task_status':
								$td = $tr->appendElement('td');
								if($row['task_status']==1) {
									$status = 'textyellow';
								}
								elseif($row['task_status']==0 || $row['task_status']==4) {
									$status = 'textred';
								}
								elseif($row['task_status']==2 || $row['task_status']==3) {
									$status = 'textgreen';
								}
								
								
								$td->appendElement('span', array('class'=>$status), $order_status[$row['task_status']] , true);
								break;
								
							case 'action':	
								$td = $tr->appendElement('td');

								//$td->appendElement('a', array('href'=>'javascript:void(0);', 'onclick'=>'orderdetails(' . $row['task_id'] . ')', 'class'=>'opt-button', 'title'=>'Preview'), createButton('Preview').' | ', true);
								$td->appendElement('a', array('href'=>generateUrl('orders','order_details',array($row['task_id'])),'title'=>'Preview','class'=>'button small black'), 'Preview', true);
								
								$td->appendElement('a', array('href'=>generateUrl('orders','bids',array($row['task_id'])),'title'=>'Bids Listing','class'=>'button small green'), 'Bids', true);
													
								break;
							default:
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($order_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
			?>
		</div>
	</section>
	
	<div class="gap"></div>
	
	<section class="box">
		<div class="box_head"><a class="button small red right" href="<?php echo generateUrl('transactions','withdrawal_requests');?>">View All</a><h3>PENDING WITHDRAWAL REQUEST </h3></div>
		<div class="box_content clearfix">
		<?php 
			$arr_flds = array(
			'listserial'=>'S.No.',
			'user_screen_name'=>'User',
			'amount_requested'=>'Amount',
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
							$td->appendElement('plaintext', array(), ($row['user_first_name'] != '' && User::isWriter($row['user_id']) == false)?$row['user_first_name'].' '.$row['user_last_name']:$row['user_screen_name'], true);
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
			
			if (count($arr_listing['data']) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No Pending Request Found');

			echo $tbl->getHtml();
		?>
		</div>
	</section>
	
	<div class="gap"></div>
	
	<section class="box" id="arrTestTaker">
		<div class="box_head"><a class="button small red right" href="<?php echo generateUrl('user','user_listing');?>">View All</a><h3>USERS AWAITING GRAMMAR TEST APPROVAL</h3></div>
		<div class="box_content clearfix">  
		<?php	
		
			$arr_flds = array(
			'listserial'=>'S.No.',
			//'user_name'=>'Name',
			'user_email'=>'Email',
			//'user_type'=>'User Type',
			//'user_is_featured'=>'Is User Featured',
			'user_active'=>'User Status',
			'user_email_verified'=>'Is Email Verified',
			'user_verified_by_admin'=>'Verified by Admin',
			'action' => 'Action'
			);

			//if ($write_permission) $arr_flds['action'] = 'Action';

			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {					
				$e = $th->appendElement('th', array(), $val);					
			}
			//echo "<pre>".print_r($arrTestTaker,true)."<pre>";
			foreach ($arrTestTaker as $sn=>$row){
				$tr = $tbl->appendElement('tr');
				if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sn+1);
							break;
						case 'user_name':
							$td->appendElement('plaintext', array(), $row['user_first_name'] . ' ' . $row['user_last_name'], true);
							break;
						case 'user_type':
							if ($row['user_type'] == 0) {
								
								$td->appendElement('p', array('id'=>'ml_'.$row['user_id']), 'Customer' . ' ', true);
								
							}
							
							if ($row['user_type'] == 1) {
								
								$td->appendElement('p', array('id'=>'ml_'.$row['user_id']),'Writer'. ' ', true);
								
							}
						
							break;
						
						case 'user_active':
								$ul=$td->appendElement('ul',array('class'=>'iconbtns'));
								$li=$ul->appendElement('li');
								
								$img = $row['user_active'] == 1 ? '<img src="' . CONF_WEBROOT_URL . 'images/actives.png" alt="" class="whiteicon active_icon">' : '<img src="' . CONF_WEBROOT_URL . 'images/inactives.png" alt="" class="whiteicon inactive_icon">';
								
								$a = $li->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $row['user_id'], 'onclick'=>'updateUserStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle featured status'),$img, true);
								
								
								
								
								
								
							
							break;
							case 'user_is_featured':
								
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $row['user_id'], 'onclick'=>'markFeatured(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle featured status','class'=>($row['user_is_featured']==1? 'toggleswitch':'toggleswitch actives')));
							
							break;
						case 'user_email_verified':
							if ($row['user_type']==1) {
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'verified_'. $row['user_id'], 'onclick'=>'updateEmailVerificationStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle email verification status'), (($row[$key]==1)?'<a class="toggleswitch"></a>':'<a class="toggleswitch actives"></a>'), true);
								}
								else {
									$td->appendElement('a', array(), '--', true);
								}
							break;
						case 'user_verified_by_admin':
							if($row['user_is_approved']!=1){
								$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'verified_'. $row['user_id'], 'onclick'=>'updateUserVerificationStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle User verification status','class'=>'toggleswitch actives'),'', true);			
							}else {
								$td->appendElement('a',array('class'=>'toggleswitch'),'',true);
							}
														
							break;
						case 'action':				
														
							if ($row['user_type'] == 1) $td->appendElement('a', array('href'=>generateUrl('user','preview_writer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							
							if ($row['user_type'] == 0) $td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							
							
						
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
			}

			if (count($arrTestTaker) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
		?>
	</div>
	</section>
	
	<div class="gap"></div>
	
	<section class="box" id="arruserdelete">
		<div class="box_head"><a class="button small red right" href="<?php echo generateUrl('user','user_listing');?>">View All</a><h3>ACCOUNTS DEACTIVATED BY USERS</h3></div>
		<div class="box_content clearfix">
		<?php	
		
			$arr_flds = array(
			'listserial'=>'S.No.',
			//'user_name'=>'Name',
			'user_email'=>'Email',
			'user_type'=>'User Type',
			'user_active'=>'Mark Inactive',
			'action' => 'Action'
			);

			//if ($write_permission) $arr_flds['action'] = 'Action';

			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {					
				$e = $th->appendElement('th', array(), $val);					
			}
			//echo "<pre>".print_r($arruserdelete,true)."<pre>";
			foreach ($arruserdelete as $sn=>$row){
				$tr = $tbl->appendElement('tr');
				if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sn+1);
							break;
						case 'user_name':
							$td->appendElement('plaintext', array(), $row['user_first_name'] . ' ' . $row['user_last_name'], true);
							break;
						case 'user_type':
							if ($row['user_type'] == 0) {
								
								$td->appendElement('p', array('id'=>'ml_'.$row['user_id']), 'Customer' . ' ', true);
								
							}
							
							if ($row['user_type'] == 1) {
								
								$td->appendElement('p', array('id'=>'ml_'.$row['user_id']),'Writer'. ' ', true);
								
							}
						
							break;
						
						case 'user_active':
								$ul=$td->appendElement('ul',array('class'=>'iconbtns'));
								$li=$ul->appendElement('li');
								
								$img = $row['user_active'] == 1 ? '<img src="' . CONF_WEBROOT_URL . 'images/actives.png" alt="" class="whiteicon active_icon">' : '<img src="' . CONF_WEBROOT_URL . 'images/inactives.png" alt="" class="whiteicon inactive_icon">';
								
								$a = $li->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $row['user_id'], 'onclick'=>'updateUserStatus(' . $row['user_id'] . ', $(this))', 'title'=>'Toggle featured status'),$img, true);
							break;
							
						case 'action':		
								if ($row['user_type'] == 1) $td->appendElement('a', array('href'=>generateUrl('user','preview_writer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							
								if ($row['user_type'] == 0) $td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
			}

			if (count($arruserdelete) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
		?>
	</div>
	</section>
	
	<div class="gap"></div>
	
	<section class="box" id="arrOrderCancelReq">
		<div class="box_head"><h3>PENDING CANCELLATION REQUESTS</h3></div>
		<div class="box_content clearfix">
		<?php	
		
			$arr_flds = array(
			'listserial'=>'S.No.',
			'task_ref_id'=>'Order ID',
			'task_topic'=>'Order Topic',
			'task_due_date'=>'Deadline',
			'task_posted_on'=>'Posted On',
			'action' => 'Action'
			);

			//if ($write_permission) $arr_flds['action'] = 'Action';

			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'dataTable'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {					
				$e = $th->appendElement('th', array(), $val);					
			}
			//echo "<pre>".print_r($arrOrderCancelReq,true)."</pre>";exit;
			foreach ($arrOrderCancelReq as $sn=>$row){
				$tr = $tbl->appendElement('tr');
				//if ($row['user_active'] == 0) $tr->addValueToAttribute('class', 'inactive');
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sn+1);
							break;
						case 'task_ref_id':
							$td->appendElement('a', array('href'=>generateUrl('orders','order_details',array($row['task_id']))), '#'.$row['task_ref_id'], true);
							break;
						case 'task_topic':
							$td->appendElement('plaintext', array(), $row['task_topic'], true);
							break;
						
							
						case 'action':	
							$select = $td->appendElement('select',array('name'=>'task_status','onchange'=>'return changeOrderStatus('.$row['task_id'].',this.value);','id'=>'change_status_'.$row['task_id']));
							foreach($order_status as $key=>$status_arr) {
								$select->appendElement('option',$key==$row['task_status']? array('selected'=>'selected','value'=>$key) : array('value'=>$key),$status_arr,true);
							}
							/* 	if ($row['user_type'] == 1) $td->appendElement('a', array('href'=>generateUrl('user','preview_writer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							
								if ($row['user_type'] == 0) $td->appendElement('a', array('href'=>generateUrl('user','preview_customer_profile',array($row['user_id'])), 'class'=>'button small black', 'title'=>'Details'), createButton('Details'), true);
							break; */
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
			}

			if (count($arrOrderCancelReq) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
		?>
	</div>
	</section>
</section>