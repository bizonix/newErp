<?php
require_once "/data/web/pc.valsun.cn/framework.php";
require_once "/data/web/pc.valsun.cn/lib/php-export-data.class.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟
        error_reporting(E_ALL);
        $startTime = date('Y-m-d H:i:s');//开始时间
        echo "程序开始运行时间：$startTime \n";
        $tName = 'pc_auto_create_spu';
        $select = 'spu';
        $where = "WHERE is_delete=0 AND isSingSpu=1";
        $autoCreateSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $excel = new ExportDataExcel('file');
    	$excel->filename = "/data/web/pc.valsun.cn/html/excel/spuSalersInfo".".xls"; 
    	$excel->initialize();
        $tableHeader = array (
                            'SPU',
                            'SKU',
                            '真实料号信息',
                            '类别',
                            '状态',
                            '采购',
                            '组合人',
                            '网页制作人',
                            'ebay销售',
                            '接手时间',
                            'SMT销售',
                            '接手时间',
                            'AM销售',
                            '接手时间',
                            '海外仓销售',
                            '接手时间'                         
                            );
        $excel->addRow($tableHeader);
        //$totalRows= array();
        $i = 1;
        foreach($autoCreateSpuList as $value1){
            $spu = $value1['spu'];
            $tName = 'pc_spu_saler_single';
            $select = 'platformId,salerId,isHandsOn,addTime';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $singleSpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
            $tName = 'pc_spu_web_maker';
            $select = 'webMakerId,isAgree';
            $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
            $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
            $excelOutWebMaker = '';
            if(!empty($spuWebMakerList) && isset($spuWebMakerList[0]['isAgree']) && $spuWebMakerList[0]['isAgree'] == 2){
                $excelOutWebMaker = getPersonNameById($spuWebMakerList[0]['webMakerId']);//对应网页制作人
            }            
            $tName = 'pc_goods';
            $select = 'sku,goodsCategory,goodsStatus,purchaseId';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($skuList as $k=>$value2){
                $row = array();
                $excelOutSpu = $k == 0?$spu:'';//输出在表中的spu
                $excelOutSku = $value2['sku'];//SKU
                $excelOutCategoryName = getAllCateNameByPath($value2['goodsCategory']);
                $excelOutStatusName = '';
                if($value2['goodsStatus'] == 1){
                    $excelOutStatusName = '在线';
                }elseif($value2['goodsStatus'] == 2){
                    $excelOutStatusName = '停售';
                }elseif($value2['goodsStatus'] == 3){
                    $excelOutStatusName = '暂时停售';
                }elseif($value2['goodsStatus'] == 51){
                    $excelOutStatusName = 'PK';
                }
                $excelOutPurchase = getPersonNameById($value2['purchaseId']);//对应采购
                $ebaySaler = '';//ebay销售
                $ebayHanderTime = '';//ebay接手时间
                $aliexpressSaler = '';//速卖通销售
                $aliexpressHanderTime = '';//速卖通接手时间
                $amazonSaler = '';//亚马逊销售
                $amazonHanderTime = '';//亚马逊接手时间
                $overseaSaler = '';//海外仓销售
                $overseaHanderTime = '';//海外仓接手时间
                foreach($singleSpuSalerList as $value3){
                    if($value3['platformId'] == 1){//ebay平台
                        $ebaySaler = getPersonNameById($value3['salerId']);
                        if($value3['isHandsOn'] == 1 && !empty($value3['addTime'])){
                            $ebayHanderTime = date('Y-m-d', $value3['addTime']);
                        }
                    }elseif($value3['platformId'] == 2){//aliexpress平台
                        $aliexpressSaler = getPersonNameById($value3['salerId']);
                        if($value3['isHandsOn'] == 1 && !empty($value3['addTime'])){
                            $aliexpressHanderTime = date('Y-m-d', $value3['addTime']);
                        }
                    }elseif($value3['platformId'] == 11){//Amazon平台
                        $amazonSaler = getPersonNameById($value3['salerId']);
                        if($value3['isHandsOn'] == 1 && !empty($value3['addTime'])){
                            $amazonHanderTime = date('Y-m-d', $value3['addTime']);
                        }
                    }elseif($value3['platformId'] == 14){//海外仓平台
                        $overseaSaler = getPersonNameById($value3['salerId']);
                        if($value3['isHandsOn'] == 1 && !empty($value3['addTime'])){
                            $overseaHanderTime = date('Y-m-d', $value3['addTime']);
                        }
                    }
                }            
                $row[] = intval($excelOutSpu)>0?intval($excelOutSpu):$excelOutSpu;
                $row[] = intval($excelOutSku)>0?intval($excelOutSku):$excelOutSku;
                $row[] = '';
                $row[] = $excelOutCategoryName;//add by 20140512,类别
                $row[] = $excelOutStatusName;//add by 20140512,状态
                $row[] = $excelOutPurchase;
                $row[] = '';
                $row[] = $excelOutWebMaker;
                $row[] = $ebaySaler;
                $row[] = $ebayHanderTime;
                $row[] = $aliexpressSaler;
                $row[] = $aliexpressHanderTime;
                $row[] = $amazonSaler;
                $row[] = $amazonHanderTime;
                $row[] = $overseaSaler;
                $row[] = $overseaHanderTime;
                //$totalRows[] = $row;
                $excel->addRow($row);
                echo "记录数：$i \n";
                $i++;
            }
        }
        //导出组合料号的
        $tName = 'pc_auto_create_spu';
        $select = 'spu';
        $where = "WHERE is_delete=0 AND isSingSpu=2";
        $autoCreateSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($autoCreateSpuList as $value1){
            $spu = $value1['spu'];
            $tName = 'pc_spu_saler_combine';
            $select = 'platformId,salerId,isHandsOn,addTime';
            $where = "WHERE is_delete=0 AND spu='$spu'";
            $combineSpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
            $tName = 'pc_spu_web_maker';
            $select = 'webMakerId,isAgree';
            $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
            $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
            $excelOutWebMaker = '';
            if(!empty($spuWebMakerList) && isset($spuWebMakerList[0]['isAgree']) && $spuWebMakerList[0]['isAgree'] == 2){
                $excelOutWebMaker = getPersonNameById($spuWebMakerList[0]['webMakerId']);//对应网页制作人
            }
            $tName = 'pc_goods_combine';
            $select = 'combineSku,combineUserId';
            $where = "WHERE is_delete=0 AND combineSpu='$spu'";
            $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
            foreach($combineSkuList as $k=>$value2){
                $row = array();
                $excelOutSpu = $k == 0?$spu:'';//输出在表中的spu
                $excelOutCombineSku = $value2['combineSku'];//SKU
                $trueSkuInfoStr = '';//真实料号信息字符串
                $tName = 'pc_sku_combine_relation';
                $select = 'sku,count';
                $where = "WHERE combineSku='$excelOutCombineSku'";
                $combineSkuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
                $tmpArr = array();
                foreach($combineSkuRelationList as $value3){                    
                    $tmpStr = $value3['sku'].'*'.$value3['count'];
                    $tmpArr[] = $tmpStr;
                }
                if(!empty($tmpArr)){
                    $trueSkuInfoStr = implode(',', $tmpArr);
                }
                $excelOutCombineUser = getPersonNameById($value2['combineUserId']);//对应组合人
                $ebaySaler = '';//ebay销售
                $ebayHanderTime = '';//ebay接手时间
                $aliexpressSaler = '';//速卖通销售
                $aliexpressHanderTime = '';//速卖通接手时间
                $amazonSaler = '';//亚马逊销售
                $amazonHanderTime = '';//亚马逊接手时间
                $overseaSaler = '';//海外仓销售
                $overseaHanderTime = '';//海外仓接手时间
                foreach($combineSpuSalerList as $value4){
                    if($value4['platformId'] == 1){//ebay平台
                        $ebaySaler = getPersonNameById($value4['salerId']);
                        if($value4['isHandsOn'] == 1 && !empty($value4['addTime'])){
                            $ebayHanderTime = date('Y-m-d', $value4['addTime']);
                        }
                    }elseif($value4['platformId'] == 2){//aliexpress平台
                        $aliexpressSaler = getPersonNameById($value4['salerId']);
                        if($value4['isHandsOn'] == 1 && !empty($value4['addTime'])){
                            $aliexpressHanderTime = date('Y-m-d', $value4['addTime']);
                        }
                    }elseif($value4['platformId'] == 11){//Amazon平台
                        $amazonSaler = getPersonNameById($value4['salerId']);
                        if($value4['isHandsOn'] == 1 && !empty($value4['addTime'])){
                            $amazonHanderTime = date('Y-m-d', $value4['addTime']);
                        }
                    }elseif($value4['platformId'] == 14){//Amazon平台
                        $overseaSaler = getPersonNameById($value4['salerId']);
                        if($value4['isHandsOn'] == 1 && !empty($value4['addTime'])){
                            $overseaHanderTime = date('Y-m-d', $value4['addTime']);
                        }
                    }
                }
                $row[] = $excelOutSpu;
                $row[] = $excelOutCombineSku;
                $row[] = $trueSkuInfoStr;
                $row[] = '';//类别
                $row[] = '';//状态
                $row[] = '';//采购
                $row[] = $excelOutCombineUser;
                $row[] = $excelOutWebMaker;
                $row[] = $ebaySaler;
                $row[] = $ebayHanderTime;
                $row[] = $aliexpressSaler;
                $row[] = $aliexpressHanderTime;
                $row[] = $amazonSaler;
                $row[] = $amazonHanderTime;
                $row[] = $overseaSaler;
                $row[] = $overseaHanderTime;
                //$totalRows[] = $row;
                $excel->addRow($row);
                echo "记录数：$i \n";
                $i++;
            }
        }
        //foreach($totalRows as $row){
//            $excel->addRow($row);
//        } 
        $excel->finalize();
        $endTime = date('Y-m-d H:i:s');//开始时间
        echo "程序开始运行时间：$startTime \n";
        echo "程序结束时间：$endTime \n";
        echo "一共生成了 $i 条记录 \n";
        exit(); 


?>
