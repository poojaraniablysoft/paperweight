$(document).ready(function() {
	//Table DND call
	$('#degree_listing').tableDnD({
		onDrop: function(table, row) {
			var order= $.tableDnD.serialize('id');
			
			callAjax(generateUrl('academic', 'reorder_menu'), order, function(t){
				$('#common_msg').html(t);
			});
		}
	}); 
});

function AcademicStatus(id, el) { 
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('academic', 'update_academic_status'), 'id=' + id, function(t){
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
