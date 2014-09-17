<?php


/*
简单的重定向函数 add by xiaojinhua
*/
function redirect_to( $location = NULL ) {
  if ($location != NULL) {
    header("Location: {$location}");
    exit;
  }
}

/*
函数名称：post_check()
函数作用：对用户提交内容进行处理,防止Sql注入
参　　数：$post: 要提交的内容
返 回 值：$post: 返回过滤后的内容
*/

function post_check($post) {
    $post = str_replace("\\", "", $post); // 把 '\'替换成'/'
    $post = str_replace("%", "%", $post); // 把 '%'过滤掉
    $post = nl2br($post); // 回车转换
    $post = htmlspecialchars($post); // html标记转换
    if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否为打开
        $post = addslashes($post); // 进行magic_quotes_gpc没有打开的情况对提交数据的过滤
    }
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
    $log_path = WEB_PATH.'log.txt';
    $ip   = getClientIP();
    $info = "$time--$ip ------>$msg";
    //echo  $info;
    $fp = fopen($log_path, "a+");//追加写入
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

function getMicrotime() {
	list ($usec, $sec) = explode(" ", microtime());
	return ((float) $usec + (float) $sec);
}

function fast_in_array($elem, $array)
{
	$top = count($array) -1;
	$bot = 0;

	while($top >= $bot)
	{
		$p = floor(($top + $bot) / 2);
		if ($array[$p] < $elem) $bot = $p + 1;
		elseif ($array[$p] > $elem) $top = $p - 1;
		else return TRUE;
	}

	return FALSE;
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
	$tes = array("=" , "{", "}");
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
	if($return=="id"){
		return $dbConn->insert_id();
	}else{
		return $query;
	}
}
/*
 *edit by wxb
 *2013/07/05
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
					$str.=$key."=".$val;
				}else{
					$str.=" {$link} ".$key."=".$val;
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

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return str_replace(',', '', number_format($r,2));
}

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
		return $trueName;
	}else{
		return $trueName;
	}
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

//根据id算出条形码
function calGoodsCodeById($id){
	return $id+1000000;
}

//根据sku得出其排序的sort值
function calGoodsSortBysku($sku){
	$sort = 0;
	if(preg_match("/[A-Z]{2}/", substr($sku,2)) && intval(substr($sku,2)) != 0){
		$sort = intval(substr($sku,2));
	}else{
		$sort = time();
	}
	return $sort;
}

//得到指定path的全名称字符串
function getAllCateNameByPath($pid){
	   $categorySearch = '';
        if(!empty($pid)){
            $tmpCateNameArr = array();
            $tmpCategoryArr = array_filter(explode('-', $pid));
            foreach($tmpCategoryArr as $value){
                if(!empty($value)){
                    $tmpCateNameArr[] = CategoryModel::getCategoryNameById($value);
                }
            }
            $categorySearch = implode('->',$tmpCateNameArr);
        }
        return $categorySearch;
}

//根据当前path($pid)返回其所有父类的path字符串
function getAllPathBypid($pid){
        $pathArr = array();//定义一个用来存放path的数组，里面存放的是当前$pid的所有父类的id数组组合形式，比如说，当前$pid=1-2,则$pathArr=array([0]=>array(1),[1]=>array(1,2))])]))
        if(empty($pid)){
            return false;
        }
        $pathIdArr = explode('-',$pid);
        $tmpArr = array();//临时变量
        foreach($pathIdArr as $value){
            $tmpArr[] = $value;
            $pathArr[] = $tmpArr;
        }
        $pathImplodeArr = array();//将数组组合形式implode成如1-2形式，即当$pid=1-2,则$pathImplodeArr=array([0]=>1,[1]=1-2)])]))
        foreach($pathArr as $value){
            $pathImplodeArr[] = "'".implode('-',$value)."'";
        }
        $pathImplodeStr = implode(',',$pathImplodeArr);//转换成(1,1-2)形式
        return $pathImplodeStr;
}

//取得指定采购下对应的供应商id及公司名称
function getParterInfoByPurchaseId($purchaseId){
        //调用采购系统得到数据
        /*
		if(intval($purchaseId) == 0){
			return null;
		}
        $partnerList = UserCacheModel::getOpenSysApi('purchase.getPartnerList',array('purchaseuser_id'=>$purchaseId),'gw88');
        if(empty($partnerList['data'])){
			return null;
        }
        $partnerList = json_decode($partnerList['data'],true);
        return $partnerList[1];
        */

        //通过调用旧erp系统，得到数据
        //$cguser = getPersonNameById($purchaseId);
        //采购提供接口调用
        $ret = UserCacheModel::getOpenSysApi('purchase.getPurchaseList',array('purchaseId'=>$purchaseId),'');
        return $ret['data'];
}

//通过接口取得指定id的人员名称
function getPersonNameById($id){
        if(intval($id) <= 0){
            return '';
        }
        $tName = 'power_global_user';
        $select = 'global_user_name';
        $where = "WHERE global_user_is_delete=0 AND global_user_id=$id";
        $userList = OmAvailableModel::getTNameList($tName, $select, $where);
        if($userList[0]['global_user_name']){
            return $userList[0]['global_user_name'];
        }
        $queryConditions = array('userId' =>$id);
        $queryConditions = json_encode($queryConditions);
        $userInfo = Auth::getApiGlobalUser($queryConditions);
        $userInfo = json_decode($userInfo,true);
        //print_r($userInfo);
        //echo "\n";
        return $userInfo[0]['userName'];
}

//通过接口取得指定username的人员id
function getPersonIdByName($username){
        if(empty($username)){
            return 0;
        }
        $tName = 'power_global_user';
        $select = 'global_user_id';
        $where = "WHERE global_user_is_delete=0 AND global_user_name='$username'";
        $userList = OmAvailableModel::getTNameList($tName, $select, $where);
        if($userList[0]['global_user_id']){
            return $userList[0]['global_user_id'];
        }
        $queryConditions = array('loginName' =>$username);
        $queryConditions = json_encode($queryConditions);
        $userInfo = Auth::getApiGlobalUser($queryConditions);
        $userInfo = json_decode($userInfo,true);
        //print_r($userInfo);
        //echo "\n";
        return $userInfo[0]['userId'];
}

//根据鉴权获取公司信息
function getAllCompanyInfo(){
    $tName = 'power_company';
    $select = '*';
    $where = "WHERE company_pid in(0,1)";
    $companyList = OmAvailableModel::getTNameList($tName, $select, $where);
    if(!empty($companyList)){
        return $companyList;
    }
    $companyInfo = Auth::getApiCompany();
    if(empty($companyInfo)){
        return array();
    }
    return json_decode($companyInfo, true);
}

