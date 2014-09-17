<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

$ebayGoodsList = array();
//$ebayGoodsList = OmAvailableModel::newData2ErpInterfOpen('pc.erp.getEbayGoodsCrontab',array('all'=>'all'),'gw88',false);
//$tName = 'ebay_goods';
//$select = '*';
//$countGoods = OmAvailableModel::getTNameCount($tName, '');
$countGoods = UserCacheModel::getOpenSysApi('pc.erp.getEbayOrderProductCount',array('all'=>'all'),'gw88');
//print_r($countGoods);
//exit;
$countGoods = intval($countGoods);
if($countGoods <= 0){
    echo "总数小于或等于0，错误，退出 \n";
    exit;
}
echo "总数是 $countGoods \n";
$per = 200;//每次读取的条数
$countFor = ceil($countGoods/$per);
echo "每次读取 $per 条\n";
echo "要循环 $countFor 次 \n";
for($i=0;$i<$countFor;$i++){
    $start = $per * $i;
    //$tName = 'ebay_goods';
//    $select = '*';
//    $where = "limit $start,$per";
    //$ebayGoodsList = OmAvailableModel::getTNameList($tName, $select, $where);
    $skuList = UserCacheModel::getOpenSysApi('pc.erp.getEbayOrderProductCrontab',array('start'=>$start,'per'=>$per),'gw88');
    echo "这是第 $i 次读取，读取为 $start $per \n";
    //print_r($skuList);
//    exit;
    $pcProductsArr = array();
    $now = time();
    foreach($skuList as $value){
        $pcProductsArr['id'] = $value['id'];
        $pcProductsArr['sku'] = $value['sku'];
        if(!empty($value['sku'])){
            $tmpArr = explode('_',$value['sku']);
            $pcProductsArr['spu'] = $tmpArr[0];
        }
        $pcProductsArr['productsStatus'] = $value['change_type'];
        
        $comfirmer = $value['comfirmuser'];
        $taker = $value['takeuser'];
        $completer = $value['completeuser'];
        
        if(!empty($comfirmer)){
            echo "签收人 is $comfirmer\n";
            $queryConditions = array('userName' =>$comfirmer);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "签收人Id is '$personId'\n";
            $pcProductsArr['productsComfirmerId'] = $personId;
        }
        if(!empty($taker)){
            echo "领取人 is $taker\n";
            $queryConditions = array('userName' =>$taker);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "领取人Id is '$personId'\n";
            $pcProductsArr['productsTakerId'] = $personId;
        }
        if(!empty($completer)){
            echo "制作完成人 is $completer\n";
            $queryConditions = array('userName' =>$completer);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "制作完成人Id is '$personId'\n";
            $pcProductsArr['productsCompleterId'] = $personId;
        }
        $pcProductsArr['productsComfirmTime'] = $value['comfirmtime'];
        $pcProductsArr['productsTakeTime'] = $value['taketime'];
        $pcProductsArr['productsCompleteTime'] = $value['completetime'];
        
        $tName = 'pc_products';
        OmAvailableModel::replaceTNameRow2arr($tName, $pcProductsArr);
    }
}




?>
