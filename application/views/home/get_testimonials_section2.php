<?php// echo "<pre>".print_r($arr_feedback,true)."</pre>";exit;?>

<div class="row greyBg paddng">
	<div class="fix-container">
		<div class="reviews">
			<h2 class="page-title"><?php echo Utilities::getLabel( 'L_Reviews' ); ?></h2>
			<p><?php echo Utilities::getLabel( 'L_Reviews_Description' ); ?></p>
			<div class="review-tab" id="horizontalTab">
				<ul class="resp-tabs-list">
					<li><a class="toggle-button" href="#tabs1"><i class="icon ion-ios-contact"></i><?php echo Utilities::getLabel( 'L_Clients_review' ); ?></a></li>
					<li><a class="toggle-button" href="#tabs2"><i class="icon ion-ios-compose"></i><?php echo Utilities::getLabel( 'L_Writers_review' ); ?></a></li>
				</ul>
				<div class="resp-tabs-container">
					<div class="rivews-box tab_content" id="tabs1">
						<ul id="client" class="bxslider">
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
					<div class="rivews-box tab_content" id="tabs2">
						<ul id="writer" class="bxslider">
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
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo CONF_WEBROOT_URL;?>js/slick.js"></script>  
<script>
    $(document).ready(function(){
      $('#client').slick({
          infinite: true,
          slidesToShow: 1,
          slidesToScroll: 1,
		  dots: true,
		  arrows: false
     });

	$('#writer').slick({
          infinite: true,
          slidesToShow: 1,
          slidesToScroll: 1,
		  dots: true,
		  arrows: false
     });
	 
	  $(".rivews-box").css({'display':'none'}); //Hide all content
	  $(".review-tab .resp-tabs-list li:first").addClass("active"); //Activate first tab
	  $(".rivews-box:first").css({'display':'block'}); //Show first tab content

	  $(".review-tab .resp-tabs-list li").click(function() {
			/* client.destroySlider();
			writer.destroySlider();
			client.reloadSlider();
			writer.reloadSlider(); */
			
		  $(".review-tab .resp-tabs-list li").removeClass("active"); //Remove any "active" class
		  $(this).addClass("active"); //Add "active" class to selected tab
		  $(".rivews-box").css({'display':'none'}); //Hide all tab content
		 
		  var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		  $(activeTab).css({'display':'block'}); //Fade in the active ID content
		  return false;
	  });
	 
    });
</script>    

