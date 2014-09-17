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
    header('location:index.php?mod=showMessage&act=showOkMsg&data='.$msg);
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
	chmod($path, 0777);
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
 * 过滤并获取url中类似这样的数据 id=1,2,3...
 * $str 参数字符串
 */
function clearData($str){
    $ar = explode(',', $str);
    $resulte = array();
    foreach ($ar  as $arv){
        $arv = trim($arv);
        $arv = intval($arv);
        $resulte[] = $arv;
    }
    return $resulte;
}

/*
 * 将数据中的数据组装成sql中的update语句形式 形如 set xxx='yyy'
 * $array 数组 array('field'=>'value'...)
 */
function formateSqlUpdate($array){
    $returnvalue = array();
    foreach ($array as $key=>$val){
        $returnvalue[] = "`$key`='$val'";
    }
    return implode(', ', $returnvalue);
}

/* 调用开放系统指定接口的公用方法
 * para:method:调用开发系统接口的接口名，paArr为传递的参数（参数均要用数组包装，不能直接传字段）
* add by zqt
*/
function getOpenSysApi($method, $paArr, $gateway=''){
    if(empty($method) || empty($paArr) || !is_array($paArr)){   //参数不规范
        return false;
    }else{
        $paramArr = array(
            'format'    => 'json',
            'v'    => '1.0',
            'username'  => 'Message'
        );
        $paramArr['method'] = $method;//调用接口名称，系统级参数
        foreach($paArr as $key=>$value){
            if(!is_array($value)){//如果传递的应用级参数不是数组的话，直接加入到paramArr中
                $paramArr[$key] = $value;
            }else{
                $paramArr['jsonArr'] = base64_encode(json_encode($value));//对数组进行jsonencode再对其进行base64编码进行传递，否则直接传递数组会出错
            }
        }
        //生成签名
        $sign = createSign($paramArr,OPENTOKEN);
        //echo $sign,"<br/>";
        //组织参数
        $strParam = createStrParam($paramArr);

        $strParam .= 'sign='.$sign;

        //构造Url
        $urls = OPENURL.$strParam;
        if (!empty($gateway)){
            $urls = $gateway.$strParam;
        } else {
            $urls = OPENURL.$strParam;
        }
//  var_dump($urls);exit;
        //连接超时自动重试3次
        $cnt=0;
        while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
        //$result = file_get_contents($urls);
        $data	= json_decode($result,true);
        //var_dump($data);exit;
        			//var_dump($data,$result,"++___+++");
        if($data){
          return  $data;
        }else{
            return FALSE;
        }
    }
}

/*
 * 脚本日志功能
 * $path    存放根目录
 * $conent  内容
 * */
function writeLog($path, $contents){
    $year       = date('Y', time());
    $month_day  = date('m-d', time());
    $filepath   = $path.$year;                           //文件夹
    if(!file_exists($filepath)){
        //检测存放文件夹是否存在 不存在则创建之
        $mkresult       = mkdir($filepath);
        if(!$mkresult){
            echo '创建log日志目录失败!', "\n";
            return false;
        }
    }
    $fallpath   = $filepath."/$month_day.log";
    $pf         = fopen($fallpath, 'a');
    flock($pf, LOCK_EX);
    if($pf === false){
        echo '打开日志文件失败--'.$fallpath."\n";
        return false;
    }
    $formattime = date('Y-m-d H:i:s', time());
//     echo $formattime;exit;
    $logstring  = <<<EOF
---------------【 $formattime 】 --------------------
$contents;
------------------             ---------------------

EOF;
    fwrite($pf, $logstring);
    flock($pf, LOCK_UN);
    fclose($pf);
    return true;
}

/*
 * 生成message文件夹规则字符列表数组
 */
function generate_alphabet(){
    $alphabet = array_merge(array('_','-','.','*','$','!','(',')', '-','+','=','#','@','`','~'),range('a', 'z'),range('A', 'Z'), array('0' , '1' , '2' , '3' , '4' , '5' , '6' , '7' , '8' , '9'));
    return $alphabet;
}


