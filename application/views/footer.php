<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>

<?php if (User::isUserLogged() && (!User::isWriter() || User::writerCanView()) || (User::writerCanBid() && $arr[2]!='signup_message')) { 
/* ?>
<div class="copyright">&copy; 2015 <?php echo CONF_WEBSITE_NAME;?>.com.  All rights reserved </div>
 </div>
</div>*/
?>
<script type="text/javascript">
	$('.mob-nav').click(function () {
		$('.my-account a').toggleClass("active");
		$('.my-account a').siblings('.drop-down').slideUp();
		$(".db-nav").slideToggle("slow", "linear");
	});
	
	$(window).scroll(function() {
		if ($(this).scrollTop() > 1){  
			$('div.db-content>div.heading').addClass("sticky");
		}
		else{
			$('div.db-content>div.heading').removeClass("sticky");
		}
	});	
</script>
<?php } /* </body>
</html>
 exit(0) ; */ ?>
<!-- container --> 

<?php $footer = getFooterMenu();
//echo "<pre>".print_r($footer['2'],true)."</pre>";
?>
<!-- footer-->
<footer id="footer" class="light-graybg">
	<div class="wrapgray">
		<div class="fix-container">
		  <div class="left-penal">
			<ul class="socialLinks">
				<li>connect with us</li>
				<li><a href="<?php echo CONF_FACEBOOK;?>"><i class="icon ion-social-facebook"></i></a></li>
				<li><a href="<?php echo CONF_TWITTER;?>"><i class="icon ion-social-twitter"></i></a></li>
				<li><a href="<?php echo CONF_LINKEDIN;?>"><i class="icon ion-social-linkedin"></i></a></li>
				<li><a href="<?php echo CONF_GOOGLEPLUS;?>"><i class="icon ion-social-googleplus"></i></a></li>
			</ul>
		  </div>
		  <div class="right-side"><img src="<?php echo CONF_WEBROOT_URL;?>images/paypal2.png" alt="img"></div>	  
		</div>
	</div>
	<div class="fix-container">
		<div class="footer-wrap middfooter">
			<div class="footer-colm">
			  <h2 class="trigger">Company</h2>
			  <ul class="footer-menu">
			  <?php foreach($footer['1'] as $value) {?>
				<li><a href="<?php echo generateUrl('cms','page',array($value['cmspage_name']));?>"><?php echo $value['cmspage_title'];?></a></li>
			  <?php }?>
			  </ul>
			</div>
			<div class="footer-colm">
				<h2 class="trigger">Services</h2>
				<ul class="footer-menu">
					<?php foreach($footer['2'] as $value) {?>
					<li><a href="<?php echo generateUrl('cms','page',array($value['cmspage_name']));?>"><?php echo $value['cmspage_title'];?></a></li>
				  <?php }?>
				</ul>
			</div>
			<div class="footer-colm">
				<h2 class="trigger">Quick Links</h2>
				<ul class="footer-menu">
					<?php foreach($footer['3'] as $value) {?>
					<li><a href="<?php echo generateUrl('cms','page',array($value['cmspage_name']));?>"><?php echo $value['cmspage_title'];?></a></li>
					<?php }?>
					<li><a href="<?php echo generateUrl('writers/');?>">Find Writers by Paper Type</a></li>
					<li><a href="<?php echo generateUrl('writers-discipline/');?>">Find Writers by Discipline</a></li>	
				</ul>
			</div>
			<div class="footer-colm">
				<h2 class="trigger">Quick Links</h2>
				<ul class="footer-menu">
					<?php foreach($footer['4'] as $value) {?>
					<li><a href="<?php echo generateUrl('cms','page',array($value['cmspage_name']));?>"><?php echo $value['cmspage_title'];?></a></li>
					<?php }?>
				</ul>
			</div>
			<div class="footer-logo">
				<ul class="contact-info">
				<li><a href="<?php echo generateUrl('');?>">
					<?php
						$logoUrl = generateUrl('image', 'logo', array( CONF_WEBSITE_FOOTER_LOGO ));
					?>
					<img src="<?php echo $logoUrl;?>" alt="Footer Logo"></a> </li>
				<li><a href="mailto:<?php echo CONF_CONTACT_EMAIL_TO;?>"><i class="icon ion-ios-email"></i><?php echo CONF_CONTACT_EMAIL_TO;?></a></li>
				<li><i class="icon ion-iphone"></i><?php echo CONF_CONTACT_PHONE;?></li>
				</ul>
			 </div>
		</div>
		<div class="footer-botom">
			 <?php echo sprintf(CONF_FOOTER_COPYRIGHT,date("Y"), CONF_WEBSITE_NAME)?>	
		</div>
	</div>    
</footer>

<!--wrapper end here--> 
<!-- for  slider --> 
<!-- for main navigations --> 
<script type="text/javascript">
$(document).ready(function() {		 	 			 
  $('#pull').click(function(){	 
	$('.my-account a').toggleClass("active");
	$('.my-account a').siblings('.drop-down').slideUp();	 
	$(this).toggleClass("active");
	$('#menu').slideToggle(600);
  });	  
  $('.my-account a').click(function(){
	if($(window).width()<990){
		$('#pull').toggleClass("active");
		$('#menu').slideUp(600);
		$('.db-nav').slideUp(600);
	}
	$(this).toggleClass("active");
	 //if($(window).width()<990)
	$(this).siblings('.drop-down').slideToggle();
  });
	
  $('#menu > li a').click(function(){
	  $(this).toggleClass("active");
	  if($(window).width()<1050)
	  $('.sub-menu ').slideToggle();
  });	

  $('.footer-colm h2.trigger').click(function(){
	  $(this).toggleClass("active");
	  if($(window).width()<990)
	  $(this).siblings('.footer-menu').slideToggle();
  }); 	  
});
</script>
<!-- End Document
================================================== -->
<?php echo CUSTOM_HTML_FOOTER; ?>
</body>
</html>
