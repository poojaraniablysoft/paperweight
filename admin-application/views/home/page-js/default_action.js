/* 
function getOrderList(is_ajax=0) {
	if(is_ajax){
	//do nothing	
	}else{
	$('#orders_listing').find('div.box_content').html('<img src="/images/loader.gif">');
	}
	callAjax(generateUrl('home', 'orders_listing'),'', function(t){		
		var ans = parseJsonData(t);		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		str = '<table width="100%" class="dataTable">';
		str += '<thead><tr><th>S.No.</th><th>Order ID</th><th>Orders Topic</th><th>Deadline Date</th><th>Approved By Admin</th><th>Status</th><th>Action</th></tr></thead>';
		str += '<tbody>';
		if (ans.order_listing.length == 0) return;
		
		$.each(ans.order_listing, function(index,arr) {			
			str += '<tr class=" inactive"><td>' + (index+1) + '</td><td>#' + arr['task_ref_id'] + '</td><td width="20%">' + arr['task_topic'] + '</td><td>' + arr['task_due_date'] + '</td><td><a class="toggleswitch actives" title="Toggle Order verification status" onclick="updateOrderVerificationStatus(' + arr['task_id'] + ', $(this))" id="verified_' + arr['task_id'] + '" href="javascript:void(0);"></a></td><td><span class="textred">Pending approval</span></td><td><a class="button small black" title="Preview" href="' + generateUrl('orders','order_details',[arr['task_id']]) + '">Preview</a><a class="button small green" title="Bids Listing" href="' + generateUrl('orders','bids',[arr['task_id']]) + '">Bids</a></td></tr>';

		});
		str += '</tbody></table>';
		
		$('#orders_listing').find('div.box_content').html(str);
	});
}
 */
/* 
function markFeatured(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'mark_featured'), 'id=' + id, function(t){
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
	});
}
 */
function updateEmailVerificationStatus(id, el) {
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'update_email_verification_status'), 'id=' + id, function(t){
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
	});
}

function updateUserVerificationStatus(id, el) {	
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'mark_user_verified_by_admin'), 'id=' + id, function(t){
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
		getNonVerifiedUserList();
	});
}


function getNonVerifiedUserList() {
	//$('#arrTestTaker').find('div.box_content').html('<img src="/images/loader.gif">');
	callAjax(generateUrl('home', 'verify_user'),'', function(t){
		
		var ans = parseJsonData(t);
		
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return;
		}
		
		str = '<table width="100%" class="dataTable">';
		str += '<thead><tr><th>S.No.</th><th>Email</th><th>Is User Featured</th><th>User Status</th><th>Is Email Verified</th><th>Verified by Admin</th><th>Action</th></tr></thead>';
		str += '<tbody>';
		if (ans.arrTestTaker.length == 0) return;
		
		$.each(ans.arrTestTaker, function(index,arr) {	
			str += '<tr><td>' + (index+1) + '</td><td>' + arr['user_email'] + '</td><td><a class="toggleswitch actives" title="Toggle featured status" onclick="markFeatured(' + arr['user_id'] + ', $(this))" id="status_' + arr['user_id'] + '" href="javascript:void(0);"></a></td><td><ul class="iconbtns"><li><a title="Toggle featured status" onclick="updateUserStatus(' + arr['user_id'] + ', $(this))" id="status_' + arr['user_id'] + '" href="javascript:void(0);"><img class="whiteicon active_icon" alt="" src="/images/actives.png"></a></li></ul></td><td><a title="Toggle email verification status" onclick="updateEmailVerificationStatus(' + arr['user_id'] + ', $(this))" id="verified_' + arr['user_id'] + '" href="javascript:void(0);"></a><a class="toggleswitch"></a></td><td><a class="toggleswitch actives" title="Toggle User verification status" onclick="updateUserVerificationStatus(' + arr['user_id'] + ', $(this))" id="verified_' + arr['user_id'] + '" href="javascript:void(0);"></a></td><td><a title="Details" class="button small black" href="' + generateUrl('user','preview_writer_profile',[arr['user_id']]) + '">Details</a></td></tr>';

		});
		str += '</tbody></table>';
		
		$('#arrTestTaker').find('div.box_content').html(str);
	});
}

