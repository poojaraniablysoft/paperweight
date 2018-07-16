<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
global $order_status;
//echo "<pre>".print_r($arr_feedback,true)."</pre>";exit;
 ?>
 <script type="text/javascript">
	var filter = "<?php echo $filter; ?>";
</script>
<div id="body">
	<div class="row  greyBg paddng strip">
	  <div class="fix-container">
		<h2 class="page-title"><?php echo Utilities::getLabel( 'L_User_Reviews_For' ); ?></h2>
		<div class="contact-detali"></div>
		<div class="how-it-work">  
		
		  <div class="review-tab" id="horizontalTab">
			<div class="border-bottom">
				<ul class="resp-tabs-list">
				  <li class="active"><a class="toggle-button" href="#tabs1"><figure class="image"><img src="<?php echo CONF_WEBROOT_URL;?>images/writer-icon.png" alt="img"></figure> 
					<?php echo Utilities::getLabel( 'L_Writer' ); ?></a></li>
				  <li><a class="toggle-button" href="#tabs2"><figure class="image"><img src="<?php echo CONF_WEBROOT_URL;?>images/customer-icon.png" alt="img"></figure>
					<?php echo Utilities::getLabel( 'L_Customer' ); ?></a></li>
				</ul>
			</div>
			<div class="resp-tabs-container grid_13">
			  <div class="rivews-box tab_content" id="tabs1">
			    <?php foreach($arr_reviews['review'] as $value) { ?>
					<div class="our-works">
						<div class="grid-1">
						  <h3><span class="step"><?php echo '#'.$value['task_ref_id']; ?></span><?php echo $value['task_topic'].', '.$value['paptype_name'].','.$value['task_pages'].' pages';?></h3>
						  <div class="user-cmnt"><p><?php echo $value['comments'];?></p></div>
						  
						</div>
						<div class="grid-2">
						  <div class="user-prfl"> 
							<a href="<?php echo generateUrl('writer',seoUrl($value['reviewee']));?>"><img src="<?php echo generateUrl('user', 'photo', array($value['rev_reviewee_id'],180,180));?>" alt="img"></a>							
						  </div>
						  <div class="user_info">
							<a href="<?php echo generateUrl('writer',seoUrl($value['reviewee']));?>"><span><?php echo $value['reviewee'];?></span></a>
							<div class="ratingsWrap ratingSection"><span class="p<?php echo ($value['user_rating']*2); ?>"></span></div>
							<span class="txtnrml"><?php echo Utilities::getLabel( 'L_Completed_orders' ); ?> <?php echo $value['complete_order'];?></span>
						  </div>
						</div>
					</div>
				<?php }
					if(count($arr_reviews['review']) < 1) { ?>
					<div class="no_result">
						<div class="no_result_icon">
							<?php
								$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
							?>
							<img src="<?php echo $logoUrl;?>">
						</div>
						<div class="no_result_text">
							<h5><?php echo Utilities::getLabel( 'L_No_Review_found' ); ?></h5>
						</div>
					</div>
				<?php }
				?>
			  </div>
			  <div class="rivews-box tab_content" id="tabs2">
				<?php foreach($arr_feedback['feedbacks'] as $value) {?>
					<div class="our-works">
						<div class="grid-1">
						  <h3><span class="step"><?php echo '#'.$value['task_ref_id']; ?></span><?php echo $value['task_topic'].', '.$value['paptype_name'].','.$value['task_pages'].' pages';?></h3>
						  <div class="user-cmnt"><p><?php echo $value['comments'];?></p></div>
						</div>
						<div class="grid-2">
						  <div class="user-prfl"> 
							<img src="<?php echo generateUrl('user', 'photo', array($value['rev_reviewee_id'],180,180));?>" alt="img">							
						  </div>
						  <div class="user_info">
							<span><?php echo $value['reviewee'];?></span>
							<div class="ratingsWrap ratingSection"><span class="p<?php echo ($value['user_rating']*2); ?>"></span></div>
							<span class="txtnrml"><?php echo Utilities::getLabel( 'L_Completed_orders ' ); ?> <?php echo $value['complete_order'];?></span>
						  </div>
						</div>
					</div>
				<?php }
					if(count($arr_feedback['feedbacks']) < 1) { ?>
					<div class="no_result">
						<div class="no_result_icon">
							<?php
								$logoUrl = generateUrl('image', 'logo', array(  CONF_LOGIN_LOGO , 'FULL',));
							?>
							<img src="<?php echo $logoUrl;?>">
						</div>
						<div class="no_result_text">
							<h5><?php echo Utilities::getLabel( 'L_No_Review_found' ); ?></h5>
						</div>
					</div>
				<?php }
				?>
			  </div>
			  <!--reviews   --> 
			  
			</div>
		  </div>
		</div>
	  </div>
	</div> 
<script type="text/javascript">
	$(document).ready(function() {
		$('.footer-colm h2.trigger').click(function(){
		  $(this).toggleClass("active");
		 /*  if($(window).width()<990)
		  $(this).siblings('.footer-menu').slideToggle(); */
		});	
		$(".accordion h4:first").addClass("active");
		$(".accordion p:not(:first)").hide();

		$(".accordion h4").click(function(){
			$(this).next("p").slideToggle("slow").siblings("p:visible").slideUp("slow");
			$(this).toggleClass("active");
			$(this).siblings("h4").removeClass("active");
		});
		
		  
		$(".rivews-box").css({'display':'none','z-index':'0'}); //Hide all content
		$(".review-tab .resp-tabs-list li:first").addClass("active"); //Activate first tab
		$(".rivews-box:first").css({'display':'block','z-index':'1'}); //Show first tab content

		$(".review-tab .resp-tabs-list li").click(function() {				
			$(".review-tab .resp-tabs-list li").removeClass("active"); //Remove any "active" class
			$(this).addClass("active"); //Add "active" class to selected tab
			$(".rivews-box").css({'display':'none','z-index':'0'}); //Hide all tab content
			 
			var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
			$(activeTab).css({'display':'block','z-index':'1'}); //Fade in the active ID content
			return false;
		});
	}); 	
</script> 