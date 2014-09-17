<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

$specialStatusArr = array();//存放特殊性质SKU对应的值，同步ebay_adjust_transport
$ebay_adjust_transportArr = UserCacheModel::getOpenSysApi('pc.erp.getEbay_adjust_transport',array('all'=>'all'),'gw88');
foreach($ebay_adjust_transportArr as $value1){
    $id = $value1['id'];
    $skulist = $value1['skulist'];
    $skulistArr = explode(',', $skulist);
    foreach($skulistArr as $value2){
        if(!empty($value2)){
            $specialStatusArr[trim($value2)] = $id;
        }
    }
}
//print_r($specialStatusArr);
//exit;

$ebayGoodsList = array();
//$ebayGoodsList = OmAvailableModel::newData2ErpInterfOpen('pc.erp.getEbayGoodsCrontab',array('all'=>'all'),'gw88',false);
$tName = 'ebay_goods';
$select = '*';
//$countGoods = OmAvailableModel::getTNameCount($tName, '');
$countGoods = UserCacheModel::getOpenSysApi('pc.erp.getGoodsCount',array('all'=>'all'),'gw88');
//print_r($countGoods);
//exit;
echo "总数是 $countGoods \n";
$per = 200;//每次读取的条数
$countFor = ceil($countGoods/$per);
echo "要循环的次数为 $countFor \n";
//if($countFor >= 1){
//    $tName = 'pc_goods';
//    $where = "WHERE 1=1";
//    OmAvailableModel::deleteTNameRow($tName, $where);
//    echo "pc_goods 已经清空 \n";
//}
for($i=0;$i<$countFor;$i++){
    $start = $per * $i;
    //$tName = 'ebay_goods';
//    $select = '*';
//    $where = "limit $start,$per";
    //$ebayGoodsList = OmAvailableModel::getTNameList($tName, $select, $where);
    $ebayGoodsList = UserCacheModel::getOpenSysApi('pc.erp.getEbayGoodsCrontab',array('start'=>$start,'per'=>$per),'gw88');
    echo "这是第 $i 次读取，读取为 $start $per \n";
    //print_r($ebayGoodsList);
//    exit;
    $now = time();
    foreach($ebayGoodsList as $value){
        $pcGoodsArr = array();
        $pcGoodsArr['id'] = $value['goods_id'];
        $pcGoodsArr['goodsName'] = $value['goods_name'];
        if(empty($value['goods_name']) || $value['goods_name'] == '无'){
            $pcGoodsArr['is_delete'] = 1;
        }
        $pcGoodsArr['sku'] = $value['goods_sn'];
        //$tName = 'pc_goods';
//        $where = "WHERE sku='{$pcGoodsArr['sku']}'";
//        $countIsExist = OmAvailableModel::getTNameCount($tName, $where);
//        if($countIsExist>0){
//            echo "{$pcGoodsArr['sku']} has exist\n";
//            //continue;
//        }
        $spu = $value['spu'];
        if(empty($spu)){//spu为空，则将sku按下划线截取的第一个元素值赋过去
            $tmpSpu = explode('_',$value['goods_sn']);
            $spu = $tmpSpu[0];
        }
        if(strlen($spu)<3){//spu小于三位，填充
            $spu = str_pad($spu, 3, '0', STR_PAD_LEFT);
        }
        $pcGoodsArr['spu'] = $spu;
        $pcGoodsArr['goodsCost'] = $value['goods_cost'];
        $pcGoodsArr['goodsWeight'] = $value['goods_weight'];
        $pcGoodsArr['goodsNote'] = $value['goods_note'];
        $pcGoodsArr['goodsLength'] = $value['goods_length'];
        $pcGoodsArr['goodsWidth'] = $value['goods_width'];
        $pcGoodsArr['goodsHeight'] = $value['goods_height'];
        $pcGoodsArr['goodsColor'] = $value['color'];
        $pcGoodsArr['isPacking'] = $value['ispacking'] == 1 ? 2 : 1;//是否带包装，1为不带包装，2为带包装
        $pcGoodsArr['goodsCategory'] = $value['goods_category'];
        if($value['isuse']==0){
            $pcGoodsArr['goodsStatus'] = 1;//在线
        }elseif($value['isuse']==1){
            $pcGoodsArr['goodsStatus'] = 2;//停售
        }elseif($value['isuse']==2){
            $pcGoodsArr['goodsStatus'] = 2;//零库存，也就是停售
        }elseif($value['isuse']==3){
            $pcGoodsArr['goodsStatus'] = 3;//暂时停售
        }elseif($value['isuse']==51){
            $pcGoodsArr['goodsStatus'] = 51;//PK产品
        }else{
            $pcGoodsArr['goodsStatus'] = 2;//其他认为是停售
        }
        if(!empty($value['cguser'])){
            echo "purchase is {$value['cguser']}\n";
            $purchaseId = getPersonIdByName($value['cguser']);
            echo "purchaseId is '$purchaseId'\n";
            $pcGoodsArr['purchaseId'] = $purchaseId;
        }

        $tName = 'pc_packing_material';
        $select = 'id';
        $where = "WHERE pmName='{$value['ebay_packingmaterial']}'";
        $pmInfo = OmAvailableModel::getTNameList($tName, $select, $where);
        $pcGoodsArr['pmId'] = $pmInfo[0]['id'];
        $pcGoodsArr['goodsUpdateTime'] = $value['update_status_time'];
        $pcGoodsArr['goodsCreatedTime'] = $value['add_time'];
        $pcGoodsArr['goodsSort'] = $value['mainsku'];
        $pcGoodsArr['isNew'] = $value['is_new'];
        $pcGoodsArr['goodsSize'] = $value['size'];
        $pcGoodsArr['pmCapacity'] = $value['capacity'];
        $pcGoodsArr['packageType'] = $value['package_type'];
        $pcGoodsArr['goodsColor'] = intval($value['color']);
        $pcGoodsArr['goodsSize'] = intval($value['size']);
        $pcGoodsArr['checkCost'] = $value['checkCost'];// add by zqt 20140321
        if(!empty($value['ow_inCharge_user'])){
            echo "OverSeaSkuCharger is {$value['ow_inCharge_user']}\n";
            $OverSeaSkuCharger = getPersonIdByName($value['ow_inCharge_user']);
            echo "OverSeaSkuCharger is '$OverSeaSkuCharger'\n";
            $pcGoodsArr['OverSeaSkuCharger'] = $OverSeaSkuCharger;
        }
        if(isset($specialStatusArr[$value['goods_sn']]) && !empty($specialStatusArr[$value['goods_sn']])){
            $pcGoodsArr['specialStatus'] = $specialStatusArr[$value['goods_sn']];//同步该sku的特殊状态过去
            echo "specialStatus is {$specialStatusArr[$value['goods_sn']]}\n";
        }else{
            echo "NO specialStatus value!\n";
        }

        $tName = 'pc_goods';
        $set = 'SET '.array2sql($pcGoodsArr);

        OmAvailableModel::replaceTNameRow2arr($tName, $pcGoodsArr);


        //if($affectRow){

            //$partnerId = $value['factory']?$value['factory']:0;
//            $tName = 'pc_goods_partner_relation';
//            $where = "WHERE sku='{$pcGoodsArr['sku']}'";
//            OmAvailableModel::deleteTNameRow($tName, $where);//删除原来的关系
//            echo "{$pcGoodsArr['sku']} relation was delete!\n";
//            $set = "SET sku='{$pcGoodsArr['sku']}',partnerId='$partnerId'";
//            OmAvailableModel::addTNameRow($tName, $set);
//            echo "{$pcGoodsArr['sku']} relation add success!\n";
            
            //if(!empty($value['goods_hgbm'])){//海关编码不为空时，插入sku，海关编码关系表
//                $tName = 'pc_spu_tax_hscode';
//                $where = "WHERE spu='$spu'";
//                $countHscode = OmAvailableModel::getTNameCount($tName, $where);
//                $hsCodeArr = array();
//                $hsCodeArr['hsCode'] = $value['goods_hgbm'];
//                $hsCodeArr['customsName'] = $value['customsName'];
//                $hsCodeArr['exportRebateRate'] = $value['exportRebateRate'];
//                $hsCodeArr['importMFNRates'] = $value['importMFNRates'];
//                $hsCodeArr['generalRate'] = $value['generalRate'];
//                $hsCodeArr['RegulatoryConditions'] = $value['RegulatoryConditions'];
//                if($countHscode){//如果已经存在记录时，更新
//                    echo "$spu hscode had exist\n";
//                    OmAvailableModel::updateTNameRow2arr($tName, $hsCodeArr, $where);
//                    echo "$spu hscode update success\n";
//                }else{//不存在时，添加
//                    $hsCodeArr['spu'] = $spu;
//                    OmAvailableModel::addTNameRow2arr($tName, $hsCodeArr);
//                    echo "$spu hscode insert success\n";
//                }
//                //$set = "SET sku='{$value['goods_sn']}',hsCode='{$value['goods_hgbm']}',createdTime='$now'";
////                if(!$countHscode){
////                    OmAvailableModel::addTNameRow($tName, $set);
////                    echo "hscode insert success!\n";
////                }
//
//            }
            //在新系统中插入对应的自动生成SPU记录，和相应映射的SPU档案
            //$isMinCategory = true;
//            $tName = 'pc_goods_category';
//    		$where = "WHERE path like'{$value['goods_category']}-%' and is_delete=0";
//    		$count = OmAvailableModel :: getTNameCount($tName, $where);
//    		if ($count || empty($value['goods_category'])) {
//    			$isMinCategory = false;//不是最小分类,或者无分类
//    		}
//            if(!empty($spu) && !empty($pcGoodsArr['purchaseId']) && $isMinCategory){//spu不为空，并且是最小分类
//                //添加自动生成SPU记录
//                $tName = 'pc_auto_create_spu';
//                $where = "WHERE spu='{$spu}'";
//                $countAutoCreSpu = OmAvailableModel::getTNameCount($tName, $where);
//                if($countAutoCreSpu){
//                    echo "{$spu} autoCreateSpu has exist!\n";
//                    //continue;
//                }
//                if(preg_match("/^[A-Z]{2}[0-9]{6}$/",$spu)){
//                    $sort = intval(substr($spu, 2));
//                }else{
//                    $sort = 0;
//                }
//                $set = "SET spu='{$spu}',purchaseId='$purchaseId',createdTime='$now',sort='$sort',status=2";
//                if(!$countAutoCreSpu){
//                    OmAvailableModel::addTNameRow($tName, $set);
//                    echo "{$spu} autoCreateSpu insert success\n";
//                }
//
//                //添加SPU档案
//                $tName = 'pc_spu_archive';
//                $where = "WHERE spu='{$spu}'";
//                $countSpuArchive = OmAvailableModel::getTNameCount($tName, $where);
//                if($countSpuArchive){
//                    echo "{$spu} spuArchive has exist!\n";
//                    $set = "SET categoryPath='{$value['goods_category']}',purchaseId='{$pcGoodsArr['purchaseId']}'";
//                    OmAvailableModel::updateTNameRow($tName, $set, $where);
//                    echo "{$spu} spuArchive update category and purchaseId success! {$value['goods_category']}  {$pcGoodsArr['purchaseId']} \n";
//                    //continue;
//                }
//
//                $dataSpuArchive = array();
//                $dataSpuArchive['spu'] = $spu;
//                $dataSpuArchive['categoryPath'] = $value['goods_category'];
//                $dataSpuArchive['spuName'] = $value['goods_name'];
//                $dataSpuArchive['spuPurchasePrice'] = $value['goods_cost'];
//                $dataSpuArchive['spuLowestPrice'] = $value['goods_cost'];
//                $dataSpuArchive['spuCalWeight'] = $value['goods_weight'];
//                $dataSpuArchive['isPacking'] = $value['ispacking']==0?1:2;
//                $dataSpuArchive['spuNote'] = $value['goods_note'];
//                $dataSpuArchive['spuSort'] = $value['mainsku'];
//                $dataSpuArchive['purchaseId'] = $purchaseId;
//                $dataSpuArchive['spuCreatedTime'] = $now;
//                $dataSpuArchive['spuStatus'] = $value['isuse']==1?2:1;//上、下线状态
//                $dataSpuArchive['auditStatus'] = 2;
//                $dataSpuArchive['referMonthSales'] = 100;
//                $dataSpuArchive['lowestUrl'] = 'taobao.com';
//                $dataSpuArchive['bidUrl'] = 'taobao.com';
//                if(!$countSpuArchive){
//                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuArchive);
//                    echo "{$spu} spuArchive insert success\n";
//                }
//
//            }
        //}else{
    //        echo "{$pcGoodsArr['sku']} insert fail!\n";
    //    }
    }
}




?>
