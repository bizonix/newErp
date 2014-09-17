<?php
class paypalEmailAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
    public function act_addPaypalEmail(){
		
		
		$emails = isset($_POST['emails'])?trim($_POST['emails']):"";
		$accounts 	= isset($_POST['accounts'])?$_POST['accounts']:"";
		$emails = explode(",",$emails);
		$emails = array_filter($emails);
		$userId = $_SESSION['sysUserId'];
		foreach($emails as $key=>$value){
			
			if(!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/",$value)){
				self::$errCode = 101;
				self::$errMsg = "插入记录失败！";
				return false;
			}
			foreach($accounts as $account){
				
				$msg = paypalEmailModel::insertRecord($value,$account,$userId);
				if(!$msg){
					self::$errCode = 102;
					self::$errMsg = "插入记录失败！";
					return false;
				}
			}
		}
		return true;
	}
	public function act_paypalEmailModify(){
		
		
		$email = isset($_POST['email'])?trim($_POST['email']):"";
		$account 	= isset($_POST['account'])?$_POST['account']:"";
		$id 	= isset($_POST['id'])?$_POST['id']:"";
		$enable 	= isset($_POST['enable'])?$_POST['enable']:"";

		$userId = $_SESSION['sysUserId'];

		$msg = paypalEmailModel::updateRecord($email,$account,$enable,$id);
		if(!$msg){
			self::$errCode = 104;
			self::$errMsg = "修改记录失败！";
			return false;
		}
		return true;
	}
	public function act_paypalEmailDel(){
		
		$id 	= isset($_POST['id'])?$_POST['id']:"";


		$msg = paypalEmailModel::delRecord($id);
		if(!$msg){
			self::$errCode = 104;
			self::$errMsg = "修改记录失败！";
			return false;
		}
		return true;
	}
}
?>