<?php
function dieWithError($err){
	global $post;
	if ($post['outmode'] == 'json')	dieJsonError($err);
	die($err);
}

function redirectUser($url=''){
	if ($url == '') $url = $_SERVER['REQUEST_URI'];
	header("Location: " . $url);
	exit;
}

function generateUrl($model='', $action='', $queryData=array(), $use_root_url='', $url_rewriting = null){
    if ($url_rewriting === null) $url_rewriting = CONF_URL_REWRITING_ENABLED;    
    if ($use_root_url == '') $use_root_url = CONF_USER_ROOT_URL;    
    foreach ($queryData as $key=>$val) $queryData[$key] = rawurlencode($val);    
    if ($url_rewriting){
        $url = rtrim($use_root_url . strtolower($model) . '/' . strtolower($action) . '/' . implode('/', $queryData), '/ ');
        if ($url == '') $url = '/';		
		//echo $url;
		if (!preg_match('/manager/', $url) && $url != '/') $url = formatUrl($url);
	
        return $url;
    }
    else {
        $url = rtrim($use_root_url . 'index.php?url=' . strtolower($model) . '/' . strtolower($action) . '/' . implode('/', $queryData), '/');
        return $url;
    }
}

function generateAbsoluteUrl($model='', $action='', $queryData=array(), $use_root_url='', $url_rewriting=null) {
    return getUrlScheme() . generateUrl($model, $action, $queryData, $use_root_url, $url_rewriting);
}

function getUrlScheme() {
 $pageURL = 'http';
 if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"];
 }
 return $pageURL;
}

function formatUrl($url) {
	$arr_url = explode('/', $url);
	$arr_url = array_filter($arr_url, 'strlen');
	$arr_url = array_values($arr_url);
	$model = strtolower($arr_url[0]);
	//echo "<pre>".print_r($arr_url,true)."</pre>";
	if (!in_array($model, array('cms'))) return $url;
	array_shift($arr_url);
	
	$action = strtolower($arr_url[0]);
	if(isset($arr_url[1])) array_shift($arr_url);
	
	$controller = ucfirst($model) . 'Controller';
	
	if (!class_exists($controller)) {
		require_once CONF_INSTALLATION_PATH . 'application/controllers/' . strtolower($controller) . '.php';
	}
	
	$obj = new $controller(ucfirst($model), $model, 'get_url_data');
	$arr_data = $obj->get_url_data($action,$arr_url);

	switch($model) {
		case 'cms':
			switch($action) {
				case 'page':
					$formatted_url = '/' . $arr_data['type'] ;
					if($arr_data['name']) $formatted_url .= '/' . createUrlString($arr_data['name']) ; 
					if($arr_data['year']) $formatted_url .= '/' . $arr_data['year'] ;
					if($arr_data['id']) $formatted_url .= '/' . $arr_data['id'] ;
					
					break;
				case 'contact_us':
					
					$formatted_url = '/' . $arr_data['type'] ;
					if($arr_data['name']) $formatted_url .= '/' . createUrlString($arr_data['name']) ; 
					if($arr_data['year']) $formatted_url .= '/' . $arr_data['year'] ;
					if($arr_data['id']) $formatted_url .= '/' . $arr_data['id'] ;
					
					break;
				default:
					$formatted_url = $url;
			}
			
			break;
		default:
			$formatted_url = $url;
	}
	//echo $formatted_url;exit;
	return $formatted_url;
}

function createUrlString($str) {
	if (trim($str) == '') return '-';
	
	$str = preg_replace('/\W+/', ' ', $str);
	$str = preg_replace('/\s+/', '-', $str);
	$str = trim($str, '-');
	
	return strtolower($str);
}

function writeMetaTags(){
	echo '<title>' . CONF_WEBSITE_NAME . '</title>' . "\n";
}

function encryptPassword($pass){
	return md5(PASSWORD_SALT . $pass . PASSWORD_SALT);
}

function getRandomPassword($n){
    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass='';
    for($i=0; $i<$n; $i++){
        $pass .= substr($chars,rand(0, strlen($chars)-1), 1);
    }
    return $pass;
}

/**
 * 
 * Saves an image
 * @param String $fl full path of the file
 * @param String $name name of file
 * @param String $response In case of success exact name of file will be returned in this else error message
 * @return Boolean
 */
