<?php
/*
 *提供账号和平台列表接口
 *add by linzhengxiang @ 20140524
 */
class AccountModel extends CommonModel{	

	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 根据平台Id和accountid获取账号信息 分页显示
	 * @param array $data   
	 * @return array 
	 * @author yxd
	 */
	public function getAccountList($data,$page=1, $perpage=50, $sort='ORDER BY id DESC'){
		$condition    =	$this->getAccountSql($data);
		return $this->sql("SELECT * FROM ".$this->getTableName()." WHERE  $condition")->sort($sort)->page($page)->perpage($perpage)->select(array('cache', 'mysql'));
	}
	/**
	 * 获取账户Id和name
	 * @platform id
	 * @return array
	 * @author yxd
	 */
	public function getAccountAll($pid=""){
		$where    = "";
		if($pid){
			$where    .= " AND platformId = $pid";
		}
		return $this->sql("SELECT id,account FROM ".$this->getTableName()." WHERE is_delete=0 $where ")->limit('*')->select(array('cache','mysql'),600);
	}
	/**
	 * 通过平台Id获取账户Id和name 
	 * @return array
	 * @author yxd
	 */
	public function getAccountByPlatformId($platformId){
		return $this->sql("SELECT id,account FROM ".$this->getTableName()." WHERE platformId=$platformId and is_delete=0")->limit('*')->select(array('cache','mysql'),600);
	}
	
	/**
	 * 通过平台Id获取name列表
	 * @return array
	 * @author yxd
	 */
	public function getAccountNameByPlatformId($platformId){
		$accounts = array();
		$accountlists = $this->getAccountByPlatformId($platformId);
		foreach ($accountlists AS $accountlist){
			$accounts[$accountlist['id']] = $accountlist['account'];
		}
		return $accounts;
	}
	
	/**
	 * 通过平台Id获取账号ID列表
	 * @return array
	 * @author herman.xi @20140616
	 */
	public function getAccountIdByPlatformId($platformId){
		$accounts = array();
		$accountlists = $this->getAccountByPlatformId($platformId);
		foreach ($accountlists AS $accountlist){
			array_push($accounts, $accountlist['id']);
		}
		return $accounts;
	}
	
	/**
	 * 根据账号id获取平台id
	 * @param int $id 账号id
	 * @return string
	 * @author yxd
	 */
	public function getPlatformid($accountid){
		return $this->sql("SELECT platformId FROM ".$this->getTableName()." WHERE id=$accountid and is_delete=0 ")->limit(1)->select(array('cache','mysql'),600);
	}
	
	/**
	 * 根据账号获取平台id
	 * @param int $id 账号id
	 * @return string
	 * @author yxd
	 */
	public function getPlatformidByAccount($account){
		
		$pltformid    = $this->sql("SELECT platformId FROM ".$this->getTableName()." WHERE account='$account' and is_delete=0 ")->limit(1)->select(array('cache','mysql'),600);
		return isset($pltformid[0]['platformId']) ? $pltformid[0]['platformId'] :false;
	}
	/**
	 * 实例化父类，完成数据库插入判断
	 * @see commonModel::checkIsExists()
	 * @param string $data
	 * @return bool
	 * @author lzx
	 */
	public function checkIsExists($data){
		$checkdata = $this->sql("SELECT * FROM ".$this->getTableName()." WHERE account='{$data['account']}' AND is_delete=0")->select();
		if (!empty($checkdata)){
			self::$errMsg[10017] = get_promptmsg(10017, $data['account']);
			return true;
		}
		return false;
	}
	
	/**
	 * 根据账号id获取，扩展表后缀
	 * @param int $accoutid
	 * @return string
	 * @author lzx
	 */
	public function getSuffixByAccout($accoutid){
		$accoutid = intval($accoutid);
		$sql = "SELECT a.suffix FROM ".C('DB_PREFIX')."platform AS a LEFT JOIN ".C('DB_PREFIX')."account AS b ON a.id=b.platformId WHERE b.id={$accoutid}";
		$result = $this->sql($sql)->limit(1)->select(array('cache', 'mysql'));
		return isset($result[0]['suffix']) ? $result[0]['suffix'] : false;
	}
	
	/**
	 * 根据账号id获取，扩展表后缀
	 * @param int $accoutid
	 * @return string
	 * @author lzx
	 */
	public function getShippingByAccout($accoutid){
		$accoutid = intval($accoutid);
		$sql = "SELECT a.shipping FROM ".C('DB_PREFIX')."platform AS a LEFT JOIN ".C('DB_PREFIX')."account AS b ON a.id=b.platformId WHERE b.id={$accoutid}";
		$result = $this->sql($sql)->limit(1)->select(array('cache', 'mysql'));
		return isset($result[0]['shipping']) ? $result[0]['shipping'] : false;
	}
	
	/**
	 * 获取错误数量
	 * @return int
	 * @author yxd
	 */
	public function getAccountCount($data){
		 return $this->sql($this->replaceSql2Count("SELECT * FROM ".C('DB_PREFIX')."account WHERE ".$this->getAccountSql($data)))->count();
	}
	
	/**
	 * 根据查询条件组装查询SQL语句
	 * @prama array
	 * @return string
	 * @author yxd
	 */
	private function getAccountSql($data){
		$where                = implode(" AND ", array2where($data));
		return $where ;
	}
	
	public function getAccountIdByName($account){
		$accountInfo = $this->sql("SELECT id FROM ".C('DB_PREFIX')."account WHERE account='{$account}'")->select();
		if($accountInfo){
			return $accountInfo[0]['id'];
		}
		return false;
	}
}
?>	
	