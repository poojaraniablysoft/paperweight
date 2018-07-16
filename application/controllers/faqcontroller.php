<?php
class FaqController extends LegitController {
	public function __construct($model, $controller, $action) {
        parent::__construct($model, $controller, $action);
    }
	
	public function default_action(){
		$db = &Syspage::getDb();
		$data = array();
		
		foreach(Applicationconstants::$arr_cats as $index => $title){
			$srch = Faq::search();
			$srch->addCondition('faq_type_id', '=', $index);
			$srch->addCondition('faq_delete', '=', 0);
			$srch->addOrder('faq_display_order', 'ASC');
			$rs = $srch->getResultSet();
			$data[$index] = $db->fetch_all($rs);
		}
		
		$this->set('data_arr', $data);
		
		$Meta = new Meta();
		$meta = $Meta->getMetaById(5);		
		
		$arr_listing=array('meta_title'=>$meta['meta_title'],
		'meta_keywords'=>$meta['meta_keywords'],
		'meta_description'=>$meta['meta_description']);

		$this->set('arr_listing', $arr_listing);		
		$this->set('faq', true);
		$this->_template->render();
	}
}
?>
