function markFeatured(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'mark_featured'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		el.html(ans.linktext);		
	});
}
function markExperienced(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'mark_experienced'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		el.html(ans.linktext);		
	});
}
function updateUserStatus(id, el) { 
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'update_user_status'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		el.html(ans.linktext);		
	});
}

function istestpassed(id, el) {
	callAjax(generateUrl('user', 'test_passed'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		//el.html(ans.linktext);	
		if (el.hasClass('toggleswitch actives')) {
			el.removeClass('actives');
		}
		
	});
}

function updateUserVerificationStatus(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'mark_user_verified_by_admin'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		el.closest('td').html(ans.linktext);		
	});
}

function showEssay(essay) {
	$.facebox(decodeURIComponent(essay));
	return false;
}

function declineEssaySample(user_id) {
	if (!confirm('Are you sure you want to disapprove this essay?')) return;
	
	callAjax(generateUrl('user', 'decline_essay_sample'), 'user_id=' + user_id, function(t){
		var ans = parseJsonData(t);

		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		if (ans.status == 1) {
			$('span#txt_sample_essay').addClass('textred').html('Declined');
		}
	});
}