function saveImage($fl, $name, &$response, $is_profile_pic=false ,$location = '') {
    $size = getimagesize($fl);
	
    if ($size === false || !in_array($size[2],array(2,3))){
        $response = 'Image format not recognized. Please try with jpg, jpeg or png file.';
        return false;
    }
	
    $fname = preg_replace('/[^a-zA-Z0-9]/', '', $name);
	
	if ($is_profile_pic) {
		$jpeg_quality = 100;

		$width	= $size[0];
		$height	= $size[1];
		
		$dim = min($width, $height);
		
		if ($width > $height) {
			$x1 = ($width - $height)/2;
			$y1 = 0;
		}
		else {
			$x1 = 0;
			$y1 = ($height - $width)/2;
		}
		
		if ($size[2] == 2) {
			$img_r = imagecreatefromjpeg($fl);
		}
		else {
			$img_r = imagecreatefrompng($fl);
		}
		
		$dst_r = ImageCreateTrueColor($dim, $dim);
		
		imagecopyresampled($dst_r, $img_r , 0, 0, $x1, $y1, $dim, $dim, $dim, $dim);		
		imagejpeg($dst_r, $fl, $jpeg_quality);
	}
	
    while (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)){
        $fname .= '_' . rand(10, 99);
    }

   $location = CONF_INSTALLATION_PATH . 'user-uploads/' . $location;
    
    if(!file_exists($location)){
        mkdir($location, 0777);
    }

    if (!copy($fl, $location . $fname)){
        $response = 'Could not save the file.';
        return false;
    }

    $response = $fname;
    return true;
}

function getImage($file_name='', $w=0, $h=0,$default_img = 'default.jpg', $crop = ImageResize::IMG_RESIZE_EXTRA_CROP) {
	if (!is_numeric($w)) $w = 0;
	if (!is_numeric($h)) $h = 0;
	//die($file_name);
	if($file_name=='' || !file_exists($file_name) || $file_name === CONF_INSTALLATION_PATH . 'user-uploads/post-images/'){
        $file_name = CONF_INSTALLATION_PATH.'public/images/'.$default_img;
    }
	if ($w == 0) $w = 100;
	if ($h == 0) $h = 100;
	
	$image = new ImageResize($file_name);
	if (!$image) die('Image not found.');
	
	if (function_exists('apache_request_headers')) {
		$headers = apache_request_headers();
	}else {
		$headers = array();		
	}
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($file_name))) { 
		header('Cache-Control: public');
		header("Pragma: public"); 
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file_name)).' GMT', true, 304);
		header('Expires: ' . date('D, d M Y H:i:s', strtotime("+2 Day")));
		exit; 
	}
	header('Cache-Control: public');
	header("Pragma: public");
	header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+2 Day')).' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file_name)).' GMT', true, 200);
	
	$image->setMaxDimensions($w, $h);
	#$image->setResizeMethod(3);
	$image->setResizeMethod($crop);
	header("content-type: image/png");
	
	$image->displayImage();
}


function showOriginalImage($file='',$path_suffix){
	
	if($file === '' || !is_string($file) || strlen(trim($file)) < 1) $file = 'no-img.jpg';
	//$file=getRealFilePath($file); 	
	$img = CONF_INSTALLATION_PATH . 'user-uploads/'. $path_suffix . $file;
	if(!file_exists($img)) $img = CONF_INSTALLATION_PATH . 'user-uploads/' . $path_suffix . $no_img;
	if(!file_exists($img)) $img = CONF_INSTALLATION_PATH . 'user-uploads/'  . $no_img;
	if(!file_exists($img)) $img = CONF_INSTALLATION_PATH . 'public/images/' . $no_img;
	//header("content-type: image/jpeg");
	$filename = basename($file);
	$file_extension = strtolower(substr(strrchr($filename,"."),1));
	if ($file_extension!="svg"){
		header("content-type: image/jpeg");
	}else if ($file_extension!="png"){
		header("content-type: image/png");
	}else{
		header("Content-type: image/svg+xml");
	}
	if (function_exists('apache_request_headers')) {
		$headers = apache_request_headers();
	}else {
		$headers = array();
		
	}
	
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($img))) { 
		header('Cache-Control: public');
		header("Pragma: public"); 
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($img)).' GMT', true, 304);
		header('Expires: ' . date('D, d M Y H:i:s', strtotime("+2 Day")));
		exit; 
	}
	header('Cache-Control: public');
	header("Pragma: public");
	header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+2 Day')).' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($img)).' GMT', true, 200);
	//header("Content-Type: image/jpg");
	echo file_get_contents($img);
	return true;
}

