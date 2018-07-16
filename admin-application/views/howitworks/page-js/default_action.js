function searchsteps(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#listing-div'));
	callAjax(generateUrl('howitworks', 'listing'), data, function(t){
		$('#listing-div').html(t);
	});
}

function listPages(p){
	var frm = document.frmSearch;
	frm.page.value = p;
	searchsteps(frm);
}

$(document).ready(function(){
	$('#type_id').val($('#step_type_id').val())
	$('#type_id').change(function() {		
		$('#step_type_id').val($(this).val())		
		//goToPage($('#page').val())
		goToPage(1)
	})	
	searchsteps(document.frmSearch);
});

function goToPage(pageid){
	if(typeof pageid == typeof undefined) pageid = 1;
	if(pageid == '') pageid = 1;
	$("#page").val(pageid);
	searchsteps(document.frmSearch);
}