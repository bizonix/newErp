<?php

/**
 * 类名：ProductsModel
 * 功能：对pc_products表进行数据库操作
 * 版本：1.0
 * 日期：2013/07/25
 * 作者：朱清庭
 */

class ProductsModel {
	public static $dbConn;
	public static $errCode = 0;
	public static $errMsg = "";
	static $table = "pc_products";
	public static $url = 'http://idc.gw.open.valsun.cn/router/rest?';  //开放系统入口地址
	public static $token = '18006c5d80cf4a05518e382adccb3469'; //用户token(222)测试用
	public function initDB() {
		global $dbConn;
		self :: $dbConn = $dbConn;
	}

	/**
	* 根据条件取得pc_goods表的结果集
	* @param     $select	select的字段
	* @param     $where 	条件
	* @return    $ret		结果集
	*/
	public static function getProducts($select, $where) {
		self :: initDB();
		$sql = "select $select from " . self :: $table . " $where";
		//var_dump(UserCacheModel::getPowerList($_SESSION['userId']));
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1101";
			self :: $errMsg = "getProductsByStatus";
			return false;
		}
	}
    /**
    取得老系统的eabay_products数据
    */
    public static function getProductsCache($method,$idc='',$decode=true) {
		include_once WEB_PATH."api/include/functions.php";
        if(empty($method)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{
			$paramArr = array(
				'format' => 'json',
					 'v' => '1.0',
				'username'	 => 'valsun.cn'
			);
            $paramArr['method'] = $method;//调用接口名称，系统级参数
			//生成签名
			$sign = createSign($paramArr,self::$token);
			//echo $sign,"<br/>";
			//组织参数
			$strParam = createStrParam($paramArr);

			$strParam .= 'sign='.$sign;
			//echo $strParam,"<br/>";
            if($idc == ''){
                $url = self::$url;
            }else{
                $url = 'http://gw.open.valsun.cn:88/router/rest?';
            }
			//构造Url
			$urls = $url.$strParam;
			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
            if($decode){
              $data	= json_decode($result,true);  
            }else{
              $data = $result;  
            }
            //var_dump($data);exit;
			if($data){
				self::$errCode = 200;
        		self::$errMsg = 'Success';
				return $data;
			}else{
				self::$errCode = "000";
        		self::$errMsg = "is empty!";
			}
		}
	}

    /**
    获取pc_products的Id
    
    */
    public static function getItemLsit($item){
        self :: initDB();
		$sql = "select $item from " . self :: $table;
		
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->fetch_array_all($query);
			return $ret;
		} else {
			self :: $errCode = "1101";
			self :: $errMsg = "getProductsByStatus";
			return false;
		}
    }
	/**
	* 根据条件更新数据
	* @param     $set		更新的字段
	* @param     $where 	条件
	* @return    void
	*/
	public static function updateProducts($set, $where) {
		self :: initDB();
		$sql = "update " . self :: $table . " $set $where";
		//echo $sql.'<br>';
		if (!self :: $dbConn->query($sql)) {
			self :: $errCode = "1201";
			self :: $errMsg = "updateProductsByStatus";
			return false;
		} else
			if ($dbConn->affected_rows < 1) {
				self :: $errCode = "1202";
				self :: $errMsg = "updateNoRows";
			}
	}

	/**
	* 根据条件取得符合的记录数
	* @param     $where 	条件
	* @return    $ret		记录数
	*/
	public static function getProductsCount($where) {
		self :: initDB();
		$sql = "select id from " . self :: $table . " $where";
		//echo $sql.'<br>';
		$query = self :: $dbConn->query($sql);
		if ($query) {
			$ret = self :: $dbConn->num_rows($query);
			return $ret;
		} else {
			self :: $errCode = "1301";
			self :: $errMsg = "getProductsCount";
			return false;
		}
	}

}
?>