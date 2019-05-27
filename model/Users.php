<?php

defined("ACC") || exit("Acc denied");

require_once('model/mysqlEnhance.php');
//2019-3月
//此folder內的class 涉及資料庫操作
//繼承了MysqlEnhance: 高度封裝的Mysql Object 可建立一個Mysql物件
//針對個人資訊頁的操作
class Users extends MysqlEnhance {
    public static $user_instance = null;
    private $table = 'users';
    //用取得用戶資料(where通用版)
    public function get_datum_where($fields=array("*"), $where = array() , $except_telephone=array()){
        if(!is_array($fields) || !is_array($where) ||!is_array($except_telephone )){
            return false;
        }
        
        $rs = $this->get_data($fields, $where, 'id', 'asc', $except_telephone);
        if(empty($rs)){
            return false;
        }else{
            return $rs[0];
        }
        
        
    }

}