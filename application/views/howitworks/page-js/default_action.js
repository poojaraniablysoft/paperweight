$(document).ready(function() {
	 $('.fancybox').click(function(){
		
		var href = $(this).attr('href');
		var gallary = $(this).data('fancybox-group');
		var arr = [];
		
		$.each($('.fancybox'), function(){
			var grHref = $(this).attr('href');
			var grGallary = $(this).data('fancybox-group');
			if(grGallary == gallary){
				if(grHref == href){
					arr.push({'href' : href});
				}else{
					arr.push(href);
				}
			}
		});
		
		if(arr.length > 0){
			$.fancybox(arr,{'type': 'image'});
		}
		return false;
		
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