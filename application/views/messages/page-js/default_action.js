$(function() {
	/* if (bid_id > 0) {
		$('.leftCol .userList[data-bid-id=' + bid_id + ']').addClass('selected');
		$('.userList[data-bid-id=' + bid_id + ']')[0].scrollIntoView(true); */
		//$('.scrollSection .scrollWrap').scrollTop($('.userList[data-bid-id=' + bid_id + ']').offset().top);
		/* var x = $('.scrollSection .scrollWrap').offset().top;
		var y = $('.userList[data-bid-id=' + bid_id + ']').offset().top;
		var z = y - x;
		alert(z);
		$('.scrollSection .scrollWrap').scrollTop(z); */
	/* }
	else {
		$('.leftCol .userList:first').addClass('selected');
	} */
	
	/* $('#srch_filter').keyup(function() {
		var keyword = $(this).val();
		var str		= '';
		
		callAjax(generateUrl('messages', 'search_conversations'), 'keyword=' + keyword, function(t) {
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
				if (ans.data.length > 0) {
					$.each(ans.data, function(index,arr) {
						str += '<div class="userList" data-bid-id="' + arr.bid_id + '"  onclick="return getConversationHistory($(this));">';
						str += '<div class="photo">';
						str += '<img src="' + generateUrl('user', 'photo', [arr.user_id,56,56]) + '" alt="">';
						str += '<span class="online_status ' + (arr.user_status == 1 ? 'greentxt' : 'redtxt') + '">' + (arr.user_status == 1 ? 'Online' : 'Offline') + '</span>';
						str += '</div>';
						str += '<h5>#' + arr.task_ref_id + '</h5>';
						str += '<p title="' + arr.user_screen_name + '">' + (arr.user_screen_name.length > 14 ? arr.user_screen_name.substr(0,14) + '...' : arr.user_screen_name) + '</p>';
						str += '<span class="timetxt">' + arr.last_updated_on + '</span>';
						str += '</div>';
					});
				}
				else {
					if (keyword.trim() != '') {
						str = 'No match found.';
					}
				}
				
				$('.leftCol .scrollWrap').html(str);
			}
		});
	});
	 */
	
});

setInterval(function(){
	getUpdates(function (res) {
		var str	= '';
		
		$.each(res, function(index, ele) {
			str += '<div class="userList">';
			str += '<div class="photo"><img src="' + generateUrl('user', 'photo', [ele.tskmsg_user_id,50,50]) + '" alt=""></div>';
			str += '<span class="timetxt">' + ele.tskmsg_date + '</span>';
			str += '<h5>' + ele.sender + '</h5>';
			str += '<p>' + ele.tskmsg_msg.replace(/\r?\n/g, '<br />');  + '</p>';
			str += '</div>';
		});
		
		$('.scrollWrap').append(str);
		$("#conversation_history").animate({ scrollTop: $('#conversation_history')[0].scrollHeight}, 1000);
	});
}, 30000);

/* function getUpdates() {
	var bid_id		= $('input[name="bid_id"]').val();
	var str			= '';
	
	callAjax(generateUrl('messages', 'get_bid_updates'), 'bid_id=' + bid_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) return;
		
		if (ans.status == 0) return;
		
		if (ans.status == 1) {
			$.each(ans.data, function(index, ele) {
				str += '<div class="userList">';
				str += '<div class="photo"><img src="' + generateUrl('user', 'photo', [ele.tskmsg_user_id,50,50]) + '" alt=""></div>';
				str += '<span class="timetxt">' + ele.tskmsg_date + '</span>';
				str += '<h5>' + ele.sender + '</h5>';
				str += '<p>' + ele.tskmsg_msg + '</p>';
				str += '</div>';
			});
			
			$('.rightCol .scrollWrap').append(str);
		}
	});
} */

function getConversationHistory(ele) {
	var bid_id	= ele.attr('data-bid-id');
	var str		= '';
	
	callAjax(generateUrl('messages', 'get_conversation_history'), 'bid_id=' + bid_id, function(t) {
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
			$('.userList').removeClass('selected');
			ele.addClass('selected');
			
			$('.topCol .datetxt').html(ans.data.bid.task_due_date + ' (' + ans.data.bid.time_left + ')');
			
			$('.topCol h2 span').html(ans.data.bid.task_topic);
			str += '<div class="sectionMsgs user-list"><div class="white-box clearfix"><div  class="conversation_history"><div class="topCol blue-bg"> <a href="'+ generateUrl('messages','')+'" class="white-btn"><i class="icon ion-chevron-left"></i> Back</a> <div id="task_ref_id" class="right-side"></div></h2></div><div class="scrollWrap" id="conversation_history">';
			if (ans.data.conversation.length > 0) {
				ele.find('span.unread_msg_count').html('');
				getUnreadMessageCount();
				
				$.each(ans.data.conversation, function(index,arr) {
					str += '<div class="userList">';
					str += '<div class="photo"><img src="' + generateUrl('user', 'photo', [arr.tskmsg_user_id,50,50]) + '" alt=""></div>';
					str += '<span class="timetxt">' + arr.tskmsg_date + '</span>';
					str += '<h5>' + arr.sender + '</h5>';
					str += '<p>' + arr.tskmsg_msg.replace(/\r?\n/g, '<br />');  + '</p>';
					str += '</div>';
				});
			}
			else {
				str = 'No messages to show here.';
			}
				
			str += '</div></div></div><div class="white-box clearfix padding_30">';
			if (bid_id > 0) {
				str += '<div class="postSection"><form name="frmMessage" id="frmMessage"><input type="hidden" name="bid_id" id="bid_id" value="' + bid_id + '"><textarea name="message" id="message" rows="" cols="" placeholder="Enter your message here..."></textarea><input type="button" value="Send" name="btn_send" id="btn_send" onclick="return submitFrm();" class="theme-btn"></form></div>';
			} 
			str += '</div></div>';
			
			$('div#history').html(str);
			//$('#history').html($('div#msg_section').html());
			$('#msg_section').remove();
			$('input[name="bid_id"]').val(bid_id);
			$("#conversation_history").animate({ scrollTop: $('#conversation_history')[0].scrollHeight}, 1000);
			$('#task_ref_id').html('#' + ans.data.bid.task_ref_id);
			
		}
	});
}