function saveFile($fl, $name, &$response){
    $fname = preg_replace('/[^a-zA-Z0-9]/', '', $name);
    while (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)){
        $fname .= '_' . rand(10, 99);
    }

    if (!copy($fl, CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)){
        $response = 'Could not save file.';
        return false;
    }

    $response = $fname;
    return true;
}

function createButton($caption){
    return $caption;
}

function sendMail($to, $subject, $body, $extra_headers=''){
    $send_email_by = CONF_EMAIL_SEND_VIA;
	if($send_email_by==CONF_EMAIL_SEND_VIA_SMTP){
		$res = sendSmtpMail($to, $subject, $body);
		
		if($res['status']!='sent' || $res['reject_reason']!='')
		{
			$message = "Hi,<br><br>";
			$message .= "Current status to send email for ".$subject." to ".$res['email']." is ".$res['status'];
			$message .= ", due to: ".$res['reject_reason'];                
			$message .= ".<br><br>";
			$message .= "Thanks,<br><br>";
			$message .= CONF_WEBSITE_NAME." Team";
			$sub = 'Mail could not sent via SMTP on '.CONF_WEBSITE_URL;
			mail(CONF_ADMIN_EMAIL_ID, $sub, $message);
		}
		else{
			return true;
		}
	}
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    $headers .= 'From: ' .CONF_EMAILS_FROM_NAME. '<'. CONF_EMAILS_FROM . "> \r\n";

    if ($extra_headers != '') $headers .= $extra_headers;

    mail($to, $subject, $body, $headers);
	return true;
}

// smtp mail send
function sendSmtpMail($to, $subject, $body, $from=CONF_EMAILS_FROM, $attachments=array(), $from_name =CONF_EMAILS_FROM_NAME){
    // $to = 'user@dummyid.com';
    require_once dirname(__FILE__).'/phpmailer/class.phpmailer.php';
    $mail = new PHPMailer();
    $mail->IsSMTP();  // telling the class to use SMTP
    // $mail->Mailer = "smtp";
    
    $mail->SMTPSecure = (CONF_SMTP_MAIL_SECURE==0) ? 'ssl' : 'tls';
    $mail->Host     = CONF_SMTP_MAIL_HOST; // SMTP server

    $mail->Port = CONF_SMTP_MAIL_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = CONF_SMTP_MAIL_USERNAME;
    $mail->Password = CONF_SMTP_MAIL_PASSWORD;
    $mail->IsHTML(true);
    $mail->SMTPDebug = 0;
    $mail->AddAddress($to);
    $mail->From     = $from;
    $mail->FromName = $from_name;
    $mail->Subject  = $subject;
    $mail->Body     = $body;
	
    
    if(!empty($attachments))
    $mail->AddAttachment($attachments['file'], $attachments['filename']);
    $res = array();
    if(!$mail->Send()) {
      $res['status'] = 'rejected';
      $res['reject_reason'] = $mail->ErrorInfo;
      $res['email'] = $to;
      return $res;
    } else {
      $res['status'] = 'sent';
      return $res;
    }
}


function sendMandrillMail($to, $subject, $body, $from=CONF_EMAILS_FROM, $attachments=array()){
    require_once dirname(__FILE__).'/Mandrill.php';
    $mandrill = new Mandrill(CONF_MANDRILL_API_KEY);
    $message = array(
        'subject' => $subject,
        'from_email' => CONF_EMAILS_FROM,
        'from_name' => CONF_EMAILS_FROM_NAME,
        'html' => $body,
        'to' => array(array('email' => $to))
	);
	
    if(!empty($attachments)){
        $attachment = array('attachments'=>
                    array(
                    array(
                        'content' => base64_encode(file_get_contents($attachments['file'])),
                        'type' => 'application/octet-stream',
                        'name' => $attachments['filename'],
                    )
                    )
                );
        $message = array_merge($message, $attachment);
    }
    return $mandrill->messages->send($message);
}

