<?php

/*
 * 系统共用函数页面
 */
/**
简单的重定向函数 add by xiaojinhua
*/
function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

/*
 * 跳转到报错页面
 * 作者 涂兴隆
 * $msgdata 数组 结构 Array('data'=>array('消息1','消息2'... ...),link=>'显示信息时的url链接地址')
 */
function goErrMsgPage($msgdata) {
	$msg = urlencode(json_encode($msgdata));
	header('location:index.php?mod=showMessage&act=showErrMsg&data=' . $msg);
}

/*
 * 跳转到成功提示页面
 *作者 涂兴隆
 * $msgdata 数组 结构 Array('data'=>array('消息1','消息2'... ...),link=>'显示信息时的url链接地址')
 */
function goOkMsgPage($msgdata) {
	$msg = urlencode(json_encode($msgdata));
	header('location:index.php?mod=ShowMessage&act=showOkMsg&data=' . $msg);
}

/*
函数名称：post_check()
函数作用：对用户提交内容进行处理,防止Sql注入
参　　数：$post: 要提交的内容
返 回 值：$post: 返回过滤后的内容
*/

function post_check($post) {
	if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
		$post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
	}
	$post = str_replace("_", "_", $post); // 把 '_'过滤掉
	$post = str_replace("%", "%", $post); // 把 '%'过滤掉
	$post = nl2br($post); // 回车转换
	$post = htmlspecialchars($post); // html标记转换
	return $post;
}

/*
函数名称：inject_check()
函数作用：检测提交的值是不是含有SQL注射的字符，防止注射，保护服务器安全
参　　数：$sql_str: 提交的变量
返 回 值：返回检测结果，ture or false
*/
function inject_check($sql_str) {
	return eregi('select|insert|and|or|update|delete|\'|/*|*|../|./|union|into|load_file|outfile', $sql_str); //进行过滤
}

function getCateTitleOrId($type = "*", $val = ' ') {
	global $dbConn;
	$link = $dbConn->link;
	$where = "where id={$val}";
	if (trim($type) == "*") {
		$type = "*";
		$where = " ";
	}
	$sql = "select " . $type . " from opensys_api_categories " . $where;
	$query = mysql_query($sql, $link);
	if ($query) {
		$ret = $dbConn->fetch_array_all($query);
		if (trim($type) == "*") {
			return $ret ? $ret : "无此分类";
		}
		return isset ($ret[0][$type]) ? ($ret[0][$type]) : "无此分类";
	}
}

