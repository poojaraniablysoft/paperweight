<?php
$requested_controller;
$requested_action;
$requested_query;

function unregisterGlobals() {
	if (ini_get('register_globals')) {
		$array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
		foreach ($array as $value) {
			foreach ($GLOBALS[$value] as $key => $var) {
				if ($var === $GLOBALS[$key]) {
					unset($GLOBALS[$key]);
				}
			}
		}
	}
}

function callHook_bk() {
	$get = getQueryStringData();
	
	if (!isset($get['url'])) $get['url']='';
	$url = $get['url'];
	
	$urlArray = array();
	$urlArray = explode("/",$url);
	
	$controller = $urlArray[0];
	array_shift($urlArray);
	$action = $urlArray[0];
	array_shift($urlArray);
	$queryString = $urlArray;
	
	if ($controller == '') $controller = 'home';
	if ($action == '') $action = 'default_action';

	$controllerName = $controller;
	$controller = ucwords($controller);
	$model = $controller;
	$controller .= 'Controller';
	
	if (!file_exists(CONF_APPLICATION_PATH . 'controllers/' . strtolower($controller) . '.php') && !file_exists(CONF_INSTALLATION_PATH . 'library/' . strtolower($controller) . '.class.php')){
		/* Make sure that a class from application folder is called and not any php system class for hacking. */
		
		//@todo ehance this area to send user to some 404 page.
		redirectUser(generateUrl('page','error_404'));
		die('The page you are looking for could not be found.');
	}
	
	if (!class_exists($controller)){
		die('Page you are looking for could not be found.');
	}
	
	$dispatch = new $controller($model,$controllerName,$action);
	
	if ((int)method_exists($controller, $action)) {
		global $requested_controller;
		global $requested_action;
		global $requested_query;
		
		$requested_controller	= $controller;
		$requested_action		= $action;
		$requested_query		= $queryString;
		
		call_user_func_array(array($dispatch,$action),$queryString);
	} else {
		/* Error Generation Code Here */
		dieWithError('Invalid Request ' . $action);
	}
}

function callHook() {
	global $glb_action;
	$get = getQueryStringData();
	/* $subdomain = array_shift(explode('.', $_SERVER['HTTP_HOST']));
	$subdomain = strtolower(trim($subdomain)) ; */
	
	if (!isset($get['url'])) $get['url']='';
	$url = $get['url'];
	
	/* Custom domain url for dealers */
	
	$urlArray = array();
	$urlArray = explode("/",$url);
	
	if (in_array(strtolower($urlArray[0]), array('cms'))) {
		
		if (!array_key_exists(4,$urlArray)) $urlArray[4] = '';
		$action = in_array($urlArray[1], array('page','contact')) ? $urlArray[1] : 'page';
		
		$cms_type = $urlArray[0];
		$urlArray[0] = 'cms';
		$controller = $urlArray[0];
		array_shift($urlArray);
		
		array_pop($urlArray);
		
		array_push($urlArray,$cms_type);
	}
	else {	
		$controller = $urlArray[0];
		array_shift($urlArray);
		
		$action = $urlArray[0];
		array_shift($urlArray);
	}
	
	$queryString = $urlArray;
	
	if ($controller == '') $controller = 'home';
	if ($action == '') $action = 'default_action';
	
	$glb_action = $action;	
	
	$controllerName = $controller;
	$controller = ucwords($controller);
	$model = $controller;
	$controller .= 'Controller';
	//echo $controller . ' --- ' . $model;exit;
	if (!file_exists(CONF_APPLICATION_PATH . 'controllers/' . strtolower($controller) . '.php') && !file_exists(CONF_INSTALLATION_PATH . 'library/' . strtolower($controller) . '.class.php')){
		/* Make sure that a class from application folder is called and not any php system class for hacking. */
		
		//@todo ehance this area to send user to some 404 page.
		
		//die('The page you are looking for could not be found.');
		redirectUser(generateUrl('page','error_404'));
	}
	
	if (!class_exists($controller)){
		redirectUser(generateUrl('page','error_404'));
		//die('Page you are looking for could not be found.');
	}
	
	/** generate query string **/
	$params = getUrlParameters($model,$action,$queryString);
	if ($params === false) die('The page you are looking for could not be found.');
	//echo '<pre>' . print_r($params) . '</pre>';
	$dispatch = new $controller($model,$controllerName,$action);
	
	//echo "<pre>".print_r($dispatch,true)."</pre>";exit;
	if ((int)method_exists($controller, $action)) {
		global $requested_controller;
		global $requested_action;
		global $requested_query;
		
		$requested_controller	= $controller;
		$requested_action		= $action;
		$requested_query		= $queryString;
		
		call_user_func_array(array($dispatch,$action),$params);
	} else {
		/* Error Generation Code Here */
		dieWithError('Invalid Request ' . $action);
	}
}

