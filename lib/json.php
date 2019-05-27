<?php

defined('ACC')||exit('ACC denied');//避免瀏覽器直接訪問

// echo "json";
// /*
class Json {
	private static $instance ;//本類別的實例
	
	public static function get_instance(){//取得實例
		if(!self::$instance){//實例如果不存在
			self::$instance = new self();//new 一個並存在本類別的實例中
		}
		return self::$instance;//不然就返回本實例
	}
	private function __construct(){//私人建構式避免外部直接new
	
	}
	
	private function __clone(){}//避免外部clone
	public function success($comment, $data = array()){//成功時返回的方法

		$output = array();

		$output['status'] = 'success';
		$output['comment'] = $comment;

		if (!empty($data)) {
			$output['data'] = $data;
		}

		SELF::_render($output);//使用render拼接
	}
	
	public function fail($comment, $data = array()){

		$output = array();

		$output['status'] = 'fail';
        $output['comment'] = $comment;

		if (!empty($data)) {
			$output['data'] = $data;
		}

		SELF::_render($output);
	}
	private function _render($output){

        header_remove();//重新定義header

		//json 格式
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        header('Content-Type: application/json; charset=utf-8');

		//版本判斷
        if (phpversion() >= 5.4) {
            echo json_encode($output, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } else {
            echo json_encode($output);
        }
		//中斷點
        exit();
	}
	
}
// */

?>