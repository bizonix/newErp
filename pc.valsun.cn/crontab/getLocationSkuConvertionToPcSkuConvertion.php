<?php
require_once "/data/web/erpNew/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟



$tName = 'purchase_sku_conversion';
$select = '*';
$where = "WHERE 1=1";
$purchaseSkuConvertionList = OmAvailableModel::getTNameList($tName, $select, $where);
if(!empty($purchaseSkuConvertionList)){
    $tName = 'pc_sku_conversion';
    $where = "WHERE 1=1";
    OmAvailableModel::deleteTNameRow($tName, $where);
}
foreach($purchaseSkuConvertionList as $value){
    $id = $value['id'];
    $old_sku = $value['old_sku'];
    $new_sku = $value['new_sku'];
    $user = $value['user'];
    $createdtime = $value['createdtime'];
    $modifiedtime = $value['modifiedtime'];
    
    $dataConvertion = array();
    $dataConvertion['id'] = $id;
    $dataConvertion['old_sku'] = $old_sku;
    $dataConvertion['new_sku'] = $new_sku;
    $dataConvertion['addUserId'] = getPersonIdByName($user);
    $dataConvertion['createdTime'] = strtotime($createdtime);
    $dataConvertion['modifiedUserId'] = getPersonIdByName($user);
    $dataConvertion['modifiedTime'] = strtotime($modifiedtime);
    OmAvailableModel::addTNameRow2arr($tName, $dataConvertion);
}




?>
