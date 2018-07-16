<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$arr_feedback['str_paging'] = html_entity_decode($arr_feedback['str_paging']);
die(convertToJson(array('status'=>1, 'data'=>$arr_feedback)));