<?php 
defined('ACC')||exit('ACC denied');

//引入設定檔

class Mysql {
	
	private static $instance ;//本類別的實例 也就是conn
	private static $db;//資料庫實例
	
	public static function get_instance(){//外部取得實例的接口
		if(!self::$instance){//如果物件(實例)尚未存在
			include('include/config.inc.php');
			self::$instance = new self($_CFG);//新增物件並存入實例
		}
		return self::$instance;//否則回傳本實例
	}
	
	private function __clone(){}//避免clone
	
	private function __construct($_CFG){//建構式初始化參數private 防止外部呼叫
		
		//拼接dsn
		$dsn = "mysql:host=" . $_CFG['host'] . ";dbname=" . $_CFG['db'];
//		 echo $dsn;
//		 exit();
		if(!isset(self::$instance)){
				
                    self::$db= new PDO($dsn,$_CFG['user'],$_CFG['pwd']);
                    self::$db->query('set names "utf8"');	
			
		}
	}
	
	public function query($sql){//外部操作接口
		return self::$db->query($sql);
	}
	
	public function prepare($sql){//外部操作接口
		return self::$db->prepare($sql);
	}
        
        //取所有
	public function find_all($table, $fields = array(), $where= array(), $order_by = 'id', $sort_way = 'asc', 
                $perpage = null, $page = null){
            //綁參時 表名不能綁 會出錯
            $fields_str = implode(',' , $fields);
            $sql = "select " . $fields_str . " from " . $table ;
            
            //如果where 沒有傳入 就不用循環綁
            if(!empty($where)){
                
               $count=0;//計數器
               foreach($where as $k=>$v){
                   $sql .= ($count==0) ? " where " : " and ";//如果是第一筆 就where 其他就加上and
                   $sql .= "$k = :$k " ; //加上field and value, 等一下要綁的值前加上冒號
                   $count++;
               }
                
               $sql .= " order by " . $order_by . " " . $sort_way ; 
               if(!is_null($page) && !is_null($perpage)){
                    $sql .= " limit " . ($page-1) * $perpage . ", " . $perpage;
               }
               
               //送出prepare指令
               $stmt = $this::$instance->prepare($sql);
               foreach($where as $k=>$v){//循環綁參 綁的field為$k 值為$v
                   $stmt->bindValue(":$k", $v);
               }
               
            }else{
                $sql .= " order by " . $order_by . " " . $sort_way ; 
                if(!is_null($page) && !is_null($perpage)){
                    $sql .= " limit " . ($page-1) * $perpage . ", " . $perpage;
               }
                //送出prepare指令
                $stmt = $this::$instance->prepare($sql);
            }
            
            $stmt->execute();
            
            if(empty($stmt)){
                return false;
            }
            
            $rs =  $stmt->fetchAll(PDO::FETCH_CLASS);
            
            if(empty($rs)){
                return false;
            }
            
            return $rs;
        }
        
        
        //取單筆
        public function find($table, $where= array()){
            //綁參時 表名不能綁 會出錯
            $sql = "select * from " . $table ;
            
            //如果where 沒有傳入 就不用循環綁
            if(!empty($where)){
                
               $count=0;//計數器
               foreach($where as $k=>$v){
                   $sql .= ($count==0) ? " where " : " and ";//如果是第一筆 就where 其他就加上and
                   $sql .= "$k = :$k " ; //加上field and value, 等一下要綁的值前加上冒號
                   $count++;
               }
                //送出prepare指令
               $stmt = $this::$instance->prepare($sql);
               foreach($where as $k=>$v){//循環綁參 綁的field為$k 值為$v
                   $stmt->bindValue(":$k", $v);
               }
              
            }else{
                //送出prepare指令
                $stmt = $this::$instance->prepare($sql);
            }
            
            $stmt->execute();
            
            if(empty($stmt)){
                return false;
            }
            
            $rs =  $stmt->fetchAll(PDO::FETCH_CLASS);
            
            if(empty($rs)){
                return false;
            }
            
            return $rs[0];
        }
        
        //取得$table表中的數量 並綁參
        public function get_count($table, $where = array()){
            //綁參時 表名不能綁 會出錯
            $sql = "select count(1) as count from " . $table ;
            
            
            //如果where 沒有傳入 就不用循環綁
            if(!empty($where)){
                
               $count=0;//計數器
               foreach($where as $k=>$v){
                   $sql .= ($count==0) ? " where " : " and ";//如果是第一筆 就where 其他就加上and
                   $sql .= "$k = :$k " ; //加上field and value, 等一下要綁的值前加上冒號
                   $count++;
               }
                //送出prepare指令
               $stmt = $this::$instance->prepare($sql);
               foreach($where as $k=>$v){//循環綁參 綁的field為$k 值為$v
                   $stmt->bindValue(":$k", $v);
               }
            }else{
                //送出prepare指令
                $stmt = $this::$instance->prepare($sql);
            }
            
            $stmt->execute();
            
            $rs = $stmt->fetchAll(PDO::FETCH_CLASS);
            
            if(empty($rs)){
                return false;
            }
            
            return $rs[0]->count;
            
        
        //使用例
        //$table = "gsk_epf_messages";
        //$where_condition = array('status'=>'enable', 'lecture_id'=>1);
        //$mysql= mysql::get_instance();
        //$result = $mysql->get_count($table, $where_condition);
        //以上效果等同於 select count(1) from $table $where;
        }
        
        
        //通用insert方法        
        //參數 table  表名
        //data= array('欄位名'=>欄位值)
        //update_timestamp 是否自動新增 updated_time欄位
        public function insert($table , $data, $update_timestamp =true){
            if(empty($table) || empty($data)){
                return false;
            }
            
            $sql = ' insert into ' . $table .'(';
            
            $counter = 0;//計數用
            foreach(array_keys($data) as $k){
                if($counter != count($data)-1){
                    $sql .= "`$k`, ";
                }else{
                    if($update_timestamp === true){
                        $sql .= "`$k`, updated_time)";
                    }else{
                        $sql .= "`$k`)";
                    }
                }
                $counter++;
            }
            
            $counter_values =0;//記value用
            
            $sql .= " values (";
            
            foreach(array_keys($data) as $k){
                if($counter_values != count($data)-1){
                    $sql .= ":$k, ";
                }else{
                    if($update_timestamp === true){
                        $sql .= ":$k, now())";
                    }else{
                        $sql .= ":$k)";
                    }
                }
                $counter_values++;
            }
            
            //送出prepare指令
            $stmt = $this::$instance->prepare($sql);
            foreach($data as $k=>$v){//循環綁參 綁的field為$k 值為$v
                $stmt->bindValue(":$k", $v);
            }
            $rs =  $stmt->execute();
            
            if(empty($rs)){
                return false;
            }
            
            $sql_last_id  = " select last_insert_id() as last_id from " . $table;
            
            $stmt_last_id  = $this::$instance->query($sql_last_id);
            if(empty($stmt_last_id)){
                return false;
            }
            
            $rs_last_id = $stmt_last_id->fetchAll(PDO::FETCH_CLASS);
            
            if(empty($rs_last_id)){
                return false;
            }
            return $rs_last_id[0]->last_id;
            
        }
        
        
        

}


?>

















