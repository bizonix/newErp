<?php
/*
* 拆分订单功能
* @author by heminghua 
*/
class splitOrderAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
		
    }
	
	/*
	 *批量超重拆分订单
	 */
	public function act_overWeightSplit() {
		$omOrderIds = isset ($_POST['omOrderIds']) ? $_POST['omOrderIds'] : ''; //选中要拆分的订单
		//var_dump($omOrderIds); exit;
		if (empty ($omOrderIds)) {
			self :: $errCode = '0061';
			self :: $errMsg = "empty omOrderIds";
			return false;
		}
		$omOrderIdArr = array_filter(explode(',', $omOrderIds));
		if (empty ($omOrderIdArr)) {
			self :: $errCode = '0062';
			self :: $errMsg = "error moOrderIdArr";
			return false;
		}
		try {
			$OrderindexAct = new OrderindexAct();
			$OrderRecordAct = new OrderRecordAct();
			//BaseModel :: begin();
			foreach($omOrderIdArr as $omOrderId){
				$skuinfos = $OrderindexAct->act_getRealskulist($omOrderId);
				//var_dump($skuinfos); echo "<br>";
				$issend = $OrderRecordAct->act_judgeAuditRecordsInSkus($omOrderId, $skuinfos);
				//var_dump($issend); exit;
				if(!$issend){
					$flag = SplitOrderModel::overWeightSplit($omOrderId); //拆分订单
					if (!$flag) {
						self :: $errCode = SplitOrderModel::$errCode;
						self :: $errMsg = SplitOrderModel::$errMsg;
						return false;
					}
				}
			}
			self :: $errCode = SplitOrderModel::$errCode;
			self :: $errMsg = SplitOrderModel::$errMsg;
			return true;
		} catch (Exception $e) {
			self :: $errCode = '404';
			self :: $errMsg = "split in ones error";
			return $flag; //返回splitOverWeightOrderForOne中的错误return值
		}
	}
	
	public function act_selectDetail(){
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$ostatus = $_POST['orderStatus'];
		$otype = $_POST['orderType'];
		
		/*$StatusMenuAct = new StatusMenuAct();
		$tableName = $StatusMenuAct->act_getOrderNameByStatus($ostatus, $otype);*/
		//echo $tableName; echo "<br>";
		$tableName = 'om_unshipped_order';
		//var_dump($orderid); exit;
		$where = ' WHERE omOrderId = '.$orderid;
		$detail  = OrderindexModel::showOnlyOrderDetailList($tableName, $where);
		//$detail  = splitOrderModel::selectDetail($orderid);
		//var_dump($detail); exit;
		if(!$detail){
			self::$errCode = 601;
			self::$errMsg  = "此订单明细为空，无法拆分！";
			return false;
		}
		if(count($detail)==1&&$detail[0]['amount']==1){
			self::$errCode = 601;
			self::$errMsg  = "此为单料号订单，无法拆分！";
			return false;
		}
		$arr = array();
		//print_r($detail);
		foreach($detail as $key=>$value){
			$arr[] = $value['sku']."*".$value['amount'];
		}
		$info = join(',', $arr);
		return $info;
	}
	
	public function act_handSplitOrder(){
		global $memc_obj;
		
		$skus = isset($_POST['skus'])?$_POST['skus']:"";
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$type = isset($_POST['type'])?$_POST['type']:"";
		//$userId = $_SESSION['sysUserId'];
		$sku_arr = explode(",",$skus);
		
		if (empty ($orderid)) {
			self :: $errCode = '0061';
			self :: $errMsg = "empty orderid";
			return false;
		}
		
		$flag = SplitOrderModel::handSplitOrder($orderid,$skus); //拆分订单
		if (!$flag) {
			self :: $errCode = SplitOrderModel::$errCode;
			self :: $errMsg = SplitOrderModel::$errMsg;
			return false;
		}
		self :: $errCode = SplitOrderModel::$errCode;
		self :: $errMsg = SplitOrderModel::$errMsg;
		return true;
	}
	
	public function act_autoSplitOrder(){
		$type = isset($_POST['type'])?$_POST['type']:""; 
		$key = isset($_POST['key'])?$_POST['key']:""; 
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:""; 
		$userId = $_SESSION['sysUserId'];
		$order = splitOrderModel::selectOrder($orderid);
		$details = splitOrderModel::selectDetail($orderid);
		$userinfo = splitOrderModel::selectUser($orderid);
		global $memc_obj;
		
		$platformId = $order['platformId'];
		$plateform = splitOrderModel::selectplatform($platformId); 
		$table = "om_unshipped_order_extension_".$plateform;
		
		$extension = splitOrderModel::selectExtension($table,$orderid);
		$warehouse = splitOrderModel::selectWarehouse($orderid);
		if($type==1){
			$amount=0;
			foreach($details as $detail){
				$nums = 0; //每个料号数量
				
				$result = $memc_obj->get_extral("sku_info_".$detail['sku']);
				for($i =0;$i<$detail['amount'];$i++){
					
					if($amount=$key){
						$skus[$detail['sku']] = $nums;
						$new_orders[] = array($skus,$weight,$shippingfee);
						$weight = 0;
						$shippingfee = 0;
						$amount=0;
					}
					$shippingfee += $detail['shippingfee'];
					
					$weight += $result['weight'];
						
					$amount += 1;
					$nums += 1;
				}
			}
			$skus[$detail['sku']] = $nums;
		}elseif($type==2){
			$weight = 0;
			foreach($details as $num=>$detail){
				
				$amount = 0;
				$result = $memc_obj->get_extral("sku_info_".$detail['sku']);
				foreach($detail as $value){
					
					
					if($weight+$result['weight']>$key){
						$skus[$detail['sku']] = $amount;
						$new_orders[] = array($skus,$weight,$shippingfee);
						$weight = 0;
						$shippingfee = 0;
						$amount=0;
					}
					//$price += $value['itemPrice']
					$shippingfee += $value['shippingfee'];
					
					$weight += $result['weight'];
						
					$amount += 1;
					
				}
				$skus[$detail['sku']] = $amount;
			}
		}//获取新订单数组
		
		
		
		BaseModel::begin();
		foreach($new_orders as $neworder){

			//先插入订单
			foreach($order as $key=>$value){
				
				if($key=='id'){
					continue;
				}
				if($key=='isSplit'){
					$new_order[$key] = 2;
					continue;
				}
				if($key=='calcWeight'){
					$new_order[$key] = $neworder[1];
					continue;
				}
				if($key=='calcShipping'){
					$new_order[$key] = $neworder[2];
					continue;
				}
				$new_order[$key] = $value;
			}
			$sql = array();
			foreach($new_order as $key=>$value){
				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
			}
			$sql = implode(",",$sql);
			$id = splitOrderModel::insertOrder($sql,$userId);
			if(!$id){
				self::$errCode = 611;
				self::$errMsg  = "拆分订单订单插入失败！";
				BaseModel::rollback();
				return false;
			}
			
			//插入订单明细信息
			foreach($details as $nums=>$detail){
				foreach($neworder[0] as $key1=> $sku_amount){
					if($key1==$setail['sku']){
						$new_detail = array();
						foreach($detail as $key=>$value){
							if($key=='id'){
								continue;
							}
							$new_detail[$key] = $value;
							if($key=='omOrderId'){
								$new_detail[$key] = $id;
							}
							if($key=='amount'){
								$new_detail[$key] = $sku_amount;
							}
							
						}
					
						$sql = array();
						
						foreach($new_detail as $key=>$value){
							if($key=='createdTime'){
								$sql[] = "{$key}=".time()." ";
								continue;
							}
							if(is_numeric($value)){
								$sql[] = "{$key}={$value}";
							}else{
								$sql[] = "{$key}='{$value}'";
							}
						}
						
						$sql = implode(",",$sql);
						$msg = splitOrderModel::insertDetail($sql,$userId);
						if(!$msg){
							self::$errCode = 612;
							self::$errMsg  = "插入拆分订单明细信息失败！";
							BaseModel::rollback();
							return false;
						}
					}
				}
			}
			
			$new_user = array();
			//插入用户信息
			foreach($userinfo as $key=>$value){
				$new_user[$key] = $value;
				if($key=='omOrderId'){
					$new_user[$key] = $id;
				}
				
			}
			$sql = array();
			foreach($new_user as $key=>$value){
				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
			}
			$sql = implode(",",$sql);
			$msg = splitOrderModel::insertUser($sql,$userId);
			if(!$msg){
				self::$errCode = 613;
				self::$errMsg  = "插入拆分订单用户信息失败！";
				BaseModel::rollback();
				return false;
			}
			
			
			//插入订单扩展信息
			$new_extension = array();
			foreach($extension as $key=>$value){
				if($key=='omOrderId'){
					$new_extension[$key] = $id;
					continue;
				}
				$new_extension[$key] = $value;
			}
			$sql = array();
			foreach($new_extension as $key=>$value){
				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
			}
			$sql = implode(",",$sql);
			$msg = splitOrderModel::insertExtension($table,$sql,$userId);
			if(!$msg){
				self::$errCode = 614;
				self::$errMsg  = "插入订单扩展信息失败！";
				BaseModel::rollback();
				return false;
			}
			
			
			//插入复制订单仓库信息
			if($warehouse){
				$new_warehouse = array();
				foreach($warehouse as $key=>$value){
					if($key=='omOrdeId'){
						$new_warehouse[$key] = $id;
						continue;
					}
					$new_warehouse[$key] = $value;
				}
				$sql = array();
				foreach($new_warehouse as $key=>$value){

					if(is_numeric($value)){
						$sql[] = "{$key}={$value}";
					}else{
						$sql[] = "{$key}='{$value}'";
					}
					
				}
				$sql = implode(",",$sql);
				$msg = splitOrderModel::insertWarehouse($sql,$userId);
				if(!$msg){
					self::$errCode = 615;
					self::$errMsg  = "插入拆分订单仓库信息失败！";
					BaseModel::rollback();
					return false;
				}
			}
			
			//完全插入成功再插入拆分记录和订单操作记录
			
			$msg = splitOrderModel::insertSplitRecord($orderid,$id,$userId);
			if(!$msg){
				self::$errCode = 616;
				self::$errMsg  = "插入拆分订单记录失败！";
				BaseModel::rollback();
				return false;
			}
			
			//最后修改原订单为拆分订单订单
			$msg = splitOrderModel::updateOrder($orderid);
			if(!$msg){
				self::$errCode = 617;
				self::$errMsg  = "修改原订单失败！";
				BaseModel::rollback();
				return false;
			}
			
		

		}
		BaseModel::commit();
		return true;
	}

}
?>