<?php
class Paper extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getPaperById($paper_id) {
		$db = &Syspage::getdb();
		
		if(!$paper_id=intval($paper_id)){return false;}			
		$srch = new SearchBase('tbl_paper_types');
		$srch->addMultipleFields(array('paptype_id','paptype_name','paptype_active'));
		$srch->addCondition('paptype_id','=',$paper_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}