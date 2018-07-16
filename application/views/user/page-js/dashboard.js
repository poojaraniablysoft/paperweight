/* $(document).ready(function() {
 
 //When page loads...
 $(".selected_content").hide(); //Hide all content
 $(".normalTab li:first-child").addClass("select").show(); //Activate first tab
 $(".selected_content:first-child").show(); //Show first tab content
 
 //On Click Event
 $(".normalTab li").click(function() {
 
  $(".normalTab li").removeClass("select"); //Remove any "active" class
  $(this).addClass("select"); //Add "active" class to selected tab
  $(".selected_content").hide(); //Hide all tab content
 
  var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
  $(activeTab).fadeIn(); //Fade in the active ID content
  return false;
 });
 
}); */
$('.mob-nav').click(function () {
    $(".db-nav").slideToggle("slow", "linear");
});
$(document).ready(function() {
  /* $('.my-account a').click(function(){
	$(this).toggleClass("active");
	if($(window).width()<990)
	$(this).siblings('.drop-down').slideToggle();
  });  
	 */
	// tabbed content
  $(".tabs_content").hide();
  $(".tabs_content:first").show();
/* if in tab mode */
  $(".responsivetabs li a").click(function() {
    $(".tabs_content").hide();
    var activeTab = $(this).attr("href");
    $(activeTab).fadeIn();	
    $(".responsivetabs li a").removeClass("selected");
    $(this).addClass("selected");
	$(".m_heading").removeClass("m_active");
	$(".m_heading[href^='"+activeTab+"']").addClass("m_active");
  });
	/* if in drawer mode */
  $(".m_heading").click(function() {
	$(".tabs_content").hide();
	var d_activeTab = $(this).attr("href"); 
	$(d_activeTab).fadeIn();

	$(".m_heading").removeClass("m_active");
	$(this).addClass("m_active");

	$(".responsivetabs li a").removeClass("selected");
	$(".responsivetabs li a[href^='"+d_activeTab+"']").addClass("selected");
  });


/* Extra class "tab_last" 
 to add border to right side
 of last tab */
  $('.responsivetabs li a').last().addClass("tab_last");  
});
    
$(window).scroll(function() {
    if ($(this).scrollTop() > 1){  
        $('div.db-content>div.heading').addClass("sticky");
    }
    else{
        $('div.db-content>div.heading').removeClass("sticky");
    }
});