function array2sql($array) {
	$sql_array = array ();
	foreach ($array AS $_k => $_v) {
		if (empty ($_k)) {
			continue;
		}
		$_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
		if (ctype_digit($_v) && preg_match("/^[1-9][0-9]+$/", $_v)) { //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`={$_v}";
		} else {
			$_v = Deal_SC($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}

function array2sql_extral($array) {
	$sql_array = array ();
	foreach ($array AS $_k => $_v) {
		if (empty ($_k)) {
			continue;
		}
		$_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
		if (ctype_digit($_v) && preg_match("/^[1-9][0-9]+$/", $_v)) { //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`={$_v}";
		} else {
			$_v = mysql_real_escape_string($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}

function Deal_SC($str) {
	//处理特殊字符,add by　Herman.Xi @ 20130307
	$str = str_replace("'", "&acute;", $str);
	$str = str_replace("\"", "&quot;", $str);
	$tes = array (
		"=",
		"(",
		")",
		"{",
		"}"
	);
	foreach ($tes as $v) {
		$str = str_replace($v, "", $str);
	}
	return addslashes($str);
}

function tep_not_null($value) {
	//判断一个数不为空
	if (is_array($value)) {
		if (sizeof($value) > 0) {
			return true;
		} else {
			return false;
		}
	} else {
		if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
			return true;
		} else {
			return false;
		}
	}
}

function scientific_convert_digital($ret) {
	//读取的科学计数法转换成数字
	//add By Herman.Xi @ 20130222
	if (is_numeric($ret)) {
		$ret = number_format($ret, '0', '', '');
		return $ret;
	}
	return $ret;
}

/*
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

//转换成array
function format_array($var) {
	if (empty ($var)) {
		return array ();
	} else
		if (is_array($var)) {
			return $var;
		} else
			if (is_numeric($var)) {
				return array (
					$var
				);
			} else
				if (is_string($var)) {
					if (strpos($var, ',')) {
						return implode(',', $var);
					} else {
						return array (
							$var
						);
					}
				} else {
					return array ();
				}
}

function write_log($file, $data) {

	global $truename;

	$config_path = dirname(dirname(__FILE__)) . '/log';
	list ($filepath, $filename) = explode('/', $file);
	$dirPath = $config_path . '/' . $filepath;
	if (!is_dir($dirPath)) {
		mkdir($dirPath);
	}
	$readpath = $dirPath . '/' . $filename;
	if (!$handle = fopen($readpath, 'a')) {
		return false;
	}
	if (flock($handle, LOCK_EX)) {
		if (fwrite($handle, $truename . '====' . $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function mkdirs($path) {
	/*
	$path_out = preg_replace('/[^\/.]+\/?$/', '', $path);
	var_dump($path_out);
	if (!is_dir($path_out)) {
		mkdir($path_out);

	}
	var_dump($path);
	mkdir($path);
	*/
	$all	=	explode("/",$path);

	$new_dir	=	"/";
	foreach ($all as $cur){
		$new_dir	.=	$cur;
		if (!is_dir($new_dir)) {
			mkdir($new_dir);
		}
		$new_dir	.="/";
	}

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
	;
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

function excel2array($PHPExcel, $filename, $rownums = 0, $num = 2) {
	$Worksheet = $PHPExcel->getActiveSheet();
	$highestRow = $Worksheet->getHighestRow();
	$highestColumn = $Worksheet->getHighestColumn();
	$highestColumnIndex = empty ($rownums) ? PHPExcel_Cell :: columnIndexFromString($highestColumn) : $rownums;
	$excelData = array ();
	for ($row = 1; $row <= $highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			if ($highestColumnIndex > 100)
				break;
			$value = $Worksheet->getCellByColumnAndRow($col, $row)->getValue();
			if($value instanceof PHPExcel_RichText){
				$value = $Worksheet->getCellByColumnAndRow($col, $row)->getValue()->getPlainText();
			}
			if (preg_match("/^[0-9]+\.[0-9]+$/", $value)) {
				$value = empty ($num) ? $value : round_num($value, $num);
			}
			$excelData[$row][] = $value;
		}
	}
	return $excelData;
}

function array2excel($data, $colnums) {
	global $PHPExcel;
	$Worksheet = $PHPExcel->getActiveSheet();
	foreach ($data AS $key => $value) {
		$Worksheet->setCellValueByColumnAndRow($key, $colnums, $value);
	}
	return true;
}

function get_columns($table) {

	global $dbConn;

	$sql = "SHOW COLUMNS FROM {$table}";
	$sql = $dbConn->query($sql);
	$columns = $dbConn->fetch_array_all($sql);
	$fields = array ();
	foreach ($columns AS $column) {
		array_push($fields, $column['Field']);
	}
	return $fields;
}

function array2strarray($array) {
	$results = array ();
	foreach ($array AS $_k => $_v) {
		$results[$_k] = "'{$_v}'";
	}
	return $results;
}

function multi2single($key, $arrays) {
	$results = array ();
	foreach ($arrays AS $array) {
		array_push($results, $array[$key]);
	}
	return $results;
}

/*
 * 整理从前端发过来的数据
* 返回发货单数据数组
*/
function clearupData() {
	$result = array ();
	$idlist = isset ($_POST['ids']) ? trim($_POST['ids']) : 0;
	if (empty ($idlist)) {
		return $result;
	}
	$idarray = explode(',', $idlist);
	$po_obj = new PackingOrderModel();
	$result = $po_obj->getOrderInfoByIdList($idarray);
	return $result;
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

/*
 * 根据用户id获取用户名称
*/
function getUserNameById($userId){
$mem = new Memcache;
$mem->connect("192.168.200.222",11211);
$var = $mem->get('GlobalUser_'.$userId);
if(empty($var))
{
	$url = 'dev.power.valsun.cn/api/mem.php';//开发环境
	$urlPost = 'userId='.$userId;
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_URL,$url);//设置你要抓取的URL
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//设置CURL参数，要求结果保存到字符串还是输出到屏幕上
	curl_setopt($curl,CURLOPT_POST,1);//设置为POST提交
	curl_setopt($curl,CURLOPT_POSTFIELDS,$urlPost);//提交的参数
	$data=curl_exec($curl);//运行CURL，请求网页
	curl_close($curl);
}
$var = $mem->get('GlobalUser_'.$userId);
$var = json_decode($var, true);
return $var[0]['userName'];
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

function func_explode($str, $e='*', $k='1'){
	$arr = explode($e,$str);
	if(count($arr) > 1){
		return trim($arr[$k]);
	}
	return trim($str);
}

function get_realtime($sku){
	$realtimes = 1;
	if(strpos($sku,"*") === false){
		return 1;
	}else{
		$sku_arr = explode('*', $sku);
		$realtimes = $sku_arr[0];
		return $realtimes;
	}
}

function str_rep($str){

	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	return $str;
}

function auto_swith_transport($ebay_carrier){
	switch($ebay_carrier){
		case 'hongkong post air mail' :
		case 'hk post air mail' :
		case 'hkpam' :
		case 'hongkong post airmail' :
		case 'hk post airmail' :
			$ebay_carrier = '香港小包挂号';
			break;
		case 'upss' :
		case 'ups express saver' :
			$ebay_carrier = 'UPS';
			break;
		case 'dhl' :
			$ebay_carrier = 'DHL';
			break;
		case 'ems' :
			$ebay_carrier = 'EMS';
			break;
		case 'chinapost post air mail' :
		case 'china post air mail' :
		case 'cpam' :
		case 'china post airmail' :
			$ebay_carrier = '中国邮政挂号';
			break;
		case 'china post air mail (surface)' :
			$ebay_carrier = '中国邮政平邮';
			break;
		case 'epacket' :
			$ebay_carrier = 'EUB';
			break;
		case 'fedex' :
		case 'fedex ip' :
		case 'fedex ie' :
			$ebay_carrier = 'FedEx';
			break;
		default :
			$ebay_carrier = '';
	}
	return $ebay_carrier;
}
