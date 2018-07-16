function searchBlocks(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#listing-div'));
	callAjax(generateUrl('cmsblock', 'listing'), data, function(t){
		$('#listing-div').html(t);
	});
}

function listPages(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchBlocks(frm);
}

$(document).ready(function(){
	searchBlocks(document.frmSearch);
});