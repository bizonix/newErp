<?php 
/**
 * 黑名单管理
 *
 * @add by yxd ,date 2014/06/10
 */
class BlacklistAct extends CheckAct{
	/**
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 分页获取
	 * return array
	 */
	public function act_getBlackList(){
				//var_dump(get_skudailystatus('SV002401_R'));
		//var_dump(M('InterfacePc')->getSkuinfo('001'));
		//var_dump(M('InterfacePc')->getSkuinfo('CB001260_3'));
		//var_dump(M('InterfaceWh')->getSkuStores(array('001', 'CB001260_3','OS000123')));exit;
		//var_dump(M('InterfacePc')->getSkuinfo('CB001260_3'));
		//var_dump(M('InterfacePc')->getMaterList());
		//var_dump(M('InterfacePc')->getSkuinfo('TK_CB49'));
    	//var_dump(get_orderskulist('CB001260_3'));
		//var_dump(get_orderskulist('TK_CB49'));
		//var_dump(get_orderskulist('001'));
		//var_dump(M('InterfacePower')->userLogin('linzhengxiang@sailvan.com', 'abc-1231'));
		//var_dump(M('Order')->getOrderList($conditions, $this->page, $this->perpage, $sort));
		//var_dump(M('InterfacePower')->getUserInfo(10));
		//var_dump(M('InterfacePower')->userLogin('linzhengxiang@sailvan.com', 'abc-123'));
		//var_dump(M('InterfacePower')->getUserPower('5fe15e41a36a5f9e1f683656f9d88fdc'));
		//var_dump(M('StatusMenu')->getStatusMenuByUserId(get_userid()));
		//fortest
		$calcshipping = F('CalcOrderShipping');
		$orders = A('Order')->act_getFullUnshippedOrderById(array(7180397));
		$calcshipping->setOrder($orders[7180397]);
		$calcshipping->calcOrderWeight();
		//fortest
		return M('Blacklist')->getBlackList($this->act_getBlacklistCondition(),$this->page, $this->perpage);
	}
	public function act_getaccountOptionByPid(){
		$pid    = isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : "";
		return A('Account')->act_getAccountAll($pid);
	}
	/**
	 * 通过黑名单id获取黑名单信息
	 * param id
	 * return array
	 */
	public function act_getBlackListByid(){
		$id           = isset($_GET['id']) ? $_GET['id'] : '';
		$blacklist    = M('Blacklist')->getBlackListByid($id);
		if(is_array($blacklist) && count($blacklist)==1){
			$blacklist    = $blacklist[0];
		}else{
			$blacklist    = false;
		}
		return $blacklist;
	}
	/**
	 * 获取黑名单数量
	 * return int
	 */
	public function act_getBlacklistCount(){
		return M('Blacklist')->getBlacklistCount($this->act_getBlacklistCondition());
	}
	/**
	 * 通过平台Id获取账号
	 * return array
	 */
	public function act_getAccountByPId(){
		$platformId     = isset($_POST['platformId']) ? $_POST['platformId'] :'' ;
		if(!$platformId){
			self::$errMsg[10046]    = get_promptmsg(10046);
		}
		$account    = M('Account')->getAccountAll($platformId);
		if(!empty($account)){
			self::$errMsg[200]      = get_promptmsg(10047); 
		}else{
			self::$errMsg[204]      = get_promptmsg(10048);
		}
		return $account;
	}
	
	/**
	 * 根据 平台名称，用户信息
	 * @param platformUsername,username,usermail,street,phone,account
	 * retrun boolean
	 * 
	 */
	public function act_isExitInBlacklist($data){
		$cdata     = array();
		$where =' (1=2 ';
		if(!empty($data['phone'])){
			$where .= " OR phone='".$data['phone']."'  ";
		}
		if(!empty($data['address1'])){
			$where .= " OR street='".$data['address1']."'  ";
		}
		if(!empty($data['usermail'])){
			$where .= " OR usermail='".$data['usermail']."'  ";
		}
		if(!empty($data['platformId']) && !empty($data['platformUsername'])){
			$where .= " OR (platformId='".$data['platformId']."' AND platformUsername='".$data['platformUsername']."')  ";
		}
		$where = $where.") AND is_delete=0";
		
		return  M('Blacklist')->isExitInBlacklist($where);
	}
	/**
	 * 插入黑名单信息
	 * return boolean
	 */
	public function act_insert(){
		$data                        = array();
		$data['platformUsername']    = isset($_POST['platformUsername']) ?$_POST['platformUsername']:"";
		$data['username']            = isset($_POST['username']) ?$_POST['username']:"";
		$data['usermail']            = isset($_POST['usermail']) ?$_POST['usermail']:"";
		$data['street']              = isset($_POST['street']) ?$_POST['street']:"";
		$data['phone']               = isset($_POST['phone']) ?$_POST['phone']:"";
		$data['platformId']          = isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : '';
		$data['addTime']	         = time();
		$data['addUser']             = get_userid();
		$data['is_delete']           = 0;
		$postAccount = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
		foreach($postAccount as $k => $v){
			$data['account']    = $v;
			if(!M('Blacklist')->insertData($data)){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 删除信息
	 * @param id
	 * return boolean
	 */
	public function act_delete(){
		$id    = isset($_GET['id']) ?$_GET['id']:"";
		return M('Blacklist')->deleteData($id);
	}
	/**
	 * 更新黑名单信息
	 * retrun boolean
	 */
	public function act_update(){
		$id                          = isset($_POST['id']) ?$_POST['id']:"";
		$data                        = array();
		$data['platformUsername']    = isset($_POST['platformUsername']) ?$_POST['platformUsername']:"";
		$data['username']            = isset($_POST['username']) ?$_POST['username']:"";
		$data['usermail']            = isset($_POST['usermail']) ?$_POST['usermail']:"";
		$data['street']              = isset($_POST['street']) ?$_POST['street']:"";
		$data['phone']               = isset($_POST['phone']) ?$_POST['phone']:"";
		$data['platformId']          = isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : '';
		$data['account']             = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
		//$data['addTime']	         = time();
		//$data['addUser']             = '1'; 
		return M('Blacklist')->updateData($id,$data);
	}
	private function act_getBlacklistCondition(){
		$data['platformId']          = isset($_GET['platformId']) ? $_GET['platformId'] : '';
		$data['account']             = isset($_GET['accountId']) ? $_GET['accountId'] : '';
		$data['platformUsername']    = isset($_GET['platformUsername']) ? $_GET['platformUsername'] : '';
		foreach($data as $key=>$value){
			if($value){
				$data["$key"]    = array('$e'=>"$value");
			}
		}
		$data['is_delete']       = array('$e'=>0);
		return $data;
	} 
}
?>
