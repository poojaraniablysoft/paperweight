<?php
class Users extends Model {
	
    function __construct() {
        parent::__construct();
    }   

    function addUpdate($data) {
        global $db;

        $admin_id = intval($data['admin_id']);
        if (!($admin_id > 0))
            $admin_id = 0;
        unset($data['admin_id']);

        $arr_fields = array();

        $arr_fields['admin_username'] = $data['admin_username'];
        $arr_fields['admin_email'] = $data['admin_email'];

        if ($data['admin_password'] != '')
            $arr_fields['admin_password'] = encryptPassword($data['admin_password']);

        if ($admin_id > 0) {
            $success = $db->update_from_array('tbl_admin', $arr_fields, array('smt' => 'admin_id = ?', 'vals' => array($admin_id)));
        } else {
            $success = $db->insert_from_array('tbl_admin', $arr_fields);
            $admin_id = $db->insert_id();
        }

        if ($success) {
            $db->deleteRecords('tbl_admin_permissions', array('smt' => 'ap_admin_id = ?', 'vals' => array($admin_id)));

            foreach ($data['permissions'] as $key => $val) {
                $db->insert_from_array('tbl_admin_permissions', array('ap_admin_id' => $admin_id, 'ap_module' => $key, 'ap_permission' => $val));
            }
        } else {
            $this->error = $db->getError();
            return false;
        }

        return true;
    }

    function getData($id) {

        if (!is_numeric($id)) {
            $this->error = 'Invalid Request';
            return false;
        }

        $id = intval($id);

        $record = new TableRecord('tbl_users');

        if (!$record->loadFromDb(array('smt' => 'user_id = ?', 'vals' => array($id)))) {
            $this->error = $record->getError();
            return false;
        }

        $data = $record->getFlds();
        //$data['permissions'] = $this->getAdminPermissions($id);

        unset($data['user_password']);

        return $data;
    }
	
	function preview_user($id) {
		$db = &Syspage::getdb();
		if (!is_numeric($id)) {
            $this->error = 'Invalid Request';
            return false;
        }

        $id = intval($id);
		
		$record	= new SearchBase('tbl_users','u');		
		$record->joinTable( 'tbl_user_profile', 'LEFT OUTER JOIN', 'up.userprofile_user_id=u.user_id', 'up');
		$record->joinTable( 'tbl_user_education', 'LEFT OUTER JOIN', 'ue.usered_user_id=u.user_id', 'ue');		
		$record->joinTable( 'tbl_user_experience', 'LEFT OUTER JOIN', 'uex.uexp_user_id=u.user_id', 'uex');						
		$record->joinTable( 'tbl_academic_degrees', 'LEFT OUTER JOIN', 'ad.deg_id=up.user_deg_id', 'ad');
		$record->joinTable( 'tbl_sample_essay', 'LEFT OUTER JOIN', 'u.user_id=se.file_user_id', 'se');
		
		$record->addMultipleFields('u.*','up.*','ue.*','ad.deg_name','se.*','uex.*');		
		$record->addCondition('u.user_id','=',$id);
		$rs = $record->getResultSet();		
		$row = $db->fetch($rs);
		
		$srch = new SearchBase('tbl_users','u');
		$srch->joinTable( 'tbl_user_citation_styles','INNER JOIN','uc.ucitstyle_user_id = u.user_id','uc');
		$srch->joinTable( 'tbl_citation_styles','INNER JOIN','cs.citstyle_id = uc.ucitstyle_citstyle_id','cs');
		$srch->addMultipleFields(array('cs.citstyle_name'));	
		$srch->addCondition('u.user_id','=',$id);
		$rs1 = $srch->getResultSet();		
		$citations = $db->fetch_all($rs1);
		foreach($citations as $val) {
			$cit[] = $val['citstyle_name'];
		}
		$row['citation_style'] = implode(',',$cit);
		
		$srch1 = new SearchBase('tbl_user_subscriptions','us');		
		$srch1->joinTable('tbl_newsletters','INNER JOIN','us.subscription_id = n.subs_id','n');
		$srch1->addFld('subs_name');	
		$srch1->addCondition('user_id','=',$id);
		$rs2 = $srch1->getResultSet();		
		$subscriptions = $db->fetch_all($rs2);
		foreach($subscriptions as $val) {
			$sub[] = $val['subs_name'];
		}
		$row['subscription'] = implode("\n",$sub);
		
		
		return $row;
	}
	
	public function getUserTransactionHistory($page,$user_id) {
		$db = &Syspage::getdb();
		
		$page = intval($page);
		if ($page < 1) $page = 1;
		
		$pagesize = 10;
		
		$srch = new SearchBase('tbl_wallet_transactions', 'w');
		
		$srch->joinTable('tbl_transactions', 'LEFT OUTER JOIN', 'w.wtrx_reference_trx_id=trx.trans_id', 'trx');
		$srch->joinTable('tbl_withdrawal_requests', 'LEFT OUTER JOIN', 'w.wtrx_withdrawal_request_id=wr.req_id', 'wr');
		$srch->joinTable('tbl_tasks', 'LEFT OUTER JOIN', 'w.wtrx_task_id=t.task_id', 't');
		
		$srch->addCondition('w.wtrx_user_id', '=', $user_id);
		
		$srch->addOrder('w.wtrx_date', 'desc');
		
		$srch->addMultipleFields(array('w.*', 't.task_ref_id', 'trx.trans_gateway_transaction_id', 'trx.trans_status', 'wr.req_transaction_id', 'wr.req_status'));
		
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		
		$rs = $srch->getResultSet();
		
		$transactions = $db->fetch_all($rs);
		
		foreach ($transactions as $key=>$arr) {			
			$transactions[$key]['wtrx_date']	= displayDate($arr['wtrx_date'], true, true, CONF_TIMEZONE);
		}
		
		$rows['transactions'] 	= $transactions;
		$rows['pages']			= $srch->pages();
		$rows['page']			= $page;
		$rows['pagesize']		= $pagesize;
		$rows['total_records']  = $srch->recordCount();
		return $rows;
	}
	
}