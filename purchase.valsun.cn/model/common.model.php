<?php
/**
 * 类名：CommonModel
 * 功能：采购通用调用model方法
 * 版本：1.0
 * 日期：2013/11/11
 * 作者：管拥军
 */
 
class CommonModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	
	//初始化db类
	public static function	initDB(){
		global $dbConn;
		self::$dbConn	= $dbConn;
	}
	
	/**
	 * CommonModel::getSkuInfo()
	 * 获取某个sku的代发货，实际库存等详细数据
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuInfo1($sku){
		$paramArr = array(
				'method' => 'purchase.erp.sku.info.get',  // 获取旧系统sku 信息
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
				'sku'	=> $sku,
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		return $skuInfo;
	}
	
	/**
	 * CommonModel::getSkuStockqty()
	 * 获取某个sku的实际库存
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuStockqty($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'wh.getSkuStock',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr);
		unset($paramArr);		
		$skuInfo	= json_decode($skuInfo, true);
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuFirstSaleTime()
	 * 获取某个sku的第一次销售时间
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuFirstSaleTime($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getfirstsale',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		$skuInfo	= json_decode($skuInfo, true);
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuLastSaleTime()
	 * 获取某个sku的最后一次销售时间
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuLastSaleTime($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getlastsale',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		echo $skuInfo;
		$skuInfo	= json_decode($skuInfo, true);
		print_r($skuInfo);
		exit;
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuSalensend()
	 * 获取某个sku的代发货数量
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuSalensend($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getsaleandnosendall',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		echo $skuInfo;
		$skuInfo	= json_decode($skuInfo, true);
		print_r($skuInfo);
		exit;
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
			
	/**
	 * CommonModel::getSkuInterceptnums()
	 * 获取某个sku的拦截数量
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuInterceptnums($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getinterceptall',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		echo $skuInfo;
		$skuInfo	= json_decode($skuInfo, true);
		print_r($skuInfo);
		exit;
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuAutointerceptnums()
	 * 获取某个sku的自动拦截数量
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuAutointerceptnums($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.autointercept',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		echo $skuInfo;
		$skuInfo	= json_decode($skuInfo, true);
		print_r($skuInfo);
		exit;
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuAuditingnums()
	 * 获取某个sku的审核数量
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuAuditingnums($sku){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getauditingall',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sku'	=> $sku,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		$skuInfo	= json_decode($skuInfo, true);
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getSkuSaleProducts()
	 * 获取某个sku的审核数量
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuSaleProducts($startTime, $endTime, $sku, $everyday_sale){
		$paramArr = array(
			/* API系统级输入参数 Start */
				'method' => 'order.system.getSaleProducts',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'start'	=> $startTime,
				'end'	=> $endTime,
				'sku'	=> $sku,
				'everyday_sale'	=> $everyday_sale,
			/* API应用级输入参数 End*/
		);
		$skuInfo	= callOpenSystem($paramArr,'local');
		unset($paramArr);
		echo $skuInfo;
		$skuInfo	= json_decode($skuInfo, true);
		print_r($skuInfo);
		exit;
		$res 		= is_array($skuInfo) ? $skuInfo['data'] : 0;
		return $res;
	}
	
	/**
	 * CommonModel::getPurchaseList()
	 * 获取公司采购列表
	 * @return  array
	 */
	public static function getPurchaseList(){
		// self::initDB();//保留待用
		// $sql 	= "SELECT global_user_id,userName FROM `power_global_user` WHERE global_user_is_delete=0 AND  `global_user_job` IN(SELECT `job_id` FROM `power_job` WHERE `job_name` like '%采购%')";
		// $query	= self::$dbConn->query($sql);
		// if ($query) {
			// $res	= self::$dbConn->fetch_array_all($query);
			// return $res;
		// } else {
			// self::$errCode	= 10000;
			// self::$errMsg	= "获取数据失败";
			// return false;
		// }
		$paramArr	= array(
			/* API系统级输入参数 Start */
				'method' => 'power.new.getApiPurchaseUsers.get',  //API名称
				'format' => 'json',  //返回格式
					 'v' => '1.0',   //API版本号
			'username'	 => C('OPEN_SYS_USER'),
			/* API系统级参数 End */				 
			/* API应用级输入参数 Start*/
				'sysName'	=> C('AUTH_SYSNAME'),
				'sysToken'	=> C('AUTH_SYSTOKEN'),
			/* API应用级输入参数 End*/
		);
		$res		= callOpenSystem($paramArr);
		$res		= json_decode($res, true);
		$res		= is_array($res) ? $res : array();
		unset($paramArr);
		return $res;
	}
	
	/**
	 * CommonModel::getPartnerList()
	 * 获取采购供应商列表
	 * @param string $uids 采购们统一ID
	 * @return  array
	 */
	public static function getPartnerList($uids){
		self::initDB();
		$condition	= empty($uids) ? "" : "purchaseuser_id IN($uids) AND";
		$sql 	= "SELECT id,company_name FROM ph_partner WHERE $condition is_delete = 0";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}

	/**
	 * CommonModel::getSkuPartner()
	 * 根据sku获取供应商列表
	 * @param string $sku 料号
	 * @return  array
	 */
	public static function getSkuPartner($sku){
		self::initDB();
		$sql 	= "SELECT
					b.id,
					b.company_name
					FROM
					pc_goods_partner_relation AS a
					INNER JOIN ph_partner AS b ON a.partnerId = b.id
					WHERE sku = '{$sku}'";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}

	/**
	 * CommonModel::getPartnerSkuList()
	 * 获取某个供应商提供的全部料号
	 * @param int $id 供应商ID
	 * @return  array
	 */
	public static function getPartnerSkuList($id){
		global $dbConn;
		self::initDB();
		$sql = "select company_name from ph_partner where id={$id}";
		$sql = $dbConn->execute($sql);
		$company_name = $dbConn->fetch_one($sql);
		$sql = "SELECT id from ph_partner where company_name='{$company_name['company_name']}'";
		$sql = $dbConn->execute($sql);
		$idArr = $dbConn->getResultArray($sql);
		$data = array();
		foreach($idArr as $item){
			$data[] = $item['id'];
		}
		$idStr = implode(",",$data);
		$sql 	= "SELECT sku FROM pc_goods_partner_relation WHERE partnerId in ({$idStr})";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * CommonModel::getPurchaseAccess()
	 * 获取某个采购用户的细颗粒权限
	 * @param int $uid 统一用户ID 
	 * @return  array
	 */
	public static function getPurchaseAccess($uid){
		self::initDB();
		$sql 	= "SELECT * FROM ph_purchases_access WHERE user_id = {$uid}";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 10001;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * CommonModel::getSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @param string $sku 待用
	 * @return string
	 */
	public static function getSkuImg($skuArr,$size){
		$skuJson = json_encode($skuArr);
		$paramArr= array(
			/* API系统级输入参数 Start */
			//'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			//'method'	=> 'datacenter.picture.getSpuAllSizePic',  //API名称
			'method'	=> 'datacenter.picture.getPicBySkuArr',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> C('OPEN_SYS_USER'),
			/* API系统级参数 End */
			/* API应用级输入参数 Start*/
			'sku'		=> $skuJson,  //主料号
			'size'      =>  $size, 
			'picType'	=> 'G', 
			/* API应用级输入参数 End*/
		);
		$data 	= callOpenSystem($paramArr);
		return $data;
	}	

	/*
		获取超大订单
	*/
	public static function getBigOrders($purid){
		/*
		$paramArr= array(
			'method'	=> 'erp.get.bigOrders',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号/
			'username'  => C('OPEN_SYS_USER'),
			'pusername' => $purchaseName
		);
		*/
		//print_r($paramArr);
		$paramArr= array(
			'method'	 => 'om.showSuperOrder',  //API名称
			'format'	 => 'json',  //返回格式
			'v'			 => '1.0',   //API版本号/
			'username'   => C('OPEN_SYS_USER'),
			'purchaseId' => $purid
		);

		$data 	= callOpenSystem($paramArr);
		$data 	= json_decode($data, true);
        return $data;
	}	

	/**
	*獲取用戶基础信息
	*/
	public static function getUserInfo($loginName){
		self::initDB();
		$sql = "SELECT * FROM `power_global_user` WHERE global_user_login_name='{$loginName}'";
		//echo $sql;
		$query = self::$dbConn->query($sql);
		$rtn  = self::$dbConn->fetch_array($query);
		return $rtn;
	}


	/*
	 *获取sku 基础信息
	 * */

	public function getSKUInfo($sku){
		self::initDB();
		$sql = "select * from pc_goods where sku='{$sku}' and is_delete=0 ";
		$query = self::$dbConn->query($sql);
		$rtn  = self::$dbConn->fetch_array($query);
		return $rtn;
	}

	/**
	 * CommonModel::getPartnerIdBySku()
	 * 根据SKU获取供应商ID
	 * @param string $sku 料号
	 * @return 供应商ID
	 * add by wangminwei 2013-11-13
	 */
	public static function getPartnerIdBySku($sku){
		self::initDB();
		//$sql 	= "SELECT partnerId FROM pc_goods_partner_relation WHERE sku = '{$sku}'";
		$sql 	= "SELECT partnerId FROM ph_user_partner_relation WHERE sku = '{$sku}'";
		$query	= self::$dbConn->query($sql);
		if ($query) {
			$rtn	= self::$dbConn->fetch_array_all($query);
			if(!empty($rtn)){
				return $rtn[0]['partnerId'];
			}else{
				return false;
			}
		} else {
			self::$errCode	= 10000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 *CommonModel::categoryName($path)
	 * 获取产品类别名
	 * @param string $path 类别路径
	 * @return string
	 */
	public static function categoryName($path){
		self::initDB();
		$sql = "SELECT name FROM ".C('DB_PREFIX')."goods_category_pc WHERE is_delete = 0 AND path = '{$path}' LIMIT 1 ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if(empty($ret[0])){
				self::$errMsg = "无数据";
				return false;
			}
			return $ret[0]['name'];
		}
		self::$errMsg = "获取类别失败";
		return false;
	}	
	
	/**
	 * CommonAct::act_getCategoryInfo($pid)
	 * 获取产品本地某类别下的所有产品
	 * @param string $pid 类别id
	 * @return string
	 * @author wxb
	 * @date 2013/11/13
	 */
	public static function getCategoryInfo($pid){
		self::initDB();
		$sql = "SELECT id,name FROM ".C('DB_PREFIX')."goods_category_pc WHERE is_delete=0 and pid={$pid} ";
		$query = self::$dbConn->query($sql);
		if($query){
			$ret = self::$dbConn->fetch_array_all($query);
			if(empty($ret[0])){
				self::$errMsg = "无该类别列表";
				return false;
			}
			return $ret;
		}
		self::$errMsg = "获取类别列表失败";
		return false;
	}
}
?>
