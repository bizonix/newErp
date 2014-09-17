<?php
/*
 *核心逻辑 统计 订单数据 变化sku 数据对应变化
 *
 *add by xiaojinhua
 * */
class SkuDailyInfo{
	/*
	private	$waitingsend = array(0, 2, 615, 617, 625, 640, 642, 652, 654);
	private	$interceptsend = array(640);
	private	$shortagesend = array(642);
	private	$waitingaudit = array(652, 654);
	private $partpackage = array(123);
	 */

	private $dbcon;
	private static $_instance;
	private $orderType; //待发货 、 自动拦截 、待审核、部分包货、超大订单拦截
	public $rmqObj;
	public $exchange = 'data_analyze';
	public $orders_except = ' and ($o.isSplit=0 or $o.isSplit=2) and ($o.is_delete=0  and $od.is_delete=0 ) ';
	public $statusArr;
	
	public function __construct(){
		global $dbConn;

		// 获取订单状态类型
		$this->statusArr = C('ORDER_STATUS');
	}
	
	public function replaceTablePrefix($order_prefix, $order_detail_prefix){
		$orders_except = str_replace('$o.', $order_prefix, $this->orders_except);
		$orders_except = str_replace('$od.', $order_detail_prefix, $orders_except);
		return $orders_except;
	}

	public function updateSkuDailyCountInfoByField($sku,$field='',$amount=-1){
		if($field == 'waitingSendCount'){
			if($amount == -1){
				$amount = $this->getWaitingSendCount($sku);//待发货
			}
			
		}else if($field == 'waitingAuditCount'){
			if($amount == -1){
				$amount = $this->getWaitingAuditCount($sku); //超大待审核数量
			}
			
		}else if($field == 'superAmountSku'){
			if($amount == -1){
				$amount = $this->getSuperAmountSkuCount($sku);//超大拦截
			}
			
		}else if($field == 'shortageSendCount'){
			if($amount == -1){
				$amount = $this->getOutOfStockOrderSkuCount($sku); //自动拦截 缺货sku
			}
			
		}else if($field == 'averageDailyCount'){
			
		}
		
		$sql = "select count(*) as number from om_sku_daily_status where sku='{$sku}'";
		
		$dataInfo = $this->getSqlResult($sql, 1, 2); 
		
		$sql_type = 1;
		
		if($dataInfo['number'] > 0){
			$sql = "update om_sku_daily_status set 
				{$field}={$amount} 
				WHERE sku='{$sku}'";
			
			$sql_type = 2;
		}else{
			$sql = "insert into om_sku_daily_status set 
				{$field}={$amount},
				sku='{$sku}'";
			$sql_type = 3;
		}
		
		if($this->getSqlResult($sql,$sql_type)){
			return true;
		}else{
			return false;
		}
		
	}
	/*
	 * 重置SKU 库存实际数据
	 * 
	 * */
	public function updateSkuDailyCountInfo($sku){
	
		$waitingsend = $this->calWaitingSendCount($sku);//待发货
		//echo $waitingsend;exit;
		$averageDailyCount = $this->calcSkuAverageDailyCount($sku);//echo '$averageDailyCount:'.$averageDailyCount;exit;
//		echo $averageDailyCount;exit;
		$superAmountSku = $this->calSuperAmountSkuCount($sku);//超大拦截
		$shortagesendCount = $this->calOutOfStockOrderSkuCount($sku); //自动拦截 缺货sku
		$waitingauditCount = $this->calWaitingAuditCount($sku); //超大待审核数量

		$sql = "select count(*) as number from om_sku_daily_status where sku='{$sku}'";
		
		$dataInfo = $this->getSqlResult($sql, 1, 2); 
		
		$sql_type = 1;
		
		if($dataInfo['number'] > 0){
			$sql = "update om_sku_daily_status set 
				averageDailyCount={$averageDailyCount},
				waitingSendCount={$waitingsend},
				shortageSendCount={$shortagesendCount},
				waitingAuditCount={$waitingauditCount},
				superAmountSku={$superAmountSku}
				WHERE sku='{$sku}'";
			
			$sql_type = 2;
		}else{
			$sql = "insert into om_sku_daily_status set 
				averageDailyCount={$averageDailyCount},
				waitingSendCount={$waitingsend},
				shortageSendCount={$shortagesendCount},
				waitingAuditCount={$waitingauditCount},
				superAmountSku={$superAmountSku},
				sku='{$sku}'";
			$sql_type = 3;
		}
		
		if($this->getSqlResult($sql,$sql_type)){
			return true;
		}else{
			return false;
		}
	}


