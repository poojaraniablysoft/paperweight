$(document).ready(function() {
	$(".selected_content").hide();
	
	switch (filter) {
		case 'history':
			$("a[href='#tab1']").parent('li').addClass("select");
			$("div#tab1").show();
			break;
		case 'load_funds':
			$("a[href='#tab2']").parent('li').addClass("select");
			$("div#tab2").show();
			break;
		case 'withdraw_funds':
			$("a[href='#tab3']").parent('li').addClass("select");
			$("div#tab3").show();
			break;
	}
 
	$(".normalTab li").click(function() {
		$(".normalTab li").removeClass("select");
		$(this).addClass("select");
		$(".selected_content").hide();
 
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn();
		return false;
	}); 
	
	$('.funds h2.trigger').click(function(){
		  $(this).toggleClass("active");
		  //if($(window).width()<990)
		$(this).siblings('.funds .blockpayments').slideToggle();
  });  
});