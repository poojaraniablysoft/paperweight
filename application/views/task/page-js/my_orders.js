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
		case 'invites':
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

var invites_page_num = 1;

function invites(page) {
	callAjax(generateUrl('task', 'invites'), 'page=' + page, function(t){
		var ans = parseJsonData(t);
		var str = '';
		var status_class;
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.orders.length == 0) {
			$('div#tab5').find('table').find('tbody').html('<tr><td colspan="8">No orders to show here.</td></tr>');
			$('div#tab5').find('div.pagination').html('');
			return;
		}
		
		getPrivateOrders = page;
		
		$.each(ans.orders, function(index,arr) {
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
				str += '<td><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
			}
			else {
				str += '<td><a href="' + generateUrl('task', 'bid_preview', [arr['bid_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
			}
			
			str += '<td>' + arr['task_topic'] + '</td>';
			
			if (arr['task_status'] == 0) {
				str += '<td><span class="orgtag">' + order_status[arr['task_status']] + '</span></td>';
			}
			else {
				str += '<td><span class="' + status_class + '">' + invitation_status[arr['inv_status']] + '</span></td>';
			}
			
			str += '<td>' + arr['paper_type'] + '</td>';
			str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
			str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
			str += '<td>' + arr['writer'] + '</td>';
			
			if (arr['task_status'] == 0 || arr['task_status'] == 1) {
				str += '<td class="dataTable"><a class="theme-btn" href="javascript:void(0);" onclick="makeTaskPublic($(this),' + arr['task_id'] + ');">Make public</a></td>';
				str += '<td class="dataTable"><a class="theme-btn" href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '" >View Order </a></td>';
			}
			else {
				str += '<td>-</td>';
			}
			
			str += '</tr>';
		});
		
		$('div#tab5').find('table').find('tbody').html(str);
		$('div#tab5').find('div.pagination').html(ans.str_paging);
		$('html, body').animate({scrollTop: $("#"+block_id).offset().top-150}, 700);
	});
}

function bids(filter,page) {
	
	callAjax(generateUrl('task', 'bids'), 'filter=' + filter + '&page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		var block_id;
		var status_class;
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.orders.length == 0) return;
		
		switch(filter) {
			
			case 'mine':
			
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
					if(arr['action'] == 1){ 
						var action = '<td class="dataTable"><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '" class="theme-btn">View Order</a></td>'; 
					}
					else{
						var action = '<td></td>'
					}
					
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td><span class="' + status_class + '">' + order_status[arr['task_status']] + '</span></td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + arr['total_bids'] + '</td>';
					str += action;
					str += '</tr>';
				});
				break;
				
			case 'ongoing':
				block_id = 'tab2';
				
				$.each(ans.orders, function(index,arr) {
					if(arr['action'] == 1){ 
						var action = '<td class="dataTable"><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '" class="theme-btn">View Order</a></td>'; 
					}
					else{
						var action = '<td></td>'
					}
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += '<td>' + DEFAULT_CURRENCY + arr['amount_paid'] + ' of ' + DEFAULT_CURRENCY + arr['order_amount'] + '</td>';
					if(arr['writer'] == null || arr['writer']=='')
					{	
				
						str += '<td title=""> -- </td>';
					}
					else{
						str += '<td title="' + arr['writer'] + '">' + (arr['writer'].length > 10 ? arr['writer'].substr(0,10) + '...' : arr['writer']) + '</td>';
					}
					/* str += '<td title="' + arr['writer'] + '">' + (arr['writer'].length > 10 ? arr['writer'].substr(0,10) + '...' : arr['writer']) + '</td>'; */
					str += action;
					str += '</tr>';
				});
				break;
				
			case 'completed':
				block_id = 'tab3';
				
				$.each(ans.orders, function(index,arr) {
					if(arr['action'] == 1){ 
						var action = '<td class="dataTable"><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '" class="theme-btn">View Order</a></td>'; 
					}
					else{
						var action = '<td></td>'
					}
					str += '<tr>';
					str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_completed_on'] + '</td>';
					str += '<td>' + DEFAULT_CURRENCY + arr['amount_paid'] + ' of ' + DEFAULT_CURRENCY + arr['order_amount'] + '</td>';
					if(arr['writer'] == null || arr['writer']=='')
					{	
				
						str += '<td title=""> -- </td>';
					}
					else{
						str += '<td title="' + arr['writer'] + '">' + (arr['writer'].length > 10 ? arr['writer'].substr(0,10) + '...' : arr['writer']) + '</td>';
					}
					/* str += '<td title="' + arr['writer'] + '">' + (arr['writer'].length > 10 ? arr['writer'].substr(0,10) + '...' : arr['writer']) + '</td>'; */
					str += action;
					str += '</tr>';
				});
				break;
				
			case 'cancelled':
				block_id = 'tab4';
				
				$.each(ans.orders, function(index,arr) {
					if(arr['action'] == 1){ 
					if (arr['task_status'] == 2 || arr['task_status'] == 3 || (arr['task_status'] == 4 && arr['task_writer_id'] > 0)) {
						var action = '<td class="dataTable"><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '" class="theme-btn">View Order</a></td>'; 
					}
					else{
						var action = '<td class="dataTable"><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '" class="theme-btn">View Order</a></td>';
					}
					}
					else{
						var action = '<td></td>'
					}
					str += '<tr>';
					
					if (arr['task_status'] == 2 || arr['task_status'] == 3 || (arr['task_status'] == 4 && arr['task_writer_id'] > 0)) {
						str += '<td><a href="' + generateUrl('task', 'order_process', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					} else {
						str += '<td><a href="' + generateUrl('task', 'order_bids', [arr['task_id']]) + '">' + '#' + arr['task_ref_id'] + '</a></td>';
					}
					
					str += '<td>' + arr['task_topic'] + '</td>';
					str += '<td>' + arr['paper_type'] + '</td>';
					str += '<td>' + arr['task_pages'] + ' pages <br>' + ' <small>(' + arr['task_pages']*arr['task_words_per_page'] + ' words)</small></td>';
					str += '<td>' + arr['task_due_date'] + '<br /><small>(' + arr['time_left'] + ')</small></td>';
					str += action;
					str += '</tr>';
				});
				break;
				
			default:
				return;
				break;
		}
		
		$('div#' + block_id).find('table').find('tbody').html(str);
		$('div#' + block_id).find('div.pagination').html(ans.str_paging);
		$('html, body').animate({scrollTop: $("#"+block_id).offset().top-150}, 700);
	});
}

function makeTaskPublic(ele,task_id) {
	if (!confirm('Are you sure you want to make this task public?')) return;
	
	callAjax(generateUrl('task', 'make_task_public'), 'task_id=' + task_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			invites(invites_page_num);
		}
	});
}