//在本地表中，根据公司id获取公司名称
function getCompanyNameById($id){
    if(intval($id) <= 0){
        return '';
    }
    $tName = 'power_company';
    $select = 'company_name';
    $where = "WHERE company_id=$id";
    $companyList = OmAvailableModel::getTNameList($tName, $select, $where);
    return $companyList[0]['company_name'];
}

 	/*根据id获取对应供应商公司名称，memcache
	 *
     */
	function getMemPartnerNameById($id){
        global $memc_obj;
        if (intval($id) == 0){
            return '';
        }
        $partnerInfo = $memc_obj->get_extral('purchase_partner_'.$id);
        //print_r($partnerInfo);
        return $partnerInfo;
    }

    /*
	 *将$value以$key的键set到mem中
     */
	function setMemNewByKey($key,$value){
        global $memc_obj;
        if (empty($key)){
            return false;
        }
        $expire = 0;
        $ret = $memc_obj->set_extral($key, $value, $expire);
        return $ret;
    }

    //返回指定$userId下的所有上级岗位的job_id(array()))
    function getGolbalUserSuperJobId($userId){
        if(intval($userId) == 0){
            return array();
        }
        $tName = 'power_global_user';
        $select = 'global_user_job_path';
        $where = "WHERE global_user_is_delete=0 AND global_user_id=$userId";
        $golbalUserJobList = OmAvailableModel::getTNameList($tName, $select ,$where);
        if(empty($golbalUserJobList)){
            return array();
        }
        $SuperJobIdArr = explode('-',$golbalUserJobList[0]['global_user_job_path']);
        //return $SuperJobIdArr;
        if(count($SuperJobIdArr) <= 1){
            return array();
        }
        array_pop($SuperJobIdArr);//将该golbalUserId所属的岗位id去掉
        return $SuperJobIdArr;
    }

    //判断当前session.userId是否和相应采购相等或者是是其上级，用来控制采购修改或其他通用权限,返回true或false
    function getIsAccess($purchaseId){
        if(intval($purchaseId) == 0){
            return true;//如果没有采购，则所有人都有权限
        }
        $sessionUserId = $_SESSION['userId'];
        if(intval($sessionUserId) == 0){
            return false;//session.userId非法的话，没有权限
        }
        if($purchaseId == $sessionUserId){//采购和本人是同一个人，则返回true;
            return true;
        }
        $tName = 'power_global_user';
        $select = 'global_user_job';
        $where = "WHERE global_user_id=$sessionUserId";
        $golbalUserJobList = OmAvailableModel::getTNameList($tName, $select, $where);//得到此时登陆人的岗位
        if(empty($golbalUserJobList)){
            return false;
        }
        $SuperJobIdArr = getGolbalUserSuperJobId($purchaseId);//获得指定采购的上级岗位数组
        if(in_array($golbalUserJobList[0]['global_user_job'],$SuperJobIdArr)){//不是本人的话，只要登录人岗位是采购上级岗位的都有权限
            return true;
        }else{
            return false;
        }
    }

    //根据条形码获取对应sku,spu的方法,正常返回为记录数组，异常为false
    function getSkuBygoodsCode($goodsCode){
        if(empty($goodsCode)){
            return false;
        }
        $tName = 'pc_goods';
        $select = '*';//add by zqt 2014.2.20
        if(intval($goodsCode) > 0 && $goodsCode - 1000000 > 0){//如果goodsCode为数字，并且减去100W为正数，则表示该为条形码
            $id = $goodsCode - 1000000;
            $where = "WHERE is_delete=0 AND id=$id";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($skuList)){
                return false;
            }
            return $skuList;
        }else{//否则为sku
            $sku = $goodsCode;
            $where = "WHERE is_delete=0 AND sku='$sku'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($skuList)){
                return false;
            }
            return $skuList;
        }
    }

     //根据条形码获取对应pmId的方法,正常返回为pmId，异常为false,支持pmName或其id+1000000
    function getPmIdByPmCode($pmCode){
        if(empty($pmCode)){
            return false;
        }
        $tName = 'pc_packing_material';
        $select = 'id';
        if(intval($pmCode) > 1000000){//如果$pmCode为大于100W的数字，则认为是条形码
            $id = $pmCode - 1000000;
            $where = "WHERE id=$id";
            $pmList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($pmList)){
                return false;
            }
            return $pmList[0]['id'];
        }else{//否则为sku
            $pmName = $pmCode;
            $where = "WHERE pmName='$pmName'";
            $pmList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($pmList)){
                return false;
            }
            return $pmList[0]['id'];
        }
    }




        //$id =  $goodsCode - 1000000;
