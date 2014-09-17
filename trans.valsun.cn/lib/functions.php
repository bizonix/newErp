<?php
/*
 * 公用函数
 */

/*
 * 格式化时间 以 Y-m-d H:i:s来显示时间
 */
function timeFormat($timestamp){
    return date('Y-m-d H:i:s' ,$timestamp);
}

/*
 * 判断是否有某个本地权限
 * $group 权限组名 $pwoer权限名
 */
function LP($group, $power){
    $localpowerlist = UserCacheModel::getLocalPowerList($_SESSION['userId']);
    if(array_key_exists($group, $localpowerlist) && in_array($power, $localpowerlist[$group])){
        return TRUE;
    }else{
        return FALSE;
    }
}

/*
 * 判断用户是否具有鉴权系统的某个权限
 * $group 权限组名 $pwoer权限名
 */
function P($group, $power){
    $powerlist = UserCacheModel::getPowerList($_SESSION['userId']);
    if(array_key_exists($group, $powerlist) && in_array($power, $powerlist[$group])){
        return TRUE;
    }else{
        return FALSE;
    }
}