function generateNewPagingStringHtml($page,$pages,$event='',$url='',$total_records,$pagesize) {
	$pagestring = '';
	
	$onClick = $event == '' ? 'setPage(xxpagexx,document.frmPaging);' : $event;
	if ($url == '') $url = 'javascript:void(0);';
	
	if ($pages > 1) {
		if ($event == '') $pagestring .= createHiddenFormFromPost('frmPaging', '?', array('page'), array('page'=>''));
		$pagestring .= '<div class="pagination fr"><ul><li><a href="javascript:void(0);">DISPLAYING RECORD ' . (($page - 1) * $pagesize + 1) . 
		' TO ' . (($page * $pagesize > $total_records)?$total_records:($page * $pagesize)) . ' OF ' . $total_records.'</a></li>';
		//if ($page > 1) $pagestring .= '<li><a href="' . $url . '" onclick="' . str_replace('xxpagexx',($page-1),$onClick) . '"></a></li>';
		$pagestring .= getPageString('<li><a href="' . $url . '" onclick="' . $onClick . '">xxpagexx</a></li>', $pages, $page, '<li class="selected"><a href="' . $url . '">xxpagexx</a></li>');
		//if ($page < $pages) $pagestring .= '<li><a href="' . $url . '" onclick="' . str_replace('xxpagexx',($page+1),$onClick) . '"></a><li>';
		$pagestring .= '</ul></div>';
	}
	
	return $pagestring;
}

function generatePaging($pagesize, $total_records, $page, $pages) {
		$pagestring = '';
	
	 /* $arr 		= array_merge($params, array('xxpagexx'));
	$arr_next 	= array_merge($params, array($page+1));
	$arr_prev 	= array_merge($params, array($page-1));
	
	
	// $pagestring = '';  */

	if ($pages > 0) {
		$pagestring .= '<div class="pagination fr"><ul><li><a href="javascript:void(0);">DISPLAYING RECORD ' . (($page - 1) * $pagesize + 1) . 
		' TO ' . (($page * $pagesize > $total_records)?$total_records:($page * $pagesize)) . ' OF ' . $total_records.'</a></li>';
		$pagestring .= getPageString('<li><a href="javascript:void(0);" onclick="goToPage(xxpagexx);">xxpagexx</a></li>'
		, $pages, $page,'<li class="selected"><a class="active" onclick="goToPage(xxpagexx);" href="javascript:void(0);">xxpagexx</a></li>');
		$pagestring .= '</div>';
		}
	return $pagestring;

}

function generatePagingStringHtml($page,$pages,$event='',$url='') {
	$pagestring = '';
	
	$onClick = $event == '' ? 'setPage(xxpagexx,document.frmPaging);' : $event;
	if ($url == '') $url = 'javascript:void(0);';
	
	if ($pages > 1) {
		/* if ($event == '')  */$pagestring .= createHiddenFormFromPost('frmPaging', '?', array('page'), array('page'=>''));
		$pagestring .= '<ul>';
		if ($page > 1) $pagestring .= '<li><a href="' . $url . '" onclick="' . str_replace('xxpagexx',($page-1),$onClick) . '" class="left-arrow"><i class="icon ion-chevron-left"></i></a></li>';
		$pagestring .= getPageString('<li><a href="' . $url . '" onclick="' . $onClick . '">xxpagexx</a></li>', $pages, $page, '<li><a class="active" href="' . $url . '">xxpagexx</a></li>');
		if ($page < $pages) $pagestring .= '<li><a href="' . $url . '" onclick="' . str_replace('xxpagexx',($page+1),$onClick) . '" class="right-arrow"><i class="icon ion-chevron-right"></i></a><li>';
		$pagestring .= '</ul>';
	}
	
	return $pagestring;
}

function generateBackendPagingStringHtml($pagesize,$total_records,$page,$pages,$filter) {
	$pagestring = '';
	
	 /* $arr 		= array_merge($params, array('xxpagexx'));
	$arr_next 	= array_merge($params, array($page+1));
	$arr_prev 	= array_merge($params, array($page-1));
	
	
	// $pagestring = '';  */

	if ($pages > 0) {
		$pagestring .= createHiddenFormFromPost('frmPaging', '?', array('page'), array('page'=>'','filter'=>($filter != null) ? $filter : ''));
		$pagestring .= '<div class="pagination fr"><ul><li><a href="javascript:void(0);">DISPLAYING RECORD ' . (($page - 1) * $pagesize + 1) . 
		' TO ' . (($page * $pagesize > $total_records)?$total_records:($page * $pagesize)) . ' OF ' . $total_records.'</a></li>';
		$pagestring .= getPageString('<li><a href="javascript:void(0);" onclick="setPage(xxpagexx,document.frmPaging);">xxpagexx</a></li>'
		, $pages, $page,'<li class="selected"><a class="active" href="javascript:void(0);">xxpagexx</a></li>');
		$pagestring .= '</div>';
		}
	return $pagestring;
}


