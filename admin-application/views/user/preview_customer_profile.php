<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $order_status;
echo Message::getHtml();
//echo "<pre>".print_r($rs,true)."</pre>";exit;
?>

<section class="rightPanel wide">
	<ul class="breadcrumb">
		<li><a href="<?php echo generateUrl('home');?>"><img src="<?php echo CONF_WEBROOT_URL;?>images/home.png" alt=""> </a></li>
		<li><a href="<?php echo generateUrl('user','user_listing');?>">User Management System</a></li>
		<li>Customer Details</li>
	</ul>
	<?php echo Message::getHtml(); ?>
	<div class="title"><h2><?php echo $rs['user_screen_name'];?></h2><a class="button green" href="<?php echo generateUrl('user','transaction_details',array($rs['user_id']))?>">View Wallet Transactions</a></div>
	<section class="box">
		<div class="box_head"><h3>Customer Details</h3></div>
		<div class="box_content clearfix">
		<?php		
			$tbl = new HtmlElement('table',array('class'=>'colTable','width'=>'100%'));
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Customer Email');
			$tr->appendElement('td',array(),$rs['user_email']);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Customer Name');
			$tr->appendElement('td',array(),$rs['user_first_name']." ".$rs['user_last_name']);
			/*
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Gender');
			if($rs['user_sex']==1) {
				$sex = 'Male';
			}
			else {
				$sex = 'Female';
			}
			$tr->appendElement('td',array(),$sex);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Country');
			$tr->appendElement('td',array(),$rs['user_country_id']['country_name']);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'City');
			$tr->appendElement('td',array(),$rs['user_city']);*/
			
			/* $tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Phone');
			$tr->appendElement('td',array(),$rs['user_mobile']); */
			
			if ($user_rating > 0) {
				$user_rat = '<div class="ratingsWrap ratingSection"><span class="p'.($user_rating*2).'"></span></div>';
			} else {
				$user_rat = '<span class="nrmltxt">Not rated yet</span>';
			}
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Profile Photo');
			$tr->appendElement('td','','<img src="'.generateUrl('user','photo',array($rs['user_id'],184,100)).'"/><br>'.$user_rat.'<br><p>Completed orders '.$orders_completed.'</p>',true);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Is Featured');
			$td = $tr->appendElement('td',array(''),'');
			$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'featured_'. $rs['user_id'], 'onclick'=>'markFeatured(' . $rs['user_id'] . ', $(this))', 'title'=>'Toggle featured status','class'=>($rs['user_is_featured']==1?"toggleswitch":"toggleswitch actives")),'', true);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Customer Status');
			$td=$tr->appendElement('td',array());
			
			$img = $rs['user_active'] == 1 ? '<img src="' . CONF_WEBROOT_URL . 'images/actives.png" alt="" class="whiteicon active_icon">' : '<img src="' . CONF_WEBROOT_URL . 'images/inactives.png" alt="" class="whiteicon inactive_icon">';
			/* $ul = $td->appendElement('ul',array('class'=>'iconbtns'));
			$li = $ul->appendElement('li'); */
			$td->appendElement('a', array('href'=>'javascript:void(0);', 'id'=>'status_'. $rs['user_id'], 'onclick'=>'updateUserStatus(' . $rs['user_id'] . ', $(this))', 'title'=>'Toggle featured status','class'=>'iconbtns_admin'),$img, true);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Impersonate Link');
			$td=$tr->appendElement('td',array(''),'');
			$td->appendElement('a', array('href'=>generateUrl('user','user_impersonation',array($rs['user_id'])),'target'=>'_blank' ,'id'=>'user_'. $rs['user_id'],'title'=>'Click to login as User', 'class'=>'button green small'),'Login As User Impersonation', true);
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Registration Date');
			$tr->appendElement('td',array(),$rs['user_regdate']);
			
			if(!isset($rs['subscription'])){
				$rs['subscription'] = "No Subscription!";
			}
			
			$tr =$tbl->appendElement('tr',array());
			$tr->appendElement('td',array(),'Subscription');
			$tr->appendElement('td',array(),nl2br($rs['subscription']),true);
			
			
			echo $tbl->getHtml();
		?>
		</div>
		
			<div class="clear"></div>
	</section>
	<section class="box">
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
								$td->appendElement('plaintext', array(), $row['user_screen_name'] , true);
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
								$div= $td->appendElement('div', array('class'=>'action_btn'));
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
		
	<section class="box filterbox">
		<div class="box_head"><h3>Change Password for this Customer</h3></div>
		<div class="box_content clearfix space">
			<?php echo $frm->getFormHtml();?>
		</div>
	</section>
</section>