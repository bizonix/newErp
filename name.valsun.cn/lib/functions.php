<?php
/*
 * 系统共用函数页面
 */

/*
 * 跳转到报错页面
 * 作者 涂兴隆
 * $msgdata 数组 结构 Array('data'=>array('消息1','消息2'... ...),link=>'显示信息时的url链接地址')
 */
function goErrMsgPage($msgdata){
    $msg = urlencode(json_encode($msgdata));
    header('location:index.php?mod=showMessage&act=showErrMsg&data='.$msg);
}

/*
 * 跳转到成功提示页面
 *作者 涂兴隆
 * $msgdata 数组 结构 Array('data'=>array('消息1','消息2'... ...),link=>'显示信息时的url链接地址')
 */
function goOkMsgPage($msgdata){
    $msg = urlencode(json_encode($msgdata));
    header('location:index.php?mod=ShowMessage&act=showOkMsg&data='.$msg);
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

/**
简单的重定向函数 add by xiaojinhua
*/
function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

function getCateTitleOrId($type="*",$val=' '){
	global $dbConn;
	$link=$dbConn->link;
	$where=	"where id={$val}";
	if(trim($type)=="*"){
		$type="*";
		$where=" ";
	}
	$sql="select ".$type." from opensys_api_categories ".$where;
	$query=mysql_query($sql,$link);
	if($query){
	   $ret	=	$dbConn->fetch_array_all($query);
		if(trim($type)=="*"){
			return  $ret?$ret:"无此分类";
		}
		return isset($ret[0][$type])?($ret[0][$type]):"无此分类";
	}
}

function array2sql($array){
	$sql_array = array();
	foreach ($array AS $_k=>$_v){
	   if (empty($_k)){
	       continue;
       }
       $_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
	   if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`={$_v}";
	   } else {
            $_v = Deal_SC($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}

function Deal_SC($str){
	//处理特殊字符,add by　Herman.Xi @ 20130307
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	$tes = array("=" , ")" , "(" , "{", "}");
	foreach($tes as $v){
		$str = str_replace($v,"",$str);
	}
	return addslashes($str);
}

/**
 * 根据收货单记录id查找对应的明细
 */
function getWhRecMaDetailByRmId($rmId){
    global $dbConn;
    if(empty($rmId)){
        return array();
    }
	$sql = "SELECT * FROM wh_receipt_management_details WHERE rmId='$rmId' ORDER BY insertTime";
	$sql = $dbConn->query($sql);
	if($sql){
		return $dbConn->fetch_array_all($sql);
	}else{
	  return array();
	}
}

/*
 * 自动生成出库单据编码
 */
function getIostoreOrdersn(){
	global $dbConn;
	while (1){
		$io_ordersn = "WH-IO-INVOICE".date("ymd").rand(1000, 9999);
		$sql = "SELECT ordersn FROM wh_iostore WHERE ordersn='{$io_ordersn}'";
		$result = $dbConn->query($sql);
		$num = $dbConn->num_rows($result);
		if ($num==0){
			return $io_ordersn;
		}
	}
}

function tep_not_null($value){
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

function get_realskulist($id){
	/**
	 *add by Herman.Xi ·2013-08-26
	 *获取订单号下面所有的料号信息,每个detail下面的信息，包括合并包裹,多料号,单料号,组合订单,合并订单
	 */
	global $dbConn;
	$skuinfos = array();	
	$orderdetails = "SELECT * FROM wh_shipping_orderdetail where shipOrderId ='{$id}' and storeId = 1 ";
	$orderdetails = $dbConn->query($orderdetails);
	$orderdetails = $dbConn->fetch_array_all($orderdetails);
	foreach ($orderdetails AS $_k => $odlist){
		$sku = trim($odlist['sku']);
		$combineSku = $odlist['combineSku'];
		$amount = $odlist['amount'];
		
		if(tep_not_null($combineSku)){
			$skuinfos[$combineSku][$sku] = $amount;
		}else{
			$skuinfos[$sku] = $amount;
		}
	}
	
	return $skuinfos;
}

function get_realskunum($id){
	/**
	 *add by Herman.Xi ·2013-08-26
	 *获取订单号下面所有的料号数量,每个detail下面的信息，包括合并包裹,多料号,单料号,组合订单,合并订单
	 */
	global $dbConn;
	$skuinfos = array();
	$orderdetails = "SELECT * FROM wh_shipping_orderdetail where shipOrderId ='{$id}' and storeId = 1 ";
	$orderdetails = $dbConn->query($orderdetails);
	$orderdetails = $dbConn->fetch_array_all($orderdetails);
	foreach ($orderdetails AS $_k => $odlist){
		$sku    = trim($odlist['sku']);
		$amount = $odlist['amount'];	
		if(isset($skuinfos[$sku])){
			$skuinfos[$sku] += $amount;
		}else{
			$skuinfos[$sku] = $amount;
		}
	}

	return $skuinfos;
}

function scientific_convert_digital($ret){
	//读取的科学计数法转换成数字
	//add By Herman.Xi @ 20130222
	if(is_numeric($ret)){
		$ret = number_format($ret,'0','','');
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
function google_translate($text,$fromLanguage='zh-cn',$toLanguage='en'){
	if(empty($text))return false;
	$language="{$fromLanguage}|{$toLanguage}";
	@set_time_limit(0);
	$html = "";
	$ch=curl_init("http://translate.google.com/?langpair=".urlencode($language)."&text=".urlencode($text));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_HEADER, 0);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	$html=curl_exec($ch);
	if(curl_errno($ch))$html = "";
	curl_close($ch);
	if(!empty($html)){
		$x=explode("</span></span></div></div>",$html);
		$x=explode("onmouseout=\"this.style.backgroundColor='#fff'\">",$x[0]);
		return $x[1];
	}else{
		return false;
	}
}

//转换成array
function format_array($var){
	if(empty($var)){
		return array();
	}else if(is_array($var)){
		return $var;
	}else if(is_numeric($var)){
		return array($var);
	}else if(is_string($var)){
		if(strpos($var, ',')){
			return implode(',', $var);
		}else{
			return array($var);
		}
	}else{
		return array();
	}
}

function write_log($file, $data){
	
	global $truename;

	$config_path = dirname(dirname(__FILE__)).'/log';
	list($filepath, $filename) = explode ( '/', $file );
	$dirPath = $config_path.'/'.$filepath;
	if (!is_dir($dirPath)){
    	mkdir( $dirPath);
    }
    $readpath = $dirPath.'/'.$filename;
	if (!$handle=fopen($readpath, 'a')) {
         return false;
    }
    if(flock($handle, LOCK_EX)) { 
	    if (fwrite($handle, $truename.'===='.$data) === FALSE) {
	        return false;
	    }
	    flock($handle, LOCK_UN);
    }
    fclose($handle);
    return true;
}

function mkdirs($path){
	$path_out=preg_replace('/[^\/.]+\/?$/','',$path);
	if(!is_dir($path_out)){			
		mkdirs($path_out);
	}
	mkdir($path);
}

function write_a_file($file, $data){
	$tmp_dir = dirname($file);
	if(!is_dir($tmp_dir)){
		mkdirs($tmp_dir);
	}
	if (!$handle=fopen($file, 'a')) {
		 return false;
	}
	if(flock($handle, LOCK_EX)) { 
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function write_w_file($file, $data){
	$tmp_dir = dirname($file);
	if(!is_dir($tmp_dir)){
		mkdirs($tmp_dir);
	}
	if (!$handle=fopen($file, 'w')) {
		 return false;
	}
	if(flock($handle, LOCK_EX)) { 
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function read_file($file){
	if(!is_file($file)){
		return false;
	}
	return file_get_contents($file);;
}

function read_and_empty_file($file){
	if(!is_file($file)){
		return false;
	}
	$contents =  file_get_contents($file);
	if (!$handle=fopen($file, 'w')) {
		 return false;
	}
	return $contents;
}

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return str_replace(',', '', number_format($r,2));
}

function excel2array($filename, $rownums=0, $num=2){
	global $PHPExcel;
	$Worksheet = $PHPExcel->getActiveSheet();
	$highestRow = $Worksheet->getHighestRow();
	$highestColumn = $Worksheet->getHighestColumn();
	$highestColumnIndex = empty($rownums) ? PHPExcel_Cell::columnIndexFromString($highestColumn) : $rownums;
	$excelData = array();
	for ($row=1; $row<=$highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			if ($highestColumnIndex>100) break;
			$value = $Worksheet->getCellByColumnAndRow($col, $row)->getValue();
			if (preg_match("/^[0-9]+\.[0-9]+$/", $value)){
				$value = empty($num) ? $value : round_num($value, $num);
			}
			$excelData[$row][] = $value;
		}
	}
	return $excelData;
}

function array2excel($data, $colnums){
	global $PHPExcel;
	$Worksheet = $PHPExcel->getActiveSheet();
	foreach ($data AS $key=>$value){
		$Worksheet->setCellValueByColumnAndRow($key, $colnums, $value);
	}
	return true;
}

function get_columns($table){
	
	global $dbcon;
	
	$sql = "SHOW COLUMNS FROM {$table}";
	$sql = $dbcon->execute($sql);
	$columns = $dbcon->getResultArray($sql);
	$fields = array(); 
	foreach ($columns AS $column){
		array_push($fields, $column['Field']);
	}
	return $fields;
}

function array2strarray($array){
	$results = array();
	foreach ($array AS $_k=>$_v){
		$results[$_k] = "'{$_v}'";
	}
	return $results;
}

function multi2single($key, $arrays){
	$results = array();
	foreach ($arrays AS $array){
		array_push($results, $array[$key]);
	}
	return $results;
}

/*
 * 整理从前端发过来的数据
* 返回发货单数据数组
*/
function clearupData(){
    $result = array();
    $idlist = isset($_POST['ids']) ? trim($_POST['ids']) : 0;
    if(empty($idlist)){
        return $result;
    }
    $idarray = explode(',', $idlist);
    $po_obj = new PackingOrderModel();
    $result = $po_obj->getOrderInfoByIdList($idarray);
    return $result;
}

/*
 * 计算某个sku的最优仓位信息
 * 失败 返回空字符串 
 */
function getLocationBySku($sku,$storeid=1){
    global $dbConn;
    //获得料号id
    $getskuid = 'select ppr.positionId from wh_product_information as pi join wh_product_position_relation as ppr on pi.id=ppr.pid where pi.sku='."'$sku' and pi.storeId=$storeid";
    //echo $getskuid;exit;
    $posarr = $dbConn->fetch_array_all($dbConn->query($getskuid));
    if (empty($posarr)) {
    	return '';
    }
    $idar = array();
    foreach ($posarr as $poval){
        $idar[] = $poval['positionId'];
    }
    $sqlstr = implode(',', $idar);
    //根据餐位id获得仓位的详细信息
    $sql = 'select pName, x_alixs, y_alixs from wh_position_distribution where id in ('.$sqlstr.')';
    //echo $sql;exit;
    $positions = $dbConn->fetch_array_all($dbConn->query($sql));
    if (empty($positions)) {
    	return '';
    }
    
    $min = intval($positions[0]['x_alixs']) + intval($positions[0]['y_alixs']);
    $location = $positions[0]['pName'];
    foreach ($positions as $pval){
        $x = $pval['x_alixs']+$pval['y_alixs'];
        if($x < $min){
            $min = $x;
            $location = $pval['pName'];
        }
    }
    //echo $location;exit();
    return $location;
}

/*
 * 根据sku获得某个sku的详细信息
 * $sku
 */
function getSkuInfoBySku($sku){
    global $dbConn;
    $sql = 'select * from wh_product_information where sku= '."'$sku'";
    return $dbConn->fetch_first($sql);
}
