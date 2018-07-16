function selectAllUsers(ele) {
	$(ele).change(function() {
		if ($(this).is(':checked')) {
			$(".user_chk").each(function() {
				$(this).attr('checked', 'checked');
			});
		}
		else {
			$(".user_chk").each(function() {
				$(this).removeAttr('checked');
			});
		}
	});
}

function searchUsers(frm){
	var data = getFrmData(frm);
	
	showHtmlElementLoading($('#listing-div'));
	
	callAjax(generateUrl('users', 'search'), data, function(t){
		$('#listing-div').html(t);
	});
}

function listUsersPage(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchUsers(frm);
}
/* 
function previewCustomerProfile(id){
	$.facebox({ajax:generateUrl('user', 'preview_customer_profile', [id])});
}

function previewWriterProfile(id){
	$.facebox({ajax:generateUrl('user', 'preview_writer_profile', [id])});
} */

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

function istestpassed(id,el) {
	el.html('<img src="/images/loader.gif">');
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
		if(el.find('img').hasClass('active_icon')){
			el.attr('title','Active');
		}else{
			el.attr('title','Inactive');
		}
	});
}

function updateEmailVerificationStatus(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'update_email_verification_status'), 'id=' + id, function(t){
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
		el.html(ans.linktext);		
	});
}