<?php
//初始化檔案

defined('ACC')||exit('ACC Denied');//防止用瀏覽器直接訪問
date_default_timezone_set("Asia/Taipei");//設定時區
header('Content-Type: text/html; charset=utf-8');//設定編碼

//配合unix
define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');
define('URL_API','http://127.0.0.1/test/api/');
define('DEBUG',true);//開啟錯誤訊息

require('include/lib_base.php');//引入遞歸加斜線方法


//過濾
$_GET = isset($_GET) ? _addslashes($_GET) : null;
$_POST = _addslashes($_POST);
$_COOKIE = _addslashes($_COOKIE);

//開啟session
session_start();
ini_set('session.gc_maxlifetime', 86400); //設定session 存活時間為1天


if(defined('DEBUG')) {
    error_reporting(E_ALL);//開發模式
} else {
    error_reporting(0);//上線模式
}


?>