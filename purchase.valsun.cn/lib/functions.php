<?php


/*
函数名称：post_check()
函数作用：对用户提交内容进行处理,防止Sql注入
参　　数：$post: 要提交的内容
返 回 值：$post: 返回过滤后的内容
*/

function post_check($post) {
	$post = checkData($post);
    return $post;
}


// 对特殊字符串进行处理 add by xiaojinhua
function getStr($str) {
    $tmpstr = trim($str);
    $tmpstr = strip_tags($tmpstr);
    $tmpstr = htmlspecialchars($tmpstr);
    $tmpstr = addslashes($tmpstr);
    return $tmpstr;
}

function checkData($data){
     if(is_array($data)){
         foreach($data as $key => $v){
             $data[$key] = checkData($v);
         }
     }else{
         $data = getStr($data);
     }
     return $data;
}

/**
 *  将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function quan2ban($str) {
	$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
			'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
			'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
			'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
			'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
			'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
			'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
			'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
			'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
			'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
			'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
			'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
			'ｙ' => 'y', 'ｚ' => 'z',
			'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
			'】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
			'‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
			'》' => '>',
			'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
			'：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
			'；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
			'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
			' ' => ' ');

	return strtr($str, $arr);
}

/**
 *  将一个字串中含有半角的数字字符、字母、空格或'%+-()'字符转换为相应全角字符
 *
 * @access  public
 * @param   string       $str         待转换字串
 *
 * @return  string       $str         处理后字串
 */
function ban2quan($str) {
	$arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
			'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
			'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
			'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
			'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
			'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
			'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
			'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
			'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
			'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
			'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
			'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
			'ｙ' => 'y', 'ｚ' => 'z',
			'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
			'】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
			'‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
			'》' => '>',
			'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
			'：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',
			'；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
			'”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
			' ' => ' ');
	$arr = array_flip($arr);
	return strtr($str, $arr);
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

function getClientIP(){
    if ($_SERVER['REMOTE_ADDR']) {
        $cip = $_SERVER['REMOTE_ADDR'];
    } elseif (getenv("REMOTE_ADDR")) {
        $cip = getenv("REMOTE_ADDR");
    } elseif (getenv("HTTP_CLIENT_IP")) {
        $cip = getenv("HTTP_CLIENT_IP");
    } else {
    $cip = "unknown";
    }
    return $cip;
}

function writeLog($msg) {
    $time = Date('Y-m-d h:i:s');
    $ip   = getClientIP();
    $info = "$time--$ip ------>$msg";
    //echo  $info;
    $fp = fopen("/data/web/open.valsun.cn/log.txt", "a+");//追加写入
    if($fp) {
        $flag=fwrite($fp, $info."\r\n");
        if(!$flag) {
            echo "写入文件失败<br>";
        }
    } else {
        echo "打开文件失败";
    }

    fclose($fp);
}

