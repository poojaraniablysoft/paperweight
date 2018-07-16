<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
die(convertToJson(array('status'=>1, 'msg'=>'', 'data'=>html_entity_decode($data))));