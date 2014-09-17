<?php
require_once "/data/web/erpNew/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟

//这个是先将关联表的全部删除后再同步的脚本

$ebayGoodsList = array();
//$ebayGoodsList = OmAvailableModel::newData2ErpInterfOpen('pc.erp.getEbayGoodsCrontab',array('all'=>'all'),'gw88',false);
$tName = 'ebay_goods';
$select = '*';
//$countGoods = OmAvailableModel::getTNameCount($tName, '');
$countGoods = UserCacheModel::getOpenSysApi('pc.erp.getGoodsCount',array('all'=>'all'),'gw88');
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
echo "循环的总次数是 $countFor \n";

$tName = 'pc_goods';
$where = "WHERE 1=1";
OmAvailableModel::deleteTNameRow($tName, $where);//清空goods表
echo "清空 pc_goods表 成功！\n";

$tName = 'pc_goods_partner_relation';
$where = "WHERE 1=1";
OmAvailableModel::deleteTNameRow($tName, $where);
echo "清空 pc_goods_partner_relation 成功!\n";

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
        if(empty($pcGoodsArr['goodsName']) || $pcGoodsArr['goodsName'] == '无'){
            $pcGoodsArr['is_delete'] = 1;
        }
        $pcGoodsArr['sku'] = $value['goods_sn'];
        //$tName = 'pc_goods';
