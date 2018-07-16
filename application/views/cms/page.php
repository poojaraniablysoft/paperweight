<?php
if (!SYSTEM_INIT) die('Invalid Access'); // avoid direct access.
include CONF_THEME_PATH . 'navigation_header.php';
/* if(!User::isUserLogged() || (User::isWriter() == true && (!User::writerCanView() || (!User::writerCanBid() && $arr[2]=='signup_message')))){
}else{
//	include CONF_THEME_PATH . 'page_bar.php';
} */
?>

<div id="body">
	<div class="row  greyBg paddng strip">
		<div class="fix-container">
			<h2 class="page-title"><?=$arr_listing['cmspage_title']?></h2>
			<div class="contact-detali"></div>
			<?php
				echo Message::getHtml(); 
				echo html_entity_decode($arr_listing['cmspage_content']);
			?>
		</div>	
	</div>
</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$('.fancybox').fancybox();
				/* $('.footer-colm h2.trigger').click(function(){
				  $(this).toggleClass("active");
				  if($(window).width()>990)  $(this).siblings('.footer-menu').slideToggle();
				});	 */
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