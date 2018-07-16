$(function() {
	$('div.order-form').before('<div id="common_error_msg"></div>');
});

function whatNext() {
	$.facebox({ajax:generateUrl('home', 'what_next')});
}
/* $(document).ready(function(){
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

$(document).ready(function() {
	//When page loads...
	$(".tab_content").hide(); //Hide all content
	$(".resp-tabs-list li:first-child").addClass("active").show(); //Activate first tab
	$(".tab_content:first-child").show(); //Show first tab content
	
	//On Click Event
	$(".resp-tabs-list li").click(function() {
	
		$(".resp-tabs-list li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		 
		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	$('.slider_roundthumbs').tinycarousel({
	bullets  : true,
	infinite : false,
	interval : true,
	intervalTime : 6000
	});	
var today = new Date();
var tomorrow = new Date(today.getTime() + 24 * 60 * 60 * 1000);
console.log(tomorrow);
// $('#task_due_date').datepicker({
    // minDate: tomorrow,
    // /*onSelect: function (dat, inst) {
        // $('#end').datepicker('option', 'minDate', dat);
    // }*/
// });

 
	
});

/*copied from top_writers.js*/

function chooseWriter(user_id) {
	if(isUserLogged == 1) {
		document.location.href = generateUrl('task','add',[0,user_id]);
		return;
	}
	var str = '';
	
	str += '<div id="body" ><div class="contentArea"><div class="fullWrapper ">';
	str += '<div class="leftPanel upload-profile"><h2 class="pagetitle">Request Writer For Your Order</h2><ul class="reqt_writer_panel">';
	str += '<li><a href="javascript:void(0);" class="theme-btn" onclick="return loadLogin('+ user_id +')">Click here if you are registered customer</a></li>'
	str += '<li><a href="javascript:void(0);" class="theme-btn" onclick="return placeNewOrder('+ user_id +')">Click here if you are new customer</a></li>'
	str += '</ul></div></div></div></div>';	
	$.facebox(str);
	return;
}



function loadLogin(user_id) {
	$.facebox({ajax:generateUrl('task', 'request_writer',[user_id])});
}

function placeNewOrder(user_id) {
	$.facebox({ajax:generateUrl('user', 'add_invite_task',[user_id])});
}




/*copied from top_writers.js endss*/