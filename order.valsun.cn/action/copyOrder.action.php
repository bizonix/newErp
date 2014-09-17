<?php
/*
* 复制订单功能
* @author by heminghua 
*/
class copyOrderAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	public function act_orderinfo(){
		$ret = array();
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:"";
		$order = copyOrderModel::selectOrder($orderid);
		$user = copyOrderModel::selectUser($orderid);
		$details = copyOrderModel::selectDetail($orderid);
		
		$platformId = $order['platformId'];
		$plateform = copyOrderModel::selectplatform($platformId); 
		$table = "om_unshipped_order_extension_".$plateform;
		
		$extension = copyOrderModel::selectExtension($table,$orderid);
		$notes = copyOrderModel::selectNote($orderid);
		$ret['order']['plateform'] = $plateform;
		foreach($extension as $key=>$value){
			if($key=='transId'){
				$ret['order']['transId'] = $value;
				
			}
			if($key=='currency'){
				$ret['fee']['currency'] = $value;
			}
		}
		foreach($order as $key=>$value){
			if($key=='recordNumber'){
				$ret['order']['recordNumber'] = $value;
			}
			if($key=='ordersTime'){
				$ret['order']['ordersTime'] = $value;
			}
			if($key=='paymentTime'){
				$ret['order']['paymentTime'] = $value;
			}
			if($key=='transportId'){
				$ret['fee']['transportId'] = $value;
			}
			if($key=='calcShipping'){
				$ret['fee']['calcShipping'] = $value;
			}
			if($key=='calcWeight'){
				$ret['fee']['calcWeight'] = $value;
			}
			if($key=='actualTotal'){
				$ret['fee']['actualTotal'] = $value;
			}
			if($key=='accountId'){
				$account = copyOrderModel::selectAccount($value);
				$ret['order']['account'] = $account['account'];
				$ret['order']['email'] = $account['email'];
			}
		}
		foreach($details as $key=>$value){
			if($key==0){
				$ret['detail']['sku'] .= $value['sku']."*".$value['amount']."*".$value['itemPrice'];
			}else{
				$ret['detail']['sku'] .= ",".$value['sku']."*".$value['amount']."*".$value['itemPrice'];
			}
		}
		foreach($user as $key=>$value){
			if($key=='countryName'){
				$ret['order']['countryName'] = $value;
				$ret['user']['countryName'] = $value;
			}
			if($key=='state'){
				$ret['user']['state'] = $value;
			}
			if($key=='landline'){
				$ret['user']['landline'] = $value;
			}
			if($key=='phone'){
				$ret['user']['phone'] = $value;
			}
			if($key=='zipCode'){
				$ret['user']['zipCode'] = $value;
			}
			if($key=='street'){
				$ret['user']['street'] = $value;
			}
			if($key=='address2'){
				$ret['user']['address2'] = $value;
			}
			if($key=='address3'){
				$ret['user']['address3'] = $value;
			}
			if($key=='username'){
				$ret['order']['username'] = $value;
			}
			if($key=='email'){
				$ret['order']['uemail'] = $value;
			}
		}
		if(!$notes){
			$notes = array();
			$ret['note'] = "";
		}else{
			foreach($notes as $key=>$value){
				if($key==0){
					$ret['note'] .= $value['content']."*".$value['userId']."*".$value['createdTime'];
				}else{
					$ret['note'] .= ",".$value['content']."*".$value['userId']."*".$value['createdTime'];
				}
			}
		}
		return $ret;
	}
	public function act_copyOrder(){
		$orderid 		= isset($_POST['orderid'])?$_POST['orderid']:"";
		$countryName 	= isset($_POST['countryName'])?$_POST['countryName']:"";
		$state 			= isset($_POST['state'])?$_POST['state']:"";
		$detail_sku 	= isset($_POST['detail_sku'])?$_POST['detail_sku']:"";
		$city 			= isset($_POST['city'])?$_POST['city']:"";
		$landline 		= isset($_POST['landline'])?$_POST['landline']:"";
		$phone 			= isset($_POST['phone'])?$_POST['phone']:"";
		$zipCode 		= isset($_POST['zipCode'])?$_POST['zipCode']:"";
		$street 		= isset($_POST['street'])?$_POST['street']:"";
		$address2 		= isset($_POST['address2'])?$_POST['address2']:"";
		$address3 		= isset($_POST['address3'])?$_POST['address3']:"";
		$transport 		= isset($_POST['transport'])?$_POST['transport']:"";
		$note  			= isset($_POST['note'])?$_POST['note']:"";
		$userId 		= $_SESSION['sysUserId'];
		$order = copyOrderModel::selectOrder($orderid);
		$user = copyOrderModel::selectUser($orderid);
		$details = copyOrderModel::selectDetail($orderid);
		
		$platformId = $order['platformId'];
		$plateform = copyOrderModel::selectplatform($platformId); 
		$table = "om_unshipped_order_extension_".$plateform;
		
		$extension = copyOrderModel::selectExtension($table,$orderid);
		$warehouse = copyOrderModel::selectWarehouse($orderid);
		$notes = copyOrderModel::selectNote($orderid);
		BaseModel::begin();
		if(!$order){
			self::$errCode = 501;
			self::$errMsg  = "原订单已完成或不存在！";
			return false;
		}
		if($order['isCopy']==2){
			self::$errCode = 502;
			self::$errMsg  = "此订单是复制产生的订单，不能在被复制！";
			return false;
		}
		$new_order = array();
		foreach($order as $key=>$value){
			if($key=='id'){
				continue;
			}
			$new_order[$key] = $value;
			if($key=='isCopy'){
				$new_order[$key] = 2;
			}
			
		}
		

		//$statuslist = copyOrderModel::selectStatus($status);
		//$new_order['orderStatus'] = $statusList['groupId'];
		//$new_order['orderType'] = $statusList['statusCode'];
		//先插入订单生成订单id
		//$sql = "";
		foreach($new_order as $key=>$value){
			if(is_numeric($value)){
				$sql[] = "{$key}={$value}";
			}else{
				$sql[] = "{$key}='{$value}'";
			}
		}
		$sql = implode(",",$sql);
		$id = copyOrderModel::insertOrder($sql,$userId);
		if(!$id){
			self::$errCode = 503;
			self::$errMsg  = "复制订单插入失败！";
			BaseModel::rollback();
			return false;
		}
		
		
		
		
		$new_user = array();
		//插入用户信息
		
		foreach($user as $key=>$value){
			$new_user[$key] = $value;
			if($key=='omOrderId'){
				$new_user[$key] = $id;
			}
			if($key=='countryName'){
				$new_user[$key] = $countryName;
			}
			if($key=='state'){
				$new_user[$key] = $state;
			}
			if($key=='city'){
				$new_user[$key] = $city;
			}
			if($key=='landline'){
				$new_user[$key] = $landline;
			}
			if($key=='phone'){
				$new_user[$key] = $phone;
			}
			if($key=='zipCode'){
				$new_user[$key] = $zipCode;
			}
			if($key=='street'){
				$new_user[$key] = $street;
			}
			if($key=='address2'){
				$new_user[$key] = $address2;
			}
			if($key=='address3'){
				$new_user[$key] = $address3;
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
		$msg = copyOrderModel::insertUser($sql,$userId);
		if(!$msg){
			self::$errCode = 503;
			self::$errMsg  = "插入复制订单用户信息失败！";
			BaseModel::rollback();
			return false;
		}
		
		
		
		//插入订单明细信息
		
		foreach($details as $nums=>$detail){
			$new_detail = array();
			
			$skuinfo = explode("*",$detail_sku[$nums]);
			
			foreach($detail as $key=>$value){
				if($key=='id'){
					continue;
				}
				$new_detail[$key] = $value;
				if($key=='omOrderId'){
					$new_detail[$key] = $id;
				}
				if($key=='sku'){
					$new_detail[$key] = $skuinfo[0];
				}
				if($key=='amount'){
					$new_detail[$key] = $skuinfo[1];
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
			$msg = copyOrderModel::insertDetail($sql,$userId);
			if(!$msg){
				self::$errCode = 504;
				self::$errMsg  = "插入复制订单明细信息失败！";
				BaseModel::rollback();
				return false;
			}
		}
		
		
		//插入复制订单扩展信息
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
		$msg = copyOrderModel::insertExtension($table,$sql,$userId);
		if(!$msg){
			self::$errCode = 505;
			self::$errMsg  = "插入复制订单扩展信息失败！";
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
			$msg = copyOrderModel::insertWarehouse($sql,$userId);
			if(!$msg){
				self::$errCode = 506;
				self::$errMsg  = "插入复制订单仓库信息失败！";
				BaseModel::rollback();
				return false;
			}
		}
		//插入复制订单备注信息
		if($notes){
			$new_note = array();
			foreach($notes as $key=>$value){
				if($key=='omOrdeId'){
					$new_note[$key] = $id;
					continue;
				}
				if($key=='userId'){
					$new_note[$key] = $userId;
					continue;
				}
				if($key=='createdTime'){
					$new_note[$key] = time();
					continue;
				}
				$new_note[$key] = $value;
			}
			$sql = array();
			foreach($new_note as $key=>$value){
				if(is_numeric($value)){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
				
			}
			$sql = implode(",",$sql);
			$msg = copyOrderModel::insertNote($sql);
			if(!$msg){
				self::$errCode = 506;
				self::$errMsg  = "插入复制订单备注信息失败！";
				BaseModel::rollback();
				return false;
			}
		}
		
		//完全插入成功再插入复制记录和订单操作记录
		
		$msg = copyOrderModel::insertCopyRecord($orderid,$id,$userId);
		if(!$msg){
			self::$errCode = 507;
			self::$errMsg  = "插入复制订单记录失败！";
			BaseModel::rollback();
			return false;
		}
		
		//最后修改原订单为复制订单
		$msg = copyOrderModel::updateOrder($orderid);
		if(!$msg){
			self::$errCode = 508;
			self::$errMsg  = "修改原订单失败！";
			BaseModel::rollback();
			return false;
		}
		
		
		BaseModel::commit();
		return true;
	}
}
?>