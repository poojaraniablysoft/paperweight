<?php
class Howitworks extends Model{
    private $step_id;
    
    function __construct(){
        parent::__construct();
        $this->step_id = 0;
    }
    
    function setup($data){
		global $db;
		$success = false;
		
        $step_id = intval($data['step_id']);
		
        if (!($step_id > 0)) $step_id = 0;
        if (isset($data['step_id'])) unset($data['step_id']);
		
		
		$record = new TableRecord('tbl_howitworks');
        $record->assignValues($data);

        if ($step_id > 0)
            $success = $record->update(array('smt' => 'step_id = ?', 'vals' => array($step_id)));
        else {
            $data['step_delete'] = 0;
            $record->assignValues($data);
            $success = $record->addNew();
        }
		
        if ($success) {
			$success = $this->step_id = ($step_id > 0) ? $step_id : $record->getId();
            if ($step_id > 0) {
                Message::addMessage( ucwords( $data['step_title'] ) . ' ' . Utilities::getLabel( 'L_updated_successfully' ) );
            } else {
                Message::addMessage( ucwords( $data['step_title'] ) . ' ' . Utilities::getLabel( 'L_added_successfully' ) );
            }
        } else {
            Message::addErrorMessage( $record->getError( ) );
        }
		
		return $success;
    }

	function deletestep( $step_id ) { 
		if ( intval( $step_id ) <= 0 ) return false;
		
		$srch = Howitworks::search();
		$srch->addCondition('step_id', '=', $step_id);
		$rs = $srch->getResultSet();
		$db = &Syspage::getDb();
		$data = $db->fetch($rs);
		if($data){
			$data['step_delete'] = 1;
			$record = new TableRecord('tbl_howitworks');
			$record->assignValues($data);
			$success = $record->update(array('smt' => 'step_id = ?', 'vals' => array($step_id)));
			if ($success) {
				Message::addMessage( ucwords( $data['step_title'] ) . ' ' . Utilities::getLabel( 'L_deleted_successfully' ) );
			} else {
				Message::addErrorMessage($record->getError());
				return false;
			}
		}else{
			Message::addErrorMessage( Utilities::getLabel( 'L_Step_not_exists' ) );
			return false;
		}
		
		return true;
	}
	
	static function search($flds=array()) {
		$srch = new SearchBase('tbl_howitworks','steps');
		if(count($flds) > 0) $srch->addMultipleFields($flds);		
		$srch->addOrder('step_display_order');		
		return $srch;
	}

	function reOrder($data){
		if(count($data) <= 0) return false;		
		
		$record = new TableRecord('tbl_howitworks');				
		
		foreach($data as $key => $val){
			$record->assignValues(array('step_display_order'=>$val));
			$success=$record->update(array('smt' => 'step_id = ?', 'vals' => array($key)));
			if (!$success) {
				Message::addErrorMessage($record->getError());
				return false;
			}
		}

		if ( $success ) { 
			Message::addMessage( Utilities::getLabel( 'L_Re-ordered_successfully' ) );
		}
		
		return true;

	}	
	
}