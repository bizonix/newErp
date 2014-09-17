<?php
require_once "/data/web/pc.valsun.cn/framework.php";
Core :: getInstance();
session_start();
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
session_cache_expire(30);//session有效时间为30分钟
		echo date('Y-m-d_H:i:s')."开始运行 \n";
        $page = 1;//标识第几次通过接口取数据，初始值为第一次
        do{
            $skuInfoList = UserCacheModel::getOpenSysApi('wh.getSKUInInfo',array('page'=>$page));//调用idc上的仓库系统接口，返回指定下标及对应记录数
            $skuInfoList = $skuInfoList['data'];
            //print_r($skuInfoList);
//            exit;
            $totalNum = $skuInfoList['totalNum'];//sku记录的总数
            if(intval($totalNum) <= 0){
                echo "$totalNum <= 0 或者不是数字 \n";
                continue;
            }
            $totalPage = ceil($totalNum/200);
            echo "共有 $totalPage 页 \n";
            echo "这是第 $page 页，调用接口取得数据 \n";
            $skuInfo = $skuInfoList['skuInfo'];//具体的sku信息数组
            if(empty($skuInfo) || !is_array($skuInfo)){
                echo "$skuInfo 为空或者不是数组 \n";
                $page++;
                continue;
            }
            foreach($skuInfo as $value){
                $sku = $value['sku'];
                $isHasStock = intval($value['isHasStock']);
                $whId = intval($value['whId']);
                $isHasLocation = $value['isHasLocation'];
                $location = post_check(trim($value['location']));
                $storageTime = intval($value['storageTime']);
                try{
                    BaseModel::begin();
                    $tName = 'pc_goods_whId_location_raletion';
                    $where = "WHERE sku='$sku' AND whId='$whId'";
                    $wlraleCount = OmAvailableModel::getTNameCount($tName, $where);
                    $tmpArr = array();
                    $tmpArr['sku'] = $sku;
                    $tmpArr['isHasStock'] = $isHasStock;
                    $tmpArr['whId'] = $whId;
                    $tmpArr['isHasLocation'] = $isHasLocation;
                    $tmpArr['location'] = $location;
                    $tmpArr['storageTime'] = $storageTime;
                    $tmpArr['updateTime'] = time();
                    if(!$wlraleCount){//如果不存在记录
                        OmAvailableModel::addTNameRow2arr($tName, $tmpArr);
                        echo "添加记录 成功 \n";
                        echo "‘ $sku ’ ‘ $isHasStock ’ ‘ $whId ’ ‘ $isHasLocation ’ ‘ $location ’ ‘ $storageTime ’ \n";
                    }else{//存在记录
                        unset($tmpArr['sku'], $tmpArr['whId']);
                        OmAvailableModel::updateTNameRow2arr($tName, $tmpArr, $where);
                        echo "更新记录 成功 \n";
                        echo "‘ $sku ’ ‘ $isHasStock ’ ‘ $whId ’ ‘ $isHasLocation ’ ‘ $location ’ ‘ $storageTime ’ \n";
                    }
                    BaseModel::commit();
                    BaseModel::autoCommit();
                }catch(Exception $e){//发生错误则进行下次循环
                    BaseModel::rollback();
                    BaseModel::autoCommit();
                    echo "‘ $sku ’ 记录插入/更新失败，数据回滚，进入下次循环\n";
                    continue;
                }
            }
            $page++;
        }while($totalPage >= $page);
		echo date('Y-m-d_H:i:s')."开始结束 \n";

?>
