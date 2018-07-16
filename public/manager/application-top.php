<?php
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' && $donotcompress !== true)){
    ob_start("ob_gzhandler");
}
else {
    ob_start();
}

session_start();
define('SYSTEM_INIT', true);
ini_set( 'display_errors', 0 );
ini_set( 'display_startup_errors', 0 );
error_reporting(E_ALL);

require_once dirname(dirname(__FILE__)) . '/includes/conf-common.php';
require_once dirname(__FILE__) . '/includes/conf.php';
require_once dirname(dirname(__FILE__)) . '/includes/utilities.php';

/*$currentCookieParams = session_get_cookie_params();

session_set_cookie_params(
$currentCookieParams["lifetime"],
CONF_WEBROOT_URL,
$_SERVER['SERVER_NAME'],
$currentCookieParams["secure"],
true
);*/

set_include_path(get_include_path() . PATH_SEPARATOR . CONF_INSTALLATION_PATH . 'library');

require_once 'includes/functions.php';
require_once dirname(dirname(__FILE__)) . '/includes/site-functions.php';
require_once dirname(dirname(__FILE__)) . '/includes/framework-functions.php';
require_once '_classes/message.cls.php';
//require_once 'ckeditor/ckeditor.php';

$db_config=array(
    'server'=>CONF_DB_SERVER,
    'user'=>CONF_DB_USER,
    'pass'=>CONF_DB_PASS,
    'db'=>CONF_DB_NAME);
$db=new Database(CONF_DB_SERVER, CONF_DB_USER, CONF_DB_PASS, CONF_DB_NAME);

/* define configuration variables */
$rs=$db->query("select * from tbl_configurations");
while($row=$db->fetch($rs)){
	define(strtoupper($row['conf_name']), $row['conf_val']);
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
					'../js/jquery-1.10.2.js',
					'js/jconfirmaction.jquery.js',
					'js/respond.min.js',
					'../facebox/facebox.js',
					'js/waypoints.min.js',
					'js/modernizr-1.7.min.js',
					'../js/jquery.tablednd_0_5.js',
					'../js/mbsmessage.js',
					'../js/calendar.js',
					'../js/calendar-setup.js',
					'../js/calendar-en.js',
					'../js/admin/jquery.jeditable.mini.js',
					'functions.js.php',
					'js/site-functions.js',
					'form-validation.js.php',
					'../tinymce/jquery.tinymce.min.js',
					//'../tinymce/jscripts/tiny_mce/jquery.tinymce.js',
					'js/function.js'
					), true);

Syspage::addCss(array(					
					'../css/ionicons.css',
					'../css/cal-css/calendar-blue.css',
					'../facebox/facebox-admin.css',
					'../css/admin/style.css',
					'css/mbsmessage.css',
					'css/mbs-styles.css',
					), true);

$tpl_for_js_css = ''; // used to include page js and page css

$system_alerts=array();

if(CONF_DEVELOPMENT_MODE) $system_alerts[]='System is in development mode.';

$post = getPostedData();

$_SESSION['WYSIWYGFileManagerRequirements'] = realpath(dirname(__FILE__) . '/includes/WYSIWYGFileManagerRequirements.php');
?>
