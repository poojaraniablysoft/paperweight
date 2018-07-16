function updateStatus(el)
{
	var span = document.createElement('SPAN');
	span.id = 'loader';
	el.parentNode.appendChild(span);
	showHtmlElementLoading($('#loader'));
	var data = getFrmData(document.frmBlogComments);
	callAjax(generateUrl('blogcomments', 'updateStatus'), data, function(t){
		var ans = parseJsonData(t);
		if(ans === false){
			$('#loader').remove();
			window.adding_in_progress = false;
			return false;
		}
		$('#loader').html('');
		$.facebox(ans.msg);
		//$('.div_error').css('margin-top','10px');
		//$('.div_msg').css('margin-top','10px');
		setTimeout(function(){ 
			location.reload(true); 
		}, 3000);
	});
}
