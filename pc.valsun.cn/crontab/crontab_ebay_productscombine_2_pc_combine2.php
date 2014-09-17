<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

//这个是先将关联表的全部删除后再同步的脚本
//$ebayGoodsList = array();
//$ebayGoodsList = UserCacheModel::getOpenSysApi('pc.erp.getEbayProductscombineCrontab',array('all'=>'all'),'gw88');
//print_r($ebayGoodsList);
//exit;
$countGoods = UserCacheModel::getOpenSysApi('pc.erp.getProductscombineCount',array('all'=>'all'),'gw88');
//print_r($countGoods);
//exit;
$countGoods = intval($countGoods);
if($countGoods <= 0){
    echo "获取总数小于或等于0，错误，退出 \n";
    exit;
}
echo "总数是 $countGoods \n";
$per = 200;//每次读取的条数
$countFor = ceil($countGoods/$per);
echo "读取次数为 $countFor \n";

$tName = 'pc_goods_combine';
$where = "WHERE 1=1";
OmAvailableModel::deleteTNameRow($tName, $where);//清空 combine 表
echo "pc_goods_combine 清空 success! \n";

$tName = 'pc_sku_combine_relation';
$where = "WHERE 1=1";
OmAvailableModel::deleteTNameRow($tName, $where);//清空旧的关系表

echo "pc_sku_combine_relation 清空 success! \n";
for($i=0;$i<$countFor;$i++){
    $start = $per * $i;
    //$tName = 'ebay_goods';
//    $select = '*';
//    $where = "limit $start,$per";
    //$ebayGoodsList = OmAvailableModel::getTNameList($tName, $select, $where);
    $ebayGoodsList = UserCacheModel::getOpenSysApi('pc.erp.getProductscombineCrontab2',array('start'=>$start,'per'=>$per),'gw88');
    echo "这是第 $i 次读取，读取为 $start $per \n";
    //print_r($ebayGoodsList);
//    exit;
    $pcGoodsArr = array();
    $now = time();
    foreach($ebayGoodsList as $value){
        $pcGoodsArr = array();
        $pcGoodsArr['id'] = $value['id'];
        $pcGoodsArr['combineSku'] = $value['goods_sn'];
        $tName = 'pc_goods_combine';
        //$where = "WHERE combineSku='{$value['goods_sn']}'";
//        OmAvailableModel::deleteTNameRow($tName, $where);//删除要插入的combineSku先
//        echo "删除 combineSku{{$value['goods_sn']}} success!\n";
        $pcGoodsArr['combineSpu'] = '';
        if(strpos($value['goods_sn'], '_') === false){//字符串中不带_
            $pcGoodsArr['combineSpu'] = $value['goods_sn'];
        }else{//字符串中带_
            //$subArr1 = explode('_',$value['goods_sn']);//_开始截取
//            if(!preg_match("/\d+/", $subArr1[0])){//按_截取后的第一个字符串如果不包含数字,则spu为整个字符串按照第二个_截取的[0]
//                $firstIndex = strpos($value['goods_sn'],'_');//在$value['goods_sn']中_首次出现的位置
//                $secondIndex = strpos($value['goods_sn'],'_',$firstIndex+1);
//                $pcGoodsArr['combineSpu'] = substr($value['goods_sn'],0,$secondIndex);
//            }else{//如果包含数字，则该spu就是[0]]
//                $pcGoodsArr['combineSpu'] = $subArr1[0];
//            }           
            if(preg_match("/^CB_[A-Z0-9]+(_[A-Z0-9]+)*$/", $value['goods_sn']) || preg_match("/^EB[A-Z0-9]+_[A-Z0-9]+(_[A-Z0-9]+)*$/", $value['goods_sn']) || preg_match("/^TK_CB[A-Z0-9]+(_[A-Z0-9]+)*$/", $value['goods_sn'])){//根据不同的格式，得出对应的虚拟主料号
                $_combineskus = explode('_', $value['goods_sn']);
                $pcGoodsArr['combineSpu'] = $_combineskus[0].'_'.$_combineskus[1];//spu只保留一个下划线
            }else{
                $_combineskus = explode('_', $value['goods_sn']);
                $pcGoodsArr['combineSpu'] = $_combineskus[0];
            }
            
        }
        $pcGoodsArr['combinePrice'] = $value['goods_price'];
        $pcGoodsArr['combineCost'] = $value['goods_price'];
        if(!empty($value['cguser'])){//如果对应采购字段不为空
            echo "purchase is {$value['cguser']}\n";
            if(intval($value['cguser']) == 0){//如果采购不是数字，则表示是名字
                $purchaseId = getPersonIdByName($value['cguser']);
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

            //$tName = 'pc_sku_combine_relation';
//            $where = "WHERE combineSku='{$pcGoodsArr['combineSku']}'";
//            OmAvailableModel::deleteTNameRow($tName, $where);//删除旧的关系
//            echo "{$pcGoodsArr['combineSku']} old relation delete success\n";
            foreach($tmpArr1 as $value1){
                $tmpArr2 = explode('*',$value1);//按*截取，获取真实料号及数量
                $trueSku = trim($tmpArr2[0]);
                $amount = trim($tmpArr2[1]);
                if(!empty($trueSku) && !empty($amount)){
                    $tName = 'pc_sku_combine_relation';
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
}




?>
