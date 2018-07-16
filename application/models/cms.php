<?php
class Cms extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getError(){
        return $this->error;
    }
	
	function getRecords($page_id) {
		$db = &Syspage::getdb();
		
		$srch = new SearchBase('tbl_cms');
		
		$srch->addCondition('cmspage_id', '=', $page_id);
		
		$rs = $srch->getResultSet();
		
		return $db->fetch($rs);
	}
}