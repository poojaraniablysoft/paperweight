function reviews(id,rev_id){
	$.facebox({ajax:generateUrl('orders', 'reviews', [id,rev_id])});
}

function updateReview(rev_id,task_id){
	$.facebox({ajax:generateUrl('orders', 'update_review', [rev_id,task_id])});
}

function declineReview(rev_id,user_email, el) { 
	callAjax(generateUrl('orders', 'decline_review'), 'id=' + rev_id + '&user_email=' + user_email, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if(ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return false;
		}		
		$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
		el.html(ans.linktext);
		
		if (el.hasClass('green')) {
			el.removeClass('green').addClass('red');
		}
			
	});
}

function cancel_order(task_id) {
	callAjax(generateUrl('orders','cancel_order'), 'task_id=' + task_id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if(ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return false;
		}		
		$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
		el.remove();return;
	});
}

function show_description(Comment)
{
$.facebox("<div class='popup_msg information'>"+Comment+"</div>");
}