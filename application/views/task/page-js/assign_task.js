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

$(function() {
	$('#btn_reserve_amount').click(function() {
		if (!confirm('Are you sure you want to proceed?')) return;
		
		var bid_id = parseInt($(this).attr('data-bid-id'));
		if (isNaN(bid_id)) return;
		
		callAjax(generateUrl('task', 'assign_writer'), 'bid_id=' + bid_id, function(t) {
			var ans = parseJsonData(t);
			if (ans === false) {
				$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
				return;
			}
			
			if (ans.status == 0) {
				$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
				return;
			}
			
			if (ans.status == 2) {
				$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
				$('div.blockpayments').removeClass('hide');				
				return;
			}
			
			if (ans.status == 1) {
				document.location.href = generateUrl('task', 'order_process', [ans.data.task_id]);
			}
		});
	});
});