//        $where = "WHERE sku='{$value['goods_sn']}'";
//        OmAvailableModel::deleteTNameRow($tName, $where);
//        echo "删除 SKU:{$value['goods_sn']} success \n";
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
        }else{
            $pcGoodsArr['goodsStatus'] = 2;//其他认为是停售
        }
        if(!empty($value['cguser'])){
            echo "purchase is {$value['cguser']}\n";
            $queryConditions = array('userName' =>$value['cguser']);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $purchaseId = $userInfo[0]['userId'];
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
        if(!empty($value['ow_inCharge_user'])){
            echo "OverSeaSkuCharger is {$value['ow_inCharge_user']}\n";
            $queryConditions = array('userName' =>$value['ow_inCharge_user']);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $OverSeaSkuCharger = $userInfo[0]['userId'];
            echo "OverSeaSkuCharger is '$OverSeaSkuCharger'\n";
            $pcGoodsArr['OverSeaSkuCharger'] = $OverSeaSkuCharger;
        }
        $tName = 'pc_goods';
        $set = 'SET '.array2sql($pcGoodsArr);
        //if($countIsExist == 0){
//            $affectRow = OmAvailableModel::addTNameRow($tName, $set);
//            echo "{$pcGoodsArr['sku']} insert success!\n";
//        }
//        else{
//            if(!empty($pcGoodsArr['goodsCategory']) && !empty($pcGoodsArr['purchaseId'])){
//                $set = "SET goodsCategory='{$pcGoodsArr['goodsCategory']}',purchaseId='{$pcGoodsArr['purchaseId']}',goodsStatus='{$pcGoodsArr['goodsStatus']}',isNew='{$pcGoodsArr['isNew']}'";
//            	$where = "WHERE sku='{$pcGoodsArr['sku']}'";
//            	OmAvailableModel::updateTNameRow($tName, $set, $where);
//            	echo "{$pcGoodsArr['sku']} update category,purchaseId,goodsStatus  {$pcGoodsArr['goodsCategory']},{$pcGoodsArr['purchaseId']},{$pcGoodsArr['goodsStatus']} success!\n";
//            }else{
//                echo "{$pcGoodsArr['sku']} not update category,purchaseId,because its empty!\n";
//            }
//
//        }
        OmAvailableModel::replaceTNameRow2arr($tName, $pcGoodsArr);


        //if($affectRow){

            $partnerId = $value['factory']?$value['factory']:0;
            $tName = 'pc_goods_partner_relation';
            $set = "SET sku='{$pcGoodsArr['sku']}',partnerId='$partnerId'";
            OmAvailableModel::addTNameRow($tName, $set);//插入sku，供应商关系表
            echo "{$pcGoodsArr['sku']} $partnerId insert relation success!\n";

            if(!empty($value['goods_hgbm'])){//海关编码不为空时，插入sku，海关编码关系表
                $tName = 'pc_sku_hscode';
                $where = "WHERE sku='{$value['goods_sn']}' AND hsCode='{$value['goods_hgbm']}'";
                $countHscode = OmAvailableModel::getTNameCount($tName, $where);
                if($countHscode){
                    echo "hscode {$value['goods_sn']} {$value['goods_hgbm']} had exist\n";
                    //continue;
                }
                $set = "SET sku='{$value['goods_sn']}',hsCode='{$value['goods_hgbm']}',createdTime='$now'";
                if(!$countHscode){
                    OmAvailableModel::addTNameRow($tName, $set);
                    echo "hscode insert success!\n";
                }

            }
            //在新系统中插入对应的自动生成SPU记录，和相应映射的SPU档案
            $isMinCategory = true;
            $tName = 'pc_goods_category';
    		$where = "WHERE path like'{$value['goods_category']}-%' and is_delete=0";
    		$count = OmAvailableModel :: getTNameCount($tName, $where);
    		if ($count || empty($value['goods_category'])) {
    			$isMinCategory = false;//不是最小分类,或者无分类
    		}
            if(!empty($spu) && !empty($pcGoodsArr['purchaseId']) && $isMinCategory){//spu不为空，并且是最小分类
                //添加自动生成SPU记录
                $tName = 'pc_auto_create_spu';
                $where = "WHERE spu='{$spu}'";
                $countAutoCreSpu = OmAvailableModel::getTNameCount($tName, $where);
                if($countAutoCreSpu){
                    echo "{$spu} autoCreateSpu has exist!\n";
                    //continue;
                }
                if(preg_match("/^[A-Z]{2}[0-9]{6}$/",$spu)){
                    $sort = intval(substr($spu, 2));
                }else{
                    $sort = 0;
                }
                $set = "SET spu='{$spu}',purchaseId='$purchaseId',createdTime='$now',sort='$sort',status=2";
                if(!$countAutoCreSpu){
                    OmAvailableModel::addTNameRow($tName, $set);
                    echo "{$spu} autoCreateSpu insert success\n";
                }

                //添加SPU档案
                $tName = 'pc_spu_archive';
                $where = "WHERE spu='{$spu}'";
                $countSpuArchive = OmAvailableModel::getTNameCount($tName, $where);
                if($countSpuArchive){
                    echo "{$spu} spuArchive has exist!\n";
                    $set = "SET categoryPath='{$value['goods_category']}',purchaseId='{$pcGoodsArr['purchaseId']}'";
                    OmAvailableModel::updateTNameRow($tName, $set, $where);
                    echo "{$spu} spuArchive update category and purchaseId success! {$value['goods_category']}  {$pcGoodsArr['purchaseId']} \n";
                    //continue;
                }

                $dataSpuArchive = array();
                $dataSpuArchive['spu'] = $spu;
                $dataSpuArchive['categoryPath'] = $value['goods_category'];
                $dataSpuArchive['spuName'] = $value['goods_name'];
                $dataSpuArchive['spuPurchasePrice'] = $value['goods_cost'];
                $dataSpuArchive['spuLowestPrice'] = $value['goods_cost'];
                $dataSpuArchive['spuCalWeight'] = $value['goods_weight'];
                $dataSpuArchive['isPacking'] = $value['ispacking']==0?1:2;
                $dataSpuArchive['spuNote'] = $value['goods_note'];
                $dataSpuArchive['spuSort'] = $value['mainsku'];
                $dataSpuArchive['purchaseId'] = $purchaseId;
                $dataSpuArchive['spuCreatedTime'] = $now;
                $dataSpuArchive['spuStatus'] = $value['isuse']==1?2:1;//上、下线状态
                $dataSpuArchive['auditStatus'] = 2;
                $dataSpuArchive['referMonthSales'] = 100;
                $dataSpuArchive['lowestUrl'] = 'taobao.com';
                $dataSpuArchive['bidUrl'] = 'taobao.com';
                if(!$countSpuArchive){
                    OmAvailableModel::addTNameRow2arr($tName, $dataSpuArchive);
                    echo "{$spu} spuArchive insert success\n";
                }

            }
        //}else{
    //        echo "{$pcGoodsArr['sku']} insert fail!\n";
    //    }
    }
}




?>