function getOrderAmount($price_per_page,$total_pages) {
	$total_pages = 1;
	$bid_price			= floatval($price_per_page) * intval($total_pages);
	$service_comission	= ($bid_price + CONF_SERVICE_CHARGE);//($bid_price * CONF_SERVICE_COMISSION)/100;
	
	$total_price		= number_format($service_comission, 2, '.', '');
	
	return $total_price;
}

function getBidAmount($price_per_page,$total_pages) {
	$total_pages = 1;
	$bid_price		= floatval($price_per_page) * intval($total_pages);
	
	$total_price	= number_format($bid_price,2, '.', '');
	
	return $total_price;
}

/* function getAmountPayableToWriter($amount,$price_per_page,$total_pages) {
	$amount = floatval($amount);
	
	$amount_payable = (getBidAmount($price_per_page,$total_pages)/getOrderAmount($price_per_page,$total_pages))*$amount;
	
	return number_format($amount_payable, 2, '.', '');
}
 */
 
 function getAmountPayableToWriter($bid_price) {
	$amount = floatval($amount);
	
	$amount_payable = ($bid_price-($bid_price*CONF_SERVICE_COMISSION)/100);
	
	return number_format($amount_payable, 2, '.', '');
}
 
 
function getBreadCrumb($controller='', $action='') {
	global $requested_controller;
	global $requested_action;
	global $requested_query;
	
	if ($controller == '') $controller = $requested_controller;
	if ($action == '') $action = $requested_action;
	
	switch($controller) {
		case 'TaskController':
			switch($action) {
				case 'assign_task':
					$bread_crumb = 'Assign Writer';
					break;
				case 'my_orders':
					$bread_crumb = 'My Orders';
					break;
				case 'order_bids':
					$bread_crumb = 'Order Bids';
					break;
				case 'orders':
					$bread_crumb = 'Browse Orders';
					break;
				case 'bid_preview':
					$bread_crumb = 'Bid Details';
					break;
				case 'add':
					foreach($requested_query as $val);
					$bread_crumb = ($val > 0) ? 'Change Order':'Place Order';
					break;
				case 'order_process':
					$bread_crumb = 'Order Process';
					break;
				case 'order_history':
					$bread_crumb = 'Order History';
					break;
				
			}
			break;
		case 'TransactionsController':
			switch($action) {
				case 'payments':
					$bread_crumb = 'Payments';
					break;
				case 'earnings':
					$bread_crumb = 'Earnings';
					break;
				case 'order_payment':
					$bread_crumb = 'Order Payment';
					break;
			}
			break;
		case 'BidController':
			switch($action) {
				case 'add':
					$bread_crumb = intval($requested_query[1]) > 0 ? 'My Orders' : 'Browse Orders';					
					break;
				case 'my_bids':
					$bread_crumb = 'My Orders';
					break;
				case 'bid_details':
					$bread_crumb = 'Bid Details';
					break;
			}
			break;
		case 'UserController':
			switch($action) {
				case 'dashboard':
					$bread_crumb = 'Dashboard';
					break;
				case 'update_profile':
					$bread_crumb = 'My Profile';
					break;
				case 'signin':
					$bread_crumb = 'Sign In';
					break;
				case 'forgot_password':
					$bread_crumb = 'Forgot Password';
					break;
				case 'top_writers':
					$bread_crumb = 'Top writers';
					break;
			}
			break;
		case 'SupportController':
			switch($action) {
				case 'default_action':
					$bread_crumb = 'Support';
					break;
			}
			break;
		case 'MessagesController':
			switch($action) {
				case 'default_action':
					$bread_crumb = 'Messages';
					break;
			}
			break;
		case 'ReviewsController':
			switch($action) {
				case 'default_action':
					$bread_crumb = 'Reviews';
					break;
				case 'testimonials':
					$bread_crumb = 'Testimonials';
					break;
				case 'user_reviews':
					$bread_crumb = 'Reviews';
			}
			break;
		case 'SampleController':
			switch($action) {
				case 'default_action':
					$bread_crumb = 'Sample Essay';
					break;
				case 'sample_essay':
					$bread_crumb = 'Sample Essay';
					break;
			}
			break;
	}
	
	return $bread_crumb;
}

