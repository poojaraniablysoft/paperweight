$(document).ready(function() {
	
	$('.chatscroll:last').animate({ scrollTop: $('.chatscroll')[0].scrollHeight}, 1000);
	$('#pluslink').click(function() {
		$(this).toggleClass('actives');
		$('#wrapcontentShow').toggle();
	});
	
	$('#btn_send').click(function() {
		var msg_block = '';
		$('#btn_send').attr('disabled','disabled');
		postMessage(function(res) {
			$('#btn_send').removeAttr('disabled');
			msg_block += '<div class="chatList">';
			msg_block += '<span class="timetxt">' + res.tskmsg_date + '</span>';
			msg_block += '<h5>Me</h5>';
			msg_block += '<p>' + res.tskmsg_msg.replace(/\r?\n/g, '<br />');  + '</p>';
			msg_block += '</div>';
			
			$('textarea[name="message"]').val('');
			$('div.chatscroll').append(msg_block);
			$('.chatscroll:last').animate({ scrollTop: $('.chatscroll')[0].scrollHeight}, 1000);
		});
	});
	
	/* $('input#task_file').click(function(){
		alert('\" Please mark the order as complete after file upload to notify the user about file upload and revision \"');
	}); */
});

setInterval(function(){
	getUpdates(function (res) {
		var str	= '';
		
		$.each(res, function(index, ele) {
			str += '<div class="chatList">';
			str += '<span class="timetxt">' + ele.tskmsg_date + '</span>';
			str += '<h5 title="' + ele.sender + '">' + (ele.sender.length > 8 ? ele.sender.substr(0,8) + '...' : ele.sender) + '</h5>';
			str += '<p>' + ele.tskmsg_msg.replace(/\r?\n/g, '<br />');  + '</p>';
			str += '</div>';
		});
		
		$('div.chatscroll').append(str);
		$('.chatscroll:last').animate({ scrollTop: $('.chatscroll')[0].scrollHeight}, 1000);		
	});
}, 60000);