function logOut() {
    session_start();
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
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
			$sql_array[] = "`{$_k}`='{$_v}'";
	   } else {
            //$_v = Deal_SC($_v);
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


/*
 *edit by wxb
 *2013/07/05
 *
 *
 **/

function queryResult($sql){
	global $dbConn;
	$link=$dbConn->link;
	$query=mysql_query($sql,$link);
	if($query){
		$ret	=	$dbConn->fetch_array_all($query);
		return $ret;
	}
}
/*
 *edit by wxb
 *2013/07/05
 *
 *
 **/

function queryExcute($sql,$returnType=''){
	global $dbConn;
	$link=$dbConn->link;
	$query=mysql_query($sql,$link);
	if($returnType=="id"){
		return $dbConn->insert_id();
	}else{
		return $query;
	}
}
/*
 *edit by wxb
 *2013/09/16
 *
 *
 **/

function arrToLinkStr($arr,$link){
	if(is_array($arr)){
		$str=' ';
		$count=0;
		foreach($arr as $key=>$val){
			++$count;
			if($count===1){
				$str.=$key."='".$val."'";
			}else{
				$str.=" {$link} ".$key."='".$val."'";
			}
		}
		return $str;
	}
	return "not array";
}

function getBarcode($barcode){
	include_once WEB_PATH.'lib/Barcode.class.php';
	$barcode = new BarCode($barcode,$barcode);
	$barcode->createBarCode();
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

/**
*过滤特殊字符
*/
function str_rep($str) {
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	return $str;
}

/**
*获取SKU图片
*/
function get_sku_imgName($name) {
	if(strpos($name,"*")) {
		$nameArr = explode("*",$name);
		$name = $nameArr[1];
	}
	$pattern1 = '/(^0)(\d+)/i';
	$pattern2 = '/(\w+)_(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern3 = '/(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern4 = '/(\w+)_(\w+)(_\d+)$/i';
	if(preg_match($pattern1,$name)){
		$replacement1 = '${2}';
		$trueName = preg_replace($pattern1, $replacement1, $name);
	}else if(preg_match($pattern2,$name)){
		$replacement2 = '${1}_${2}';
		$trueName = preg_replace($pattern2, $replacement2, $name);
	}else if(preg_match($pattern3,$name)){
		$replacement3 = '${1}';
		$trueName = preg_replace($pattern3, $replacement3, $name);
	}else if(preg_match($pattern4,$name)){
		$replacement4 = '${1}_${2}';
		$trueName = preg_replace($pattern4, $replacement4, $name);
	}else {
		$trueName = $name;
	}


	//$url_file = "http://img.sku.valsun.cn:88/imgs/".$trueName."-G.jpg";
	$url_file = "http://192.168.200.200:9998/imgs/".$trueName."-G.jpg";

	$ch = curl_init();
	$timeout = 10;
	curl_setopt ($ch, CURLOPT_URL, $url_file);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	$contents = curl_exec($ch);
	if (preg_match("/404/", $contents)){
		if(strpos($trueName,"_")) {
			$nameArr = explode("_",$trueName);
			$trueName = $nameArr[0];
		}
	}else{
	}
	return $trueName;
}
/*
 * add by wxb 2013/08/09
 * */

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return str_replace(',', '', number_format($r,2));
}

function purchaseuserNameById($id){
	$sql="SELECT global_user_name FROM power_global_user WHERE global_user_id= ".$id;
	$ret=queryResult($sql);
	if($ret){
		return $ret[0]["global_user_name"];
	}
	return false;
}

/*
 * 功能：获取供应商名称
* @param $id 供应商id号
* return  供应商名称
* 版本：v1.0
* 日期：2013/08/09
* 作者：温小彬
* */
function partnerNameById($id){
	$sql="SELECT username FROM user WHERE id= ".$id;
	$ret=queryResult($sql);
	if($ret){
		return $ret[0]["username"];
	}
	return false;
}

/*
 * 功能：获取某一订单的总成本
* @param $rec 订单号
* return  某一订单的总成本
* 版本：v1.0
* 日期：2013/08/09
* 作者：温小彬
* */
function clacTotalCost($po_id){
	$sql="SELECT price,count FROM ".C('DB_PREFIX')."order_detail WHERE is_delete=0 and po_id= ".$po_id;
	$ret=queryResult($sql);
	if($ret){
		$count=0;
		foreach($ret as $val){
			$count+=$val["price"]*$val["count"];
		}
		return $count;
	}
	return false;
}



function getPartnerById($partnerId){
	global $dbconn;
	$sql="SELECT company_name FROM ph_partner where id={$partnerId}";
	$sql = $dbconn->execute($sql);
	$ret = $dbconn->fetch_one($sql);
	if($ret){
		return $ret["company_name"];
	}
	return false;
}

function getSkuNameBySku($sku){
	$sql ="SELECT goodsName FROM ".C('DB_PREFIX')."goods WHERE sku=".$sku;
	$ret = queryResult($sql);
	if($ret){
		return $ret[0]["goodsName"];
	}else{
		return false;
	}
}


function getUserNameById($id){
	global $dbconn;
	if(empty($id)){
		return false;
	}
	$sql = "SELECT global_user_name FROM power_global_user WHERE global_user_id={$id}";
	$sql = $dbconn->execute($sql);
	$ret = $dbconn->fetch_one($sql);
	if($ret){
		return $ret["global_user_name"];
	}
	return false;
}

function getPartnerBySku($sku){
	global $dbconn;
	$sql = "select b.company_name from pc_goods_partner_relation as a left join ph_partner as b on a.partnerId=b.id where a.sku='{$sku}'";
	$sql = $dbconn->execute($sql);
	$partner = $dbconn->fetch_one($sql);
	return $partner["company_name"];
}

function checkSkuOnWayNum($sku){
	global $dbConn;
	$sql = "select b.count ,b.stockqty from ph_order_detail as b left join ph_order as a on b.po_id=a.id where a.status in(1,2,3) and b.sku='{$sku}' and a.is_delete!=1 and b.is_delete!=1";
	$sql = $dbConn->execute($sql);
	$skuNum = $dbConn->getResultArray($sql);
	$totalnum = 0;
	$totalqty = 0;
	$total = 0;
	if(count($skuNum) != 0){
		foreach($skuNum as $itemNum){
			$totalnum += $itemNum["count"];
			$totalqty += $itemNum["stockqty"];
		}
		$total = $totalnum - $totalqty;
	}
	return $total;
}


function checkSkuOnWayNum1($sku){
	global $dbConn;
	$sql = "select b.count ,b.stockqty from ph_order_detail as b left join ph_order as a on b.po_id=a.id where a.status=3 and b.sku='{$sku}' and a.is_delete!=1 and b.is_delete!=1";
	$sql = $dbConn->execute($sql);
	$skuNum = $dbConn->getResultArray($sql);
	$totalnum = 0;
	$totalqty = 0;
	$total = 0;
	if(count($skuNum) != 0){
		foreach($skuNum as $itemNum){
			$totalnum += $itemNum["count"];
			$totalqty += $itemNum["stockqty"];
		}
		$total = $totalnum - $totalqty;
	}
	return $total;
}

/*
 * 通过sku 获取采购和海外仓料号负责人
 *
 * */
function getUserIdBySku($sku){
	global $dbConn;
	$sql = "select purchaseId,OverSeaSkuCharger from pc_goods where sku='{$sku}'";
	$sql = $dbConn->execute($sql);
	$userInfo = $dbConn->fetch_one($sql);
	return $userInfo;
}

//获取采购员列表
function getPurchaseUserList(){
	global $dbconn;
	$access_id = $_SESSION['access_id'];
	if($access_id != ""){
		$access_id .= ",{$_SESSION['sysUserId']}";
	}else{
		$access_id .= "{$_SESSION['sysUserId']}";
	}
	$sql = "select global_user_id,global_user_name from power_global_user where global_user_id in ({$access_id}) ";
	$sql = $dbconn->execute($sql);
	$userInfo = $dbconn->getResultArray($sql);
	return $userInfo;
}

//获取供应商列表

function getPartnerlist($purchaseId){
	global $dbConn;
	if(!isset($purchaseId)){
		$purchaseId = $_SESSION['sysUserId'];
	}
	$sql = "SELECT distinct a.partnerId,b.company_name as companyname from ph_user_partner_relation as a left join ph_partner as b on a.partnerId=b.id where a.purchaseId='{$purchaseId}' and a.companyname!='' ";
	$sql = $dbConn->execute($sql);
	$partnerInfo = $dbConn->getResultArray($sql);
	return $partnerInfo;
}

function write_log($name,$str){
	$fp = fopen($name, 'a+');
	$time = date('Y-m-d H:i:s', time());
	$str = "[--$time--] === $str \n\n";
	fwrite($fp, $str);
}

function getTallySkuNum($sku){
	global $dbConn;
	$sql = "SELECT tallyAmout FROM  `ph_tallySku_record` where sku='{$sku}'";
	$sql = $dbConn->execute($sql);
	$number = $dbConn->fetch_one($sql);
	return $number["tallyAmout"];
}

function getOrderDetailInfo($po_id){
	global $dbConn;
	$sql = "select * from ph_order_detail where po_id='{$po_id}' and is_delete=0";
	$sql = $dbConn->execute($sql);
	$infodetail = $dbConn->getResultArray($sql);
	return $infodetail;
}


function getOwOrderDetailInfo($po_id){
	global $dbConn;
	$sql = "select * from ph_ow_order_detail where recordnumber='{$po_id}'";
	$sql = $dbConn->execute($sql);
	$infodetail = $dbConn->getResultArray($sql);
	return $infodetail;
}

function getSkuData($sku){
	global $dbConn;
	$sql = "select * from om_sku_daily_status where sku='{$sku}'";
	$sql = $dbConn->execute($sql);
	$infodetail = $dbConn->fetch_one($sql);
	return $infodetail;
}

function recalcdays($days,$addtime){
	if($addtime != "" && $addtime != 0){
		$now = time();
		$days1 = floor(($now - $addtime) / (24*60*60));
		$nowdays = $days - $days1;
	}else{
		$nowdays = 0;
	}
	return $nowdays;
}

//根据真实姓名获取统一编号
function getUserIdByTrueName($name){
	global $dbconn;
	$sql 	= "SELECT global_user_id AS userId FROM power_global_user WHERE global_user_name = '{$name}'";
	$sql 	= $dbconn->execute($sql);
	$ret 	= $dbconn->fetch_one($sql);
	$userId = '';
	if($ret){
		$userId = $ret["userId"];
	}
	return $userId;
}





?>
