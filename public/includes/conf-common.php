<?php
/**
 * 
 * General configurations
 */

define('CONF_DEVELOPMENT_MODE', true);
define('CONF_LIB_HALDLE_ERROR_IN_PRODUCTION', true);

define ('CONF_URL_REWRITING_ENABLED', true);

if( strpos( $_SERVER['SERVER_NAME'], 'moneymahey.4demo.biz' ) !== false ) { 
    define('CONF_WEBROOT_URL', '/');

    define('CONF_DB_SERVER', 'localhost');
    define('CONF_DB_USER', 'moneymahey');
    define('CONF_DB_PASS', 'moneymahey');
    define('CONF_DB_NAME', 'moneymahey_paperweight');
}if( strpos( $_SERVER['SERVER_NAME'], 'poojarani.4demo.biz' ) !== false ) { 
    define('CONF_WEBROOT_URL', '/');

    define('CONF_DB_SERVER', 'localhost');
    define('CONF_DB_USER', 'poojarani');
    define('CONF_DB_PASS', 'poojarani');
    define('CONF_DB_NAME', 'poojarani_paperweight');
} elseif( strpos( $_SERVER['SERVER_NAME'], 'staging.4demo.biz' ) !== false ) { 
    define('CONF_WEBROOT_URL', '/');

    define('CONF_DB_SERVER', 'localhost');
    define('CONF_DB_USER', 'staging');
    define('CONF_DB_PASS', 'staging');
    define('CONF_DB_NAME', 'staging_paperweight');
} else {
	
    define('CONF_WEBROOT_URL', '/');

    define('CONF_DB_SERVER', '');
    define('CONF_DB_USER', '');
    define('CONF_DB_PASS', '');
    define('CONF_DB_NAME', '');
}

define('PASSWORD_SALT', 'ewoiruqojfklajreajflajoer');
/* define('CONF_HTML_EDITOR', 'tinymce'); */
define('CONF_CONTRIBUTION_FILE_UPLOAD_SIZE', 10);

//$mbs_tinymce_settings = array('script_url'=>CONF_WEBROOT_URL . 'tinymce-lnk/jscripts/tiny_mce/tiny_mce.js');
/* $mbs_tinymce_settings = array('script_url'=>CONF_WEBROOT_URL . 'tinymce/tinymce.min.js', 'menubar'=>'false', 'height'=>'150', 'toolbar'=>'undo redo | bold italic | bullist numlist ', 'resize'=>'false', 'remove_script_host'=>'true', 'browser_spellcheck'=>'true', 'plugins'=>'wordcount'); */

define('CONF_HTML_EDITOR', 'innova');
define('CONF_CKEDITOR_PATH', CONF_WEBROOT_URL.'innova-lnk');

define('CONF_EMAIL_SEND_VIA_PHP', 0);
define('CONF_EMAIL_SEND_VIA_SMTP', 1);

/* Labels 0054 [ */
/* define( 'CONF_DEFAULT_LANG_ID', 1 );
define( 'CONF_ALL_LANGUAGES', array( CONF_DEFAULT_LANG_ID => 'English' ) ); */
/* ] */

$innova_settings = array(
	'width'=>'"100%"',
	'height'=>'150',
	'groups'=>'[
			["group1", "", ["Bold", "Italic" , "Underline", "FontDialog", "ForeColor", "TextDialog", "RemoveFormat", "Paragraph"]],
			["group2", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]],
			["group3", "", ["Undo", "Redo"]]
			]','css' => '"'.CONF_WEBROOT_URL.'css/editor.css"');


$service_types				= array(1=>'Writing from scratch', 2=>'Rewriting', 3=>'Editing');
$order_status				= array(0=>'Pending approval', 1=>'Active', 2=>'In Progress', 3=>'Finished', 4=>'Cancelled');
$bid_status					= array(0=>'Pending', 1=>'Awarded', 2=>'Rejected', 3=>'Cancelled');
$invitation_status			= array(0=>'Pending', 1=>'Accepted', 2=>'Declined');
$milestone_status			= array(0=>'Pending', 1=>'Approved', 2=>'Cancelled');
$withdrawal_request_status	= array(0=>'Pending', 1=>'Approved', 2=>'Declined');
$binary_status				= array(0=>'No', 1=>'Yes');
