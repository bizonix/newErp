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
       if(function_exists('get_magic_quotes_gpc') && !get_magic_quotes_gpc()){
	      $_v = addslashes($_v);
	   }
       $sql_array[] = "`{$_k}`='{$_v}'";
       /*
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
	   if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "`{$_k}`={$_v}";
	   } else {
            $_v = Deal_SC($_v);
			$sql_array[] = "`{$_k}`='{$_v}'";
		}
		*/
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
	$orderdetails = "SELECT * FROM wh_shipping_orderdetail where shipOrderId ='{$id}' ";
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
	$orderdetails = "SELECT * FROM wh_shipping_orderdetail where shipOrderId ='{$id}' ";
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

function write_sql_log($str){
	 if (preg_match ("/^((insert)|(delete)|(update)).+/i",$str)){
		$fp   = fopen('sql.txt', 'a+');
		$time = date('Y-m-d H:i:s', time());
		$str  = "[--$time--] === $str \n\n";
		fwrite($fp, $str);
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
    $idlist = isset($_POST['ids']) ? trim($_POST['ids']) : (isset($_GET['ids']) ? trim($_GET['ids']) : 0);
    if(empty($idlist)){
        return $result;
    }
    $idarray = explode(',', $idlist);
    $po_obj = new PackingOrderModel();
    $result = $po_obj->getOrderInfoByIdList($idarray);
    return $result;
}

function clearupData2($idlist){
    $result = array();
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
    $getskuid = 'select ppr.positionId from pc_goods as pi join wh_product_position_relation as ppr on pi.id=ppr.pid where pi.sku='."'$sku' and ppr.storeId=$storeid";
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
    $sql = 'select * from pc_goods where sku= '."'$sku' and is_delete=0";
    return $dbConn->fetch_first($sql);
}

/*
 * 根据sku、数量分配仓位号
 * 失败 返回空字符串 
 */
function getPositionBySku($sku,$amount){
	$location_position_info = array();
	/*
	$location_position_info[] = array(
		'positionId' => 1,
		'position'	 => 'E0103',
		'amount' 	 => $amount,
		'storeId' 	 => 1,
	);
	return $location_position_info;
	*/
    $sku_position_id = OmAvailableModel::getTNameList("pc_goods","id","where sku='$sku' and is_delete=0");
	if($sku_position_id){
		$pid = $sku_position_id[0]['id'];
		$sku_position_info   = OmAvailableModel::getTNameList("wh_product_position_relation","positionId,nums","where pId='$pid' and storeId=1 and type=1 and is_delete=0");
		
		$sku_position_info_B = OmAvailableModel::getTNameList("wh_product_position_relation","positionId,nums","where pId='$pid' and storeId=2 and type=1 and is_delete=0");
		if(empty($sku_position_info) && empty($sku_position_info_B)){
			return '';
		}
		
		$enough_arr    = array();                //A仓位可用数量大于sku数量的
		$no_enough_arr = array();				 //A仓位可用数量小于sku数量的
		$enough_arr_B  = array();				 //B仓位可用数量
		$int_num       = $amount;
		if(!empty($sku_position_info)){
			foreach($sku_position_info as $position_info){
				$use_num 	 = getPositionUseStock($position_info['positionId'],$sku);
				$can_use_num = $position_info['nums']-$use_num;
				if($can_use_num>=$amount){
					$enough_arr[] = array(
						'num' 		 => $can_use_num,
						'positionId' => $position_info['positionId'],
					);
				}else{
					$no_enough_arr[] = array(
						'num' 		 => $can_use_num,
						'positionId' => $position_info['positionId'],
					);
				}
			}
		}

		if(!empty($enough_arr)){
			$position = OmAvailableModel::getTNameList("wh_position_distribution","*","where id='{$enough_arr[0]['positionId']}'");
			$min_distance = getRelativelyDistance($position[0]['x_alixs'],$position[0]['y_alixs'],$position[0]['floor']);   					 //距离
			$location_position   = $position[0]['pName'];
            $area                = $position[0]['area'];
			$location_positionid = $position[0]['id'];
			foreach($enough_arr as $exceed){
				$position = OmAvailableModel::getTNameList("wh_position_distribution","*","where id='{$exceed['positionId']}'");
				$distance = getRelativelyDistance($position[0]['x_alixs'],$position[0]['y_alixs'],$position[0]['floor']);   					 //距离
				if($distance<$min_distance){
					$location_position 	 = $position[0]['pName'];
                    $area                = $position[0]['area'];
					$location_positionid = $position[0]['id'];
				}
			}
			$location_position_info[] = array(
				'positionId' => $location_positionid,
				'position'	 => $location_position,
                'area'       => $area,
				'amount' 	 => $amount,
				'storeId' 	 => 1,
                'pId'        => $pid
			);
			return $location_position_info;
		}else if(!empty($no_enough_arr)){
			$no_enough_info 		= array();
			$no_enough_positon_info = array();
			//$int_num                = $amount;
			$now_num                = 0;
			foreach($no_enough_arr as $exceed){
				$position = OmAvailableModel::getTNameList("wh_position_distribution","*","where id='{$exceed['positionId']}'");
				$distance = getRelativelyDistance($position[0]['x_alixs'],$position[0]['y_alixs'],$position[0]['floor']);   					 //距离
				$no_enough_info[$exceed['positionId']] = $distance;
				$no_enough_positon_info[$exceed['positionId']] = array(
					'num' 	=> $exceed['num'],
					'pName' => $position[0]['pName'],
                    'area'  => $position[0]['area']
				);
			}
			asort($no_enough_info);
			foreach($no_enough_info as $pos=>$dis){
				$now_num = $int_num-$no_enough_positon_info[$pos]['num'];
				if($now_num>0){
					$location_position_info[] = array(
						'positionId' => $pos,
						'position'	 => $no_enough_positon_info[$pos]['pName'],
						'amount' 	 => $no_enough_positon_info[$pos]['num'],
                        'area'       => $no_enough_positon_info[$pos]['area'],
						'storeId' 	 => 1,
                        'pId'        => $pid
					);
					$int_num = $now_num;
				}else{
					$location_position_info[] = array(
						'positionId' => $pos,
						'position'	 => $no_enough_positon_info[$pos]['pName'],
                        'area'	     => $no_enough_positon_info[$pos]['area'],
						'amount' 	 => $int_num,
						'storeId' 	 => 1,
                        'pId'        => $pid
					);
					$int_num = 0;
					break;
				}
			}
		}
		if($int_num>0){
			if(!empty($sku_position_info_B)){
				foreach($sku_position_info_B as $position_info){
					$use_num 	 = getPositionUseStock($position_info['positionId'],$sku);
					$can_use_num = $position_info['nums']-$use_num;
					if($can_use_num>=$int_num){
						$position = OmAvailableModel::getTNameList("wh_position_distribution","*","where id='{$position_info['positionId']}'");
						$location_position_info[] = array(
							'positionId' => $position_info['positionId'],
							'position'	 => $position[0]['pName'],
                            'area'       => $position[0]['area'],
							'amount' 	 => $int_num,
							'storeId' 	 => 2,
						);
						return $location_position_info;
					}else{
						return '';
					}
				}
			}else{
				return '';
			}
		}else{
			return $location_position_info;
		}
	}else{
		return '';
	}
}

/*
 * 根据x、y轴计算相对距离
 * 标准点定死在包装处，初始x为25，y为20，$floor楼层
 */
 function getRelativelyDistance($x,$y,$floor){
	$Origin_x = 25;                  
	$Origin_y = 20;
	$floor_add = 100;
	$square_x = pow(abs($x-$Origin_x),2);
	$square_y = pow(abs($y-$Origin_y),2);
	$distance = sqrt($square_x+$square_y);
	if($floor>0){
		$distance = $distance+$floor_add;
	}
	return $distance;
 }
 
 /*
 * 获取仓位被占用库存
 */
 function getPositionUseStock($positionId,$sku){
	$use_num  	  = 0;
    return $use_num;
	$order_status = array(400,401,402);
	$order_str    = implode(',',$order_status);
	$order_info   = OmAvailableModel::getOrderList($positionId,$sku,$order_str);
	if($order_info){
		foreach($order_info as $order){
			$use_num += $order['amount'];
		}
	}
	return $use_num;
 }

 /*
 * 根据x、y轴计算相对距离
 * $com_x,$com_y,$com_floor为对照的坐标
 * $x,$y,$floor为取值坐标
 */
 function getDistance($com_x,$com_y,$com_floor,$x,$y,$floor){
	$floor_add = 100;
	$square_x = pow(abs($x-$com_x),2);
	$square_y = pow(abs($y-$com_y),2);
	$distance = sqrt($square_x+$square_y);
	if($com_floor!=$floor){
		$distance = $distance+$floor_add;
	}
	return $distance;
 }
 
//对接图片系统，取得对应sku的spu的图片
function getPicFromOpenSys($sku, $picType=''){
	if(empty($sku)){
		return false;
	}
	if(empty($picType)){
		$picType = 'G';
	}
	$tName = 'pc_goods';
	$select = 'spu';
	$where = "WHERE sku='$sku'";
	$spuList = OmAvailableModel::getTNameList($tName, $select, $where);
	if(empty($spuList[0]['spu'])){
		return false;
	}
	$spu = $spuList[0]['spu'];
	$spuPicList = UserCacheModel::getOpenSysApi('datacenter.picture.getAllSizePic',array('spu'=>str_pad($spu,3,0,STR_PAD_LEFT),'picType'=>$picType));
	//$spuPicList = json_decode($spuPicList, true);
	//return $spuPicList;
	if(!empty($spuPicList['errCode'])){
		return false;
	}
	if(empty($spuPicList['data']['artwork'][$spu][0])){
		return false;
	}
	return $spuPicList['data']['artwork'][$spu][0];
}

//获取料号下详细信息
function get_realskuinfo($sku){
	//$sku = strtoupper($sku);
	$combinelists = OmAvailableModel::getTNameList("pc_goods_combine","*","where combineSku='$sku' and is_delete=0");	
	if (empty($combinelists)){
		//$sku = get_conversion_sku($sku);
		return array($sku=>1);
	}
	
	$combineinfos = OmAvailableModel::getTNameList("pc_sku_combine_relation","*","where combineSku='{$combinelists[0]['combineSku']}'");
	if (empty($combineinfos)){
		return array($sku=>1);
	}
	
	$results = array();
	foreach($combineinfos as $info){
		$results[$info['sku']] = $info['count'];
	}
	return $results;
}

//根据用户id获取用户名字
function getUserNameById($userId){
	$usermodel = UserModel::getInstance();
	$user_info = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id={$userId}",'','limit 1');
	if($user_info){
		return $user_info[0]['global_user_name'];
	}else{
		return '';
	}
	
}

//根据用户名字获取用户id
function getUserIdByName($userName){
	$usermodel = UserModel::getInstance();
	$user_info = $usermodel->getGlobalUserLists('global_user_id',"where a.global_user_name='{$userName}'",'','limit 1');	
	if($user_info){
		return $user_info[0]['global_user_id'];
	}else{
		return '';
	}
}

//根据用户sku获取料号名称
function getSKUName($sku){
    $skuname = printLabelModel::getSkuInfo($sku);
    return $skuname['goodsName'];
}

//判断sku是否是组合料号
function get_skuIsCombine($sku){
	//$sku = strtoupper($sku);
	$combinelists = OmAvailableModel::getTNameList("pc_goods_combine","id","where combineSku='{$sku}' and is_delete=0");	
	if (empty($combinelists)){
		return false;
	}
	return true;
}

//获取料号GoodsCode
function get_skuGoodsCode($sku, $skuId = 0){
	$num = 1000000;
	if(!$skuId){
		$skuinfo = printLabelModel::getSkuInfo($sku);
		$skuId = $skuinfo['id'];
	}
	if($skuId > $num){
		return $skuId;
	}
	return $num+$skuId;
}

//获取真实料号
function get_goodsSn($lable) {
	$code = substr($lable,0,7);
	$num  = intval($code);
	if(is_numeric($lable) && $num > 1000000){
		$id = $num-1000000;
		$skuinfo = printLabelModel::getSkuInfoById($id);
		return $skuinfo['sku'];
	}else{
		return str_pad(trim($lable), 3, '0', STR_PAD_LEFT);
	}
}
//根据料号ID、仓位ID、仓库ID获取料号所在仓位库存数量 add by wangminwei 2014-03-07
function get_SkuStockQty($skuId, $posId, $storeId){
	global $dbConn;
    $nums 	= 0;
    $sql 	= "SELECT nums FROM wh_product_position_relation WHERE pId= '{$skuId}' AND positionId = '{$posId}' AND storeId = '{$storeId}' AND is_delete = 0";
    $data   = $dbConn->fetch_first($sql);
    if(!empty($data)){
    	$nums = $data['nums'];
    }
    return $nums;
}

/**
 * getSkuTime()
 * 获取点货的贴标日期和上架日期
 * @author GARY
 * @param array $params
 * @return void
 */
function getSkuTime($params){
    extract($params);   //格式化参数
    $returnTime     =   '无';
    $table          =   ''; //搜索的表名
    $search         =   ''; //搜索的字段名
    $where          =   ''; //sql条件
    switch($type){
        case 'paste':
            $table  =   'wh_print_group';
            $search =   'labelTime';
            $where  =   "where tallyListId='{$tallyList}' and is_delete=0 order by id desc limit 1";
            break;
        case 'input':
            if(!$shelvesNums){ //没有良品数
                break;
            }
            if($finishTime){
                $returnTime = date('Y-m-d H:i:s', $finishTime);
            }else{
                $table  =   'wh_iorecords';
                $search =   'createdTime';
                $where  =   "where sku='{$sku}' order by id desc limit 1";
            }
            break;
        default:
    }
    if($table && $search && $where){
        $res    = OmAvailableModel::getTNameList($table, $search, $where);	
    	if (!empty($res)){
    		$returnTime    =   $res[0][$search] ? date('Y-m-d H:i:s', $res[0][$search]) : '无';
    	}
    }
    return $returnTime;  
}

/**
* checkSkuPackage()
* 判断等待上架数量是否 
* $beforNum 点货调整是原点货数据
* $amount  点货调整数量或者上架数量
* @return void
*/
function checkSkuPackage($sku, $amount, $beforNum=0){
    $amount     =   intval($amount);
    $log_file   =   'getSkuOnWayNum_log/'.date('Y-m-d').'.txt';
    $date       =   date('Y-m-d H:i:s');
    //$OnWayNum   =   CommonModel::checkOnWaySkuNum($sku); //料号订单在途数量
    //$waitNum    =   packageCheckModel::getSkuWaitShelfNum($sku); //料号待上架数量
    //echo $OnWayNum, $waitNum,$amount;exit;
    $log_info      = sprintf("料号：%s, 时间：%s, 待上架数量：%s, 在途数量:%s, amount:%s, 点货调整前数量:%s, 操作人：%s \r\n", $sku, $date, $waitNum, $OnWayNum, $amount, $beforNum, $_SESSION['userCnName']);  
    write_log($log_file, $log_info);
    //$checkOnWaySku = $OnWayNum-$waitNum-$amount-$beforNum >= 0 ? 0 : 1; //判断在途数量是否大于待上架和点货录入数量
    return 0;
}

/**
 * array2select()
 * 将数组或字符串转化为select语句字段
 * @param int $array string $array
 * @return void
 */
function array2select($array){
    $select =   '';
    if(is_array($array)){
        foreach($array as $val){
            $select .=   $val.',';
        }
        $select =  trim($select, ',');
    }else{
        $select .=  $array;
    }
    return $select;
}

/**
 * array2where()
 * 将数组转化为where语句字段
 * @param int $array
 * @return void
 */
function array2where($array){
    $where =   '';
    if(is_numeric($array)){
		$where  .=  "id = $array";
	}else if(!is_array($array)){ //不是数组直接返回
        $where  .=  $array;
    }else{
        foreach($array as $key=>$val){
            if(strpos($key, ' in') !== FALSE){ //存在in语句
                if(!is_array($val)){ //值不是数组，则不匹配该条件
                    $where  .=  " {$key} ($val) and";
                    continue;
                }
                $keys   =   explode(' ', $key);
                $key    =   array_shift($keys); //where字段
                $char   =   implode(' ', $keys); //where条件 适用于not in
                $value  =   '';
                foreach($val as $v){
                    $value  .=  "'$v',"; 
                }
                $value  =   '('.trim($value, ',').')';
                $where  .=  " {$key} {$char} $value and";
            }else if(preg_match("/[!><]/", $key)){
                $keys   =   explode(' ', $key);
                $key    =   $keys[0];
                $char   =   $keys[1];
                $where  .=  " {$key} {$char} '{$val}' and";
            }else if(in_array( $key, array('limit', 'order by', 'group by') ) ){
                $where  =   rtrim($where, 'and');
                $where  .=  " $key $val ";
            }else{
                $where .=   " $key = '{$val}' and"; 
            }
        }
        $where  =   rtrim($where, 'and');
    }
    return $where;
}
//保留小数点3位数
function shurtDouble($double){
	return round($double, 3);
}

/**
 * get_filed_array()
 * 获取二维数组中指定键对应值的数组集合
 * @param mixed $filed 指定键名或数组
 * @param array $array 循环的二维数组
 * @return array 返回二维数组
 */
function get_filed_array($filed, $array){
    $return =   array();
    if(is_array($array)){
        if(is_array($filed)){
            foreach($filed as $v){
                $return[$v] =   array();
            }
        }
        
        foreach($array as $val){
            if(is_array($filed)){
                foreach($filed as $v){
                    $return[$v][]   =   $val[$v];
                }
            }else{
                $return[] = $val[$filed];   
            }
        }
    }
    return array_filter($return); 
}

/**
 * reverse_array()
 * 从一个数组中拿出指定的字段作为键名，另外一个字段作为值生成数组
 * @param mixed $array
 * @param mixed $val_filter  作为值的字段
 * @param string $key_filter 作为键名的字段 为空则为key值
 * @return void
 */
function reverse_array($array, $val_filter, $key_filter = ''){
    $return     =   array();
    foreach($array as $key=>$val){
        if($key_filter){
            $return[$val[$key_filter]]  =   $val[$val_filter];
        }else{
            $return[$key]               =   $val[$val_filter];
        }
    }
    return array_filter($return);
}

/**
 * 
 */
function getBestTransport($compareTrasprot,$lastTransport){
	if($compareTrasprot['carrierId'] == 6 && array($lastTransport['carrierId'],array(1,2))){
		//EUB对比中国邮政取最优
		if(($compareTrasprot['fee'] - $lastTransport['fee'] <= 2) || ($compareTrasprot['fee']/$lastTransport['fee'] - 1 <= 0.09)){
			return $compareTrasprot;
		}
	}
	return $lastTransport;
}