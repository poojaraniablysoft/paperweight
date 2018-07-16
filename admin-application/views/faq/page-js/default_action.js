function searchfaqs(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#listing-div'));
	callAjax(generateUrl('faq', 'listing'), data, function(t){
		$('#listing-div').html(t);
	});
}

function listPages(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchfaqs(frm);
}

$(document).ready(function(){	
	$('#type_id').val($('#faq_type_id').val())
	$('#type_id').change(function() {		
		$('#faq_type_id').val($(this).val())		
		//goToPage($('#page').val())
		goToPage(1)
	})
	searchfaqs(document.frmSearch);
});

function goToPage(pageid){
	if(typeof pageid == typeof undefined) pageid = 1;
	if(pageid == '') pageid = 1;
	$("#page").val(pageid);
	searchfaqs(document.frmSearch);
}