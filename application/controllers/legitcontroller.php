<?php
class LegitController extends Controller{
	private $user;

	function __construct($model, $controller, $action){
        parent::__construct($model, $controller, $action);
		
		$this->user = new User();
		
		$this->user->authenticatePersistentLogin();
		
		$this->setLegitControllerCommonData();
		//echo $controller;exit;
		if($controller != 'home') {
			#Syspage::addCss(array('css/inner.css','css/skeleton.css'));
			Syspage::addCss(array('css/skeleton.css'));
		}
		if (!User::isUserLogged() || (User::isWriter() == true && (!User::writerCanView() || (!User::writerCanBid() && $action=='signup_message')))) {
		}else{
			if(User::isUserLogged()){			
				if(!in_array($controller,array('sample','test','home','cms','default_action','blog')) && !in_array($action,array('top_writers','writers'))) {
					Syspage::addCss(array('css/dashboard.css','css/responsive-dashboard.css'));
				}
			}
		}
		Syspage::addCss(array('css/tablet.css','css/jquery.bxslider.css'));
    }
    
    function setLegitControllerCommonData($controller='',$action='') {
    	if (!User::isUserLogged()) return;
		
    	$common = new Common();
		
		$this->set('page_data', $common->getCommonTopData($controller,$action));
		$this->set('controller', $controller);
		$this->set('action', $action);
    }
}