<?php
class RatingController extends Controller{
    
	private $admin;
	private $ratings;
	private $orders;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
		$this->ratings = new Ratings();
		$this->orders = new Orders();
	}
	
    function default_action() {
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		if($user_id > 0) {
			$user_id = intval($user_id);
		}
		$this->set('frmordersSearch', $this->getSearchForm());
		$this->search($user_id);
    	$this->_template->render();
    }
	
	private function getSearchForm(){
		
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$frm = new Form('frmordersSearch');
		$frm->setAction('rating');
		$frm->setExtra('class="siteForm" width="100%"');
		$frm->setTableProperties('class="formTable" width="100%"');
		$fld = $frm->addTextBox('', 'keyword','','','placeholder="Keyword (Reference ID OR any Username)"');
		$fld->captionCellExtra="width='35%'";
		
		$frm->addHiddenField('', 'page', 1);
		$fld_submit = $frm->addSubmitButton('', 'btn_submit', 'Search');
		$fld_reset = $frm->addButton('', 'cancel_search', 'Reset','','style="cursor:pointer; margin-left:10px;" onclick="document.location.href=\''.generateUrl('rating').'\'"');
		$fld_submit->attachField($fld_reset);
		return $frm;
	}
	
	function search($user_id){
		
		if (!Admin::isLogged()) redirectUser(generateUrl('admin', 'loginform'));
		$post = Syspage::getPostedVar();
		$db = &Syspage::getdb();
		//print_r($post);
		
		$srch = new SearchBase('tbl_tasks','t');
		
		$srch->joinTable('tbl_reviews', 'LEFT OUTER JOIN', 't.task_id=r1.rev_task_id AND t.task_user_id=r1.rev_reviewer_id', 'r1');
		$srch->joinTable('tbl_reviews', 'LEFT OUTER JOIN', 't.task_id=r2.rev_task_id AND t.task_writer_id=r2.rev_reviewer_id', 'r2');
		
		$srch->joinTable('tbl_users','INNER JOIN','u1.user_id=t.task_user_id','u1');
		$srch->joinTable('tbl_users','INNER JOIN','u2.user_id=t.task_writer_id','u2');
			
		if (isset($post['keyword']) && $post['keyword'] != ''){
			$cnd = $srch->addCondition('t.task_ref_id', 'LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition('u1.user_screen_name','LIKE', '%' . $post['keyword'] . '%');
			$cnd->attachCondition('u2.user_screen_name','LIKE', '%' . $post['keyword'] . '%');
		}
		
		//if (is_numeric($post['task_status'])) $srch->addCondition('t.task_status', '=', intval($post['task_status']));
				
		$pagesize = 10;
		
		$page = intval($post['page']);
		if (!($page > 0)) $page = 1;
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$srch->addGroupBy('t.task_id');
		$srch->addOrder('t.task_id','DESC');
		
		$srch->addMultipleFields(array('t.task_id','t.task_topic','t.task_ref_id','t.task_user_id','t.task_writer_id','u1.user_first_name as customer','u2.user_screen_name as writer', 'r1.rev_comments AS customer_comment', 'r2.rev_comments AS writer_comment','r1.rev_status AS customer_comment_status', 'r2.rev_status AS writer_comment_status','r1.rev_is_featured as cus_rev_featured','r2.rev_is_featured as wri_rev_featured','r1.rev_id as cus_rev_id','r2.rev_id as wri_rev_id'));
		if($user_id >0) {
			$srch->addCondition('u1.user_id','=',$user_id);
		}
		$srch->addCondition('t.task_status','=',3);
		
		//echo $srch->getQuery();
		
		$rs = $srch->getResultSet();
		$data = $db->fetch_all($rs);
		foreach($data as $key=>$arr_data) {
			
			
			$data[$key]['customer_rating'] = $this->ratings->getTaskRating($arr_data['task_id'],$arr_data['task_user_id']);
			$data[$key]['writer_rating'] = $this->ratings->getTaskRating($arr_data['task_id'],$arr_data['task_writer_id']);
		}
		//echo "<pre>".print_r($data,true)."</pre>";
		
		/* Dummy content to test functionality 22-may-2014 */
		/* 	$content = array('0'=>array('task_id'=>'2','task_topic'=>'financial','task_ref_id'=>'21321432','task_user_id'=>'21','task_writer_id'=>18,'customer'=>'Customer','writer'=>'Writer','customer_rating'=>array('delivery_rating'=>4,'communication_rating'=>4.5,'quality_rating'=>4),'writer_rating'=>array('delivery_rating'=>3,'communication_rating'=>3.5,'quality_rating'=>4))); */
		/* end here */
		
		
		
		
		
		$this->set('arr_listing',$data);
		$this->set('pages', $srch->pages());
		$this->set('page', $page);
		$this->set('pagesize', $pagesize);
		$this->set('start_record', ($page-1)*$pagesize + 1);
		$end_record = $page * $pagesize;
		$total_records = $srch->recordCount();
		if ($total_records < $end_record) $end_record = $total_records;
		$this->set('end_record', $end_record);
		$this->set('total_records', $total_records);				
	}

}