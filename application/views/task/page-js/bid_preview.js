function uploadAdditional(id) {
	$.facebox({ajax:generateUrl('task', 'upload_additional', [id])});
}
function extendDeadline(id) {
	$.facebox({ajax:generateUrl('task', 'extend_deadline', [id])});
}
$(document).ready(function() { 
	//When page loads...
	$(".selected_content").hide(); //Hide all content
	$(".normalTab li:first-child").addClass("select").show(); //Activate first tab
	$(".selected_content:first-child").show(); //Show first tab content
	
	//On Click Event
	$(".normalTab li").click(function() {	
		$(".normalTab li").removeClass("select"); //Remove any "active" class
		$(this).addClass("select"); //Add "active" class to selected tab
		$(".selected_content").hide(); //Hide all tab content
	 
		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
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
			msg_block += '<p>' + res.tskmsg_msg.replace(/\r?\n/g, '<br />'); + '</p>';
			msg_block += '</div>';
			
			$('textarea[name="message"]').val('');
			$('div.chatscroll').append(msg_block);
			
		});
	});
	$('.chatscroll:last').animate({ scrollTop: $('.chatscroll')[0].scrollHeight}, 1000);
});

setInterval(function(){
	getUpdates(function (res) {
		var str	= '';
		
		$.each(res, function(index, ele) {
			str += '<div class="chatList">';
			str += '<span class="timetxt">' + ele.tskmsg_date + '</span>';
			str += '<h5 title="' + ele.sender + '">' + (ele.sender.length > 8 ? ele.sender.substr(0,8) + '...' : ele.sender) + '</h5>';
			str += '<p>' + ele.tskmsg_msg.replace(/\r?\n/g, '<br />'); + '</p>';
			str += '</div>';
		});
		
		$('div.chatscroll').append(str);
		$('.chatscroll:last').animate({ scrollTop: $('.chatscroll')[0].scrollHeight}, 1000);
	});
}, 60000);

function checkDate(frm){

	//data=frm.elements;
	$task_due_date=$("#task_due_date").val();
	$task_time=$("#task_time").val();
	$task_id=$('input[name=task_id]').val();
	$data='task_due_date='+$task_due_date+'&task_time='+$task_time+'&task_id='+$task_id;	
	callAjax(generateUrl('task', 'extendsetup'), $data, function(t) {
		var ans = parseJsonData(t);
		if(ans.status===0)
		{
			$("#error").html('<div class="popup_msg failure">'+ans.msg+'</div>');
		}
		else{
			$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
		}
		return;
	});
	
} 

function add_new(ref){
	var row = '<tr><td><input id="task_file" type="file" title="" multiple="multiple" class="input-filetype" name="task_files[]"></td><td><a onclick="return remove_new(this);" href="javascript:void(0);" class="remove-file"><i class="icon ion-minus-round"></i></a></td></tr>';
		
	$("#task_file").parent().parent().after(row);
	return;
}
function remove_new(reff) {
	$(reff).parent().parent().remove();
	
	return;
}	