<?php
/*
 * 提供账号和平台列表接口
 * ADD BY heminghua
 * 修改：linzhengxiang @ 20140523
 */
class AccountAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}

	/**
	 * 根据平台id获取平台名称
	 * @param int $id 平台编号
	 * @return string
	 * @author lzx
	 */
	public function act_getAccountById($id){
	    $data    = M('Account')->getAccountById($id);
		return $data;
	}
	/**
	 * 根据账号id获取平台名称
	 * @param int $id 账号id
	 * @return string
	 * @author yxd
	 */
	public function act_getPlatformidByAccountid($id){
		$data    = M('Account')->getPlatformid($id);
		return $data;
	}
	/**
	 * 根据账户名称和平台id获取账户列表
	 * @param int $AccountId 账户编号  int $platformId平台编号
	 * @return Array
	 * @author yxd
	 */
	public function act_getAccountList(){
		return M('Account')->getAccountList($this->act_getAccountCondition(),$this->page,$this->perpage, $sort);
	}
	/**
	 * 获取平台账户Id和name
	 * @param  平台id
	 * @return array
	 * @author yxd
	 */
	public function act_getAccountAll($pid=""){
		return M('Account')->getAccountAll($pid="");
	}
	
	/**
	 * 获取错误数量
	 * @return int
	 * @author yxd
	 */
	public function act_getAccountCount(){
		return M('Account')->getAccountCount($this->act_getAccountCondition());
	}
	
	public function act_insert(){
		$data['account']       = isset($_POST['account'])?trim($_POST['account']):''; 
		$data['platformId']    = isset($_POST['platformId'])?trim($_POST['platformId']):'';
		$data['appname']       = isset($_POST['appname'])?trim($_POST['appname']):'';
		$data['email']         = isset($_POST['email'])?trim($_POST['email']):'';
		$data['suffix']        = isset($_POST['suffix'])?trim($_POST['suffix']):'';
		$data['charger']       = isset($_POST['charger'])?trim($_POST['charger']):'';
		$data['addTime']       = time();
		$data['is_delete']     = 0;
		$data['addUser']       = get_userid();
		return M('Account')->insertData($data);
	}
	
	
	public function act_delete(){
		$id    = isset($_GET['id']) ? trim($_GET['id']) : '';
		return M('account')->deleteData($id);
	}
	
	public function act_update(){
		$id             	   = isset($_POST['id']) ? trim($_POST['id']) : '';
		$data['account']       = isset($_POST['account'])?trim($_POST['account']):'';
		$data['platformId']    = isset($_POST['platformId'])?trim($_POST['platformId']):'';
		$data['appname']       = isset($_POST['appname'])?trim($_POST['appname']):'';
		$data['email']         = isset($_POST['email'])?trim($_POST['email']):'';
		$data['suffix']        = isset($_POST['suffix'])?trim($_POST['suffix']):'';
		$data['charger']       = isset($_POST['charger'])?trim($_POST['charger']):'';
		$data['addTime']       = time();
		$data['is_delete']     = 0;
		$data['addUser']       = get_userid();
		return M('Account')->updateData($id,$data);
	}
	
	
	/**
	 * 组装查询条件
	 * */
	private function act_getAccountCondition(){
		$data["id"]            = isset($_GET['accountId']) ? $_GET['accountId'] :"";
		$data["platformId"]    = isset($_GET['platformId']) ? $_GET['platformId'] :"";
		if($data['id']){
			$data['id']            = array('$e'=>$data['id']);
		}
		if($data['platformId']){
			$data["platformId"]    = array('$e'=>$data['platformId']);
		}
		$data['is_delete']     = array('$e'=>0);
		return $data;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	 * 提供账号和平台列表接口
	 */
	function act_getPlatformAccountListAPI() {
		$accountList = OmAccountModel::accountList();
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return array();
		}else{
			$accountArr = array();
			foreach($accountList as $value){
				$accountArr[$value['platformId']]['name'] = $value['platform'];
				$accountArr[$value['platformId']]['account'][$value['id']] = $value['account'];
			}
		}
		return $accountArr;
	}
	
	/*
	 * 提供ebay账号和平台列表接口
	 */
	function act_getAccountListEbay() {
		$PlatformId = 1;
		return self::act_getAccountListByPid($PlatformId);
	}

	/*
	 * 提供Aliexpress账号和平台列表接口
	 */
	function act_getAccountListAliexpress() {
		$PlatformId = 2;
		return self::act_getAccountListByPid($PlatformId);
	}


	/*
	 * 提供Dhgate账号和平台列表接口
	 */
	function act_getAccountListDhgate() {
		$PlatformId = 4;
		return self::act_getAccountListByPid($PlatformId);
	}
	
	
	/*
	 * 提供账号和平台列表接口
	 */
	function act_getPlatformList() { 
		global $memc_obj;
		$id = !empty($_GET['id']) ? $_GET['id'] : '';
		$cacheName = md5("om_system_platform".$id);
		$list = $memc_obj->get_extral($cacheName);
		if($list){
			return json_encode($list);
		}else{
			$accountList = omAccountModel::platformList($id);
			if(!$accountList){
				self::$errCode = 101;
				self::$errMsg = "没取到账号列表！";
				return false;
			}else{
				$isok = $memc_obj->set_extral($cacheName, $accountList);
				if(!$isok){
					self::$errCode = 102;
					self::$errMsg = 'memcache缓存账号信息出错!';
					return json_encode($accountList);
				}
				return json_encode($accountList);
			}
		}
	}
	
	/*
	 * 提供平台接口，连接权限
	 */
	function act_getPlatformListByPower() {
		$platformList = omAccountModel::platformListPower();
		if(!$platformList){
			self::$errCode = 400;
			self::$errMsg = "没取到平台列表！";
			return $platformList;
		}else{
			self::$errCode = 200;
			self::$errMsg = "获取到平台列表！";
			return $platformList;
		}
	}
	
	/*
	 * 提供账号列表，连接权限
	 */
	function act_getAccountListByPower($userid) {
		$accountList = omAccountModel::accountListPower($userid);
		return $accountList;
	}
	
	/*
	 * 列出平台下账号列表，连接权限
	 */
	function act_getAccountListByPlatform() {
		$Platform = $this->act_platformListPowerById();
		//var_dump($Platform);
		$accountList = omAccountModel::accountListPlatform($Platform);
		if(!$accountList){
			self::$errCode = 400;
			self::$errMsg = "没取到平台列表！";
			return $accountList;
		}else{
			self::$errCode = 200;
			self::$errMsg = "获取到平台列表！";
			return $accountList;
		}
	}
	
	/*
	 * 列出平台下账号列表，连接权限
	 */
	function act_platformListPowerById() {
		$accountList = omAccountModel::platformListPowerById($Platform);
		if(!$accountList){
			self::$errCode = 400;
			self::$errMsg = "没取到平台列表！";
			return $accountList;
		}else{
			self::$errCode = 200;
			self::$errMsg = "获取到平台列表！";
			return $accountList;
		}
	}
	
	function act_getOpenAccountList() {
		$res = userCacheModel::getOpenSysApi("om.omAccount",array("1100"));
		self::$errCode = userCacheModel::$errCode;
		self::$errMsg = userCacheModel::$errMsg;
		$res = json_decode($res);
		return $res;
	}
	
	function act_getEbayAccountList() {
		$Platform = array('1');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}
	
	function act_getB2BAccountList() {
		//$platform 	 = "'aliexpress','出口通','B2B外单'";	
		//$platform 	 = "'aliexpress','出口通'";	
		$Platform = array('2','3','4','6','9');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}

	function act_getNeweggAccountList() {
		$Platform = array('15');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}

	function act_ebayaccountAllList() {
		$accountList = omAccountModel::ebayaccountAllList();
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}
	
	function act_amazonaccountAllList() {
		$Platform = array('11');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}
	
	function act_getINNERAccountList() {
		$Platform = array('16');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}
	
	function act_dresslinkaccountAllList() {
		$Platform = array('8','10');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}

	function act_getAllAccountList() {		
		$accountList = omAccountModel::accountListAcc();
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}

	function act_getAliexpressAccountList() {
		$Platform = array('2','16');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}

	function act_getDhgateAccountList() {
		$Platform = array('4');
		$accountList = omAccountModel::getAccountByPlatform($Platform);
		if(!$accountList){
			self::$errCode = 101;
			self::$errMsg = "没取到账号列表！";
			return ;
		}		
		return $accountList;
	}
	
	//根据平台id获取后缀名称
	function act_getPlatformSuffixById($platformId) {
		$res = omAccountModel::getPlatformSuffixById($platformId);
		self::$errCode = omAccountModel::$errCode;
		self::$errMsg = omAccountModel::$errMsg;
		return $res;
	}
	
	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_getUserCompenseList($uid, $type=1) { //
		global $memc_obj; //调用memcache获取sku信息
		//$addUser = $_SESSION['sysUserId'];
		
		$UserCompenseInfo = OmAccountModel :: getUserCompenseInfo($uid, $type);
		
		self :: $errCode = OmAccountModel :: $errCode;
		self :: $errMsg = OmAccountModel :: $errMsg;
		return $UserCompenseInfo;
	}
	
	/*
	 * 申请跟踪号,可以批量申请
	 */
	function act_addUserCompense($post) {
		global $memc_obj; //调用memcache获取sku信息
		$addUser = $_SESSION['sysUserId'];
		$data = array();
		//var_dump($post); exit;
		$action = $post['action'];
		$uid = $post['uid'];
		switch($action){
			case 'accountpower':
				$visible_platform_account = array();
				/*echo "<pre>";
				var_dump($post); exit;*/
				foreach($post as $key => $valueArray){
					if(strpos($key, 'checkboxes_account_')!==false){
						$strarr=explode('checkboxes_account_',$key);
						$pid=$strarr[1];
						$visible_platform_account[$pid] = $valueArray;
					}
				}
				/*if(!$post['checkboxes_platform']){
					$post['checkboxes_platform'] = array();
				}else{
					foreach($post['checkboxes_platform'] as $pid){
						if($post['checkboxes_account_'.$pid]){
							$visible_platform_account[$pid] = $post['checkboxes_account_'.$pid];
						}
					}	
				}*/
				/*if(!$post['checkboxes_account']){
					$post['checkboxes_account'] = array();
				}*/
				/*echo "<pre>";
				var_dump($uid); echo "<br>";
				var_dump($visible_platform_account); exit;*/
				$data['visible_platform_account'] = json_encode($visible_platform_account);
				/*$data['visible_platform'] = join(',', $post['checkboxes_platform']);
				$data['visible_account'] = join(',', $post['checkboxes_account']);*/
				$rtn = OmAccountModel :: addUserCompense($uid,$data);
				break;
			default:
				
		}
		self :: $errCode = OmAccountModel :: $errCode;
		self :: $errMsg = OmAccountModel :: $errMsg;
		return $rtn;
	}
	
	//获取所有的账号信息
	function act_accountAllListAPI() {
		$res = omAccountModel::accountAllList();
		self::$errCode = omAccountModel::$errCode;
		self::$errMsg = omAccountModel::$errMsg;
		return $res;
	}
	
	//获取所有的账号信息BY id
	function act_accountAllListById() {
		$res = omAccountModel::accountAllListById();
		self::$errCode = omAccountModel::$errCode;
		self::$errMsg = omAccountModel::$errMsg;
		return $res;
	}

	//获取订单编辑的选项
	function act_getEditOrderOptions() {

		$options = array(1 => '平台','账号','买家ID','订单号','下单时间','付款时间','产品总金额','物流费用','订单金额','Transaction ID','币种','估算重量','买家选择发货物流','跟踪号','Full name','Street1','Street2','City','State','Country	','Postcode','Tel1','Tel2','Tel3','买家邮箱1','买家邮箱2','买家邮箱3','订单备注');
		return $options;

		/*
		$res = omAccountModel::accountAllListById();
		self::$errCode = omAccountModel::$errCode;
		self::$errMsg = omAccountModel::$errMsg;
		return $res;
		*/
		
	}
}
?>	