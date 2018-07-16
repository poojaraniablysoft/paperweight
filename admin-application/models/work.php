<?php
class Work extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getWorkById($work_id) {
		$db = &Syspage::getdb();
		
		if(!$work_id=intval($work_id)){return false;}			
		$srch = new SearchBase('tbl_experience_fields');
		$srch->addMultipleFields(array('exfld_id','exfld_name','exfld_active'));
		$srch->addCondition('exfld_id','=',$work_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}