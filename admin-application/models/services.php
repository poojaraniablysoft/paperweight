<?php
class Services extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getServicesById($service_id) {
		$db = &Syspage::getdb();
		
		if(!$service_id=intval($service_id)){return false;}			
		$srch = new SearchBase('tbl_service_fields');
		$srch->addMultipleFields(array('service_id','service_name','service_active'));
		$srch->addCondition('service_id','=',$service_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}