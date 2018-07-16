var order_status		= ['Pending approval', 'Bidding', 'In Progress', 'Finished', 'Cancelled'];
var bid_status			= ['Pending', 'Awarded', 'Rejected', 'Cancelled'];
var invitation_status	= ['Pending', 'Accepted', 'Declined'];

$(document).ready(function() {
	//getUnreadMessageCount();
	
	//update user online status
	if ($('span.online_status').length > 0) {
		setInterval(function() {
			$('span.online_status').each(function(index,ele) {
				var uid = $(ele).attr('data-uid');
				getUserOnlineStatus($(ele),uid);
			});
		}, 120000);
	}
	setInterval(function() {
		if($('div#common_msg').length > 0) {
			$('div#common_msg').animate({opacity:0.50}, 5000,'swing', function() {
				$('div#common_msg').html("");
				$('div#common_msg').css({opacity:1});
			});
		}
	}, 10000);

});

function generateUrl(model, action, others, use_root_url, url_rewriting){
	if (!use_root_url) use_root_url = userwebroot;
	if (url_rewriting == null) url_rewriting = url_rewriting_enabled;
	
	if (url_rewriting == 1){
		var url = use_root_url + model + '/' + action;
		if (others){
			for (x in others) others[x] = encodeURIComponent(others[x]);
			url += '/' + others.join('/');
		}
		return url;
	}
	else {
		var url = use_root_url + 'index.php?url=' + model + '/' + action;
		if (others){
			for (x in others) others[x] = encodeURIComponent(others[x]);
			url += '/' + others.join('/');
		}
		return url;
	}
}

function showHtmlElementLoading(el){
	el.html('<img src="' + webroot + 'images/facebox/loading.gif">');
}

function setPage(page,frm){
	frm.elements['page'].value=page;
	//document.getElementById('page').value=page;
	frm.submit();
}

function limitTextCharacters(ele,maxLength) {
	maxLength = typeof maxLength == 'undefined' ? 500 : maxLength;
	
	var textlength = ele.value.length;
	var counter_id = $(ele).attr('name') + '_counter';
	/* var characters_left = (maxLength - textlength);
	
	if (characters_left < 0) {
		characters_left = 0;
	} */
		
	$('#'+counter_id).html(textlength);
	ele.value = ele.value.substring(0, maxLength);
	
	if (textlength == 0) {
		$('#'+counter_id).html(0);
	}
}

function nl2br(text) {
	return text.replace('/\n/gm', '<br/>');
}

function getFrmDataForUrl(frm,arr_ele) {
	var data = [];
	
	if (arr_ele === 'undefined') arr_ele = [];

	frm.each(function() {
		if ($(this).val() == '') return true;
		data.push($(this).attr('name'));
		data.push($(this).val());
	});
	
	/* Remove unwanted array items */
	if (arr_ele.length > 0) {
		$.each(arr_ele, function(key, value) {
			data.splice(data.indexOf(value),1);
		});
	}
	
	return data;
}

function updateOrderPreview() {
	var bid_id			= $('#bid_id').val();
	var preview_text	= window.parent.tinymce.get('preview_text').getContent();
	var frm = $("#frmOrderPreview").serialize();
	
	if (preview_text.trim() == '') {
		$.facebox('No words written.');
		return;
	}
	
	callAjax(generateUrl('bid', 'update_preview'), frm, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			$.facebox(ans.msg);
			setTimeout(function(){jQuery(document).trigger('close.facebox')}, 2000);
		}
	});
}

function getEssay() {
	var user_id			= $('#user_id').val();
	var sample_essay	= window.parent.tinymce.get('sample_essay').getContent();
	var frm = $("#frmEssay").serialize();
	
	if (sample_essay.trim() == '') {
		$.facebox('No words written.');
		return;
	}
	
	callAjax(generateUrl('sample', 'sample_essay'), frm, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		if (ans.status == 1) {
			$.facebox(ans.msg);
			setTimeout(function(){jQuery(document).trigger('close.facebox')}, 2000);
		}
	});
}