	//更新每日均量
	public function updateSkuDailyAverageInfo($sku){
		
		$averageDailyCount = $this->calcSkuAverageDailyCount($sku);	
		
		$this->updateSkuDailyCountInfoByField($sku, 'averageDailyCount', $averageDailyCount);
		
		return true;
		
	}
	//更新订单中sku的每日均量
	public function updateDailyAverageInfoByOrder($order_id){
		$sku_arr = $this->getOrderSkuInfo($order_id);//echo '<pre>';print_r($sku_arr);exit;
		foreach($sku_arr as $row){
			$this->updateSkuDailyCountInfo($row['sku']);
		}
	}
	/**
	 * 计算SKU每日均量
	 * @param string $sku
	 */
	public function getSkuAverageDailyCount($sku){
		$sql = "select averageDailyCount from om_sku_daily_status where sku='{$sku}'";
		$data = $this->getSqlResult($sql,1,2);
		$dayilyNum = floatval($data["averageDailyCount"]);
		if($dayilyNum == 0){
			$dayilyNum = $this->updateSkuDailyAverageInfo($sku);
		}	
			
		return $dayilyNum;
	}

	/*
	 *正常均量计算
	 *
	 * */
	public function calcSkuAverageDailyCount($sku){
		$totalNum7 = $this->getPastDayCount($sku,7); 
		$totalNum15 = $this->getPastDayCount($sku,15);
		$totalNum30 = $this->getPastDayCount($sku,30);
		$dayilyNum = $totalNum7 / 7 * 0.7 + ($totalNum15 - $totalNum7) / 8* 0.2 + ($totalNum30 -$totalNum15) / 15 * 0.1;
		$dayilyNum = round($dayilyNum,2); //取两位小数
		//echo $sku.'$dayilyNum:'.$dayilyNum;
		return $dayilyNum;
	}

	public function getOrderSkuInfo($id){ 
		$sql = "select a.orderStatus,a.orderType,b.sku,b.amount 
		from om_unshipped_order as a 
		left join om_unshipped_order_detail as b on a.id=b.omOrderId where a.id={$id}";//echo $sql;
		$rtnArr = $this->getSqlResult($sql, 1); 
		return $rtnArr;
	}
	
