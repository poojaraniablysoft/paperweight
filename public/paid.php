<?php
require_once 'application-top.php';
//echo "<pre>".print_r($_REQUEST,true)."</pre>";exit();
if(isset($_REQUEST['tx']) && isset($_REQUEST['st'])){
	$_SESSION['e_f_pp_return'] = array(
									'txn'=>$_REQUEST['tx'],
									'payment_status'=>$_REQUEST['st'],
									'amount'=>$_REQUEST['amt'],
									'currency_code'=>$_REQUEST['cc'],
									'custom'=>$_REQUEST['cm'],
								);
	redirectUser(generateUrl('transactions','confirm'));
}else{
	redirectUser(generateUrl('',''));
}