function workStatus(id, el) { 
	callAjax(generateUrl('work', 'update_work_status'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if(ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return false;
		}		
		el.html(ans.linktext);
		
		if (el.hasClass('green')) {
			el.removeClass('green').addClass('red');
		}
		else {
			el.removeClass('red').addClass('green');
		}
		$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
	});
}
