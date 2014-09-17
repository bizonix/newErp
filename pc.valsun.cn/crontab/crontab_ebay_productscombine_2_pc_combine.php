<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

$ebayGoodsList = array();
$ebayGoodsList = UserCacheModel::getOpenSysApi('pc.erp.getEbayProductscombineCrontab',array('all'=>'all'),'gw88');
//print_r($ebayGoodsList);
//exit;
if(empty($ebayGoodsList)){
    echo 'no data';
    exit;
}
$tName = 'pc_goods_combine';
$where = "WHERE id>127600";
OmAvailableModel::deleteTNameRow($tName, $where);
echo "删除 id>127600 成功\n";
$pcGoodsArr = array();
foreach($ebayGoodsList as $value){
    $pcGoodsArr['id'] = $value['id'];
    $pcGoodsArr['combineSku'] = $value['goods_sn'];
    $pcGoodsArr['combineSpu'] = '';
    if(strpos($value['goods_sn'], '_') === false){//字符串中不带_
        $pcGoodsArr['combineSpu'] = $value['goods_sn'];
    }else{//字符串中带_
        $subArr1 = explode('_',$value['goods_sn']);//_开始截取
        if(!preg_match("/\d+/", $subArr1[0])){//按_截取后的第一个字符串如果不包含数字,则spu为整个字符串按照第二个_截取的[0]
            $firstIndex = strpos($value['goods_sn'],'_');//在$value['goods_sn']中_首次出现的位置
            $secondIndex = strpos($value['goods_sn'],'_',$firstIndex+1);
            $pcGoodsArr['combineSpu'] = substr($value['goods_sn'],0,$secondIndex);
        }else{//如果包含数字，则该spu就是[0]]
            $pcGoodsArr['combineSpu'] = $subArr1[0];
        }
    }


    $pcGoodsArr['combinePrice'] = $value['goods_price'];
    $pcGoodsArr['combineCost'] = $value['goods_price'];
    if(!empty($value['cguser'])){//如果对应采购字段不为空
        echo "purchase is {$value['cguser']}\n";
        if(intval($value['cguser']) == 0){//如果采购不是数字，则表示是名字
            $queryConditions = array('userName' =>$value['cguser']);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $purchaseId = $userInfo[0]['userId'];
        }else{//如果采购是数字，表示就是其ID
            $purchaseId = $value['cguser'];
        }
        echo "purchaseId is $purchaseId\n";
        $pcGoodsArr['combineUserId'] = $purchaseId;
    }
    $tName = 'pc_goods_combine';
//    $where = "WHERE combineSku='{$pcGoodsArr['combineSku']}'";
    //$countIsExist = OmAvailableModel::getTNameCount($tName, $where);
//    if($countIsExist>0){
//        echo "{$pcGoodsArr['combineSku']} has exist\n";
//        continue;
//    }
    $pcGoodsArr['combineWeight'] = $value['goods_weight']>=10?$value['goods_weight']/1000:$value['goods_weight'];
    $pcGoodsArr['combineNote'] = $value['notes'];
    $pcGoodsArr['addTime'] = $value['createdtime'];
    //$set = 'SET '.array2sql($pcGoodsArr);
    //$affectRowCom = OmAvailableModel::addTNameRow($tName, $set);
    OmAvailableModel::replaceTNameRow2arr($tName, $pcGoodsArr);
    $affectRowCom = 1;
    if($affectRowCom !== false){
        //echo "{$pcGoodsArr['combineSku']} insert success\n";
        $goods_sncombine = $value['goods_sncombine'];
        $tmpArr1 = explode(',',$goods_sncombine);//按逗号截取字符串，生成对应真实料号及对应数量的数组数量
        
        $tName = 'pc_sku_combine_relation';
        $where = "WHERE combineSku='{$pcGoodsArr['combineSku']}'";
        OmAvailableModel::deleteTNameRow($tName, $where);//删除旧的关系
        echo "{$pcGoodsArr['combineSku']} old relation delete success\n";        
        foreach($tmpArr1 as $value1){
            $tmpArr2 = explode('*',$value1);//按*截取，获取真实料号及数量
            $trueSku = trim($tmpArr2[0]);
            $amount = trim($tmpArr2[1]);
            if(!empty($trueSku) && !empty($amount)){ 
                $set = "SET combineSku='{$pcGoodsArr['combineSku']}',sku='$trueSku',count='$amount'";
                $affectRow = OmAvailableModel::addTNameRow($tName, $set);
                if($affectRow !== false){
                    echo "{$pcGoodsArr['combineSku']} $trueSku $amount insert success in relation\n";
                }else{
                    echo "{$pcGoodsArr['combineSku']} $trueSku $amount insert fail in relation\n";
                }
            }
        }
    }else{
        echo "{$pcGoodsArr['combineSku']} insert fail\n";
    }

}



?>
