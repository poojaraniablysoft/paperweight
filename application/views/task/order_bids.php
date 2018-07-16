<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
global $service_types;
global $order_status;
global $bid_status;
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';
//echo $service_types[2];
//echo '<pre>' . print_r($order,true) . '</pre>';exit;
//echo '<pre>' . print_r($files,true) . '</pre>';
?>
<div class="order-details">
  <div class="white-box clearfix">
	<div class="order-step">
	  <div class="sectionTitle">
		  <h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $order['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo $order['task_topic'];?></span></h4>
	  </div>
	  <div id="rootwizard" class="register-step">
		<ul>
		  <li class="fill-up"> <a href="<?php echo generateUrl('task','add',array($order['task_id']));?>" data-toggle="tab">
			<figure class="figure">1</figure>
			<span><?php echo Utilities::getLabel( 'L_Fill_in_Order_Details' ); ?></span> </a></li>
		  <li class="active"> <a data-toggle="tab">
			<figure class="figure">2</figure>
			<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a></li>
		  <li> <a href="#tab3" data-toggle="tab">
			<figure class="figure">3</figure>
			<span><?php echo Utilities::getLabel( 'L_Choose_writer_&_Reserve_money' ); ?></span></a></li>
		  <li> <a href="#tab4" data-toggle="tab">
			<figure class="figure">4</figure>
			<span><?php echo Utilities::getLabel( 'L_Track_Progress' ); ?></span></a></li>
		</ul>
		<div id="bar" class="progress progress-striped active">
		  <div style="width: 33.33%;" class="bar"></div>
		</div>
	  </div>
	  <div class="sortwrap clearfix padding_30">
		<ul class="floatedList">
		  <li><strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?>:</strong>
			<?php echo $date = displayDate($order['task_due_date'],true, true, CONF_TIMEZONE);?><?php print_time_zone(); ?>
			<?php if(in_array($order['task_status'],array(0,1,2))) {?>
				<span <?php if(strtotime($order['task_due_date']) > time()){
					echo 'class="green_Txt"';
				}else{
					echo 'class="red_Txt"';
				}?>>
					<?php echo '('.time_diff($order['task_due_date']).')';?>
				</span>
			<?php }?>
		  </li>
			<?php if(in_array($order['task_status'],array(0,1,2))){ 
				$class =  'orgtag';
			}else if($order['task_status'] == 3){
				$class = 'greentag';
			}else if($order['task_status'] == 4){
				$class = 'redtag';
			} ?>
		  <li><strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong> <span class="<?php echo $class;?>"><?php echo $order_status[$order['task_status']]; ?></span></li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>: </strong><?php echo $order['task_pages'];?></li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Total_bids' ); ?>: </strong><?php echo count($arr_bids);?> </li>
		  <li><strong><?php echo Utilities::getLabel( 'L_Order_Budget' ); ?>: </strong><?php echo CONF_CURRENCY.number_format($order['task_budget'],2);?></li>
		</ul>
	  </div>
	</div>
  </div>
  <div class="user-list">
	<h2><?php echo Utilities::getLabel( 'L_Order_Detail' ); ?></h2>
	<div class="white-box clearfix">
	  <?php echo Message::getHtml(); ?> 
	  <div class="light-gray-box clearfix">
	  <div class="table-scroll">
	  
		<table class="order-info" width="100%" border="0" cellspacing="0" cellpadding="0" >
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Type_of_paper' ); ?>: </td>
			<td><?php echo $order['paptype_name'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Topic' ); ?>:</td>
			<td><?php echo $order['task_topic'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Pages' ); ?>:</td>
			<td><?php echo $order['task_pages'];?> <?php echo Utilities::getLabel( 'L_pages' ); ?> / <?php echo $order['task_pages']*$order['task_words_per_page'];?> <?php echo Utilities::getLabel( 'L_words' ); ?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Discipline' ); ?>:</td>
			<td><?php echo $order['discipline_name'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Type_of_service' ); ?>:</td>
			<td><?php echo $order['task_service_type'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Format_or_citation_style' ); ?>:</td>
			<td><?php echo $order['citstyle_name'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Paper_Instructions' ); ?></td>
			<td><?php echo $order['task_instructions'];?></td>
		  </tr>
		  <tr>
			<td><?php echo Utilities::getLabel( 'L_Additional_materials_(files)' ); ?></td>
			<td><?php foreach($files as $file) { ?> 
					<a class="attachlink attachicon" title="<?php echo $file['file_download_name'];?>" href="<?php echo generateUrl('task', 'download_file', array($file['file_id'])); ?>">
						<?php if(strlen($file['file_download_name']) < 36) {
								echo $file['file_download_name'];
							}else {
								echo substr($file['file_download_name'],0,30).'....'.substr($file['file_download_name'],-6);
							}
						?>
						<b><?php if ($file['file_size'] >= 1048576)						{							echo number_format($file['file_size'] / 1048576, 2) . ' MB';						}						elseif ($file['file_size'] >= 1024)						{							echo number_format($file['file_size'] / 1024, 2) . ' KB';						}						elseif ($file['file_size'] > 1)						{							echo $file['file_size']. ' ' . Utilities::getLabel( 'L_bytes' );						}						#echo number_format(($file['file_size']/1069547),3).'MB';?></b>
					</a>						
				<?php } ?>
				<?php if (count($files) == 0) echo Utilities::getLabel( 'L_No files uploaded' ); ?>
				
			</td>
		  </tr>
		</table>
		</div>
		<?php if ($order['task_status'] == 0 || $order['task_status'] == 1) { ?>
			<?php if(!User::isUserReqDeactive()) { ?>
			<div class="change-order-details"> 
				<a href="<?php echo generateUrl('task', 'add', array($order['task_id'])); ?>" class="theme-btn theme-btn"><i class="icon ion-edit"></i><?php echo Utilities::getLabel( 'L_Change_Order_Details' ); ?></a>
				<a href="javascript:void(0);" onclick="uploadAdditional('<?php echo $order['task_id'];?>')" class="theme-btn fadeandscale_open"><i class="icon ion-android-attach"></i><?php echo Utilities::getLabel( 'L_Upload_Additional_Material' ); ?></a>
				<a href="javascript:void(0);"  onclick="extendDeadline('<?php echo $order['task_id'];?>')" class="theme-btn"><i class="icon ion-clock"></i><?php echo Utilities::getLabel( 'L_Extend_Deadline' ); ?></a>
				<a href="javascript:void(0);" onclick="if(confirm('<?php echo Utilities::getLabel( 'L_Cancel_Order_Alert' ); ?>')) {document.location.href='<?php echo generateUrl('task', 'cancel_order', array($order['task_id'])); ?>';}" class="theme-btn"><i class="icon ion-close-round"></i><?php echo Utilities::getLabel( 'L_Cancel_Order' ); ?></a>
			</div>
			<?php }?>
		<?php } ?>
		
		
		
	  </div>
	</div>
  </div>
  <div class="user-list">
	<h2><?php echo Utilities::getLabel( 'L_Total_bids' ); ?><span><?php echo count($arr_bids);?></span></h2>
	<div class="white-box clearfix">
	  <div class="table-scroll">
		  
	 
	  <table class="total-bids" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <th><?php echo Utilities::getLabel( 'L_Writer' ); ?></th>
		  <th><?php echo Utilities::getLabel( 'L_Rating' ); ?></th>
		  <th><?php echo Utilities::getLabel( 'L_Price' ); ?></th>
		  <th><?php echo Utilities::getLabel( 'L_Preview' ); ?></th>
		  <th><?php echo Utilities::getLabel( 'L_Status' ); ?></th>
		  <th>&nbsp;</th>
		</tr>
		<?php foreach ($arr_bids as $key=>$arr) { ?>
		<tr>
		  <td><figure class="writer-img"><img src="<?php echo generateUrl('user', 'photo', array($arr['bid_user_id'],54,54)); ?>"  alt="img"></figure>
			<p><strong> <?php echo $arr['writer']; ?> </strong><br/>
			  <span><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $arr['orders_completed']; ?></span> </p></td>
		  <td>
			<?php if ($arr['bidder_rating'] > 0) { ?>
				<div class="ratingsWrap"><span class="p<?php echo ($arr['bidder_rating']*2); ?>"></span></div>
			<?php } else { ?>
				<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
			<?php } ?>
		  </td>
		  <td><?php echo CONF_CURRENCY . $arr['bid_price']; ?></td>
		  <td><?php echo strlen( $arr['bid_preview'] ) > 0 ? Utilities::getLabel( 'L_Available' ) : Utilities::getLabel( 'L_Not_available' ); ?></td>
		  <td>
			<span class="<?php if($arr['bid_status'] == 0){
									echo 'orgtag';
								}else if($arr['bid_status'] == 1){
									echo 'greentag';
								}else if($arr['bid_status'] == 2 || $arr['bid_status'] == 3){
									echo 'redtag';
								} ?>">
			<?php echo $bid_status[$arr['bid_status']]; ?></span>
		  </td>
		  <td><a class="theme-btn" href="<?php echo generateUrl('task', 'bid_preview', array($arr['bid_id'])); ?>"><?php echo Utilities::getLabel( 'L_Bid_Details' ); ?></a></td>
		</tr>
		<?php } ?>
		<?php if (count($arr_bids) == 0) echo '<tr><td colspan="6">' . Utilities::getLabel( 'L_No_bids_placed_yet' ) . '</td></tr>'; ?>            
	  </table>
	</div></div>
  </div>
  <div class="white-box clearfix padding_30">
	<p><?php echo Utilities::getLabel( 'L_Order_Short_Preview_Request' ); ?></p>
  </div>
</div>
