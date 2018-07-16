<?php
/**
 * General Front End Configurations
 */

define('CONF_INSTALLATION_PATH', $_SERVER['DOCUMENT_ROOT'] . CONF_WEBROOT_URL);

define('CONF_APPLICATION_PATH', CONF_INSTALLATION_PATH . 'admin-application/');
if (CONF_URL_REWRITING_ENABLED){
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL . 'manager/');
}
else {
    define('CONF_USER_ROOT_URL', CONF_WEBROOT_URL . 'public/manager/');
}

define('CONF_THEME_PATH', CONF_APPLICATION_PATH . 'views/');

define('CONF_DATE_FIELD_TRIGGER_IMG', CONF_WEBROOT_URL . 'images/iocn_clender.gif');

/* define('CONF_HTML_EDITOR', 'tinymce'); */
$innova_settings = array(
				'width'=>'"100%"',
				'height'=>'150',
				'groups'=>'[
                        ["group1", "", ["Bold", "Italic" , "Underline", "FontDialog", "ForeColor", "TextDialog", "RemoveFormat", "Paragraph"]],
                        ["group2", "", ["Undo", "Redo", "FullScreen", "SourceDialog"]],
                        ["group3", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]],
                        ["group4", "", ["LinkDialog", "ImageDialog", "YoutubeDialog", "TableDialog", "Emoticons"]],
                        ["group5", "", ["InternalLink"]],
                        ]', 'fileBrowser'=> '"'.CONF_WEBROOT_URL.'innova-lnk/assetmanager/asset.php"');
// echo $_SERVER['SERVER_NAME'];die;

//$mbs_tinymce_settings = array('script_url'=>CONF_WEBROOT_URL . 'tinymce/tinymce.min.js', 'selector'=>'textarea', 'height'=>'350', 'toolbar'=>'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image', 'plugins'=>array('advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table contextmenu paste'),'relative_urls'=> false, 'document_base_url' => 'http://dummy');
/* $mbs_tinymce_settings = array('script_url'=>CONF_WEBROOT_URL . 'tinymce/tinymce.min.js', 'selector'=>'textarea', 'height'=>'350', 'toolbar'=>'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image', 'plugins'=>array('advlist autolink lists link image charmap print preview anchor', 'searchreplace visualblocks code fullscreen', 'insertdatetime media table contextmenu paste')); */
/* $mbs_tinymce_settings = array('script_url'=>CONF_WEBROOT_URL . 'tinymce-lnk/jscripts/tiny_mce/tiny_mce.js', 'selector'=>'textarea', 'height'=>'350', 'toolbar'=>'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image','relative_urls'=> false, 'document_base_url' => 'http://dummy'); */
?>