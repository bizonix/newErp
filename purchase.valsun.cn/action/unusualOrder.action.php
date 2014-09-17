<?php
/**
 * 类名： UnusualOrderAct
 * 功能： 异常采购订单
 * 版本： 1.0
 * 日期： 2013/08/08
 * 作者： 王民伟
 */
class UnusualOrderAct{
	static $errCode	=	0;
	static $errMsg	=	"";

	/**
	 *功能:API获取返回异常到货采购单
	 *@param $purid 只获取采购自己的料号
	 *日期:2013/08/07
	 *作者:王民伟
	 */
	public static function act_getUnusualOrderList($purid, $condition, $page){
		$rtn = get_unusualOrder($purid, $condition, $page);
		return $rtn;
	}

	/**
	 *功能:处理异常采购订单，例：采购补单、二次录入
	 *@param $type 处理类型
	 *@param $dataarray 料号 数量
	 *@param $purid 采购员编号
	 *日期:2013/08/05
	 *作者:王民伟
	 */
	public static function handleUnusualOrder(){
		$checkPur = UnusualOrderAct::checkPower();
		if($checkPur !== true){
			self::$errMsg = '无权限操作';
			return false;
		}
		$type 		= $_GET['type'];
		$dataarray 	= $_GET['datalist'];
		$purid 	   	= $_GET['purid'];
		global $dbConn;
		$errArr = array();
		if($type == 'patchOrder'){//不能重复补单
			if(!empty($dataarray)){
				$dbConn->begin();//开启事物
				$datalist = explode('@', $dataarray);
				$datanum  = count($datalist);
				for ($i = 0; $i < $datanum; $i++) {
					$infolist = explode('*', $datalist[$i]);
					if(count($infolist) == 3){
						$oid     	= $infolist[0];
						$oidlist 	= "('".$oid."')";
						$sku 		= $infolist[1];
						$amount 	= $infolist[2];
						$skulist[] 	= $sku;
						$rtn_infolist = PurchaseOrderModel::getPurSkuInfo($skulist, $purid);//过滤不是自己负责的SKU,并返回所需数据
						unset($skulist);
						if(empty($rtn_infolist)){
							$errArr[] = $sku.'不属于你负责的sku';
						}else{//符合条件开始补单
							$info_list_num = count($rtn_infolist);
							for($i = 0; $i < $info_list_num; $i++){
								$detailinfo       = array();
								$rtn_sku 		  = $rtn_infolist[$i]['sku'];//返回SKU
								$rtn_partner_id   = $rtn_infolist[$i]['partnerid'];//返回供应商编号
								$rtn_price        = $rtn_infolist[$i]['goodsCost'];//返回采购单价
								$isExistPatchOrdersn = PurchaseOrderModel::isExistPatchOrdersn($rtn_partner_id, $purid);//查找条件符合的补单号???
								if(empty($isExistPatchOrdersn)){//不存在符合的补单号
									//此次需判断生成订单跟踪号前缀---0:SWB;1:FZ;2:ZG
									$recordnumber	= PurchaseOrderModel::autoCreateOrderSn($purid,0);//生成采购补单号
									$maininfo['recordnumber'] 		= $recordnumber;//订单号
									$maininfo['purchaseuser_id'] 	= $purid;//采购员编号
									$maininfo['partner_id'] 		= $rtn_partner_id;//供应商编号
									$maininfo['company_id'] 		= 1;//公司编号
									$rtn_mainorder = PurchaseOrderModel::insertPatchMainOrder($maininfo);//添加补单主体信息
									if($rtn_mainorder){//主体添加成功
										$detailinfo['sku_id'] = PurchaseOrderModel::getSkuIdBySku($rtn_sku);//根据sku获SKU编号
										$detailinfo['price']  = $rtn_price;//采购单价
										$detailinfo['count']  = $amount;//补单数量
										$detailinfo['sku']  = $sku;//sku
										$poid = PurchaseOrderModel::getOrderIdByNum($recordnumber);
										$rtn_detailorder = PurchaseOrderModel::insertPatchDetailOrder($poid, $detailinfo);
										if($rtn_detailorder){
											$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'patchorder', $recordnumber);//通过API更新状态
											if($rtn_api){
												$dbConn->commit();
											}else{
												$dbConn->rollback();
												$errArr[] =  $sku.'仓库数据修改失败';
											}	
										}else{
											$dbConn->rollback();
											$errArr[] = $sku.'详情增加失败';
										}
									}else{
										$dbConn->rollback();
										$errArr[]  = $sku.'主订单增加失败';
									}
									//self::$errMsg = 'success';
								}else{//存在符合条件的补单号
									$detailinfo['sku_id'] = PurchaseOrderModel::getSkuIdBySku($rtn_sku);//根据sku获SKU编号
									$detailinfo['price']  = $rtn_price;//采购单价
									$detailinfo['count']  = $amount;//补单数量
									$detailinfo['sku']  = $sku;//sku
									$poid 			 = PurchaseOrderModel::getOrderIdByNum($isExistPatchOrdersn);//根据跟踪号取编号
									$rtn_detailorder = PurchaseOrderModel::insertPatchDetailOrder($poid, $detailinfo);//添加采购订单明细
									if($rtn_detailorder){
										$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'patchorder', $isExistPatchOrdersn);//通过API更新状态
										if($rtn_api){
											$dbConn->commit();
										}else{
											$dbConn->rollback();
											$errArr[] =  $sku.'仓库数据修改失败';
										}	
									}else{
										$dbConn->rollback();
										$errArr[] =  $sku.'详情增加失败';
									}
								}
							}
						}
					}
				}
			}else{
				$errArr[] =  '未传入sku数据';
			}
			if(count($errArr)>0){
				$errStr = implode("<br/>",$errArr);
				self::$errMsg = $errStr;
			}else{
				self::$errCode = "111";
			}
			
		}else if($type == 'secondStockIn'){//二次补录
			if(!empty($dataarray)){
				$datalist = explode('@', $dataarray);
				$datanum  = count($datalist);
				for ($i = 0; $i < $datanum; $i++) {
					$infolist = explode('*', $datalist[$i]);
					if(count($infolist)==3){
						$oid    = $infolist[0];
						$oidlist = "('".$oid."')";
						$sku 	= $infolist[1];
						$amount = $infolist[2];
						$skuid   = PurchaseOrderModel::getSkuIdBySku($sku);
						if(empty($skuid)){
							$errArr[] = $sku."不存在库存中";
							continue;
						}
						$rtnlist = PurchaseOrderModel::checkStockInOrder($skuid);
						if(!empty($rtnlist)){ //过滤找不到的，不再参与入库操作，否则会继续生成未订单
							$rtn 	 = PurchaseOrderAct::act_stockIn($sku,$amount);
							$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'secondstockin', 'no');
							if(!$rtn_api){
								$errArr[] = $sku."数据提交仓库系统失败";
								break;
							}
						}else{
							$errArr[] = $sku."未找到二次录入符合条件";
						}
					}
				}
			}else{
				$errArr[] = "传参有误";
			}
			if(count($errArr)>0){
				self::$errMsg = implode("<br/>",$errArr);
				return false;
			}
			self::$errCode = "111";
			return true;
		}else if($type == 'setZero'){
			if(!empty($dataarray)){
				$dataarray_num = count($dataarray);
				for($i = 0; $i < $dataarray_num; $i++){
					$oidlist .= "'".$dataarray[$i]."',";
				}
				$oidlist = "(".substr($oidlist, 0, strlen($oidlist) - 1).")";
				$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'setzero', 'no');
				$rtn['msg'] = $rtn_api;
			}else{
				$rtn['msg'] = 'null';
			}
			return $rtn;
			exit();
		}else if($type == 'comfirmOrder'){
			if(!empty($dataarray)){
				$dataarray_num = count($dataarray);
				for($i = 0; $i < $dataarray_num; $i++){
					$oidlist .= "'".$dataarray[$i]."',";
				}
				$oidlist = "(".substr($oidlist, 0, strlen($oidlist) - 1).")";
				$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'comfirmorder', 'no');
				$rtn['msg'] = $rtn_api;
			}else{
				$rtn['msg'] = 'null';
			}
			return $rtn;
		}else if($type == 'backOrder'){//订单退货
			$dataStr = trim($_GET['dataStr']);
			$order_type = trim($_GET['order_type']);
			$dbConn->begin();
			if(!empty($dataarray) && !empty($dataStr) && !empty($order_type)){
				$dataarray_num = count($dataarray);
				for($i = 0; $i < $dataarray_num; $i++){
					$oidlist .= "'".$dataarray[$i]."',";
				}
				$oidlist = "(".substr($oidlist, 0, strlen($oidlist) - 1).")";
				//生成退货订单
				$datalist = explode('@', $dataStr);
				$datanum  = count($datalist);
				for ($i = 0; $i < $datanum; $i++) {
					$infolist = explode('*', $datalist[$i]);
					if(count($infolist) == 3){
						$oid     	= $infolist[0];
						$oidlist 	= "('".$oid."')";
						$sku 		= $infolist[1];
						$amount 	= $infolist[2];
						$skulist[] 	= $sku;
						$rtn_infolist = PurchaseOrderModel::getPurSkuInfo($skulist, $purid);//过滤不是自己负责的SKU,并返回所需数据
						unset($skulist);
						if(empty($rtn_infolist)){
							$errArr[] = $sku.'不属于你负责的sku';
						}else{//符合条件开始补单
							$info_list_num = count($rtn_infolist);
							for($i = 0; $i < $info_list_num; $i++){
								$detailinfo       = array();
								$rtn_sku 		  = $rtn_infolist[$i]['sku'];//返回SKU
								$rtn_partner_id   = $rtn_infolist[$i]['partnerid'];//返回供应商编号
								$rtn_price        = $rtn_infolist[$i]['goodsCost'];//返回采购单价
								$status_type =  " status = 5 AND order_type = {$order_type}";
								$isExistPatchOrdersn = PurchaseOrderModel::isExistPatchOrdersn($rtn_partner_id, $purid,$status_type);//查找条件符合的退货单???
								if(empty($isExistPatchOrdersn)){//不存在符合的单号
									//此次需判断生成订单跟踪号前缀---0:SWB;1:FZ;2:ZG
									$recordnumber	= PurchaseOrderModel::autoCreateOrderSn($purid,0);//生成采购补单号
									$maininfo['recordnumber'] 		= $recordnumber;//订单号
									$maininfo['purchaseuser_id'] 	= $purid;//采购员编号
									$maininfo['partner_id'] 		= $rtn_partner_id;//供应商编号
									$maininfo['company_id'] 		= 1;//公司编号
									$status = 5;
									$rtn_mainorder = PurchaseOrderModel::insertPatchMainOrder($maininfo,$status,$order_type);//添加主体信息
									if($rtn_mainorder){//主体添加成功
										$detailinfo['sku_id'] = PurchaseOrderModel::getSkuIdBySku($rtn_sku);//根据sku获SKU编号
										$detailinfo['price']  = $rtn_price;//采购单价
										$detailinfo['count']  = $amount;//数量
										$detailinfo['sku']  = $sku;
										$poid = PurchaseOrderModel::getOrderIdByNum($recordnumber);
										$rtn_detailorder = PurchaseOrderModel::insertPatchDetailOrder($poid, $detailinfo);
										if($rtn_detailorder){
											$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'backorder', 'no');
											if($rtn_api){
												$dbConn->commit();
											}else{
												$dbConn->rollback();
												$errArr[] =  $sku.'仓库数据修改失败';
											}
										}else{
											$dbConn->rollback();
											$errArr[] = $sku.'详情增加失败';
										}
									}else{
										$dbConn->rollback();
										$errArr[]  = $sku.'主订单增加失败';
									}
									//self::$errMsg = 'success';
								}else{//存在符合条件的补单号
									$detailinfo['sku_id'] = PurchaseOrderModel::getSkuIdBySku($rtn_sku);//根据sku获SKU编号
									$detailinfo['price']  = $rtn_price;//采购单价
									$detailinfo['count']  = $amount;//补单数量
									$detailinfo['sku']  = $sku;
									$poid 			 = PurchaseOrderModel::getOrderIdByNum($isExistPatchOrdersn);//根据跟踪号取编号
									$rtn_detailorder = PurchaseOrderModel::insertPatchDetailOrder($poid, $detailinfo);//添加采购订单明细
									if($rtn_detailorder){
										$rtn_api = ApiAct::update_unusualOrderSataus($purid, $oidlist, 'backorder', 'no');
										if($rtn_api){
											$dbConn->commit();
										}else{
											$dbConn->rollback();
											$errArr[] =  $sku.'仓库数据修改失败';
										}	
									}else{
										$dbConn->rollback();
										$errArr[] =  $sku.'详情增加失败';
									}
								}
							}
						}	
					}
				}
			}else{
				$errArr[] = "传参有误";
			}
			if(count($errArr)>0){
				self::$errMsg = implode("<br/>",$errArr);
				return false;
			}
			self::$errCode = "111";
			return true;
		}
	}
	/**
	 *通过id 获取中文名
	 *@param str $id
	 *@author wxb
	 */
	public function getNameById($id){
		$ret = UnusualOrderModel::getNameById($id);
		self::$errMsg = UnusualOrderModel::$errMsg;
		return $ret;
	}
	/**
	 *通过id 获取中文名
	 *@param str $sku
	 *@author wxb
	 */
	public function getComNameBySku($sku){
		$ret = UnusualOrderModel::getComNameBySku($sku);
		self::$errMsg = UnusualOrderModel::$errMsg;
		return $ret;
	}
	/**
	 *核查登入者是否为采购
	 *@author wxb
	 *@date 2013/11/21
	 */
	public  function checkPower(){
		$purid      = $_SESSION[C('USER_AUTH_SYS_ID')];//采购员ID
		$comid      = $_SESSION[C('USER_COM_ID')];//公司ID
		$res 		= UserCompetenceModel::showCompetence($purid);
		$purArr	    = explode(',', $res[0]['power_ids']) ;
		if(!in_array($purid, $purArr)){//过滤只能采购员才能生产采购订单
			$result['msg'] = 'noPower';
			return $result;
		}
		return true;
	}
}
?>
