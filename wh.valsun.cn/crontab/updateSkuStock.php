<?php
/**
 * 同步新旧系统库存
 */
error_reporting(-1);
session_start();
set_time_limit(0);
ini_set('memory_limit','256M');
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
//include "../framework.php";

if(!class_exists('Core')){  //Core类不存在，重新载入文件
    $web_path   =   str_replace('crontab', '', __DIR__); //获取framework.php所在路径
    include_once $web_path.'framework.php';
}

Core::getInstance();
global $dbConn;
mysql_query('SET NAMES UTF8');

$sql    =   'select id, sku from pc_goods where is_delete = 0';
//$sql    =   'select id, sku from pc_goods where is_delete = 0 and sku="18558_P_140" order by id desc';
$sql    =   $dbConn->query($sql);
$res    =   $dbConn->fetch_array_all($sql);  //获取新仓库所有料号
$log_file   =   'update_sku_stock/'.date('Y-m-d').'.txt';
//print_r($res);exit;
foreach($res as $val){
    $sku            =   $val['sku'];
    $pId            =   $val['id'];
    $date           =   date('Y-m-d H:i:s');
    
    $erpSkuInfo     =   CommonModel::getErpSkuInfo($sku);  //获取ERP仓位库存信息
    //print_r($erpSkuInfo);exit;
    if($erpSkuInfo['errocode'] != 200){ //获取信息失败
        $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s,返回值：%s \r\n", $sku, $date, '获取旧ERP库存失败',
                                            is_array($erpSkuInfo) ? json_encode($erpSkuInfo) : $erpSkuInfo);
        write_log($log_file, $log_info);
        continue;
    }
    //print_r($erpSkuInfo);exit;
    $sku_location   =   $erpSkuInfo['data']['goods_location'];
    if(!$sku_location){ //ERP仓位不存在
        $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s, 参数:%s, %s \r\n", $sku, $date, '没有仓位',
                                $sku, $sku_location);
        write_log($log_file, $log_info);
        continue;
    }
    $storeId    =   preg_match("/HW|WH/U", $sku_location) ? 2 : 1;   //仓库ID
    $goods_count=   $erpSkuInfo['data']['goods_count']; //A仓仓位库存
    $second_count=  $erpSkuInfo['data']['second_count']; //B仓仓位库存
    //$sku_num    =   intval($erpSkuInfo['data']['second_count'] + $erpSkuInfo['data']['goods_count']); //ERP仓位库存
    //print_r($sku_num);exit;
    
    /** 无料号对应仓位的关系时更新关系表***/
    $positionId     =   whShelfModel::selectPosition("where pName = '{$sku_location}'");
    //print_r($positionId);exit;
    if(empty($positionId)){
        $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s,仓位：%s \r\n", $sku, $date, '系统没有该仓位',
                                            $sku_location);
        write_log($log_file, $log_info);
        $tname      =   'wh_position_distribution';
        
        $set        =   "set pName='$sku_location', storeId =$storeId";
        $info       =   OmAvailableModel::insertRow($tname, $set);
        if($info){
            $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s,仓位：%s \r\n", $sku, $date, '插入仓位成功', $sku_location);
            write_log($log_file, $log_info);
            $positionId =   whShelfModel::selectPosition("where pName = '{$sku_location}'");
        }else{
            $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s,仓位：%s \r\n", $sku, $date, '插入仓位失败', $sku_location);
            write_log($log_file, $log_info);
            continue;
        }
    }
    
    /** 更新新系统料号仓位关系**/
    $info       =   updateNewPostion($sku, $sku_location);  
    $msg        =   $info ? '更新仓位成功!' : '更新仓位失败！';
    $log_info   =   sprintf("料号：%s, 时间：%s,信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $msg,
                                    $info, $sku, $sku_location);
    write_log($log_file, $log_info);
    
    
    $positionId     =   $positionId[0]['id'];
    TransactionBaseModel :: begin();
    $relation       =   whShelfModel::selectRelation(" where pId={$pId} and positionId={$positionId}");
    
    /** 更新仓位库存**/
    $temp       =   whShelfModel::selectRelationShip($pId, 8290);
    $temp_nums  =   empty($temp) ? 0 : $temp[0]['nums']; //临时仓位表库存
    
    /** 更新A仓仓位库存**/
    $a_diff     =   $goods_count - $temp_nums;
    if( $a_diff < 0 && $temp_nums){ //如果临时仓位有库存且库存大于获取的A仓库存
        whShelfModel::updateProductPositionRelation_new(array('nums'=>0), array('pId'=>$pId, 'storeId'=>1)); //将A仓库存变为0
        whShelfModel::updateProductPositionRelation_new(array('nums'=>$goods_count), array('pId'=>$pId, 'positionId'=>8290)); //A仓临时仓位变为获取的A仓库存 
    }else if($a_diff > 0 && $temp_nums){
        whShelfModel::updateProductPositionRelation_new(array('nums'=>$a_diff), array('pId'=>$pId, 'storeId'=>1)); //将A仓库存变为差值
    }else if(!$temp_nums && $goods_count > 0){
        whShelfModel::updateProductPositionRelation_new(array('nums'=>$goods_count), array('pId'=>$pId, 'storeId'=>1)); //将A仓库存变为差值
    }
    
    /** 更新B仓仓位库存**/
    if($second_count){
        whShelfModel::updateProductPositionRelation_new(array('nums'=>$second_count), array('pId'=>$pId, 'storeId'=>2)); //将A仓库存变为差值
    }
    
    //$where           = " where pId={$pId} and positionId={$positionId}";
