<?php 
$valid_urls = array(
	'transactions/validate_transaction'
);

if (!in_array($_GET['url'], $valid_urls)) die('Unauthorized Access.');


require_once 'index.php';