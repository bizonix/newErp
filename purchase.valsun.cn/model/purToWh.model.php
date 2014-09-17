<?php
class PurToWhModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	private static $_instance;
	
	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	//删除返回仓库重点的SKU异常状态
	public static function delUnusualSku($idArr){
		self::initDB();
		$inArr = implode(',', $idArr);
		$upd   = "DELETE FROM ph_sku_reach_record  WHERE unOrderId in ($inArr)";
		$query	= self::$dbConn->query($upd);
		if($query){
			return true;
		}else{
			return false;
		}
	}
	
	//判断异常到货记录是否已存在采购系统 add by wangminwei 2014-04-03
	public static function isExistUnusualSku($unOrderId){
		self::initDB();
		$sql 	= "SELECT COUNT(*) AS total FROM ph_sku_reach_record WHERE unOrderId = '{$unOrderId}'";
		$query 	= self::$dbConn->query($sql);
		if($query){
			$rtnData	= self::$dbConn->fetch_one($query);
			return $rtnData['total'];
		}
	}

	//收货管理列表 add by wangminwei 2014-04-08
	public static function getReceiptGoods($condition, $page){
		self::initDB();
		$pagenum    = 200;
		$start		= ($page - 1)* $pagenum;
		$limit 		= " ORDER by purtime DESC,sku limit {$start}, {$pagenum}";
		$dataInfo   = array();
		$dataDetail = array();
		$totalNum   = 0;
		$data       = array();
		$sql 	    = "SELECT * FROM ph_receipt_goods WHERE 1 = 1 ";
		$sqlcount 	= "SELECT COUNT(*) AS totalNum FROM ph_receipt_goods WHERE 1 = 1 ";
		$sqlcount  .= $condition;
		$sql       .= $condition;
		$sqlstr     = $sql.$limit;
		$query    	= self::$dbConn->query($sqlstr);
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				foreach($dataInfo as $k => $v){
					$id 				= $v['id'];
					$dataDetail[$id] 	= self::getReceiptGoodsDetailById($id);
				}
			}
			$totalData 		= self::$dbConn->fetch_first($sqlcount);
			$totalNum       = $totalData['totalNum'];
			$data 		= array("totalNum"=>$totalNum,"goodsInfo"=>$dataInfo, "detailInfo"=>$dataDetail);
		}
		return $data;
	}
	
	/**
	 * 收货管理表月度搜索
	 */
	public static function getReceiptMothInfo($condition){
		self::initDB();
		$sql 	    	= "SELECT distinct(ordersn) as ordersn FROM ph_receipt_goods WHERE status = 1 AND order_stu = 1 ";
		$sql       	   .= $condition;
		$sql           .= " ORDER BY ordersn, sku";
		$query    		= self::$dbConn->query($sql);
		$totalMoney 	= 0;
		$totalRecMoney 	= 0;
		$data       	= array();
		$orderArr   	= array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				$num = 0;
				foreach($dataInfo as $k => $v){
					$ordersn 		    = $v['ordersn'];//获取条件时间段内下的订单号
					$rtnData            = self::getOrderSnTotalMoney($ordersn);
					$totalMoney        += $rtnData['money'];//搜索条件下的订单总额
					$totalRecMoney     += $rtnData['recmoney'];//总的到货金额
					$orderArr[$num]['money'] 		= $rtnData['money'];//获取订单号对应的订单总额
					$orderArr[$num]['recmoney'] 	= $rtnData['recmoney'];//获取订单号对应的订单总额
					$orderArr[$num]['diffmoney'] 	= $rtnData['money'] - $rtnData['recmoney'];//未到货金额
					$orderArr[$num]['ordersn'] 		= $ordersn;
					$num++;
				}
				$data 	= array("orderArr"=>$orderArr, "totalMoney"=>$totalMoney, "totalRecMoney"=>$totalRecMoney);
			}
		}
		return $data;
	}
	
	/**
	 * 获取某个订单的总金额和已到货金额(订单状态:正常订单)
	 */
	public static function getOrderSnTotalMoney($ordersn){
		self::initDB();
		$sql 		= "SELECT purcount, purprice, actualcount FROM ph_receipt_goods WHERE ordersn = '{$ordersn}' AND order_stu = 1";
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= array();
		$money      = 0;
		$recmoney   = 0;
		if($query){
			$data = self::$dbConn->fetch_array_all($query);
			if(!empty($data)){
				foreach($data as $k => $v){
					$purcount   	= $v['purcount'];
					$actualcount 	= $v['actualcount'];
					$purprice 		= $v['purprice'];
					$money         += $purcount * $purprice;//订单总额
					$recmoney      += $actualcount * $purprice;//到货金额
				}
			}
		}
		$rtnData['money'] 		= $money;
		$rtnData['recmoney'] 	= $recmoney;
		return $rtnData;
	}

	//根据id获取收货主体信息 add by wangminwei 2014-04-08
	public static function getReceiptGoodsById($id){
		self::initDB();
		$sql    	= "SELECT * FROM  `ph_receipt_goods` WHERE id = '{$id}' ";
		$query    	= self::$dbConn->query($sql);
		$dataInfo   = array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_one($query);
		}
		return $dataInfo;
	}

	//根据id获取收货明细信息 add by wangminwei 2014-04-08
	public static function getReceiptGoodsDetailById($id){
		self::initDB();
		$sql    	= "SELECT * FROM  `ph_receipt_goods_detail` WHERE rid = '{$id}' ORDER BY id ASC";
		$query    	= self::$dbConn->query($sql);
		$dataInfo   = array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
		}
		return $dataInfo;
	}

	//采购员添加收货主体信息
	public static function add($dataArr){
		self::initDB();
		foreach($dataArr as $k => $data){
			$purmoney 	= $data['purcount'] * $data['purprice'];
			$purtime    = strtotime($data['purtime']);
			$nowTime    = time();
			$adduser    = $_SESSION['userCnName'];
			$sql  = "INSERT INTO ph_receipt_goods(ordersn, parnter, sku, purcount, purprice, purmoney, cguser, ";
			$sql .= "purnote, purtime, addtime) VALUES ";
			$sql .= "('{$data['ordersn']}', '{$data['parnter']}', '{$data['sku']}', '{$data['purcount']}', '{$data['purprice']}', ";
			$sql .= "'{$purmoney}', '{$adduser}', '{$data['purnote']}', '{$purtime}', '{$nowTime}')";
			$query	= self::$dbConn->query($sql);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}

	//根据采购订单编号获取采购订单号
	public static function getOrderSn($idArr){
		self::initDB();
		$rtnInfo    = array();
		$sql 		= "SELECT recordnumber FROM ph_order WHERE id IN({$idArr})";
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_array_all($query);
		if(!empty($rtnData)){
			$rtnInfo = $rtnData;
		}
		return $rtnInfo;
	}
	//采购员添加收货主体信息,订单号自动填充
	public static function autoAdd($ordersn){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback 	= false;
		$rtnData 	= self::getOrderInfo($ordersn);
		foreach($rtnData as $k => $data){
			$purtime    = $data['purtime'];
			$adduser    = $_SESSION['userCnName'];
			$nowTime    = time();
			$sql    	= "SELECT COUNT(*) AS total FROM ph_receipt_goods WHERE ordersn = '{$data['recordnumber']}' AND sku = '{$data['sku']}'";
			$query  	= self::$dbConn->query($sql);
			$rtnData 	= self::$dbConn->fetch_one($query);
			if($rtnData['total'] == 0){
				$inert  = "INSERT INTO ph_receipt_goods(ordersn, parnter, sku, purcount, purprice, cguser, ";
				$inert .= "purnote, purtime, addtime, paymethod) VALUES ";
				$inert .= "('{$data['recordnumber']}', '{$data['company_name']}', '{$data['sku']}', '{$data['count']}', '{$data['price']}', ";
				$inert .= "'{$adduser}', '{$data['note']}', '{$purtime}', '{$nowTime}', '{$data['paymethod']}')";
				$query	= self::$dbConn->query($inert);
				if(!$query){
					$rollback = true;
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
	//仓库人员添加收货信息
	public static function addDetail($dataArr){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback 	= false;
		$adduser    = $_SESSION['userCnName'];
		foreach($dataArr as $k => $data){
			$rectime    = strtotime($data['rectime']);
			$insert 	= "INSERT INTO ph_receipt_goods_detail(rid, incount, intime, innote, adduser) VALUES ";
			$insert    .= "('{$data['rid']}', '{$data['incount']}', '{$rectime}', '{$data['innote']}', '{$adduser}')";
			$query		= self::$dbConn->query($insert);
			if(!$query){
				$rollback = true;
			}
			$upd 		= "UPDATE ph_receipt_goods SET actualcount = '{$data['actualcount']}' WHERE id = '{$data['rid']}'";
			$updquery   = self::$dbConn->query($upd);
			if(!$updquery){
				$rollback = true;
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
	//删除收货信息记录
	public static function delete($data){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback 		= false;
		$idArr 			= implode(',', $data);
		$del   			= "DELETE FROM ph_receipt_goods WHERE id IN ($idArr)";
		$delDetail 		= "DELETE FROM ph_receipt_goods_detail WHERE rid IN ($idArr)";
		$query			= self::$dbConn->query($del);
		$queryDetail	= self::$dbConn->query($delDetail);
		if(!$query){
			$rollback = true;
		}
		if(!$queryDetail){
			$rollback = true;
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}

	//财务审核收货信息记录
	public static function auit($data, $paytime, $payaway, $fee){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback 		= false;
		$paytime        = strtotime($paytime);
		$auituser       = $_SESSION['userCnName'];
		$nowTime        = time();
		$totalNum       = count($data);
		$num            = 1;
		foreach($data as $k => $v){
			$paymethod   	= '';
			switch($payaway){
				case '1':
					$paymethod = '支付宝';
					break;
				case '2':
					$paymethod = '银行';
					break;
				case '3':
					$paymethod = '现金';
					break;
				default:
					$paymethod = '';
					break;
			}
			if($num == $totalNum){
				$upd   		= "UPDATE ph_receipt_goods SET status = 2, purmoney = '{$v['paymoney']}', paymethod = '{$paymethod}', paytime = '{$paytime}', fee = '{$fee}', auituser = '{$auituser}', auittime = '{$nowTime}' WHERE id = '{$v['id']}'";
			}else{
				$upd   		= "UPDATE ph_receipt_goods SET status = 2, purmoney = '{$v['paymoney']}', paymethod = '{$paymethod}', paytime = '{$paytime}', auituser = '{$auituser}', auittime = '{$nowTime}' WHERE id = '{$v['id']}'";
			}
				$query		= self::$dbConn->query($upd);
			if(!$query){
				$rollback = true;
			}
			$num++;
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}

	//获取采购订单详情
	public static function getOrderInfo($ordersn){
		self::initDB();
		$sql 		= "SELECT a.recordnumber, a.addtime, a.note, a.paymethod, b.sku, b.price, b.count, ";
		$sql       .= "c.company_name FROM ph_order AS a ";
		$sql       .= "JOIN ph_order_detail AS b ON a.id = b.po_id ";
		$sql       .= "JOIN ph_partner AS c ON a.partner_id = c.id ";
		$sql       .= "WHERE a.recordnumber = '{$ordersn}' ORDER BY a.id ASC";
		$query    	= self::$dbConn->query($sql);
		$rtnData    = array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				$num = 0;
				foreach($dataInfo as $k => $v){
					$rtnData[$num]['addtime'] 				= date('Y-m-d', $v['addtime']);
					$rtnData[$num]['note']    				= !empty($v['note']) ? $v['note'] : '';
					$rtnData[$num]['recordnumber']          = $v['recordnumber'];
					$rtnData[$num]['sku']           		= $v['sku'];
					$rtnData[$num]['price']					= $v['price'];
					$rtnData[$num]['count']					= $v['count'];
					$rtnData[$num]['company_name']			= $v['company_name'];
					$rtnData[$num]['paymethod']				= $v['paymethod'];
					$rtnData[$num]['purtime']               = $v['addtime'];
					$num++;
				}
			}
		}
		return $rtnData;
	}

	//更新到货入库数量记录
	public static function editDetail($mainid, $detailid, $beforecount, $aftercount){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback 		= false;
		$diffcount  	= $beforecount - $aftercount;//调整后相差数量
		$upddetail 		= "UPDATE ph_receipt_goods_detail SET incount = '{$aftercount}' WHERE id = '{$detailid}'";
		$detailquery    = self::$dbConn->query($upddetail);
		if(!$detailquery){
			$rollback = true;
		}
		$sql 			= "SELECT actualcount FROM ph_receipt_goods WHERE id = '{$mainid}'";
		$str 			= self::$dbConn->query($sql);
		$rtnData 		= self::$dbConn->fetch_one($str);
		$actualcount 	= $rtnData['actualcount'];
		$truecount      = $actualcount - $diffcount;
		$updmain        = "UPDATE ph_receipt_goods SET actualcount = '{$truecount}' WHERE id = '{$mainid}'";//更新实收数量
		$querymain      = self::$dbConn->query($updmain);
		if(!$querymain){
			$rollback = true;
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}

	//收货管理报表导出 add by wangminwei 2014-04-09
	public static function exportData($condition){
		self::initDB();
		$orderby    = " ORDER BY ordersn, sku ";
		$sql 		= "SELECT * FROM ph_receipt_goods WHERE 1 = 1 ";
		$sql       .= $condition;
		$sqlstr     = $sql.$orderby;
		$query    	= self::$dbConn->query($sqlstr);
		$dataInfo   = array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
		}
		return $dataInfo;
	}

	//根据料号返回料号描述
	public static function getSkuName($sku){
		self::initDB();
		$sql 		= "SELECT goodsName FROM pc_goods WHERE sku = '{$sku}' ";
		$query    	= self::$dbConn->query($sql);
		$dataInfo   = array();
		if($query){
			$dataInfo 	= self::$dbConn->fetch_one($query);
		}
		return $dataInfo;
	}

	//根据采购订单明细ID判断是否已存在收货管理表
	public static function isExistReceiptSku($id){
		self::initDB();
		$rtnData   	= array();
		$sql 		= "SELECT recordnumber, sku, count, price FROM ph_order_detail WHERE id = '{$id}'";
		$query 		= self::$dbConn->query($sql);
		if($query){
			$rtnData = self::$dbConn->fetch_one($query);
		}
		return $rtnData;
	}

	//采购订单修改时更新订货价格及订货数量到收货管理表 add by wangminwei 2014-04-14
	public static function updReceiptGoodsInfo($id){
		self::initDB();
		$data 	= self::isExistReceiptSku($id);
		if(!empty($data)){
			$sql     = "SELECT COUNT(*) AS total FROM ph_receipt_goods WHERE ordersn = '{$data['recordnumber']}' AND sku = '{$data['sku']}'";
			$query   = self::$dbConn->query($sql);
			$num 	 = self::$dbConn->fetch_one($query);
			if($num > 0){
				$upd 		= "UPDATE ph_receipt_goods SET purcount = '{$data['count']}', purprice = '{$data['price']}' WHERE ordersn = '{$data['recordnumber']}' AND sku = '{$data['sku']}'";
				$updquery 	= self::$dbConn->query($upd);
			}
		}
	}

	//异常到货记录页面加载时重新计算待确认数量,仅限于状态为未处理 add by wangminwei 2014-04-17
	public static function updUnusualSkuConfirmQty($id, $qty){
		self::initDB();
		$upd 	= "UPDATE ph_sku_reach_record SET amount = '{$qty}' WHERE id = '{$id}'";
		self::$dbConn->query($upd);
	}

	//判断请求的订单号和料号是否存在 add by wangminwei 2014-04-21
	public static function isExistOrderSku($ordersn, $sku){
		self::initDB();
		$sysUserId 	= $_SESSION['sysUserId'];//登录ID
		$userCnName = $_SESSION['userCnName'];//登录中文名
		$sql   		= "SELECT COUNT(*) AS total FROM ph_order AS a JOIN ph_order_detail AS b ON a.id = b.po_id ";
		$sql  	   .= "WHERE a.recordnumber = '{$ordersn}' AND b.sku = '{$sku}' AND a.is_delete = 0 AND b.is_delete = 0 ";
		if($userCnName != '王民伟'){
			$sql .= "AND purchaseuser_id = '{$sysUserId}'";
		}
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_one($query);
		return $rtnData['total'];
	}

	//请求的订单号和料号存在返回对应的已入库数量 add by wangminwei 2014-04-21
	public static function rtnOrderSkuStockQty($ordersn, $sku){
		self::initDB();
		$dataInfo   = array();
		$sql   		= "SELECT a.addtime, b.stockqty, c.company_name FROM ph_order AS a JOIN ph_order_detail AS b ON a.id = b.po_id ";
		$sql       .= "JOIN ph_partner AS c ON a.partner_id = c.id ";
		$sql   	   .= "WHERE a.recordnumber = '{$ordersn}' AND b.sku = '{$sku}' AND a.is_delete = 0 AND b.is_delete = 0 ";
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_one($query);
		if(!empty($rtnData)){
			$dataInfo['addtime'] 		= date('Y-m-d', $rtnData['addtime']);
			$dataInfo['stockqty'] 		= $rtnData['stockqty'];
			$dataInfo['company_name'] 	= $rtnData['company_name'];
		}
		return $dataInfo;
	}

	//根据订单号和料号获取满足订单料号迁入的信息(订单状态:在途) add by wangminwei 2014-04-21
	public static function getMoveOrdeSkuInfo($ordersn, $sku){
		self::initDB();
		$dataInfo   = array();
		$sql  		= "SELECT a.recordnumber, a.addtime, b.id, b.sku, b.count, b.stockqty, c.company_name FROM ph_order AS a JOIN ph_order_detail AS b ON a.id = b.po_id ";
		$sql       .= "JOIN ph_partner AS c ON a.partner_id = c.id ";
		$sql 	   .= "WHERE a.recordnumber != '{$ordersn}' AND a.status = 3 AND b.sku = '{$sku}' AND b.count != b.stockqty AND a.is_delete = 0 AND b.is_delete = 0 ";
		$query 	    = self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_array_all($query);
		if(!empty($rtnData)){
			$num = 0;
			foreach ($rtnData as $k => $v) {
				$dataInfo[$num]['detailid']		= $v['id'];
				$dataInfo[$num]['recordnumber'] = $v['recordnumber'];
				$dataInfo[$num]['addtime']		= date('Y-m-d', $v['addtime']);
				$dataInfo[$num]['sku']			= $v['sku'];
				$dataInfo[$num]['count']		= $v['count'];
				$dataInfo[$num]['stockqty']		= $v['stockqty'];
				$dataInfo[$num]['company_name'] = $v['company_name'];
				$num++;
			}
		}
		return $dataInfo;
	}

	//更新迁入订单到货数量及订单状态
	public static function updMoveOrderStatusAndInfo($dataArr, $ordersn, $sku, $stockqty, $outAmount){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback   	= false;
		$dataInfo   	= array();
		$nowTime    	= time();
		$userCnName 	= $_SESSION['userCnName'];//登录中文名
		$markcount  	= 0;
		$tmpstockqty 	= $stockqty;
		foreach($dataArr as $k => $v){
			$diffamount     = $stockqty - $markcount;
			$recordnumber   = $v['recordnumber'];
			$detailid 		= $v['detailid'];
			$incount 		= $v['incount'];//未迁入前已入库数量
			$putInCount  	= $v['putincount'];//迁入数量
			$currCount      = $incount + $putInCount;//迁入后的已入库数量
			$upd 			= "UPDATE ph_order_detail SET stockqty = '{$currCount}' WHERE id = '{$detailid}' AND is_delete = 0";
			$rtnUpd 		= self::$dbConn->query($upd);
			if(!$rtnUpd){
				$rollback = true;
			}
			//记录迁移日志
			$insert  = "INSERT INTO ph_order_sku_move(move_out_ordersn, move_sku, move_out_beforecount, move_out_count, move_in_ordersn, move_user, move_in_beforecount, move_in_count, move_time) VALUES ";
			$insert .= "('{$ordersn}', '{$sku}', '{$diffamount}', '{$putInCount}', '{$recordnumber}', '{$userCnName}', '{$incount}', '{$putInCount}', '{$nowTime}')";
			$rtnIn   = self::$dbConn->query($insert);
			if(!$rtnIn){
				$rollback = true;
			}
			$stockqty  = $stockqty - $markcount;
			$markcount = $putInCount;
			$rtnMsg  = self::serOrderCompleteStatus($recordnumber);
			if($rtnMsg == 'complete'){
				$rtnStatus = self::updOrderStatus($recordnumber, '4');//订单状态修改为已完成入库
				if(!$rtnStatus){
					$rollback = true;
				}
			}
		}
		/***处理迁入的订单料号 扣除已到货数量迁出的部份 Start ***/
		$curAmount 	= $tmpstockqty - $outAmount;//迁出后的数量
		$up 		= "UPDATE ph_order_detail SET stockqty = '{$curAmount}' WHERE recordnumber = '{$ordersn}' AND sku = '{$sku}' AND is_delete = 0";
		$rtnUp      = self::$dbConn->query($up);
		if(!$rtnUp){
			$rollback = true;
		}
		$rtnStu = self::updOrderStatus($ordersn, '3');//更新订单为在途状态
		if(!$rtnStu){
			$rollback = true;
		}
		/***处理迁入的订单料号 End ***/
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}

	//检索订单是否已完成入库
	public static function serOrderCompleteStatus($recordnumber){
		self::initDB();
		$sign  	= 'complete';
		$sql 	= "SELECT b.count, b.stockqty FROM ph_order AS a JOIN ph_order_detail AS b ON a.id = b.po_id WHERE a.recordnumber = '{$recordnumber}' AND a.is_delete = 0 AND b.is_delete = 0";
		$query 	= self::$dbConn->query($sql);
		if($query){
			$rtnData = self::$dbConn->fetch_array_all($query);
			if(!empty($rtnData)){
				foreach($rtnData as $k => $v){
					$count 		= $v['count'];
					$stockqty 	= $v['stockqty'];
					if($count != $stockqty){
						$sign = 'uncomplete';
						break;
					}
				}
			}else{
				$sign = 'null';
			}
		}
		return $sign;
	}

	//更新采购订单状态
	public static function updOrderStatus($ordersn, $status){
		self::initDB();
		if($status == 3){
			$finishtime = 0;
		}
		if($status == 4){
			$finishtime = time();
		}
		$upd 		= "UPDATE ph_order SET status = '{$status}', finishtime = '{$finishtime}' WHERE recordnumber = '{$ordersn}' AND is_delete = 0";
		$rtnUpd 	= self::$dbConn->query($upd);
		return $rtnUpd;
	}

	//更新实收数量
	public static function updActualQty(){
		self::initDB();
		$sql = "SELECT id FROM ph_receipt_goods";
		$query = self::$dbConn->query($sql);
		if($query){
			$rtnData = self::$dbConn->fetch_array_all($query);
			if(!empty($rtnData)){
				foreach($rtnData as $k => $v){
					$id  = $v['id'];
					$str = "SELECT SUM(incount) AS sumnum FROM ph_receipt_goods_detail WHERE rid = '{$id}'";
					$qrt = self::$dbConn->query($str);
					$rtn = self::$dbConn->fetch_one($qrt);
					$sumnum = $rtn['sumnum'];
					if($sumnum > 0){
						$upd = "UPDATE ph_receipt_goods SET actualcount = '{$sumnum}' WHERE id = '{$id}'";
						self::$dbConn->query($upd);
					}
				}
			}
		}
	}
	
	//更新收货管理表订单料号状态
	public static function updReceiptStatus($orderId, $orderstu){
		self::initDB();
		$upd 		= "UPDATE ph_receipt_goods SET order_stu = '{$orderstu}' WHERE id = '{$orderId}'";
		$rtnUpd 	= self::$dbConn->query($upd);
		return $rtnUpd;
	}
	
	//批量更新收货管理表订单料号状态
	public static function batchUpdStatus($orderId, $orderstu){
		self::initDB();
		$upd 		= "UPDATE ph_receipt_goods SET order_stu = '{$orderstu}' WHERE id IN {$orderId}";
		$rtnUpd 	= self::$dbConn->query($upd);
		return $rtnUpd;
	}
	
	/**
	 * 新品迁移为海外仓预警
	 *
	 */
	public static function moveOverSeaSku($skuArr){
		if(!empty($skuArr)){
			self::initDB();
			self::$dbConn->begin();//开启事物
			$rollback   = false;
			foreach($skuArr as $k => $v){
				$sku 	= $v['sku'];
				$purId 	= $v['purId'];
				$rtn = self::isExistOverSeaSku($sku);
				if($rtn){
					$rtnResult = self::insertOverSeaSku($sku);
					if(!$rtnResult){
						$rollback = true;
					}
				}
				$rtnLog = self::insertOverSeaSkuMoveLog($sku, $purId);
				if(!$rtnLog){
					$rollback = true;
				}
				$rtnUpd = self::updOverSeaSkuCharger($sku, $purId);
				if(!$rtnUpd){
					$rollback = true;
				}
			}
			if($rollback){
				self::$dbConn->rollback();
				return false;
			}else{
				self::$dbConn->commit();
				return true;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 判断料号是否已存在预警表
	 * Enter description here ...
	 * @param $sku
	 */
	public static function isExistOverSeaSku($sku){
		self::initDB();
		$sql 	= "SELECT * FROM ow_stock WHERE sku = '{$sku}'";
		$query 	= self::$dbConn->query($sql);
		if($query){
			$rtnData = self::$dbConn->fetch_one($query);
			if(empty($rtnData)){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 海外仓预警表添加新品信息
	 */
	public static function insertOverSeaSku($sku){
		self::initDB();
		$insert  = "INSERT INTO ow_stock(sku, position, everyday_sale, count, onWayCount, salensend, booknums, ";
		$insert .= "virtual_stock, b_stock_cout, purchasedays, safeStockDays, cycle_days, reach_days, ";
		$insert .= "addReachtime, is_alert, out_alert, out_mark) VALUES ";
		$insert .= "('{$sku}', '', '0.00', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')";
		$rtnAdd  = self::$dbConn->query($insert);
		return $rtnAdd;
	}
	
	/**
	 * 海外仓新品迁移记录
	 */
	public static function insertOverSeaSkuMoveLog($sku, $cguserId){
		self::initDB();
		$time   = time();
		$sql    = "SELECT COUNT(*) AS totalNum FROM ow_new_sku_move WHERE sku = '{$sku}'";
		$query  = self::$dbConn->fetch_first($sql);
		$totalNum = 0;
		if(!empty($query)){
			$totalNum = $query['totalNum'];
		}
		if($totalNum == 0){
			$insert = "INSERT INTO ow_new_sku_move(sku,time, cguserId) VALUES ('{$sku}', '{$time}', '{$cguserId}')";
			$rtnAdd  = self::$dbConn->query($insert);
			return $rtnAdd;
		}else{
			$upd 		= "UPDATE ow_new_sku_move SET cguserId = '{$cguserId}' WHERE sku = '{$sku}'";
			$rtnUpd  	= self::$dbConn->query($upd);
			return $rtnUpd;
		}
		
	}
	
	/**
	 * 更新pc_goods表OverSeaSkuCharger字段
	 */
	public static function updOverSeaSkuCharger($sku, $purId){
		self::initDB();
		$upd 		= "UPDATE pc_goods SET OverSeaSkuCharger = '{$purId}' WHERE sku = '{$sku}'";
		$rtnUpd  	= self::$dbConn->query($upd);
		return $rtnUpd;
	}
	
	/**
	 * 接收仓库系统海外仓备货单复核==>同步数量到采购系统备货
	 */
	public static function receiptOverSeaUpdBOrderAmount($ordersn, $sku, $amount){
		self::initDB();
		$upd 		= "UPDATE ph_ow_order_detail SET stockqty = '{$amount}' WHERE recordnumber = '{$ordersn}' AND sku = '{$sku}' AND is_delete = 0";
		$rtnUpd  	= self::$dbConn->query($upd);
		return $rtnUpd;
	}
	
	/**
	 * 根据订单号获取订单明细
	 * Enter description here ...
	 * @param unknown_type $orderSn
	 */
	public static function getPurOrderDetailByOrdersn($orderSn){
		self::initDB();
		$data   = array();
		$sql 	= "SELECT a.*, b.goodsName, b.id as goodsId, b.goodsCost FROM `ph_order_detail` as a
					INNER JOIN `pc_goods` as b ON a.sku = b.sku
					WHERE a.recordnumber = '{$orderSn}'";
		$query	 = self::$dbConn->query($sql);
		$rtnData = self::$dbConn->fetch_array_all($query);
		if(!empty($rtnData)){
			$data = $rtnData;
		}
		return $data;
	}
	
	/**
	 * 线下订单导入方法
	 */
	public function importOrderData($orderSn, $sku, $parnter, $purcount, $purprice, $cguser, $purnote, $purtime){
		self::initDB();
		$sql 		= "SELECT COUNT(*) AS total FROM ph_receipt_goods WHERE ordersn = '{$orderSn}' AND sku = '{$sku}'";
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_array_all($query);
		$addtime    = time();
		if(!empty($rtnData)){
			$totalNum = $rtnData[0]['total'];
			if($totalNum == 0){
				$insert = "INSERT INTO ph_receipt_goods(ordersn, sku, parnter, purcount, purprice, cguser, purnote, purtime)VALUES('{$orderSn}', '{$sku}', '{$parnter}', '{$purcount}', '{$purprice}', '{$cguser}', '{$purnote}', '{$addtime}')";
				$queryInsert = self::$dbConn->query($insert);
				if($queryInsert){
					return 200;
				}else{
					return 201;
				}
			}else{
				return 202;//已存在
			}
		}else{
			return 404;
		}
	}
	
	/**
	 * 添加旧品到海外仓预警
	 */
	public function addOldSkuToOverSku($sku, $cguser){
		self::initDB();
		$sql	= "SELECT COUNT(*) AS total FROM pc_goods WHERE sku = '{$sku}'";
		$query 	= self::$dbConn->fetch_first($sql);
		$total  = $query['total'];
		if($total == 0){
			return 404;//料号不存在
		}else{
			self::$dbConn->begin();//开启事物
			$rollback   = false;
			$rtn 		= self::isExistOverSeaSku($sku);
			if($rtn){//为空时添加
				$rtnResult 	= self::insertOverSeaSku($sku);
				if(!$rtnResult){
					$rollback = true;
				}
			}
			$hasLog = self::getMoveLog($sku);
			if($hasLog == 0){
				$cguserId 	= getUserIdByTrueName($cguser);
				$rtnLog 	= self::insertOverSeaSkuMoveLog($sku, $cguserId);
				if(!$rtnLog){
					$rollback = true;
				}
			}
			$purId 	= self::getUserIdByName($cguser);
			$rtnUpd = self::updOverSeaSkuCharger($sku, $purId);
			if(!$rtnUpd){
				$rollback = true;
			}
			if($rollback){
				self::$dbConn->rollback();
				return 202;
			}else{
				self::$dbConn->commit();
				return 200;
			}
		}
	}
	/*
	 * 判断迁移到海外仓预警的料号记录是否存在
	 */
	public static function getMoveLog($sku){
		self::initDB();
		$sql 	= "SELECT COUNT(*) AS total FROM ow_new_sku_move WHERE sku = '{$sku}'";
		$query  = self::$dbConn->query($sql);
		$data   = self::$dbConn->fetch_one($query);
		return $data['total'];
	}
	
	/**
	 * 获取海外料号新品迁移记录数
	 */
	public static function getOverSkuMoveLogCount(){
		self::initDB();
		$sql 	= "SELECT COUNT(*) AS total FROM ow_new_sku_move";
		$query  = self::$dbConn->query($sql);
		$data   = self::$dbConn->fetch_one($query);
		return $data['total'];
	}
	
	/**
	 * 获取海外料号新品迁移记录信息返回更新到旧ERP系统海外采购人
	 */
	public static function getOverSkuMoveLogInfo($page, $pagenum){
		self::initDB();
		$rtnData 	= array();
		$rtnInfo    = array();
		$start      = ($page - 1) * 200;
		$pagenum    = 200;
		$sql 		= "SELECT * FROM ow_new_sku_move ";
		$sql       .= "limit $start, $pagenum ";
		$query  	= self::$dbConn->query($sql);
		$data   	= self::$dbConn->fetch_array_all($query);
		if(!empty($data)){
			$rtnData = $data;
			$mark    = 0;
			foreach($rtnData AS $k => $v){
				$sku 						= $v['sku'];
				$purId 						= $v['cguserId'];
				$cguser 					= getUserNameById($purId);
				$rtnInfo[$mark]['sku'] 		= $sku;
				$rtnInfo[$mark]['cguser'] 	= $cguser;
				$mark++;
				$upd 	= "UPDATE pc_goods SET OverSeaSkuCharger = '{$purId}' WHERE sku = '{$sku}'";
				self::$dbConn->query($upd);
			}
		}
		return $rtnInfo;
	}
	
	/**
	 * 根据名户名称获取统一ID
	 */
	public static function getUserIdByName($name){
		self::initDB();
		$gid    = 0;
		$sql	= "SELECT global_user_id FROM power_global_user WHERE global_user_name = '{$name}'";
		$query  = self::$dbConn->fetch_first($sql);
		if(!empty($query)){
			$gid = $query['global_user_id'];
		}
		return $gid;
	}
	
	/**
	 * 批量删除B仓备货单
	 */
	public static function batchDelOwOrder($pid){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback   = false;
		$id         = '';
		foreach($pid AS $idArr){
			$id .= $idArr.',';
		}
		$idList  	= "(".substr($id, 0, strlen($id) - 1).")";
		$delMain    = "DELETE FROM ph_ow_order WHERE id IN {$idList}";
		$rtnMain    = self::$dbConn->query($delMain);
		if($rtnMain === false){
			$rollback = true;
		}
		$delDetail  = "DELETE FROM ph_ow_order_detail WHERE po_id IN {$idList}";
		$rtnDetail   = self::$dbConn->query($delDetail);
		if($rtnDetail === false){
			$rollback = true;
		}
		if($rollback){
			self::$dbConn->rollback();
			return false;
		}else{
			self::$dbConn->commit();
			return true;
		}
	}
	
	/**
	 * 财务批量审核周结、月结
	 */
	public static function batchAuit($ordersn, $paytime, $payaway, $fee){
		self::initDB();
		self::$dbConn->begin();//开启事物
		$rollback   = false;
		$paytime    = strtotime($paytime);
		$totalNum   = self::getBatchAuitCount($ordersn);
		$sql 		= "SELECT id, purcount, purprice FROM ph_receipt_goods WHERE ordersn IN {$ordersn} ORDER BY ordersn, sku ";
		$query 		= self::$dbConn->query($sql);
		$rtnData 	= self::$dbConn->fetch_array_all($query);
		if(!empty($rtnData)){
			$paymethod   	= '';
			$nowTime        = time();
			$auituser       = $_SESSION['userCnName'];
			switch($payaway){
				case '1':
					$paymethod = '支付宝';
					break;
				case '2':
					$paymethod = '银行';
					break;
				case '3':
					$paymethod = '现金';
					break;
				default:
					$paymethod = '';
					break;
			}
			$num = 1;
			foreach($rtnData as $k => $v){
				$id       	= $v['id'];
				$purcount 	= $v['purcount'];
				$purprice 	= $v['purprice'];
				$money    	= $purcount * $purprice;
				if($num == $totalNum){//运费更新到最后一条记录里面
					$upd   		= "UPDATE ph_receipt_goods SET status = 2, purmoney = '{$money}', paymethod = '{$paymethod}', paytime = '{$paytime}', fee = '{$fee}', auituser = '{$auituser}', auittime = '{$nowTime}' WHERE id = '{$id}'";
				}else{
					$upd   		= "UPDATE ph_receipt_goods SET status = 2, purmoney = '{$money}', paymethod = '{$paymethod}', paytime = '{$paytime}', auituser = '{$auituser}', auittime = '{$nowTime}' WHERE id = '{$id}'";
				}
				$query		= self::$dbConn->query($upd);
				if($query === false){
					$rollback = true;
				}
				$num++;
			}
			if($rollback){
				self::$dbConn->rollback();
				return false;
			}else{
				self::$dbConn->commit();
				return true;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 获取财务批量审核周结、月结数
	 */
	public static function getBatchAuitCount($ordersn){
		$sql 		= "SELECT COUNT(*) AS totalNum FROM ph_receipt_goods WHERE ordersn IN {$ordersn}";
		$query 		= self::$dbConn->query($sql);
		$data   	= self::$dbConn->fetch_one($query);
		return 	$data['totalNum'];
	}
	
	/**
	 * 列表显示B仓库存到货立方数
	 */
	public function getOverSeaSkuVolume($condition, $page){
		self::initDB();
		$time  		= time();
		$pagenum    = 200;
		$start		= ($page - 1)* $pagenum;
		$limit 		= " ORDER by sku limit {$start}, {$pagenum}";
		$dataInfo   = array();
		$dataDetail = array();
		$totalNum   = 0;
		$data       = array();
		$sql 	    = "SELECT a.sku, a.goodsName, a.goodsLength, a.goodsWidth, a.goodsHeight, a.OverSeaSkuCharger, b.b_stock_cout, b.inBoxQty FROM pc_goods AS a JOIN ow_stock AS b ON a.sku = b.sku WHERE a.OverSeaSkuCharger != '' ";
		$sqlcount 	= "SELECT COUNT(*) as totalNum FROM pc_goods AS a JOIN ow_stock AS b ON a.sku = b.sku WHERE a.OverSeaSkuCharger != '' ";
		$sqlcount  .= $condition;
		$sql       .= $condition;
		$sqlstr     = $sql.$limit;
		$query    	= self::$dbConn->query($sqlstr);
		if($query){
			$dataInfo 		= self::$dbConn->fetch_array_all($query);
			$totalData 		= self::$dbConn->fetch_first($sqlcount);
			$totalNum       = $totalData['totalNum'];
			$data 			= array("totalNum"=>$totalNum,"goodsInfo"=>$dataInfo);
		}
		return $data;
	}
	
	/**
	 * 获取总立方数
	 */
	public function getTotalVolume(){
		self::initDB();
		$sql 	    	= "SELECT a.sku, a.goodsLength, a.goodsWidth, a.goodsHeight, b.b_stock_cout, b.inBoxQty FROM pc_goods AS a JOIN ow_stock AS b ON a.sku = b.sku WHERE a.OverSeaSkuCharger != '' ";
		$query    		= self::$dbConn->query($sql);
		$totalVolume 	= 0;
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				foreach($dataInfo as $k => $v){
					$sku    	= $v['sku'];
					$length 	= $v['goodsLength'];
					$width  	= $v['goodsWidth'];
					$height 	= $v['goodsHeight'];
					$stock  	= $v['b_stock_cout'];
					$inboxqty 	= $v['inBoxQty'];
					$totalVolume += $length * $width * $height * ($stock + $inboxqty);
				}
			}
			$totalVolume = round($totalVolume / 1000000, 3);
		}
		return $totalVolume;
	}
	
	/**
	 * 根据关键字获取可能匹配到的采购员编号
	 */
	public function getCguserArrId($name){
		global $dbconn;
		$sql 		= "SELECT global_user_id FROM power_global_user WHERE global_user_name LIKE '%{$name}%'";
		$sql 		= $dbconn->execute($sql);
		$parArr 	= $dbconn->getResultArray($sql);
		return $parArr;
	}
	
	/**
	 * 导出立方报表
	 */
	public function exportOverSeaSkuVolume($condition){
		self::initDB();
		$rtnData    = array();
		$sql 	    = "SELECT a.sku, a.goodsName, a.goodsLength, a.goodsWidth, a.goodsHeight, a.OverSeaSkuCharger, b.b_stock_cout, b.inBoxQty FROM pc_goods AS a JOIN ow_stock AS b ON a.sku = b.sku WHERE a.OverSeaSkuCharger != '' ";
		$sql       .= $condition;
		$query    	= self::$dbConn->query($sql);
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				$rtnData = $dataInfo;
			}
		}
		return $rtnData;
	}
	
	//实收数量大于订货数量
	public function exportRec(){
		self::initDB();
		$rtnData    = array();
		$sql 	    = "SELECT * FROM `ph_receipt_goods` WHERE `actualcount` > `purcount`";
		$query    	= self::$dbConn->query($sql);
		if($query){
			$dataInfo 	= self::$dbConn->fetch_array_all($query);
			if(!empty($dataInfo)){
				$rtnData = $dataInfo;
			}
		}
		return $rtnData;
	}

	/***
	 *采购订单修改供应商映射到收货管理表
	 *name:wangminwei
	 *time:2014-09-10
	 */
	public function updReceiptSupplier($ordersn, $supplierId){
		self::initDB();
		$sql           = "SELECT COUNT(*) AS totalNum FROM ph_receipt_goods WHERE ordersn = '{$ordersn}'";
		$query         = self::$dbConn->query($sql);
		$data          = self::$dbConn->fetch_one($query);
		$totalNum      = $data['totalNum'];
		if($totalNum > 0){
			$sql 		= "SELECT company_name FROM ph_partner WHERE id = '{$supplierId}'";
			$query		= self::$dbConn->query($sql);
			$data  		= self::$dbConn->fetch_one($query);
			if(!empty($data)){
				$parName = $data['company_name'];
				$upd     = "UPDATE ph_receipt_goods SET parnter = '{$parName}' WHERE ordersn = '{$ordersn}'";
				self::$dbConn->query($upd);
			}
		}
	}
}
?>
