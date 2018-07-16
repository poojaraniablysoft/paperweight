<?php
class Disciplines extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getDisciplinesById($discipline_id) {
		$db = &Syspage::getdb();
		
		if(!$discipline_id=intval($discipline_id)){return false;}			
		$srch = new SearchBase('tbl_disciplines');
		$srch->addMultipleFields(array('discipline_id','discipline_name','discipline_active'));
		$srch->addCondition('discipline_id','=',$discipline_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}