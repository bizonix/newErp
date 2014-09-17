<?php
/**
 * 类名：ChannelPriceModel
 * 功能：渠道运费数据（CRUD）层
 * 版本：1.0
 * 日期：2013/11/18
 * 作者：管拥军
 */
 
class ChannelPriceModel{
	public static $dbConn;
	public static $errCode		= 0;
	public static $errMsg		= "";
	public static $prefix;
	public static $prefixfee;
	private static $tab_channel	= "channels";
		
	/**
	 * ChannelPriceModel::initDB()
	 * 返回数据库连接
	 * @return 
	 */
	public static function	initDB(){
		global $dbConn;
		self::$dbConn		= $dbConn;
		self::$prefix		= C('DB_PREFIX');
		self::$prefixfee	= C('DB_PREFIX').'freight_';
	}
	
	/**
	 * ChannelPriceModel::modList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
	public static function modList($tab, $where, $page, $pagenum){
		self::initDB();
		$start		= ($page-1)*$pagenum;
		$res		= self::getField($tab);
		$sql 		= "SELECT {$res['view']} FROM ".self::$prefixfee.$tab." WHERE $where AND is_delete = 0 ORDER BY id DESC LIMIT $start,$pagenum";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array_all($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ChannelPriceModel::modListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public static function modListCount($tab, $where){
		self::initDB();
		$sql 		= "SELECT count(*)	FROM ".self::$prefixfee.$tab." WHERE $where AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($result	= self::$dbConn->query($sql)) {
			$data	= self::$dbConn->fetch_row($result);
			return $data[0];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 0;
		}
	}
	
	/**
	 * ChannelPriceModel::getField()
	 * 列出符合条件的数据并分页显示
	 * @param string $tab 运费价目表
	 * @return string 字段
	 */
	public static function getField($tab){
		$res 	= array();
		switch($tab) {
			case "cpsf_fujian_zhangpu": //平邮福建漳浦
				$res['view']	= "id,name as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "name,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "cpsf_fujian_quanzhou": //平邮福建泉州
				$res['view']	= "id,name as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "name,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "cpsf_shenzhen": //平邮深圳
				$res['view']	= "id,name as pr_group,countries as pr_country,firstweight as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "name,firstweight,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "cprg_fujian_zhangpu": //福建漳浦挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "cprg_fujian_quanzhou": //福建泉州挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "cprg_shenzhen": //深圳挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "zhengzhou_xb_py": //郑州小包平邮
				$res['view']	= "id,name as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "name,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;
			case "zhengzhou_xb_gh": //郑州小包挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
				$res['batch']	= "discount";
			break;			
			case "hkpostsf_hk": //香港小包平邮
				$res['view']	= "id,name as pr_group,countrys as pr_country,firstweight as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,exchange_rate as pr_kilo_next,zgTranFee as pr_file";
				$res['edit']	= "name,firstweight,countrys,handlefee,discount,exchange_rate,zgTranFee";
			break;
			case "hkpostrg_hk": //香港小包挂号
				$res['view']	= "id,name as pr_group,countrys as pr_country,firstweight as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,exchange_rate as pr_kilo_next,zgTranFee as pr_file";
				$res['edit']	= "name,firstweight,countrys,handlefee,discount,exchange_rate,zgTranFee";
			break;
			case "ems_shenzhen": //EMS深圳
				$res['view']	= "id,name as pr_group,countrys as pr_country,firstweight as pr_kilo,discount as pr_discount,declared_value as pr_handlefee,nextweight as pr_kilo_next,firstweight0 as pr_file,files as pr_isfile";
				$res['edit']	= "name,firstweight,countrys,declared_value,discount,nextweight,firstweight0,files";
			break;
			case "eub_shenzhen": //EUB深圳
				$res['view']	= "id,name as pr_group,countrys as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,discount1 as pr_file,nextweight as pr_kilo_next,noWeight as pr_isfile";
				$res['edit']	= "name,unitPrice,countrys,handlefee,discount,discount1,nextweight,noWeight";
			break;
			case "eub_fujian": //EUB福建
				$res['view']	= "id,name as pr_group,countrys as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,discount1 as pr_file,nextweight as pr_kilo_next,noWeight as pr_isfile";
				$res['edit']	= "name,unitPrice,countrys,handlefee,discount,discount1,nextweight,noWeight";
			break;
			case "eub_jiete": //EUB捷特
				$res['view']	= "id,name as pr_group,countrys as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,discount1 as pr_file,nextweight as pr_kilo_next,noWeight as pr_isfile";
				$res['edit']	= "name,unitPrice,countrys,handlefee,discount,discount1,nextweight,noWeight";
			break;
			case "dhl_shenzhen": //DHL深圳
				$res['view']	= "id,partition as pr_group,country as pr_country,weight_freight as pr_kilo,mode as pr_handlefee,fuelcosts as pr_discount";
				$res['edit']	= "partition,weight_freight,country,mode,fuelcosts";
				$res['batch']	= "fuelcosts";
			break;
			case "fedex_shenzhen": //联邦深圳
				$res['view']	= "id,countrylist as pr_country,unitprice as pr_kilo,type as pr_handlefee,baf as pr_discount,weightinterval as pr_kilo_next";
				$res['edit']	= "unitprice,weightinterval,countrylist,type,baf";
				$res['batch']	= "baf";
			break;
			case "ups_uk":
			case "ups_fr":
			case "ups_ger":
			case "ups_us": //UPS(美国、法国、德国、英国)
				$res['view']	= "id,price as pr_kilo,type as pr_isfile,min_weight as pr_file,max_weight as pr_kilo_next,fuelcosts as pr_discount,vat as pr_handlefee";
				$res['edit']	= "price,min_weight,max_weight,fuelcosts,vat,type";
				$res['batch']	= "fuelcosts";
			break;
			case "globalmail_shenzhen": //德国小包深圳
				$res['view']	= "id,country as pr_country,weight_freight as pr_kilo,fuelcosts as pr_handlefee,zgTranFee as pr_file";
				$res['edit']	= "weight_freight,country,fuelcosts,zgTranFee";
			break;
			case "ups_calcfree": //ups ground 海外仓UPS运费价目表
				$res['view']	= "id,zone as pr_group,cost as pr_kilo,weight as pr_handlefee,unit as pr_kilo_next";
				$res['edit']	= "zone,cost,weight,unit";
			break;
			case "usps_calcfree": //USPS 海外仓USPS运费价目表
				$res['view']	= "id,zone as pr_group,cost as pr_kilo,weight as pr_handlefee,unit as pr_kilo_next";
				$res['edit']	= "zone,cost,weight,unit";
			break;
			case "sto_shenzhen": //申通
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "zto_shenzhen": //中通
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "yto_shenzhen": //圆通
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "yundaex_shenzhen": //韵达
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "best_shenzhen": //汇通
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "jym_shenzhen": //加运美
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "gto_shenzhen": //国通
				$res['view']	= "id,areaId as pr_group,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,firstWeight as pr_isfile,nextPrice as pr_kilo_next,noPrice as pr_file";
				$res['edit']	= "areaId,noPrice,price,nextPrice,firstWeight,discount,handlefee";
			break;
			case "ruston_packet_py": //俄速通平邮
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,maxWeight as pr_file,nextPrice as pr_kilo_next";
				$res['edit']	= "groupName,countrys,price,nextPrice,maxWeight,discount,handlefee";
			break;
			case "ruston_packet_gh": //俄速通挂号
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,maxWeight as pr_file,nextPrice as pr_kilo_next";
				$res['edit']	= "groupName,countrys,price,nextPrice,maxWeight,discount,handlefee";
			break;
			case "ruston_large_package": //俄速通大包
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,minWeight as pr_file,nextPrice as pr_kilo_next";
				$res['edit']	= "groupName,countrys,price,nextPrice,minWeight,discount,handlefee";
			break;
			case "sg_dhl_gm_gh": //新加坡DHL GM挂号
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,paTranFee as pr_kilo,paFee as pr_handlefee,discount as pr_discount,delFee as pr_kilo_next,zgTranFee as pr_isfile,airFee as pr_air,otherFee as pr_other";
				$res['edit']	= "groupName,paTranFee,paFee,delFee,countrys,discount,zgTranFee,airFee,otherFee";
				$res['batch']	= "discount";
			break;
			case "sg_dhl_gm_py": //新加坡DHL GM平邮
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,paTranFee as pr_kilo,paFee as pr_handlefee,discount as pr_discount,delFee as pr_kilo_next,zgTranFee as pr_isfile,airFee as pr_air,otherFee as pr_other";
				$res['edit']	= "groupName,paTranFee,paFee,delFee,countrys,discount,zgTranFee,airFee,otherFee";
				$res['batch']	= "discount";
			break;
			case "ruishi_xb_py": //瑞士小包平邮
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,zgTranFee as pr_kilo_next,level as pr_other";
				$res['edit']	= "groupName,unitPrice,zgTranFee,handlefee,countries,discount,level";
			break;
			case "ruishi_xb_gh": //瑞士小包挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,zgTranFee as pr_kilo_next,level as pr_other";
				$res['edit']	= "groupName,unitPrice,zgTranFee,handlefee,countries,discount,level";
			break;
			case "bilishi_xb_py": //比利时小包平邮
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
			break;
			case "bilishi_xb_gh": //比利时小包挂号
				$res['view']	= "id,groupName as pr_group,countries as pr_country,unitPrice as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,unitPrice,countries,handlefee,discount";
			break;
			case "usps_first_class": //赛维USPS
				$res['view']	= "id,zone as pr_group,cost as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,CONCAT(minWeight,'-',maxWeight) as pr_kilo_next,fuelCost as pr_other,zgTranFee as pr_isfile,airFee as pr_air,clsFee as pr_file";
				$res['edit']	= "zone,cost,handlefee,discount,minWeight,maxWeight,fuelCost,zgTranFee,airFee,clsFee";
			break;
			case "ups_ground_commercia": //赛维UPS
				$res['view']	= "id,zone as pr_group,cost as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,CONCAT(minWeight,'-',maxWeight) as pr_kilo_next,fuelCost as pr_other,zgTranFee as pr_isfile,airFee as pr_air,clsFee as pr_file";
				$res['edit']	= "zone,cost,handlefee,discount,minWeight,maxWeight,fuelCost,zgTranFee,airFee,clsFee";
			break;
			case "sv_sure_post": //赛维SurePost
				$res['view']	= "id,zone as pr_group,cost as pr_kilo,handlefee as pr_handlefee,discount as pr_discount,CONCAT(minWeight,'-',maxWeight) as pr_kilo_next,fuelCost as pr_other,zgTranFee as pr_isfile,airFee as pr_air,clsFee as pr_file";
				$res['edit']	= "zone,cost,handlefee,discount,minWeight,maxWeight,fuelCost,zgTranFee,airFee,clsFee";
			break;
			case "aoyoubao_py": //澳邮宝平邮
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,countrys,price,discount,handlefee";
			break;
			case "aoyoubao_gh": //澳邮宝挂号
				$res['view']	= "id,groupName as pr_group,countrys as pr_country,price as pr_kilo,handlefee as pr_handlefee,discount as pr_discount";
				$res['edit']	= "groupName,countrys,price,discount,handlefee";
			break;
			default:
				exit("<script>alert('暂未开放此渠道的运费价目表维护...');history.back();</script>");			
		}
		return $res;
	}
	
