<?php if(count($arr_orders['orders']) > 0) {?>
<div class="latest-order">
<?php foreach($arr_orders['orders'] as $key=>$ele) { ?>
	<div class="comment-sectn">
	  <div class="left-sectn">
		<h2><?php echo $ele['task_topic'];?></h2>
		<p><label><?php echo Utilities::getLabel( 'L_Type' ); ?></label>: <?php echo $ele['paptype_name'];?></p> 
		<div class="time">
			<label><?php echo Utilities::getLabel( 'L_Deadline' ); ?></label>: <?php echo $date = displayDate($ele['task_due_date'],true, true,CONF_TIMEZONE);?> 
			<?php print_time_zone();?>
				<font>(<?php echo time_diff($ele['task_due_date']); ?>)</font>
				<span><?php echo Utilities::getLabel( 'L_By' ); ?>: <a href="#"><?php echo $ele['user_first_name'].' '.$ele['user_last_name']; ?></a></span>
				<span><?php echo '#'.$ele['task_ref_id'];?></span>
		</div> 
	  </div>
	  <div class="middle-sectn">
		<div class="grydtn-btn">
		<span><?php echo $ele['task_pages'];?></span>
		<p><?php echo Utilities::getLabel( 'L_Pages' ); ?></p>
		</div>
		<div class="grydtn-btn">
		<span><?php echo ($ele['num_bid'] > 0) ?$ele['num_bid']:0;?></span>
		<p><?php echo Utilities::getLabel( 'L_Bids' ); ?></p>
		</div>
	  </div>
	  <div class="right-sectn">
		<p><?php echo Utilities::getLabel( 'L_Budget' ); ?>: <?php echo CONF_CURRENCY.number_format($ele['task_budget'],2);?></p>
		<a  href="javascript:void(0);" onclick="return signIn('<?php echo $ele['task_id'];?>')" class="greenBtn"><?php echo Utilities::getLabel( 'L_Bid_on_this_Order' ); ?></a>
	  </div>
	</div>
<?php }?>
</div>

<div class="pagination">
<?php echo generatePagingStringHtml($arr_orders['page'],$arr_orders['pages'],'SearchOrder(xxpagexx,\''.$filter.'\','.$filter_value.')');?>
</div>
<?php }else {?>
	<div class="no_result">
		<div class="no_result_icon">
			<?php
				$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
			?>
			<img src="<?php echo $logoUrl;?>">
		</div>
		<div class="no_result_text">
		<h5><?php echo Utilities::getLabel( 'L_No_Latest_Order_found_with_search_criteria' ); ?></h5>
		<p><?php echo Utilities::getLabel( 'L_Try_to_search_with_other_filters' ); ?> <a href="javascript:void(0);" onclick="SearchOrder(1)"><?php echo Utilities::getLabel( 'L_Back' ); ?></a> </p>
		</div>
	</div>
<?php }?>
