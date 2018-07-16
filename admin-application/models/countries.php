<?php
class Countries extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getCountryById($country_id) {
		$db = &Syspage::getdb();
		if(!$country_id=intval($country_id)){return false;}
		$srch = new SearchBase('tbl_countries');
		$srch->addMultipleFields(array('country_id','country_name','country_active'));
		$srch->addCondition('country_id','=',$country_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}