function updateUserStatus(id, el) { 
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('user', 'update_user_status'), 'id=' + id, function(t){
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
		getDeactiveUserRequestList();		
	});
}


function getDeactiveUserRequestList() {
	$('#arruserdelete').find('div.box_content').html('<img src="/images/loader.gif">');
	callAjax(generateUrl('home', 'user_delete_req'),'', function(t){
		
		var ans = parseJsonData(t);
		var user = '';
		var profile = '';
		if (ans === false){
			$.facebox('<div class="popup_msg failure">Oops! Internal error.</div>');
			return false;
		}
		str = '<table width="100%" class="dataTable">';
		str += '<thead><tr><th>S.No.</th><th>Email</th><th>User Type</th><th>User Status</th><th>Action</th</tr></thead>';
		str += '<tbody>';
		if (ans.arruserdelete.length == 0) {
			str += '<tr class="inactive"><td>No records found</td></tr>';
		//return;
		}
		
		$.each(ans.arruserdelete, function(index,arr) {			
			if(arr['user_type'] == '1'){
				user = 'Writer';
				profile = 'preview_customer_profile';
			}else {
				user = 'Customer';
				profile = 'preview_writer_profile';
			}
			
			str += '<tr class=" inactive"><td>' + (index+1) + '</td><td>' + arr['user_email'] + '</td><td><p id="ml_' + arr['user_id'] + '">'+ user +'</p></td><td><ul class="iconbtns"><li><a href="javascript:void(0);" id="status_' + arr['user_id'] + '" onclick="updateUserStatus(' + arr['user_id'] + ', $(this))" title="Toggle featured status"><img src="/images/actives.png" alt="" class="whiteicon active_icon"></a></li></ul></td><td><a href="' + generateUrl('user', profile ,[arr['user_id']]) +'" class="button small black" title="Details">Details</a></td></tr>';

		});
		
		str += '</tbody></table>';
		//alert(str);
		$('#arruserdelete').find('div.box_content').html(str);
	});
}

function changeOrderStatus(id,el) {
	//$.facebox(order_status);return;
	el.html('<img src="/images/loader.gif">');
	callAjax(generateUrl('orders', 'change_order_status'),'task_id=' + id + '&status=' + el, function(t){
		var ans = parseJsonData(t);
		if (ans === false){
			$('#common_msg').html('<div class="popup_msg failure">Oops! Error. </div>');
			return false;
		}
	
		$('#common_msg').html(ans.msg);
		if(ans.status == 0) {
			return false;
		}
		updateOrderChangeReqTbl();
		//$.facebox('<div class="popup_msg success">'+ans.msg+'</div>');
		//$.facebox(ans.msg);
	});
}

function updateOrderChangeReqTbl(){
	callAjax(generateUrl('home', 'order_cancel_req'),'', function(t){
		
		var ans = parseJsonData(t);
		
		/* if (ans.status === 0){
			$.facebox('Oops! Internal error.');
			return;
		} */
		str = '<table width="100%" class="dataTable">';
		str += '<thead><tr><th>S.No.</th><th>Order Id</th><th>Order Topic</th><th>Deadline</th><th>Posted On</th><th>Action</th</tr></thead>';
		str += '<tbody>';
		if (ans.arrOrderCancelReq.length == 0) {
			str += '<tr class="inactive"><td>No records found</td></tr>';
		//return;
		}
		
		$.each(ans.arrOrderCancelReq, function(index,arr) {			
			
			
			str += '<tr class=" inactive"><td>' + (index+1) + '</td><td>#' + arr['task_ref_id'] + '</td><td>' + arr['task_topic'] + '</td><td>' + arr['task_due_date'] + '</td><td>' + arr['task_posted_on'] + '</td><td><select id="change_status_' + arr['task_id'] + '" onchange="return changeOrderStatus('+ arr['task_id'] + ',this.value);">';
			$.each(order_status, function(inx,value){
				if(inx == arr['task_status']) {
					var selct = 'selected = "selected"';
				}
				str += '<option value="'+ inx +'" '+ selct +'>'+ value +'</option>';
			});
			str += '</select></td></tr>';

		});
		
		str += '</tbody></table>';
		$('#arrOrderCancelReq').find('div.box_content').html(str);
	});
}