<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;
?>
<!--leftPanel start here-->    
<section class="leftPanel">
	<ul class="leftNavs">
		<li><a href="<?php echo generateUrl('orders');?>" class="selected">Orders Management</a></li>
		<li><a href="<?php echo generateUrl('rating');?>">Ratings & Reviews Management</a></li>		
		<!--<li><a href="<?php// echo generateUrl('update');?>">Manage Order</a></li>		-->
	</ul>
</section>
<!--leftPanel end here--> 

<section class="rightPanel">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li>Orders Management</li>
	</ul>
	<?php echo Message::getHtml();?>
	<div class="title"><h2>Orders Management</h2></div>
	<section class="box search_filter">
		<!--<div class="box_head"><h3>Search Order </h3></div><span class="toggles"></span>-->
		<div class="box_content clearfix toggle_container">
			<?php
				global $post;
				$frmordersSearch->fill(array('keyword'=>$post['keyword'],'task_status'=>$post['task_status']));
				$frmordersSearch->setFieldsPerRow(5);
				$frmordersSearch->captionInSameCell(true);
				echo $frmordersSearch->getFormHtml();
			?>
		</div>
	</section>
	
	<section class="box">
		<div class="box_head"><h3>Order Listing </h3></div>
		<div class="box_content clearfix">	
			<?php
				$arr_flds = array(
				'listserial'=>'S.No.',
				'task_ref_id'=>'Order ID',
				'task_topic'=>'Orders Topic',
				'user_screen_name'=>'Customer Username',
				'task_posted_on'=>'Order Posted Date',
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
				
				foreach ($arr_listing as $sn=>$row){
					$tr = $tbl->appendElement('tr');
					if ($row['task_status'] == 0) $tr->addValueToAttribute('class', 'inactive');
					foreach ($arr_flds as $key=>$val){
						
						switch ($key){
							case 'listserial':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $sn+$start_record);
								break;
							case 'task_topic':
								$td = $tr->appendElement('td',array('width'=>'20%'));
								$td->appendElement('plaintext', array(), $row['task_topic'] , true);
								break;
							case 'user_screen_name':
								$td = $tr->appendElement('td',array('width'=>'15%'));
								$td->appendElement('plaintext', array(), ($row['user_first_name'] != '')?$row['user_first_name']:$row['user_screen_name'] , true);
								break;
							case 'task_posted_on':
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), displayDate($row['task_posted_on'],true,true,CONF_TIMEZONE) , true);
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
								$td->appendElement('plaintext', array(),($row['task_status']==0) ? '<a class="toggleswitch actives" href="javascript:void(0);" id="verified_'. $row['task_id'].'" onclick="updateOrderVerificationStatus(' . $row['task_id'] . ', $(this))" title="Toggle Order verification status"></a>' : '<a class="toggleswitch"></a>', true);			
								
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
								$div = $td->appendElement('div', array('class'=>'action_btn'));
								$div->appendElement('a', array('href'=>generateUrl('orders','update',array($row['task_id'])),'title'=>'Edit','class'=>'button small black'), 'Edit', true);
								
								$div->appendElement('a', array('href'=>generateUrl('orders','order_details',array($row['task_id'])),'title'=>'Preview','class'=>'button small black'), 'Preview', true);
								
								$div->appendElement('a', array('href'=>generateUrl('orders','bids',array($row['task_id'])),'title'=>'Bids Listing','class'=>'button small green'), 'Bids', true);
													
								break;
							default:
								$td = $tr->appendElement('td');
								$td->appendElement('plaintext', array(), $row[$key], true);
								break;
						}
					}
				}
				
				if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

				echo $tbl->getHtml();
				

			?>
			<div class="paginationwrap"><?php echo generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages);?></div>
		</div>
	
	</section>
</section>