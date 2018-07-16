<?php 
require_once dirname(__FILE__) . '/application-top.php';

unregisterGlobals();

$post = getPostedData();

callHook();