function submitFrm() {
	var msg_block = '';
	$('#btn_send').attr('disabled','disabled');
	postMessage(function(res) {
		$('#btn_send').removeAttr('disabled');
		msg_block += '<div class="userList">';
		msg_block += '<div class="photo"><img src="' + generateUrl('user', 'photo', [res.tskmsg_user_id,50,50]) + '" alt=""></div>';
		msg_block += '<span class="timetxt">' + res.tskmsg_date + '</span>';
		msg_block += '<h5>Me</h5>';
		msg_block += '<p>' + res.tskmsg_msg.replace(/\r?\n/g, '<br />');  + '</p>';
		msg_block += '</div>';
		
		$('textarea[name="message"]').val('');
		
		$('.scrollWrap ').append(msg_block);
		//$('#conversation_history').scrollTop($('.scrollWrap:last-child')[0].scrollHeight);
		$("#conversation_history").animate({ scrollTop: $('#conversation_history')[0].scrollHeight}, 1000);
	});
	return false;
}

$( document ).ready(function() {
   $("#conversation_history").animate({ scrollTop: $('#conversation_history')[0].scrollHeight}, 1000);   
   
  /*  $('.ion-star').on('click', function(){ alert('star'); return false;
		var star_element = $(this);
		$.facebox(star_element);return false;
		var conversationId = $(this).parent('a').attr('data-conversation-id');
		callAjax(generateUrl('messages','mark_starred'),'conversation_id=' + conversationId ,function(t){
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
				if(star_element.hasClass('starred')){
					star_element.removeClass('starred');
				}else {
					star_element.addClass('starred');
				}
			}

		});
		return false;
   }); */
});

function markStarred(ele) { 
	var conversationId = ele.attr('data-conversation-id');
	callAjax(generateUrl('messages','mark_starred'),'conversation_id=' + conversationId ,function(t){
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
			if(ele.find('i').hasClass('starred')){
				ele.find('i').removeClass('starred');
			}else {
				ele.find('i').addClass('starred');
			}
		}

	});
	return false;
}

function all_msg(msg_status,page) {	
	var str		= '';
	var keyword = '';
	$('.roundtabs').find('li').removeClass('currenttab');
	
	if(msg_status == 'read') {
		$('.roundtabs li#read').addClass('currenttab');
		
	}else if(msg_status == 'unread'){
		$('.roundtabs li#Unread').addClass('currenttab');
		
	}else if(msg_status == 'starred'){
		$('.roundtabs li#starred').addClass('currenttab');
		
	}else {
		
		$('.roundtabs li#all').addClass('currenttab');
		
	}
	callAjax(generateUrl('messages', 'search_conversations'), 'keyword=' + keyword + '&status=' + msg_status+ '&page=' + page, function(t) {
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
			str += '<div class="white-box clearfix"><div class="msgcontainer">';
			
			 if (ans.data.messages.length > 0) { 
			 /*if (ans.data.messages.page<=ans.data.pages) { */
				$.each(ans.data.messages, function(index,arr) {
					var msg = arr.tskmsg_msg.replace(/\r?\n/g, '<br />');
					//var msg = arr.tskmsg_msg; 
					str += '<div class="border-box '+ (arr['unread_messages'] >= 1 ? 'unread' : 'read' ) + '">';
					str += '<ul class="choiceTabs"><li>';
					str += '<a onclick="markStarred($(this)); return false;" href="javascript:void(0);" data-conversation-id="' + arr['bid_id'] + '"><i class="ion-star'+ (arr['bid_is_starred'] == 1 ? ' starred' : '' ) +'"></i></a>';
					str += '</li><li>';
					str += '<a href="#"><i class="icon ion-record"></i></a>';
					str += '</li></ul>';
					str += '<div class="cursor" onclick="return getConversationHistory($(this));" data-bid-id="' + arr.bid_id + '">';
					str += '<div class="user_msg">' + (arr.user_screen_name.length > 14 ? arr.user_screen_name.substr(0,14) + '...' : arr.user_screen_name); 
					 /* str += '<div class="user_msg">' + arr.user_screen_name; */
					str += '</div>';
					str += '<div class="msg_text"><h5>#' + arr.task_ref_id + '</h5><p><span>Updated: </span>' + arr.last_updated_on + '</p></div>';
					str += '<div class="last-msg">'+ msg + '</div>';
					str += '</div></div>';
				});
			}
			else {				
				str += '<div class="no_result"><div class="no_result_icon"><img src="'+logoUrl+'"></div><div class="no_result_text"><h5>No Message found!!</h5></div></div>';				
			}
			str += '</div></div>';
			
			$('#history').html(str);
			//$('#msg_div').html(str);			
			$('div#writer-inbox').find('div.pagination').html(ans.data.str_paging);
			$('html, body').animate({scrollTop: $("#history").offset().top-150}, 700);
		}
	});
}