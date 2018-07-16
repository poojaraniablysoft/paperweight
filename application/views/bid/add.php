<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
global $service_types;
global $order_status;

//echo '<pre>' . print_r($arr_bid,true) . '</pre>';exit;

include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

?>
<?php if(User::isWriter() && !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {?>
	<div class="order_page">
		<div class="approve_div">
			<a class="close" href="javascript:void(0);"><img class="close_image" title="close" src="/facebox/closelabel.gif"></a>
			<p>
				<?php echo Utilities::getLabel( 'L_Approve_Request_Description' ); ?>
				<?php if(!Common::is_essay_submit(User::getLoggedUserAttribute('user_id'))) {echo '<a href="'.generateUrl('sample').'" class="theme-btn">' . Utilities::getLabel( 'L_Click_here_to_submit_essay' ). '</a>';}?>
			</p>
		</div>
	</div>
<?php  }?>
<div class="place-bid">
	<div class="white-box clearfix">
		<?php echo Message::getHtml(); ?>
	  <div class="sectionTitle"> 
		<h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $order['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo substr($order['task_topic'],0,50);?></span></h4>
		<a class="theme-btn" href="<?php echo generateUrl('task','orders');?>"><?php echo Utilities::getLabel( 'L_Back' ); ?><span> <?php echo Utilities::getLabel( 'L_to available orders' ); ?></span></a>
	  </div>

	  <div class="sortwrap clearfix blue-bg">
		<ul class="floatedList">
		  <li><strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?>:</strong> <?php echo $date = displayDate($order['task_due_date'],true,true, CONF_TIMEZONE);?><?php echo ' ';
		  print_time_zone();?> <span <?php if(strtotime($ele['task_due_date']) < time()){echo 'class="bluetag"';}else{echo 'class="redtag"';}?>>(<?php echo time_diff($order['task_due_date']);?>)</span></li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong> <span class="orgtag"><?php echo $order_status[$order['task_status']];?></span></li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>: </strong><?php echo $order['task_pages'];?></li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Total_bids' ); ?>: </strong><?php echo Bid::getnumbids($order['task_id']);?> </li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Order_Budget' ); ?>: </strong><?php echo CONF_CURRENCY.number_format($order['task_budget'],2);?></li>
		</ul>
	  </div>
	</div>
	<div class="white-box padding_30">
		<div class="grid_12"> 
		  
		  <!--left penal -->
		  <div class="<?php if($order['task_status']==2 || $order['task_status']==3 || !User::writerCanBid(User::getLoggedUserAttribute('user_id'))) {echo 'full';}else{echo 'left';}?>-penal gray-box">
			<div class="grid_1">
			  <div class="sectionphoto">
				<figure class="photo"> <img alt="img" src="<?php echo generateUrl('user', 'photo', array($order['task_user_id'],150,150));?>"></figure>
				<p><strong><?php echo $order['customer_first_name'];?></strong></p>
				<?php if($user_rating >0) {?>
					<div class="ratingsWrap"><span class="p<?php echo ($user_rating*2); ?>"></span></div>
				<?php }else { echo "<div class='ratingsWrap'>" . Utilities::getLabel( 'L_Not_rated_yet' ) . "</div>"; }?>
				<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $completed_orders;?></p>
			  </div>
		    </div>
		  <div class="grid_2">
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
				  <td><?php echo $order['task_pages'] . " " . Utilities::getLabel( 'L_pages' );?> / <?php echo ( $order['task_pages']*$order['task_words_per_page'] ) . " " . Utilities::getLabel( 'L_words' );?></td>
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
					<?php if (count($files) == 0) echo Utilities::getLabel( 'L_No_files_uploaded' ) . '.'; ?></td>
				</tr>
			</table>
		  </div>
		</div>
		  
		  <!--right penal -->
		<?php if(User::writerCanBid(User::getLoggedUserAttribute('user_id'))) { 
			if(($order['task_status']!=2 && $order['task_status']!=3) && User::isUserReqDeactive() == false) {?>
			<div class="right-penal gray-box">
				<?php	if($invite_status != 2) { echo $frmbid->getFormHtml();}else { ?>
					<div class="bid_declined"><h3><?php echo Utilities::getLabel( 'L_Bidding' ); ?></h3><span class="redtag"><?php echo Utilities::getLabel( 'L_Declined' ); ?></span></div>
				<?php }?>
			</div>				
			<?php }
		}?>
	</div>
</div>
<?php if($bid_details['bid_id']>0) { ?>
<div class="user-list padding_30 padding-top-none">
  <div class="clearfix rate-review">
	<div class="cols_1">
	  <div class="white-box assigned-writer">     
		<ul class="list-order clearfix total-price">
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Bid to order' ); ?>:</p> <span><?php echo CONF_CURRENCY . $bid_details['bid_price']; ?></span></div></li>
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Paid to you' ); ?>: </p><span><?php echo CONF_CURRENCY.number_format(getAmountPayableToWriter($bid_details['bid_price']),2);?></span></div></li>
			<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'Total Price' ); ?>:</p><span><?php echo CONF_CURRENCY.number_format(getOrderAmount($bid_details['bid_price'],$bid_details['task_pages']),2); ?></span></div></li>						  
		</ul>
		<div class="cancel-wrap" >
			<?php if(User::isUserActive()== true && User::isUserReqDeactive() != true) {?>
				<a class="theme-btn" href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Remove_Bid_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('bid','bid_remove',array($bid_id));?>'}"><?php echo Utilities::getLabel( 'L_REMOVE_MY_BID' ); ?></a>
			<?php }?>
		</div>            
	  </div> 
	
	</div>
  </div>
</div>
<?php }?>
</div>