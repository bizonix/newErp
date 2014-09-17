<?php
/*
 * 系统共用函数页面
 * @modify by lzx ,date 20140528
 */

/**
 * author:Herman.Xi
 * date:2012/3/23
 * last Modified:2013/06/20
 * 调用Google API 中文到英文互相翻译，默认，中文翻译成英文
 */
function google_translate($text, $fromLanguage = 'zh-cn', $toLanguage = 'en') {
	if (empty ($text))
		return false;
	$language = "{$fromLanguage}|{$toLanguage}";
	@ set_time_limit(0);
	$html = "";
	$ch = curl_init("http://translate.google.com/?langpair=" . urlencode($language) . "&text=" . urlencode($text));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$html = curl_exec($ch);
	if (curl_errno($ch))
		$html = "";
	curl_close($ch);
	if (!empty ($html)) {
		$x = explode("</span></span></div></div>", $html);
		$x = explode("onmouseout=\"this.style.backgroundColor='#fff'\">", $x[0]);
		return $x[1];
	} else {
		return false;
	}
}

/**
 * 简单的重定向函数 add by xiaojinhua
 * @param string 需要跳转的链接地址
 * @modify author lzx
 */
function redirect_to($location='') {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

/**
 * 字符串、数组转化为标准的数组
 * @param string|array $array
 * @return array
 * @author lzx
 */
function format_array($var){
	if (empty($var)){
		return array();
	}else if(is_array($var)){
		return $var;
	}else if(is_numeric($var)){
		return array($var);
	}else if(is_string($var)){
		return strpos($var, ',')!==false ? implode(',', $var) : array($var);
	}else{
		return array();
	}
}

function write_log($file, $content) {
	$filepath = dirname($file);
	if (!is_dir($filepath)){
		mkdirs($filepath);
	}
	if (!$handle = fopen($file, 'a')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $content) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function mkdirs($path) {
	$path_out = preg_replace('/[^\/.]+\/?$/', '', $path);
	if (!is_dir($path_out)) {
		mkdirs($path_out);
	}
	mkdir($path);
}

function write_a_file($file, $data) {
	$tmp_dir = dirname($file);
	if (!is_dir($tmp_dir)) {
		mkdirs($tmp_dir);
	}
	if (!$handle = fopen($file, 'a')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function write_w_file($file, $data) {
	$tmp_dir = dirname($file);
	if (!is_dir($tmp_dir)) {
		mkdirs($tmp_dir);
	}
	if (!$handle = fopen($file, 'w')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function read_file($file) {
	if (!is_file($file)) {
		return false;
	}
	return file_get_contents($file);
}

function read_and_empty_file($file) {
	if (!is_file($file)) {
		return false;
	}
	$contents = file_get_contents($file);
	if (!$handle = fopen($file, 'w')) {
		return false;
	}
	return $contents;
}

function round_num($f, $n) {
	$num = pow(10, $n);
	$intn = intval(round($f * $num));
	$r = $intn / $num;
	$r = $r +0.00001;
	return str_replace(',', '', number_format($r, 2));
}
/**
 * 查找带分隔符的字符中的某个字符
 * 例如：instr("1,2,11,23,34",3) 返回结果是false; 
 * @param string $mystring
 * @param string $findme
 * @return boolean
 * @author yxd
 */
function instr($mystring,$findme){
	$myarray    = explode(",", $mystring);
	if(in_array($findme, $myarray))
		return true;
	else  
		return false;
}
function multi2single($key, $arrays) {
	$results = array ();
	foreach ($arrays AS $array) {
		array_push($results, $array[$key]);
	}
	return $results;
}

/*
 * 获得指定sku是单料号还是组合料号及对应的真实sku及对应数量，
 * 如果在memcache中未找到直接返回false，如果在memcache中存在，
 * 返回格式为array('sku'=>array('$sku1'=>$nums1,'$sku2'=>nums2,...),'isCombine'=>1),
 * 其中sku键值对应的是真实料号及数量的键值对，isCombine键值对应是否是组合料号，1为组合料号，0为单料号
*/
function getRealSkuAndNums($sku) {
	global $memc_obj; //调用memcache对象
	$skuArr = array (
		'sku' => array (
			$sku => 1
		),
		'isCombine' => 0
	); //默认为单料号
	$skuInfo = $memc_obj->get_extral("sku_info_" . $sku);
	if (empty ($skuInfo)) {
		return false;
	}
	if (isset($skuInfo['sku']) && is_array($skuInfo['sku'])) { //如果为组合料号时
		$tmpArr = array ();
		foreach ($skuInfo['sku'] as $key => $value) { //循环$skuInfo下的sku的键，找出所有真实料号及对应数量,$key为组合料号下对应的真实单料号，value为对应数量
			$tmpArr[$key] = $value;
		}
		$skuArr['sku'] = $tmpArr;
		$skuArr['isCombine'] = 1;
	}
	return $skuArr;
}

//检查跟踪号是否有效
function validate_trackingnumber($num){
	if(empty($num)||preg_match("/^0/", $num)){
		return false;
	}else{
		return true;
	}
}

//生成随机数
function round_num2($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return number_format($r,2);
}

function calceveryweight($weightarray, $totalfee){
	$feearray = array();
	$totalweight = array_sum($weightarray);
	foreach ($weightarray AS $weight){
		$feearray[] = round(($totalfee*$weight/$totalweight), 2);
	}
	return $feearray;
}

/**
 * 根据状态码code获取状态码名称
 * @param int $cid 状态码code
 * @return string
 * @author lzx
 */
function get_statusmenunamebyid($cid){
    $statusmenu = A('StatusMenu')->act_getStatusMenuByCode($cid);
	return $statusmenu['statusName'];
}

/**
 * 根据用户id获取用户真实姓名
 * @param int $userid
 * @return string
 * @author lzx
 */
function get_usernamebyid($userid){
	$userinfo = M('InterfacePower')->getUserInfo($userid);
	return isset($userinfo['userName']) ? $userinfo['userName'] : '';
}

/**
 * 通过账号名称获取账号Id
 * @param string accout
 * @return int id
 * @author yxd
 */
function get_accountIdByName($account){
	$id    = M('Account')->getAccountIdByName($account);
	return $id;
}
/**
 * 根据用户id获取用户token
 * @param int $userid
 * @return string
 * @author lzx
 */
function get_usertokenbyid($userid){
	$userinfo = M('InterfacePower')->getUserInfo($userid);
	return isset($userinfo['userToken']) ? $userinfo['userToken'] : '';
}

/**
 * 获取当前用户id
 * @return string
 * @author lzx
 */
function get_userid(){
	return $_SESSION['sysUserId'];
}

/**
 * 获取当前用户token
 * @return int
 * @author lzx
 */
function get_usertoken(){
	return $_SESSION['userToken'];
}

/**
 * 获取当前用户用户名
 * @return int
 * @author lzx
 */
function get_username(){
	return $_SESSION['userName'];
}

/**
 * 获取当前用户中文用户名
 * @return int
 * @author lzx
 */
function get_usercompanyid(){
	return $_SESSION['companyId'];
}

/**
 * 验证日期是否合法
 * @param date $date
 * @return bool
 * @author lzx
 */
function validate_date($date){
	return preg_match("/^20[0-9]{2}-[0-1][0-9]-[0-3][0-9]$/", $date)>0;
}
/**
 * 验证日期和时间是否合法
 * @param datetime $datetime
 * @return bool
 * @author lzx
 */
function validate_datetime($datetime){
	return preg_match("/^20[0-9]{2}-[0-1][0-9]-[0-3][0-9]\s[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $datetime)>0;
}
/**
 * 验证email
 * @param string $emial
 * @return  bool
 * @author yxd
 */
function validate_email($email){
	return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email);
}
/**
 * 验证字母数字-组合  12122-1212-3r
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_numTchar($value){
	return preg_match('/^[a-z0-9A-Z]+([a-z0-9A-Z]+-[a-z0-9A-Z]*)*[a-z0-9A-Z]$/', $value);
}
/**
 * 验证字母数字大写字母  3J447573WK9855335
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_numUpchar($value){
	return preg_match('/^[0-9A-Z]*[0-9A-Z]$/', $value);
}
/**
 * 验证字符和空格3J447573WK9855335
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_spaceNchare($value){
	return preg_match('/^[A-Za-z0-9][A-Za-z0-9\s]*[A-Za-z0-9]*$/', $value);
}

/**
 * 验证2为小数浮点数
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_float2($value){
	return preg_match('/^(([1-9]\d*.\d{2})|(0.\d{2}))$/', $value);
}

/**
 * 验证电话格式
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_phone($value){
	return preg_match('/^\d{5,11}$|^(\+|\d)((\d{1,4})(\s|-)\d{1,11})*\d$/', $value);
}

/**
 * 验证邮编格式
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_zipCode($value){
	return preg_match('/^[A-Za-z0-9]*[\s\-]?[A-Za-z0-9]*$/', $value);
}

/**
 * 验证3为小数浮点数
 * @param string $value
 * @return  bool
 * @author yxd
 */
function validate_float3($value){
	return preg_match('/^(([1-9]\d*.\d{3})|(0.\d{3}))$/', $value);
}

/**
 * 驼峰类命名方式转化为下划线命名方式
 * @param string $hname
 * @return string
 * @author lzx
 */
function hump2underline($hname){
	return str_replace(
			array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"), 
			array("_a", "_b", "_c", "_d", "_e", "_f", "_g", "_h", "_i", "_j", "_k", "_l", "_m", "_n", "_o", "_p", "_q", "_r", "_s", "_t", "_u", "_v", "_w", "_x", "_y", "_z"), $hname);
}
/**
 * 下划线命名方式转化为驼峰类命名方式
 * @param string $uname
 * @author lzx
 */
function underline2hump($uname){
	return str_replace(
				array("_a", "_b", "_c", "_d", "_e", "_f", "_g", "_h", "_i", "_j", "_k", "_l", "_m", "_n", "_o", "_p", "_q", "_r", "_s", "_t", "_u", "_v", "_w", "_x", "_y", "_z"),
				array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"), $uname);
}

/**
 * 根据提示码获取提示信息
 * string get_promptmsg ( string $msgNum, mixed var [, mixed ...] )
 * @param int $msgNum 提示信息编号
 * @param string $var 替换变量
 * @param .... 多变量扩展
 * @global $dbConn 数据库对象全局变量
 * @global $memc_obj 缓存对象全局变量
 * @return string
 * @author lzx
 */
function get_promptmsg($msgNum){
	$msgNum  = intval($msgNum);
	$numargs = func_num_args();
    if ($numargs===0){
        return get_promptmsg(10004);
    }
    if ($msgNum===0){
    	 return get_promptmsg(10015);
    }
    $arg_list = func_get_args();
    $data = MC("SELECT errormsg FROM ".C('DB_PREFIX')."prompt_msg WHERE id={$msgNum}", 1800);
	if (!isset($data[0]['errormsg'])){
		return get_promptmsg(10016, $msgNum);
	}
	$arg_list[0] = $data[0]['errormsg'];
	return call_user_func_array('sprintf',$arg_list);
}

/**
 * 数组转为字符串数组
 * @param array $array
 * @return array
 * @author lzx
 */
function array2strarray($array) {
	$results = array ();
	foreach ($array AS $_k => $_v) {
		$results[$_k] = "'{$_v}'";
	}
	return $results;
}

/**
 * 数组转化为http请求参数
 * @param array $array
 * @return string
 * @author lzx
 */
function array2http($array){
	$param = array(); 
	foreach ($array as $key => $val) { 
		$param[] = "{$key}=".urlencode($val); 
	} 
	return implode('&', $param); 
}

/**
 * 数组转化为查询语句
 * @param array $data
 * @return array
 * @author lzx
 */
function array2sql($array) {
	$sql_array = array ();
	foreach ($array AS $_k => $_v) {
		if (empty ($_k)) {
			continue;
		}
		$_v = trim($_v);
		if (ctype_digit($_v) && preg_match("/^[1-9][0-9]+$/", $_v)) {
			$sql_array[] = "`{$_k}`={$_v}";
		} else {
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}

/**
 * 数组转化为查询简单条件
 * @param array $data
 * @return array
 * @author lzx
 */
function array2where($data, $pre=''){
	$sqlarr = array();
	foreach ($data AS $key=>$value){
		if(is_array($value)){
			foreach ($value AS $k=>$v){
				if($k=='$gte'){
					$sqlarr[] = is_numeric($v) ? "{$key}>={$v}" : "{$key}>='{$v}'";
				}else if($k=='$gt'){
					$sqlarr[] = is_numeric($v) ? "{$key}>{$v}" : "{$key}>'{$v}'";
				}else if($k=='$lt'){
					$sqlarr[] = is_numeric($v) ? "{$key}<{$v}" : "{$key}<'{$v}'";
				}else if($k=='$lte'){
					$sqlarr[] = is_numeric($v) ? "{$key}=<{$v}" : "{$key}=<'{$v}'";
				}else if($k=='$ne'){
					$sqlarr[] = is_numeric($v) ? "{$key}!={$v}" : "{$key}!='{$v}'";
				}else if($k=='$e'){
					$sqlarr[] = is_numeric($v) ? "{$key}={$v}" : "{$key}='{$v}'";
				}else if($k=='$b'){
					list($starttime, $endtime) = explode('-', $v);
					$sqlarr[] = "{$key} BETWEEN {$starttime} AND {$endtime}";
				}else if($k=='$in'){
					$v_str = is_array($v) ? implode(',', $v) : $v;
					$sqlarr[] = "{$key} IN ({$v_str})";
				}else if($k=='$nin'){
					$v_str = is_array($v) ? implode(',', $v) : $v;
					$sqlarr[] = "{$key} NOT IN ({$v_str})";
				}
			}
		}
	}
	if (!empty($pre)) array_walk($sqlarr , 'add_tableprefix', $pre);
	return $sqlarr;
}

/**
 * array2where函数调用，用来自动增加前缀
 * Enter description here ...
 * @param string &$field
 * @param string $key
 * @param string $prefix
 * @return null
 * @author lzx
 */
function add_tableprefix(&$field, $key, $prefix){
	$field="{$prefix}.$field";
}

/**
 * 获取请求IP
 * @return string 用户IP地址
 * @author lzx
 */
function get_ip() {
	if (isset($_SERVER)) {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if (getenv('HTTP_X_FORWARDED_FOR')) {
			$realip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_CLIENT_IP')) {
			$realip = getenv('HTTP_CLIENT_IP');
		} else {
			$realip = getenv('REMOTE_ADDR');
		}
	}
	
	$realip_arr = explode(',' , $realip) ;
	$realip = (isset($realip_arr[0]) && $realip_arr[0] != 'unknown') ? $realip_arr[0] : '0.0.0.0' ;
	return $realip ;
}

/**
 * 传入参数为中国重庆时区时间戳
 * @param int $time
 * @return array
 * @author lzx
 */
function get_ebay_timestamp($time){
	return	$time-(3600*8);
}

/**
 * 替换特殊字符（'\','''）
 * @param string $str
 * @return mixed
 * @author czq
 */
function str_rep($str){
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	return $str;
}

/**
 * API接口返回函数封装
 * @param int|array $message 信息编码 匹配表---om_prompt_msg
 * @param array $data
 * @author lzx
 */
function json_return($message, $data=''){
	if (is_array($message)){
		$msgNo = array_shift(array_keys($message));
    	$msg = implode('<br>', $message);
	}else{
		$msgNo   = intval($message);
		$numargs = func_num_args();
	    if ($numargs<3){
			$msg = get_promptmsg($msgNo);
	    }else{
	    	$arg_list = func_get_args();
		    $varlist  = array_slice($arg_list, 2);
			$varstr   = implode(',', array2strarray(array_map('addslashes', array_map('htmlspecialchars', $varlist))));
		    eval("\$msg = get_promptmsg({$msgNo}, {$varstr});");
	    }
	}
	echo json_encode(array("errCode"=>$msgNo, "errMsg"=>$msg, "status"=>$msg=='Success' ? true : false, "data"=>$data));
	exit;
}

/**
 * 对象转为数组
 * @param object $obj
 * @return array
 * @author czq
 */
function object_array($array) {
	if(is_object($array)) {
		$array = (array)$array;
	} 
	if(is_array($array)) {
		foreach($array as $key=>$value) {
			$array[$key] = object_array($value);
		}
	}
	return $array;
}

/**
 * 根据传过来的channelId数组，组装成array('transportId'=>array(channelId1,channelId2)))的格式返回
 * @param array $channelIdArr
 * @return array
 * @author zqt
 */
function returnTCArrFromChannelIdArr($channelIdArr) {
    $returnArr = array();
    $channelIdList = M('InterfaceTran')->key('id')->getChannelList();//获取所有的channel列表
    foreach($channelIdArr as $channelId){
        if(intval($channelId) > 0){
            $tmpTransportId = $channelIdList[$channelId]['carrierId'];
            if(intval($tmpTransportId) > 0){
                $returnArr[$tmpTransportId][] = $channelId;
            }
        }
    }
	return $returnArr;
}

/**
 * 获取所有的transportId和channelId信息，组装成array('transportId'=>array(channelId1,channelId2)))的格式返回
 * @return array
 * @author zqt
 */
function returnTCArrAll() {
    $returnArr = array();
    $transportIdList = M('InterfaceTran')->key('id')->getCarrierList(2);
    $channelIdList = M('InterfaceTran')->key('id')->getChannelList();//获取所有的channel列表
    foreach($channelIdList as $channelId=>$channelArr){
        if(intval($channelId) > 0){
            $returnArr[$channelIdList[$channelId]['carrierId']][] = $channelArr;
        }
    }
	return $returnArr;
}

/**
 * 根据运输方式Id获取渠道列表信息
 * @author yxd
 * @return array
 */
function getChinnelbyCarrierId($carrierId){
	$chinnels     = M('InterfaceTran')->getChannelList();
	$reData       = array();
	foreach($chinnels as $value){
		$reData[$value['carrierId']][]    = array("id"=>$value['id'],"channelName"=>$value['channelName']);
	}
	return $reData[$carrierId];
}

/**
     * 运输方式Id和运输名称键值对数组
     * @retrun array
     * @author yxd
     */
    function getCarrierList(){
    	$carrierLists    = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
    	foreach ( $carrierLists as $carrierList ) {
    		$carriers [$carrierList ['id']]    = $carrierList ['carrierNameCn'];
    	}
    	return $carriers;
    }

/**
 * 将二维关系数组转换成字符串返回
 * @param array $Tarr
 * @param symbol1 $symbol 第一,二维度之间的字符串，默认为':'
 * @param symbol2 $symbol 第二维度之间的字符串，默认为','
 * @param symbol2 $symbol 第一维度之间的字符串，默认为'|'
 * @return str
 * @author zqt
 */
function Tarr2Str($Tarr, $symbol1=':', $symbol2=',', $symbol3='|') {
    $tmpArr1 = array();
    foreach($Tarr as $key=>$valueArr){
        $tmpArr2 = array();
        foreach($valueArr as $value){
            $tmpArr2[] = $value;
        }
        $tmpArr1[] = implode($symbol1, array($key, implode($symbol2, $tmpArr2)));
    }
	return implode($symbol3, $tmpArr1);
}
function getSkuRealStock($sku, $storeId = 1){
	
	//从仓库接口获取可用库存
	$stock_info = $arr = M('interfaceWh')->getSkuStock(array($storeId=>array($sku)));;//料号可以用150测试
	if(empty($stock_info) || empty($stock_info[$sku][$storeId])){
		return 0;
	}
	return intval($stock_info[$sku][$storeId]);
}
function getSkuAvailableStock($sku, $storeId = 1){
	//从仓库接口获取可用库存
	$stock_info = M("interfaceWh")->getSkuStockByArrNew(array($storeId=>array($sku)));//料号可以用150测试
	if(empty($stock_info) || empty($stock_info[$storeId])){
		return 0;
	}
	
	return intval($stock_info[$storeId]);
}
function getOrderStatusWaitingSend(){
	$ORDER_STATUS = C('ORDER_STATUS');
	if(empty($ORDER_STATUS)){
		return array();
	}
	return $ORDER_STATUS['waitingsend'];
}