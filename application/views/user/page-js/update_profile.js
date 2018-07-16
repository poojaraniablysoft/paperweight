$(document).ready(function() {	
	showCompanyName($('input[name=user_is_experienced]').filter(':checked').val());
	
	$('.remove_profile').click(function(){
		if(confirm("Do you really want to remove your profile photo?") == false ) return false;
		$('.photo').html('<img src="/images/loader.gif" class="loader">');
		$('.my-account a:first img').html('<img src="/images/loader.gif" class="loader">');
		callAjax(generateUrl('user','remove_profile_image'),'',function(t){
			var ans = parseJsonData(t);
			//$.facebox(t);return false;
			var img_large = '<img src="'+generateUrl('user','photo',[ans.user_id])+'">';
			var img_small = '<img src="'+generateUrl('user','photo',[ans.user_id,32,32])+'">';
			$('.photo').html(img_large);
			$('.my-account a:first img').remove();
			$('.my-account a:first').prepend(img_small);
			if (ans === false){
				$('#common_error_msg').html('<div class="div_error"><ul><li>Oops! Internal error.</li></ul></div>');
				return;
			}
			
			if(ans.status == 0) {
				$('#common_error_msg').html('<div class="div_error"><ul><li>'+ans.msg+'</li></ul></div>');
				return false;
			}		
			$('#common_error_msg').html('<div class="div_msg"><ul><li>'+ans.msg+'</li></ul></div>');
		});
	});
	
});

function check_box_validation(frm){
	var error = 0;
	if($('input[name="citations[]"]:checked').length < 1) {
		error++;
		$('span#error_citation').html('<ul class="errorlist erlist_citation"><li><a href="javascript:void(0);">Please select atleat one citation.</a></li></ul>').focus();
	}else {
		$('span#error_citation').html('');
	}
	if($('input[name="user_lang_id[]"]:checked').length < 1) {
		error++;
		$('span#error_lang').html('<ul class="errorlist erlist_user_lang_id"><li><a href="javascript:void(0);">Please select atleat one language.</a></li></ul>').focus();
	}else {
		$('span#error_lang').html('');
	}
	if(error > 0) {
		return false;
	}
	return true;
}

function ChangeImage(id) {
	$.facebox({ajax:generateUrl('user', 'upload_photo', [id])});
}

function showCompanyName(val) {
	callAjax(generateUrl('user', 'getCompanyField', [val]), '&outmode=json', function(t){	
		$('#companyname').html(t);
		
		if (val == 1) {
			$('span#companyname').closest('tr').removeClass('hide');
		}
		else if (val == 2) {
			$('span#companyname').closest('tr').addClass('hide');
		}    
	});
}