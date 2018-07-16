<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$arr_reviews['str_paging'] = html_entity_decode($arr_reviews['str_paging']);
die(convertToJson(array('status'=>1, 'data'=>$arr_reviews)));