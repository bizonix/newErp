<?php
/**
*类名：Action
*功能：操作(动作)权限表管理
*版本：2013-05-10
*作者：冯赛明
*
*/
class fetchEbayOrderModel{
	public static $dbConn;
	private static $_instance;
	static $errCode = '0';
	static $errMsg  = "";

	public function __construct(){

	}	

	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	public function count(){
		$this->is_count = true;
		return $this;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	public function addOrder($account){

		if(!defined('WEB_PATH')){
			define("WEB_PATH","/data/web/order.valsun.cn/");
		}
		require_once WEB_PATH."crontab/scripts.comm.php";
		require_once WEB_PATH_CONF_SCRIPTS."script.ebay.config.php";
		require_once WEB_PATH_LIB_SDK_EBAY."GetCertainOrder.php";
		require_once WEB_PATH_LIB_SCRIPTS_EBAY."ebay_order_cron_func.php";
		
		$rmq_config	=	C("RMQ_CONFIG");
		$rabbitMQClass= new RabbitMQClass($rmq_config['fetchOrder'][1],$rmq_config['fetchOrder'][2],$rmq_config['fetchOrder'][4],$rmq_config['fetchOrder'][0]);//队列对象
		$omAvailableAct = new OmAvailableAct();
		$where = 'WHERE is_delete=0 ';
		$where .= 'AND platformId in(1,5) ';
		$GLOBAL_EBAY_ACCOUNT = $omAvailableAct->act_getTNameList2arrById('om_account', 'id', 'account', $where);
		
		$FLIP_GLOBAL_EBAY_ACCOUNT = array_flip($GLOBAL_EBAY_ACCOUNT);
		
		if(!preg_match('#^[\da-zA-Z]+$#i',$account)){
			$message .= "<font color='red'>Invalid ebay account: $account!</font><br>";
			self::$errCode = 101;
			self::$errMsg = $mesage;
			return false;
		}
		if(!in_array($account,$GLOBAL_EBAY_ACCOUNT)){
			//exit("$account is not support now !\n");
			$message .= "<font color='red'>$account is not support now !</font><br>";
			self::$errCode = 102;
			self::$errMsg = $mesage;
			return false;
		}
		
		//预先判断ebaytoken文件
		$__token_file = WEB_PATH_CONF_SCRIPTS_KEYS_EBAY.'keys_'.$account.'.php';
		if(!file_exists($__token_file)){
			//exit($__token_file." does not exists!!!");
			$message .= "<font color='red'>{$__token_file} does not exists!!!</font><br>";
			self::$errCode = 103;
			self::$errMsg = $mesage;
			return false;
		}
		
		$express_delivery = array();
		$express_delivery_value = array();
		$no_express_delivery = array();
		$no_express_delivery_value = array();
		$express_delivery_arr = CommonModel::getTransCarrierInfo(1);
		foreach($express_delivery_arr['data'] as $value){
			$express_delivery_value[$value['id']] = $value['carrierNameCn'];
		}
		$express_delivery = array_keys($express_delivery_value);
		//var_dump($express_delivery);
		$no_express_delivery_arr = CommonModel::getTransCarrierInfo();
		foreach($no_express_delivery_arr['data'] as $value){
			$no_express_delivery_value[$value['id']] = $value['carrierNameCn'];
		}
		$no_express_delivery = array_keys($no_express_delivery_value);
		//var_dump($no_express_delivery); exit;
		
		#########全局变量设置########	
		date_default_timezone_set('Asia/Chongqing');      
		$detailLevel = 0;
		$Sordersn	= "eBay";
		
		$mctime		= time();      	
		$cc			= $mctime;
		$nowtime	= date("Y-m-d H:i:s",$cc);
		$nowd		= date("Y-m-d",$cc);
		#################以下账号用于测试#############	
		//$account= $__ebayaccount;	
		#############类或API 实例化##############
		$api_gco=new GetCertainOrderAPI($account);
		//$oa	=new OrderAction();
		//程序计时器
		$time_start=$cc;
		//echo "\n=====[".date('Y-m-d H:i:s',$time_start)."] 系统【开始】抓取账号【 $account 】订单 ====>\n\n";
		
		$message = $api_gco->GetCertainOrder($account);//监听获取队列信息
		if($message===true){
			self::$errCode = 200;
			self::$errMsg = "成功抓取订单！";
			return true;
		}elseif($message===false){
			self::$errCode = 104;
			self::$errMsg = "抓取订单失败！";
			return false;
		}else{
			self::$errCode = 105;
			self::$errMsg = $message;
			return ;
		}
		//return $message;
	}
	public function checkEbayOrder($orderid, $ebay_recordnumber, $accountId){
		self::initDB();
		$orderid_p1='#^\d{12}$#i';
		$orderid_p2='#^\d{12}\-\d{12,14}$#i';
		$orderid_p3='#^\d{12}\-0$#i';
		if(	(!preg_match($orderid_p1,$orderid)) &&(!preg_match($orderid_p2,$orderid)) &&(!preg_match($orderid_p3,$orderid))  ){
			return '001';//'WrongOrderID';
		}
		$sql= "SELECT * FROM om_order_ids where orderid='{$orderid}' ";
		$query = self::$dbConn->query($sql);
		$row=self::$dbConn->fetch_array($query);
		/*$query=mysql_query($sql);
		$row=mysql_num_rows($query);*/
		if($row==0){
			//$sql="select ebay_account from ebay_orderdetail where recordnumber='{$ebay_recordnumber}' AND ebay_account='{$accountId}'";
			/*$sql = "SELECT * FROM om_unshipped_order_detail where ";
			$res=$dbcon->execute($sql);
			$number=$dbcon->num_rows($res);
			$query=mysql_query($sql);
			$number=mysql_num_rows($query);
			if ($number==0){
				return '002';//'NoThisOrder';
			}else if($number>1){
				return '003';//chongfu
			}else if($number==1){
				return '100';//'HasThisOrder';
			}*/
			return '002';//'NoThisOrder';
		}else if($row>1){			
			return '003';//chongfu
		}else if($row==1){
			return '100';//'HasThisOrder';
		}
		return FALSE;
	}

}
?>