<?php
require_once WEB_PATH.'lib/rabbitmq/rabbitmq.class.php';
/*
 * om通用Model
 * ADD BY zqt 2013.9.5
 */
class OmAvailableModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
    public static $isSync = "YES";

	//db初始化
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
		mysql_query('SET NAMES UTF8');
	}
	/*
	 *取得指定表中的指定记录
	 */
	public static function getTNameList($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录并且存入到单个数组中
	 */
	public static function getTNameList2arr($tName, $select, $where) {
		self :: initDB();
		$sql = "select $select from $tName $where";
        //echo $sql.'<br>';
      //  global $memc_obj;
//        $result1 = $memc_obj->get_extral("sku_info_".'001');
//        var_dump($result1);
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			$ret2 = array();
			foreach($ret as $val){
				$ret2[] = $val[$select];
			}
			return $ret2; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/*
	 *取得指定表中的指定记录记录数
	 */
	public static function getTNameCount($tName, $where) {
		self :: initDB();
		$sql = "select count(*) count from $tName $where";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['count']; //成功， 返回列表数据
		} else {
			self :: $errCode = "001";
			self :: $errMsg = "获取数据失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

	/**
	 *添加指定表记录
	 */
	public static function addTNameRow($tName, $set) {
		self :: initDB();
		$sql = "INSERT INTO $tName $set";
		//echo $sql."<br/>";
		$query = self :: $dbConn->query($sql);
		if ($query) {
		    publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$insertId = self :: $dbConn->insert_id($query);
			return $insertId; //成功， 返回插入的id
		} else {
			self :: $errCode = "002";
			self :: $errMsg = "添加失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 * 插入一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function addTNameRow2arr($tName, $data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "INSERT INTO `".$tName."` SET ".$sql;
        //echo $sql;exit;
		$query	=	self::$dbConn->query($sql);
		if($query){
            publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}

    /**
	 * replace一条记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function replaceTNameRow2arr($tName, $data){
		self::initDB();
        $sql = array2sql($data);
		$sql = "REPLACE INTO `".$tName."` SET ".$sql;
        echo $sql."\n";
		$query	=	self::$dbConn->query($sql);
		if($query){
            publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$insertId = self::$dbConn->insert_id();
			return $insertId;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}

	/**
	 *修改指定表记录
	 */
	public static function updateTNameRow($tName, $set, $where) {
	    if(trim($where) == ''){
	       return false;
	    }
		self :: initDB();
		$sql = "UPDATE $tName $set $where";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
            publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 * 修改记录
	 * @para $data as array
	 * return insert_id
	 */
	public static function updateTNameRow2arr($tName, $data, $where){
	   if(trim($where) == ''){
	       return false;
	    }
		self::initDB();
        $sql = array2sql($data);
		$sql = "UPDATE `".$tName."` SET ".$sql.' '.$where;
        //echo $sql.'<br>';
		$query	=	self::$dbConn->query($sql);
		if($query){
		    publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$affectRow = self::$dbConn->affected_rows();
			return $affectRow;
		}else{
			self::$errCode	=	"003";
			self::$errMsg	=	"444444444";
			return false;
		}
	}

    /**
	 *修改指定表记录
	 */
	public static function deleteTNameRow($tName, $where) {
	    if(trim($where) == ''){
	       return false;
	    }
		self :: initDB();
		$sql = "DELETE FROM $tName $where";
        //echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
		    publishMQ($tName, $sql, C("MQSERVERADDRESS"));
			$affectRows = self :: $dbConn->affected_rows($query);
			return $affectRows; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "修改失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据属性id取得对应的属性值的字符串
	 */
	public static function getProValStrByProId($propertyId) {
		self :: initDB();
		$sql = "SELECT * from pc_archive_property_value WHERE propertyId='$propertyId'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
            $str = '';
            foreach($ret as $value){
                $str .= $value['propertyValue'];
                if(!empty($value['propertyValueShort']) || !empty($value['propertyValueAlias'])){
                    $str .= '('.$value['propertyValueShort'];
                    if(!empty($value['propertyValueAlias'])){
                        $str .= ' '.$value['propertyValueAlias'];
                    }
                    $str .= ')';
                }
                $str .= ',';
            }
			return substr($str,0,strlen($str)-1); //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据属性id取得对应的属性名的字符串
	 */
	public static function getProValNameById($id){
	   self :: initDB();
		$sql = "SELECT propertyName from pc_archive_property WHERE id='$id'";
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret[0]['propertyName']; //成功， 返回列表数据
		} else {
			self :: $errCode = "003";
			self :: $errMsg = "失败";
			return false; //失败则设置错误码和错误信息， 返回false
		}
	}

    /**
	 *根据前缀，及单/虚拟料号，返回其对应表（goods或combine）spu截取前缀后的最大数字
	 */
	public static function getMaxSpu($prefix, $isSingSpu){
	    self :: initDB();
        if(!preg_match("/^[A-Z]{2}$/", $prefix)){
            return false;
        }
        if($isSingSpu != 1 && $isSingSpu != 2){
            return false;
        }
	    if($isSingSpu == 1){
	       $sql = "SELECT spu from pc_goods WHERE spu REGEXP '^{$prefix}[0-9]{6}$' order by spu desc limit 1";
           $query = self :: $dbConn->query($sql);
           if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			if(empty($ret)){//如果pc_goods表中没有该前缀的spu则，默认为**000001
			   $max = 0;
               return $max;
			}else{//不为空，则取出满足即满足**\d{6}的spu,$tmpArrPreg中存放的是截取字母后的数字
               $max = intval(substr($ret[0]['spu'],2,6));
			}
            return $max;
		   }else{
		      return false;
		   }
	    }else{
	       $sql = "SELECT combineSpu from pc_goods_combine WHERE combineSpu REGEXP '^CB[0-9]{6}$' order by combineSpu desc limit 1";
           $query = self :: $dbConn->query($sql);
           if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			if(empty($ret)){//如果pc_goods_combine表中没有该前缀的spu则，默认为**000001
			   $max = 0;
               return $max;
			}else{//不为空，则取出满足即满足**\d{6}的spu,$tmpArrPreg中存放的是截取字母后的数字
               $max = intval(substr($ret[0]['combineSpu'],2,6));
               return $max;
			}
		   }else{
		      return false;
		   }
	    }
	}


    /**
	 *判断指定spu是否通过审核，只是单料号情况
	 */
	public static function isSpuAudit($spu){
	   self :: initDB();
	   $spu = post_check($spu);
	   $sql = "select count(*) count from pc_spu_archive where spu='$spu' and auditStatus=2 and is_delete=0";
       $query = self :: $dbConn->query($sql);
       if ($query) {
	       $ret = self :: $dbConn->fetch_array_all($query);
           if($ret[0]['count']){
              return true;
           }else{
              return false;
           }
       }else{
            return false;
       }
    }

    /**
	 *得出指定虚拟料号下的真实料号
	 */
	public static function getTrueSkuForCombine($combineSku){
	   if(empty($combineSku)){
	       return false;
	   }
	   self :: initDB();
	   $combineSku = post_check($combineSku);
	   $sql = "select * from pc_sku_combine_relation where combineSku='$combineSku'";
       $query = self :: $dbConn->query($sql);
       if ($query) {
	       $ret = self :: $dbConn->fetch_array_all($query);
           return $ret;
       }else{
            return false;
       }
    }

    /**
	 *根据id返回ppv的值
	 */
	public static function getPropertyValueById($id){
	   self :: initDB();
	   $sql = "select propertyValue,propertyValueShort,propertyValueAlias from pc_archive_property_value where id=$id";
       $query = self :: $dbConn->query($sql);
       if ($query) {
	       $ret = self :: $dbConn->fetch_array_all($query);
           $ppvName = $ret[0]['propertyValue'];//值名称
           $ppvAlias = $ret[0]['propertyValueAlias'];//值名称
           $ppvShort = $ret[0]['propertyValueShort'];//值简称
           $returnName = !empty($ppvAlias)?$ppvName.'('.$ppvAlias.')':$ppvName;//别名不为空，则也返回
           $returnName = !empty($ppvShort)?$returnName.'('.$ppvShort.')':$returnName;//简称不为空，则也返回
           return $returnName;
       }else{
            return false;
       }
    }

   //取得指定sku对应的供应商公司名称
    public static function getParterNameBySku($sku){
       self :: initDB();
       $sku = post_check($sku);
       $sql = "select partnerId from pc_goods_partner_relation where sku='$sku' limit 1";
       $query = self :: $dbConn->query($sql);
       if ($query) {
           $ret = self :: $dbConn->fetch_array_all($query);
           $partnerId = $ret[0]['partnerId'];
           if(!$partnerId){
              return '';
           }else{
             //通过采购系统提供的数据
             $partner = UserCacheModel::getOpenSysApi('purchase.getSupplierInfo',array('supplierId'=>$partnerId),'');
             return isset($partner['data']['company_name'])?$partner['data']['company_name']:'';
           }
       }else{
            return false;
       }
    }

    //将新系统的数据同步到老系统中
    //public static function newData2Old($sql){
//        if(self::$isSync === 'YES'){
//            $link_listing2  =   mysql_connect('192.168.200.158','cerp','123456', true)or die("Could not connect: " . mysql_error());
//            $db_listing2    =	mysql_select_db('cerp',$link_listing2) or die('数据库连接错误');
//            mysql_query('set names utf8',$link_listing2);
//            $query = mysql_query($sql, $link_listing2);
//        }
//
//    }

    //新数据同步旧数据的接口
    public static function newData2ErpInterf($url){
        if(self::$isSync == 'ZQT'){
            include_once "../api/include/functions.php";
            $url = 'http://erp.valsun.cn/api/'.$url;
            //$url = urlencode($url);
            $result = vita_get_url_content($url);
        }
    }

    //新数据同步旧数据的接口,通过开放系统
    public static function newData2ErpInterfOpen($metode,$paraArr,$idc='',$decode=true){
        if(self::$isSync == 'YES'){
            $res = UserCacheModel::getOpenSysApi($metode,$paraArr,$idc,$decode=true);
            return $res;
        }
    }

    /**
	 *根据id返回ppv的值
	 */
	public static function getPropertyNameById($id){
	   self :: initDB();
	   $sql = "select propertyName from pc_archive_property where id=$id";
       $query = self :: $dbConn->query($sql);
       if ($query) {
	       $ret = self :: $dbConn->fetch_array_all($query);
           return $ret[0]['propertyName'];
       }else{
            return false;
       }
    }

    /**
	 *根据部门id返回该部门下所有人员的id
	 */
	public static function getAllPersonIdByDeptId($id){
	   $id = intval($id);
	   if($id <= 0){
	       return false;
	   }
	   self :: initDB();
	   $sql = "select global_user_id from power_global_user where global_user_dept=$id";
       $query = self :: $dbConn->query($sql);
       if ($query) {
	       $ret = self :: $dbConn->fetch_array_all($query);
           if(empty($ret)){
              return false;
           }
           $personIdArr = array();
           foreach($ret as $value){
             $personIdArr[] =  $value['global_user_id'];
           }
           return $personIdArr;
       }else{
            return false;
       }
    }


}
?>
