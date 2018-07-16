<?php
/**
 * General Front End Configurations
 */

define('CONF_INSTALLATION_PATH', $_SERVER['DOCUMENT_ROOT'] . CONF_WEBROOT_URL);

define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . 'application/');
if (CONF_URL_REWRITING_ENABLED){
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL);
}
else {
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL . 'public/');
}

define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');

define('CONF_DATE_FIELD_TRIGGER_IMG', CONF_WEBROOT_URL . 'images/iocn_clender.gif');
define('CONF_MESSAGE_ERROR_HEADING', '');



//define('CONF_FCKEDITOR_PATH', CONF_WEBROOT_URL . 'fckeditor/');

?>