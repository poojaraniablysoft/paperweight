var order_status		= ['Pending approval', 'Bidding', 'In Progress', 'Finished', 'Cancelled'];
var bid_status			= ['Pending', 'Awarded', 'Rejected', 'Cancelled'];
var invitation_status	= ['Pending', 'Accepted', 'Declined'];

$(document).ready(function() {
	getUnreadMessageCount();
	
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
		if($('div#common_error_msg').html().length > 0) {
			$('div#common_error_msg').animate({opacity:0.50}, 2000,'swing', function() {
				$('div#common_error_msg').html("");
				$('div#common_error_msg').css({opacity:1});
			});
		}
	}, 5000);
	
	$('.approve_div .close').on('click',function() {
		$('.approve_div').animate({width: "0px",height: "35px",overflow: "hidden"},'fast',function(){
			$('.approve_div').remove();
		});
	});
	
	$.expander.defaults = {          
		slicePoint: 200,
		preserveWords: false,
		expandText: 'Full Bio',
		expandPrefix: '... ',
		moreClass: 'read-more',
		lessClass: 'read-more full-bio',
		collapseTimer: 0,
		expandEffect: 'fadeIn',
		expandSpeed: 250,
		collapseEffect: 'fadeOut',
		collapseSpeed: 200,
		userCollapse: true,
		userCollapseText: 'Collapse',
		userCollapsePrefix: ' ',
	};
	$('.expander').expander();
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
	var bid_id = $('#bid_id').val();
		 
	var frm = $("#frmOrderPreview").serialize();
	 
	$("#mbs_auto_id_preview_text").html($("#idContentoEdit_mbs_auto_id_preview_text").contents().find("html > body").html());	
	var preview_text = $("#idContentoEdit_mbs_auto_id_preview_text").contents().find("html > body").text();
	
	if(preview_text == ''){
		$.facebox('<div class="popup_msg failure">No words written.</div>');
		return false;
	}
 
	callAjax(generateUrl('bid', 'update_preview'), frm, function(t) {
	 
		var ans = parseJsonData(t);
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return false;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return false;
		}
		
		if (ans.status == 1) {
			$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
			setTimeout(function(){jQuery(document).trigger('close.facebox')}, 2000);
		}
	});
}

function getEssay() {
	var user_id			= $('#user_id').val();
	var sample_essay	= window.parent.tinymce.get('sample_essay').getContent();
	var frm = $("#frmEssay").serialize();
	
	if (sample_essay.trim() == '') {
		$.facebox('<div class="popup_msg failure">No words written.</div>');
		return;
	}
	
	callAjax(generateUrl('sample', 'sample_essay'), frm, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
			setTimeout(function(){jQuery(document).trigger('close.facebox')}, 2000);
		}
	});
}
function callFbPopup(Comment){

		$.facebox("<div class='popup_msg information'>"+Comment+"</div>");
}
function getWalletTransactions(page) {
	callAjax(generateUrl('transactions', 'get_transactions'), 'page=' + page, function(t){
		var ans = parseJsonData(t);
		var str = '';
		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
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
					var wtrx_comments = arr['wtrx_comments'].replace(/'|\\'/g, "\\'");
					//str += '<a href="javascript:void(0);" onclick="$.facebox(\'<div class="popup_msg information">' + arr['wtrx_comments'] + '</div>\')">View comments</a>';
					//alert(comm)
					str += '<a href="javascript:void(0);" onclick="callFbPopup(\''+wtrx_comments+'\')">View comments</a>';
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
		$('html, body').animate({
        scrollTop: $("#tab1").offset().top-150
    }, 700);
				
	});
}

setInterval(function(){getUnreadMessageCount();}, 30000);
setInterval(function(){updateUserLastActivity();}, 60000);

function getUnreadMessageCount() {
	callAjax(generateUrl('messages', 'get_unread_message_count'), '', function(t) {
		var ans = parseJsonData(t);
		if (ans === false) return;
		if (ans.unread_messages > 0) {
			$('span.count').html('<font>'+ans.unread_messages+'</font>');
			if ($('span#total_count').length > 0) {
				$('span#total_count').html('(' + ans.unread_messages + ')');
				
				$.each(ans.users, function(index,ele) {
					if (ele['unread_messages'] > 0) {						
						$('.userList[data-bid-id="' + ele['bid_id'] + '"]').not('.selected').find('span.unread_msg_count').html('(' + ele['unread_messages'] + ')');
					}
				});
			}
		}
		else {
			//$('span.count').html(ans.unread_messages);
			if ($('span#total_count').length > 0) $('span#total_count').html('');
		}
		
		if(ans.unread_messages > 0) {							
			$('.db-nav span.count').html('<font>'+ans.unread_messages+'</font>');
		}else {
			$('.db-nav span.count').html('');
		}
	});
}

function updateUserLastActivity() {
	callAjax(generateUrl('user', 'update_last_activity'), '', function(){});
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

/* Online chat */
function postMessage(callback) {
	var bid_id	= $('input[name="bid_id"]').val();
	var message	= $('textarea[name="message"]').val();
	
	var msg_block = '';
	
	bid_id = parseInt(bid_id);
	if (bid_id < 1) return;
	
	if (message === '') {
		$('textarea[name="message"]').focus();
		return false;
	}
	
	callAjax(generateUrl('messages', 'post_message'), 'bid_id=' + bid_id + '&message=' + message, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			/* msg_block += '<div class="chatList">';
			msg_block += '<span class="timetxt">' + ans.data.tskmsg_date + '</span>';
			msg_block += '<h5>Me</h5>';
			msg_block += '<p>' + ans.data.tskmsg_msg + '</p>';
			msg_block += '</div>';
			
			$('textarea[name="message"]').val('');
			$('div.chatscroll').append(msg_block); */
			
			callback(ans.data);
		}
	});
}

