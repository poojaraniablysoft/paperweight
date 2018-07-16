$(document).ready(function() {
 $('.fliter-by li').click(function(){
	 if($(this).hasClass('active')) {
	  $(this).toggleClass("active");		
	 }else {
		$('.fliter-by li').removeClass('active');
		$(this).addClass('active');
		$(this).siblings('.sub-menu').slideToggle();
		//$(this).siblings('.sub-menu').slideToggle();
	 }
  }); 
/* $('#pull').click(function(){
	  $(this).toggleClass("active");
	  $('#menu').slideToggle(600);
  }); */	  
 $('.language ul li').click(function(){
	  $(this).toggleClass("active");
	  if($(window).width()<990) $(this).siblings('.tooltip').slideToggle();
  }); 
	  
 /* $('.my-account a').click(function(){
	  $(this).toggleClass("active");
	  if($(window).width()<990)
	  $(this).siblings('.drop-down').slideToggle();
  }); */ 
 
	  
});

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

function search_writers(p,filter,filterValue) {
	var frm = document.frmPaging;
	frm.page.value = p;
	var data = getFrmData(frm);
	getTopUsers(p,filter,filterValue);
}

function getTopUsers(p,filter,filterValue) {
	var paper_id=$('#paper_id').val();
	var data = 'page='+ p +'&filter='+ filter + '&filter_value=' + filterValue + '&featured=false' + '&paper_id=' + paper_id;
	callAjax(generateUrl('user','search_top_writers'),data,function(t){
		var ans = parseJsonData(t);
		if(ans.status === 0) {
			$.facebox('Oops! Internal error.');
		}
		$('#top_writers').html(t);
	});
}