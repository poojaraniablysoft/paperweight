<?php
class LabelsController extends Controller{
    
	private $admin;
	
	function __construct($model, $controller, $action) {
		parent::__construct($model, $controller, $action);
		$this->set( 'selectedNav', $controller );
		$this->admin = new Admin();
	}
	
    function default_action( ) { 
		
		if ( !Admin::isLogged( ) ) { 
			redirectUser( generateUrl( 'admin', 'loginform' ) );
			return false;
		}
		
		$this->set( 'frmlabelSearch', $this->getSearchForm( ) );
		$this->search( );
    	$this->_template->render( );
		
    }
	
	private function search( ) { 
		
		if ( !Admin::isLogged( ) ) redirectUser( generateUrl( 'admin', 'loginform' ) );
		
		$postedData = array( 'pagesize' => 20 );
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) { 
			$post = Syspage::getPostedVar();
			$page = ( isset( $post['page'] ) && intval( $post['page'] ) > 0 )?intval( $post['page'] ):1;
			$postedData['page'] = $page;
			$postedData['keyword'] = $post['filter'];
			$this->set( 'filter', $post['filter'] );
		}
		
		$this->set( 'arr_listing', $this->Labels->getLabels( $postedData ) );
		$this->set( 'pages', $this->Labels->getTotalPages( ) );
		$this->set( 'page', $page );
		$this->set( 'pagesize', $pagesize );
		$this->set( 'start_record', ( $page - 1 ) * $pagesize + 1 );
		$end_record = $page * $pagesize;
		$total_records = $this->Labels->getTotalRecords( );
		
		if ( $total_records < $end_record )
			$end_record = $total_records;
		
		$this->set( 'end_record', $end_record );
		$this->set( 'total_records', $total_records );
		
	}
	
	private function getSearchForm( ) { 
		if (!Admin::isLogged()) die('Unauthorized Access!');
		
		$frm = new Form('frmSearchLabels','frmSearchLabels');
		$frm->setFieldsPerRow(2);
		$frm->setExtra('class="siteForm last_td_nowrap"');
		$frm->setMethod('POST');
		$frm->setAction('?');
		$frm->captionInSameCell(true);
		$frm->setRequiredStarWith('not-required');
		$frm->setTableProperties('class="formTable threeCol" width="100%"');
		$frm->addHiddenField('', 'mode', "search");
		$frm->addTextBox('Keyword', 'filter','','');
		$fld1 = $frm->addButton('', 'btn_cancel', 'Clear Search', '', ' class="medium" onclick="clearSearch()" style="cursor:pointer; margin-left:10px;"');
		$fld = $frm->addSubmitButton( '', 'btn_submit', 'Search', 'btn_submit' )->attachField( $fld1 );
		$frm->addHiddenField( '', 'page', 1 );
		$frm->setOnSubmit('searchLabels(this); return false;');
		
		if( $_SERVER['REQUEST_METHOD'] == 'POST' ) { 
			$post = Syspage::getPostedVar();
			$frm->fill( $post );
		}
		
		return $frm;
	}
	
	function update_label(){
		if (!Admin::isLogged()) die('Unauthorized Access!');
		$post = Syspage::getPostedVar();
		$post["value"] = html_entity_decode($post["value"]);
		$this->Labels->updateLabelText($post);
		echo htmlentities($post["value"]);
	}
}