<?php
class Language extends Model {
	protected $error;
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getLanguageById($lang_id) {
		$db = &Syspage::getdb();
		
		if(!$lang_id=intval($lang_id)){return false;}			
		$srch = new SearchBase('tbl_languages');
		$srch->addMultipleFields(array('lang_id','lang_name','lang_active'));
		$srch->addCondition('lang_id','=',$lang_id);
		$rs = $srch->getResultSet();
		$res = $db->fetch($rs);
		return $res;
	}
}