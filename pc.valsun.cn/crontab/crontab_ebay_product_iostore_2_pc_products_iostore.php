<?php
require_once "/data/web/erpNew/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟


    $ioStoreList = UserCacheModel::getOpenSysApi('pc.erp.getEbayProductIostoreCrontab',array('all'=>'1111'),'gw88');
    $countStore = count($ioStoreList);
    echo "一共有 $countStore 条表头记录\n";
    //print_r($skuList);
//    exit; 
    $now = time();
    foreach($ioStoreList as $value){
        $iostoreArr = array();
        $iostoreArr['id'] = $value['id'];//id
        $iostoreArr['ordersn'] = $value['io_ordersn'];//单据编码
        //$iostoreArr['iostoreStatus'] = 2;//1为草稿，2为已发送至仓库，为2
        $iostoreArr['iostoreTypeId'] = $value['type'] == 1?1:2;//$value['type']=1为出库单（领料单），=0为入库单（退料单）
        $iostoreArr['useTypeId'] = $value['io_type'] == 127 || $value['io_type'] == 128?1:2;//127,128为制作，129,130为修改
        
        $addUser = $value['io_user'];//添加人
        $iostoreArr['createdTime'] = $value['io_addtime'];//添加时间
        
        $auditor = $value['audituser'];//审核人
        $iostoreArr['auditTime'] = $value['io_audittime'];//审核时间
        
        $comfirmUser = $value['confirmer'];//确认人

        if(!empty($addUser)){
            echo "添加人 is $addUser\n";
            $queryConditions = array('userName' =>$addUser);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "添加人Id is '$personId'\n";
            $iostoreArr['addUserId'] = $personId;
        }
        if(!empty($auditor)){
            echo "审核人 is $auditor\n";
            $queryConditions = array('userName' =>$auditor);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "审核人Id is '$personId'\n";
            $iostoreArr['auditorId'] = $personId;
            $iostoreArr['isAudit'] = 2;//审核通过
        }
        if(!empty($comfirmUser)){
            echo "确认人 is $comfirmUser\n";
            $queryConditions = array('userName' =>$comfirmUser);
            $queryConditions = json_encode($queryConditions);
            $userInfo = Auth::getApiGlobalUser($queryConditions);
            $userInfo = json_decode($userInfo,true);
            //print_r($userInfo);
            //echo "\n";
            $personId = $userInfo[0]['userId'];
            echo "确认人Id is '$personId'\n";
            $iostoreArr['comfirmUserId'] = $personId;
            $iostoreArr['isComfirm'] = 2;//已确认
        }
        

        $tName = 'pc_products_iostore';
        $ioStoreId = OmAvailableModel::replaceTNameRow2arr($tName, $iostoreArr);
        if(!$ioStoreId){
            continue;
        }
        $ioStoreDetailList = UserCacheModel::getOpenSysApi('pc.erp.getEbayProductIostoreDetailCrontab',array('io_ordersn'=>$value['io_ordersn']),'gw88');
        //print_r($ioStoreDetailList);
//        exit;
        $countStoreDetail = count($ioStoreDetailList);       
        echo "{$value['io_ordersn']} 一共有 $countStoreDetail 条详细记录\n";
        
        foreach($ioStoreDetailList as $valueDetail){           
            $iostoreDetailArr = array();
            $iostoreDetailArr['id'] = $valueDetail['id'];
            $iostoreDetailArr['iostoreId'] = $ioStoreId;
            $iostoreDetailArr['iostoreTypeId'] = $iostoreArr['iostoreTypeId'];
            $iostoreDetailArr['useTypeId'] = $iostoreArr['useTypeId'];
            $iostoreDetailArr['sku'] = $valueDetail['goods_sn'];
            if(intval($iostoreArr['addUserId']) > 0){
                $iostoreDetailArr['addUserId'] = $iostoreArr['addUserId'];
                $iostoreDetailArr['addTime'] = $iostoreArr['createdTime'];
            }
            $iostoreDetailArr['iostoreStatus'] = 2;
            if(!empty($iostoreArr['isAudit'])){
                $iostoreDetailArr['isAudit'] = $iostoreArr['isAudit'];
            }
            if(!empty($iostoreArr['isComfirm'])){
                $iostoreDetailArr['isComfirm'] = $iostoreArr['isComfirm'];
            }    
            $tName = 'pc_products_iostore_detail';
            OmAvailableModel::replaceTNameRow2arr($tName, $iostoreDetailArr);
        }
    }

?>
