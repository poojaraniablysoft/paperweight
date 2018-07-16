<?php
if(session_id() === ""){
	ob_start();
	session_start();
}

function checkValidUserForFileManager(){
	$admin = /* 1 ||  */(isset($_SESSION['logged_admin']['admin_id']) && is_numeric($_SESSION['logged_admin']['admin_id']) && intval($_SESSION['logged_admin']['admin_id']) > 0 && strlen(trim($_SESSION['logged_admin']['admin_username'])) >= 4);
	
	if($admin){
		global $is_admin_for_file_manager;
		$is_admin_for_file_manager = 1;
	}else{
		/* exit(0); */
	}
}
checkValidUserForFileManager();

if($is_admin_for_file_manager){
	$path_for_images = "/images/cms"; /* Relative to URI Root - This is for Innova */
	//$path_for_images = "/ed-images/fck-images/"; /* Relative to URI Root - This is for FCKEditor keep double parent folders for FCK */
	
	$absolute_path_for_images = '/var/www/dv/a/a/4demo/paperweight/public/images/cms/'; /*Absolute physical path for the relative path defined above - This is for FCKEditor */
}