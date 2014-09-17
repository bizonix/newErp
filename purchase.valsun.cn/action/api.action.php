<?php

/**
* 类名：ApiAct
* 功能：对外提供各种采购系统数据api
* 版本：1.0
* 日期：2013/09/07
* 作者：温小彬
*/


class ApiAct{
	static $errCode = 0;
	static $errMsg = "";

	/**
	 *功能：提交特殊运输的所有信息
	 */
	public function act_getAdjustransport(){
		$ret = ApiModel::getAdjustransport();
		self::$errCode = ApiModel::$errCode;
		self::$errMsg = ApiModel::$errMsg;
		return $ret;
	}

	/**
	 * ApiAct::act_getAuthCompanyList()
	 * 获取鉴权公司列表memcache
	 * @return  array
	 */
	public function act_getAuthCompanyList(){
		$cacheName 		= md5("purchase_auth_company_list");
		$memc_obj		= new Cache(C('CACHEGROUP'));
		$companyInfo 	= $memc_obj->get_extral($cacheName);
		if (!empty($companyInfo)) {
			return unserialize($companyInfo);
		} else {
			$companyInfo = ApiModel::getAuthCompanyList();
			$isok 		   = $memc_obj->set_extral($cacheName, serialize($companyInfo));
			if (!$isok) {
				self::$errCode = 308;
				self::$errMsg = 'memcache缓存出错!';
				//return false;
			}
			return $companyInfo;
		}
    }
	
	/**
	 * 说明：从产品中心获取产品类别中文名 支持get 传参 或者方法直接传参（优先后者）
	 * @param $goodsCategory 从goods表里取得的产品类型
	 * @return  $categoryName
     * pc.getCategoryInfoByPath
	 */
	public  function act_categoryName($goodsCategory){
// 		return "test";
        if(!empty($goodsCategory)){
	        $path                     = $goodsCategory;
        }else{
        	$path                     = trim($_GET['goodsCategory']);
        }
        if(empty($path)){
        	self::$errCode = '002';
        	self::$errMsg = '传参非法';
        	return false;
        }
        $paramArr['method']      = 'pc.getCategoryInfoByPath';  //API名称
        $paramArr['username']    = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']      = 'json';  //数据格式
        $paramArr['v']      	  = '1.0';  //版本号
		$paramArr['path']        = $path;
        $data                     = callOpenSystem($paramArr,"local");
        $data = json_decode($data,true);
        if(empty($data['data']['name'])){//接口无返回数据
        	$name = GoodsAct::act_getCategoryName($path);
        	if(empty($name)){
        		return "无类别";
        	}
        	return $name;
        }
        return $data['data']['name'];
	}


    /**
     * 以下为封装的采购系统提供API接口
     */
   	public function act_getPurchaseOrderList(){
        $jsonArr = $_GET;
        //return $jsonArr;
  		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg  = '参数数组为空';
			return false;
		}
        $key    = isset($jsonArr['key']) ? trim($jsonArr['key']) : '';
        $type   = isset($jsonArr['type']) ? trim($jsonArr['type']) : '';
        $status = isset($jsonArr['status']) ? trim($jsonArr['status']) : '';
        $addTime_start   = isset($jsonArr['addTime_start']) ? trim($jsonArr['addTime_start']) : '';
        $addTime_end     = isset($jsonArr['addTime_end']) ? trim($jsonArr['addTime_end']) : '';
        $auditTime_start = isset($jsonArr['auditTime_start']) ? trim($jsonArr['auditTime_start']) : '';
        $auditTime_end   = isset($jsonArr['auditTime_end']) ? trim($jsonArr['auditTime_end']) : '';
        $page            = isset($jsonArr['page']) ? $jsonArr['page']: 1;

        /*
        $debug = false;
        if($key=='debug'){
            $debug = true;
            $key = '';
        }*/

        $table = "`ph_order` as a LEFT JOIN `ph_order_detail` as b ON a.id = b.po_id";
        $field = 'DISTINCT a.id';
        $condition = ' WHERE 1';
        if($key != '') {
            if($type == 1) {
                $condition .= " AND a.recordnumber = '$key' ";
            } else if($type == 2) {
                $sku = $key;
                $condition .= " AND c.sku = '$sku' ";
                $table = "`ph_order` as a LEFT JOIN `ph_order_detail` as b ON a.id = b.po_id LEFT JOIN `pc_goods` as c ON b.sku_id = c.id ";
             }
        }
        if($status != '') {
            if($status == 0) {
                $condition .= " AND a.status < '4' ";
            } else if($status == 1) {
                $condition .= " AND a.status = '4' ";
            }
        }

