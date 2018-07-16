$(document).ready(function() {
	$(".selected_content").hide(); //Hide all content
	
	switch (filter) {
		case 'reviews':
			$("a[href='#tab1']").parent('li').addClass("select");
			$("div#tab1").show();
			break;
		case 'feedback':
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


/* function reviews(filter,page) {
	callAjax(generateUrl('reviews', 'user_review'), 'filter=' + filter + '&page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		var block_id;
		var status_class;
		$.facebox(ans.review);return;
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.orders.length == 0) return;
		
		switch(filter) {
			case 'reviews':
				block_id = 'tab1';
				
				$.each(ans.orders, function(index,arr) {
					if (arr['task_status'] == 0 || arr['task_status'] == 1 || arr['task_status'] == 2) {
						status_class = 'orgtag';
					}
					else if (arr['task_status'] == 3) {
						status_class = 'greentag';
					}
					else {
						status_class = 'redtag';
					}
					
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td><span class="' + status_class + '">' + order_status[arr['task_status']] + '</span></td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + arr['total_bids'] + '</td>';
					str += '</tr>';
				});
				break;
				
			case 'feedback':
				block_id = 'tab2';
				
				$.each(ans.orders, function(index,arr) {
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + DEFAULT_CURRENCY + arr['amount_paid'] + ' of ' + DEFAULT_CURRENCY + arr['order_amount'] + '</td>';
					str += '<td title="' + arr['writer'] + '">' + (arr['writer'].length > 10 ? arr['writer'].substr(0,10) + '...' : arr['writer']) + '</td>';
					str += '</tr>';
				});
				break;
				
			
			default:
				return;
				break;
		}
		
		$('div#' + block_id).find('table.dataTable').find('tbody').html(str);
		$('div#' + block_id).find('div.paging').html(ans.str_paging);
	});
} */