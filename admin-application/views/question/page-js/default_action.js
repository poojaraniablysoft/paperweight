function queStatus(id, el) { 
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('question', 'update_quest_status'), 'id=' + id, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}		
		el.html(ans.linktext);

		if (el.hasClass('green')) {
			el.removeClass('green').addClass('red');
		}
		else {
			el.removeClass('red').addClass('green');
		}	
	});
}
function resetPage(frm)
{
	$("input[name=page]").val(1); return true;
}