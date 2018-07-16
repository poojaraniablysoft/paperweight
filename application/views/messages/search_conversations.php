<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

$arr_search['str_paging'] = html_entity_decode($arr_search['str_paging']);
die(convertToJson(array('status'=>1, 'data'=>$arr_search)));