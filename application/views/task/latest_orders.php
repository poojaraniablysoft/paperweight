<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
?>
<div id="body">
  <div class="row whiteBg paddng strip">
	<div class="fix-container">
	  <h2 class="page-title"><?php echo $page_title; ?></h2>
	  <div class="center-align"><?php echo $page_content; ?></div>			
	  <div class="fliter-order">
		<ul class="fliter-by">
		  <li><a><?php echo Utilities::getLabel( 'L_Pages' ); ?><i class="icon ion-chevron-down"></i></a>
			<div class="sub-menu">
			  <ul>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'task_pages',5);"><?php echo Utilities::getLabel( 'L_5_Pages_or_Below' ); ?></a></li>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'task_pages',20);"><?php echo Utilities::getLabel( 'L_5_to_20_Pages' ); ?></a></li>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'task_pages',21);"><?php echo Utilities::getLabel( 'L_20_Pages_or_Above' ); ?></a></li>
			  </ul>
			</div>
		  </li>
		  <li><a><?php echo Utilities::getLabel( 'L_Deadline' ); ?><i class="icon ion-chevron-down"></i></a>
			<div class="sub-menu">
			  <ul>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'deadline',7);"><?php echo Utilities::getLabel( 'L_1_To_7_Days' ); ?></a></li>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'deadline',14);"><?php echo Utilities::getLabel( 'L_8_To_14_Days' ); ?></a></li>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'deadline',21);"><?php echo Utilities::getLabel( 'L_15_To_21_Days' ); ?></a></li>
				<li><a href="javascript:void(0);" onclick="SearchOrder(1,'deadline',28);"><?php echo Utilities::getLabel( 'L_22_To_28_Days' ); ?></a></li>
			  </ul>
			</div>
		  </li>
		  <li><a><?php echo Utilities::getLabel( 'L_Type' ); ?><i class="icon ion-chevron-down"></i></a>
			<div class="sub-menu">
			  <ul>
				<?php foreach($paper_types as $key=>$val) { ?>
					<li><a href="javascript:void(0);" onclick="SearchOrder(1,'pep_type',<?php echo $key;?>);"><?php echo $val;?> </a></li>
				<?php } ?>
			  </ul>
			</div>
		  </li>
		</ul>
	  </div>
	  <?php if(count($arr_orders['orders']) >0) {?>
		<div id="recent_orders">
		  <div class="latest-order">
			<?php foreach($arr_orders['orders'] as $key=>$ele) { ?>
				<div class="comment-sectn">
				  <div class="left-sectn">
					<h2><?php echo $ele['task_topic'];?></h2>
					<p><label><?php echo Utilities::getLabel( 'L_Type' ); ?></label>: <?php echo $ele['paptype_name'];?></p> 
					<div class="time">
						<label><?php echo Utilities::getLabel( 'L_Deadline' ); ?></label>: <?php echo $date = displayDate($ele['task_due_date'],true, true, CONF_TIMEZONE);?><?php print_time_zone(); ?> 
							<font>(<?php echo time_diff($ele['task_due_date']); ?>)</font>
							<span><?php echo Utilities::getLabel( 'L_By' ); ?>: <span><?php echo $ele['user_first_name'].' '.$ele['user_last_name']; ?></span></span>
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
			<?php echo generatePagingStringHtml($arr_orders['page'],$arr_orders['pages']);?>
		  </div>
		</div>
		<?php }else{?>
			<div class="no_result">
				<div class="no_result_icon">
					<?php
						$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
					?>
					<img src="<?php echo $logoUrl;?>">
				</div>
				<div class="no_result_text">
				<h5><?php echo Utilities::getLabel( 'L_Latest_Order_Not_Found' ); ?></h5>
				<p><?php echo Utilities::getLabel( 'L_Try_to_search_with_other_filters' ); ?> <a onclick="SearchOrder(1)" href="javascript:void(0);"><?php echo Utilities::getLabel( 'L_Back' ); ?></a> </p>
				</div>
			</div>
		<?php }?>		
	</div>
  </div>
</div>