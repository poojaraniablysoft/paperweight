$(document).ready(function() {
	$(".selected_content").hide(); //Hide all content
	
	switch (filter) {
		case 'posted':
			$(".normalTab li").removeClass("select");
			$("a[href='#tab1']").parent('li').addClass("select");
			$("div#tab1").show();
			break;
		case 'received':
			$(".normalTab li").removeClass("select");
			$("a[href='#tab2']").parent('li').addClass("select");
			$("div#tab2").show();
			break;
	}
	
	//On Click Event
	$(".normalTab li").click(function() {
		$(".normalTab li").removeClass("select"); //Remove any "active" class
		$(this).addClass("select"); //Add "active" class to selected tab
		$(".selected_content").hide(); //Hide all tab content
 
		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	}); 
});

/* function getFeedback(page) {
	callAjax(generateUrl('reviews', 'get_feedback'), 'page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		
		if (ans.status === false) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			var i=1;
			$.each(ans.data.feedback, function(index,arr) {
				str += '<div class="top">';
				str += '<h2><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">#' + arr["task_ref_id"] + '</a>';
				str += '<span> ' + arr["task_topic"] + '</span>'; 
				str += '<span class="lightxt"> (' + arr["paptype_name"] + ' - ' + arr["task_pages"] + ' pages)</span>';
				str += '</h2>';
				str += '</div>';
				str += '<div class="middle clearfix">'; 
				str += '<div class="grid_1">'
				str += '<div class="userinfo">';
				str += '<div class="boxleft"><div class="photo"><img src="' + generateUrl("user", "photo", [arr["rev_reviewer_id"]]) + '" alt=""></div></div>';
				str += '<div class="boxright">';
				str += '<span class="nameuser" title="' + arr["reviewee"] + '">';
				str += arr["reviewee"].length > 15 ? arr["reviewee"].substr(0,15) + '...' : arr["reviewee"];
				str += '</span>';
				str += '<div class="ratingsWrap ratingSection">';
				str += '<span class="p' + (arr["user_rating"]*2) + '"></span>';
				str += '</div>';
				str += '<p>Completed orders ' + arr['orders_completed'] + '</p>';
				str += '</div>';
				str += '</div></div>';
				
				str += '<div class="grid_2">';
				str += '<div class="bar">';
				str += '<div class="ratingsWrap ratingSection"> <span class="p' + (arr['posted_rating']["average_rating"]*2) + '"></span></div>';
				str += '<h6>Customer\'s Feedback</h6>';
				str += '</div>';
				str += '<div class="textsection">';
				str += '<p>' + arr['comments'] + '</p>';
				str += '</div></div></div>';					
			});
			
			$('#reviews_history').html(str);
			$('.pagination').html(ans.data.str_paging);
		}
	});
}
 */
 function getFeedback(page,user_type) {
	callAjax(generateUrl('reviews', 'get_feedback'), 'page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		var divclass ='' ;
		
		if (ans.status === false) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			var i=1;
			$.each(ans.data.feedback, function(index,arr) {
				if( i >1)
				{
					divclass = 'padding_30';
				}
				
				str += '<div class="white-box review-page clearfix'+divclass+'" >';
				str += '<h2><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">#' + arr["task_ref_id"] + '</a>';
				str += '<span> ' + arr["task_topic"] + '</span>'; 
				str += '<span class="lightxt"> (' + arr["paptype_name"] + ' - ' + arr["task_pages"] + ' pages)</span>';
				str += '</h2>';
				
				str += '<div class="border-box">'; 
				str += '<div class="sectionphoto">'
				//str += '<div class="userinfo">';
				str += '<figure class="photo"><img src="' + generateUrl("user", "photo", [arr["rev_reviewer_id"]]) + '" alt=""></figure>';
				str += '<div class="ratingsWrap ">';
				str += '<span class="p' + (arr["user_rating"]*2) + '"></span>';
				str += '</div>';
				str += '</div>';
				str += '<div class="writer-review">';
				str += '<div class="ratingsWrap "> <span class="p' + (arr['posted_rating']["average_rating"]*2) + '"></span></div>';
				str += (user_type == 1)?'<h3>Customer\'s Feedback</h3>':'<h3>Writer\'s Feedback</h3>';
				str += '<p>' + arr['comments'] + '</p>';
				
				str += '</div>';
				str += '</div>';
				str += '</div>';
				i = i+1;					
			});
			
			$('#reviews_history').html(str);
			$('.pagination').html(ans.data.str_paging);
			$('html, body').animate({scrollTop: $("#reviews_history").offset().top-150}, 700);
		}
	});
}

function getReviews(page) {
	callAjax(generateUrl('reviews', 'get_reviews'), 'page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		
		if (ans.status === false) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			$.each(ans.data.reviews, function(index,arr) {
				str += '<div class="top">';
				str += '<h2><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">#' + arr["task_ref_id"] + '</a>';
				str += '<span> ' + arr["task_topic"] + '</span>'; 
				str += '<span class="lightxt"> (' + arr["paptype_name"] + ' - ' + arr["task_pages"] + ' pages)</span>';
				str += '</h2>';
				str += '</div>';
				str += '<div class="middle clearfix">'; 
				str += '<div class="grid_1">'
				str += '<div class="userinfo">';
				str += '<div class="boxleft"><div class="photo"><img src="' + generateUrl("user", "photo", [arr["rev_reviewee_id"]]) + '" alt=""></div></div>';
				str += '<div class="boxright">';
				str += '<span class="nameuser" title="' + arr["reviewee"] + '">';
				str += arr["reviewee"].length > 15 ? arr["reviewee"].substr(0,15) + '...' : arr["reviewee"];
				str += '</span>';
				str += '<div class="ratingsWrap ratingSection">';
				str += '<span class="p' + (arr["user_rating"]*2) + '"></span>';
				str += '</div>';
				str += '<p>Completed orders ' + arr['orders_completed'] + '</p>';
				str += '</div>';
				str += '</div></div>';
				
				str += '<div class="grid_2">';
				str += '<div class="bar">';
				str += '<div class="ratingsWrap ratingSection"> <span class="p' + (arr['posted_rating']["average_rating"]*2) + '"></span></div>';
				str += '<h6>Writer\'s Comments</h6>';
				str += '</div>';
				str += '<div class="textsection">';
				str += '<p>' + arr['comments'] + '</p>';
				str += '</div></div></div>';
			});
			
			$('div#tab1').find('.modulerepeat').html(str);
			$('div#tab1').find('.paging').html(ans.data.str_paging);
		}
	});
}