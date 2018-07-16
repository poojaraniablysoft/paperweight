$(document).ready(function() {
	$(".toggle_container").show();
	$(".toggles").toggle(function(){
		$(this).addClass("active"); 
		}, function () {
		$(this).removeClass("active");
	});
	$(".toggles").click(function(){
		$(this).next(".toggle_container").slideToggle("slow");
	});

	var el = document.querySelectorAll("ul.navigations > li")[0];
	var box = el.getBoundingClientRect();
	$('.navigations .fixedWrap').css({'margin-left': box.left +'px', 'margin-right': box.left +'px', 'position':'relative'});
	

	// Do our DOM lookups beforehand
	var nav_container = $(".navs");
	var nav = $(".navWrap");

	nav_container.waypoint({
		handler: function(event, direction) {
			nav.toggleClass('sticky', direction=='down');
			
			if (direction == 'down') nav_container.css({ 'height':nav.outerHeight() });
			else nav_container.css({ 'height':'auto' });
		},
		offset: 15
	});
	
	var leftnav_container = $("#body");
	var leftnav = $(".leftPanel");
	$(document).scroll(function(){
		if(leftnav_container.offset().top < $(this).scrollTop()){
			leftnav.css({'position':'fixed', 'top': (leftnav_container.offset().top - $('.topBar').height() - 5) + 'px'});	
		}else{
			leftnav.css({'position':'relative','top': 0});	
		}
	});
	
});
