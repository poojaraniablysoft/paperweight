<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

echo Message::getHtml();
redirectUser(generateUrl('page','error_404'));
?>

This is the default system view. It seems that view for function being accessed does not exist.