//    $set             = " set nums={$sku_num}, storeId=$storeId";
//    $update_position = OmAvailableModel::updateTNameRow('wh_product_position_relation', $set, $where);
//	if($update_position === FALSE){
//		    $errCode = 410;
//		    $errMsg = "更新仓位库存失败！";
//            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
//                                    $update_position, $sku_num, $where);
//            write_log($log_file, $log_info);
//			TransactionBaseModel :: rollback();
//			continue;
//	}
//    write_log($log_file, date('Y-m-d H:i:s').'更新仓位库存成功！'."{$sku}\r\n");
    
	
	//更新仓位使用状态
	$update_position = OmAvailableModel::updateTNameRow("wh_position_distribution","set is_enable=1","where id=$positionId");
	if($update_position===false){
		$errCode = 409;
		$errMsg = "更新仓位使用状态失败！";
        $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数: %s \r\n", $sku, $date, $errMsg,
                                    $update_position, $positionId);
        write_log($log_file, $log_info);
		TransactionBaseModel :: rollback();
		continue;
	}
    write_log($log_file, date('Y-m-d H:i:s').'更新仓位使用状态成功！'."{$sku}\r\n");
	
    
    /** 更新A仓总库存**/
    //if($goods_count){
        $actualStock = whShelfModel::selectSkuNums($sku, 1);
    	if(!empty($actualStock)){
    		$where =     "where sku='{$sku}' and storeId = 1";
            $sql   =    "update wh_sku_location set actualStock={$goods_count}, storeId=1 ".$where;
            //echo $sql;exit;
    		$info  =  $dbConn->query($sql);
    		if(!$info){
    			$errCode = 412;
    			$errMsg = "更新A仓总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
                                            $sql, $goods_count, $where);
                write_log($log_file, $log_info);               
    			TransactionBaseModel :: rollback();
    			continue;
    		}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
    	}else{
    		$info = whShelfModel::insertStore($sku,$goods_count, 1);
    		if(!$info){
    			$errCode = 412;
    			$errMsg = "插入总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
                                            $info, $sku, $goods_count);
                write_log($log_file, $log_info);
    			TransactionBaseModel :: rollback();
    			continue;
    		}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
    	}
    //}
	
    
    /** 更新B仓总库存**/
    //if($second_count){
        $actualStock = whShelfModel::selectSkuNums($sku, 2);
    	if(!empty($actualStock)){
    		$where =     "where sku='{$sku}' and storeId = 2";
            $sql   =    "update wh_sku_location set actualStock={$second_count}, storeId=2 ".$where;
            //echo $sql;exit;
    		$info  =  $dbConn->query($sql);
    		if(!$info){
    			$errCode = 412;
    			$errMsg = "更新B仓总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
                                            $sql, $second_count, $where);
                write_log($log_file, $log_info);               
    			TransactionBaseModel :: rollback();
    			continue;
    		}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
    	}else{
    		$info = whShelfModel::insertStore($sku,$second_count, 2);
    		if(!$info){
    			$errCode = 412;
    			$errMsg = "插入B仓总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, $errMsg,
                                            $info, $sku, $second_count);
                write_log($log_file, $log_info);
    			TransactionBaseModel :: rollback();
    			continue;
    		}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
    	}
    //}
    TransactionBaseModel :: commit(); 
}

function updateNewPostion($sku,$location){
    global $dbConn;
	$goodsinfos = OmAvailableModel::getTNameList("pc_goods","sku,id","where sku='{$sku}'");
	$pId = $goodsinfos[0]['id']; //产品id
	if($location){
		$sql1   = "select * from `wh_position_distribution` where pName = '{$location}'";
		$query = $dbConn->query($sql1);
		$wh_position_distribution =$dbConn->fetch_array($query);
		if($wh_position_distribution){
			$positionId		= $wh_position_distribution['id'];
			//$postionId = OmAvailableModel::insertRow2("wh_position_distribution","set pName='$location',x_alixs=0,y_alixs=0,z_alixs=0,floor=0,is_enable=0,type=1,storeId=2");
			if($positionId){
				$positioninfos = OmAvailableModel::getTNameList("wh_product_position_relation","id","where pId='$pId' and storeId = '{$wh_position_distribution['storeId']}'");	
				if(!empty($positioninfos)){
					if($data = OmAvailableModel::updateTNameRow("wh_product_position_relation","set positionId='$positionId', storeId={$wh_position_distribution['storeId']}", " where pId='$pId' and storeId = '{$wh_position_distribution['storeId']}'")){
						//echo "update <".$sku."> ===(".$positionId.") success\n";
						return true;
					}else{
						return false;
					}
				}else{
					//$infos = OmAvailableModel::getTNameList("wh_sku_location","sku,actualStock","where sku='{$sku}'");
//					$num = $info['actualStock'];
					$data = OmAvailableModel::insertRow("wh_product_position_relation","set pId='$pId',positionId='$positionId', storeId={$wh_position_distribution['storeId']}");
				}
			}else{
				return false;	
			}
		}else{
			return false;	
		}
	}	
}

?>