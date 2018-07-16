<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
die(convertToJson(array('status'=>1,'user_data'=>$user_info)));
?>