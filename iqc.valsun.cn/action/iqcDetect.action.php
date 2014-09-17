<?php
/**
*类名：IQC检测
*功能：处理产品检测过程
*作者：hws
*
*/
class IqcDetectAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取sku信息
	function  act_getSkuInfo(){
		$sku   = $_POST['sku'];
		$where = "where sku='$sku' and sellerId=0 and getUserId='{$_SESSION['userId']}' and detectStatus=1 order by getTime asc limit 1";
		$list  = WhStandardModel::getNowWhList("*",$where);
		if($list){
			$info = array();
			$info['info'] = " 领货记录：{$_SESSION['userName']} 于<br> ".date("Y-m-d H:i", $list[0]['getTime'])." 领货成功<br>
					料号信息：总共---{$list[0]['num']},<br>
					名字要获取!";
					//(料：{$sku},库存：要获取 个, 仓：要获取,采购：要获取)!";
			$info['num'] = $list[0]['num'];
			$info['id']  = $list[0]['id'];
			return $info;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "未找到该料号[{$sku}]对于的{$_SESSION['userName']}领取记录!";
			return false;
		}
	}
	
	//获取检测类别信息
	function  act_getTypeInfo(){
		$cate  = $_POST['cate'];
		$num   = $_POST['num'];
		$sku   = $_POST['sku'];
		$where = "where sampleTypeId='$cate' and minimumLimit<='$num' and maximumLimit>='$num' limit 1";
		$list  = DetectStandardModel::getNowStandardList("*",$where);
		if($list){
			$list[0]['sku'] = get_sku_imgName($sku);
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "未找到该类别的检测标准!";
			return false;
		}
	}
	
	//提交检测
	function  act_subcheck(){
		$data		  = array();
		$id  	      = $_POST['id'];
		$num  	      = $_POST['num'];
		$sku  		  = $_POST['sku'];
		$check_num    = $_POST['check_num'];
		$rejects_num  = $_POST['rejects_num'];
		$bad_reason   = post_check($_POST['bad_reason']);
		
		if(!empty($rejects_num)){
			$set = "SET infoId='$id',sku='$sku',defectiveNum='$rejects_num',note='$bad_reason' ";
			$res = DefectiveProductsModel::addDefectiveProducts($set);
			if($res){
				$data = array(
					'detectorId' 	  => $_SESSION['userId'],
					'detectStartTime' => time(),
					'detectStatus'    => 3,
					'typeId'   		  => 1,
					'ichibanNum'   	  => $num-$rejects_num 
				);
				if(WhStandardModel::update($data,"and id='$id'")){
					self::$errMsg  = "提交成功，请检测下一料号";
					return true;
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}else{
			$data = array(
				'detectorId' 	  => $_SESSION['userId'],
				'detectStartTime' => time(),
				'detectStatus'    => 3,
				'typeId'   		  => 1,
				'ichibanNum'   	  => $num 
			);
			if(WhStandardModel::update($data,"and id='$id'")){
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}
	}
	
	//全部待定
	function  act_allDetermined(){
		$data		  = array();
		$id  	      = $_POST['id'];
		$num  	      = $_POST['num'];
		$sku  		  = $_POST['sku'];
		$wait_reason  = post_check($_POST['wait_reason']);

		$set = "SET infoId='$id',sku='$sku',pendingNum='$num',note='$wait_reason' ";
		$res = PendingProductsModel::addPendingProducts($set);
		if($res){
			$data = array(
				'detectorId' 	  => $_SESSION['userId'],
				'detectStartTime' => time(),
				'detectStatus'    => 4
			);
			if(WhStandardModel::update($data,"and id='$id'")){
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}else{
			self::$errCode = "003";
			self::$errMsg  = "提交失败，请重试";
			return false;
		}
		
	}
	
	//退回处理、库存不良品处理
	function  act_otherCheck(){
		$data		  = array();
		$typeid  	  = $_POST['typeid'];
		$id  	      = $_POST['id'];
		$num  	      = $_POST['num'];
		$sku  		  = $_POST['sku'];
		$check_num    = $_POST['check_num'];
		$rejects_num  = $_POST['rejects_num'];
		$bad_reason   = post_check($_POST['bad_reason']);
		
		if(!empty($rejects_num)){
			$set = "SET infoId='$id',sku='$sku',defectiveNum='$rejects_num',note='$bad_reason' ";
			$res = DefectiveProductsModel::addDefectiveProducts($set);
			if($res){
				$data = array(
					'detectorId' 	  => $_SESSION['userId'],
					'detectStartTime' => time(),
					'detectStatus'    => 3,
					'typeId'   		  => $typeid,
					'ichibanNum'   	  => $num-$rejects_num 
				);
				if(WhStandardModel::update($data,"and id='$id'")){
					self::$errMsg  = "提交成功，请检测下一料号";
					return true;
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}else{
			$data = array(
				'detectorId' 	  => $_SESSION['userId'],
				'detectStartTime' => time(),
				'detectStatus'    => 3,
				'typeId'   		  => $typeid,
				'ichibanNum'   	  => $num 
			);
			if(WhStandardModel::update($data,"and id='$id'")){
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}
	}
	
	
}


?>