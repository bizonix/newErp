<?php
/**
*类名：检测标准
*功能：处理检测标准
*作者：hws
*
*/
class DetectStandardAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前标准列表
	function  act_getNowStandardList($select = '*',$where){
		$list =	DetectStandardModel::getNowStandardList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = DetectStandardModel::$errCode;
			self::$errMsg  = DetectStandardModel::$errMsg;
			return false;
		}
	}
	
	//获取检测标准样本标准列表
	function  act_getSampleStandardList($select = '*',$where){
		$list =	SampleStandardModel::getSampleStandardList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = SampleStandardModel::$errCode;
			self::$errMsg  = SampleStandardModel::$errMsg;
			return false;
		}
	}
	
	//增加/修改样本标准
	function  act_sureAdd(){
		$data 		  = array();
		$property_arr = array();
		$id 			  	  = post_check(trim($_POST['standardId']));
		$is_open              = post_check(trim($_POST['isopen']));
		$data['sampleTypeId'] = post_check(trim($_POST['typeId']));
		$data['sName'] 		  = post_check(trim($_POST['sName']));
		$data['minimumLimit'] = post_check(trim($_POST['minimumLimit']));
		$data['maximumLimit'] = post_check(trim($_POST['maximumLimit']));
		$data['sizeCodeId']   = post_check(trim($_POST['codeId']));
		$data['userId']       = $_SESSION['sysUserId'];
		$data['createdTime']  = time();
		if(empty($id)){
			$data['is_open'] = 0;
			$insertid = SampleStandardModel::insertRow($data);
			if($insertid){
				return true;
			}else{
				return false;
			}
		}else{
			$updatedata = SampleStandardModel::update($data,"and id='$id'");
			if($updatedata){
				return true;
			}else{
				return false;
			}
		}		
	}
	
	//开启样本标准
	function  act_openStandard(){
		$old_data = array();
		$new_data = array();
		$sampleTypeId = $_POST['id'];	
		$sName 		  = $_POST['name'];
		$old_data     = array('is_open'=>0);
		//return SampleStandardModel::update($old_data,"and sampleTypeId='$sampleTypeId' and is_open=1");
		if(SampleStandardModel::update($old_data,"and sampleTypeId='$sampleTypeId' and is_open=1")){
			$new_data  = array('is_open'=>1);
			$update_st = SampleStandardModel::update($new_data,"and sampleTypeId='$sampleTypeId' and sName='$sName'");		    
			if($update_st){
				$update_qc_sample_standard_list = DetectStandardModel::updateSampleStandard();
				if($update_qc_sample_standard_list){
					return true;
				}else{
					self::$errCode = "003";
					self::$errMsg  = "更新临时表失败！";
					return false;
				}				
			}else{
				self::$errCode = "003";
				self::$errMsg  = "开启失败！";
				return false;
			}
		}else{
			self::$errCode = "003";
			self::$errMsg  = "";
			return false;
		}
	}
	
	//更新样本标准
	function  act_getnowStandard(){
		$update = DetectStandardModel::updateSampleStandard();
		if($update){
			return true;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "";
			return false;
		}
	}
	
	//获取检测类型下面的标准名称
	function  act_getStandardInfo(){
		$id   = $_POST['id'];
		$list =	SampleStandardModel::getNowStandardList("sName","where sampleTypeId='{$id}' group by sName");
		if($list){
			return $list;
		}else{
			self::$errCode = CategoryModel::$errCode;
			self::$errMsg  = CategoryModel::$errMsg;
			return false;
		}
	}
	
}


?>