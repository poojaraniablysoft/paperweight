$(document).ready(function() {
	$(".selected_content").hide(); //Hide all content
	
	switch (filter) {
		case 'mine':
			$("a[href='#tab1']").parent('li').addClass("select");
			$("div#tab1").show();
			break;
		case 'ongoing':
			$("a[href='#tab2']").parent('li').addClass("select");
			$("div#tab2").show();
			break;
		case 'completed':
			$("a[href='#tab3']").parent('li').addClass("select");
			$("div#tab3").show();
			break;
		case 'cancelled':
			$("a[href='#tab4']").parent('li').addClass("select");
			$("div#tab4").show();
			break;
		case 'invitations':
			$("a[href='#tab5']").parent('li').addClass("select");
			$("div#tab5").show();
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

function invitations(page) {
	callAjax(generateUrl('bid', 'get_writer_invitations'), 'page=' + page, function(t){
		var ans = parseJsonData(t);
		var str = '';
		var status_class;
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.data.orders.length == 0) return;
		
		$.each(ans.data.orders, function(index,arr) {
			if (arr['inv_status'] == 0) {
				status_class = 'orgtag';
			}
			else if (arr['inv_status'] == 1) {
				status_class = 'greentag';
			}
			else {
				status_class = 'redtag';
			}
			
			str += '<tr>';
			
			if (arr['task_status'] != 2 && arr['task_status'] != 3) {
				str += '<td><a href="' + generateUrl('bid', 'add', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
			}
			else {
				str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
			}
			
			str += '<td>' + arr['task_topic'] + '</td>';
			str += '<td><span class="' + status_class + '">' + invitation_status[arr['inv_status']] + '</span></td>';
			str += '<td>' + arr['paptype_name'] + '</td>';
			str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
			str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
			str += '<td title="' + arr['customer'] + '">' + (arr['customer'].length > 10 ? arr['customer'].substr(0,10) + '...' : arr['customer']) + '</td>';
			str += '</tr>';
		});
		$('div#' + block_id).find('table').find('tbody').html(str);
		$('div#' + block_id).find('div.pagination').html(ans.data.str_paging);
		$('html, body').animate({scrollTop: $("#"+block_id).offset().top-150}, 700);
		/* $('div#tab5').find('table.dataTable').find('tbody').html(str);
		$('div#tab5').find('div.paging').html(ans.data.str_paging); */
	});
}

function bids(filter,page) {
	
	callAjax(generateUrl('bid', 'get_writer_bids'), 'filter=' + filter + '&page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		var block_id;
		var status_class;
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.data.orders.length == 0) return;
		
		switch(filter) {
			case 'mine':
				block_id = 'tab1';
				
				$.each(ans.data.orders, function(index,arr) {
					if (arr['bid_status'] == 0) {
						status_class = 'orgtag';
					}
					else if (arr['bid_status'] == 1) {
						status_class = 'greentag';
					}
					else {
						status_class = 'redtag';
					}
					
					str += '<tr>';
					str += '<td><a href="' + generateUrl('bid', 'bid_details', [arr['bid_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td><span class="' + status_class + '">' + bid_status[arr['bid_status']] + '</span></td>';
					str += '<td>' + arr['paptype_name'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + arr['total_bids'] + '</td>';
					str += '<td title="' + arr['user_first_name'] + '">' + (arr['user_first_name'].length > 10 ? arr['user_first_name'].substr(0,10) + '...' : arr['user_first_name']) + '</td>';
					str += '</tr>';
				});
				
				break;
				
			case 'ongoing':
				block_id = 'tab2';
				
				$.each(ans.data.orders, function(index,arr) {
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paptype_name'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + DEFAULT_CURRENCY + arr['amount_paid'] + ' of ' + DEFAULT_CURRENCY + arr['total_bid_amount'] + '</td>';
					str += '<td title="' + arr['user_first_name'] + '">' + (arr['user_first_name'].length > 10 ? arr['user_first_name'].substr(0,10) + '...' : arr['user_first_name']) + '</td>';
					str += '</tr>';
				});
				break;
				
			case 'completed':
				block_id = 'tab3';
				
				$.each(ans.data.orders, function(index,arr) {
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paptype_name'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_completed_on'] + '</td>';
					str += '<td>' + DEFAULT_CURRENCY + arr['amount_paid'] + ' of ' + DEFAULT_CURRENCY + arr['total_bid_amount'] + '</td>';
					str += '<td title="' + arr['user_first_name'] + '">' + (arr['user_first_name'].length > 10 ? arr['user_first_name'].substr(0,10) + '...' : arr['user_first_name']) + '</td>';
					str += '</tr>';
				});
				break;
				
			case 'cancelled':
				block_id = 'tab4';
				
				$.each(ans.data.orders, function(index,arr) {
					str += '<tr>';
					
					if (arr['bid_status'] == 1 || (arr['bid_status'] == 3 && arr['task_writer_id'] > 0)) {
						str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					}
					else {
						str += '<td><a href="' + generateUrl('bid', 'bid_details', [arr['bid_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					}
					
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + arr['total_bids'] + '</td>';
					str += '<td title="' + arr['user_first_name'] + '">' + (arr['user_first_name'].length > 10 ? arr['user_first_name'].substr(0,10) + '...' : arr['user_first_name']) + '</td>';
					str += '</tr>';
				});
				break;
				
			default:
				return;
				break;
		}
		$('div#' + block_id).find('table').find('tbody').html(str);
		$('div#' + block_id).find('div.pagination').html(ans.data.str_paging);
		$('html, body').animate({scrollTop: $("#"+block_id).offset().top-150}, 700);
		/* $('div#' + block_id).find('table.dataTable').find('tbody').html(str);
		$('div#' + block_id).find('div.paging').html(ans.data.str_paging); */
	});
}