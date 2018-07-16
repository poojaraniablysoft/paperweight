<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
include CONF_THEME_PATH . 'inner_nav_header.php';
include CONF_THEME_PATH . 'page_bar.php';

global $service_types;
global $order_status;
global $milestone_status;

//echo "<pre>" . print_r($_POST,true) . "</pre>";
//echo "<pre>" . print_r($ongoing_milestone,true) . "</pre>";exit;
?>

	<link rel="stylesheet" type="text/css" href="<?php echo CONF_WEBROOT_URL;?>css/jquery.rating.css" />
	<script type="text/javascript" src="<?php echo CONF_WEBROOT_URL;?>js/jquery.rating.js"></script>										
										
<script type="text/javascript">
	var logged_user_id = <?php echo User::getLoggedUserAttribute('user_id'); ?>;
</script>

<div class="place-bid order-process1">
	<div class="white-box order-step  clearfix">
		<?php echo Message::getHtml(); ?>
		<div class="sectionTitle">
			<h4><?php echo Utilities::getLabel( 'L_Order_id' ); ?> <span>#<?php echo $order['task_ref_id'];?></span><font><?php echo Utilities::getLabel( 'L_Task_Topic' ); ?></font><span><?php echo $order['task_topic'];?></span></h4>
			
		</div>
		<div class="register-step" id="rootwizard">
			<ul>
				<li class="fill-up"> <a data-toggle="tab">
					<figure class="figure">1</figure>
					<span><?php echo Utilities::getLabel( 'L_Fill_in_Order_Details' ); ?></span> </a>
				</li>
				<li class="active"> <a data-toggle="tab">
					<figure class="figure">2</figure>
					<span><?php echo Utilities::getLabel( 'L_Order_Bidding' ); ?></span></a>
				</li>
				<li class="fill-up"> <a data-toggle="tab">
					<figure class="figure">3</figure>
					<span><?php echo Utilities::getLabel( 'L_Choose_Writer_and_reserve_money' ); ?></span></a>
				</li>
				<li class="active">
					<a data-toggle="tab">
					<figure class="figure">4</figure>
					<span><?php echo Utilities::getLabel( 'L_Track_Progress' ); ?></span></a>
				</li>
			</ul>
			<div class="progress progress-striped active" id="bar">
				<div class="bar" style="width: 100%;"></div>
			</div>
		</div>
		<div class="sortwrap clearfix blue-bg padding_30">
			<ul class="floatedList">
				<li>
					<?php if(!in_array($order['task_status'],array(3))) {?>
						<strong><?php echo Utilities::getLabel( 'L_Deadline' ); ?>:</strong>
						<?php
							echo $date = displayDate($order['task_due_date'],true, true, CONF_TIMEZONE);
							print_time_zone(); 
							if(in_array($order['task_status'],array(0,1,2)) ) {
						?> (<span <?php if(strtotime($order['task_due_date']) > time()){echo 'class="green_Txt"';}else{echo 'class="red_Txt"';}?>>
							<?php echo time_diff($order['task_due_date']); ?>
							</span>)
						<?php }
					}else { ?>
						<strong><?php echo Utilities::getLabel( 'L_Order_Completed_On' ); ?>:</strong>
						<?php
							echo $date = displayDate($order['task_completed_on'],true, true, CONF_TIMEZONE);
					}?>
				</li>
				<li>
					<strong><?php echo Utilities::getLabel( 'L_Status' ); ?>: </strong>
					<span class="<?php if(in_array($order['task_status'],array(0,1,2))){echo 'orgtag';}else if($order['task_status'] == 3){echo 'greentag';}else if($order['task_status'] == 4){echo 'redtag';} ?>">
						<?php echo $order_status[$order['task_status']]; ?>
					</span>
				</li>
				<li><strong><?php echo Utilities::getLabel( 'L_Pages' ); ?>: </strong><?php echo $order['task_pages']; ?></li>
				<li><strong><?php echo Utilities::getLabel( 'L_Total_bids' ); ?>: </strong><?php echo Bid::getnumbids($order['task_id']); ?> </li>
			</ul>
		</div>
	</div>
	<div class="white-box clearfix padding_30">
		<div class="grid_12 clearfix">
			<div class="gray-box clearfix">
				<div class="grid_1 grid_4">
					<div class="sectionphoto">
						<figure class="photo"> <img alt="img" src="<?php echo generateUrl('user', 'photo', array($order['task_user_id'],200,200)); ?>"></figure>
						<p><strong><?php echo $order['user_first_name']; ?></strong></p>
						<?php if ($customer_rating > 0) { ?>
							<div class="ratingsWrap"><span class="p<?php echo $customer_rating*2; ?>"></span></div>
						<?php } else { ?>
							<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
						<?php } ?>
						<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $customer_orders_completed; ?></p>
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
							<td><?php echo $order['task_pages']; ?> <?php echo Utilities::getLabel( 'L_pages' ); ?> / <?php echo $order['task_pages']*$order['task_words_per_page']; ?> <?php echo Utilities::getLabel( 'L_words' ); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo Utilities::getLabel( 'L_Discipline' ); ?>:</strong></td>
							<td><?php echo $order['discipline_name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo Utilities::getLabel( 'L_Type_of_service' ); ?>:</strong></td>
							<td><?php echo $order['task_service_type']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo Utilities::getLabel( 'L_Format_or_citation_style' ); ?>:</strong></td>
							<td><?php echo $order['citstyle_name']; ?></td>
						</tr>
						<tr>
							<td><strong><?php echo Utilities::getLabel( 'L_Paper_Instructions' ); ?></strong></td>
							<td><?php echo nl2br($order['task_instructions']); ?></td>
						</tr>
						<tr>
							<td><strong><?php echo Utilities::getLabel( 'L_Additional_material_(files)' ); ?></strong></td>
							<td>
								<?php foreach($files as $file) {?> 
									<a title="<?php echo $file['file_download_name'];?>" href="<?php echo generateUrl('task', 'download_file', array($file['file_id'])); ?>" class="attachlink attachicon">
									<?php 
										if(strlen($file['file_download_name']) < 36) {
											echo $file['file_download_name'];
										}else {
										echo substr($file['file_download_name'],0,30).'....'.substr($file['file_download_name'],-6);
										}
									?> <b><?php echo number_format(($file['file_size']/1069547),3).'MB';?></b></a>
								
								<?php } ?>
								<?php if (count($files) == 0) echo Utilities::getLabel( 'L_No_files_uploaded' ); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="view_file"><a class="theme-btn" href="javascript:void(0);" onclick="showOrderFiles(<?php echo $order['task_id']; ?>);"><?php echo Utilities::getLabel( 'L_View_Files' ); ?></a></div>
			<div class="change-order-details">
				<div class="left-align">
					<?php if(!User::isUserReqDeactive()) { ?>						
						<?php if (!User::isWriter()) { 		
							if ($order['task_status'] != 3 && $order['task_status'] != 4) { ?>
								<a href="javascript:void(0);" onclick="upload(<?php echo $order['task_id']; ?>);" class="theme-btn">
									<i class="icon ion-android-attach"></i> <?php echo Utilities::getLabel( 'L_Upload_Additional_Material' ); ?>
								</a>
								<a href="javascript:void(0);" onclick="extendDeadline('<?php echo $order['task_id'];?>')" class="theme-btn">
									<i class="icon ion-clock"></i> <?php echo Utilities::getLabel( 'L_Extend_Deadline' ); ?>
								</a>
							<?php }	else if($order['task_status']==3) {
								echo '<div class="ord-completed"><i class="icon ion-checkmark-round"></i>' . Utilities::getLabel( 'L_Your_order_is_completed' ) . '</div>';
							}elseif($order['task_status']==4){
								echo '<div class="cancel-ord"><i class="icon ion-close-round"></i>' . Utilities::getLabel( 'L_Your_order_is_cancelled' ) . '</div>';
							}
							
							if ( ( ( count($file_uploaded) > 0 || $order['cus_req_to_upload_again'] != 1 ) && ( $order['task_finished_from_writer'] == 1 && count($file_uploaded) > 0 ) ) && $order['task_status'] != 3 && $order['task_status'] != 4 ) { ?>
								<a href="javascript:void(0);" class="theme-btn" onclick="if(confirm('Are you sure you want to mark this order as finished?')) {document.location.href='<?php echo generateUrl('task', 'mark_as_finished', array($order['task_id'])); ?>';}">
									<i class="icon ion-checkmark-round"></i> <?php echo Utilities::getLabel( 'L_Mark_as_Finished' ); ?>
								</a>
							<?php } 
								
							if ($order['task_status'] != 3 && $order['task_status'] != 4) { ?>
								<a href="javascript:void(0);" class="theme-btn" onclick="if(confirm('Are you sure you want to cancel this order?')) {document.location.href='<?php echo generateUrl('task', 'cancel_order', array($order['task_id'])); ?>';}">
									<i class="icon ion-close-round"></i> <?php echo Utilities::getLabel( 'L_Cancel_Order' ); ?>
								</a>
							<?php } 
							
							if ($order['task_status'] == 2 && count($file_uploaded) > 0 && $order['task_review_request_status'] == 0 && $order['task_finished_from_writer'] == 1) { ?>
								<a href="javascript:void(0);" class="theme-btn" onclick="return review_request(<?php echo $order['task_id'];?>)"><?php echo Utilities::getLabel( 'L_Request_For_Revision' ); ?></a>
							<?php }
						}else{ 
							if ($order['task_status'] == 2 && $order['task_cancel_request'] == 0) {
								if($order['task_finished_from_writer'] != 1) {
							?>								
									<a href="javascript:void(0);" onclick="return cancel_order_request(<?php echo $order['task_id'];?>);" class="theme-btn">
										<i class="icon ion-close-round"></i> <?php echo Utilities::getLabel( 'L_Cancel_Order_Request' ); ?>
									</a>
							<?php 
								}
								if(count($file_uploaded) > 0 || $order['cus_req_to_upload_again'] != 1) { 
									if( $order['task_finished_from_writer'] != 1 && count($file_uploaded) > 0 ) { 
								?>
									<a href="javascript:void(0);" onclick="return mark_order_complete(<?php echo $order['task_id'];?>);" class="theme-btn"><?php echo Utilities::getLabel( 'L_Mark_the_order_as_completed' ); ?></a>
								<?php }
								}
							}elseif($order['task_status'] == 3) {
								echo '<div class="ord-completed"><i class="icon ion-checkmark-round"></i>' . Utilities::getLabel( 'L_Your_order_is_completed' ) . '</div>';
							}elseif($order['task_status'] == 4) {
								echo '<div class="cancel-ord"><i class="icon ion-close-round"></i>' . Utilities::getLabel( 'L_Your_order_is_cancelled' ) . '</div>';
							}else{
								echo '<div class="ord-completed"><i class="icon ion-checkmark-round"></i>' . Utilities::getLabel( 'L_You_have_requested_to_cancel_this_order' ) . '</div>';
							}
							if( $order['task_status'] != 3 && $order['task_finished_from_writer'] == 1 ) { echo '<div class="ord-completed"><i class="icon ion-checkmark-round"></i>' . Utilities::getLabel( 'L_You_have_marked_this_order_completed' ) . '</div>'; }
						} ?>					
					<?php } ?>
				
					<a href="<?php echo User::isWriter() ? generateUrl('bid', 'my_bids') : generateUrl('task', 'my_orders') ?>"  class="theme-btn right-aling"> <?php echo Utilities::getLabel( 'L_Back_to_Orders' ); ?></a>
				</div>
			</div>
		</div>
	</div>
	
	<div class="user-list">
		<?php if (!User::isWriter()) { ?><h2><?php echo Utilities::getLabel( 'L_Assigned_writer' ); ?></h2><?php }?>
		<div class="rate-review">
			<div class="cols_4">
			<?php if (!User::isWriter()) { ?>
				<div class="white-box assigned-writer">
					<div class="grid_1">
						<div class="gray-box clearfix">
							<div class="writer-info ">
								<figure class="img-sect"><img src="<?php echo generateUrl('user', 'photo', array($order['bid_user_id'],130,130)); ?>" alt="img"></figure>
								<h3><?php echo ucfirst($order['writer']);?></h3>
								<?php if ($writer_rating > 0) { ?>
									<div class="ratingsWrap"><span class="p<?php echo $writer_rating*2; ?>"></span></div>
								<?php } else { ?>
									<span><?php echo Utilities::getLabel( 'L_Not_rated_yet' ); ?></span>
								<?php } ?>
								<p><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $writer_orders_completed;?></p>
							</div>
						</div>
					</div>
					<div class="grid_2">
						<div class="gray-box">
							<i class="icon earnigs-icon"></i><?php echo CONF_CURRENCY . number_format(getOrderAmount($order['bid_price'],$order['task_pages']),2); ?>
						</div>
						<div class="gray-box margin20">
							<i class="icon progress-icon"></i><?php echo $order_status[$order['task_status']]; ?>
						</div>
					</div>
				</div>				
			<?php }else {?>	
				<div class="white-box  assigned-writer">
					<ul class="list-order clearfix total-price">
						<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Bid_to_order' ); ?>:</p> <span><?php echo CONF_CURRENCY . number_format($order['bid_price'],2); ?></span></div></li>
						<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Paid_to_you' ); ?>: </p><span><?php echo CONF_CURRENCY . number_format(getAmountPayableToWriter($order['bid_price']),2); ?></span></div></li>
						<li><div class="gray-box"><i class="icon earnigs-icon"></i><p><?php echo Utilities::getLabel( 'L_Total_Price' ); ?>:</p><span><?php echo CONF_CURRENCY . number_format(getOrderAmount($order['bid_price'],$order['task_pages']),2); ?></span></div></li>
					</ul>
				</div>			
			<?php } ?>
				<?php if ($order['task_status'] == 3) { ?>
				<div class="padding_30">
					<div class="white-box clearfix">
						<div class="rating-table clearfix">			
							<h4><?php echo Utilities::getLabel( 'L_Rate_&_Review' ); ?></h4>
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td colspan="2">
										<h5><?php if ( intval( $arr_rating[ 'average_rating' ] ) <= 0 ) { echo Utilities::getLabel( 'L_Rate_this_User' ); } else { echo Utilities::getLabel( 'L_Your_Rating' ); } ?></h5>
									</td>
								</tr>
								<?php if ($arr_rating['delivery_rating'] > 0 || $arr_rating['communication_rating'] > 0 || $arr_rating['quality_rating'] > 0) { ?>
								<tr>
									<td><?php echo Utilities::getLabel( 'L_Delivery' ); ?></td>
									<td><div class="ratingsWrap"><span class="p<?php echo ($arr_rating['delivery_rating']*2); ?>"></span></div></td>
								</tr>
								<tr>
									<td><?php echo Utilities::getLabel( 'L_Communication' ); ?></td>
									<td><div class="ratingsWrap"><span class="p<?php echo ($arr_rating['communication_rating']*2); ?>"></span></div></td>
								</tr>
								<tr>
									<td><?php echo Utilities::getLabel( 'L_Quality' ); ?></td>
									<td><div class="ratingsWrap"><span class="p<?php echo ($arr_rating['quality_rating']*2); ?>"></span></div></td>
								</tr>
								<?php } ?>
								<?php if ($arr_posted_review) { ?>
									<tr><td colspan="2"><h5><?php echo Utilities::getLabel( 'L_Your_Review' ); ?></h5></td></tr>
									<tr>
										<td colspan="2">
											<p class="user-review"><?php echo $arr_posted_review['comments']; ?></p>
											<small><?php echo displayDate($arr_posted_review['posted_on'], true, true, CONF_TIMEZONE); ?></small>
										</td>
									</tr>								
								<?php } ?>	
							</table>
							<?php if (!$arr_posted_review) { ?> 
								<div class="rate_control"><?php echo $frm_review->getFormHtml();?></div>
							<?php }?>							
															
						</div>
					</div>
				</div>
				<?php }	else { ?>
				<?php if(User::isWriter()) {	?>						
						<?php if (in_array($order['task_status'], array(0,1,2)) && !User::isUserReqDeactive()) { ?>				
					<div class="white-box clearfix padding_30">
							<?php if((count($file_uploaded) < 1 || $order['cus_req_to_upload_again'] == 1) && $order['task_review_request_status'] != 2) {?>
							<form name="frmUpload" id="frmUpload" action="<?php echo generateUrl('task', 'update_project_file'); ?>" method="post" class="siteForm" enctype="multipart/form-data">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="upload-fils">
									<input type="hidden" name="task_id" value="<?php echo $order['task_id']; ?>">								
									<input type="hidden" name="cus_req_to_upload_again" value="<?php echo $order['cus_req_to_upload_again'];?>">
									<tr><td><input type="file" name="task_files[]" id="task_file" class="file-type" multiple="multiple"></td><td><input type="submit" value="Upload" class="theme-btn"></td></tr>
									<tr><td colspan="2"><p><?php 
										$limit = ini_get( 'upload_max_filesize' );
										echo sprintf( Utilities::getLabel( 'L_Task_File_Upload_Limit' ), $limit );
									?></p></td></tr>
								</table>
							</form>	
							<?php } else { ?>
							<img alt="" src="<?php echo CONF_WEBROOT_URL;?>images/upload_disabled.jpg">
							<div class="layerup"><span class="translayer"></span><span class="txtlayer"><?php echo Utilities::getLabel( 'L_No_More_Upload_Task_File_Restriction' ); ?></span></div>
							<?php }?>
					</div>
						<?php }?>
					<div class="white-box clearfix padding_30">
						<?php echo $frm->getFormHtml(); ?>
					</div>
					<?php }else { ?>
					<div class="padding_30">
						<div class="white-box clearfix ">
							<div class="previewwrap">
								<div class="grid6">								
									<?php 
										if (!empty($ongoing_milestone)) { 
											echo $ongoing_milestone['mile_content'] != '' ? html_entity_decode($ongoing_milestone['mile_content']) : ''; 
										} else { ?>
										<div class="preview_head"><h2><?php echo Utilities::getLabel( 'L_Sample_Preview' ); ?></h2></div><div class="no-preview"><?php echo Utilities::getLabel( 'L_No_Preview_Available' ); ?></div>
									<?php } ?>								
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				<?php }?>

			</div>
			<div class="cols_5">
				<div class="white-box clearfix">
					<div class="chatwrap">
						<div class="gray-box top clearfix">
							<?php if (User::getLoggedUserAttribute('user_id') == $order['bid_user_id']) { ?>
								<figure class="online-user-img"><img src="<?php echo generateUrl('user', 'photo', array($order['task_user_id'],45,45)); ?>"  alt="img"></figure>
							<?php } else { ?>
								<figure class="online-user-img"><img src="<?php echo generateUrl('user', 'photo', array($order['bid_user_id'],45,45)); ?>"  alt="img"></figure>
							<?php } ?>
							<?php
								if (User::getLoggedUserAttribute('user_id') == $order['bid_user_id']) {
									$recipient_id = $order['task_user_id'];										
									echo '<p>' . (strlen($order['user_first_name']) > 18 ? substr($order['user_first_name'],0,18) . '...' : $order['user_first_name']) . '</p>';
								}
								else {
									$recipient_id = $order['bid_user_id'];										
									echo '<p>' . (strlen($order['writer'] > 18) ? substr($order['writer'],0,18) . '...' : $order['writer']) . '</p>';
								}
							?>
							<span class="online_status" data-uid="<?php echo $recipient_id; ?>"></span>
						</div>
						<div class="chatscroll">
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
								<input type="hidden" name="bid_id" value="<?php echo $order['bid_id']; ?>">
								<textarea name="message" id="message" cols="" rows="" placeholder="<?php echo Utilities::getLabel( 'L_Write_your_message_here' ); ?>"></textarea>
								<input type="button" class="theme-btn" name="btn_send" id="btn_send" value="<?php echo Utilities::getLabel( 'L_Send' ); ?>">
							</form>
							<p><?php echo sprintf( Utilities::getLabel( 'L_follow_our_confidentiality_policy' ), generateUrl('cms', 'page',array('confidentiality_policy')) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('input#task_file').click(function(){
		<?php if(count($file_uploaded) < 1 && $order['cus_req_to_upload_again'] != 1){ ?>
		alert('\" <?php echo Utilities::getLabel( 'L_Alert_Mark_Order_Completed_After_File_Upload' ); ?> \"');
		<?php }?>
	});
	
});
</script>