function getUrlParameters($model,$action,$query_string) {
	$params = array();
	
	$model	= strtolower($model);
	$action	= strtolower($action);
	
	if (!CONF_URL_REWRITING_ENABLED) return $query_string;
	
	switch($model) {
		case 'page':
			switch($action) {
				
				case 'cms':
					$params = $query_string[3] == 'cms' ? array(0,$query_string[2]) : array($query_string[2],0);
					break;
				case 'reviews':
					$params = $query_string[3] == 'cms' ? array('variant_id', 0, 'model_id', $query_string[2]) : array('variant_id', $query_string[2], 'model_id', 0);
					break;
				default:
					$params = $query_string;
			}
			
			break;
		case 'carlisting':
			switch($action) {
				case 'overview':
				case 'features':
					$params = array($query_string[2]);
					break;
				default:
					$params = $query_string;
			}
			
			break;
		case 'search':
			switch($action) {
				case 'research':
					if (empty($query_string) || strtolower($query_string[0]) == 'home') {
						$params = $query_string;
					}
					else {
						foreach($query_string as $key=>$val)
							if (preg_match('/[^0-9]/', $val)) unset($query_string[$key]);
							
						$query_string = array_values($query_string);
						
						$params = array('mkId', $query_string[1], 'modelid', $query_string[2], 'carlist_year', $query_string[0]);
					}
					break;
				default:
					$params = $query_string;
			}
			
			break;
		default:
			$params = $query_string;
	}
	
	return $params;
}

function __autoload($clname){
    switch ($clname){
        case 'Applicationconstants':
            require_once CONF_INSTALLATION_PATH . 'public/includes/applicationconstants.php';
            break;
            
        case 'Message':
            require_once '_classes/message.cls.php';
            break;
             
        case 'Database':
            require_once '_classes/db.inc.php';
            break;
        case 'FormField':
            require_once '_classes/form-field.cls.php';
            break;
        case 'SearchBase':
            require_once '_classes/search-base.cls.php';
            break;

        case 'SearchCondition':
            require_once '_classes/search-condition.cls.php';
            break;

        case 'Form':
            require_once '_classes/form.cls.php';
            break;

        case 'FormFieldRequirement':
            require_once '_classes/form-field-requirement.cls.php';
            break;
            	
        case 'TableRecord':
            require_once '_classes/table-record.cls.php';
            break;
            	
        case 'Record':
            require_once '_classes/record-base.cls.php';
            break;
            	
        case 'imageResize':
        case 'ImageResize':
            require_once '_classes/image-resize.cls.php';
            break;
        case 'HtmlElement':
            require_once '_classes/html-element.cls.php';
            break;
            	
            /*for framework*/
        	
        default:
            if (file_exists(CONF_INSTALLATION_PATH . 'library/' . strtolower($clname) . '.class.php')){
            	require_once CONF_INSTALLATION_PATH . 'library/' . strtolower($clname) . '.class.php';
			} else if (file_exists(CONF_APPLICATION_PATH . 'controllers/' . strtolower($clname) . '.php')){
				require_once CONF_APPLICATION_PATH . 'controllers/' . strtolower($clname) . '.php';
			} else if (file_exists(CONF_APPLICATION_PATH . 'models/' . strtolower($clname) . '.php')){
				require_once CONF_APPLICATION_PATH . 'models/' . strtolower($clname) . '.php';
			} else {
            /* if current application path is not the application folder at installtion path
             * let us try to look into application at root if that exists
            *  */
            $root_application_path = CONF_INSTALLATION_PATH . 'application/';
            if ($root_application_path != CONF_APPLICATION_PATH){
                if (file_exists($root_application_path . 'models/' . strtolower($clname) . '.php')){
                    require_once $root_application_path . 'models/' . strtolower($clname) . '.php';
                }
            }
        }
        break;
        	
    }
}