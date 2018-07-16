function loadReviews(user_id,disp_id) {
	callAjax(generateUrl('reviews','search_reviews'),'user_id= ' + user_id +'&disp_id=' + disp_id,function(t){
		var ans = parseJsonData(t);
		var str = '';
		if(ans.status === 0) {
			$.facebox('Oops! Internal error.');
		}
		str += t;
		$('#reviews_list').html(t);
	});
}

function loadReviewsByRatings(user_id,rating) {
	callAjax(generateUrl('reviews','search_reviews'),'user_id= ' + user_id +'&rating=' + rating,function(t){
		var ans = parseJsonData(t);
		var str = '';
		if(ans.status === 0) {
			$.facebox('Oops! Internal error.');
		}
		str += t;
		$('#reviews_list').html(t);
	});
}

function chooseWriter(user_id) {
	var str = '';
	if(isUserLogged == 1) {
		document.location.href = generateUrl('task','add',[0,user_id]);
		return;
	}
	str += '<div id="body" ><div class="contentArea"><div class="fullWrapper ">';
	str += '<div class="leftPanel upload-profile"><h2 class="pagetitle">Request Writer For Your Order</h2><ul class="reqt_writer_panel">';
	str += '<li><a href="javascript:void(0);" class="theme-btn" onclick="return loadLogin('+ user_id +')">Click here if you are a registered Customer.</a></li>'
	str += '<li><a href="javascript:void(0);" class="theme-btn" onclick="return placeNewOrder('+ user_id +')">Click here if you are a new Customer.</a></li>'
	str += '</ul></div></div></div></div>';	
	$.facebox(str);
	return;
}

function loadLogin(user_id) {
	$.facebox({ajax:generateUrl('task', 'request_writer',[user_id])});
}

function placeNewOrder(user_id) {
	$.facebox({ajax:generateUrl('user', 'add_invite_task',[user_id])});
}

function search_reviews(p,userid, filter) {
	var frm = document.frmPaging;
	frm.page.value = p;
	//frm.user_id.value = userid;
	searchReviews(frm,userid,filter);
}

function searchReviews(frm,user_id,filter){
	var data = getFrmData(frm);
	//$.facebox(data);
	data += '&user_id='+ user_id;
	callAjax(generateUrl('reviews','search_reviews'),data,function(t){
		var ans = parseJsonData(t);
		var str = '';
		if(ans.status === 0) {
			$.facebox('Oops! Internal error.');
		}
		str += t;
		$('#reviews_list').html(t);
		$('html, body').animate({scrollTop: $("#reviews_list").offset().top-150}, 700);
	});
}
$(document).ready(function() {
	$('.comt-box').hide();
	$('body').on('click','.writers-feedback',function(event){		
		if ($(this).next('.comt-box').children().length !== 0){     
			event.preventDefault();
		}
		else 
	  $('.comt-box').hide(); 
	$(this).siblings('.comt-box').slideToggle('slow');
	});
});