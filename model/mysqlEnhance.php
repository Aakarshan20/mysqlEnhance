<?php
defined("ACC") || exit('Acc denied');

require_once('model/mysql.php');

//2019-03-18  開發被繼承用mysql class
//此folder內的class 涉及資料庫操作
//高度封裝的Mysql Object 可建立一個Mysql物件 可被繼承 且取得了單例模式的mysql的實例
class MysqlEnhance{
    
    public static $mysql_instance = null;
    public static $stmt = 'mysqlenhance stmt';
    
    //建構式為取得mysql(pdo)的connection
    public function __construct(){
        self::$mysql_instance = mysql::get_instance();
    }
    
    //不綁參執行
    public function query($sql){
        self::$stmt = self::$mysql_instance->query($sql);
    }
    
    //預執行兼綁參
    public function prepare_bind_and_execute($sql, $bindValues, $rs = false){
        $this->prepare($sql);
        foreach($bindValues as $k=>$v){
            $this->bindValue($k+1, $v);
        }
        $this->execute();
        
        if($rs){
            return $this->fetch_all();
        }
    }
    
    //設定該物件stmt
    public function setStmt($stmt){
        self::$stmt = $stmt;
    }
    
    //取得該物件stmt
    public function getStmt(){
        return self::$stmt;
    }
    
    //封裝pdo的prepare
    public function prepare($sql){
        self::$stmt = self::$mysql_instance->prepare($sql);
    }
    
    //封裝pdo的bindValue
    public function bindValue($symble, $value){
        self::$stmt->bindValue($symble, $value);   
    }
    
    //封裝pdo的execute
    public function execute(){
        self::$stmt->execute();
        if(empty(self::$stmt)){//出錯就返false
            return false;
        }else{
            return true;
        }
    }
    
    //封裝pdo的fetchALl
    public function fetch_all($pdomethod = PDO::FETCH_CLASS){
        return self::$stmt->fetchAll($pdomethod);
    }
    
    //仿同名異式版取值
    public function find_all($table, $fields = array(), $where= array(), $order_by = 'id', $sort_way = 'asc', 
                $perpage = null, $page = null){
        return self::$mysql_instance->find_all($table, $fields, $where, $order_by, $sort_way,$perpage, $page);
    }
    
    //取單筆
    public function find($table, $fields = array(), $where= array()){
        $rs =  self::$mysql_instance->find_all($table, $fields, $where);
        if(empty($rs) || !is_array($rs)){//若為空 或是不為array
            return false;//你懂的
        }
        return $rs[0];//返回第一筆
    }
    
    
    //取出數量
    public function get_count($table, $where=array()){
        return self::$mysql_instance->get_count($table, $where);
    }
    
    //新增
    //預設不更新update_stamp
    public function insert($table, $data= array(), $update_timestamp = false){
        return self::$mysql_instance->insert($table, $data, $update_timestamp);
    }
}


