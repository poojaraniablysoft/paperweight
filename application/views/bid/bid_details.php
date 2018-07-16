<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $service_types;
global $order_status;
//echo "<pre>".print_r($order_status[$order['task_status']],true)."</pre>";exit;
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
?>
<div class="place-bid order-process1">
  <?php echo Message::getHtml(); ?>
  <div class="white-box  clearfix">
	<div class="sectionTitle"> 
	  <h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $order['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo $order['task_topic'];?></span></h4>
	</div>
	<div class="sortwrap clearfix blue-bg">
	  <ul class="floatedList">
		<li><strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?></strong> <?php echo $date = displayDate($order['task_due_date'],true, true,CONF_TIMEZONE);
		print_time_zone();
			if(in_array($order['task_status'],array(0,1,2))) { ?>
				(<span <?php if(strtotime($order['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>><?php echo time_diff($order['task_due_date']); ?></span>)
			<?php }?>
		</li>
		<li><strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong>
			<span class="<?php if(in_array($order['task_status'],array(0,1,2))){
								echo 'orgtag';
						}else if($order['task_status'] == 3 && $order['task_writer_id'] == User::getLoggedUserAttribute('user_id')){ 
							echo 'greentag';
						}else if($order['task_status'] == 4 || $order['task_writer_id'] != User::getLoggedUserAttribute('user_id')){ 
							echo 'redtag';
						} ?>">
				<?php if($order['task_writer_id'] != User::getLoggedUserAttribute('user_id') && $order['task_status'] == 3 ){
						echo Utilities::getLabel( 'L_Rejected' );
				}else {
						echo $order_status[$order['task_status']];
				} ?>
			</span>
		</li>
		<li><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>: </strong><?php echo $order['task_pages'];?></li>
		<li><strong><?php echo Utilities::getLabel( 'L_Total_bids' ); ?>: </strong><?php echo Bid::getnumbids($order['task_id']);?></li>
	  </ul>
	</div>
  </div>
  <div class="white-box clearfix padding_30">
	<div class="grid_12 clearfix">
	  <div class="gray-box clearfix">
		<div class="grid_1 grid_4">
		  <div class="sectionphoto">
			<figure class="photo"> <img alt="img" src="<?php echo generateUrl('user', 'photo', array($order['task_user_id'],200,200)); ?>"></figure>
			<p><strong><?php echo $order['customer_first_name']; ?></strong></p>
			<?php if ($customer_rating > 0) { ?>
				<div class="ratingsWrap"><span class="p<?php echo $customer_rating*2; ?>"></span></div>
				<span class="nrmltxt"><?php echo $customer_rating; ?></span>
			<?php } else { ?>
				<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
			<?php } ?>
			<p><?php echo Utilities::getLabel( 'L_Completed_orders_4' ); ?></p>
		  </div>
		</div>
		<div class="grid_2 grid_3">
		  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="28%"><strong><?php echo Utilities::getLabel( 'L_Customer_Name' ); ?>:</strong></td>
			  <td><?php echo $order['customer_first_name']; ?></td>
			</tr>
			<tr>
			  <td width="28%"><strong><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?>:</strong></td>
			  <td><?php echo $order['paptype_name'];?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Topic' ); ?>:</strong></td>
			  <td><?php echo $order['task_topic'];?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>:</strong></td>
			  <td><?php echo $order['task_pages'] . ' ' . Utilities::getLabel( 'L_pages' );?> / <?php echo ( $order['task_pages']*$order['task_words_per_page'] ) . ' ' . Utilities::getLabel( 'L_words' ); ?></td>
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
			  <td><?php echo nl2br($order['task_instructions']);?></td>
			</tr>
			<tr>
			  <td><strong><?php echo Utilities::getLabel( 'L_Additional_material_(files)' ); ?></strong></td>
			  <td>
				<?php foreach($files as $file) { ?> 
					<a title="<?php echo $file['file_download_name'];?>" href="<?php echo generateUrl('task', 'download_file', array($file['file_id'])); ?>" class="attachlink attachicon"><?php if(strlen($file['file_download_name']) < 30) {echo $file['file_download_name'];}else { echo 
						substr($file['file_download_name'],0,30).'....'.substr($file['file_download_name'],-6);}?> <b><?php echo number_format(($file['file_size']/1069547),3).'MB';?></b></a>
				<?php } ?>
				<?php if (count($files) == 0) echo Utilities::getLabel( 'L_No_files_uploaded' ) . '.'; ?>
			  </td>
			</tr>
		  </table>
		</div>
	  </div>
	  <div class="order-complete">
		<a class="theme-btn" href="<?php echo generateUrl('bid','my_bids');?>"><?php echo Utilities::getLabel( 'L_Back_to_Orders' ); ?></a>		
	  </div>
	</div>
  </div>
</div>
<?php if($order['task_status']!= 4){?>
<div class="user-list padding_30 padding-top-none">
  <div class="rate-review">
	<div class="cols_4">
	  <div class="white-box clearfix assigned-writer">     
		<ul class="list-order clearfix total-price">
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Bid_to_order' ); ?>:</p> <span><?php echo CONF_CURRENCY.$arr_bid['bid_price'];?></span></div></li>
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Paid_to_you' ); ?>: </p><span><?php echo CONF_CURRENCY.number_format(getAmountPayableToWriter($arr_bid['bid_price']),2);?></span></div></li>
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Total_Price' ); ?>:</p><span><?php echo CONF_CURRENCY . number_format(getOrderAmount($arr_bid['bid_price'],$arr_bid['task_pages']),2); ?></span></div></li>						  
		</ul>
		<div class="cancel-wrap" >
			<?php if( User::isUserActive()== true && User::isUserReqDeactive() != true && !in_array( $order['task_status'], array( 0, 3 ) ) ) {?>
				<a class="theme-btn" href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Remove_Bid_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('bid','bid_remove',array($bid_id));?>'}"><?php echo Utilities::getLabel( 'L_REMOVE_MY_BID' ); ?></a>
				<a class="theme-btn" href="<?php echo generateUrl("bid","add",array($order['task_id'],$bid_id));?>"><?php echo Utilities::getLabel( 'L_CHANGE_MY_BID' ); ?></a>
			<?php }?>
		</div>            
	  </div> 
	<?php if(User::isUserActive()) {?>
	  <div class="padding_30">
		<div class="white-box editorwrap clearfix">
		  <?php echo $frm->getFormHtml(); ?>
		</div>          
	  </div>
	<?php }?>
	</div>
	<?php if(User::isUserActive()) {?>
	<div class="cols_5">
	  <div class="white-box clearfix">
		<div class="chatwrap ">
		  <div class="gray-box top clearfix">
			<figure class="online-user-img"><img src="<?php echo generateUrl('user', 'photo', array($order['task_user_id'],45,45)); ?>" alt="img"></figure>
			<p><?php echo (strlen($order['customer_first_name']) > 18 ? substr($order['customer_first_name'],0,18) . '...' : $order['customer_first_name'])?><span class="online_status left" data-uid="<?php echo $recipient_id; ?>"></span></p>
		  </div>
		  <div class="chatscroll chatscroll-min-height">
		
			<?php foreach ($arr_messages as $ele) { ?>
				<div class="chatList">
					<span class="timetxt"><?php echo datesToInterval($ele['tskmsg_date']); ?></span>
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
				<input type="hidden" name="bid_id" value="<?php echo $bid_id; ?>">
				<textarea name="message" id="message" cols="" rows="" placeholder="<?php echo Utilities::getLabel( 'L_Write_your_message_here' ); ?>"></textarea>
				<input type="button" class="theme-btn" name="btn_send" id="btn_send" value="<?php echo Utilities::getLabel( 'L_Send' ); ?>">
			</form>
			<p><?php echo sprintf( Utilities::getLabel( 'L_follow_our_confidentiality_policy' ), generateUrl('cms','page',array( 'confidentiality_policy')) ); ?></p>
		  </div>
		</div>
	  </div>
	</div>
	<?php }?>
  </div>
</div>
<?php }?>