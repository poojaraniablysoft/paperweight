<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$arr_transactions['str_paging'] = html_entity_decode($arr_transactions['str_paging']);

die(convertToJson($arr_transactions));