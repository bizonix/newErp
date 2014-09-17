<?php
/**
 * 类名：GoodsAct
 * 功能：货品资料动作处理层
 * 版本：1.0 
 * 日期：2013/8/5
 * 作者：管拥军
 */
class GoodsAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * GoodsAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$result			= goodsModel::modList($where, $page, $pagenum);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
        return $result;
    }

	/**
	 * GoodsAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$result			= goodsModel::modListCount($where);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
        return $result;
    }
    
	/**
	 * GoodsAct::actPurchaseList()
	 * 列出所有采购人员信息
	 * @param string $where 查询条件
	 * @return array 结果集数组
	 */
 	public function actPurchaseList($where='1'){
		$result			= goodsModel::modPurchaseList($where);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
        return $result;
    }
	
	/**
	 * GoodsAct::actPurchaseDetail()
	 * 列出某个采购用户信息
	 * @param integer $id 采购ID
	 * @return string 采购名
	 */
 	public function actPurchaseDetail($id='0'){
		$result			= goodsModel::modPurchaseDetail($id);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
        return $result;
    }
	
	/**
	 * GoodsAct::act_auditSku()
	 * 返回料号审核结果
	 * @param array $idArr sku数组ID
	 * @return bool 
	 */
	public function act_auditSku(){
        $idArr			= $_GET["idArr"];
        $result			= goodsModel::auditSku($idArr);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
		return $result;
    }	

	/**
	 * GoodsAct::act_moveSku()
	 * 返回料号移交结果
	 * @param array $idArr sku数组ID
	 * @return bool 
	 */
	public function act_moveSku(){
        $idArr			= $_GET["idArr"];
		$purchase		= $_GET["purchase"];
        $result			= goodsModel::moveSku($idArr, $purchase);
		self::$errCode  = goodsModel::$errCode;
        self::$errMsg   = goodsModel::$errMsg;
		return $result;
    }
    
   	/**
	 * GoodsAct::act_getCategoryName()
	 * 获取料号分类名称 
	 * @param $path 分类path
	 * @return 分类名称 
	 */
	public function act_getCategoryName($path){
        global $memc_obj;
//         $ret =$memc_obj->set_extral('goodsCategoryx_16', 'aaaaaaaaaaaaaaa', 3600);       
//         var_dump($ret);
//         $goodsCategory = $memc_obj->get_extral("goodsCategoryx_16");
//     	var_dump($goodsCategory);
//     	exit;
        
        $categoryPath = isset($path) ? trim($path) : '';
        if($categoryPath == '') {
            return '';
        }       
        $ret = $memc_obj->get_extral("goodsCategory_".$categoryPath);  
//         var_dump("goodsCategory_".$categoryPath,$ret);
        if($ret) {        
            return $ret['name'];
        } else {
            return '';        
        } 
    }

	public function getAllCategoryName($path){
		global $dbConn;
		$sql = "SELECT * FROM  `pc_goods_category` "; 
		$sql =  $dbConn->execute($sql);
		$goodsCategory = $dbConn->getResultArray($sql);
		print_r($goodsCategory);
		return $goodsCategory;
    }



    
   	public function act_getCategoryPidMap(){
        //global $memc_obj;
        //var_dump($memc_obj); 
        $memc_obj = new Cache(C('CACHEGROUP'));      
        $categoryPid = isset($_GET['pid']) ? trim($_GET['pid']) : '';       
        if($categoryPid == '') {
            return '';
        }         
        $ret = $memc_obj->get_extral("goodsCategoryPid_".$categoryPid);      
        if($ret) {        
            return $ret;
        } else {
            return '';        
        }
    }
    
   	public function getCategoryPidMap($pid){
   	    //echo "asdasdasdasd";
        //global $memc_obj;
        //var_dump($memc_obj);
        $memc_obj = new Cache(C('CACHEGROUP'));
        $categoryPid = isset($pid) ? trim($pid) : '';       
        if($categoryPid == '') {
            return '';
        }       
        $ret = $memc_obj->get_extral("goodsCategoryPid_".$categoryPid);      
        if($ret) {        
            return $ret;
        } else {
            return '';        
        }
    }
    
   	public function getCategoryPathById($id){
   	    //echo "asdasdasdasd";
        //global $memc_obj;
        //var_dump($memc_obj);
        $memc_obj = new Cache(C('CACHEGROUP'));
        $categoryId = isset($id) ? trim($id) : '';       
        if($categoryId == '') {
            return '';
        }       
        $ret = $memc_obj->get_extral("goodsCategoryPath_".$categoryId);      
        if($ret) {        
            return $ret;
        } else {
            return '';        
        }
    }
    
    
    function act_getSkuId($sku){	
       
		$field		=  ' id ';
        $where      =  " where sku = '$sku' limit 0,1 ";      
        $result     =  OmAvailableModel::getTNameList('ph_goods', $field, $where);       
		return $result[0]['id'];
    }

	// 获取产品资料
	//edit by xiaojinhua
    function getGoodsList(){
		global $dbConn;
		$page  = isset($_GET['page']) ? $_GET['page'] : 0;
		$limit = " limit {$page},100";
		$key = $_GET["searchContent"];
		$searchtype = $_GET["searchtype"];
		$control = "";

    	//$ret = GoodsModel::getGoodsList($where,$count,$limit);
		$sqlStr = "SELECT a.* ,e.global_user_name FROM  `pc_goods` as a 
			left join power_global_user as e on a.purchaseId=e.global_user_id 
			where ( a.goodsName like '%{$key}%' or  a.sku like'%{$key}%')";
			
		//echo $sql;
		$sql = $dbConn->execute($sqlStr);
		$totalNum = $dbConn->num_rows($sql);
		$sql = $sqlStr.$limit;
		$sql = $dbConn->execute($sql);
		$ret = $dbConn->getResultArray($sql);
		$rtn = array("totalNum"=>$totalNum,"data"=>$ret);
    	return $rtn;
    } 

    //获取仓库信息
    public function warehousList($where=''){
    	$ret = GoodsModel::warehousList($where);
    	if($ret==false){
    		self::$errCode = GoodsModel::$errCode;
    		self::$errMsg = GoodsModel::$errMsg;
    		return false;
    	}
    	return $ret;
    	
    }
	/**
	 * 功能：获取库存总金额
	 * @return void
	 * @author wxb
	 * @date 2013/11/8
	 */
   public function getTotal(){
   		$ret = GoodsModel::getTotal();
   		return $ret;
   } 
   /**
    * 功能：通过名字获取采购员id
    * @return void
    * @author wxb
    * @date 2013/11/8
    */
   public function purchaseIdByName($name){
   	$ret = GoodsModel::purchaseIdByName($name);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
   /**
  * 功能：通过名字获取供应商id
    * @return void
    * @author wxb
    * @date 2013/11/8
    */
   public function partnerIdByName($name){
   	$ret = GoodsModel::partnerIdByName($name);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
  
   /**
    * 功能：通过sku id获取供应商名字
    * @param str $skuid
    * @return void
    * @author wxb
    * @date 2013/11/8
    */
   public static function partnerNameBySku($sku){
   	$id = GoodsModel::partnerIdBySku($sku);
   	if($id==false){
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	$ret = GoodsModel::partnerNameById($id);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
   /**
    * 功能：通过采购员id获取名字
    * @param str $id
    * @return void
    * @author wxb
    * @date 2013/11/8
    */
   public static function purchaseNameById($id){
   	$ret = GoodsModel::purchaseNameById($id);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
   /**
    * 功能：通过供应商id获取sku
    * @param str $parId
    * @return void
    * @author wxb
    * @date 2013/11/15
    */
   public static function skuByParId($parId){
   	$ret = GoodsModel::skuByParId($parId);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
   /**
    * 功能：通过sku获取spu
    * @param str $sku
    * @return void
    * @author wxb
    * @date 2013/11/15
    */
   public  function getSpuBySku($sku){
   	$ret = GoodsModel::getSpuBySku($sku);
   	if($ret==false){
   		self::$errCode = GoodsModel::$errCode;
   		self::$errMsg = GoodsModel::$errMsg;
   		return false;
   	}
   	return $ret;
   }
}

?>
