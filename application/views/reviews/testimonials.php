<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
//include CONF_THEME_PATH . 'page_bar.php';
include CONF_THEME_PATH . 'navigation_header.php';
global $order_status;

//echo "<pre>".print_r($arr_tesimonials,true)."</pre>";exit;
//echo "<pre>".print_r($feedreceived,true)."</pre>";
?>
  <!-- body -->
<div id="body">
	
	<div class="row  greyBg paddng strip">
	  <div class="fix-container">
		<h2 class="page-title"><?php echo Utilities::getLabel( 'L_Testimonials' ); ?></h2>
		<div class="contact-detali"></div>
		<div class="how-it-work">  
		
		  <div class="review-tab" id="horizontalTab">
			
			<div class="resp-tabs-container grid_13">
			  <div class="rivews-box tab_content" id="tabs1">
			   <?php foreach($arr_tesimonials as $value) {?>
					<div class="our-works">
						<div class="grid-1">
						  <h3><span class="step"><?php echo '#'.$value['task_ref_id']; ?></span><?php echo $value['task_topic'].', '.$value['paptype_name'].','.$value['task_pages'].' pages';?></h3>
						  <div class="user-cmnt"><p><?php echo $value['rev_comments'];?></p></div>
						  
						</div>
						<div class="grid-2">
						  <div class="user-prfl"> 
							<img src="<?php echo generateUrl('user', 'photo', array($value['user_id'],180,180));?>" alt="img">							
						  </div>
						  <div class="user_info">
							<span><?php echo $value['reviewee'];?></span>
							<div class="ratingsWrap ratingSection"><span class="p<?php echo ($value['user_rating']*2); ?>"></span></div>
							<span class="txtnrml"><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $value['complete_order'];?></span>
						  </div>
						</div>
					</div>
				<?php }
					if(count($arr_tesimonials) < 1) {
						echo '<div class="no_reviews">' . Utilities::getLabel( 'L_No_Review_Found' ) . '</div>';
					}
				?>
			  </div>			  
			  <!--reviews   --> 
			  
			</div>
		  </div>
		</div>
	  </div>
	</div>
</div>

