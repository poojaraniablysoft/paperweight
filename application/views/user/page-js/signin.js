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
function validateUser(frm,v) {
	if(!v.validate()){
		return false;
	}
	var data = 'user_email=' + frm.user_email.value;
	callAjax(generateUrl('user','validate_user'),data,function(t){
		var ans = parseJsonData(t);
		var str = '';
		
		if(ans === false) {
			return true;
		}
		if(ans.status == 0 || ans.status == 2) {
			frm.submit();
			return true;
		}
		
		if(ans.status == 1) {
			//$.facebox(t);return false;
			str += '<div id="body" ><div class="contentArea"><div class="fullWrapper ">';
			str += '<div class="leftPanel upload-profile"><h2 class="pagetitle">Re-activate Your Account!</h2><div class="formWrap reactive">';
			str += '<p>Your account has been de-activated on <span>'+ ans.user_data['user_deativate_request_date']+'</span></p>';
			str += '<a href="' + generateUrl('user','re_activate_account',[ans.user_data['user_id']]) + '" class="greenBtn">Click here to re-activate account</a>';
			str += '</div></div>';	
			str += '</div></div></div>';
			$.facebox(str);	
		}		
	});
	return false;
	
}
