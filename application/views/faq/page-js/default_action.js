$(document).ready(function() {
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