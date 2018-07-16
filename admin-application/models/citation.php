<?php
class Citation extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getCitationById($citstyle_id) {
		$db = &Syspage::getdb();
		
		if(!$citstyle_id=intval($citstyle_id)){return false;}			
		$srch = new SearchBase('tbl_citation_styles');
		$srch->addMultipleFields(array('citstyle_id','citstyle_name','citstyle_active'));
		$srch->addCondition('citstyle_id','=',$citstyle_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}