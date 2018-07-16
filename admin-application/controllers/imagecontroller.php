<?php
class ImageController extends Controller{
    function default_action(){
		exit('Invalid request!!');
    }
	
	function option($img = '',$org=false){
		if($org==false)
			return getImage($img, 70, 70, 'option-images/');
		else
			return showOrgImage($img, 0, 0, 'option-images/');	
	}
	
	function prodtype($img = '',$org=false){
		if($org==false)
			return getImage($img, 78, 67, 'prodtype-images/');
		else
			return showOrgImage($img, 0, 0, 'prodtype-images/');
	}
	
	function product($type = 'big-thumb', $img = ''){
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage($img, 100, 100, 'product-images/', 'no-img.jpg');
				break;
			default:
				return getImage($img, 350, 250, 'product-images/', 'no-img.jpg');
		}
	}
	
	function banner($img = ''){
		return getImage($img, 600, 181, 'banner-images/');
	}
	
	function post($type = 'large', $img = ''){
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 100, 100, 'no-img.jpg');
				break;
			case 'LARGE':
				return getImage( CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 1031, 381, 'no-img.jpg');
				break;
			case 'MEDIUM':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 348, 274, 'video-img.png');
				break;
			default:
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/post-images/'.$img, 256, 307, 'no-img.jpg');
		}
	}
	
	function step($type = 'large', $img = ''){
		switch(strtoupper($type)){
			case 'THUMB':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 50, 50, 'no-img.jpg');
				break;
			case 'LARGE':
				return getImage( CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 1031, 381, 'no-img.jpg');
				break;
			case 'MEDIUM':
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 348, 274, 'no-img.png');
				break;
			default:
				return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 256, 307, 'no-img.jpg');
		}
	}
	
	function user($img = ''){
		return getImage($img, 126, 126, 'user-images/');
	}
	
	function logo($type = 'large', $img = ''){
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
		
		return getImage(CONF_INSTALLATION_PATH . 'user-uploads/'.$img, 16, 16, 'no-img.jpg');
		
	}		
}