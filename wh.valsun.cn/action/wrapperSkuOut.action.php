<?php
/*
*包材出库
*@author heminghua
*/
class wrapperSkuOutAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }

	public function act_postsku(){
		$sku = isset($_POST['sku'])?trim($_POST['sku']):"";
		$sku = get_goodsSn($sku);
		$position = isset($_POST['position'])?trim($_POST['position']):"";
		$num = isset($_POST['num'])?trim($_POST['num']):"";
		
		$num = intval($num);	
		
		if (empty($position)){
			self::$errCode = 502;
			self::$errMsg = "仓位不能为空";
			return false;
		}
		
		if ($num<1){
			self::$errCode = 502;
			self::$errMsg = "{$sku}出库数量{$num}有误!";
			return false;
		}	
		
		$checkonhandle = wrapperSkuOutModel::selectstock($sku);
		if (empty($checkonhandle)){
			self::$errCode=502;
			self::$errMsg="{$sku}材料未导入库存信息表!";
			return false;
		}
		
		$skuinfo = whShelfModel::selectSku(" where sku = '{$sku}'");
		if (empty($skuinfo)){
			self::$errCode=502;
			self::$errMsg="{$sku}材料没信息!";
			return false;
		}else{
			$skuId      = $skuinfo['id'];
			$purchaseId = $skuinfo['purchaseId'];
		}
		
		$positon_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$position' and storeId in(1,2)");
		if(empty($positon_info)){
			self::$errCode = 502;
			self::$errMsg  = "无仓位号信息";
			return false;
		}else{
			$positionId = $positon_info[0]['id'];
		}
		
		$relation_info = OmAvailableModel::getTNameList("wh_product_position_relation","id","where pId='{$skuId}' and positionId='{$positionId}' and storeId in(1,2)");
		if(empty($relation_info)){
			self::$errCode = 502;
			self::$errMsg  = "包材和仓位不对应";
			return false;
		}
		
		$paraArr = array(
			'ordersn' 	 => date('YmdHis',time()),
			'sku'     	 => $sku,
			'amount'  	 => $num,
			'purchaseId' => $purchaseId,
			'ioType'	 => 1,
			'ioTypeId'   => 26,
			'userId'	 => $_SESSION['userId'],
			'reason'	 => '包材出库',
			'positionId' => $positionId
		);
	
		$WhIoRecordsAct = new WhIoRecordsAct();
		$tt = $WhIoRecordsAct->act_addIoRecoresForWh($paraArr);     //出库记录
	
		self::$errCode = 200;
		self::$errMsg = "{$sku}出库{$num}个成功！";
		return true;
	}
	
	public function act_selectsku(){
		$sku = isset($_POST['sku'])?trim($_POST['sku']):"";
		$sku = get_goodsSn($sku);
		$goodsinfo = wrapperSkuOutModel::skuinfo($sku);
		$res = array();
		if(empty($goodsinfo)){
			self::$errCode = 3;
			self::$errMsg = "<font color='red'>系统没有相应包装材料，请核对！</font>".$sku;
			return $sku;
		}else{
			self::$errCode = 2;
			self::$errMsg  = "包装材料正确，请扫描仓位！";
			$cguser = getUserNameById($goodsinfo['purchaseId']);
			$res['res_info'] = $goodsinfo['goodsName']."<br>采购:".$cguser;
			$res['sku'] = $sku;		
			return $res;
		}
		
	}
}
?>	