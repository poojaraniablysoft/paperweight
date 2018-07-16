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

function updateOrderVerificationStatus(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('orders', 'mark_order_verified_by_admin'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		el.replaceWith(ans.linktext);
		//el.html();		
	});
}


function markFeatured(id, el) {
	//el.removeAttr('onclick');
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