<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' && $donotcompress !== true)){
    ob_start("ob_gzhandler");
}
else {
    ob_start();
}

session_start();
define('SYSTEM_INIT', true);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once dirname(__FILE__) . '/includes/conf-common.php';
require_once dirname(__FILE__) . '/includes/conf.php';
require_once dirname(__FILE__) . '/includes/utilities.php';

/*$currentCookieParams = session_get_cookie_params();

session_set_cookie_params(
$currentCookieParams["lifetime"],
CONF_WEBROOT_URL,
$_SERVER['SERVER_NAME'],
$currentCookieParams["secure"],
true
);*/

set_include_path(get_include_path() . PATH_SEPARATOR . CONF_INSTALLATION_PATH . 'library');

//die(get_include_path());

require_once 'includes/functions.php';
require_once dirname(__FILE__) . '/includes/site-functions.php';
require_once dirname(__FILE__) . '/includes/framework-functions.php';

$db_config=array(
    'server'=>CONF_DB_SERVER,
    'user'=>CONF_DB_USER,
    'pass'=>CONF_DB_PASS,
    'db'=>CONF_DB_NAME);
$db=new Database(CONF_DB_SERVER, CONF_DB_USER, CONF_DB_PASS, CONF_DB_NAME);

/* define configuration variables */
$rs=$db->query("select * from tbl_configurations");
while($row=$db->fetch($rs)){
	if (strtolower($row['conf_name']) == 'conf_timezone') {
		$arr_user = array();
		
		if (isset($_SESSION['logged_user'])) {
			$sql = $db->query('SELECT user_timezone FROM tbl_users WHERE user_id = ' . $_SESSION['logged_user']['user_id']);
			$arr_user = $db->fetch($sql);
		}
		
		$user_timezone = !empty($arr_user['user_timezone']) ? $arr_user['user_timezone'] : $row['conf_val'];
		define('CONF_TIMEZONE', $user_timezone);
	}
	else {
		define(strtoupper($row['conf_name']), $row['conf_val']);
	}
}
/* end configuration variables */
if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')  && (CONF_USE_SSL==1)) {
	$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	redirectUser($redirect);
}
$protocol=CONF_USE_SSL==1?"https":"http";
define('CONF_SERVER_PATH', $protocol."://".$_SERVER['SERVER_NAME'].CONF_WEBROOT_URL);
$arr_page_js=array();
$arr_page_css=array();

$arr_page_js_common = array();
$arr_page_css_common = array();

Syspage::addJs(array(
					'js/jquery-1.10.2.js',
					'facebox/facebox.js',
					'js/mbsmessage.js',
					'js/respond.min.js',
					'js/calendar.js',
					'js/calendar-en.js',
					'js/calendar-setup.js',
					'form-validation.js.php',
					'functions.js.php',
					'js/site-functions.js',
					'tinymce/jquery.tinymce.min.js',
					'tinymce-lnk/jscripts/tiny_mce/jquery.tinymce.js',
					'js/jquery.bxslider.js',
					'js/jquery.bxslider.js',
					'js/jstz-1.0.4.min.js',
					'js/jquery.expander.min.js'), true);
 
Syspage::addCss(array('facebox/facebox.css','css/inner.css', 'css/cal-css/calendar-blue.css', 'css/mbs-styles.css', 'css/mbsmessage.css', 'css/ionicons.css', 'css/reset.css', 'css/style.css'), true);

$tpl_for_js_css = ''; // used to include page js and page css

$system_alerts=array();

if(CONF_DEVELOPMENT_MODE) $system_alerts[]='System is in development mode.';

$post = getPostedData();
$_SESSION['WYSIWYGFileManagerRequirements'] = realpath(dirname(__FILE__) . '/includes/WYSIWYGFileManagerRequirements.php');