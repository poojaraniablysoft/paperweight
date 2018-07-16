<?php
class Cmsblock extends Model {

    private static $_contents = array();
    
    static function getData($id) {
        $id = intval($id);
        if($id>0!=true) return array();
        $srch = new SearchBase('tbl_content_blocks', 't1');
        $srch->addCondition('t1.block_id', '=', $id);
        $srch->doNotLimitRecords(true);
        $srch->doNotCalculateRecords(true);
        $sql = $srch->getQuery();
        
        $db = &Syspage::getdb();
        $rs = $db->query($sql);
        $row = $db->fetch($rs);
        if($row==false) return array();
        else return $row;
    }
    
    static function search($criteria, $count='') {
        $srch = new SearchBase('tbl_content_blocks', 't1');
        if($count==true) {
            $srch->addFld('COUNT(t1.block_id) AS total_rows');
        } else {
            $srch->addMultipleFields(array(
                't1.block_id AS id',
                't1.block_title AS title',
                't1.block_content AS content',
                't1.block_page_name AS page_name',
            ));
        }
        foreach($criteria as $key=>$val) {            
            switch($key) {
            case 'id':
                $srch->addCondition('t1.block_id', '=', intval($val));
                break;
            case 'page_code':
                if($val != '')
                $srch->addCondition('t1.block_page_code', '=', $val);
                break;
            case 'page_name':
                if($val != '') 
                $srch->addCondition('t1.block_page_name', 'like', '%'.$val. '%');
                break;
            case 'block_title':
                $srch->addCondition('t1.block_title', 'like', '%'.$val.'%');
                break;
            }
        }
        $srch->addOrder('t1.block_id', 'ASC');
        return $srch;
    }
    
    static function getContent($page_code='', $mod='title') {
        if($page_code=='') return ;
        $row = self::getContentRow($page_code);
        switch($mod) {
            case 'title':
                return $row['title'];
                break;
            case 'content':
                return $row['content'];
                break;
        }
    }
    
    private static function getContentRow($page_code='') {
        if($page_code=='') return ;
        if(!isset(self::$_contents[$page_code])) {
        $srch = self::search(array('page_code'=>$page_code));
        $srch->doNotLimitRecords(true);
        $srch->doNotCalculateRecords(true);
        $sql = $srch->getQuery();
        
        $db = &Syspage::getdb();
        $rs = $db->query($sql);
        self::$_contents[$page_code] = $db->fetch($rs);
        }
        return self::$_contents[$page_code];
    }
}