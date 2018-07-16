function signIn(task_id) {
	$.facebox({ajax:generateUrl('user', 'sign_in', [task_id])});
}


function search_orders(p,filter,filterValue) {
	var frm = document.frmPaging;
	frm.page.value = p;
	var data = getFrmData(frm);
	SearchOrder(p,filter,filterValue);
}

function SearchOrder(p,filter,filterValue) {
	var data = 'page='+ p +'&filter='+ filter + '&filter_value=' + filterValue;
	callAjax(generateUrl('task','search_orders'),data,function(t){
		var ans = parseJsonData(t);
		if(ans.status === 0) {
			$.facebox('Oops! Internal error.');
		}
		$('#recent_orders').html(t);
	});
}