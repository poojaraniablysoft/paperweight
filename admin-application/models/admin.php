<?php
class Admin extends Model {
	
    function __construct() {
        parent::__construct();
    }
	
    function login($username, $password) {
        /* @var $db Database */
        global $db;
		
		$srch = new SearchBase('tbl_admin');
		$srch->addCondition('admin_username', '=', $username);
		$srch->addCondition('admin_password', '=', encryptPassword($password));
		
		$rs = $srch->getResultSet();		
        $row = $db->fetch($rs);
		
		if (!$row) {
			$this->error = 'Invalid username or password';
            return false;
		}
		
        if ($row['admin_username'] != $username || $row['admin_password'] != encryptPassword($password)) {
            $this->error = 'Invalid username or password';
            return false;
        }
		
		$_SESSION['logged_admin']['admin_id']		= $row['admin_id'];
        $_SESSION['logged_admin']['admin_username']	= $row['admin_username'];
		
        return true;
    }

    function logout() {
        session_destroy();
		session_start();
    }

    static function isLogged() {
		if (!($_SESSION['logged_admin']['admin_id'] > 0)) return false;
		
		return true;
    }

    function changePassword($current_password, $new_password) {
        /* @var $db Database */
        global $db;
        $rs = $db->query("SELECT * FROM tbl_admin WHERE admin_username = '" . $_SESSION['logged_admin']['admin_username'] . "' AND admin_password = '" . encryptPassword($current_password) . "'");
        $row = $db->fetch($rs);
        if (!$row) {
            $this->error = 'Incorrect current password';
            return false;
        }
		if (!$db->query("UPDATE tbl_admin set admin_password='".encryptPassword($new_password)."' where admin_username='".$_SESSION['logged_admin']['admin_username']."'")){
            $this->error = $db->getError();
            return false;
        }
        

        return true;
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

        $record = new TableRecord('tbl_admin');

        if (!$record->loadFromDb(array('smt' => 'admin_id = ?', 'vals' => array($id)))) {
            $this->error = $record->getError();
            return false;
        }

        $data = $record->getFlds();
        $data['permissions'] = $this->getAdminPermissions($id);

        unset($data['admin_password']);

        return $data;
    }

    function getAdminPermissions($id) {
        global $db;

        if (!is_numeric($id)) {
            $this->error = 'Invalid Request';
            return false;
        }

        $id = intval($id);

        $sql = $db->query("SELECT ap_module, ap_permission FROM tbl_admin_permissions WHERE ap_admin_id = $id");
        $result_data = $db->fetch_all($sql);

        $permissions = array();

        foreach ($result_data as $arr) {
            $permissions[$arr['ap_module']] = $arr['ap_permission'];
        }

        return $permissions;
    }
	
	public function getOrdersCompleted() {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_tasks');
		$records->addCondition('task_status','=',3);
		$records->addMultipleFields(array('count(task_id) as completeorders'));
		$rs = $records->getResultSet();
		return $db->fetch($rs);
		//$records->joinTable('tbl_users','LEFT OUTER JOIN','u.user_is_approved = 1 && u.user_type=1');		
	}
	
	public function getActiveWriters() {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_users');
		$records->addCondition('user_type','=',1);
		$records->addCondition('user_active','=',1);
		$records->addMultipleFields(array('count(user_id) as active'));
		$rs = $records->getResultSet();
		return $db->fetch($rs);
	}
	public function getRegisteredUsers() {
		$db = &Syspage::getdb();
		$records = new SearchBase('tbl_users');
		$records->addMultipleFields(array('count(user_id) as registered'));
		$rs = $records->getResultSet();
		return $db->fetch($rs);
	}
	public function getWithdrawalRequests($page) {
		$db = &Syspage::getdb();
		
		$arr_req = array();
		
		$pagesize = 5;
		
		$srch = new SearchBase('tbl_withdrawal_requests', 'w');
		
		$srch->joinTable('tbl_users', 'INNER JOIN', 'w.req_user_id=u.user_id', 'u');
		
		$srch->addOrder('w.req_date', 'DESC');
		$srch->addCondition('w.req_status','=', 0);
		
		$srch->setPageSize($pagesize);
		
		$srch->setPageNumber($page);
		
		$srch->addMultipleFields(array('w.*','u.user_id' ,'u.user_screen_name','u.user_first_name','u.user_last_name'));
		
		$rs = $srch->getResultSet();
		
		$arr_req['data']			= $db->fetch_all($rs);
		$arr_req['total_records']	= $srch->recordCount();
		$arr_req['pagesize']		= $pagesize;
		$arr_req['pages']			= $srch->pages();
		$arr_req['page']			= $page;
		
		return $arr_req;
	}
	function getCountry($country_id) {
		$db = &Syspage::getdb();
		$srch = new SearchBase('tbl_countries');
		$srch->addCondition('country_id','=',$country_id);
		$srch->addFld('country_name');
		$rs = $srch->getResultSet();
		$country = $db->fetch($rs);
		
		return $country;
	}
}