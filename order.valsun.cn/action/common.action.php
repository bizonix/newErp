<?php
/**
 * 类名：CommAct
 * 功能：管理Action公用的方法类
 * 版本：2013-09-26
 * 作者：Herman.Xi
 */
class CommonAct{

	static $errCode	  = 0;
	static $errMsg	  = '';

	public static $url = 'http://idc.gw.open.valsun.cn/router/rest?';  //开放系统入口地址
	//public static $token = '18006c5d80cf4a05518e382adccb3469'; //用户token(222)测试用

	
	//构造函数
	public function __construct(){
		
	}
	
	//获取控制器方法信息
	public function act_getActionById($actionid){
		
		$actionid = intval($actionid);
		if ($actionid===0){
			self::$errCode = '5806';
			self::$errMsg  = 'actionid is error';
			return array();
		}
		
		$actionsingle = CommonModel::getInstance();
		$filed =' action_id,action_description,action_name,action_group_id,group_description,group_name,group_system_id';
		$where = " WHERE action_id='{$actionid}'";
		$actioninfo = $actionsingle->getActionInfo($filed, $where);
		return false;
	}
	
	//获取新系统待发货数量API接口
	public function act_getsaleandnosendallAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		
		$data = CommonModel::getsaleandnosendall($sku, $storeId);
		return $data;
	}
	
	//获取被拦截sku数量API接口
	public function act_getinterceptallAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		
		$data = CommonModel::getinterceptall($sku, $storeId);
		return $data;
	}
	
	//获取自动拦截sku数量API接口
	public function act_autointerceptAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel::get_autointercept($sku, $storeId);
		return $data;
	}
	
	//获取待审核sku数量API接口
	public function act_getauditingallAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel::getauditingall($sku, $storeId);
		return $data;	
	}
	
	//获取最后一次销售数据API接口
	public function act_getlastsaleAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel::get_lastsale($sku, $storeId);
		return $data;	
	}
	
	//获取第一次销售sku数量API接口
	public function act_getfirstsaleAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel::get_firstsale($sku, $storeId);
		return $data;	
	}
	
	//获取待审核sku数量API接口
	public function act_getSaleProductsAPI(){
		$start = $_GET['start'];
		if (empty($start)){
			self::$errCode = '5801';
			self::$errMsg  = 'start time is error';
			return array();
		}
		$end = $_GET['end'];
		if (empty($end)){
			self::$errCode = '5802';
			self::$errMsg  = 'end time is error';
			return array();
		}
		$everyday_sale = $_GET['everyday_sale'];
		if (empty($everyday_sale)){
			$everyday_sale = 5;
		}
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel:: getSaleProducts($start, $end, $sku, $everyday_sale, $storeId);
		//var_dump($data);
		return $data;
	}
	
	//获取第一次销售sku数量API接口
	public function act_getorderbyskuAPI(){
		$sku = $_GET['sku'];
		$storeId = 1;
		if(isset($_GET['storeId']) && !empty($_GET['storeId'])){
			$storeId = $_GET['storeId'];
		}
		if (empty($sku)){
			self::$errCode = '5806';
			self::$errMsg  = 'sku is error';
			return array();
		}
		$data = CommonModel::getorderbysku($sku, $storeId);
		return $data;	
	}
	
	/**
	 * CommonAct::act_GetSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @return string
	 */
	public static function act_GetSkuImg() {
		$spu 	= isset($_REQUEST['spu']) ? $_REQUEST['spu'] : '';
		$sku 	= isset($_REQUEST['sku']) ? $_REQUEST['sku'] : '';
		$picType	= isset($_REQUEST['picType']) ? $_REQUEST['picType'] : 'G';
		//var_dump($spuArr);
		if (empty($spu)) {
			self::$errCode  = 10000;
			self::$errMsg   = "主料号参数有误！";
			return false;
		}
		if (empty($sku)) {
			self::$errCode  = 10001;
			self::$errMsg   = "子料号参数有误！";
			return false;
		}
		if (strlen($spu) == 1) {
            $spu = '00'.$spu;
        }
        if (strlen($spu) == 2) {
            $spu = '0'.$spu;
        }
		if (strlen($sku) == 1) {
            $sku = '00'.$sku;
        }
        if (strlen($sku) == 2) {
            $sku = '0'.$sku;
        }
		$res			= CommonModel::getSkuImg($spu, $sku, $picType);
		//var_dump($res);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $res;		
	}
	
	/**
	 * CommonAct::act_GetSkuImg()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @return string
	 */
	public static function act_GetSpuImg() {
		$spuArr 	= isset($_REQUEST['spu']) ? $_REQUEST['spu'] : '';
		$skuArr 	= isset($_REQUEST['sku']) ? $_REQUEST['sku'] : '';
		$picType	= isset($_REQUEST['picType']) ? $_REQUEST['picType'] : 'G';
		//var_dump($spuArr);
		if (empty($spuArr)) {
			self::$errCode  = 10000;
			self::$errMsg   = "主料号参数有误！";
			return false;
		}
		/*if (strlen($spu) == 1) {
            $spu = '00'.$spu;
        }
        if (strlen($spu) == 2) {
            $spu = '0'.$spu;
        }*/
		// if (empty($sku)) {
			// self::$errCode  = 10001;
			// self::$errMsg   = "子料号参数有误！";
			// return false;
		// }
		$res			= CommonModel::getPicsBySkuArr($spuArr, $picType);
		//var_dump($res);
		self::$errCode  = CommonModel::$errCode;
        self::$errMsg   = CommonModel::$errMsg;
        return $res;		
	}
	
	/**
	 * CommonAct::act_getSpuAllPic()
	 * 获取sku图片
	 * @param string $spu 主料号
	 * @param string $picType 图片类型
	 * @return string
	 */
	public function act_getSpuAllPic($spu = '',$picType = '') {
		if(empty($spu)) {
			$spu	= isset($_REQUEST['spu']) ? $_REQUEST['spu'] : '';
		}
		//var_dump($spu);
		if(empty($picType)) {
			$picType	= strlen(htmlentities($_REQUEST['picType'],ENT_QUOTES)) > 0 ? htmlentities($_REQUEST['picType'],ENT_QUOTES) : 'G';
		}
		$errStr = '';
		if(empty($spu)) {
			$errStr .= '料号输入错误！<br />';
		}
		if(empty($picType)) {
			$errStr .= '站点输入错误！<br />';
		}
		if(!empty($errStr)) {
			self::$errCode = '701034';
			self::$errMsg = $errStr;
			return false;
		}
		
		$spuPicList		= CommonModel::getSpuAllPic($spu, $picType);
		//var_dump($spuPicList);
		//self::$errCode  = CommonModel::$errCode;
        //self::$errMsg   = CommonModel::$errMsg;
		if(!empty($spuPicList['errCode']) || empty($spuPicList['data'])){
			return false;
        }
        $picArr = array();//定义一个数组来存放$spuArr中对应spu及对应的值，K=>V
        foreach($spu as $value){
            if(!empty($value)){
                $picArr[$value] = $spuPicList['data'][$value]['artwork'][$value][0];
            }        
        }
		//var_dump($picArr);
        return $picArr;
	}

    //ajax拉去全部图片(by spuArr)
    function act_ajaxGetAllArtPicBySpuArr(){
        $spuArr  = isset($_POST['spu'])?$_POST['spu']:"";//料号条码数组
        if(empty($spuArr)){
            return false;
        }        
        $picUrlArr = self::getPicFromOpenSysByArr($spuArr);
        return $picUrlArr;
    }

    //对接图片系统，取得对应spu的所有图片,spu是一个数组
    function getPicFromOpenSysByArr($spuArr, $picType=''){
  		if(!is_array($spuArr) || empty($spuArr)){//$spu是一个数组
  		    return false;
  		}
        if(empty($picType)){
            $picType = 'G';
        }

        //$spuPicList = CommonModel::getPicsBySkuArr($spuArr,$picType);

        $spuPicList = self::getOpenSysApi('datacenter.picture.getSpuAllSizePic',array('spu'=>json_encode($spuArr),'picType'=>$picType));
        //echo $spuPicList;

        return $spuPicList;
        /*if(!empty($spuPicList['errCode']) || empty($spuPicList['data'])){
			return false;
        }*/
        $picArr = array();//定义一个数组来存放$spuArr中对应spu及对应的值，K=>V
        foreach($spuArr as $value){
            if(!empty($value)){
                $picArr[$value] = $spuPicList['data'][$value]['artwork'][$value][0];
            }        
        }
        return $picArr;
    }

    public static function getOpenSysApi($method, $paArr, $idc='',$decode=true){
        include_once "../api/include/functions.php";
        if(empty($method) || empty($paArr) || !is_array($paArr)){   //参数不规范
            self::$errCode = 301;
            self::$errMsg = '参数信息不规范';
            return false;
        }else{
			$paramArr = array(
				'format' => 'json',
					 'v' => '1.0',
				'username'	 => C('OPEN_SYS_USER')
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
			$token = C('OPEN_SYS_TOKEN');
			$sign = createSign($paramArr, $token);
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
            //echo self::$token.'<br>';
            //echo $urls;
			//exit;
			//连接超时自动重试3次
			$cnt=0;
			while($cnt < 3 && ($result=@vita_get_url_content($urls))===FALSE) $cnt++;
			//print_r($result);
			//exit;
            if($decode){
              $data	= json_decode($result,true);  
            }else{
              $data = $result;  
            }            
			
			//var_dump($data,$result,"++___+++");exit;
			if($data){
				self::$errCode = 200;
        		self::$errMsg  = 'Success';
				return $data;
			}else{
				self::$errCode = "000";
        		self::$errMsg = "is empty!";
			}
		}
    }

    //获取买家在Ebay平台的历史购买记录
	public function act_getEbayShipOrderInfo(){
		$userid = !empty($_GET["buyeraccount"]) ? addslashes(trim($_GET["buyeraccount"])) : '' ;
		$selleraccount = !empty($_GET["selleraccount"]) ? addslashes(trim($_GET["selleraccount"])) : '' ;

		if(empty($userid)){
			$data['errCode'] = '5806';
			$data['errMsg'] = 'userid  is null.';
			exit(json_encode($data));
		}
		if(empty($selleraccount)){
			$data['errCode'] = '5806';
			$data['errMsg'] = 'selleraccount  is null.';
			exit(json_encode($data));
		}
        
		$owOrderMg    = new OwOrderManageModel();
		$selerInfo    = $owOrderMg->getSellerInfo($selleraccount);
		if (FALSE === $selerInfo) {                                                           //未找到销售账号信息
		    $data['errCode'] = '5806';
		    $data['errMsg']  = 'invalide seller ID';
		    exit(json_encode($data));
		}
		$sellerId     = $selerInfo['id'];
		$finalResult  = $owOrderMg->getBuyerHistory($userid, $sellerId);
		echo json_encode($finalResult);exit;
	}

	//获取买家的在Express平台的历史购买记录
	public function act_getExpressShipOrderInfo(){
		
		$recordnumber = !empty($_GET["recordnumber"]) ? addslashes(trim($_GET["recordnumber"])) : '' ;
		$selleraccount = !empty($_GET["selleraccount"]) ? addslashes(trim($_GET["selleraccount"])) : '' ;

		//$selleraccount='sunwebzone';
		//$recordnumber='537995';

		if(empty($recordnumber)){
			$data['errCode'] = '5806';
			$data['errMsg'] = 'recordnumber  is null.';
			exit(json_encode($data));
		}
		if(empty($selleraccount)){
			$data['errCode'] = '5806';
			$data['errMsg'] = 'selleraccount  is null.';
			exit(json_encode($data));
		}

		$field   	= "a.*";
		$condition 	= "ORDER BY a.id DESC LIMIT 50";
		$orders   	= OrderInfoModel::getExpressOrderInfo($selleraccount, $recordnumber, $field, $condition = '');		
		
		if(empty($orders)){
			$data['errCode'] = '5806';
			$data['errMsg'] = 'No data or get a order information error';
			exit(json_encode($data));
		}

		foreach($orders AS &$order){

			if($order['orderStatus']==2){
				$order['catename'] = '已发货';
			}else{
				$select = 'statusName';
				$where  =  " WHERE groupId = '{$order['orderStatus']}' AND statusCode = '{$order['orderType']}' LIMIT 1 ";
				$cates = StatusMenuModel::getStatusMenuList($select, $where);
				$order['catename'] = $cates[0]['statusName'];
			} 			
			$order['orderdetail'] = OrderInfoModel::getShipOrderDetailByOrderId($order['id']);
		}	
		$data['data'] = $orders;
		exit(json_encode($data));
	}
	
	//申请仓库系统问题同步API
	public function act_ApplicationExceptionAPI(){
		$omOrderId = $_GET['omOrderId'];
		$content = $_GET['content'];
		$tableName = "om_unshipped_order";
		$storeId = 1;
		if(!$omOrderId || !$content){
			self::$errCode = '5806';
			self::$errMsg  = 'param is error';
			return array();
		}
		$where = " WHERE id = ".$omOrderId." AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderStatus'=>C('STATEINTERCEPTSHIP'),'orderType'=>C('STATEPENDING_APPEXC'));
		if(!OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
			self::$errCode = '5807';
			self::$errMsg  = 'update error';
			return false;
		}
		$insertOrderNoteDada = array('omOrderId'=>$omOrderId,'content'=>$content,'userId'=>0,'createdTime'=>time());
		if($insertOrderNoteids = OrderAddModel::insertOrderNotesRow($insertOrderNoteDada)){
			self::$errCode = '5808';
			self::$errMsg  = 'insert Note error';
			return false;	
		}
		self::$errCode = '200';
		self::$errMsg  = 'sync success';
		return true;
	}
	
	//申请仓库系统问题同步
	public function act_ApplicationException($omOrderId,$content){
		$tableName = "om_unshipped_order";
		$storeId = 1;
		if(!$omOrderId || !$content){
			self::$errCode = '5806';
			self::$errMsg  = 'param is error';
			return array();
		}
		$where = " WHERE id = ".$omOrderId." AND orderStatus = ".C('STATESHIPPED');
		$returnStatus0 = array('orderStatus'=>C('STATEINTERCEPTSHIP'),'orderType'=>C('STATEPENDING_APPEXC'));
		if(!OrderindexModel::updateOrder($tableName,$returnStatus0,$where)){
			self::$errCode = '5807';
			self::$errMsg  = 'update error';
			return false;
		}
		$insertOrderNoteDada = array('omOrderId'=>$omOrderId,'content'=>$content,'userId'=>0,'createdTime'=>time());
		if($insertOrderNoteids = OrderAddModel::insertOrderNotesRow($insertOrderNoteDada)){
			self::$errCode = '5808';
			self::$errMsg  = 'insert Note error';
			return false;	
		}
		self::$errCode = '200';
		self::$errMsg  = 'sync success';
		return true;
	}
	
}
?>