	/**
	 * ChannelPriceModel::getCarrierId()
	 * 根据渠道ID返回运输方式ID
	 * @param int $chid 渠道ID
	 * @return integer  
	 */
	public static function getCarrierId($chid){
		self::initDB();
		$sql 		= "SELECT carrierId FROM ".self::$prefix.self::$tab_channel." WHERE id = {$chid} AND is_delete = 0";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res 	= self::$dbConn->fetch_array($query);
			return $res['carrierId'];
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return 1;
		}
	}
	
	/**
	 * ChannelPriceModel::modModify()
	 * 返回某个运费价目的信息
	 * @param integer $id 运费价目ID
	 * @return array 结果集数组
	 */
	public static function modModify($tab, $id){
		self::initDB();
		$res		= self::getField($tab);
		$sql 		= "SELECT {$res['view']} FROM ".self::$prefixfee.$tab." WHERE id = {$id} LIMIT 1";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$res	= self::$dbConn->fetch_array($query);
			return $res;
		} else {
			self::$errCode	= 90000;
			self::$errMsg	= "获取数据失败";
			return false;
		}
	}
	
	/**
	 * ChannelPriceModel::addChannelPrice()
	 * 添加运费价目信息保存到数据库
	 * @param array $data 数据集
	 * @param string $tab 运费价目表名
	 * @return array 结果集数组
	 */
	public static function addChannelPrice($tab, $data){
		self::initDB();
		$sql 		= array2sql($data);
		$sql 		= "INSERT INTO `".self::$prefixfee.$tab."` SET ".$sql; 
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows();           
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 10001;
				self::$errMsg	= "插入数据失败";
				return false;
			}
		} else {
			self::$errCode		= 10000;
			self::$errMsg		= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * ChannelPriceModel::updateChannelPrice()
	 * 更新运费价目信息
	 * @param integer $id 运费价目ID
	 * @param string $tab 运费价目表名
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function updateChannelPrice($id, $tab, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefixfee.$tab."` SET ".$sql." WHERE id = {$id}"; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}
	
	/**
	 * ChannelPriceModel::batchChannelPrice()
	 * 批量更新运费价目信息
	 * @param string $tab 运费价目表名
	 * @param array $data 数据集
	 * @return array 结果集数组
	 */
	public static function batchChannelPrice($tab, $data){
		self::initDB();
		$sql 	= array2sql($data);
		$sql 	= "UPDATE `".self::$prefixfee.$tab."` SET ".$sql; 
		$query	= self::$dbConn->query($sql);
		if($query) {
			return true;
		} else {
			self::$errCode	= 20000;
			self::$errMsg	= "执行SQL语句出错";
			return false;
		}
	}

	/**
	 * ChannelPriceModel::delChannelPrice()
	 * 运费价目删除
	 * @param integer $id 运费价目ID
	 * @param string $tab 运费价目表名
	 * @return bool
	 */
	public static function delChannelPrice($tab, $id){
		self::initDB();
		$sql		= "UPDATE `".self::$prefixfee.$tab."` SET is_delete = 1 WHERE id = {$id}";
		$query		= self::$dbConn->query($sql);
		if($query) {
			$rows 	= self::$dbConn->affected_rows(); 
			if($rows) {
				return $rows;
			} else {
				self::$errCode	= 30001;
				self::$errMsg	= "删除数据失败";
				return false;
			}
		} else {
            self::$errCode		= 30000;
			self::$errMsg		= "执行SQL语句失败！";
			return false;
		}
	}
}
?>