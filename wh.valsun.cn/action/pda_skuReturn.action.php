<?php
/*
 * 上架操作
 */
class pda_skuReturnAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = 'dfgtfgh';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	/*
	*搜索料号的相关信息
	*/
    public function act_checkorderid(){
		$ebay_id = isset($_POST['ebay_id']) ? trim($_POST['ebay_id']) : '';
		$p_real_ebayid='#^\d+$#';
		$p_trackno_eub='#^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$#';
		$is_eub_package_type=false;
		if(	preg_match($p_real_ebayid,$ebay_id)	){
		}else if( preg_match($p_trackno_eub,$ebay_id) ){
			$is_eub_package_type=true;
		}else{
			self::$errCode = 1;
			self::$errMsg = '订单号['.$ebay_id.']格式有误';
			echo json_encode($res);exit;	
		}
		$info = skuReturnModel::selectRecordById($ebay_id);
		
		if($is_eub_package_type===true){
			$info = skuReturnModel::selectRecordBytrack($ebay_id);
			//$ebay_id = $info['shipOrderId'];
		}
		

		if(empty($info)){
			self::$errCode = 2;
			self::$errMsg = '未找到发货单单/跟踪号['.$ebay_id.']';
			return ;
		}else{
			self::$errCode = 200;
			self::$errMsg = '订单/跟踪号['.$ebay_id.']';
			return $ebay_id;
		}
	}
	public function act_checksku(){
		$sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
		$sku = get_goodsSn($sku);
		
		$goodsinfo = skuReturnModel::selectSkuRecord($sku);
		if(!$goodsinfo){
			self::$errCode = 3;
			self::$errMsg = "<font color='red'>系统没有相应料号，请核对！</font>".$sku;
			return;
		}/*else{
			$res['res_code'] = "200";
			$res['res_msg'] = "{$goodsinfo['goods_sn']}，仓位：[{$goodsinfo['goods_location']}]请输入异常订单入库数量！";
			echo json_encode($res);exit;
		}*/

		$checkonhandle = skuReturnModel::selectStockRecord($sku);
		
		if (!$checkonhandle){
			self::$errCode = 502;
			self::$errMsg = "{$sku}产品未导入库存信息表!";
			return;
		}
		
		
		$info = skuReturnModel::updateStock($sku,1);
		//$note = "PDA异常退货入库sku[{$sku}]1个!";
		if($info){
			//into_warehouse_log($sku,1,$note,'异常订单PDA扫描入库',$truename,'');
			self::$errCode = 200;
			self::$errMsg = "{$sku}入库1个成功！";
			return $sku;
		}else{
			self::$errCode = 1;
			self::$errMsg = "入库失败,请重新扫描订单!";
			return;
		}
	
	}
	public function act_postalldate(){
		$sku = isset($_POST['sku']) ? trim($_POST['sku']) : '';
		$ebay_id = isset($_POST['ebay_id']) ? trim($_POST['ebay_id']) : '';
		$sku = get_goodsSn($sku);
		$num = intval($_POST['num']);
		if ($num<0){
			self::$errCode = 502;
		    self::$errMsg = "{$sku}入库数量{$num}有误,请重新扫描订单!";
			return $sku;
		}
		if(empty($ebay_id) || empty($sku)){
			self::$errCode = 503;
			self::$errMsg = "入库数据有误,请重新扫描订单!";
			return $sku;
		}

		$checkonhandle = skuReturnModel::selectStockRecord($sku);
		
		if (!$checkonhandle){
			self::$errCode = 504;
			self::$errMsg = "{$sku}产品未导入库存信息表!";
			return $sku;
		}
		$msg = skuReturnModel::selectscanRecord($sku,$ebay_id);

		if($msg['amount'] < $num ){
			self::$errCode = 506;
			self::$errMsg = "实际配货数量为{$msg['amount']}比输入的数量小!";
			return $sku;
		}
		if($msg['amount']==$num){
		 $info1 = skuReturnModel::updatescanRecord("all",$ebay_id,$sku);
		}else{
		 $info1 = skuReturnModel::updatescanRecord($num,$ebay_id,$sku);
		}
		
		//$update_sql = "UPDATE ebay_onhandle SET goods_count=goods_count+$num WHERE store_id ={$defaultstoreid} AND goods_sn ='{$sku}' AND ebay_user ='{$user}'";
		$info = skuReturnModel::updateStock($sku,$num);
		//$note = "PDA异常退货入库sku[{$sku}]{$num}个!";
		if($info&&$info1){
			//into_warehouse_log($sku,$num,$note,'异常订单PDA扫描入库',$truename,$ebay_id);
			self::$errCode = 200;
			self::$errMsg = "{$sku}入库{$num}个成功！";
			return $sku;
		}else{
			self::$errCode = 505;
			self::$errMsg = "入库失败,请重新扫描订单!";
			
			return;
		}
	
	}
}
?>	