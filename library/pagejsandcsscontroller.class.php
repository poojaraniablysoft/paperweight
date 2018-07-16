<?php
class PagejsandcssController{
	
	function js(){
		header('Content-Type: application/x-javascript');
        header('Cache-Control: public');
		header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 Day")));
		
		$arr_pth = func_get_args();
		$flname = $arr_pth[count($arr_pth)-1];
		unset($arr_pth[count($arr_pth)-1]);
		$fl = CONF_THEME_PATH . implode('/', $arr_pth) . '/page-js/' . $flname . '.js';
		if (file_exists($fl)) readfile($fl);
		exit;
	}
	 
	function css(){
		$arr_pth = func_get_args();
		$flname = $arr_pth[count($arr_pth)-1];
		unset($arr_pth[count($arr_pth)-1]);
		$fl = CONF_THEME_PATH . implode('/', $arr_pth) . '/page-css/' . $flname . '.css';
		
		header('Content-Type: text/css');
        header('Cache-Control: public');
		header("Pragma: public");
        header("Expires: " . date('r', strtotime("+30 Day")));
		
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
            ob_start("ob_gzhandler");
        }
        else {
            ob_start();
        }
		
		if (file_exists($fl)) readfile($fl);
		exit;
	}
}