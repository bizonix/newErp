<?php
class EubAccountAct extends CheckAct{
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 获取eub信息
	 * @param accountId
	 * @return aray
	 * @author xyd
	 */
	public function act_getEubList(){
		 $accountId    = isset($_GET['id']) ? $_GET['id'] : 0;
		 $account     = isset($_GET['account']) ? $_GET['account'] : 0;
		 if($accountId){
		 	if(M('EubAccount')->geteubAccountByAcid($accountId)){
		 		return M('EubAccount')->geteubAccountByAcid($accountId);
		 	}else{
		 		return array(array('account'=>$account,'accountId'=>$accountId));
		 	}
	     	
		 }else{
		 	return array(array());
		 }
	}
	
	public function act_saveEub(){
		$data    = $_POST;
		$id      = isset($_POST['id'])?$_POST['id']:0;
		unset($data['id']);	
		if($id){
	       return   M('EubAccount')->replaceData($id,$data,'id');
	     }else{
	     	return   M('EubAccount')->insertData($data);
	     }
	}
    
    /**
	 * 提供给仓库上传EUB跟踪号，获取EUB参数的接口
	 * @param accountId
	 * @return aray
	 * @author zqt
	 */
	public function act_getEubAccountByAccountId(){
		 $accountId = intval($_GET['accountId'])?intval($_GET['accountId']):0;
         if($accountId <= 0){
             self::$errMsg[10046] = get_promptmsg(10046);
             return false;
         }else{
            $accountEUBList = M('EubAccount')->geteubAccountByAcid($accountId);
            if(empty($accountEUBList[0])){
                self::$errMsg[10140] = get_promptmsg(10140);
                return false;
            }else{
                $conditionArr = array('id'=>array('$e'=>$accountId));
                $AccountList = M('Account')->getAccountList($conditionArr);
                if(!empty($AccountList)){
                    $accountEUBList[0]['suffix'] = $AccountList[0]['suffix'];
                }
                return $accountEUBList[0];
            }
         }		 
	}
    
    
    
    
}
?>