function getPageTabs($ord_count) {
	global $requested_controller;
	global $requested_action;
	$cur_tab = ($requested_action == '' || $requested_action == 'default_action')?  strtolower(str_replace('Controller','',$requested_controller)) : $requested_action ;
	$my_orders = array('my_orders','order_bids','add','order_process','bid_details','bid_preview','my_bids','assign_task');

	$str  = '';
	if (!User::isWriter()) { 
		$str .= '<li><a ' . ($cur_tab == 'dashboard' ? 'class="active"' : '') . ' href="' . generateUrl('user', 'dashboard') . '"><span class="cm-icon db-icon2"></span>Dashboard</a></li>';
		$str .= '<li><a ' . (in_array($cur_tab,$my_orders) ? 'class="active"' : '') . ' href="' . generateUrl('task', 'my_orders') . '"><span class="cm-icon order-icon2"></span>My Orders</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'update_profile' ? 'class="active"' : '') . ' href="' . generateUrl('user', 'update_profile') . '"><span class="cm-icon profile-icon2"></span>My Profile</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'payments' ? 'class="active"' : '') . ' href="' . generateUrl('transactions', 'payments') . '"><span class="cm-icon payment2"></span>Payments</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'messages' ? 'class="active"' : '') . ' href="' . generateUrl('messages') . '"><span class="cm-icon message2"></span>Messages <span class="count"></span> </a></li>';
		$str .= '<li><a ' . ($cur_tab == 'reviews' ? 'class="active"' : '') . ' href="' . generateUrl('reviews') . '"><span class="cm-icon reviews2"></span>Reviews</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'support' ? 'class="active"' : '') . ' href="' . generateUrl('support') . '"><span class="cm-icon support2"></span>Support</a></li>';
	}else { 
		$str .= '<li><a ' . ($cur_tab == 'dashboard' ? 'class="active"' : '') . ' href="' . generateUrl('user', 'dashboard') . '"><span class="cm-icon db-icon"></span>Dashboard</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'orders' ? 'class="active"' : '') . ' href="' . generateUrl('task', 'orders') . '"><span class="cm-icon order-icon"></span>Browse Orders'.($ord_count != '' ? ' ('.$ord_count.')' : '').'</a></li>';
		$str .= '<li><a ' . (in_array($cur_tab,$my_orders) ? 'class="active"' : '') . ' href="' . generateUrl('bid', 'my_bids') . '"><span class="cm-icon order-icon"></span>My Orders</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'update_profile' ? 'class="active"' : '') . ' href="' . generateUrl('user', 'update_profile') . '"><span class="cm-icon profile-icon"></span>My Profile</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'earnings' ? 'class="active"' : '') . ' href="' . generateUrl('transactions', 'earnings') . '"><span class="cm-icon payment"></span>Earnings</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'messages' ? 'class="active"' : '') . ' href="' . generateUrl('messages') . '"><span class="cm-icon message"></span>Messages <span class="count"></span></a></li>';
		$str .= '<li><a ' . ($cur_tab == 'reviews' ? 'class="active"' : '') . ' href="' . generateUrl('reviews') . '"><span class="cm-icon reviews"></span>Reviews</a></li>';
		$str .= '<li><a ' . ($cur_tab == 'support' ? 'class="active"' : '') . ' href="' . generateUrl('support') . '"><span class="cm-icon support"></span>Support</a></li>';
	} 	
	return $str;
}

function get_time_zone_abbr() {
$objDate = new DateTime();
$objDate->setTimeZone(new DateTimeZone(CONF_TIMEZONE));
return $objDate->format('T');
}

function print_time_zone() {
echo ' (' . get_time_zone_abbr() . ')';
}

/** function to calculate the time left **/
function time_diff($end_date,$start_date="NOW") {
	/* if(isset($_COOKIE['tmzoneName']) && !empty($_COOKIE['tmzoneName'])){
		$start_date = dateForTimeZone($_COOKIE['tmzoneName']);
		#$end_date = displayDate($end_date,true, true, $_COOKIE['tmzoneName']);
	} */
	
		switch(CONF_DATE_FORMAT_PHP){
			case 'd/m/Y':
			$phpdate = strtotime(str_replace('/','-',$end_date));				
			break;
			
			case 'm-d-Y':			
			$phpdate = strtotime(str_replace('-','/',$end_date));				
			break;			
			
			default :
			$phpdate = strtotime($end_date);				
		}		
		$end_date = date('Y-m-d H:i:s', $phpdate); 	
	
	$sdate = strtotime($start_date);
	$edate = strtotime($end_date);
	$time = $edate - $sdate;	
	$timeshift = '';	
	if ($time > 0) {
		$pmonth = $time/2592000;	
		if (floor($pmonth) > 0) $timeshift .= floor($pmonth) . 'm ';		
		$pday = ($pmonth - floor($pmonth)) * 30;		
		if (floor($pday) > 0) $timeshift .= floor($pday) . 'd ';
		$phour = ($pday - floor($pday))*24;		
		if (floor($phour) > 0) $timeshift .= floor($phour) . 'hr ';		
		$pmin = ($phour - floor($phour))*60;		
		if (floor($pmin) > 0) $timeshift .= floor($pmin) . 'min';		
		$timeshift .= ' left';
	}
	else {
		$delayed_by = round(abs(strtotime($start_date)-strtotime($end_date))/86400);		//number of days
		//$delayed_by = round(abs(strtotime("NOW")-strtotime($end_date))/86400);		//number of days
		
		if ($delayed_by > 0) {
			$timeshift = 'Delayed by ' . $delayed_by . ' ' . ($delayed_by == 1 ? 'day' : 'days');
		}
		else {
			$timeshift = 'Delayed';
		}
	}
	
	return $timeshift;
}

