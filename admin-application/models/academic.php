<?php
class Academic extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getAcademicById($deg_id) {
		$db = &Syspage::getdb();
		
		if(!$deg_id=intval($deg_id)){return false;}			
		$srch = new SearchBase('tbl_academic_degrees');
		$srch->addMultipleFields(array('deg_id','deg_name','deg_active'));
		$srch->addCondition('deg_id','=',$deg_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}