	/**
	 * 获取过去N天进入系统正常订单数量和
	 * @param string $sku
	 * @param int $d
	 * @param int $esale 均量
	 */
	public function getPastDayCount($sku, $d, $esale=5){
		
		$now = time();
		$beforeDtime = $now - $d*24*60*60;
		
		//$skuStr = implode("','",$skuArr);
		$totalNum = 0;
		
		$orders_except = $this->replaceTablePrefix('b.', 'a.');
		
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		$skuinfo = $skuinfo_arr['skuInfo'];
		$sku_arr = array_keys($skuinfo);
		
		if(!in_array($sku,$sku_arr)){
			$sku_arr[] = $sku;//组合sku,把组合sku加入
		}
		
		$skuStr = implode("','", $sku_arr);
		
		foreach($skuinfo as $sub_sku=>$sub_sku_detail){
			$sub_amount = $sub_sku_detail['amount'];
			
			$sql = "select sum(a.amount) as qty from om_unshipped_order_detail as a left join om_unshipped_order as b  on b.id=a.omOrderId 
					where a.sku in ('{$skuStr}')
					and b.ordersTime > {$beforeDtime}
					 ".$orders_except." ";
			//echo $sql."\n";
			//if($d == 30) {echo $sql;exit;}
			$rtn = $this->getSqlResult($sql,1,2);
		//if( $d==30) {echo '$rtn:'.$rtn['qty'];print_r($rtn);echo $sql;exit;}
			$totalNum += $rtn['qty'] * $sub_amount;

			$sql = "select sum(a.amount) as qty from om_shipped_order_detail as a left join om_shipped_order as b  on b.id=a.omOrderId 
					where a.sku in ('{$skuStr}')
					and b.ordersTime > {$beforeDtime}
					 ".$orders_except." 
					
					";
			//echo $sql."\n";
			
			$rtn = $this->getSqlResult($sql,1,2);
			
			$totalNum += $rtn['qty'] * $sub_amount;
		}
		//echo "{$d} 天的正常销量{$totalNum}\n";
		return $totalNum;
	}
	/**
	 * 订单进入待发货状态或者出去待发货状态的时候 更新待发货数量
	 * $order_id 订单id
	 * $update_type 更新类型：1表示订单进入待发货状态，2表示出去待发货状态
	 */
	public function updateWaitingSendCountByOrder($order_id, $update_type = 1){
		$sku_arr = $this->getOrderSkuInfo($order_id);

		foreach ($sku_arr as $row){
			
			$sku = $row['sku'];
			$amount = $row['amount'];
			
			$this->updateWaitingSendCountBySku($sku, $amount, $update_type);

		}
		
		return true;
	}
	/**
	 * 订单进入待发货状态或者出去待发货状态的时候 更新待发货数量
	 * $sku 更新的sku
	 * $amount 更新数量
	 * $update_type 更新类型：1表示订单进入待发货状态，2表示出去待发货状态
	 */
	public function updateWaitingSendCountBySku($sku, $amount= 0, $update_type = 1){
		//sku有可能是组合sku
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		$skuinfo = $skuinfo_arr['skuInfo'];
		
		foreach($skuinfo as $tmp_sku=>$sku_detail){
			$tmp_sku_count = $amount * $sku_detail['amount'];
			$waiting_send_num = $this->getWaitingSendCount($tmp_sku);
			
			if($update_type == 1){
				$total_num = $waiting_send_num + $tmp_sku_count;
			}else{
				$total_num = $total_num_now - $amount < 0?0 : $total_num_now - $amount;
			}
			
			$this->updateSkuDailyCountInfoByField($tmp_sku, 'waitingSendCount', $total_num);
			
		}
		
		return true;
		
	}
	
	//获取待发货数量,直接返回表字段
	public function getWaitingSendCount($sku, $except_amount = 0){
		
		$sql = "select waitingSendCount 
		from om_sku_daily_status  
		where   sku = '{$sku}'   ";
		
		$totalNum = $this->getSqlResult($sql,1,'waitingSendCount');
		
		if($totalNum - $except_amount >0){
			return $totalNum - $except_amount;
		}else{
			return 0;
		}
		
	}
	public function calWaitingSendCount($sku){
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		
		if(empty($skuinfo_arr)) return 0;
		
		$skuinfo = $skuinfo_arr['skuInfo'];
		$skuStr = implode("','",array_keys($skuinfo));
		
		$waitingsend_status = implode("','",$this->statusArr['waitingsend']);
		
		$orders_except = $this->replaceTablePrefix('a.', 'b.');
		
		$sql = "select a.id,a.orderStatus ,b.sku,b.amount 
		from om_unshipped_order as a 
		left join om_unshipped_order_detail as b on a.id=b.omOrderId 
		where  a.orderType in ('{$waitingsend_status}')  
		 and b.sku in ('{$skuStr}') ".$orders_except;
		//echo $sql;exit;
		
		$rtnArr = $this->getSqlResult($sql,1,1);
		
		$totalNum = 0;
		foreach($rtnArr as $item){
			$totalNum += $item["amount"] * $skuinfo[$item["sku"]]['amount'];
		}
//echo '$totalNum:'.$totalNum;
		return $totalNum;
	}
	//获取超大订单拦截数量
	public function calSuperAmountSkuCount($sku){
		
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		$skuinfo = $skuinfo_arr['skuInfo'];
		$skuStr = implode("','",array_keys($skuinfo));

		$largeInterceptOrder = implode(',', $this->statusArr['superLargeBlocked']);
		
		$orders_except = $this->replaceTablePrefix('a.', 'b.');
		
		//$sql = "select a.orderStatus ,b.sku,b.amount from om_unshipped_order as a left join om_unshipped_order_detail as b on a.id=b.omOrderId where a.orderStatus=200 and a.orderType in (201,202,700,203) and b.sku in ('{$skuStr}') ";
		$sql = "select a.orderStatus ,b.sku,b.amount from om_unshipped_order as a 
		left join om_unshipped_order_detail as b on a.id=b.omOrderId 
		where a.orderType in ({$largeInterceptOrder}) and b.sku in ('{$skuStr}') ".$orders_except;
		
		$rtnArr = $this->getSqlResult($sql,1,1);
		
		$totalNum = 0;
		foreach($rtnArr as $item){
			$totalNum += $item["amount"] * $skuinfo[$item["sku"]]['amount'];
		}

		return $totalNum;
	}
	//获取超大订单拦截数量
	public function getSuperAmountSkuCount($sku){
		
		$sql = "select superAmountSku 
		from om_sku_daily_status  
		where   sku = '{$sku}'   ";
		
		$totalNum = $this->getSqlResult($sql,1,'superAmountSku');
		
		return $totalNum;
	}

