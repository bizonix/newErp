<?php
/*
 * 通用actionApi
 * ADD BY zqt 2013.9.13
 */
class OmAvailableApiAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 取得指定表的记录,成功返回记录集数组，失败返回false
     *
	 */
	function act_getTNameList() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $select = $jsonArr['select'];//select，不用关键字SELECT
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($select) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$list = OmAvailableModel :: getTNameList($tName, $select, $where);
		
		if (is_array($list)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $list;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 取得指定表的记录数,成功返回记录数count，失败返回false
     *
	 */
	function act_getTNameCount() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count !== false) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $count;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 添加记录到指定表，成功返回插入的记录ID，失败返回false
     *
	 */
	function act_addTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        if(empty($tName) || empty($set)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$insertId = OmAvailableModel :: addTNameRow($tName, $set);
		if ($insertId !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $insertId;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 修改指定表的记录,成功返回影响的记录数affectRows，失败返回false
     *
	 */
	function act_updateTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($set) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$affectRows = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $affectRows;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}
	
	public function act_getAllPic($spu = '',$picType = '') {
		require_once WEB_PATH."html/api/include/opensys_functions.php";
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
		if(!empty($errStr)) {
			self::$errCode = '001';
			self::$errMsg = $errStr;
			return false;
		}
		$token	= "5f5c4f8c005f09c567769e918fa5d2e3";
		$url	= 'http://idc.gw.open.valsun.cn/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'datacenter',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'spu'		=> $spu,  //主料号
			'picType'	=> $picType, //站点
			/* API应用级输入参数 End*/
		);
		//生成签名
		$sign = createSign($paramArr,$token);
		//组织参数
		$strParam = createStrParam($paramArr);
		$strParam .= 'sign='.$sign;
		//构造Url
		$urls = $url.$strParam;
		//echo $urls;
		//return $urls;
		$cnt=0;	
		while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
		$data = json_decode($result,true);
		return $data;
	}
	
	public function act_getAllPicApi() {
		require_once WEB_PATH."html/api/include/opensys_functions.php";
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
		if(!empty($errStr)) {
			self::$errCode = '001';
			self::$errMsg = $errStr;
			return false;
		}
		$token	= "5f5c4f8c005f09c567769e918fa5d2e3";
		$url	= 'http://idc.gw.open.valsun.cn/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'datacenter',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'spu'		=> $spu,  //主料号
			'picType'	=> $picType, //站点
			/* API应用级输入参数 End*/
		);
		//生成签名
		$sign = createSign($paramArr,$token);
		//组织参数
		$strParam = createStrParam($paramArr);
		$strParam .= 'sign='.$sign;
		//构造Url
		$urls = $url.$strParam;
		//echo $urls;
		//return $urls;
		$cnt=0;	
		while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
		$data = json_decode($result,true);
		return $data;
	}
	
	public function act_getCategoryInfoAll() {
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		
		//$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$url	= 'http://idc.gw.open.valsun.cn/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getCategoryInfoAll',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'all'		=> '',  //All
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		return $data;
	}
	
	public function act_getGoodsInfoBySku($sku) {
		require_once WEB_PATH."html/api/include/opensys_functions.php";
		
		$url	= 'http://gw.open.valsun.cn:88/router/rest?';
		$url	= 'http://idc.gw.open.valsun.cn/router/rest?';
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'pc.getGoodsInfoBySku',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */				 

			/* API应用级输入参数 Start*/
			'sku'		=> $sku,  //All
			/* API应用级输入参数 End*/
		);
		$result = callOpenSystem($paramArr);
		
		$data = json_decode($result,true);
		return $data;
	}
	
}
?>