function encodeHtmlCharacters($str) {
	$arr_tags = array(
					'<p>'=>'[p]',
					'</p>'=>'[/p]',
					'<ul>'=>'[ul]',
					'</ul>'=>'[/ul]',
					'<em>'=>'[em]',
					'</em>'=>'[/em]',
					'<li>'=>'[li]',
					'</li>'=>'[/li]',
					'<ol>'=>'[ol]',
					'</ol>'=>'[/ol]',
					'<strong>'=>'[strong]',
					'</strong>'=>'[/strong]',
					'<span>'=>'[span]',
					'</span>'=>'[/span]',
					'<script>'=>'[script]',
					'</script>'=>'[/script]'					
					);
	
	foreach ($arr_tags as $key=>$val) {
		str_replace($key, $val, $str);
	}
	
	$str = htmlentities($str);
	
	foreach ($arr_tags as $key=>$val) {
		str_replace($val, $key, $str);
	}
	
	return $str;
}

function getWordCount($str) {		//@param: html string
	$word_count = 0;	
	$str = preg_replace('/<[^>]*>/', ' ', $str);	
	$str = str_replace("\r", '', $str);
	$str = str_replace("\n", ' ', $str);
	$str = str_replace("\t", ' ', $str);	
	$arr_words = explode(' ', $str);	
	foreach($arr_words as $key=>$val) {
		if (preg_match('/\p{L}/', $val)) $word_count++;
	}	
	return $word_count;
}

function getFooterMenu() {
	$db = &Syspage::getdb();	
	$srch = new SearchBase('tbl_cms','tc');	
	$srch->joinTable('tbl_navigation','LEFT OUTER JOIN','tv.nav_id=tc.cmspage_nav_id','tv');	
	$srch->addCondition('cmspage_active','=',1);
	$srch->addMultipleFields(array('cmspage_title','cmspage_name','cmspage_active','nav_caption','nav_display_order','cmspage_nav_id'));
	$srch->addOrder('cmspage_display_order','ASC');
	$srch->doNotLimitRecords();
	$rs = $srch->getResultSet();
	$row = $db->fetch_all($rs);
	foreach($row as $key=>$value) {
		switch($value['cmspage_nav_id']) {
			case '1' :
				$val['1'][] = $row[$key];
				break;
			case '2':
				$val['2'][] = $row[$key];
				break;
			case '3':
				$val['3'][] = $row[$key];
				break;
			case '4':
				$val['4'][] = $row[$key];
				break;
		}		
	}	
	return $val;
}

function confirm_payment_loaded($data) {
	$get = explode('&',$data);
	
	$needle = 'st=';
	$ret = array_keys(array_filter($get, function($var) use ($needle){
		return strpos($var, $needle) !== false;
	}));
	
	$status = explode('=',$get[$ret[0]]);
	
	if(isset($status[1])) {
		if ($status[1] == 'Completed') { 
			return('The payment has been completed, and the fund added to your wallet balance.');
		} else if ($status == 'Pending') { 
				return('The payment is pending.'.$data['cc']);
		} else { 
			return('Payment failed.');
		}		
	}else {
		return false;
	}

}

