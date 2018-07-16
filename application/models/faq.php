<?php
class Faq extends Model{
    private $faq_id;
    
    function __construct(){
        parent::__construct();
        $this->faq_id = 0;
    }
    
    function setup($data){
		global $db;
		$success = false;
		
        $faq_id = intval($data['faq_id']);
		
        if (!($faq_id > 0)) $faq_id = 0;
        if (isset($data['faq_id'])) unset($data['faq_id']);
		
		
		$record = new TableRecord('tbl_faq');
        $record->assignValues($data);

        if ($faq_id > 0)
            $success = $record->update(array('smt' => 'faq_id = ?', 'vals' => array($faq_id)));
        else {
            $data['faq_delete'] = 0;
            $record->assignValues($data);
            $success = $record->addNew();
        }
		
        if ($success) {
			$success = $this->faq_id = ($faq_id > 0) ? $faq_id : $record->getId();
            if ($faq_id > 0) {
                Message::addMessage(ucwords($data['faq_title']) . ' ' . Utilities::getLabel( 'L_updated_successfully' ) );
            } else {
                Message::addMessage(ucwords($data['faq_title']) . ' ' . Utilities::getLabel( 'L_added_successfully' ) );
            }
        } else {
            Message::addErrorMessage($record->getError());
        }
		
		return $success;
    }

	function getData($faq_id){
      global $db;	
		$srch = new langSearchBase('tbl_faq','faqs');
		$srch->joinLangTable('tbl_faq', 'faqs', 'faq', '', 'f');
		$srch->addCondition('faq_id','=',$faq_id);
		
		$rs = $srch->getResultSet();		
		$record = $db->fetch($rs);
		
		$lang_record = Language::getLangFields($faq_id,"faqlang_faq_id","faqlang_lang_id",array("faq_title","faq_description"),"tbl_faq_lang");
	
		$arr = array_merge($record,$lang_record);
		
		return $arr;
	
    }
	
	
	function deleteFaq($faq_id){
		if(intval($faq_id) <= 0) return false;
		
		$srch = Faq::search();
		$srch->addCondition('faq_id', '=', $faq_id);
		$rs = $srch->getResultSet();
		$db = &Syspage::getDb();
		$data = $db->fetch($rs);
		if($data){
			$data['faq_delete'] = 1;
			$record = new TableRecord('tbl_faq');
			$record->assignValues($data);
			$success = $record->update(array('smt' => 'faq_id = ?', 'vals' => array($faq_id)));
			if ($success) {
				Message::addMessage( ucwords($data['faq_title']) . ' ' . Utilities::getLabel( 'L_deleted_successfully' ) );
			} else {
				Message::addErrorMessage($record->getError());
				return false;
			}
		}else{
			Message::addErrorMessage( Utilities::getLabel( 'L_Faq_not_exists' ) );
			return false;
		}
		
		return true;
	}
	
	static function search($flds=array()) {
		$srch = new SearchBase('tbl_faq','faqs');
		if(count($flds) > 0) $srch->addMultipleFields($flds);		
		$srch->addOrder('faq_display_order');		
		return $srch;
	}
	
	static function getFaqAssoc(){		
        global $db;
		
        $srch = Faqcategory::search(array("faqcat_id","faqcat_name"));   
	
        $faqCat = $srch->getResultSet();
		
        while($faqcat_row = $db->fetch($faqCat)){
		
			$srch2 = self::search();	
			$srch2->addCondition('faq_type_id', '=', intval($faqcat_row['faqcat_id']));
			$faqs = $srch2->getResultSet();
			
			if($db->total_records($faqs)==0) continue; /* Nothing found */
			
				$faq_arr[]=array('faqcat_title'=>$faqcat_row['faqcat_name'],'options'=>$db->fetch_all($faqs));	
				
		}return $faq_arr;
	}	
	

	function reOrder($data){
		if(count($data) <= 0) return false;
		
		$record = new TableRecord('tbl_faq');				
		
		foreach($data as $key => $val){
			$record->assignValues(array('faq_display_order'=>$val));
			$success=$record->update(array('smt' => 'faq_id = ?', 'vals' => array($key)));
		}
		
		if ($success) {
			Message::addMessage( Utilities::getLabel( 'L_Re-ordered_successfully' ) );
		} else {
			Message::addErrorMessage($record->getError());
			return false;
		}
		
		return true;
	}	
	
	
}