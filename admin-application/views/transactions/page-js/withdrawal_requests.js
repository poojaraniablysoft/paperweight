/* function showRequestDetails(ele) {
	var req_id = ele.attr('data-req-id');
	
	callAjax(generateUrl('transactions', 'get_withdrawal_request_details'), 'req_id=' + req_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) $.facebox('Oops! Internal error.');
		
		if (ans.status == 0) $.facebox(ans.msg);
		
		if (ans.status == 1) {
			$.facebox(nl2br(ans.data.req_details));
		}
	});
} */