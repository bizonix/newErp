<?php
/**
 *类名：FinanceAPIModel
 *功能：采购系统与财务系统交互业务数据操作类,推送数据到财务系统
 *日期: 2014-03-03
 *版本：1.0
 *作者：王民伟
 */
class PurToFinanceAPIModel{
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg  = "";
	public static function	initDB(){
		global $dbConn;
		self::$dbConn 	= $dbConn;
	}
	//结款订单信息返回确认
	public static function getEndPayOrder($dataArr){
		self::initDB();
		$inOrderArr = '';
		foreach($dataArr as $orderId){
			$inOrderArr .= "'".$orderId."',";
		}
		$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
		$sql 		= "SELECT id, recordnumber, prepaymoney FROM ph_order WHERE id in $inOrderArr ORDER BY id ASC ";
		$query  	= self::$dbConn->query($sql);
		if($query){
			$orderData 		= self::$dbConn->fetch_array_all($query);
			$rtnData   		= array();
			$rtnDataArr 	= array();
			if(!empty($orderData)){
				$num 				= 0;
				$totalallmoney 		= 0;
				$prepayallmoney 	= 0;
				foreach($orderData as $k => $v){
					$rtnData[$num]['recordnumber']     	= $v['recordnumber'];//订单号
					$rtnData[$num]['prepaymoney'] 		= $v['prepaymoney'];//已预付金额
					$rtnData[$num]['totalmoney']  		= clacTotalCost($v['id']);//订单总金额
					$totalallmoney 				 	   += $rtnData[$num]['totalmoney'];
					$prepayallmoney				 	   += $rtnData[$num]['prepaymoney'];
					$num++;
				}
				$rtnDataArr[0] = $rtnData;
				$rtnDataArr[1] = $totalallmoney;
				$rtnDataArr[2] = $prepayallmoney;
			}
			return $rtnDataArr;
		}else{
			return false;
		}
	}

