<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$arr_orders['str_paging'] = html_entity_decode($arr_orders['str_paging']);
//$arr['order'] = $arr_orders;
die(convertToJson($arr_orders));