/*
 * 格式化速卖通时间
 */
function formateAliTime($timestr){
	$year   = substr($timestr, 0,4);          //年
	$month  = substr($timestr, 4,2);          //月
	$day    = substr($timestr, 6,2);          //日
	$hour   = substr($timestr, 8,2);          //时
	$minit  = substr($timestr, 10,2);         //分
	$second = substr($timestr, 12,2);         //秒
	return $year.'-'.$month.'-'.$day.' '.$hour.':'.$minit.':'.$second;
}

function getSkuImg($spu, $sku, $picType){
    $paramArr= array(
            /* API系统级输入参数 Start */
            'method'	=> 'datacenter.picture.getAllSizePic',  //API名称
            'format'	=> 'json',  //返回格式
            'v'			=> '1.0',   //API版本号
            'username'	=> C('OPEN_SYS_USER'),
            /* API系统级参数 End */
            /* API应用级输入参数 Start*/
            'spu'		=> $spu,  //主料号
            'picType'	=> $picType, //站点
            /* API应用级输入参数 End*/
    );
    $data 	= callOpenSystem($paramArr);
    $data 	= json_decode($data, true);
    $imgUrl = isset($data['data']['artwork']) ? $data['data']['artwork'][$spu][0] : '';
    return $imgUrl;
}

/*
 * 将速卖通账号转换名称
 */
function aliAccountf2Name($account){
    include WEB_PATH.'lib/ali_keys/common.php';
    if (array_key_exists($account, $erp_user_mapping)){
        return $erp_user_mapping[$account];
    } else {
        return FALSE;
    }
}

/*
 * 将速卖通时间分解成年月日 返回数组
 */
function extractAliTimeInfo($timeStr){
    $year       = substr($timeStr, 0,4);          //年
    $month      = substr($timeStr, 4,2);          //月
    $day        = substr($timeStr, 6,2);          //日
    $hour       = substr($timeStr, 8,2);          //时
    $minit      = substr($timeStr, 10,2);         //分
    $second     = substr($timeStr, 12,2);         //秒
    $timezone   = substr($timeStr, -5, 5);        //时区
    return array(
    	'year'     => $year,
        'month'    => $month,
        'day'      => $day,
        'hour'     => $hour, 
        'minit'    => $minit,
        'second'   => $second,
        'timezone' => $timezone
    );
}
/*
 * 速卖通时间转换成时间戳
 */
function aliTranslateTime($timeStr){
    $timinfo    = extractAliTimeInfo($timeStr);
    if ($timinfo['timezone'] == -7) {               //丹佛时间
    	date_default_timezone_set('America/Denver');
    } elseif ($timinfo['timezone'] == -8) {         //洛杉矶时间
        date_default_timezone_set('America/Los_Angeles');
    } else {                                        //默认洛杉矶时间处理
        date_default_timezone_set('America/Los_Angeles');
    }
    $timestamp = strtotime("$timinfo[year]-$timinfo[month]-$timinfo[day] $timinfo[hour]:$timinfo[minit]:$timinfo[second]");
    date_default_timezone_set('Asia/Shanghai');     //处理完成 改回时区设置
    return $timestamp;
}

/*
 * 获取文件后缀名
 */
function getFileSuffix($fileName) {
    return strrchr($fileName, '.');
}

/**
 * Returns the url query as associative array
 *
 * @param    string    query
 * @return    array    params
 */
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);

    $params = array();
    foreach ($queryParts as $param)
    {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }

    return $params;
}

function getUrlQuery($array_query)
{
    $tmp = array();
    foreach($array_query as $k=>$param)
    {
        $tmp[] = $k.'='.$param;
    }
    $params = implode('&',$tmp);
    return $params;
}

/*
 * 当前时间转洛杉矶时间
 */
function trunToLosangeles($formate,$timeStamp){
    date_default_timezone_set('America/Los_Angeles');
    $date   = date($formate, $timeStamp);
    date_default_timezone_set('America/Los_Angeles');
    return $date;
}
