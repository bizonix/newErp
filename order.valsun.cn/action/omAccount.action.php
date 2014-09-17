<?php
/*
 * 提供账号和平台列表接口
 * ADD BY heminghua
 */
class OmAccountAct{
	static $errCode = 0;
	static $errMsg = "";

	/*
	 * 提供账号和平台列表接口
	 */
	function act_getAccountList() { 
		global $memc_obj;
		$cacheName = md5("om_system_account");
		
		$list = $memc_obj->get_extral($cacheName);
		
		if($list){
			return json_encode($list);
		}else{
			
			$accountList = omAccountModel::accountList();
			if(!$accountList){
				self::$errCode = 101;
				self::$errMsg = "没取到账号列表！";
				return ;
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
	 * 提供账号和平台列表接口
	 */
	function act_getAccountListByPid($PlatformId) {
		global $memc_obj;
		$cacheName = md5("om_system_account_".$PlatformId);
		
		$list = $memc_obj->get_extral($cacheName);
		
		if($list){
			return json_encode($list);
		}else{
			
			$accountList = omAccountModel::accountListByPid($PlatformId);
			if(!$accountList){
				self::$errCode = 101;
				self::$errMsg = "没取到账号列表！";
				return ;
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
	function act_getAccountListByPower() {
		$accountList = omAccountModel::accountListPower();
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