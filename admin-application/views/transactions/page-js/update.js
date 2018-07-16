function showTransactionId(val) {
	callAjax(generateUrl('transactions', 'getTransactionIdField', [val]), '&outmode=json', function(t){	
		$('#trans_field').html(t);
		
		if (val == 1) {
			$('span#trans_field').closest('tr').removeClass('hide');
		}
		else if (val == 2) {
			$('span#trans_field').closest('tr').addClass('hide');
		}    
	});
}