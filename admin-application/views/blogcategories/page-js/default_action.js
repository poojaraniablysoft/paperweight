function searchBlogCatogries(frm){
	var data = getFrmData(frm);
	showHtmlElementLoading($('#category-type-list'));
	callAjax(generateUrl('blogcategories', 'listBlogCategories'), data, function(t){
		$('#category-type-list').html(t);
	});
}

function listPages(p){
	var frm = document.frmPaging;
	frm.page.value = p;
	searchBlogCatogries(frm);
}

$(document).ready(function(){
	searchBlogCatogries(document.frmSearch);
});