        if($addTime_start != ''){
             $condition .= " AND a.addtime >= '$addTime_start' ";
        }
        if($addTime_end  != ''){
             $condition .= " AND a.addtime < '$addTime_end' ";
        }
        if($auditTime_start  != ''){
             $condition .= " AND a.aduittime >= '$auditTime_start' ";
        }
        if($auditTime_end  != ''){
             $condition .= " AND a.aduittime < '$auditTime_end' ";
        }
        //if($debug) return json_encode(array($table, $field, $condition));
        $totalrow  = OmAvailableAct::act_getTNameCount($table,$condition);

    	$pagesize 	= 100;//每页显示条数
		$pageindex  = $page;
		$limit      = "limit ".($pageindex-1)*$pagesize.",$pagesize";
        $condition .= " ORDER BY a.addtime DESC ".$limit;
        $resultList = OmAvailableAct::act_getTNameList($table, $field, $condition);

        $resultArray = array();
        foreach($resultList as $order) {
            $orderId  = $order['id'];
            $ret_main = OmAvailableAct::act_getTNameList('ph_order as a LEFT JOIN `ph_partner` as b ON a.partner_id = b.id', 'a.*,b.username as partnerName', " WHERE a.id = $orderId ");
            $ret_sub  = OmAvailableAct::act_getTNameList('`ph_order_detail` as a LEFT JOIN pc_goods as b ON a.sku_id = b.id', 'a.*,b.sku', " WHERE a.po_id = $orderId ");
            $ret_main[0]['detail']= $ret_sub;
            $resultArray[]=  $ret_main[0];
        }
		$datalist[0]= $totalrow;
		$datalist[1]= $resultArray;
		return json_encode($datalist);
    }




     public function act_getPartnerList() {
        $id  = isset($_GET['id']) ? trim($_GET['id']) : '';
        $company_name   = isset($_GET['company_name']) ? trim($_GET['company_name']) : '';
        $username = isset($_GET['user_name']) ? trim($_GET['user_name']) : '';
        $type_id        = isset($_GET['type_id']) ? trim($_GET['type_id']) : '';
        $tel   = isset($_GET['tel']) ? trim($_GET['tel']) : '';
        $phone  = isset($_GET['phone']) ? trim($_GET['phone']) : '';
        $e_mail   = isset($_GET['e_mail']) ? trim($_GET['e_mail']) : '';
        $QQ = isset($_GET['QQ']) ? trim($_GET['QQ']) : '';
        $AliIM   = isset($_GET['AliIM']) ? trim($_GET['AliIM']) : '';
        $status   = isset($_GET['status']) ? trim($_GET['status']) : '';
        $purchaseuser_id   = isset($_GET['purchaseuser_id']) ? trim($_GET['purchaseuser_id']) : '';
        $company_id   = isset($_GET['company_id']) ? trim($_GET['company_id']) : '';
        $page   = isset($_GET['page']) ? trim($_GET['page']) : 1;
        $pageSize   = isset($_GET['pageSize']) ? trim($_GET['pageSize']) : 0;

        $condition = 'WHERE 1 ';
        if($id != '') {
            $condition .= " AND id = '$id' ";
        }
        if($company_name != '') {
            $condition .= " AND company_name = '$company_name' ";
        }
        if($username != '') {
            $condition .= " AND username = '$username' ";
        }
        if($type_id != '') {
            $condition .= " AND type_id = '$type_id' ";
        }
        if($tel != '') {
            $condition .= " AND tel = '$tel' ";
        }
        if($phone != '') {
            $condition .= " AND phone = '$phone' ";
        }
        if($e_mail != '') {
            $condition .= " AND e_mail = '$e_mail' ";
        }
        if($QQ != '') {
            $condition .= " AND QQ = '$QQ' ";
        }
        if($AliIM != '') {
            $condition .= " AND AliIM = '$AliIM' ";
        }
        if($status != '') {
            $condition .= " AND status = '$status' ";
        }
        if($purchaseuser_id != '') {
            $condition .= " AND purchaseuser_id = '$purchaseuser_id' ";
        }
        if($company_id != '') {
            $condition .= " AND company_id = '$company_id' ";
        }
        $totalrow  = OmAvailableAct::act_getTNameCount('ph_partner',$condition);
        if($totalrow == 0) {
            $datalist[0]= 0;
    		$datalist[1]= '';
    		return json_encode($datalist);
        }
        if($pageSize != 0) {
    		$pageindex  = $page;
    		$limit      = " limit ".($pageindex-1)*$pagesize.",$pagesize";
            $condition .= $limit;
        }

        $ret = OmAvailableAct::act_getTNameList('ph_partner', '*', $condition);
        if(empty($ret)) {
            $datalist[0]= 0;
    		$datalist[1]= '';
    		return json_encode($datalist);
        }
       	$datalist[0]= count($ret);
		$datalist[1]= $ret;
		return json_encode($datalist);

    }

    function act_getPartnerById() {
	    $id = isset ($_GET['id']) ? post_check(trim($_GET['id'])) : ''; //sku
		if (intval ($id) == 0) {
			self :: $errCode = 101;
			self :: $errMsg = 'id不合法';
			return false;
		}

        $key = 'purchase_partner_'.$id;
        global $memc_obj;
        $ret = $memc_obj->get_extral($key);
        if($ret) {
            self :: $errCode = '200';
			self :: $errMsg = '成功Mem';
            return json_encode($ret);
        } else {
            $tName = 'ph_partner';
            $select = '*';
            $where = "WHERE is_delete=0 and id=$id";
    		$categoryList = OmAvailableModel :: getTNameList($tName, $select, $where);
    		if (count($categoryList)) {
    		    self :: $errCode = '200';
    			self :: $errMsg = '成功';
                $memc_obj->set_extral($key, $categoryList[0], 0);
    			return json_encode($categoryList[0]);
    		} elseif(count($categoryList) == 0){
                self :: $errCode = '201';
    			self :: $errMsg = '没有该id的类别信息';
    			return array();
    		}else {
    			self :: $errCode = '404';
    			self :: $errMsg = '数据库操作错误';
    			return false;
    		}
    	  }
    }

    //提供采购系统到货入库API接口
    function act_processPurchaseOrder(){
     	$sku = isset($_GET['sku']) ? trim($_GET['sku']) : '';
        $num = isset($_GET['num']) ? trim($_GET['num']) : '';
        if($sku == '' || $num == '') {
			self :: $errCode = 101;
			self :: $errMsg  = '参数不合法';
			return false;
		}
		$now_num = $num;
		$purchase_list = self::checkPurchaseSkuIsExist($sku); //api拉取采购订单
        BaseModel::begin();//开始事务
        $rollback   = false;
		if(!empty($purchase_list)){
            /*** 计算采购成本算法 Start step1 获取原有库存成本***/
            $rtnData        = ApiModel::getQtyAndPriceBySku($sku);//返回料号未入库的库存及成本单价
            $stockmoney     = 0;
            $before_qty     = 0;
            $before_price   = 0;
            $skumoney       = 0;
            $skuqty         = 0;
            if(!empty($rtnData)){
                $before_qty     = $rtnData[0]['stock_qty'];
                $before_price   = $rtnData[0]['goodsCost'];
                $stockmoney     = $before_price * $before_qty;//未入库前库存成本
            }
            /*** 计算采购成本算法 End step1 获取原有库存成本***/
			foreach($purchase_list as $purchase){
				if($now_num < 1){
					break;
				}
				$orderId   = $purchase['id'];
				$sku_id    = $purchase['sku_id'];
				$lessnum   = $purchase['count'] - $purchase['stockqty'];
                $price     = $purchase['price'];//采购成本
                $adduserid = $purchase['purchaseuser_id'];//采购员编号
				if($lessnum > 0){
					if($now_num < $lessnum){
						$total_num  = $purchase['stockqty'] + $now_num;
						$ret        = OmAvailableAct::act_updateTNameRow("ph_order_detail", "SET stockqty = '$total_num'", "WHERE po_id = '$orderId' AND sku_id = '$sku_id'");
	     				if($ret === false) {
							self :: $errCode = 405;
							self :: $errMsg  = '更新采购订单料号数量出错！';
							$rollback        = false;
						} else {
							$now_num = 0;
						}
					}else{
						$totalCount = $purchase['count'];
                        $reach_time = time();
						$ret        = OmAvailableAct::act_updateTNameRow("ph_order_detail", "SET stockqty = '$totalCount', reach_time = '{$reach_time}'", "WHERE po_id = '$orderId' AND sku_id = '$sku_id'");
	  					if($ret === false) {
							self :: $errCode = 406;
							self :: $errMsg  = '更新采购订单料号数量出错！';
							$rollback        = false;
						}else{
							$now_num = $now_num - $lessnum;
                            /*** 计算采购成本算法 Start step2 获取订单明细表已到货完成的料号***/
                                $skumoney += $price * $purchase['count'];//采购订单中单个料号的采购总金额
                                $skuqty   += $purchase['count'];
                            /*** 计算采购成本算法 End step2 获取订单明细表已到货完成的料号***/
						}
					}
					//检测订单是否完结
					$otherskus = self::getOrderDetailsById($orderId);
					$status    = true;
					foreach ($otherskus AS $othersku){
						if($othersku['count']!=$othersku['stockqty']){
							$status	= false;
							break;
						}
					}
					if($status){
						$ret = OmAvailableAct::act_updateTNameRow("ph_order", "SET status = '4'", "WHERE id = '$orderId'");
			            if($ret === false) {
			                self :: $errCode = 407;
			    			self :: $errMsg  = '更新采购订单状态出错！';
			    			$rollback        = false;
			            }
					}
				}
			}
            /*** 计算采购成本算法 Start step3 重新计算料号采购成本***/
                $totalmoney = $stockmoney + $skumoney;
                $totalqty   = $before_qty + $skuqty;
                $newprice   = round($totalmoney / $totalqty, 2);//新的料号采购成本
                $rtnApiData = self::updatePcApiPrice($sku, $newprice, $adduserid);
                $errorCode  = $rtnApiData['errCode'];
                if($errCode != 200){
                    $rollback = true;
                }
                if($rollback == false){
                    BaseModel::commit();
                    BaseModel::autoCommit();
                }else{
                    BaseModel::rollback();
                    BaseModel::autoCommit();
                }
            /*** 计算采购成本算法 End step3 重新计算料号采购成本***/
		}
        return $now_num;
    }

    //API更新产品中心料号成本价格
    function updatePcApiPrice($sku, $price, $adduserid){
        $paramArr['method']         = 'pc.updateCostAndAddHistory'; // API名称
        $paramArr['username']       = 'purchase'; // 开放系统用户名
        $paramArr['format']         = 'json'; // 数据格式
        $paramArr['v']              = '1.0'; // 版本号
        $paramArr['sku']            = $sku;
        $paramArr['purchaseCost']   = $price;
        $paramArr['addUserId']      = $adduserid;
        $data                       = callOpenSystem($paramArr, "local");
        $rtndata                    = json_decode($data,true);
        return $rtndata;
    }

    function checkPurchaseSkuIsExist($sku){
        $table = " `ph_order` as a LEFT JOIN `ph_order_detail` as b ON a.id = b.po_id LEFT JOIN `pc_goods` as c ON b.sku_id = c.id ";
        $field = " a.id, a.purchaseuser_id, a.recordnumber,b.sku_id,c.sku,b.count,b.stockqty, b.price ";
        $condition = " WHERE c.sku = '$sku' AND a.status = 3 order by id asc";
        $ret = OmAvailableAct::act_getTNameList($table, $field, $condition);
        return $ret;
    }

    function getOrderDetailsById($orderId){
        $purOrderId = isset($orderId) ? trim($orderId) : '';
        if ($purOrderId == '') {
			self :: $errCode = 101;
			self :: $errMsg  = '参数有误！';
			return false;
		}
        $table = "`ph_order_detail`";
        $field = " * ";
        $condition = " WHERE po_id = '$purOrderId' AND is_delete = '0' ";
        $ret = OmAvailableAct::act_getTNameList($table, $field, $condition);
		return $ret;
    }

    function act_updateSkuCountInStock(){
        $orderId  = isset($_GET['orderId']) ? trim($_GET['orderId']) : '';
        $sku_id   = isset($_GET['sku_id']) ? trim($_GET['sku_id']) : '';
        $stockqty = isset($_GET['stockqty']) ? trim($_GET['stockqty']) : '';
        $status   = isset($_GET['status']) ? trim($_GET['status']) : '';
        if($orderId == '') {
			self :: $errCode = 101;
			self :: $errMsg  = '参数有误！';
			return false;
		}
        if($status != '') {
            $ret = OmAvailableAct::act_updateTNameRow("ph_order", "SET status = '4'", "WHERE id = '$orderId'");
            if($ret === false) {
                self :: $errCode = 102;
    			self :: $errMsg  = '更新采购订单状态出错！';
    			return false;
            }
        }
        if($sku_id != '' && $stockqty != '') {

        	$ret = OmAvailableAct::act_updateTNameRow("ph_order_detail", "SET stockqty = '$stockqty'", "WHERE po_id = '$orderId' AND sku_id = '$sku_id'");
	        if($ret === false) {
	            self :: $errCode = 103;
				self :: $errMsg  = '更新采购订单料号数量出错！';
				return false;
	        }
        }

        self :: $errCode = 0;
		self :: $errMsg  = '更新成功！';
        return json_encode(array(true));
    }



    /**
     * 功能：通过接口获取某一个产品类别信息 支持js调用和后台调用
    * 以下为封装的调用PC系统API的方法
    */
   function act_getCategoryInfo($pid=''){
   			
   		if(empty($pid)){
	        $pid = $_GET['pid'];
   		}
		$paramArr['method']= 'pc.getCategoryInfoByPid'; // API名称
		$paramArr['username']= C ( 'OPEN_SYS_USER' ); // 开放系统用户名
		$paramArr['format']= 'json'; // 数据格式
		$paramArr['v']= '1.0'; // 版本号
		$paramArr['pid']= $pid;
		$data = json_decode ( callOpenSystem($paramArr,"local"),true);
		if ($data['data']) {
			self::$errCode = "200";
			self::$errMsg = " 获取类别成功";
			return $data['data'];
		} else {
			self::$errCode = "417";
			self::$errMsg = " 无相应子类别 ";
			return false;
		}
    }

   function act_getCategoryById($id) {
		$paramArr['method']= 'pc.getCategoryInfoById'; // API名称
		$paramArr['username']= C ( 'OPEN_SYS_USER' ); // 开放系统用户名
		$paramArr['format']= 'json'; // 数据格式
		$paramArr['v']= '1.0'; // 版本号
		$paramArr['id']= $id;
		$data = callOpenSystem ( $paramArr, "local" );
		return $data['data'];
	}
	function act_getCategoryList($pid) {
		$paramArr['method']= 'pc.getCategoryInfoByPid'; // API名称
		$paramArr['username']= C ( 'OPEN_SYS_USER' ); // 开放系统用户名
		$paramArr['format']= 'json'; // 数据格式
		$paramArr['v']= '1.0'; // 版本号
		$paramArr['pid']= $pid;
		$data = callOpenSystem ( $paramArr, "local" );
		return $data['data'];
	}
	
	/**
	 * 以下为封装的调用仓库系统API的方法
	 */
		// 调用API更新异常订单更新状态
	function update_unusualOrderSataus($purid, $oid, $category, $recordnumber) {
		$paramArr['method']= 'wh.operatUnusualOrder'; // API名称
		$paramArr['username']= C ( 'OPEN_SYS_USER' ); // 开放系统用户名
		$paramArr['format']= 'json'; // 数据格式
		$paramArr['v']= '1.0'; // 版本号
		$paramArr['purid']= $purid;
		// print_r($oid);
		$paramArr['oid']= base64_encode ( $oid );
		// $paramArr['type']= 'unusualOrderSataus';
		$paramArr['category']= $category;
		$paramArr['recordnumber']= $recordnumber;
		$data = callOpenSystem ( $paramArr, "local" );
		$data = json_decode($data,true);
		return $data['data'];
	}
		
		// 从仓库获取异常到货列表
	function act_getUnusualList() {
		$starttime = isset ( $_GET['instock_startTime']) ? $_GET['instock_startTime']: ''; // date("Y-m-d");//,'1354294861');
		$endtime = isset ( $_GET['instock_endTime']) ? $_GET['instock_endTime']: ''; // date("Y-m-d");//,'1375290061');
		$sku = isset ( $_GET['sku']) ? $_GET['sku']: '';
// 	$purid = isset ( $_GET['purid']) ? $_GET['purid']: '';
		$isconfirm = isset ( $_GET['isconfirm']) ? $_GET['isconfirm']: ''; // 待点货确认,已确认待处理
		$status = isset ( $_GET['status']) ? $_GET['status']: ''; // 处理结果: 采购已补单,取消订单,调整为零,二次录入
		$parnterid = isset ( $_GET['parnterid']) ? $_GET['parnterid']: '';
		$page = isset ( $_GET['page']) ? $_GET['page']: '1';
		
		$res			= CommonAct::actGetPurchaseAccess(); //获取所属下的采购id
		if (empty($res['power_ids'])) {
			$uids		= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		} else {
			$uids		= $res['power_ids'];
		}
		$purid  = $uids;
		$paramArr['method']= 'wh.getUnusualOrderList'; // API名称
		$paramArr['username']= C ( 'OPEN_SYS_USER' ); // 开放系统用户名
		$paramArr['format']= 'json'; // 数据格式
		$paramArr['v']= '1.0'; // 版本号
		$paramArr['abStatus']= $status;
		$paramArr['isConfirm']= $isconfirm;
		$paramArr['startTime']= !empty($starttime) ? strtotime($starttime." 00:00:00") : '';
		$paramArr['endTime']= !empty($endtime) ? strtotime($endtime." 23:59:59") : '';
		$paramArr['page']= $page;
		$paramArr['sku']= $sku;
        $paramArr['tracktime']= "on";
		$paramArr['purid']= base64_encode($purid);
        $start = time();
		//$unusualOrder = callOpenSystem($paramArr, "local");
        $unusualOrder = callOpenSystem($paramArr);
        print_r($unusualOrder);
        $end = time();
        $consumetime = $end-$start ;
        echo "consumetime:".$consumetime;
		return $unusualOrder;
    }

    /**
    * 以下为封装的调用QC系统API的方法
    */
    function act_getBadGoodsList(){

  		$timetype	= isset($_GET['timetype']) ? $_GET['timetype']: '';
		$starttime	= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00 "): '';//,'1354294861');
		$endtime	= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59 "): '';//,'1375290061');
		$sku 		= isset($_GET['sku']) ? $_GET['sku']: '';
		$purid 		= isset($_GET['purid']) ? $_GET['purid']: '1';
		$status		= isset($_GET['status']) ? $_GET['status']: '';
		$page       = isset($_GET['page']) ? $_GET['page']: '1';

        $paramArr['method']      = 'qc.search.defectiveProducts.get';  //API名称
        $paramArr['username']    = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']      = 'json';  //数据格式
        $paramArr['v']      	  = '1.0';  //版本号
		$paramArr['timetype']    = $timetype;
		$paramArr['startTime']   = $starttime;
		$paramArr['endTime']     = $endtime;
		$paramArr['sku']         = $sku;
        $paramArr['purid']       = $purid;
        $paramArr['status']	  = $status;
		$paramArr['page']        = $page;
        $data                     = callOpenSystem($paramArr,"local");
        return $data;
    }

    //处理不良品API操作更新到QC系统==scrappedStatus 1为报废，2为内部处理，3为待退回
    //qc.apiUpdateDefectiveProductsAudit  退回，报废，和内部处理的接口 type=1   退回,  type=2   报废, type=3   内部处理
    //id,type,userId
    function update_qcBadGoodData($defectiveId, $infoId, $num, $note, $scrappedStatus){

        $paramArr['method']          = 'qc.afterAuditDefPros';  //API名称
        $paramArr['username']    	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']            = 'json';  //数据格式
        $paramArr['v']      	             = '1.0';  //版本号
  		$paramArr['defectiveId']	 = $defectiveId;//编号
		$paramArr['infoId']		         = $infoId;//记录号
		$paramArr['num']               = $num;//处理数量
		$paramArr['note']			     = $note;//备注
		$paramArr['scrappedStatus'] = $scrappedStatus;//状态
        $data                                       = callOpenSystem($paramArr,"local");
        $data                                       = json_decode($data,true);
        self::$errCode = $data['errCode'];
        self::$errMsg = $data['errMsg'];
        return $data['data'];
    }

    //处理待定列表API操作更新到QC系统==type==>修改图片、回测、退回
    //qc.update.pendingProductStatus
    //id,infoId,pendingNum,userId,status
    function update_qcPendGoodData($id, $type){

        $paramArr['method']         = 'qc.update.pendingProductStatus';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['pendingId']	     = $id;//编号
    	$paramArr['status']	         = $type;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //处理退回列表API操作更新到QC系统 采购审核
    function update_qcReturnGoodData($id){

        $paramArr['method']       = '';  //API名称
  		$paramArr['returnId']	   = $id;//编号
        $paramArr['username']     = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']       = 'json';  //数据格式
        $paramArr['v']      	   = '1.0';  //版本号
        $data                      = callOpenSystem($paramArr,"local");
        return $data['data'];
    }



    //以下为封装的调用订单系统API的方法

    //取SKU第一次售出时间
    function get_firstSaleTime($sku){

        $paramArr['method']         = 'purchase.erp.getfirstsaletime';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'firstSaleTime';
    	$paramArr['sku']	         = $sku;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];

    }

    //取SKU最近一次售出时间
    function get_lastSaleTime($sku){

        $paramArr['method']         = 'purchase.erp.getlastsaletime';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'lastSaleTime';
    	$paramArr['sku']	         = $sku;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //取SKU时间段内销售数量
    function get_saleNum($start1, $end1, $sku, $warehouse_id, $everyday_sale){

        $paramArr['method']         = 'purchase.erp.getsalenum';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'saleNum';
    	$paramArr['sku']	         = $sku;
		$paramArr['startTime']      = $start1;
		$paramArr['endTime']	 	 = $end1;
		$paramArr['warehouseid']	 = $warehouse_id;
		$paramArr['everydaysale']	 = $everyday_sale;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //待发货数量
    function get_waitSendNum($sku, $warehouse_id){

        $paramArr['method']         = 'purchase.erp.getwaitsendnum';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'waitSendNum';
    	$paramArr['sku']	         = $sku;
		$paramArr['warehouseid']	 = $warehouse_id;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];

    }

    //自动拦截数量
    function get_autointerceptNum($sku, $warehouse_id){
        $paramArr['method']         = 'purchase.erp.getautointerceptnum';  //API名称
		$paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'autointercetpNum';
    	$paramArr['sku']	         = $sku;
		$paramArr['warehouseid']	 = $warehouse_id;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //取拦截数量
    function get_interceptallNum($sku, $warehouse_id){
        $paramArr['method']         = 'purchase.erp.getinterceptallnum';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'interceptallNum';
    	$paramArr['sku']	         = $sku;
    	$paramArr['warehouseid']	 = $warehouse_id;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //待审核数量
    function get_auditingallNum($sku, $warehouse_id){
        $paramArr['method']         = 'purchase.erp.getauditingallnum';  //API名称
        $paramArr['username']   	 = C('OPEN_SYS_USER');  //开放系统用户名		
        $paramArr['format']     	 = 'json';  //数据格式
        $paramArr['v']      	  	 = '1.0';  //版本号
    	$paramArr['type']	         = 'auditingallNum';
    	$paramArr['sku']	         = $sku;
    	$paramArr['warehouseid']	 = $warehouse_id;
        $data                        = callOpenSystem($paramArr,"local");
        return $data['data'];
    }

    //以下为封装的调用图片系统API的方法
    public static function act_getAllPicInfo($sku,$spu,$picType) {
//     	return "test";
        if($sku == '' || $spu == ''){
            return '';
        }
        $data  = self::act_getAllPic($spu,$picType);
        $url = isset($data['data']['artwork']) ? $data['data']['artwork'][$sku][0]: '';
        return $url;
    }

   	public static function act_getAllPic($spu,$picType) {
		if(empty($spu)) {
			$spu	= strlen(htmlentities($_REQUEST['spu'],ENT_QUOTES)) > 0 ? htmlentities($_REQUEST['spu'],ENT_QUOTES) : '';
		}
		if(empty($picType)) {
			$picType	= strlen(htmlentities($_REQUEST['picType'],ENT_QUOTES)) > 0 ? htmlentities($_REQUEST['picType'],ENT_QUOTES) : '';
		}
		$errStr = '';
		if(empty($spu)) {
			$errStr .= '料号输入错误！<br />';
		}
		if(empty($picType)) {
			$errStr .= '站点输入错误！<br />';
		}
        if(strlen($spu) == 1) {
            $spu = '00'.$spu;
        }
        if(strlen($spu) == 2) {
            $spu = '0'.$spu;
        }
		if(!empty($errStr)) {
			self::$errCode = '001';
			self::$errMsg = $errStr;
			return false;
		}
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'spu'		=> $spu,  //主料号
			'picType'	=> $picType, //站点
			/* API应用级输入参数 End*/
		);
		$data = callOpenSystem($paramArr);
		$data = json_decode($data,true);
        return $data;
	}
}
?>