<?php
class ImageController extends LegitController{
    function default_action(){
		exit( Utilities::getLabel( 'E_Invalid_request' ));
    }
	
	function option($img = '',$org=false){
		if($org==false)
			return getImage($img, 78, 67, 'option-images/');
		else
			return showOrgImage($img, 0, 0, 'option-images/');
	}
	
	function prodtype($img = '',$org=false){
		if($org==false)
			return getImage($img, 78, 67, 'prodtype-images/');
		else
			return showOrgImage($img, 0, 0, 'prodtype-images/');
	}
	
	function optionpop($img = ''){
		return getImage($img, 193, 293, 'option-images/');
	}
	
	
	function product($type = 'large', $img = ''){
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage($img, 100, 100, 'product-images/', 'no-img.jpg');
				break;
			case 'LARGE':
				//return getImage($img, 390, 480, 'product-images/', 'no-img.jpg');
				return getImage($img, 256, 307, 'product-images/', 'no-img.jpg');
				break;
			default:
				//return getImage($img, 193, 293, 'product-images/', 'no-img.jpg');
				return getImage($img, 256, 307, 'product-images/', 'no-img.jpg');
		}
	}
	
	function post($type = 'large', $img = ''){
	//echo $type;exit;
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 120, 60 , 'no-img.jpg');
				break;
			case 'LARGE':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 800, 400, 'no-img.jpg');
				break;
			case 'MEDIUM':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 348, 174, 'video-img.png');
				break;
			default:
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 256, 307, 'no-img.jpg');
		}
	}
	
	function step($type = 'large', $img = ''){
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 216, 138, 'no-img.jpg', 1);
				break;
			case 'LARGE':
				return getImage( CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 1584, 729, 'no-img.jpg');
				break;
			default:
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 256, 307, 'no-img.jpg', 1);
		}
	}
	
	function banner($img = ''){
		return getImage($img, 1800, 524, 'banner-images/');
	}
	
	function user($type, $img = ''){
		switch(strtoupper($type)){
			default:
				//return getImage($img, 126, 126, 'user-images/');
				return getImage('user-images/'.$img, 228, 228);
		}
	}
	
	function logo($img = '', $type = ''){
		switch(strtoupper($type)){
			
			case 'THUMB':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 50, 50, 'no-img.jpg');
				break;
			case 'FULL':
				return showOriginalImage($img);
				break;
			default:
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 175, 38, 'no-img.jpg');
		}
		 		
	}	
	
	function fav_icon($img = ''){		
		return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 16, 16, 'no-img.jpg', 1);		
	}		
	
}