function getWalletTransactions(page) {
	callAjax(generateUrl('transactions', 'get_transactions'), 'page=' + page, function(t){
		var ans = parseJsonData(t);
		var str = '';
		
		if (ans === false){
			$.facebox('Oops! Internal error.');
			return;
		}
		
		if (ans.transactions.length == 0) return;
		
		var s_no = ((ans.page - 1)*ans.pagesize) + 1;
		
		str += '<tr><th width="8%">S. No.</th><th>Date</th><th>Particulars</th>	<th>Debit</th><th>Credit</th><th>Balance</th><th>Status</th><th>Mode</th></tr>'
		$.each(ans.transactions, function(index,arr) {
			str += '<tr>';
			str += '<td>' + (s_no + index) + '</td>';
			str += '<td>' + arr['wtrx_date'] + '</td>';
			
			str += '<td>';
			
			if (arr['wtrx_mode'] == 0) {
				if (arr['wtrx_amount'] < 0) {
					str += 'Funds Paid for OrderID: ' + '<a href="' + generateUrl('task', 'order_process', [arr['wtrx_task_id']]) + '"><u>#' + arr['task_ref_id'] + '</u></a>';
				}
				else {
					str += 'Funds Received for Order ID: ' + '<a href="' + generateUrl('task', 'order_process', [arr['wtrx_task_id']]) + '"><u>#' + arr['task_ref_id'] + '</u></a>';
				}
			}
			else if (arr['wtrx_mode'] == 1) {
				if (arr['wtrx_amount'] > 0) {
					str += 'Funds Deposited, Transaction ID: ' + arr['trans_gateway_transaction_id'];
				}
				else {
					str += 'Debit';
				}
			}
			else if (arr['wtrx_mode'] == 2) {
				if (arr['req_status'] == '' || arr['req_status'] == null) {
					arr['wtrx_comments'] = arr['wtrx_comments'].replace(/'|\\'/g, "\\'");
					str += '<a href="javascript:void(0);" onclick="$.facebox(\'' + arr['wtrx_comments'] + '\')">View comments</a>';
				}
				else {
					if (arr['wtrx_amount'] < 0) str += 'Funds Withdrawn, Transaction ID: ' + arr['req_transaction_id'];
				}
			}
			
			str += '</td>';
			
			str += '<td>' + (arr['wtrx_amount'] < 0 ? DEFAULT_CURRENCY + Math.abs(arr['wtrx_amount']).toFixed(2) : '-') + '</td>';
			str += '<td>' + (arr['wtrx_amount'] < 0 ? '-' : DEFAULT_CURRENCY + parseFloat(arr['wtrx_amount']).toFixed(2)) + '</td>';
			str += '<td>' + DEFAULT_CURRENCY + parseFloat(arr['wtrx_balance']).toFixed(2) + '</td>';
			
			str += '<td>';
			
			if (arr['wtrx_cancelled'] == 1) {
				str += '<span class="redtag">Cancelled</span>';
			}
			else {
				if (arr['wtrx_mode'] == 0) {
					str += '<span class="greentag">Completed</span>';
				}
				else if (arr['wtrx_mode'] == 1) {
					if (arr['trans_status'] == 0) {
						str += '<span class="redtag">Failed</span>';
					}
					else {
						str += '<span class="greentag">Completed</span>';
					}
				}
				else {
					if (arr['req_status'] == '' || arr['req_status'] == null) {
						str += '<span class="greentag">Completed</span>';
					}
					else {
						if (arr['req_status'] == 1) {
							str += '<span class="greentag">Completed</span>';
						}
						else {
							str += '<span class="redtag">Denied</span>';
						}
					}
				}
			}
			
			str += '</td>';
			
			str += '<td>';
			
			if (arr['wtrx_mode'] == 0) {
				str += 'Wallet';
			} else if (arr['wtrx_mode'] == 1) {
				str += 'PayPal';
			} else {
				str += 'System';
			}
			
			str += '</td>';
			
			str += '</tr>';
		});
		$('div#tab1').find('table').find('tbody').html(str);
		$('div#tab1').find('div.pagination').html(ans.str_paging);
	});
}

function getUserOnlineStatus(ele,user_id) {
	callAjax(generateUrl('user', 'get_online_status'), 'user_id=' + user_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) return;
		
		if (ans.online_status == 1) {
			$(ele).removeClass('redtxt').addClass('greentxt').html('Online');
		}
		else {
			$(ele).removeClass('greentxt').addClass('redtxt').html('Offline');
		}
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
		el.parent().next('td').html('<span class="textyellow">Active</span>');
		//$('#'+ele_id).parent().next('td').html('<span class="textyellow">Active</span>');
		/* console.log(el);
		console.log(ele_id);
		console.log(el.parent().next('td')); */
		if(ans.status == 0) {
			return false;
		}
		//console.log(el);
		el.replaceWith( ans.linktext );
		getOrderList(1);
		
	});
}

function getOrderList(is_ajax) {
	if(is_ajax){
	//do nothing	
	}else{
	$('#orders_listing').find('div.box_content').html('<img src="/images/loader.gif">');
	}
	callAjax(generateUrl('home', 'orders_listing'),'', function(t){		
		var ans = parseJsonData(t);		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		str = '<table width="100%" class="dataTable">';
		str += '<thead><tr><th>S.No.</th><th>Order ID</th><th>Orders Topic</th><th>Deadline Date</th><th>Approved By Admin</th><th>Status</th><th>Action</th></tr></thead>';
		str += '<tbody>';
		if (ans.order_listing.length == 0) return;
		
		$.each(ans.order_listing, function(index,arr) {			
			str += '<tr class=" inactive"><td>' + (index+1) + '</td><td>#' + arr['task_ref_id'] + '</td><td width="20%">' + arr['task_topic'] + '</td><td>' + arr['task_due_date'] + '</td><td><a class="toggleswitch actives" title="Toggle Order verification status" onclick="updateOrderVerificationStatus(' + arr['task_id'] + ', $(this))" id="verified_' + arr['task_id'] + '" href="javascript:void(0);"></a></td><td><span class="textred">Pending approval</span></td><td><a class="button small black" title="Preview" href="' + generateUrl('orders','order_details',[arr['task_id']]) + '">Preview</a><a class="button small green" title="Bids Listing" href="' + generateUrl('orders','bids',[arr['task_id']]) + '">Bids</a></td></tr>';

		});
		str += '</tbody></table>';
		
		$('#orders_listing').find('div.box_content').html(str);
	});
}

function updateUserDeactiveStatus(id, el) { 
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'update_user_deactive_status'), 'id=' + id, function(t){
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