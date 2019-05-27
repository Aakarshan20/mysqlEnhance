<?php


defined('ACC')||exit('ACC Denied');//防止別人訪問

//遞歸轉義array
function _addslashes($arr) {
    if(is_array($arr)){
        foreach($arr as $k=>$v) {
            if(is_string($v)) {
                $arr[$k] = addslashes($v);
            } else if(is_array($v)) {  // 再加判斷,如果是陣列,調用自身,再轉
                $arr[$k] = _addslashes($v);
            }
        }
    }
    return $arr;
}
?>