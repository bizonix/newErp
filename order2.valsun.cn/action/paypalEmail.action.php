<?php
/**
 * paypal付款邮箱管理
 * @author yxd
 */
class PaypalEmailAct extends CheckAct{
	
	
	public function __construct(){
		parent::__construct();
	}

	
	/**
	 * 根据账户名称和平台id获取账户列表
	 * @param int $PaypalEmailId 账户编号  int $platformId平台编号
	 * @return Array
	 * @author yxd
	 */
	public function act_getPaypalEmailList(){
		return M('PaypalEmail')->getPaypalEmailList($this->act_getPaypalEmailCondition(),$this->page,$this->perpage, $sort);
	}
	/**
	 * 获取平台账户Id和name
	 * @param  平台id
	 * @return array
	 * @author yxd
	 */
	public function act_getPaypalEmailAll($pid=""){
		return M('PaypalEmail')->getPaypalEmailAll($pid="");
	}
	
	/**
	 * 获取错误数量
	 * @return int
	 * @author yxd
	 */
	public function act_getPaypalEmailCount(){
		return M('PaypalEmail')->getPaypalEmailCount($this->act_getPaypalEmailCondition());
	}
	
	/**
	 * 通过账号id获取付款邮箱
	 * @return array 
	 * @author yxd
	 */
	public function act_getPaypalEmailByAccountId($id){
		$data          = M('PaypalEmail')->getPaypalEmailByAccountId($id);
		$returndata    = array();
		foreach($data as $value){
			$returndata[]    = $value['email'];
		}
		return $returndata;
	}
	/**
	 * 根据id获取paypalEmial信息
	 * @param id
	 * @return array
	 * @author yxd
	 */
	public function act_getPaypalEmialById(){
		$id    = isset($_GET['id'])?$_GET['id']:0;
		return  M('PaypalEmail')->getPaypalEmailById($id);
	}
	
	public function act_insert(){
		$emails      = isset($_POST['emails'])?trim($_POST['emails']):"";
		$accounts 	 = isset($_POST['accounts'])?$_POST['accounts']:"";
		$emails      = explode(",",$emails);
		$emails      = array_filter($emails);
		$userId      = get_userid();
		foreach($emails as $key=>$value){
			if(!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/",$value)){
				self::$errMsg[10061] = get_promptmsg(10061,"邮箱格式有误导致");
				return false;
			}
			foreach($accounts as $account){
				$data['email']        = $value;
				$data['accountId']    = $account;
				$data['createTime']   = time();
				$data['creatorId']    = get_userid();
				$data['modefyTime']   = time();
				$msg = M('PaypalEmail')->insertData($data);
				if(!$msg){
					self::$errMsg[10061]    = get_promptmsg(10061,"插入数据库出错导致");
					return false;
				}
			}
		}
		self::$errMsg[200]    = get_promptmsg(200,"添加paypalEmail");;
		return true;
	}
	
	
	public function act_delete(){
		$id    = isset($_POST['id']) ? trim($_POST['id']) : '';
		if(M('PaypalEmail')->deleteData($id)){
			self::$errMsg[200]    = get_promptmsg(200,"删除");
			return true;
		}else{
			self::$errMsg[10061]    = get_promptmsg(10061,"删除");
			return false;
		}
	}
	
	public function act_update(){
		$id                    = isset($_GET['id']) ? trim($_GET['id']) : '';
		$data['accountId']     = isset($_GET['account'])?trim($_GET['account']):'';
		$data['email']         = isset($_GET['email'])?trim($_GET['email']):'';
		$data['status']        = isset($_GET['status'])?trim($_GET['status']):'';
		foreach($data as $key=>$value){
			if(!isset($value)){
				unset($data["$key"]);
			}
		}
		return M('PaypalEmail')->updateData($id,$data);
	}
	
	
	/**
	 * 组装查询条件
	 * */
	private function act_getPaypalEmailCondition(){
		$data["accountId"]    = isset($_REQUEST['accountId']) ? $_REQUEST['accountId'] :"";
		if($data['accountId']){
			$data['accountId']    = array('$e'=>$data['accountId']);
		}
		$data['is_delete']     = array('$e'=>0);
		return $data;
	}
}
?>