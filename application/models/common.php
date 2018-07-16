<?php
class Common extends Model {
	private $wallet;	
	public function __construct() {
		parent::__construct();
		$this->wallet = new Wallet();
	}
	public function getCountries() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_countries');
		$srch->addCondition('country_active','=',1);
		$srch->addOrder('country_name');
		$srch->addMultipleFields(array('country_id', 'country_name'));
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}
	public function getLanguages() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_languages');
		$srch->addCondition('lang_active','=',1);
		$srch->addOrder('lang_name');
		$srch->addMultipleFields(array('lang_id', 'lang_name'));
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}	
	public function getAcademicDegrees() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_academic_degrees');
		$srch->addCondition('deg_active','=',1);
		$srch->addOrder('deg_display_order');		$srch->addMultipleFields(array('deg_id', 'deg_name'));
		$rs = $srch->getResultSet();		return $db->fetch_all_assoc($rs);
	}
	public function getWorkFields() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_experience_fields');		$srch->addOrder('exfld_name');
		$srch->addMultipleFields(array('exfld_id', 'exfld_name'));
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}	
	public static function getServices() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_service_fields');
		$srch->addOrder('service_name');
		$srch->addMultipleFields(array('service_id', 'service_name'));
		$srch->addCondition('service_active','=',1);
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}
	public static function getCitationStyles() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_citation_styles');
		$srch->addCondition('citstyle_active', '=', 1);
		$srch->addOrder('citstyle_name');
		$srch->addMultipleFields(array('citstyle_id', 'citstyle_name'));
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}
	public static function getPaperTypes() { 
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_paper_types');
		$srch->addCondition('paptype_active', '=', 1);
		$srch->addOrder('paptype_name');		$srch->addMultipleFields(array('paptype_id', 'paptype_name'));
		$rs = $srch->getResultSet();
		return $db->fetch_all_assoc($rs);
	}

	public function getPaperTypesByurl($url) {
		
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_paper_types');
		$srch->addCondition('paptype_active', '=', 1);		
		$srch->addHaving('replaced_name', '=', $url);
		
		$srch->addMultipleFields(array('paptype_id','LOWER( replace( REPLACE(  REPLACE( REPLACE( `paptype_name` , ",", "" ) , "/", "" )  , "&", "amp" ) , " ", "-" ) ) AS replaced_name'));	
		
		$rs = $srch->getResultSet();
		return $db->fetch($rs);
	}	
	public static function getDisciplines() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_disciplines');
		$srch->addCondition('discipline_active', '=', 1);
		$srch->addOrder('discipline_name');
		$srch->addMultipleFields(array('discipline_id', 'discipline_name'));
		$rs = $srch->getResultSet();		return $db->fetch_all_assoc($rs);
	}

	public function getDisciplinesByurl($url) {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_disciplines');
		$srch->addCondition('discipline_active', '=', 1);		
		$srch->addHaving('replaced_name', '=', $url);		
		$srch->addMultipleFields(array('discipline_id','LOWER( replace( REPLACE( REPLACE(REPLACE( `discipline_name` , ",", "" ), "/", "" ), "&", "amp" ) , " ", "-" ) ) AS replaced_name'));		
		$rs = $srch->getResultSet();
		return $db->fetch($rs);
	}	
	public function getSubscriptions() {		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_newsletters');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch_all_assoc($rs)) {
			return false;
		}
		return $row;
	}
	public function getNumOrderInBid() {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_tasks');
		$srch->addCondition('task_status','=',1);
		$srch->addCondition('task_user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addFld('count(task_id) as num');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			$this->error = $srch->getError();
			return false;
		}
		return $row;
	}	
	public function getNumOrderInProcess() {		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_tasks');
		$srch->addCondition('task_status','=',2);
		$srch->addCondition('task_user_id','=',User::getLoggedUserAttribute('user_id'));
		$srch->addFld('count(task_id) as num');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			$this->error = $srch->getError();
			return false;
		}
		return $row;
	}	
	public function getNumOrderCompleted($user_id) {
		$db = &Syspage::getdb();
		$user_id = intval($user_id);
		if($user_id < 0) { $user_id = User::getLoggedUserAttribute('user_id');}	
		$srch = new SearchBase('tbl_tasks');
		$srch->addCondition('task_status','=',3);
		$srch->addCondition('task_user_id','=',$user_id,'OR');
		$srch->addCondition('task_writer_id','=',$user_id);
		$srch->addFld('count(task_id) as num');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			$this->error = $srch->getError();
			return false;
		}
		return $row;
	}	
	public function get_service_type_id($service_name) {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_service_fields');
		$srch->addOrder('service_name');
		$srch->addMultipleFields(array('service_id', 'service_name'));
		$srch->addCondition('service_active','=',1);
		$srch->addCondition('service_name','=',$service_name);
		$rs = $srch->getResultSet();
		return $db->fetch($rs);
	}	
	public function getCommonTopData($controller,$action) {
		return array('wallet_balance'=>$this->wallet->getUserWalletBalance());
	}
	static function is_first_order_done() {
		$db = &Syspage::getdb();
		$sql = $db->query('SELECT count(*) as num from tbl_tasks where task_user_id='.User::getLoggedUserAttribute('user_id'));
		$count = $db->fetch($sql);
		if($count['num']==1) {
			$srch = new SearchBase('tbl_tasks');
			$srch->addCondition('task_user_id','=',User::getLoggedUserAttribute('user_id'));
			$srch->addFld('task_topic');
			$rs = $srch->getResultSet();
			if(!$row = $db->fetch($rs)) {
				$this->error = $db->getError();
				return false;
			}else {
				if($row['task_topic']=='') {
					return false;
				}
			}
		}
		return true;
	}	
	/* static function is_essay_submit($user_id) {	
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_user_profile');
		$srch->addCondition('userprofile_user_id','=',$user_id);
		$srch->addCondition('user_sample_essay','=','1');
		$rs = $srch->getResultSet();
		if(!$row = $db->fetch($rs)) {
			//$this->error = $db->getError();
			return false;
		}
		
		return true;
	} */
	static function is_essay_submit($user_id) {
		$db = &Syspage::getdb();
		$user_id = intval($user_id);
		$srch = new SearchBase('tbl_user_profile');
		$srch->addCondition('userprofile_user_id', '=', $user_id);
		$rs = $srch->getResultSet();
		$row = $db->fetch($rs);
		return intval($row['user_sample_essay']);
	}
}