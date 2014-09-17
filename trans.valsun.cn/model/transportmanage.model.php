<?php

class TransportmanageModel {
    public 	static $dbConn;
	public	static $errCode	=	0;
	public	static $errMsg	=	"";
	static  $table	=	"trans_carrier";

	//db初始化
	public 	function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
    	
   public static function transportmanagelist($where = ''){
	    self::initDB();
		$info = array();
		$sql	 =	"select * from ".self::$table;
		if(!empty($where)){
		$sql .= " where {$where}";	
		}
		$query	 =	self::$dbConn->query($sql);
		if($query){
			$info =self::$dbConn->fetch_array_all($query);
			return $info;	//成功， 返回列表数据
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"搜索运输方式失败";
			return false;	//失败则设置错误码和错误信息， 返回false
		}
	}
	//添加运输方式
	public static function addTransport($arr){
		self::initDB();
		$carrierSql = array();
		if(!empty($arr['carrierNameCnInput'])){
			$carrierSql[] = "carrierNameCn = '{$arr['carrierNameCnInput']}'";
                        $isexist = self::checkTransportCnNameExist($arr['carrierNameCnInput']);
                        if($isexist){   //该名称已经存在   则出错返回
                            self::$errCode =	"003";
                            self::$errMsg  =	"运输方式中文名重复";
                            return ;
                        }
		}
		if(!empty($arr['carrierNameEnInput'])){
			$carrierSql[] = "carrierNameEn = '{$arr['carrierNameEnInput']}'";
		}
		if(!empty($arr['weightMinInput'])){
			$carrierSql[] = "weightMin = '{$arr['weightMinInput']}'";
		}
		if(!empty($arr['weightMaxInput'])){
			$carrierSql[] = "weightMax = '{$arr['weightMaxInput']}'";
		}
		if(!empty($arr['timecountInput'])){
			$carrierSql[] = "timecount = '{$arr['timecountInput']}'";
		}
		if(!empty($arr['noteInput'])){
			$carrierSql[] = "note = '{$arr['noteInput']}'";
		}
		$sql	 =	"INSERT INTO ".self::$table." SET ".join(',',$carrierSql);
		$query	 =	self::$dbConn->query($sql);
		$carrierId = self::$dbConn->insert_id();
		if($query){
			channelsManageModel::insertChannelsall($carrierId,'','');
			self::addTransportRelation($carrierId,$arr['platform']);//添加关系联系表
			self::addShippingAddressRelation($carrierId,$arr['shippingAddress']);//添加关系联系表
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"添加运输方式失败";
			return false;
		}
	}
	//添加平台与运输方式关系表
	public static function addTransportRelation($carrierId,$platformArr){
		self::initDB();
		foreach($platformArr as $platform){
			$sql	=	"INSERT INTO trans_carrierName SET carrierId='$carrierId', platformId = '$platform'";
			self::$dbConn->query($sql);
		}
	}
	//添加发货地址与运输方式关系表
	public static function addShippingAddressRelation($carrierId,$shippingAddress){
		self::initDB();
		$sql	=	"INSERT INTO trans_address_carrier_relation SET carrierId='$carrierId', addressId = '$shippingAddress'";
		$query	 =	self::$dbConn->query($sql);
		
		if($query){
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"添加发货地址与运输方式关系表";
			return false;
		}
	}
    //编辑运输方式
	public static function editTransport($carrierSql,$carrierId){
		self::initDB();
		$sql	 =	"UPDATE ".self::$table." SET ".join(',',$carrierSql)." WHERE id = $carrierId";
		$query	 =	self::$dbConn->query($sql);
		
		if($query){
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"编辑运输方式失败";
			return false;
		}
	}
	//开启运输方式
	public static function openCarrier($carrierIds){
		self::initDB();
		$sql	 =	"UPDATE ".self::$table." SET is_delete = 0 WHERE id in($carrierIds)";
		echo $sql;
		$query	 =	self::$dbConn->query($sql);
		
		if($query){
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"开启运输方式失败";
			return false;
		}
	}
	//关闭运输方式
	public static function dropCarrier($carrierIds){
		self::initDB();
		$sql	 =	"UPDATE ".self::$table." SET is_delete = 1 WHERE id in($carrierIds)";
		$query	 =	self::$dbConn->query($sql);
		
		if($query){
			return true;
		}else{
			self::$errCode =	"003";
			self::$errMsg  =	"关闭运输方式失败";
			return false;
		}
	}
        
        /*
         * 验证运输方式中文名称是否重复
         */
        public static function checkTransportCnNameExist($name){
            self::initDB();
            $name = mysql_real_escape_string($name);
            $sql = "select carrierNameCn from trans_carrier where carrierNameCn='$name'";
            $row = self::$dbConn->fetch_first($sql);
            if(empty($row)){
                return FALSE;
            }else{
                return TRUE;
            }
        }
        
        /*
         * 获得某个运输方式所属平台列表
         * $carrierid  运输方式列表
         */
        public static function getPlatforListByCarrierId($carrierid){
            self::initDB();
            $sql = "select platformId from  trans_carrierName where carrierId=$carrierid";
            $query = self::$dbConn->query();
            $result = array();
            while($row = self::$dbConn->fetch_array($query)){
                $result[] = $row['platformId'];
            }
            return $result;
        }
        
        /*
         * 更新运输方式的所属平台列表
         * $newplatlist     新的平台列表 $carrierid运输方id
         * 作者 涂兴隆
         */
        public static function updatePlatformList($newplatlist, $carrierid, $carriername){
            self::initDB();
            self::$dbConn->begin();         //以事物的方式来处理
            $sql = "delete from trans_carrierName where carrierId=$carrierid";
            $result = self::$dbConn->query($sql);
            if(!$result){   //执行删除失败 则回滚
                self::$dbConn->rollback();
                return;
            }
            $insertstr = array();
            $carriername = mysql_real_escape_string($carriername);
            foreach ($newplatlist as $value) {
                $insertstr[] = "(null, $carrierid, '$carriername', $value)";
            }
            $sqlstr = implode(', ', $insertstr);
            $sql = "insert into trans_carrierName values $sqlstr";
            $insertresult = self::$dbConn->query($sql);
            if(!$insertresult){ //插入失败 则回滚
                self::$dbConn->rollback();
                return;
            }
            self::$dbConn->commit();
        }
        
        /*
         * 更新运输方式所属发货地
         */
       public static function updateAddrList($newaddr, $carrierid){
            self::initDB();
            $sql = "update trans_address_carrier_relation set addressId=$newaddr where carrierId=$carrierid";
            self::$dbConn->query($sql);
        }

}