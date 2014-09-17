<?php
/*
 * 发货地址管理MODEL层  shippingAddressManage.model.php
 * ADD BY 陈伟 2013.7.25
 */
class shippingAddressManageModel{
    public static $errCode = 0;
    public static $errMsg = '';
    private $dbconn = null;
    
    /*
     * 构造函数 初始化数据库连接
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    

	/*
    * 发货地址管理分页总条数
    */	
	public 	function getShippingAddressListNum(){
		$num = 0;
		$sql	 =	"select * from trans_shipping_address";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$num =  $this->dbconn->num_rows($query);
			return $num;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;
		}
	}	
	
		
   /*
    * 发货地址管理mysql数据查询
    */		
   public function shippingAddressList($where=''){
		$info = array();
		$sql	 =	"select a.id as main_id,a.addressNameCn,a.addressNameEn,a.addressCode,a.sellerId,a.createdTime,a.is_delete,b.id,b.sellerName FROM trans_shipping_address as a LEFT JOIN trans_seller as b ON a.sellerId = b.id";
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	} 
	
	/*
    * 发货地址管理MYSQL插入
    */		
   public function shippingAddressAdd($shippingAddressSql,$sellerName,$name){
	   $serch_sql = "SELECT * FROM trans_seller ".$sellerName;
	   $serch_sql = $this->dbconn->query($serch_sql);
	   if($serch_sql){
		 $serch_arr =  $this->dbconn->fetch_array_all($serch_sql);
		 
		 if(empty($serch_arr)){//大卖家为空，插入大卖家
		 	
			$insert_sql	 =	"INSERT INTO trans_seller SET sellerName = '{$name}'"; 
			$this->dbconn->query($insert_sql);
			$serch     = "SELECT id FROM trans_seller where sellerName = '{$name}'";
			$serch 	   = $this->dbconn->query($serch);
			$serch     =  $this->dbconn->fetch_array_all($serch);
			
			$sql	 =	"INSERT INTO trans_shipping_address SET ".join(',',$shippingAddressSql).",sellerId = ".$serch[0]['id'].",createdTime = ".time().",is_delete = 0";
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return $query;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"444444444";
				return false;	//失败则设置错误码和错误信息， 返回false
			}
			
		 }else{
			$sql	 =	"INSERT INTO trans_shipping_address SET ".join(',',$shippingAddressSql).",sellerId = ".$serch_arr[0]['id'].",createdTime = ".time().",is_delete = 0";
			$query	 =	$this->dbconn->query($sql);
			if($query){
				return $query;	//成功， 返回列表数据
			}else{
				self::$errCode =	"003";
				self::$errMsg  =	"444444444";
				return false;	//失败则设置错误码和错误信息， 返回false
			} 
		 }
	   }else{
		 	self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false 
	   }
	   
	}
	
	/*
    * 发货地址编辑数据查询
    */		
   public function shippingAddressEdit($where){
		$info = array();
		$sql	 =	"select * from trans_shipping_address";
		if(!empty($where)){
			$sql .= " {$where}";	
		}
		$query	 =	$this->dbconn->query($sql);
		if($query){
			$info =$this->dbconn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
    * 发货地址编辑数据更新
    */		
   public function shippingAddressEditIn($shippingAddressArr,$where){
		$sql	 =	"UPDATE trans_shipping_address SET ".join(',',$shippingAddressArr)." {$where}";
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	
	/*
    * 删除标发货地址MYSQL
    */		
   public function shippingAddressDel($where){
		$sql	 =	"DELETE FROM trans_shipping_address {$where}";
		//echo $sql;exit;
		$query	 =	$this->dbconn->query($sql);
		if($query){
			return $query;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"444444444";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	    
}

