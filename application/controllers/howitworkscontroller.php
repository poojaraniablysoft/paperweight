<?php
class HowitworksController extends LegitController {
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
    }
	
	public function default_action(){
		$db = &Syspage::getDb();
		$data = array();
		Syspage::addJs('js/jquery.fancybox.js');
		
		foreach(Applicationconstants::$arr_cats as $index => $title){
			$srch = Howitworks::search();
			$srch->addCondition('step_type_id', '=', $index);
			$srch->addCondition('step_delete', '=', 0);
			$srch->addOrder('step_display_order', 'ASC');
			$rs = $srch->getResultSet();
			$data[$index] = $db->fetch_all($rs);
		}
		
		$Meta = new Meta();
		$meta = $Meta->getMetaById(2);		
		
		$arr_listing=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);

		$this->set('arr_listing', $arr_listing);		
		
		$this->set('data_arr', $data);
		$this->set('howitworks', true);
		$this->_template->render();
	}
}
?>
