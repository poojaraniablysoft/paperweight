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
 
});

$(document).ready(function() {

$('#pluslink').click(function() {
    $(this).toggleClass('actives');
    $('#wrapcontentShow').toggle();
});
});

function checkDate(frm){

	//data=frm.elements;
	$task_due_date=$("#task_due_date").val();
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
/* 
$(document).ready(function(){
	var currentDate = new Date();
	var day = currentDate.getDate();
      var month = currentDate.getMonth() + 1;
      var year = currentDate.getFullYear();
 var my_date = month+"-"+day+"-"+year;
	$("#task_due_date").datetimepicker({
		format: 'M d, Y H:i',
		minDate: my_date
	});
}); */

function add_new(ref){
	var row = '<tr><td><input id="task_file" type="file" title="" multiple="multiple" class="input-filetype" name="task_files[]"></td><td><a onclick="return remove_new(this);" href="javascript:void(0);" class="remove-file"><i class="icon ion-minus-round"></i></a></td></tr>';
		
	$("#task_file").parent().parent().after(row);
	return;
}
function remove_new(reff) {
	$(reff).parent().parent().remove();
	
	return;
}	