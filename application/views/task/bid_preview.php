<?php 
global $service_types;
global $order_status;
global $bid_status;
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo "<pre>" . print_r($customer_orders_completed,true) . "</pre>";exit;
?>
<div class="place-bid order-process1">
  <div class="white-box order-step  clearfix">
	<div class="sectionTitle">
	  <h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $order['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo $order['task_topic'];?></span></h4>
	</div>
	<?php echo Message::getHtml(); ?>
	<div class="register-step" id="rootwizard">
		<ul>
			<li class="fill-up"> <a data-toggle="tab" href="<?php echo generateUrl('task','add',array($order['task_id']));?>">
			<figure class="figure">1</figure>
			<span><?php echo Utilities::getLabel( 'L_Fill_in_Order_Details' ); ?></span> </a></li>
			<li <?php if($order['task_status']!=2 && $order['task_status']!=3) {echo 'class="active"';}?>> <a data-toggle="tab" href="<?php echo generateUrl('task','order_bids',array($order['task_id']));?>">
			<figure class="figure">2</figure>
			<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a></li>
			<li> <a data-toggle="tab">
			<figure class="figure">3</figure>
			<span><?php echo Utilities::getLabel( 'L_Choose_writer_&_Reserve_money' ); ?></span></a></li>
			<li> <a data-toggle="tab">
			<figure class="figure">4</figure>
			<span><?php echo Utilities::getLabel( 'L_Track_Progress' ); ?></span></a></li>
		</ul>
		<div class="progress progress-striped active" id="bar">
			<div class="bar" style="width: 33%;"></div>
		</div>
	</div>
	<div class="sortwrap clearfix blue-bg padding_30">
		<ul class="floatedList">
			<li><strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?>:</strong> <?php echo $date = displayDate($order['task_due_date'],true, true, CONF_TIMEZONE);
			print_time_zone();
			if($order['task_status'] != 4) {?> (<span <?php if(strtotime($order['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>><?php echo time_diff($order['task_due_date']);?></span>)<?php }?> </li>
			<li><strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong> <span class="<?php if(in_array($order['task_status'],array(0,1,2))){echo 'orgtag';}else if($order['task_status'] == 3){echo 'greentag';}else if($order['task_status'] == 4){echo 'redtag';} ?>"><?php echo $order_status[$order['task_status']]; ?></span></li>
			<li><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>: </strong><?php echo $order['task_pages'];?></li>
			<li><strong><?php echo Utilities::getLabel( 'L_Total_bids' ); ?>: </strong>1 </li>
		</ul>
	</div>
  </div>
  <div class="white-box clearfix padding_30">
	<div class="grid_12 clearfix">
	  <div class="gray-box clearfix">
		<div class="grid_1 grid_4">
			<div class="sectionphoto">
				<figure class="photo"> <img alt="img" src="<?php echo generateUrl('user', 'photo', array(User::getLoggedUserAttribute('user_id'),200,200));?>"></figure>
				<p><strong><?php echo $order['user_first_name']; ?></strong></p>
				
					<div class="ratingsWrap"><span class="p<?php echo $customer_rating*2; ?>"></span></div>
				
				<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <span class="cmp_ord"><?php echo ($customer_orders_completed > 0)?$customer_orders_completed:'0'; ?></span></p>
			</div>
		</div>
		<div class="grid_2 grid_3">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="28%;"><strong><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?>:</strong></td>
					<td><?php echo $order['paptype_name'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Topic' ); ?>:</strong></td>
					<td><?php echo $order['task_topic'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>:</strong></td>
					<td><?php echo $order['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?> / <?php echo $order['task_pages']*$order['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Discipline' ); ?>:</strong></td>
					<td><?php echo $order['discipline_name'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Type_of_service' ); ?>:</strong></td>
					<td><?php echo $order['task_service_type'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Format_or_citation_style' ); ?>:</strong></td>
					<td><?php echo $order['citstyle_name'];?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Paper_Instructions' ); ?></strong></td>
					<td><?php echo nl2br($order['task_instructions']); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo Utilities::getLabel( 'L_Additional_material_(files)' ); ?></strong></td>
					<td>
						<?php foreach($files as $file) { ?> 
							<a title="<?php echo $file['file_download_name'];?>" href="<?php echo generateUrl('task', 'download_file', array($file['file_id'])); ?>" class="attachlink attachicon"><?php if(strlen($file['file_download_name']) < 36) {echo $file['file_download_name'];}else { echo 
								substr($file['file_download_name'],0,30).'....'.substr($file['file_download_name'],-6);}?> <b><?php echo number_format(($file['file_size']/1069547),3).'MB';?></b></a>
						<?php } ?>
						<?php if (count($files) == 0) echo Utilities::getLabel( 'L_No_files_uploaded' ); ?>
					</td>
				</tr>
			</table>
		</div>
	  </div>
	  
	  <div class="change-order-details">
		<div class="left-align">
			<?php 
				if(!User::isUserReqDeactive()) {
					if($order['task_status'] == 1) { ?>
						<a href="<?php echo generateUrl('task', 'add', array($order['task_id'])); ?>" class="theme-btn"><i class="icon ion-edit"></i> <?php echo Utilities::getLabel( 'L_Change_Order_Details' ); ?></a>
						<a href="javascript:void(0);" onclick="uploadAdditional('<?php echo $order['task_id'];?>')" class="theme-btn"><i class="icon ion-android-attach"></i> <?php echo Utilities::getLabel( 'L_Upload_Additional_Material' ); ?></a>
						<a href="javascript:void(0);"  onclick="extendDeadline('<?php echo $order['task_id'];?>')" class="theme-btn"><i class="icon ion-clock"></i> <?php echo Utilities::getLabel( 'L_Extend_Deadline' ); ?></a>
						<?php if($order['task_status']!=4) { ?>
							<a href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Cancel_Order_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('task', 'cancel_order', array($order['task_id'])); ?>';}" class="theme-btn"><i class="icon ion-close-round"></i> <?php echo Utilities::getLabel( 'L_Cancel_Order' ); ?></a>
						<?php } 
					}else{ ?>
						<h2><?php echo Utilities::getLabel( 'L_Your_bid_has_been_cancelled' ); ?></h2>
					<?php }
				}?>			
		</div>
		<a href="<?php echo generateUrl('task','order_bids',array($order['task_id']))?>"  class="theme-btn right-aling"> <?php echo Utilities::getLabel( 'L_Back_to_Orders' ); ?></a>
	  </div>
	</div>
  </div>
</div>

<div class="user-list">
	<?php if (($order['task_status'] == 2 || $order['task_status'] == 3) && $arr_bid['bid_status'] == 1) { ?><h2><?php echo Utilities::getLabel( 'L_Assigned_writer' ); ?></h2><?php }?>
	<div class="rate-review">
	  <div class="cols_4">
		<div class="white-box clearfix assigned-writer">
		  <div class="grid_1">
			<div class="gray-box clearfix">
			  <div class="writer-info ">
				
				<figure class="img-sect"><img src="<?php echo generateUrl('user', 'photo', array($arr_bid['bid_user_id'],130,130));?>" alt="img"></figure>
				<h3><?php echo $arr_bid['writer'];?></h3>
				<?php if ($writer_rating > 0) { ?>
					<div class="ratingsWrap"><span class="p<?php echo ($writer_rating*2); ?>"></span></div>
				<?php } else { ?>
					<div class='ratingsWrap'><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></div>
				<?php } ?>
				
				<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $writer_completed_orders;?></p>
				<div class=""><a class="theme-btn right-aling" href="<?php echo generateUrl('writer',seoUrl($arr_bid['writer']));?>"><?php echo Utilities::getLabel( 'L_View_Profile' ); ?></a></div>
			  </div>
			</div>
		  </div>
		  <div class="grid_2">
			<div class="gray-box">
				<?php if ($order['task_status'] == 2 || $order['task_status'] == 3 || ($order['task_status'] == 4 && $arr_bid['bid_status'] == 1)) { ?>
					<?php echo Utilities::getLabel( 'L_Price' ); ?>: <?php echo CONF_CURRENCY . number_format(getOrderAmount($arr_bid['bid_price'],$arr_bid['task_pages']),2); ?> 
				<?php } else { ?>
					<?php echo Utilities::getLabel( 'L_Writer_Bid' ); ?>: <?php echo CONF_CURRENCY . $arr_bid['bid_price']; ?>
				<?php } ?>
			</div>
			<div class="gray-box margin20 <?php if(($order['task_status']==4 && $arr_bid['bid_status']==0)||($arr_bid['bid_status'] == 2 || $arr_bid['bid_status'] == 3)){echo "rejected";}?>">
				<?php if(!User::isUserReqDeactive()) {
					if ($arr_bid['bid_status'] == 0 && $order['task_status'] != 4) { ?>
						<a class="theme-btn" href="<?php echo generateUrl('task','assign_task',array($arr_bid['bid_id']));?>"><?php echo Utilities::getLabel( 'L_Assign_writer' ); ?></a>
						<a class="theme-btn" href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Reject_Bid_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('task','reject_writer',array($arr_bid['bid_id']));?>';}"><?php echo Utilities::getLabel( 'L_Reject_bid' ); ?></a>
					<?php }elseif($order['task_status'] == 4 && $arr_bid['bid_status']==0) { ?>
						<span class="redtag"><?php echo $order_status[$order['task_status']];?></span>
					<?php } else { ?>
						<span class="<?php if($arr_bid['bid_status'] == 0){ echo 'orgtag'; }else if($arr_bid['bid_status'] == 1){echo 'greentag';}else if($arr_bid['bid_status'] == 2 || $arr_bid['bid_status'] == 3){echo 'redtag';} ?>"><?php echo $bid_status[$arr_bid['bid_status']]; ?></span>
					<?php } 
				}else{ ?>
					<span class="redtag"><?php echo Utilities::getLabel( 'L_Restrained' ); ?></span>
				<?php }?>
			</div>
		  </div>
		</div>		
		<div class="padding_30">
			<div class="white-box clearfix ">
				<div class="previewwrap">
					<div class="grid6">
						
						<?php if (!empty($arr_bid['bid_preview'])) { 
							echo Utilities::getLabel( 'L_Word_count' ) . ':&nbsp;<strong>' . getWordCount(html_entity_decode($arr_bid['bid_preview'])) . '</strong><br>'.html_entity_decode($arr_bid['bid_preview']);
						} else {
							//echo "<strong>".$arr_bid['writer']." </strong>". (empty($arr_bid['bid_preview']) ? 'has not provided a preview yet.' : 'has provided a sample preview for your paper.'); 
							echo '<div class="preview_head"><h2>' . Utilities::getLabel( 'L_Sample_Preview' ) . '</h2></div><div class="no-preview"> ' . Utilities::getLabel( 'L_No_Preview_Available' ) . ' </div>';
						}?>
					</div>
				</div>
			</div>
		</div>
	  </div>
	  <div class="cols_5">
		<div class="white-box clearfix">
			<div class="chatwrap">
				<div class="gray-box top clearfix">
					<figure class="online-user-img"><img src="<?php echo generateUrl('user', 'photo', array($arr_bid['bid_user_id'],45,45)); ?>"  alt="img"></figure>
					<?php
						$recipient_id = $arr_bid['bid_user_id'];						
						echo '<p>' . (strlen($arr_bid['writer']) > 18 ? substr($arr_bid['writer'],0,18) . '...' : $arr_bid['writer']) . '</p>';
					?>
					<span class="online_status left" data-uid="<?php echo $recipient_id; ?>"></span>	
				</div>
					<!--<span><a href="<?php echo generateUrl('messages','default_action',array($arr_bid['bid_id']));?>">Return to Msg</a></span>			-->		
				<div class="chatscroll">
					<?php foreach ($arr_messages as $ele) { ?>
						<div class="chatList">
							<span class="timetxt"><?php echo datesToInterval($ele['tskmsg_date'], true, true, CONF_TIMEZONE); ?></span>
							<?php
							if (strlen($ele['sender']) > 8) {
								echo '<h5 title="' . $ele['sender'] . '">' . substr($ele['sender'],0,8) . '...</h5>';
							}
							else {
								echo '<h5>' . $ele['sender'] . '</h5>';
							}
							?>
							
							<p><?php echo nl2br($ele['tskmsg_msg']); ?></p>
						</div>
					<?php } ?>
				</div>
				<div class="postSection">
					<form>
						<input type="hidden" name="bid_id" value="<?php echo $arr_bid['bid_id']; ?>">
						<textarea name="message" id="message" cols="" rows="" placeholder="<?php echo Utilities::getLabel( 'L_Write_your_message_here' ); ?>"></textarea>
						<input type="button" name="btn_send" id="btn_send" value="<?php echo Utilities::getLabel( 'L_Send' ); ?>" class="theme-btn">
					</form>
								
					<p><?php echo sprintf( Utilities::getLabel( 'L_follow_our_confidentiality_policy' ), generateUrl( 'cms', 'page', array( 'confidentiality_policy' ) ) ); ?></p>
				</div>
			</div>
		</div>
	  </div>


	</div>
</div>
