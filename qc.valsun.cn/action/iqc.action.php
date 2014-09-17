<?php
/**
*类名：IQC领取、待测
*功能：处理产品检测信息
*作者：hws
*
*/
class IqcAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取当前待测列表
	function  act_getNowWhList($select = '*',$where){
		$list =	WhStandardModel::getNowWhList($select,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return array();
		}
	}
	
	//获取当前待测数量
	function act_getNowWhNum($where){
		//调用model层获取数据
		$list =	WhStandardModel::getNowWhNum($where);
		if($list){
			return $list;
		}else{
			self::$errCode = WhStandardModel::$errCode;
			self::$errMsg  = WhStandardModel::$errMsg;
			return false;
		}
	}
	
	//领取料号
	function  act_getSku(){
		$data   = array();
		
		if(!isset($_SESSION)||empty($_SESSION['sysUserId'])){
			self::$errCode = "001";
			self::$errMsg  = "SESSION 过期了！请重新登录！";
			return false;
		}
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = " and id in(".$id.")";
		$data   = array(
			'getUserId'		=> $_SESSION['sysUserId'],
			'getTime'   	=> time(),
			'detectStatus' 	=> 1
		);
		$list_arr = WhStandardModel::getNowWhList("*"," where 1=1".$where);
		foreach($list_arr as $key=>$value){
			if(!empty($value['getUserId']) || !empty($value['getTime'])){
				$user = userModel::getUsernameById($value['getUserId']);
				self::$errCode = "030";
				self::$errMsg  = "料号{$value['sku']}已被用户{$user}领取!";
				return false;
			}
			if($value['detectStatus'] !=0){
				self::$errCode = "040";
				self::$errMsg  = "料号{$value['sku']}不在待领取状态!";
				return false;
			}
		}
		$list =	WhStandardModel::update($data,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "领取失败，请重试！";
			return false;
		}
	}

	//退回料号
	function  act_returnSku(){
		$data   = array();
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = " and id in(".$id.")";
		$data   = array(
			'getUserId'		=> '',
			'getTime'   	=> '',
			'detectStatus' 	=> 0
		);
		$list =	WhStandardModel::update($data,$where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "退回失败，请重试！";
			return false;
		}
	}
	
	//删掉料号
	function  act_delSku(){
		$data   = array();
		$id_arr = $_POST['id'];
		$id     = implode(',',$id_arr);
		$where  = "where id in(".$id.")";
		$list =	WhStandardModel::delete($where);
		if($list){
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "退回失败，请重试！";
			return false;
		}
	}
	
	
	//料号查询
	function  act_getSkuInfo(){
		$sku   		= $_POST['sku'];
		$is_delete	= $_POST['is_delete'];
		if(is_numeric($sku)&& $sku>1000000){      //此sku为goods_code
			$goods_codes = WhStandardModel::goods_codeTosku($sku);
			$sku = $goods_codes['sku'];
		}
		//$where = "where sku='$sku' and sellerId=0 and detectStatus=0 order by id desc";
		if($is_delete){
			//如果是已删数据检索
			$where = "where sku='$sku' and is_delete=1 order by id desc";
		}else{
			$where = "where sku='$sku' and detectStatus=0 and is_delete=0 order by id desc";
		}
		$list  = WhStandardModel::getNowWhList("*",$where);
		foreach($list as $key=>$value){
			$list[$key]['printTime']   = date("Y-m-d H:i:s",$value['printTime']);
			$list[$key]['printerId']   = userModel::getUsernameById($value['printerId']);
			$list[$key]['purchaseId']  = userModel::getUsernameById($value['purchaseId']);
			$list[$key]['deleteUserId']   = userModel::getUsernameById($value['deleteUserId']);
			$list[$key]['getUserId'] = userModel::getUsernameById($value['getUserId']);
		}
		if($list){
			return $list;
		}else{
			$where = "where sku='$sku' order by id desc limit 3";
			$list  = WhStandardModel::getNowWhList("*",$where);
			foreach($list as $key=>$value){
				if(!empty($value['getTime']) && $value['detectStatus'] == 1){
					$user = userModel::getUsernameById($value['getUserId']);
					$getTime = date('Y-m-d H:i:s',$value['getTime']);
					self::$errMsg  .= "-料号-{$value['sku']} {$value['num']}件-于<font color='green'>{$getTime}</font>被<font color='green'>{$user}</font>领取，请联系他/她。<br>";
				} else if ($value['is_delete'] == 1) {
					$printer   = userModel::getUsernameById($value['printerId']);
					$printTime = date('Y-m-d H:i:s',$value['printTime']);					
					self::$errMsg  .= "-料号-{$value['sku']} {$value['num']}件-于<font color='green'>{$printTime}</font>由<font color='green'>{$printer}</font>打印，已过期删除!<br>";
				} else if($value['detectStatus'] == 3){
					$detector   = userModel::getUsernameById($value['detectorId']);
					$detectTime = date('Y-m-d H:i:s',$value['detectStartTime']);
					self::$errMsg  .= "-料号-{$value['sku']} {$value['num']}件-于<font color='green'>{$detectTime}</font>由<font color='green'>{$detector}</font>检测完成!<br>";
				} 
			}
			if (self::$errMsg == '') {
				if($is_delete == 0){
					self::$errMsg  .= "-料号-{$sku}-不在待领取列表中，请联系<font color='green'>仓库或打标人员</font>!<br>";
				}else{
					self::$errMsg .= "-料号-{$sku}-未在删除数据中找到。<br>";
				}
			}
			self::$errCode  = "003";	
			return false;
		}
	}	
}


?>