//        if($id <= 0){
//            return false;
//        }else{
//            $tName = 'pc_goods';
//            $select = 'sku,spu';
//            $where = "WHERE id=$id";
//            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
//            if(empty($skuList)){
//
//                return false;
//            }
//            return $skuList;
//        }

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

    //对接图片系统，取得对应spu的spu的图片
    function getPicFromOpenSysSpu($spu, $picType=''){
  		if(empty($spu)){
  		    return false;
  		}
        if(empty($picType)){
            $picType = 'G';
        }
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

    //对接图片系统，取得对应spu的所有图片
    function getAllArtPicFromOpenSysSpu($spu, $picType=''){
  		if(empty($spu)){
  		    return false;
  		}
        if(empty($picType)){
            $picType = 'G';
        }
        $spuPicList = UserCacheModel::getOpenSysApi('datacenter.picture.getAllSizePic',array('spu'=>str_pad($spu,3,0,STR_PAD_LEFT),'picType'=>$picType));
        //$spuPicList = json_decode($spuPicList, true);
        //return $spuPicList;
        if(!empty($spuPicList['errCode'])){
			return false;
        }
        if(empty($spuPicList['data']['artwork'][$spu][0])){
            return false;
        }
        return $spuPicList['data']['artwork'];
    }

    //对接图片系统，取得对应spu的所有图片,spu是一个数组
    function getPicFromOpenSysByArr($spuArr, $picType=''){
  		if(!is_array($spuArr) || empty($spuArr)){//$spu是一个数组
  		    return false;
  		}
        if(empty($picType)){
            $picType = 'G';
        }
        $spuPicList = UserCacheModel::getOpenSysApi('datacenter.picture.getSpuAllSizePic',array('spu'=>json_encode($spuArr),'picType'=>$picType));
        //$spuPicList = json_decode($spuPicList, true);
        //return $spuPicList;
        if(!empty($spuPicList['errCode']) || empty($spuPicList['data'])){
			return false;
        }
        $picArr = array();//定义一个数组来存放$spuArr中对应spu及对应的值，K=>V
        foreach($spuArr as $value){
            if(!empty($value)){
                $picArr[$value] = $spuPicList['data'][$value]['artwork'][$value][0];
            }
        }
        return $picArr;
    }


    //判断指定spu下对应的属性值是否存在，存在返回该spu下对应的记录,else return false
    function isExistForSpuPPV($spu, $propertyName){
        if(empty($spu) || empty($propertyName)){
            return false;
        }
        $tName = 'pc_archive_property';
        $select = 'id';
        $where = "WHERE propertyName='$propertyName'";
        $propertyList = OmAvailableModel::getTNameList($tName, $select ,$where);
        if(empty($propertyList)){
            return false;
        }
        $propertyIdArr = array();//定义一个数组用来存放在对应propertyName的所有id值
        foreach($propertyList as $value){
            if(!empty($value['id'])){
                $propertyIdArr[] = $value['id'];
            }
        }
        if(empty($propertyIdArr)){
            return false;
        }
        $propertyIdStr = implode(',',$propertyIdArr);
        $tName = 'pc_archive_spu_property_value_relation';
        $select = '*';
        $where = "WHERE spu='$spu' and propertyId IN ($propertyIdStr)";
        $spuPPVRela = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuPPVRela)){
            return false;
        }
        return $spuPPVRela;
    }

    //根据指定属性，返回具有该属性的属性值记录的spuList
    function getSpuListByPropertyName($propertyName){
        if(empty($propertyName)){
            return false;
        }
        $tName = 'pc_archive_property';
        $select = 'id';
        $where = "WHERE propertyName='$propertyName'";
        $propertyList = OmAvailableModel::getTNameList($tName, $select ,$where);
        if(empty($propertyList)){
            return false;
        }
        $propertyIdArr = array();//定义一个数组用来存放在对应propertyName的所有id值
        foreach($propertyList as $value){
            if(!empty($value['id'])){
                $propertyIdArr[] = $value['id'];
            }
        }
        if(empty($propertyIdArr)){
            return false;
        }
        $propertyIdStr = implode(',',$propertyIdArr);
        $tName = 'pc_archive_spu_property_value_relation';
        $select = '*';
        $where = "WHERE propertyId IN ($propertyIdStr)";
        $spuPPVRela = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($spuPPVRela)){
            return false;
        }
        return $spuPPVRela;
    }

    //根据ppvId返回对应的字母简写propertyValueShort
    function getPpvShortForPPVId($ppvId){
        if(intval($ppvId) <= 0){
            return '';
        }
        $tName = 'pc_archive_property_value';
        $select = 'propertyValueShort';
        $where = "WHERE id='$ppvId'";
        $shortList = OmAvailableModel::getTNameList($tName, $select ,$where);
        return $shortList[0]['propertyValueShort'];
    }

    //根据ppvId返回对应的属性值名称
    function getPPVForPPVId($ppvId){
        if(intval($ppvId) <= 0){
            return '';
        }
        $tName = 'pc_archive_property_value';
        $select = 'propertyValue';
        $where = "WHERE id='$ppvId'";
        $shortList = OmAvailableModel::getTNameList($tName, $select ,$where);
        return $shortList[0]['propertyValue'];
    }

    //用来根据权限隐藏无权限的链接或模块
    function isAccessMod($mod){
        if(empty($mod)){
            return false;
        }
        $user_id = $_SESSION['sysUserId'];
        if(empty($user_id)){
            return false;
        }
        $tName = 'power_user';
        $select = 'user_power';
        $where = "WHERE user_id=$user_id";
        $userPowerList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($userPowerList)){
            return false;
        }
        $userPower = json_decode($userPowerList[0]['user_power'], true);
        if(array_key_exists($mod,$userPower)){
            return true;
        }else{
            return false;
        }
    }

    //用来根据权限隐藏无权限的链接或模块
    function isAccessAll($mod, $act){
        if(empty($mod) || empty($act)){
            return false;
        }
        $user_id = $_SESSION['sysUserId'];
        if(empty($user_id)){
            return false;
        }
        $tName = 'power_user';
        $select = 'user_power';
        $where = "WHERE user_id=$user_id";
        $userPowerList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($userPowerList)){
            return false;
        }
        $userPower = json_decode($userPowerList[0]['user_power'], true);
        if(array_key_exists($mod,$userPower)){
            if(in_array($act, $userPower[$mod])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //用来根据鉴权返回所有采购人员的信息
    function getAllPurchaser(){
        $ret = Auth::getApiPurchaseUsers();
        return json_decode($ret,true);
    }

    //根据仓库那边的接口，返回仓库信息
    function getWhInfo(){
        $whInfo = UserCacheModel::getOpenSysApi('wh.getStoreList',array('tttttt'=>''));
        if($whInfo['errCode'] == 0){
            return json_decode($whInfo['data'],true);
        }
        return array();
    }

    //提供原始数组，让仓库那边过滤出有仓位和有库存并且符合仓库id的数组，返回符合条件的新品的数组
    function getNewGoodsArr($skuArray, $storeId){
        //print_r($skuArray);
//        print_r($storeId);
//        exit;
        if(empty($skuArray) || !is_array($skuArray) || intval($storeId) <= 0){
            return false;
        }
        $array = array('skuArr'=>gzdeflate(json_encode($skuArray)),'storeId'=>$storeId);
        $newGoodsArr = UserCacheModel::getOpenSysApiPost('wh.detectSkuStoreInfo',$array, 'gw88');
        print_r($newGoodsArr);
        exit;
        if(isset($newGoodsArr['errCode']) && $newGoodsArr['errCode'] == 0){
            return json_decode($newGoodsArr['data'],true);
        }
        return array();
    }

    ////创建MQ对象
//    function newMQObj(){
//        require_once WEB_PATH."lib/rabbitmq/rabbitmq.class.php";
//        $rabbitMq = new RabbitMQClass("MQUSER","MQPSW","MQVHOST","MQSERVERADDRESS");
//    }

    //调用MQ发布队列消息,goods,goods_combine,sku_combine_relation这3个表的
    function publishMQ($tName, $sql, $serverAddress){
        if(C("MQSWITH") == "YES"){
            require_once WEB_PATH."lib/rabbitmq/rabbitmq.class.php";
            $rabbitMq = new RabbitMQClass(C("MQUSER"),C("MQPSW"),C("MQVHOST"),C("MQSERVERADDRESS"));
            if($tName == 'pc_goods' || $tName == 'pc_goods_combine' || $tName == 'pc_sku_combine_relation'){
                $rabbitMq->queue_publish(C("MQ_EXCHANGE"), $sql);
    	    }elseif($tName == 'pc_sku_conversion'){//料号转换的
                $rabbitMq->queue_publish(C("MQ_SKUCONVERSION_EXCHANGE"), $sql);
    	    }elseif($tName == 'pc_goods_category'){
    	        $rabbitMq->queue_publish(C("MQ_CATEGROY_EXCHANGE"), $sql);
    	    }elseif($tName == 'pc_goods_partner_relation'){
    	        $rabbitMq->queue_publish(C("MQ_GOODSPARTNER_EXCHANGE"), $sql);
    	    }
        }
    }

    //检查指定sku是否存在，只支持单料号
    function isSkuExist($sku){
        if(empty($sku)){
            return false;
        }
        $tName = 'pc_goods';
        $where = "WHERE sku='$sku' and is_delete=0";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //检查指定spu是否存在goods表中，只支持单料号
    function isSpuExist($spu){
        if(empty($spu)){
            return false;
        }
        $tName = 'pc_goods';
        $where = "WHERE spu='$spu' and is_delete=0";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //检查指定spu且采购是否存在goods表中，只支持单料号
    function isSpuExistBySpuAndPurchaseId($spu, $purchaseId){
        if(empty($spu)){
            return false;
        }
        $tName = 'pc_goods';
        $where = "WHERE is_delete=0 and spu='$spu' and purchaseId='$purchaseId'";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //获取指定已经同意的指定SPU的销售人id数组
    function getisAgreeCombineSalerIdArrBySpu($spu){
        $returnArr = array();
        $tName = 'pc_spu_saler_combine';
        $select = 'salerId';
        $where = "WHERE is_delete=0 and spu='$spu' and isAgree=2";
        $salerIdList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($salerIdList as $value){
            $returnArr[] = $value['salerId'];
        }
        return $returnArr;
    }

    //检查指定spu是否是否是老品的SPU
    function isSpuOldExist($spu){
        if(empty($spu)){
            return false;
        }
        $tName = 'pc_goods';
        $where = "WHERE spu='$spu' and is_delete=0 and isNew=0";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //检查指定spu档案是否已经关联了属性值，有关联返回true,无关联返回false
    function isExistSpuPPVrelation($spu){
        if(empty($spu)){
            return false;
        }
        $tName = 'pc_archive_spu_property_value_relation';
        $where = "WHERE spu='$spu'";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //检查指定spu档案是否已经有了尺寸测量记录，有关联返回true,无关联返回false
    function isExistSpuInputSizeMeasure($spu){
        if(empty($spu)){
            return false;
        }
        $tName = 'pc_archive_spu_input_size_measure';
        $where = "WHERE spu='$spu'";
        $skuCount = OmAvailableModel::getTNameCount($tName, $where);
        if($skuCount){
            return true;
        }else{
            return false;
        }
    }

    //查看指定类别是否已经关联了指定的属性名，
    function isRelatedWithPidAndPP($pid, $propertyName){
        if(empty($pid) || empty($propertyName)){
            return false;
        }
        $tName = 'pc_archive_property';
        $where = "WHERE categoryPath='$pid' AND propertyName='$propertyName'";
        $ppCount = OmAvailableModel::getTNameCount($tName, $where);
        if($ppCount){
            return true;
        }else{
            return false;
        }
    }

    //返回spu_archive表中所有的spu记录
    function getSpuPPV(){
        $tName = 'pc_archive_spu_property_value_relation';
        $select = 'spu';
        $where = "group by spu";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $spuList;
    }

    //根据虚拟料号返回对应的真实的成本和重量(cost weight)
    function getTrueCWForCombineSku($combineSku){
        $trueSkuList = OmAvailableModel::getTrueSkuForCombine($combineSku);
        if(empty($trueSkuList)){
            return 0;
        }
        $array = array();
        $totalCost = 0;
        $totalWeight = 0;
        foreach($trueSkuList as $value){
            $sku = $value['sku'];
            $count = intval($value['count']);
            $tName = 'pc_goods';
            $select = 'goodsCost,goodsWeight';
            $where = "WHERE sku='$sku'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $goodsCost = $skuList[0]['goodsCost']?$skuList[0]['goodsCost']:0;
            $goodsWeight = $skuList[0]['goodsWeight']?$skuList[0]['goodsWeight']:0;

            $totalCost += $goodsCost*$count;
            $totalWeight += $goodsWeight*$count;
        }
        $array['totalCost'] = $totalCost;
        $array['totalWeight'] = $totalWeight;
        return $array;
    }

    //根据虚拟料号返回对应的包材成本和包材重量
    function getTruePMCWForCombineSku($combineSku){
        $trueSkuList = OmAvailableModel::getTrueSkuForCombine($combineSku);
        if(empty($trueSkuList)){
            return 0;
        }
        $countTrueSkuList = count($trueSkuList);
        $array = array();
        $pmTotalCost = 0;//包材总成本
        $pmTotalWeight = 0;//包材总重量
        foreach($trueSkuList as $value){
            $sku = $value['sku'];
            $count = intval($value['count']);
            $tName = 'pc_goods';
            $select = 'pmId,pmCapacity';
            $where = "WHERE sku='$sku'";
            $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
            $pmId = $skuList[0]['pmId'];
            $pmId = intval($pmId);//取得对应sku的pmId，和pmCapacity
            $pmCapacity = $skuList[0]['pmCapacity'];
            $pmCapacity = intval($pmCapacity)?intval($pmCapacity):1;//容量为空，则默认为1
            if($pmId > 0){
                $tName = 'pc_packing_material';
                $select = 'pmName,pmWeight,pmCost';
                $where = "WHERE id='$pmId'";
                $pmList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($pmList)){
                    $pmName = $pmList[0]['pmName'];//包材名称
                    $pmWeight = $pmList[0]['pmWeight'];//该真实sku下对应包材重量
                    $pmCost = $pmList[0]['pmCost'];//该真实sku下对应包材价格
                    //下面求出该真实料号及数量要对应包材的个数
                    $pmCount = ceil($count/$pmCapacity);//sku数量/包材容量，取整 得到包材个数
                    $pmTotalCost += $pmCost*$pmCount;
                    $pmTotalWeight += $pmWeight*$pmCount;
                }
            }
        }
        $array['pmTotalCost'] = $pmTotalCost/$countTrueSkuList;
        $array['pmTotalWeight'] = $pmTotalWeight/$countTrueSkuList;
        return $array;
    }

    //iostoreId返回他是否是草稿
    function isIoStoreSendToWh($id){
        $id = intval($id);
        $isDraft = false;//是否是草稿,默认不是草稿
        $tName = 'pc_products_iostore';
        $where = "WHERE id=$id and is_delete=0 and iostoreStatus=1";
        $isDraftCount = OmAvailableModel::getTNameCount($tName, $where);//取得满足条件的记录数
        if($isDraftCount){
            $isDraft = true;//找到对应满足id和草稿的记录时，为true;
        }
        return $isDraft;
    }

    //针对领/退料单中，已领但未归还的sku查询，$useTypeId为用途类型，1为制作，2为修改；$whId为仓库id
    function getIsNotBackSkuList($useTypeId=0, $whId=0){
        $useTypeId = intval($useTypeId);
        $whId = intval($whId);
        //领料单中的sku
        $tName = 'pc_products_iostore_detail';
        $select = "sku";
        $where = "WHERE is_delete=0 AND iostoreTypeId=1 AND isAudit<>3 ";//先select出所有领料单中的sku(审核不通过的除外),这里面要除去
        if($useTypeId > 0){
            $where .= "AND useTypeId=$useTypeId ";
        }
        if($whId > 0){
            $where .= "AND whId=$whId ";
        }
        $outStoreDetailSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//领料单详细中的sku
        $outStoreSkuArr = array();//用来存放领料单中所有的sku
        foreach($outStoreDetailSkuList as $value){
            $outStoreSkuArr[] = $value['sku'];
        }
        $outStoreSkuAndCountArr = array_count_values($outStoreSkuArr);//领料单中对应sku和数量

        //退料单中的sku
        $tName = 'pc_products_iostore_detail';
        $select = "sku";
        $where = "WHERE is_delete=0 AND iostoreTypeId=2 ";//先select出所有领料单中的sku
        if($useTypeId > 0){
            $where .= "AND useTypeId=$useTypeId ";
        }
        if($whId > 0){
            $where .= "AND whId=$whId ";
        }
        $inStoreDetailSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//领料单详细中的sku
        $inStoreSkuArr = array();//用来存放领料单中所有的sku
        foreach($inStoreDetailSkuList as $value){
            $inStoreSkuArr[] = $value['sku'];
        }
        $inStoreSkuAndCountArr = array_count_values($inStoreSkuArr);//领料单中对应sku和数量

        $isNotBackArr = array();//定义一个数组用来存放为归还的产品列表
        foreach($outStoreSkuAndCountArr as $key=>$value){//循环遍历领料单详细中的sku
            if(!isset($inStoreSkuAndCountArr[$key]) && $inStoreSkuAndCountArr[$key] > 0){//如果来归还skuList中，没有对应的sku，则表示该sku没有归还,切待归还数量就是领料单中sku对应的数量
                $isNotBackArr[$key] = $value;
            }else{//如果归还的sku列表中存在$key，则比较对应的数量
                if($value > $inStoreSkuAndCountArr[$key]){//如果领料sku对应数量大于归还的，则表示还有未归还的sku，数量是对应的差值
                    $isNotBackArr[$key] = $value - $inStoreSkuAndCountArr[$key];
                }
            }
        }
        //print_r($isNotBackArr);
//        exit;
        return $isNotBackArr;
    }

    //根据inputName和ppvName获取指定spu对应的尺寸测量的值
    function getSpuSizeInputMeasureValue($spu, $sizeName, $inputName){
        $tName = 'pc_archive_spu_input_size_measure';
        $select = 'valued';
        $where = "WHERE spu='$spu' AND sizeName='$sizeName' AND inputName='$inputName'";
        $measureValueList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $measureValueList[0]['valued'];
    }

    //添加重量变化操作记录方法
    function addWeightBackupsModify($sku, $skuweight, $userId, $isTranDL=true){
        $tName = 'pc_goods_weight_backups';
		$backupsArr = array ();
		$backupsArr['sku'] = $sku;
		$backupsArr['goodsWeight'] = $skuweight;
		$backupsArr['addUserId'] = $userId;
		$backupsArr['addTime'] = time();
		OmAvailableModel :: addTNameRow2arr($tName, $backupsArr);
        //标识是否要调用接口传输数据
        interFaceForCNDLToMH($sku, 2);
    }

    //添加体积变化操作记录方法
    function addVolumeBackupsModify($sku, $goodsLength, $goodsWidth, $goodsHeight, $userId, $isTranDL=true){
        $tName = 'pc_goods_volume_backups';
        $vpBackups = array();//体积变化表数据
        $vpBackups['sku'] = $sku;
        $vpBackups['goodsLength'] = $goodsLength;
        $vpBackups['goodsWidth'] = $goodsWidth;
        $vpBackups['goodsHeight'] = $goodsHeight;
        $vpBackups['addUserId'] = $userId;
        $vpBackups['addTime'] = time();
        OmAvailableModel::addTNameRow2arr($tName, $vpBackups);//添加体积变化记录
        //标识是否要调用接口传输数据
        interFaceForCNDLToMH($sku, 2);
    }

    //添加包材变化操作记录方法
    function addPmBackupsModify($sku, $pmId, $pmCapacity, $userId, $isTranDL=true){
        $tName = 'pc_goods_packingmaterial_backups';
        $pmBackups = array();
        $pmBackups['sku'] = $sku;
        $pmBackups['pmId'] = $pmId;
        $pmBackups['pmCapacity'] = $pmCapacity;
        $pmBackups['addUserId'] = $userId;
        $pmBackups['addTime'] = time();
        OmAvailableModel::addTNameRow2arr($tName, $pmBackups);//添加包材变化记录
    }

    //添加价格变化操作记录方法
    function addCostBackupsModify($sku, $goodsCost, $userId, $isTranDL=true){
        $tName = 'pc_goods_cost_history_record';
        $goodsCHRArr = array();
        $goodsCHRArr['sku'] = $sku;
        $goodsCHRArr['purchaseCost'] = $goodsCost;
        $goodsCHRArr['addUserId'] = $userId;
        $goodsCHRArr['addTime'] = time();
        OmAvailableModel::addTNameRow2arr($tName, $goodsCHRArr);//添加成本历史记录
        //标识是否要调用接口传输数据
        interFaceForCNDLToMH($sku, 2);
    }

    //修改成本核算的方法，在调用出判断是否有变化，有变化的才调用
    function updateCheckCostModify($sku, $checkCost, $isTranDL=true){
        $tName = 'pc_goods';
        $set = "set checkCost='$checkCost'";
        $where = "WHERE sku='$sku'";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        error_log(date('Y-m-d_H:i')."—— $sku 更新成功为 $checkCost \r\n",3,WEB_PATH."log/skuCheckCostModify.txt");
        //interFaceForCNDLToMH($sku, 2);
    }

    //添加状态变化操作记录方法
    function addStatusBackupsModify($sku, $goodsStatus, $reason, $userId, $isTranDL=true){
        $tName = 'pc_goods_update_status_reason';
        $addTime = time();
        $dataReason = array();
        $dataReason['sku'] = $sku;
        $dataReason['goodsStatus'] = $goodsStatus;
        $dataReason['reason'] = $reason;
        $dataReason['addUser'] = $userId;
        $dataReason['addTime'] = $addTime;
        OmAvailableModel::addTNameRow2arr($tName, $dataReason);
        //标识是否要调用接口传输数据
        interFaceForCNDLToMH($sku, 1);
    }

    //修改采购人Id方法
    function updatePurchaseIdModify($sku, $purchaseId, $userId, $isTranDL=true){
        $tName = 'pc_goods';
        $dataPurchaseArr = array();
        $dataPurchaseArr['purchaseId'] = $purchaseId;
        $where = "WHERE sku='$sku'";
        OmAvailableModel::updateTNameRow2arr($tName, $dataPurchaseArr, $where);
        $purchaseName = getPersonNameById($purchaseId);
        $userName = getPersonNameById($userId);
        error_log(date('Y-m-d_H:i')." $userName 将 $sku 转移给了 $purchaseName \r\n",3,WEB_PATH."log/updatePurchaseIdModify.txt");
    }

    //发送接口给明华，明华对接CNDL接口
    function interFaceForCNDLToMH($sku, $type){
        $tName = 'pc_goods';
        $select = 'spu,goodsCost,goodsStatus,goodsWeight,goodsLength,goodsWidth,goodsHeight';
        $where = "WHERE sku='$sku'";
        $skuLst = OmAvailableModel::getTNameList($tName, $select, $where);
        $spu = $skuLst[0]['spu'];
        $goodsCost = $skuLst[0]['goodsCost'];
        $goodsStatus = $skuLst[0]['goodsStatus'];
        $goodsWeight = $skuLst[0]['goodsWeight'];
        $goodsLength = $skuLst[0]['goodsLength'];
        $goodsWidth = $skuLst[0]['goodsWidth'];
        $goodsHeight = $skuLst[0]['goodsHeight'];
        $dataArr = array();
        $dataArr['spu'] = $spu;
        $dataArr['status'] = $goodsStatus == 1 || $goodsStatus == 51?1:0;
        $dataArr['weight'] = $goodsWeight;
        $dataArr['length'] = $goodsLength;
        $dataArr['width'] = $goodsWidth;
        $dataArr['height'] = $goodsHeight;
        $dataArr['cost'] = $goodsCost;
        $toArr = array();
        $toArr['sku'] = $sku;
        $toArr['type'] = $type;
        $toArr['data'] = json_encode($dataArr);
        $ret = OmAvailableModel::newData2ErpInterfOpen('erp.getPCgoodsInfo',$toArr,'gw88');
        error_log(date('Y-m-d_H:i')." sku:$sku type:$type data:{$toArr['data']} \r\n",3,WEB_PATH."log/interFaceForCNDLToMHLog.txt");
        //print_r($ret);
//        exit;
    }

    //从订单系统中获取所有平台信息
    function getAllPlatformInfo(){
        $platformList = UserCacheModel::getOpenSysApi('order.getPlatformList',array('all'=>'all'),'');
        //print_r(json_decode($platformList['data'], true));
//        exit;
        return json_decode($platformList['data'], true);
    }

    //定义SPU状态的宏定义方法
    function displayAllSpuStatus(){
        $spuStatusArr = array();//状态数组

        $spuStatusArr['1'] = array('id'=>'1','statusName'=>'普通新品');
        $spuStatusArr['51'] = array('id'=>'51','statusName'=>'PK系列');

        return $spuStatusArr;
    }

    //定义SKU状态的宏定义方法
    function displayAllSkuStatus(){
        $skuStatusArr = array();//状态数组
        $skuStatusArr['1'] = array('id'=>'1','statusName'=>'在线');
        $skuStatusArr['2'] = array('id'=>'2','statusName'=>'停售');
        $skuStatusArr['3'] = array('id'=>'3','statusName'=>'暂时停售');
        //$skuStatusArr['4'] = array('id'=>'4','statusName'=>'部分侵权-ebay');
//        $skuStatusArr['5'] = array('id'=>'5','statusName'=>'部分侵权-B2B');
//        $skuStatusArr['6'] = array('id'=>'6','statusName'=>'部分侵权-其他平台');
        $skuStatusArr['51'] = array('id'=>'51','statusName'=>'PK');
        return $skuStatusArr;
    }

    //定义SPU审核中需要按照部门搜索的部门数组
    function getSpuArchiveDetArr(){
        $spuArchiveDetArr = array();//状态数组
        $spuArchiveDetArr['2'] = array('id'=>'2','depName'=>'eBay销售一部');
        $spuArchiveDetArr['18'] = array('id'=>'18','depName'=>'eBay销售二部');
        $spuArchiveDetArr['19'] = array('id'=>'19','depName'=>'eBay销售三部');
        $spuArchiveDetArr['6'] = array('id'=>'6','depName'=>'eBay销售四部');
        $spuArchiveDetArr['74'] = array('id'=>'74','depName'=>'海外销售部&亚马逊销售一部');
        return $spuArchiveDetArr;
    }

    function isAutoCreateSpuExist($spu){
        if(empty($spu)){
            return array();
        }
        $tName = 'pc_auto_create_spu';
        $select = '*';
        $where = "WHERE is_delete=0 AND spu='$spu'";
        $autoCreateSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $autoCreateSpuList;
    }

    //获得已分配销售人员的SPU
    function getHasToSalerSpuByIPS($isSingSpu, $isAgree, $platformId='',$salerId=''){
        if($isSingSpu == 1){//单料号
            $tName = 'pc_spu_saler_single';
            $select = 'spu';
            $where = "WHERE is_delete=0 ";
            if(intval($isAgree) > 0){
                $where .= "AND isAgree=$isAgree ";
            }
            if(intval($platformId) > 0){
                $where .= "AND platformId=$platformId ";
            }
            if(intval($salerId) > 0){
                $where .= "AND salerId=$salerId ";
            }
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            return $spuList;
        }elseif($isSingSpu == 2){//虚拟料号
            $tName = 'pc_spu_saler_combine';
            $select = 'spu';
            $where = "WHERE is_delete=0 ";
            if(intval($isAgree) > 0){
                $where .= "AND isAgree=$isAgree ";
            }
            if(intval($platformId) > 0){
                $where .= "AND platformId=$platformId ";
            }
            if(intval($salerId) > 0){
                $where .= "AND salerId=$salerId ";
            }
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            return $spuList;
        }else{//两种情况相加
            $tName = 'pc_spu_saler_single';
            $select = 'spu';
            $where = "WHERE is_delete=0 ";
            if(intval($isAgree) > 0){
                $where .= "AND isAgree=$isAgree ";
            }
            if(intval($platformId) > 0){
                $where .= "AND platformId=$platformId ";
            }
            if(intval($salerId) > 0){
                $where .= "AND salerId=$salerId ";
            }
            $spuList1 = OmAvailableModel::getTNameList($tName, $select, $where);

            $tName = 'pc_spu_saler_combine';
            $select = 'spu';
            $where = "WHERE is_delete=0 ";
            if(intval($isAgree) > 0){
                $where .= "AND isAgree=$isAgree ";
            }
            if(intval($platformId) > 0){
                $where .= "AND platformId=$platformId ";
            }
            if(intval($salerId) > 0){
                $where .= "AND salerId=$salerId ";
            }
            $spuList2 = OmAvailableModel::getTNameList($tName, $select, $where);
            $spuList = array_merge($spuList1, $spuList2);
            return $spuList;
        }
    }

    //获得已存在产品制作人的SPU
    function getIsExsitWebMakerSpuByIW($isSingSpu='', $webMakerId='', $isAgree=''){
        $tName = 'pc_spu_web_maker';
        $select = 'spu';
        $where = "WHERE is_delete=0 ";
        if($isSingSpu == 1 || $isSingSpu == 2){
            $where .= "AND isSingSpu=$isSingSpu ";
        }
        if(intval($webMakerId) > 0){
            $where .= "AND webMakerId=$webMakerId ";
        }
        if(intval($isAgree) > 0){
            $where .= "AND isAgree=$isAgree ";
        }
        $where .= "group by spu";
        $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $spuList;
    }

    //根据人员id返回该对应的部门id
    function getDeptIdByUserId($userId){
        if(intval($userId) <= 0){
            return false;
        }
        $tName = 'power_global_user';
        $select = 'global_user_dept';
        $where = "WHERE global_user_is_delete=0 AND global_user_id='$userId'";
        $personInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
        $deptId = $personInfoList[0]['global_user_dept'];
        return $deptId;
    }

    //根据人员id返回该对应的部门信息，返回getAllArray形式
    function getDeptInfoByUserId($userId){
        if(intval($userId) <= 0){
            return array();
        }
        $deptId = getDeptIdByUserId($userId);
        if(empty($deptId)){
            return array();
        }
        $tName = 'power_dept';
        $select = '*';
        $where = "WHERE dept_id='$deptId' AND dept_isdelete=0";
        $deptInfoArr = OmAvailableModel::getTNameList($tName, $select, $where);
        return $deptInfoArr;
    }

    //返回所有速卖通岗位的岗位路径path数组
    function getAllSMTJobPath(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_isdelete=0 AND job_name like'速卖通销售%'";
        $jobPathList = OmAvailableModel::getTNameList($tName, $select, $where);
        $returnArr = array();
        foreach($jobPathList as $value){
            $returnArr[] = $value['job_path'];
        }
        return $returnArr;
    }

    //返回所有速卖通销售组长和经理岗位的岗位路径path数组(M-manager,G-group leader 即经理和组长)
    function getAllMGSMTJobPath(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_isdelete=0 AND job_name like'速卖通销售%组长' or job_name like'速卖通销售%经理'";
        $jobPathList = OmAvailableModel::getTNameList($tName, $select, $where);
        $returnArr = array();
        foreach($jobPathList as $value){
            $returnArr[] = $value['job_path'];
        }
        return $returnArr;
    }

    //根据条件自动添加销售人员记录
    function addOrUpdateSalerInfo($platformId, $spu, $isSingSpu, $salerId){
        if($isSingSpu == 1){//单料号
            $tName = 'pc_spu_saler_single';
        }else{
            $tName = 'pc_spu_saler_combine';
        }
        $select = 'salerId';
        $where = "WHERE is_delete=0 AND spu='$spu' AND platformId='$platformId'";
        $oldSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
        $dataArr = array();
        if(empty($oldSalerList)){//如果记录不存在，则插入数据
            $dataArr['platformId'] = $platformId;
            $dataArr['spu'] = $spu;
            $dataArr['salerId'] = $salerId;
            $dataArr['addTime'] = time();
            OmAvailableModel::addTNameRow2arr($tName, $dataArr);
        }else{//有该平台及该SPU的销售人记录时
            if($oldSalerList[0]['salerId'] != $salerId){//如果销售人不同时，则修改
                $dataArr['salerId'] = $salerId;
                $dataArr['isHandsOn'] = 1;//更改接手状态
                $dataArr['addTime'] = time();//更新添加时间
                OmAvailableModel::updateTNameRow2arr($tName, $dataArr, $where);
            }
        }
    }

    //根据userId返回对应的岗位路径
    function getJobPathByGlobalUserId($globalUserId){
        $tName = 'power_global_user';
        $select = 'global_user_job_path';
        $where = "WHERE global_user_is_delete=0 AND global_user_id='$globalUserId'";
        $jobPahtList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($jobPahtList)){
            return false;
        }else{
            return $jobPahtList[0]['global_user_job_path'];
        }
    }

    //判断并添加销售人员记录的方法,后期可以在里面添加不同逻辑，保证统一入口
    function addSalerInfoForAny($spu, $isSingSpu, $userId, $salerId){
        $jobPath = getJobPathByGlobalUserId($userId);//取得操作人$userId的岗位路径
        $smtJobPaht = getAllSMTJobPath();//得到速卖通相关的岗位路径数组
        if(!empty($jobPath) && in_array($jobPath, $smtJobPaht)){//如果操作人是速卖通部门相关人时，则自动添加$salerId进去
            $smtMGJobPaht = getAllMGSMTJobPath();
            if(in_array($jobPath, $smtMGJobPaht)){//如果是速卖通销售经理或组长的话,对应销售人就是对应真实SKU的采购
                $tName = 'pc_sku_combine_relation';
                $select = 'sku';
                $where = "WHERE combineSku REGEXP '^$spu(_[A-Z0-9]+)*$'";
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($skuList)){//存在记录的话
                    $tmpArr = array();
                    foreach($skuList as $value){
                        $tmpArr[] = "'".$value['sku']."'";
                    }
                    $tmpStr = implode(',', $tmpArr);
                    $tName = 'pc_goods';
                    $select = 'spu';
                    $where = "WHERE is_delete=0 and sku in($tmpStr) order by goodsStatus";
                    $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $salerId = 0;
                    foreach($spuList as $value){
                        $tName = 'pc_spu_saler_single';
                        $select = 'salerId';
                        $where = "WHERE is_delete=0 and platformId=2 and isAgree=2 and spu='{$value['spu']}'";
                        $salerIdList = OmAvailableModel::getTNameList($tName, $select, $where);
                        if(!empty($salerIdList) && isset($salerIdList[0]['salerId']) && intval($salerIdList[0]['salerId']) > 0){
                            $salerId = $salerIdList[0]['salerId'];
                            break;
                        }
                    }
                    if(!empty($salerId)){
                        addOrUpdateSalerInfo(2, $spu, $isSingSpu, $salerId);
                    }
                }
            }else{
                addOrUpdateSalerInfo(2, $spu, $isSingSpu, $salerId);
            }
        }
    }

    //提供部门信息，提供产品部人员指派维护里的公司列表显示
    function getAppointPersonDept(){
        $tName = 'power_dept';
        $select = 'dept_id,dept_name';
        $where = "WHERE dept_isdelete=0 AND dept_company_id=1 AND (dept_name like'%销售%' or dept_name like'采购%') order by dept_name";
        $deptList = OmAvailableModel::getTNameList($tName, $select, $where);
        return $deptList;
    }

    //提供所有产品工程师信息
    function getAllPEInfo(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_isdelete=0 AND job_id in(6)";
        $jobPathList = OmAvailableModel::getTNameList($tName, $select, $where);
        $pathArr = array();
        foreach($jobPathList as $value){
            $pathArr[] = "'".$value['job_path']."'";
        }
        if(empty($pathArr)){
            return array();
        }else{
            $pathStr = implode(',', $pathArr);
            $tName = 'power_global_user';
            $select = 'global_user_id,global_user_name';
            $where = "WHERE global_user_is_delete=0 AND global_user_job_path in($pathStr) order by global_user_login_name";
            $personList = OmAvailableModel::getTNameList($tName, $select, $where);
            return $personList;
        }
    }

    //根据detId返回部门名称
    function getDepNameByDepId($depId){
        $tName = 'power_dept';
        $select = 'dept_name';
        $where = "WHERE dept_isdelete=0 AND dept_id='$depId'";
        $depList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($depList)){
            return '';
        }else{
            return $depList[0]['dept_name'];
        }
    }

    //根据detId返回部门名称
    function getDepIdByUserId($depId){
        $tName = 'power_dept';
        $select = 'dept_name';
        $where = "WHERE dept_isdelete=0 AND dept_id='$depId'";
        $depList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($depList)){
            return '';
        }else{
            return $depList[0]['dept_name'];
        }
    }

    //根据指定userId返回该id下能获得指派的产品工程师列表
    function getAppointPersonInfoByUserId($userId){
        $depId = getDeptIdByUserId($userId);
        $personInfoArr = array();
        if(!empty($depId)){
            $tName = 'pc_products_appoint_person';
            $select = 'appointPersonId';
            $where = "WHERE is_delete=0 and depId='$depId'";
            $peIdList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($peIdList)){
                $tmpArr = array();
                foreach($peIdList as $value){
                    $tmpArr[] = $value['appointPersonId'];
                }
                if(!empty($tmpArr)){
                    $tmpStr = implode(',', $tmpArr);
                    $tName = 'power_global_user';
                    $select = 'global_user_id,global_user_name';
                    $where = "WHERE global_user_is_delete=0 AND global_user_id in($tmpStr) order by global_user_login_name";
                    $personInfoArr = OmAvailableModel::getTNameList($tName, $select, $where);
                }
            }
        }
        return $personInfoArr;
    }

    //通过接口返回深圳ERP中领料单中未审核以及产品制作表中未被领取的SPU数组
    function getNotAuditAndNotTakeSpuList(){
        $spuArr = UserCacheModel::getOpenSysApi('pc.erp.getNotAuditAndNotTakeSpuList',array('all'=>'all'),'gw88');
        if(is_array($spuArr) && !empty($spuArr)){
            return $spuArr;
        }else{
            return array();
        }
    }

    //获取所有ebay销售人员信息
    function getAllEbaySalerInfo(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_isdelete=0 and job_name like'ebay%销售' or job_name like'ebay%组长'";
        $ebayJobPathList = OmAvailableModel::getTNameList($tName, $select, $where);//取得ebay销售部门的组长及销售的job_path
        $pathArr = array();
        foreach($ebayJobPathList as $value){
            $pathArr[] = "'".$value['job_path']."'";
        }
        $returnArr = array();
        if(!empty($pathArr)){
            $pathStr = implode(',', $pathArr);
            $tName = 'power_global_user';
            $select = 'global_user_id,global_user_name';
            $where = "WHERE global_user_is_delete=0 AND global_user_job_path in($pathStr) order by global_user_login_name";
            $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
        }
        return $returnArr;
    }

    //获取所有SMT销售人员信息
    function getAllSMTSalerInfo(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_name like'速卖通%销售' or job_name like'速卖通%组长'";
        $ebayJobPathList = OmAvailableModel::getTNameList($tName, $select, $where);//取得ebay销售部门的组长及销售的job_path
        $pathArr = array();
        foreach($ebayJobPathList as $value){
            $pathArr[] = "'".$value['job_path']."'";
        }
        $returnArr = array();
        if(!empty($pathArr)){
            $pathStr = implode(',', $pathArr);
            $tName = 'power_global_user';
            $select = 'global_user_id,global_user_name';
            $where = "WHERE global_user_is_delete=0 AND global_user_job_path in($pathStr) order by global_user_login_name";
            $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
        }
        return $returnArr;
    }

    //获取所有amazon销售人员信息
    function getAllAmazonSalerInfo(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_name like'亚马逊%销售' or job_name like'亚马逊%销售组长'";
        $ebayJobPathList = OmAvailableModel::getTNameList($tName, $select, $where);//取得ebay销售部门的组长及销售的job_path
        $pathArr = array();
        foreach($ebayJobPathList as $value){
            $pathArr[] = "'".$value['job_path']."'";
        }
        $returnArr = array();
        if(!empty($pathArr)){
            $pathStr = implode(',', $pathArr);
            $tName = 'power_global_user';
            $select = 'global_user_id,global_user_name';
            $where = "WHERE global_user_is_delete=0 AND global_user_job_path in($pathStr) order by global_user_login_name";
            $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
        }
        return $returnArr;
    }

    //获取所有海外仓销售人员信息
    function getAllOverseaSalerInfo(){
        $tName = 'power_job';
        $select = 'job_path';
        $where = "WHERE job_name like'海外%销售' or job_name like'海外%销售组长'";
        $ebayJobPathList = OmAvailableModel::getTNameList($tName, $select, $where);//取得ebay销售部门的组长及销售的job_path
        $pathArr = array();
        foreach($ebayJobPathList as $value){
            $pathArr[] = "'".$value['job_path']."'";
        }
        $returnArr = array();
        if(!empty($pathArr)){
            $pathStr = implode(',', $pathArr);
            $tName = 'power_global_user';
            $select = 'global_user_id,global_user_name';
            $where = "WHERE global_user_is_delete=0 AND global_user_job_path in($pathStr) order by global_user_login_name";
            $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
        }
        return $returnArr;
    }

    //获取开启了的修改流程的类型信息
    function getisOnModifyTypeList(){
        $tName = 'pc_spu_modify_type';
        $select = 'modifyTypeName';
        $where = "WHERE is_delete=0 and isOn=0 and type=1";//修改类型type=1
        $modifyTypeList = OmAvailableModel::getTNameList($tName, $select, $where);//取得ebay销售部门的组长及销售的job_path
        return $modifyTypeList;
    }

    //根据SPU返回对应包含该SPU的虚拟SPU信息
    function getCombineSpuBySpu($spu){
        $returnArr = array();
        $tName = 'pc_goods';
        $select = 'sku';
        $where = "WHERE is_delete=0 and spu='$spu'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($skuList)){
            $tmpArr = array();
            foreach($skuList as $value){
                $tmpArr[] = "'".$value['sku']."'";
            }
            if(!empty($tmpArr)){
                $tmpStr = implode(',', $tmpArr);
                $tName = 'pc_sku_combine_relation';
                $select = 'combineSku';
                $where = "WHERE sku in($tmpStr)";
                $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($combineSkuList)){
                    $tmpArr = array();
                    foreach($combineSkuList as $value){
                        $tmpArr[] = "'".$value['combineSku']."'";
                    }
                    if(!empty($tmpArr)){
                        $tmpStr = implode(',', $tmpArr);
                        $tName = 'pc_goods_combine';
                        $select = 'combineSpu';
                        $where = "WHERE is_delete=0 and combineSku in($tmpStr) group by combineSpu";
                        $combineSpuList = OmAvailableModel::getTNameList($tName, $select, $where);
                        if(!empty($combineSpuList)){
                            foreach($combineSpuList as $value){
                                $returnArr[] = $value['combineSpu'];
                            }
                        }
                    }
                }
            }
        }
        return $returnArr;
    }

    //根据PEId获取指派给该产品工程师但是未领取的真实SPU的数量
    function getAppointSpuCountByWebMakerId($webMakerId){
        $tName = 'pc_spu_web_maker';
        $select = 'id';
        $where = "WHERE is_delete=0 and isSingSpu=1 and isTake=0 and isComplete=0 and webMakerId='$webMakerId' and webMakerId<>'759' group by spu";//去掉759这个无效人员（nullPEAccount）
        $webMakerIdList = OmAvailableModel::getTNameList($tName, $select, $where);
        return count($webMakerIdList);
    }
    
    //根据PEId获取产品部维护的指派数量
    function getPECountByPEId($PEId){
        $tName = 'pc_products_pe_count';
        $select = 'count';
        $where = "WHERE is_delete=0 and PEId='$PEId' limit 1";
        $PECountList = OmAvailableModel::getTNameList($tName, $select, $where);
        $returnCount = 14;//默认为14个
        if(!empty($PECountList[0]['count'])){
            $returnCount = $PECountList[0]['count'];
        }
        return $returnCount;
    }
    
    //根据PEId判断该产品工程师是否有被指派的记录
    function isExistAppointByPEId($PEId){
        $tName = 'pc_products_appoint_person';
        $where = "WHERE is_delete=0 and appointPersonId='$PEId'";
        $PECount = OmAvailableModel::getTNameCount($tName, $where);
        return $PECount;
    }
    
    //根据PEId判断该产品工程师是否有被指派的记录（大类指派）
    function isExistAppointByPEId2($PEId){
        $tName = 'pc_products_large_category_appoint';
        $where = "WHERE is_delete=0 and appointPEId='$PEId'";
        $PECount = OmAvailableModel::getTNameCount($tName, $where);
        return $PECount;
    }
    
    //通过combineSpu返回对应的明细信息
    function getSkuDetailInfoByCombineSpu($combineSpu){
        $returnArr = array();
        $tName = 'pc_goods_combine';
        $select = 'combineSku';
        $where = "WHERE is_delete=0 AND combineSpu='$combineSpu'";
        $combineSkuList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($combineSkuList as $value){
            $combineSku = $value['combineSku'];
            $tName = 'pc_sku_combine_relation';
            $select = 'sku,count';
            $where = "WHERE combineSku='$combineSku'";
            $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
            $tmpArr = array();
            foreach($skuRelationList as $v){
                $tmpArr[$v['sku']] = $v['count'];
            }
            $returnArr[$combineSku] = $tmpArr;
        }
        return $returnArr;
    }
    
    //通过combineSpu返回对应的明细信息
    function getAllTransportInfo(){
        $data = UserCacheModel::getOpenSysApi('trans.carrier.info.get', array('type'=>2));
        return !empty($data['data'])?$data['data']:array();
    }
    
    //通过combineSpu返回对应的明细信息
    function getChannelInfoByTransportId($carrierId){
        $data = UserCacheModel::getOpenSysApi('trans.carrier.channel.info.get', array('carrierId'=>$carrierId));
        return !empty($data['data'])?$data['data']:array();
    }
    
    //通过combineSpu返回对应的明细信息
    function getAllChannelInfo(){
        $data = UserCacheModel::getOpenSysApi('trans.carrier.channel.info.get', array('carrierId'=>'all'));
        $returnArr = array();
        if(!empty($data['data'])){
            foreach($data['data'] as $value){
                $returnArr[$value['carrierId']][] = array('id'=>$value['id'], 'channelName'=>$value['channelName']);
            }
        }
        return $returnArr;
    }
    
    //取得特殊属性表
    function getAllSpecailPropertyList(){
        $tName = 'pc_special_property';
        $select = '*';
        $where = "WHERE isOn=1 order by sort";
        return OmAvailableModel::getTNameList($tName, $select, $where);
    }
    
    //根据spu取得对应在特殊料号运输方式管理中对应的记录
    function getSpecialTMInfoBySpu($spu){
        $returnArr = array();
        $tName = 'pc_special_transport_manager_spu';//spu关联特殊运输方式管理的表
        $select = '*';
        $where = "WHERE spu='$spu' limit 1";
        $stmsList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($stmsList)){
            $pstmsId = $stmsList[0]['id'];//pc_special_transport_manager_spu 的自增ID
            $pstmsStmnId = $stmsList[0]['stmnId'];//pc_special_transport_manager 的自增ID
            $tName = 'pc_special_transport_manager';//特殊运输方式管理表
            $select = '*';
            $where = "WHERE id='$pstmsStmnId'";
            $pstmList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($pstmList)){
                $specialTransportManagerName = $pstmList[0]['specialTransportManagerName'];
                $isOn = $pstmList[0]['isOn'];
                if($isOn == 1){//启用才有效，禁用不生效
                    $tName = 'pc_special_stmnid_transportid';
                    $select = '*';
                    $where = "WHERE stmnId='$pstmsStmnId'";
                    $psstList = OmAvailableModel::getTNameList($tName, $select, $where);//可能对应过个运输方式id
                    foreach($psstList as $value){
                        
                    }
                }
            }
        }
        return OmAvailableModel::getTNameList($tName, $select, $where);
    }
    
    //根据SPU的类别能获得指派的产品工程师列表
    function getAppointPersonInfoBySpu($spu){
        $returnArr = array();
        if(!empty($spu)){
            $tName = 'pc_spu_archive';
            $select = 'categoryPath';
            $where = "WHERE is_delete=0 and spu='$spu'";
            $spuList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(!empty($spuList[0]['categoryPath'])){
                $tName = 'pc_products_large_category';
                $select = 'id';
                $where = "WHERE relateERPCategory like'%{$spuList[0]['categoryPath']}%'";
                $pplcList = OmAvailableModel::getTNameList($tName, $select, $where);
                //print_r($pplcList);exit;
                if(!empty($pplcList)){
                    $tmpArr = array();
                    foreach($pplcList as $value){
                        $tmpArr[] = $value['id'];
                    }
                    if(!empty($tmpArr)){
                        $tmpArrStr = implode(',', $tmpArr);
                        $tName = 'pc_products_large_category_appoint';
                        $select = 'appointPEId';
                        $where = "WHERE largeCategoryId in($tmpArrStr) AND is_delete=0";
                        $pplcaList = OmAvailableModel::getTNameList($tName, $select, $where);
                        //print_r($pplcList);exit;
                        if(!empty($pplcaList)){
                            $tmpArr = array();
                            foreach($pplcaList as $value){
                                $tmpArr[] = $value['appointPEId'];
                            }
                            if(!empty($tmpArr)){
                                $tmpStr = implode(',', $tmpArr);
                                $tName = 'power_global_user';
                                $select = 'global_user_id,global_user_name';
                                $where = "WHERE global_user_is_delete=0 AND global_user_id in($tmpStr) order by global_user_login_name";
                                $returnArr = OmAvailableModel::getTNameList($tName, $select, $where);
                            }
                        }
                    }
                }
            }
        }
        return $returnArr;
    }


?>