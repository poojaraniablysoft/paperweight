$(document).ready(function() {
	//When page loads...
	$(".selected_content").hide(); //Hide all content
	$(".normalTab li:nth-child(2)").addClass("select").show(); //Activate first tab
	$(".selected_content:nth-child(2)").show(); //Show first tab content

	//On Click Event
	$(".normalTab li").click(function() {
		$(".normalTab li").removeClass("select"); //Remove any "active" class
		$(this).addClass("select"); //Add "active" class to selected tab
		$(".selected_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
 
	function updatePrice(comission) {
		var price = parseFloat($("#bid_price").val());
		/* if ( (new Number($("#bid_price").val())).isInteger() ) {
			if(price > 10000 && price < 0.1) {
				$.facebox('Please enter value between 0.1 to 10000');
			} 
		}else {
			
		} */
		
		if(price > 10000) {
			$.facebox('<div class="popup_msg failure">You can bid maximum $10000.</div>');
			$('#btn_submit').focus();
		}
	
		if (isNaN(price)) price = 0;
		price = (price-(price*comission)/100).toFixed(2);
		
		return price;
	}
	
	$("#bid_price").keyup(function() {
		var comission = parseInt($("#task_comission").val());
		
		/*$("#total_price").html(updatePrice(pages));  */
		$("#total_price").html(updatePrice(comission));
	});
});

function confirmBid() {
	var form_data = getFrmData(document.frmbid);
	
	var bid_price = $('#bid_price').val();
	var bid_task_id = $('#bid_task_id').val();
	var bid_id = $('#bid_id').val();
	var task_pages = $('#task_pages').val();
	var task_comission = $('#task_comission').val();
	
	if(bid_price > 10000) {
		$.facebox('<div class="popup_msg failure">You can bid maximum $10000.</div>');
		return false;
	}
	
	if (bid_price.trim() == '') {
		$('#bid_price').focus().addClass('error');
		return;
	}
	
	//var form_data = 'bid_id='+ bid_id +'&bid_task_id='+ bid_task_id +'&task_pages='+ task_pages +'&bid_price='+ bid_price +'&task_comission='+ task_comission;
	
	callAjax(generateUrl('bid', 'confirm_bid'), form_data, function(t) {
		var ans = parseJsonData(t);
		
		if (ans === false) {
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		if (ans.status == 0) {
			$.facebox('<div class="popup_msg failure">'+ans.msg+'</div>');
			return;
		}
		
		if (ans.status == 1) {
			$.facebox(ans.data);
		}
	}); 
}