/* Online chat */
function getUpdates(callback) {
	var bid_id		= $('input[name="bid_id"]').val();
	var str			= '';
	
	callAjax(generateUrl('messages', 'get_bid_updates'), 'bid_id=' + bid_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) return;
		
		if (ans.status == 0) return;
		
		if (ans.status == 1) {
			/* $.each(ans.data, function(index, ele) {
				str += '<div class="chatList">';
				str += '<span class="timetxt">' + ele.tskmsg_date + '</span>';
				str += '<h5 title="' + ele.sender + '">' + (ele.sender.length > 8 ? ele.sender.substr(0,8) + '...' : ele.sender) + '</h5>';
				str += '<p>' + ele.tskmsg_msg + '</p>';
				str += '</div>';
			});
			
			$('div.chatscroll').append(str); */
			
			callback(ans.data);
		}
	});
}

function goBack(){
	window.history.back();
	return false;
}

function checkDate(frm){

	//data=frm.elements;
	$task_due_date=$("#task_due_date").val();
	if($task_due_date!=''){
			
		$('#btn_submit').attr('disabled','disabled');
		$('#btn_submit').attr('value','Processing..');
		
		$task_time=$("#task_time").val();
		$task_id=$('input[name=task_id]').val();
		$data='task_due_date='+$task_due_date+'&task_time='+$task_time+'&task_id='+$task_id;	
		callAjax(generateUrl('task', 'extendsetup'), $data, function(t) {
			var ans = parseJsonData(t);
			//$.facebox(t);return;
			if(ans.status===0)
			{
				$("#error").html(ans.msg);
			}
			else{
				$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
				setTimeout(function(){
					location.reload();
				},2000);
			}
			return;
		});
	}
}
function calendar_custom(el, dtformat,minyear, maxyear, disab){
if(el === 'undefined' || el == ''){
return false;
}

var now = new Date(serverDate.split('-'));
var disab = disab || 'next';
var dtformat = dtformat || "%b %d, %Y";

var minyear = minyear || 1900;
var maxyear = maxyear || now.getFullYear();


Calendar.setup({
inputField : el,
ifFormat : dtformat,
range:	[minyear, maxyear],
showsTime: true,

disableFunc: function(date)  {
if(disab == 'next'){
if(date.getFullYear()   <   now.getFullYear())  { return false; }
if(date.getFullYear()   ==  now.getFullYear())  { if(date.getMonth()    >   now.getMonth()) { return true; } }
if(date.getMonth()      ==  now.getMonth())     { if(date.getDate()     >   now.getDate())  { return true; } }
}else if(disab == 'prev'){
if(date.getFullYear()   <   now.getFullYear())  { return true; }
if(date.getFullYear()   ==  now.getFullYear())  { if(date.getMonth()    <   now.getMonth()) { return true; } }
if(date.getMonth()      ==  now.getMonth())     { if(date.getDate()     <   now.getDate())  { return true; } }
}
},
});
}
 jQuery(document).on('focus', '.task_due_date_cal', function(){
	
	var now = new Date();
	var maxyr = now.getFullYear() + 5;
	calendar_custom(this, "%d/%m/%Y %H:%M", now.getFullYear(), maxyr, 'prev');
});

/* jQuery(document).on('click', '#cal_trigger_task_due_date', function(){
 $('.task_due_date_cal').trigger('focus');
}); */ 


function getWithdrawalTransactions(page) {
	
	callAjax(generateUrl('transactions', 'get_withdrawal_transactions'), 'page=' + page, function(t){
		var ans = parseJsonData(t);
		
		var str = '';
		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.data.length == 0) return;
		
		var s_no = ((ans.page - 1)*ans.pagesize) + 1;
		
		str += '<tr><th>S. No.</th><th>Amount requested</th><th>Date</th><th>Comments</th><th>Status</th></tr>'
		$.each(ans.data, function(index,arr) {
			str += '<tr>';
			str += '<td>' + (s_no + index) + '</td>';
			str += '<td>' + DEFAULT_CURRENCY + Math.abs(arr['req_amount']).toFixed(2) + '</td>';
			str += '<td>' + arr['req_date'] + '</td>';
			str += '<td>' ;
			if(arr['req_comments'] !='') 
			{
				str += arr['req_comments'];	
			}
			else{
				str += "--";
			}
			
			str += '</td>';
			str += '<td>';
			
			if (arr['req_status'] == 0) {
				
					str += "<span class='orgtag'>Pending</span>";
				}
				else if( arr['req_status'] == 1){
					str += '<span class="greentag">Approved</span>';
				}
			 else if (arr['req_status'] == 2) {
				str += "<span class='redtag'>Declined</span>";
			}
			
			str += '</td>';
			
			
			str += '</tr>';
		});
		
		$('div#tab2').find('table').find('tbody').html(str);
		$('div#tab2').find('div.pagination').html(ans.str_paging);
		$('html, body').animate({scrollTop: $("#tab2").offset().top-150}, 700);
	});
}
