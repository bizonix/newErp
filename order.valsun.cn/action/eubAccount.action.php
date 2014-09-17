<?php
/*
* paypal邮箱管理页面
* @author by heminghua 
*/
class eubAccountAct extends Auth{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	public function act_eubAccount(){
		$id   	= isset($_POST['id'])?addslashes(trim($_POST['id'])):"";
		$account 	= isset($_POST['account'])?addslashes(trim($_POST['account'])):"";
		$where = "where account='{$account}'";
		$accountinfo = eubAccountModel::selectList($where);
		if(!$accountinfo){
			self::$errCode = 201;
			self::$errMsg  = "账户名错误！";
			header("Location:index.php?act=eubAccount&mod=eubAccount&id={$id}&data=账户名错误！");
			exit;
		}
		$dev_id 	= isset($_POST['dev_id'])?addslashes(trim($_POST['dev_id'])):"";
		$dev_sig 	= isset($_POST['dev_sig'])?addslashes(trim($_POST['dev_sig'])):"";
		$pname 		= isset($_POST['pname'])?addslashes(trim($_POST['pname'])):"";
		$pcompany 	= isset($_POST['pcompany'])?addslashes(trim($_POST['pcompany'])):"";
		$pcountry 	= isset($_POST['pcountry'])?addslashes(trim($_POST['pcountry'])):"";
		$pprovince 	= isset($_POST['pprovince'])?addslashes(trim($_POST['pprovince'])):"";
		$pcity 		= isset($_POST['pcity'])?addslashes(trim($_POST['pcity'])):"";
		$pdis 		= isset($_POST['pdis'])?addslashes(trim($_POST['pdis'])):"";
		$pstreet 	= isset($_POST['pstreet'])?addslashes(trim($_POST['pstreet'])):"";
		$pzip 		= isset($_POST['pzip'])?addslashes(trim($_POST['pzip'])):"";
		$ptel 		= isset($_POST['ptel'])?addslashes(trim($_POST['ptel'])):"";
		$ptel2 		= isset($_POST['ptel2'])?addslashes(trim($_POST['ptel2'])):"";
		$pemail 	= isset($_POST['pemail'])?addslashes(trim($_POST['pemail'])):"";
		$dname 		= isset($_POST['dname'])?addslashes(trim($_POST['dname'])):"";
		$dcompany 	= isset($_POST['dcompany'])?addslashes(trim($_POST['dcompany'])):"";
		$dcountry 	= isset($_POST['dcountry'])?addslashes(trim($_POST['dcountry'])):"";
		$dprovince 	= isset($_POST['dprovince'])?addslashes(trim($_POST['dprovince'])):"";
		$dcity 		= isset($_POST['dcity'])?addslashes(trim($_POST['dcity'])):"";
		$ddis 		= isset($_POST['ddis'])?addslashes(trim($_POST['ddis'])):"";
		$dstreet 	= isset($_POST['dstreet'])?addslashes(trim($_POST['dstreet'])):"";
		$dzip 		= isset($_POST['dzip'])?addslashes(trim($_POST['dzip'])):"";
		$dtel 	    = isset($_POST['dtel'])?addslashes(trim($_POST['dtel'])):"";
		$demail     = isset($_POST['demail'])?addslashes(trim($_POST['demail'])):"";
		$shiptype  	= isset($_POST['shiptype'])?trim($_POST['shiptype']):"";
		$rname 	   	= isset($_POST['rname'])?addslashes(trim($_POST['rname'])):"";
		$rcompany  	= isset($_POST['rcompany'])?addslashes(trim($_POST['rcompany'])):"";
		$rcountry  	= isset($_POST['rcountry'])?addslashes(trim($_POST['rcountry'])):"";
		$rprovince 	= isset($_POST['rprovince'])?addslashes(trim($_POST['rprovince'])):"";
		$rcity 	   	= isset($_POST['rcity'])?addslashes(trim($_POST['rcity'])):"";
		$rdis      	= isset($_POST['rdis'])?addslashes(trim($_POST['rdis'])):"";
		$rstreet   	= isset($_POST['rstreet'])?addslashes(trim($_POST['rstreet'])):"";
		
		//$sql = "";
		//print_r($_POST);
		foreach($_POST as $key=> $value){
			if($key=="id"){
				continue;
			}
			$value = addslashes(trim($value));
			if($value !==""){
				if($key=="shiptype"){
					$sql[] = "{$key}={$value}";
				}else{
					$sql[] = "{$key}='{$value}'";
				}
			}
		}
		$sql = implode(",",$sql);
		$info = eubAccountModel::updateRecord($sql,$id);
		if(!$info){
			self::$errCode = 202;
			self::$errMsg  = "更新失败";
			return false;
		}
		header("Location:index.php?act=eubAccount&mod=eubAccount&id={$id}&data=修改成功！");
		exit;
	}
	
	public function act_getEUBAccountList(){
		global $memc_obj;
		$cacheName = md5("om_eub_account");		
		$list = $memc_obj->get_extral($cacheName);
		
		if($list){
			return json_encode($list);
		}else{
			
			$accountList = eubAccountModel::selectList();
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
	
}
?>