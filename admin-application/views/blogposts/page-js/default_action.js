function searchPost(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#post-type-list'));
	callAjax(generateUrl('blogposts', 'listBlogPosts'), data, function(t){
		$('#post-type-list').html(t);
	});
}

function listPages(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchPost(frm);
}

$(document).ready(function(){
	searchPost(document.frmSearch);
});