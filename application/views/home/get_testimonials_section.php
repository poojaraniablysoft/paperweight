<?php// echo "<pre>".print_r($arr_feedback,true)."</pre>";exit;?>

<div class="row greyBg paddng">
	<div class="fix-container">
		<div class="reviews">
			<h2 class="page-title"><?php echo Utilities::getLabel( 'L_Reviews' ); ?></h2>
			<p><?php echo Utilities::getLabel( 'L_Reviews_Description' ); ?></p>
			<div class="review-tab" id="horizontalTab">
				<ul class="resp-tabs-list">
					<li><a class="toggle-button" href="#tabs1"><span class="testi_cl_img"><img alt="img" src="<?php echo CONF_WEBROOT_URL;?>images/customer-icon.png" ></span>&nbsp;<?php echo Utilities::getLabel( 'L_Clients_review' ); ?></a></li>
					<li><a class="toggle-button" href="#tabs2"><span class="testi_cl_img"><img alt="img" src="<?php echo CONF_WEBROOT_URL;?>images/writer-icon.png"></span>&nbsp;<?php echo Utilities::getLabel( 'L_Writers_review' ); ?></a></li>
				</ul>
				<div class="resp-tabs-container">
					<div class="rivews-box tab_content" id="tabs1">
					
						<div class="slider_roundthumbs slideer1">
							<div class="viewport">
								<ul class="overview">
									<?php foreach ($arr_reviews['review'] as $key=>$ele) { ?>
										<li>
											<div class="slidecontent">
												<div class="img-sctn">
													<div class="client-img"> <img src="<?php echo generateUrl('user', 'photo', array($ele['rev_reviewee_id']));?>"/></div>
												</div>
												<div class="txt-sctn">
													<p>
													<?php if(strlen($ele['comments'])>=250){
														echo substr($ele['comments'],0,250) . '...';
													} else {
														echo $ele['comments'];
													} ?>
													</p>
												<span><strong><?php echo $ele['customer'];?></strong></span> 
												</div>
											</div>
										</li>
									<?php } ?>
								</ul>
								</div>
								<ul class="bullets">
									<?php
										$i = 0;
									foreach ($arr_reviews['review'] as $key=>$ele) { 
										echo '<li><a href="#" class="bullet" data-slide="'.$i++.'">'.$i.'</a></li>';
										
									} ?>									
								</ul>
							
						</div>								
					</div>
					<div class="rivews-box tab_content" id="tabs2">
					
						<div class="slider_roundthumbs slideer2">
							<div class="viewport">
								<ul class="overview">
									<?php foreach ($arr_feedback['feedbacks'] as $key=>$ele) { ?>
										<li>
											<div class="slidecontent">
												<div class="img-sctn">
													<div class="client-img"> <img src="<?php echo generateUrl('user', 'photo', array($ele['rev_reviewee_id'],100,100));?>"/> </div>
												</div>
												<div class="txt-sctn">
													<p>
													<?php if(strlen($ele['comments'])>=250){
														echo substr($ele['comments'],0,250) . '...';
													} else {
														echo $ele['comments'];
													} ?>
													</p>
												<span><strong><?php echo $ele['reviewee'];?></strong></span> </div>
											</div>
										</li>									
									<?php } ?>
								</ul>
								</div>
								<ul class="bullets">
									<?php
										$i = 0;
									foreach ($arr_feedback['feedbacks'] as $key=>$ele) { 
										echo '<li><a href="#" class="bullet" data-slide="'.$i.'">'.$i.'</a></li>';
										$i++;
									} ?>									
								</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo CONF_WEBROOT_URL;?>js/jquery.tinycarousel.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		//When page loads...
		$(".tab_content").hide(); //Hide all content
		$(".resp-tabs-list li:first-child").addClass("active").show(); //Activate first tab
		$(".tab_content:first-child").show(); //Show first tab content
		
		//On Click Event
		$(".resp-tabs-list li").click(function() {
		
			$(".resp-tabs-list li").removeClass("active"); //Remove any "active" class
			$(this).addClass("active"); //Add "active" class to selected tab
			$(".tab_content").hide(); //Hide all tab content
			 
			var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
			$(activeTab).fadeIn(); //Fade in the active ID content
			return false;
		});
		
		$('.slider_roundthumbs').tinycarousel({
		bullets  : true,
		infinite : false,
		interval : true,
		intervalTime : 6000
		});			
	});
</script>