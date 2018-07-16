function pagination(str){
	var page_num = str;
	var data = '';
	callAjax(generateUrl('task', 'order'), 'page_num=' + page_num, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('Oops! Internal error');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox(ans.msg);
			return;
		}
		
		data += '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="dataTable allLinks">';
		data +=	'<tr> <th width="10%">Order No.</th>	<th width="*">Topic</th> <th width="13%">Type of paper</th>	<th width="13%">Pages (words)	</th> <th width="15%">Deadline</th> <th width="11%">Bids</th> <th width="17%">Client</th> </tr>';
			
		$.each(res,function(index,ele){
						
			
		});
	});
}