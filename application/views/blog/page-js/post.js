 function searchPost(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#comment-post-list'));
	callAjax(generateUrl('blog', 'listCommentsOfPost'), data, function(t){
		$('#comment-post-list').html(t);
	});
}

function listPages(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchPost(frm);
}
$(document).ready(function(){
	showHtmlElementLoading($('#comment-post-list'));
	var post_id = $('#comment_post_id').val();
	var data = 'post_id='+post_id+'&page=1';
	callAjax(generateUrl('blog', 'listCommentsOfPost'), data, function(t){
		$('#comment-post-list').html(t);
	});
	
	var sync1 = $("#post_slider");
	var sync2 = $("#post_carousel");
		
	sync1.owlCarousel({
		navigation : true,
		pagination : false,
		slideSpeed : 300,
		paginationSpeed : 400,
		mouseDrag : false,
		touchDrag : false,
		autoPlay : 5000,
		singleItem:true
	});
	 
	sync2.owlCarousel({
		items : 5,
		/* itemsDesktop : [1199,10], */
		/* itemsDesktopSmall : [979,10], */
		itemsTablet : [768,5],
		itemsMobile : [767,3],
		pagination:true,
		navigation : true,
		responsiveRefreshRate : 100,
		afterInit : function(el){
		el.find(".owl-item").eq(0).addClass("synced");
		}
	});
	 
	function syncPosition(el){
		var current = this.currentItem;
		$("#post_carousel")
		.find(".owl-item")
		.removeClass("synced")
		.eq(current)
		.addClass("synced")
		if($("#post_carousel").data("owlCarousel") !== undefined){
		center(current)
		}
	}
	 
	$("#post_carousel").on("click", ".owl-item", function(e){
		e.preventDefault();
		var number = $(this).data("owlItem");
		sync1.trigger("owl.goTo",number);
	});
	 
	function center(number){
		var sync2visible = sync2.data("owlCarousel").owl.visibleItems;
		var num = number;
		var found = false;
		for(var i in sync2visible){
			if(num === sync2visible[i]){
				var found = true;
			}
		}
		 
		if(found===false){
			if(num>sync2visible[sync2visible.length-1]){
				sync2.trigger("owl.goTo", num - sync2visible.length+2)
			}else{
				if(num - 1 === -1){
				num = 0;
				}
				sync2.trigger("owl.goTo", num);
			}
		} else if(num === sync2visible[sync2visible.length-1]){
			sync2.trigger("owl.goTo", sync2visible[1])
		} else if(num === sync2visible[0]){
			sync2.trigger("owl.goTo", num-1)
		}
	} 
	
});
