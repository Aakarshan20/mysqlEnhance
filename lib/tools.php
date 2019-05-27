<?php

defined('ACC') || exit('ACC denied'); //避免瀏覽器直接訪問

class Tools {

    private static $instance; //本類別的實例

    public static function get_instance() {//取得實例
        if (!self::$instance) {//實例如果不存在
            self::$instance = new self(); //new 一個並存在本類別的實例中
        }
        return self::$instance; //不然就返回本實例
    }

    private function __construct() {//私人建構式避免外部直接new
    }

    private function __clone() {
        
    }

//避免外部clone

    public function empty_check_n_trim($value) {//檢查是否為空字串 並去頭尾空格返回
        //如果有設定 去頭尾 否則返true;
        $temp_value = isset($value) ? trim($value) : true;

        //如果不是0 且為empty 返true 不然返回剛剛trim的 值
        return $value_return = ($temp_value != "0" && empty($temp_value)) ? true : $temp_value;
    }

    public function empty_check($value) {//檢查是否為空字串 並去頭尾空格返回
        //如果值 返true;
        $temp_value = isset($value) ? $value : true;

        //如果不是0 且為empty 返true 不然返回剛剛的值
        return $value_return = ($temp_value != "0" && empty($temp_value)) ? true : $temp_value;
    }

    //檢查大於0的正整數
    function positive_integer_check($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return 'nan';
        } else if (strpos($value, '.') !== false) {
            return 'float';
        } else if ($value < 0) {
            return 'minus';
        } else {
            if (!is_null($min) && $value < $min) {//如果數字比最小值小 就傳smaller
                return 'smaller';
            } else if (!is_null($max) && $value > $max) {//如果數字比最大值大 就傳bigger
                return 'bigger';
            } else {
                return 'passed';
            }
        }
    }

    //檢查整數
    function integer_check($value, $min = null, $max = null) {
        if (!is_numeric($value)) {
            return 'nan';
        } else if (strpos($value, '.') !== false) {
            return 'float';
        } else {
            if (!is_null($min) && $value < $min) {//如果數字比最小值小 就傳smaller
                return 'smaller';
            } else if (!is_null($max) && $value > $max) {//如果數字比最大值大 就傳bigger
                return 'bigger';
            } else {
                return 'passed';
            }
        }
    }

    //判斷字元長度(預設utf-8)
    function string_length_check($str, $min_length, $max_length, $encoding = 'utf-8') {
        $mb_str_len = mb_strlen($str, $encoding);

        if ($mb_str_len > $max_length) {
            return 'longer';
        } else if ($mb_str_len < $min_length) {
            return 'shorter';
        } else {
            return 'passed';
        }
    }

    function post($url, $post_data) {

        //化為字串
        $string = http_build_query($post_data);

        //基本參數設定 
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => $string,
            ),
        );
        //內容設定
        $context = stream_context_create($options);

        //接收輸出結果集
        $result = file_get_contents($url, false, $context);

        //返回結果集
        return $result;
        //echo "ready to post";
    }

    //驗證字串中是否有不合法字元
    public function format_validation($value) {
        foreach ($this->invalid_character_arr as $v) {
            if (stripos($value, $v) !== false) {
                return $v; //如果有 返回該字元
            }
        }
        return true; //沒有 就返回true
    }

    //驗證字串中是否有不合法字元
    public function format_email_validation($value) {
        foreach ($this->invalid_email_character_arr as $v) {
            if (stripos($value, $v) !== false) {
                return $v; //如果有 返回該字元
            }
        }
        return true; //沒有 就返回true
    }

    //不合法字元表
    //所有全形半形標點含空白
    private $invalid_character_arr = array(
        ' ', '.', ',', ';', ':', '?', '!', '“', '”', '‘', '‘', '"', "'", '－', '-',
        '(', ')', '[', ']', '{', '}', '/', '\\', '　', '。', '，', '、', '`', '；', '：',
        '？', '！', '「', '」', '『', '』', '──', '—', '（', '）', '［', '］', '《', '》',
        '〈', '〉', '‧'
    );
    //不合法字元表(信箱專用)
    //部份全形半形標點含空白
    private $invalid_email_character_arr = array(
        ' ', ',', ';', ':', '?', '!', '“', '”', '‘', '‘', '"', "'", '－',
        '(', ')', '[', ']', '{', '}', '/', '\\', '　', '。', '，', '、', '`', '；', '：',
        '？', '！', '「', '」', '『', '』', '──', '—', '（', '）', '［', '］', '《', '》',
        '〈', '〉', '‧'
    );

    //正則判斷email格式
    public function email_validation($email_addr) {

        if (!preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/", $email_addr)) {
            return false;
        } else {
            return true;
        }
    }

    //連通php與python
    public function call_python($python_filename, $args_arr = array()) {
        include('include/config.inc.php');
        $python_command = $_CFG['python_command']; //依據環境取出python command

        $python_command .= ' ' . $python_filename; //將command接上python檔名

        if (!empty($args_arr)) {//如果有傳引數 拼成string並用空白隔開
            $python_command .= ' ' . implode(' ', $args_arr);
        }

        $jsondata = exec($python_command); //測試python串接
        return $jsondata;

        //return $_CFG;
    }

    //簡易sig判斷
    public function valid_sig($username, $sig, $salt = 3667) {
        return (md5(md5($username) . $salt) == $sig) ? true : false;
    }
    
    //印出分頁
    function get_pages($data_count, $page, $url, $perpage, $addition_query = array()) {
        $pages = ceil($data_count / $perpage); //計算頁數

        $html_code = "共 " . $data_count . ' 筆-在 ' . $page . ' 頁-共 ' . $pages . ' 頁';
        if ($addition_query == null) {
            $html_code .= "<br /><a href='" . $url . "?page=1&perpage=" . $perpage . "'>首頁</a> ";
            $html_code .= "第 ";
            for ($i = 1; $i <= $pages; $i++) {
                if ($page - 3 < $i && $i < $page + 3) {
                    $html_code .= "<a href='" . $url . "?page=" . $i . "&perpage=" . $perpage . "'>" . $i . "</a> ";
                }
            }
            $html_code .= " 頁 ";
            $html_code .= "<a href='" . $url . "?page=" . $pages . "&perpage=" . $perpage . "'>末頁</a> ";
        } else {
            
            //如果有別的查詢條件
            $html_built_query = http_build_query($addition_query);//拼成html query格式
            
            $html_code .= "<br /><a href='" . $url . "?page=1&perpage=" . $perpage . "&$html_built_query'>首頁</a> ";
            $html_code .= "第 ";
            for ($i = 1; $i <= $pages; $i++) {
                if ($page - 3 < $i && $i < $page + 3) {
                    $html_code .= "<a href='" . $url . "?" . "page=" . $i . "&perpage=" . $perpage . "&$html_built_query'>" . $i . "</a> ";
                }
            }
            $html_code .= " 頁 ";
            $html_code .= "<a href='" . $url . "?page=" . $pages . "&perpage=" . $perpage . "&$html_built_query'>末頁</a> ";
        }

        return $html_code;
    }
}
