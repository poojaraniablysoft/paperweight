<?php
class CronController extends LegitController {
    function default_action(){
		$bkDir = dir(CONF_INSTALLATION_PATH.'restore/');
		
 		 while (($file = $bkDir->read()) !== false){
				
			if(!($file == "." || $file == ".." ||  $file == ".htaccess")){ 
				$source = $bkDir->path . $file;
				 	
				$cmd ="mysql -u ".CONF_DB_USER ." -p".CONF_DB_PASS ." ". CONF_DB_NAME ." < " . $source;				
				
				exec($cmd.' 2>&1', $output);				
			} 
		}    
    }
}