	//更新结款与全额预付订单信息
	public static function updEndPayOrderStatus($orderArr, $type){
		self::initDB();
		$arr 		= explode(',', $orderArr);
		$rollback 	= false;
		self::$dbConn->begin();//开启事物
		foreach($arr as $ordersn){
			$sql 		= "SELECT id, prepaymoney FROM ph_order WHERE recordnumber = '{$ordersn}' ";
			$query  	= self::$dbConn->query($sql);
			if($query){
				$orderData 	= self::$dbConn->fetch_array($query);
				if(!empty($orderData)){
					$id 			= $orderData['id'];
					$totalmoney     = clacTotalCost($id);//订单总金额
					$prepaymoney 	= $orderData['prepaymoney'];//已预付金额
					$endpaymoney    = $totalmoney - $prepaymoney;//结款金额
					if($type == 'pay'){//结款
						$upd       	= "UPDATE ph_order SET endmoney = '{$endpaymoney}', paystatus = 4, payresult = 1 WHERE id = '{$id}'";
					}
					if($type == 'preAllPay'){//全额预付
						$upd       	= "UPDATE ph_order SET prepaymoney = '{$endpaymoney}', paystatus = 3, payresult = 1 WHERE id = '{$id}'";
					}
					if($type == 'backAllPay'){//全额退款
						$upd       	= "UPDATE ph_order SET returnmoney = '{$totalmoney}', paystatus = 6, payresult = 3 WHERE id = '{$id}'";
					}
					$updquery  		= self::$dbConn->query($upd);
					if(!$updquery){
						$rollback = true;
					}
				}
			}
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}

	//更新部份预付订单信息
	public static function updPartPreOrderStatus($orderArr, $cate, $digitial){
		self::initDB();
		$dataArr 	= explode(',', $orderArr);
		$inOrderArr = '';
		foreach($dataArr as $orderId){
			$inOrderArr .= "'".$orderId."',";
		}
		$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
		$rollback 	= false;
		self::$dbConn->begin();//开启事物
		$sql 		= "SELECT a.id, b.id  AS bid, b.price, b.count FROM ph_order AS a JOIN ph_order_detail AS b ON a.id = b.po_id ";
		$sql 	   .= " WHERE a.recordnumber IN {$inOrderArr} ORDER BY a.id";
		$query  	= self::$dbConn->query($sql);
		if($query){
			$orderData 	= self::$dbConn->fetch_array_all($query);
			$idArr      = array();
			$payTotalMoney = array();
			if(!empty($orderData)){
				$num = 0;
				foreach($orderData as $k => $v){
					$id    = $v['id'];
					$bid   = $v['bid'];
					$price = $v['price'];
					$count = $v['count'];
					$money = $price * $count;
					if(!in_array($id, $idArr)){
						$idArr[] = $id;
					}
					if($cate == 'per'){//按百分比预付
						$paymoney = $money * ($digitial / 100);
					}else if($cate == 'money'){
						$diffmoney   = $digitial - $money;
						if($diffmoney > 0){
							$paymoney = $money;
						}
						if($diffmoney < 0 && $digitial > 0){
							$paymoney = $digitial;
						}
						if($diffmoney < 0 && $digitial < 0){
							$paymoney = 0;
						}
						$digitial 	= $diffmoney;
					}
					$payTotalMoney[$id][$num] = $paymoney;
					$num++;
				}
				foreach($idArr as $sid){
					$arrOrder = $payTotalMoney[$sid];
					$premoney = 0;
					foreach($arrOrder as $money){
						$premoney += $money;
					}
					$upd  			= "UPDATE ph_order SET prepaymoney = '{$premoney}', paystatus = 2, payresult = 1 WHERE id = '{$sid}'";
					$updquery  		= self::$dbConn->query($upd);
					if(!$updquery){
						$rollback = true;
					}
				}	
			}
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}
	//全额预付订单信息返回确认
	public static function getPreAllPayOrder($dataArr){
		self::initDB();
		$inOrderArr = '';
		foreach($dataArr as $orderId){
			$inOrderArr .= "'".$orderId."',";
		}
		$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
		$sql 		= "SELECT id, recordnumber FROM ph_order WHERE id in $inOrderArr ORDER BY id ASC ";
		$query  	= self::$dbConn->query($sql);
		if($query){
			$orderData 		= self::$dbConn->fetch_array_all($query);
			$rtnData   		= array();
			$rtnDataArr 	= array();
			if(!empty($orderData)){
				$num 				= 0;
				$totalallmoney 		= 0;
				foreach($orderData as $k => $v){
					$rtnData[$num]['recordnumber']     	= $v['recordnumber'];//订单号
					$rtnData[$num]['totalmoney']  		= clacTotalCost($v['id']);//订单总金额
					$totalallmoney 				 	   += $rtnData[$num]['totalmoney'];
					$num++;
				}
				$rtnDataArr[0] = $rtnData;
				$rtnDataArr[1] = $totalallmoney;
			}
			return $rtnDataArr;
		}else{
			return false;
		}
	}

	//部份预付订单信息返回确认
	public static function getPrePartPayOrder($dataArr){
		self::initDB();
		$inOrderArr = '';
		foreach($dataArr as $orderId){
			$inOrderArr .= "'".$orderId."',";
		}
		$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
		$sql 		= "SELECT a.id, a.recordnumber, a.prepaymoney, b.sku, b.price, b.count FROM ph_order AS a ";
		$sql       .= "JOIN ph_order_detail AS b ON a.id = b.po_id WHERE a.id in $inOrderArr ORDER BY a.id ASC ";
		$query  	= self::$dbConn->query($sql);
		$orderData  = array();
		$rtnDataArr = array();
		if($query){
			$orderData 		= self::$dbConn->fetch_array_all($query);
			if(!empty($orderData)){
				$totalallmoney 		= 0;
				$rtnData 			= array();
				$num				= 0;
				$orderArr           = array();
				foreach($orderData as $k => $v){
					$id 					  = $v['id'];
					$rtnData[$num]['ordersn'] = $v['recordnumber'];
					$rtnData[$num]['sku']     = $v['sku'];
					$rtnData[$num]['price']   = $v['price'];
					$rtnData[$num]['count']   = $v['count'];
					if(!in_array($id, $orderArr)){
						$orderArr[] = $id;
					}
					$num++;
				}
				foreach($orderArr as $pid){//订单号唯一过滤计算总额
					$totalmoney 	= clacTotalCost($pid);
					$totalallmoney += $totalmoney;
				}

				$rtnDataArr[0] = $rtnData;
				$rtnDataArr[1] = $totalallmoney;
			}
			return $rtnDataArr;
		}else{
			return false;
		}
	}

	//全额退款订单信息返回确认
	public static function getBackAllPayOrder($dataArr){
		self::initDB();
		$inOrderArr = '';
		foreach($dataArr as $orderId){
			$inOrderArr .= "'".$orderId."',";
		}
		$inOrderArr = "(".substr($inOrderArr, 0, strlen($inOrderArr) - 1).")";
		$sql 		= "SELECT id, recordnumber, prepaymoney, endmoney FROM ph_order WHERE id in $inOrderArr ORDER BY id ASC ";
		$query  	= self::$dbConn->query($sql);
		if($query){
			$orderData 		= self::$dbConn->fetch_array_all($query);
			$rtnData   		= array();
			$rtnDataArr 	= array();
			if(!empty($orderData)){
				$num 				= 0;
				$totalallmoney 		= 0;
				foreach($orderData as $k => $v){
					$rtnData[$num]['recordnumber']     	= $v['recordnumber'];//订单号
					$rtnData[$num]['premoney']			= $v['prepaymoney'];//预付金额
					$rtnData[$num]['endmoney']			= $v['endmoney'];//结款金额
					$rtnData[$num]['totalmoney']  		= clacTotalCost($v['id']);//总金额
					$totalallmoney 				 	   += $rtnData[$num]['totalmoney'];
					$num++;
				}
				$rtnDataArr[0] = $rtnData;
				$rtnDataArr[1] = $totalallmoney;
			}
			return $rtnDataArr;
		}else{
			return false;
		}
	}
}
?>