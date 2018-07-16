<?php
class Meta extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getMetaById($page_id) {
		$db = &Syspage::getdb();
		
		if(!$page_id=intval($page_id)){return false;}			
		$srch = new SearchBase('tbl_meta_tags');
		$srch->addMultipleFields(array('page_title','meta_title','meta_description','meta_keywords'));
		$srch->addCondition('page_id','=',$page_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
	
	public function getMetas() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_meta_tags');
		$srch->addMultipleFields(array('page_id','page_title','meta_title'));		
		return $srch;
	}	
	
}