	//获取自动拦截缺货数量（包括快递缺货、小包缺货）
	public function calOutOfStockOrderSkuCount($sku){
		
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		$skuinfo = $skuinfo_arr['skuInfo'];
		$skuStr = implode("','",array_keys($skuinfo));

		$orders_except = $this->replaceTablePrefix('a.', 'b.');
		
		$shortageSend = implode(',', $this->statusArr['outofStock']);
		
		$sql = "select a.orderStatus ,b.sku,b.amount 
		from om_unshipped_order as a 
		left join om_unshipped_order_detail as b on a.id=b.omOrderId 
		where  a.orderType in ({$shortageSend})  and b.sku in ('{$skuStr}') ".$orders_except;
		//echo $sql;
		
		$rtnArr = $this->getSqlResult($sql,1,1);
		
		$totalNum = 0;
		foreach($rtnArr as $item){
			$totalNum += $item["amount"] * $skuinfo[$item["sku"]]['amount'];
		}

		return $totalNum;
	}
	//获取自动拦截缺货数量（包括快递缺货、小包缺货）
	public function getOutOfStockOrderSkuCount($sku){
		
		$sql = "select shortageSendCount 
		from om_sku_daily_status  
		where   sku = '{$sku}'   ";
		
		$totalNum = $this->getSqlResult($sql,1,'shortageSendCount');
		
		return $totalNum;
	}

	//获取待审核超大订单sku数量
	public function getWaitingAuditCount($sku){
		
		$sql = "select waitingAuditCount 
		from om_sku_daily_status  
		where   sku = '{$sku}'   ";
		
		$totalNum = $this->getSqlResult($sql,1,'waitingAuditCount');
		
		return $totalNum;
		
	}
//获取待审核超大订单sku数量
	public function calWaitingAuditCount($sku){
		$skuinfo_arr = M("InterfacePc")->getSkuinfo($sku);
		$skuinfo = $skuinfo_arr['skuInfo'];
		$skuStr = implode("','",array_keys($skuinfo));

		$orders_except = $this->replaceTablePrefix('a.', 'b.');
		
		$waitingAudit = implode(',', $this->statusArr['waitingaudit']);
		
		$sql = "select a.orderStatus ,b.sku,b.amount 
		from om_unshipped_order as a 
		left join om_unshipped_order_detail as b on a.id=b.omOrderId 
		where  a.orderType in ({$waitingAudit})  and b.sku in ('{$skuStr}') ".$orders_except;
		//echo $sql;
		
		$rtnArr = $this->getSqlResult($sql,1,1);
		
		$totalNum = 0;
		foreach($rtnArr as $item){
			$totalNum += $item["amount"] * $skuinfo[$item["sku"]]['amount'];
		}

		return $totalNum;
	}



	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $sql
	 * @param int $sql_type 1:select, 2:update,3:insert
	 * @param string $return_type 结果返回类型：1所有行，2:单行，其他字符串是一个字段
	 */
	public function getSqlResult($sql,$sql_type = '1',$return_type = 1){
		
		$info = M('skuDailyInfo')->getSqlResult($sql,$sql_type,$return_type);
		return $info;
		//echo '<pre>';print_r($info);var_dump($info);
		
	}
}
 
?>