function datesToInterval($old_date, $new_date=''){
   $date1 = strtotime($old_date);

   if ($new_date == '') $new_date = date('Y-m-d H:i:s');
   $date2 = strtotime($new_date);
   $diff = $date2 - $date1;
   $days = floor($diff / 86400);
   
   if ($days == 1) return '1 Day ago';
   if ($days > 0) return $days . ' Days ago';
   $hours = floor($diff / 3600);
   if ($hours == 1) return '1 Hour ago';
   if ($hours > 0) return $hours . ' Hours ago';
   $minutes = floor($diff / 60);
   if ($minutes == 1) return '1 Minute ago';
   if ($minutes > 1) return $minutes . ' Minutes ago';
   return 'Recently';
}

function getMIMETypeInfo($file) {	
	$info = new finfo(FILEINFO_MIME_TYPE);
	//die($info->file($file));
	return $mime = $info->file($file);
}

/* Blog functions */
function myTruncateCharacters($string, $limit, $break=" ", $pad="...")
{
	if(strlen($string) <= $limit) return $string;
	
	$string = substr($string, 0, $limit);
	if(false !== ($breakpoint = strrpos($string, $break))) 
	{
		$string = substr($string, 0, $breakpoint);
	}
	return $string . $pad;
}

function dateFormat($format, $date)
{
	return date("$format",strtotime($date));
}

function seoUrl($string) {
    $string = strtolower($string);
    #$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    $string = preg_replace("/[^a-z0-9\s-]/", "", $string);
    $string = preg_replace("/[\s-]+/", " ", $string);
    #$string = preg_replace("/[\s_]/", "-", $string);
    $string = preg_replace("/[\s]/", "-", $string);
	$string = rtrim ( $string, '-');
    return $string;
}

function deleteFile($file, $path_suffix){
	if($file === '' || !is_string($file) || strlen(trim($file)) < 1) return false;
	$file_fullpath = CONF_INSTALLATION_PATH . 'user-uploads/'. $path_suffix . $file;
	if(file_exists($file_fullpath)){
		unlink($file_fullpath);
		return true;
	}
	return false;
}

function uploadContributionFile($file_tmp_name, $filename, &$response, $pathSuffix='')
{	
	$finfo = finfo_open(FILEINFO_MIME_TYPE); /*will return mime type*/
	$file_mime_type = finfo_file($finfo, $file_tmp_name);
	
	$accepted_files = array(
		'application/pdf', 
		'application/octet-stream', 
		'application/msword', 
		'text/plain', 
		'application/zip',
		'application/x-rar',
		'application/vnd.ms-excel',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
	);

	if(!in_array(trim($file_mime_type), $accepted_files)){
		$response = "Upload  only pdf, doc, docx, txt, zip or rar files.";
        return false;
	}
	
	$fname = preg_replace('/[^a-zA-Z0-9]/', '', $filename);
    while (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $pathSuffix . $fname)){
        $fname .= '_' . rand(10, 999999);
    }
	
    if (!copy($file_tmp_name, CONF_INSTALLATION_PATH . 'user-uploads/' . $pathSuffix . $fname)){
        $response = 'Could not save file.';
        return false;
    }
    $response = $fname;
    return true;
}



function isYoutubeVideoValid($url) {
	
       if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
         $video_id = $id[1];
       } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
             $video_id = $id[1];
       } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
             $video_id = $id[1];
       } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
             $video_id = $id[1];
       } else {   
             $video_id  = NULL;
       }
	
       if(empty($video_id))
       {
           return false;
       }
       
       $isValid = false;
       if (isValidURL($url)) {
           $idLength = 11;
           $idStarts = strpos($url, "?v=");
           if ($idStarts === FALSE) {
               $idStarts = strpos($url, "&v=");
		   }
		   if ($idStarts === FALSE) {
			$idStarts = strpos($url, "embed/");
		   }
           if ($idStarts !== FALSE) {
               //there is a video_id present, now validate it
               $v = substr($url, $idStarts, $idLength);
               $headers = get_headers('http://gdata.youtube.com/feeds/api/videos/' . $video_id);
				//echo $idStarts;exit;
               
               //did the request return a http code of 2xx?
                   $isValid = $video_id;
              /*  if (strpos($headers[0], '200')) {
               }   */              
           }
       }
       return $isValid;
   }
   
function isValidUrl($url) {
   return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}


function generateNoAuthUrl($model='', $action='', $queryData=array(), $use_root_url='') {
	$url = generateUrl($model, $action, $queryData, $use_root_url, false);
	$url = str_replace('index.php?', 'index_noauth.php?', $url);
	return 'http://' . $_SERVER['SERVER_NAME'] . $url;
}


function renderHtml($content='') {
    return htmlspecialchars_decode($content);
}