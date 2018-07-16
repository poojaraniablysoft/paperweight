function earn(filter,page) {
	callAjax(generateUrl('money', 'commission_detail'), 'filter=' + filter + '&page=' + page, function(t) {
		var ans = parseJsonData(t);
		var str = '';
		var i = 1;
		var block_id;
		var credit = '';
		var debit = '';
		
		//$.facebox(t);return;
		 if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		} 
		
		if (ans.data.length == 0) return;
		
		switch(filter) {
			case 'commission':
				block_id = 'commission';
				
				$.each(ans.data, function(index,arr) {
			
					str += '<tr><td>' + i++ + '</td>';
					str += '<td>#' + arr['task_ref_id'] + '</td>';
					str += '<td>' + arr['customer_name'] + '</td>';
					str += '<td>' + arr['writer_name'] + '</td>';
					str += '<td>$' + arr['me_cust_trans'] + '</td>';
					str += '<td>$' + arr['me_writer_recv'] + '</td>';
					str += '<td>$' + arr['system_amount'] + '</td>';
					str += '<td>' + arr['earn_date'] + '</td></tr>';
					
				});
				break;
				
			case 'transaction':
				block_id = 'transaction';
				
				$.each(ans.data, function(index,arr) {
					 if(arr['wtrx_amount'] < 0) {
						debit = '$' + Math.abs(arr['wtrx_amount']);
						credit = '--';
					}
					if(arr['wtrx_amount'] > 0){
						debit = '--';
						credit = '$' + arr['wtrx_amount'];
					} 
					
					str += '<tr><td>' + i++ + '</td>';
					str += '<td>' + arr['user_name'] + '</td>';
					str += '<td width="30%">' + arr['wtrx_comments'] + '</td>';
					str += '<td>' + debit + '</td>';
					str += '<td>' + credit + '</td>';
					str += '<td>' + arr['trans_date'] + '</td></tr>';
					
				});
				break;
				
			default:
				return;
				break;
		}
		
		$('section#' + block_id).find('table.dataTable').find('tbody').html(str);
		$('section#' + block_id).find('div.paginationwrap').html(ans.str_paging);
	});
}