function setMilestone() {
	var mile_content 	= $('textarea[name="mile_content"]').val();
	var mile_task_id 	= $('input[name="mile_task_id"]').val();
	var mile_page 		= $('input[name="mile_page"]').val();
	var mile_id 		= $('input[name="mile_id"]').val();
	var frm  = $("#frmMilestone").serialize();
	mile_content = mile_content.replace(/<p>&nbsp;<\/p>/g, '');
	
	//var post_data = 'mile_content=' + mile_content + '&mile_task_id=' + mile_task_id + '&mile_page=' + mile_page + '&mile_id=' + mile_id;
	//var post_data = 'mile_content=' + mile_content + '&mile_task_id=' + mile_task_id + '&mile_id=' + mile_id;
	//$.facebox(post_data);return;
	callAjax(generateUrl('task', 'add_milestone'), frm, function(t) {
		var ans = parseJsonData(t);
		//$.facebox(t);return;
		if (ans === false) {
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

function showOrderFiles(task_id) {
	task_id = parseInt(task_id);
	if (isNaN(task_id)) return;
	
	callAjax(generateUrl('task', 'get_order_files'), 'task_id=' + task_id, function(t) {
		var ans = parseJsonData(t);
		//$.facebox(t);return;
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			if (ans.data.files === false) {
				$.facebox('<div class="popup_msg information">No files uploaded.</div>');
				return;
			}
			
			var list = '';
			list += '<div class="contentArea"><div class="fullWrapper"><div class="leftPanel upload-profile"><h2 class="pagetitle">Uploaded files</h2>'
			
			if (ans.data.files.length > 0) {
				list += '<ul class="download_file">';
				
				$.each(ans.data.files, function(i,ele) {	
					
						list += '<li><a class="attachicon" href="javascript:void(0);" onclick="downloadFile(' + ele['file_id'] + ','+ ele['file_task_id']+');">' + ele['file_download_name'] + '</a></li>';
					
					/* list += '<li><a class="attachicon" href="' + generateUrl('task', 'download_file', [ele['file_id']]) + '">' + ele['file_download_name'] + '</a></li>'; */
				});
				
				list += '</ul>';
			}
			else {
				list += '<p>No files uploaded.</p>';
			}
			
			list += '</div></div></div>'
			
			$.facebox(list);
		}
	});
}

/* function order_payment(task_id) {
	task_id = parseInt(task_id);
	if (isNaN(task_id)) return;
	
	callAjax(generateUrl('transactions', 'transfer_payment'), 'task_id=' + task_id, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('Oops! Internal error');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
	});
} */

function downloadFile(file_id,task_id){
	file_id = parseInt(file_id);
	if (isNaN(file_id)) return;
	task_id = parseInt(task_id);
	if (isNaN(task_id)) return;
	
	//window.location.href = generateUrl('task', 'download_file', [file_id]);
	
	window.open(generateUrl('task', 'download_file', [file_id]),'_newtab');
	
}
/* 
function orderPayment(file_id,task_id){
	
	file_id = parseInt(file_id);
	if (isNaN(file_id)) return;
	task_id = parseInt(task_id);
	if (isNaN(task_id)) return;
	
	var post_data = 'task_id=' + task_id + '&file_id='+ file_id ;
	
	callAjax(generateUrl('task', 'download_milestone'), post_data, function(t) {
		var ans = parseJsonData(t);
		
		if(ans.status == false){
			$.facebox('Oops Internal Error!');return;
		}
		
		if(ans.status == 0) {
			$.facebox(ans.msg);return;
		}	
		
		//$.facebox({ajax:(generateUrl('task', 'download_file',[file_id]))});	
		
		if(ans.status == 1) {
			if(ans.data == false) {
				$.facebox({ajax:generateUrl('transactions', 'transfer_payment', [task_id])});
			}
		}	
		//document.location = generateUrl('task', 'download_file', [file_id]);		
	});
	
	
}
 */
function upload(task_id) {
	$.facebox({ajax:generateUrl('task', 'upload_additional', [task_id])});
}



function add_new(ref){
	var row = '<tr><td><input id="task_file" type="file" title="" multiple="multiple" name="task_files[]" class="input-filetype"></td><td><a class="remove-file" onclick="return remove_new(this);" href="javascript:void(0);"><i class="icon ion-minus-round"></i></a></td></tr>';
		
	$("#task_file").parent().parent().after(row);
	return;
}
function remove_new(reff) {
	$(reff).parent().parent().remove();
	
	return;
}	


var rated;

var delivery_rated 			= false;
var communication_rated 	= false;
var quality_rated 			= false;

function btnOver(e) {
	var c_index 	= $(e).index();
	var ele_class	= $(e).attr('class');
	var rating_attr = $(e).attr('rel');
	
	switch (rating_attr) {
		case 'delivery':
			rated = delivery_rated;
			break;
		case 'communication':
			rated = communication_rated;
			break;
		case 'quality':
			rated = quality_rated;
			break;
	}
	
	if (!rated) {
		for (i=0; i<=c_index; i++) {
			$('.'+ele_class+':eq('+i+')').attr('src', webroot + 'images/starLit.png');
		}
	}
}

function btnOut(e) {
	var c_index 	= $(e).index();
	var ele_class	= $(e).attr('class');
	var rating_attr = $(e).attr('rel');
	
	switch (rating_attr) {
		case 'delivery':
			rated = delivery_rated;
			break;
		case 'communication':
			rated = communication_rated;
			break;
		case 'quality':
			rated = quality_rated;
			break;
	}
	
	if (!rated) {
		for (i=0; i<=c_index; i++) {
			$('.'+ele_class+':eq('+i+')').attr('src', webroot + 'images/starNorm.png');
		}
	}
}

function rateUser(e) {
	var rating 			= $(e).val();
	var rating_attr 	= $(e).attr('rel');
	var task_id			= $('input[name="task_id"]').val();
	
	callAjax(generateUrl('ratings', 'rate_user'), 'task_id=' + task_id + '&rating=' + rating + '&attribute=' + rating_attr, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false){
			alert('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			alert('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			if (rating_attr == 'delivery') {
				delivery_rated = true;
			}
			else if (rating_attr == 'communication') {
				communication_rated = true;
			}
			else if (rating_attr == 'quality') {
				quality_rated = true;
			}
		}
	});
}
function extendDeadline(id) {
	$.facebox({ajax:generateUrl('task', 'extend_deadline', [id])});
}

function cancel_order_request(task_id) {
	$.facebox({ajax:generateUrl('task', 'cancel_order_request', [task_id])});
}

function mark_order_complete(task_id) {
	$.facebox('<img src=\'/images/loader.gif\'>');
	$('.footer').remove();
	callAjax(generateUrl('task', 'markOrderCompleteByWtr'), 'task_id=' + task_id, function(t) {		
		$.facebox('<div class="popup_msg success">You have marked the order as finished.</div>');
		setTimeout(function(){location.reload();}, 4000);		
	})
	//$.facebox({ajax:generateUrl('task', 'markOrderCompleteByWtr', [task_id])});
	//setTimeout(function(){location.reload();}, 2000);
}

function updated_milestone_request(task_id) {
	$.facebox({ajax:generateUrl('task', 'updated_milestone_request', [task_id])});
}

function review_request(task_id) {
	$.facebox({ajax:generateUrl('task', 'revision_request', [task_id])});
}

