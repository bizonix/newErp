<?php
class PasteLabelAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	
	/*
     * 验证分组号
     */
	public function act_checkGroupId(){
        $groupId    =   trim($_POST['groupId']); 
        $storeId    =   intval(trim($_POST['storeId'])); //所属仓库ID
        $storeId    =   $storeId ? $storeId : 1;
		if(!is_numeric($groupId)){
			self::$errCode = 201;
			self::$errMsg  = "分组号必须为数字！";
			return false;
		}
		
		$groupInfo = PasteLabelModel::selectListById($groupId);
		if($groupInfo){
		    if($groupInfo[0]['storeId'] != $storeId){ //
                self::$errCode = 203;
    			self::$errMsg  = "该贴标记录不在该仓库!";
    			return false;
		    }
			return true;
		}else{
			self::$errCode = 202;
			self::$errMsg  = "该分组号不存在或者已经录入，请确认";
			return false;
		}
	}
	/*
     * 贴标录入
     */
	public function act_pasteLabel(){
		$groupId   = $_POST['groupId'];
		$checkUser = $_POST['checkUser'];
		
		if(!is_numeric($groupId) || empty($checkUser)){
			self::$errCode = 201;
			self::$errMsg = "贴标人或分组信息有误，请确认！";
			return false;
		}
		
		$inserInfo = PasteLabelModel::insertRecord($groupId,$checkUser);
		if($inserInfo){
			self::$errMsg = "录入成功";
			return true;
		}else{
			self::$errCode = 202;
			self::$errMsg = "录入失败";
			return false;
		}
	}
	
	/*
     * 删除打标
     */
	public function act_del(){
		$id   = $_POST['id'];		
		$info = PasteLabelModel::delRecord($id);
		if($info){
			self::$errMsg = "删除成功";
			return true;
		}else{
			self::$errCode = 201;
			self::$errMsg = "删除失败";
			return false;
		}
	}
	
	/*
     * 清空贴标
     */
	public function act_clear(){
		$id   = $_POST['id'];		
		$info = PasteLabelModel::clearRecord($id);
		if($info){
			self::$errMsg = "清空成功";
			return true;
		}else{
			self::$errCode = 201;
			self::$errMsg = "清空失败";
			return false;
		}
	}
	
	/*
     * 修改贴标
     */
	public function act_edit(){
		$id   	   = $_POST['id'];	
		$labelUser = $_POST['e_username'];
		
		$usermodel = UserModel::getInstance();
		$userInfo  = $usermodel->getGlobalUserLists('global_user_id,global_user_name',"where a.global_user_name='{$labelUser}'",'','');

		if(!$userInfo){
			self::$errCode = 201;
			self::$errMsg = "该用户不存在，请先添加";
			return false;
		}
		
		$info = PasteLabelModel::editRecord($id,$userInfo[0]['global_user_id']);
		if($info){
			self::$errMsg = "修改成功";
			return true;
		}else{
			self::$errCode = 201;
			self::$errMsg = "修改失败";
			return false;
		}
	}
	
	/*
     * 贴标报表导出
     */
	public function act_export(){
		$checkUser = $_GET['checkUser'];
		$status    = $_GET['status'];
		$sku       = $_GET['sku'];
		$startdate = $_GET['startdate'];
		$enddate   = $_GET['enddate'];
		if(empty($checkUser)&&empty($status)&&empty($sku)&&empty($startdate)&&empty($enddate)){
			echo "请选择导出条件";exit;
		}
		
		if(!empty($checkUser)){
			$where[] = "a.labelUserId='{$checkUser}'";
		}
		
		if(!empty($status)){
			if($status==1){
				$where[] = "a.labelUserId is NULL";
			}
			if($status==2){
				$where[] = "a.labelUserId is not NULL";
			}	
		}
		if(!empty($sku)){
			$where[] = "b.sku = '{$sku}'";
		}
		if(!empty($startdate)){
			$start = strtotime($startdate);
			$where[] = "a.labelTime >={$start}";
		}
		if(!empty($enddate)){
			$end = strtotime($enddate);
			$where[] = "a.labelTime <={$end}";
		}
		$where = implode(" AND ",$where);
		$where = " where a.is_delete=0 and a.status=1 and ".$where." order by a.id desc";
		$lists = PasteLabelModel::selectList($where);
		
		$excel  = new ExportDataExcel('browser', "labelKpiExport".date('Y-m-d').".xls"); 
		$excel->initialize();
		$tharr = array("贴标人","贴标时间","SKU","数量","分组号","批次号");
		$excel->addRow($tharr);
		
		foreach($lists as $list){
			if(!empty($list['labelUserId'])){
				$user = getUserNameById($list['labelUserId']);
				$time = date('Y-m-d H:i:s',$list['labelTime']);
			}else{
				$user = '';
				$time = '';
			}
			$sku = $list['sku'];
			$num = $list['labelNum'];
			$id  = $list['id'];
			$batchNum = $list['batchNum'];
			$tdarr	  = array($user,$time,$sku,$num,$id,$batchNum);
			$excel->addRow($tdarr);	
		}
	
		$excel->finalize();
		exit;
	}
	
	/*
     * 补标sku检测
     */
	public function act_checkSku(){
		$skus     = $_POST['skus'];
		$skuarray = array();
		$skulists = explode(',', $skus);
		
		foreach($skulists AS $skulist){
			list($sku, $num)=array_map('trim', explode('*', $skulist));
			$skuarray[] = "'{$sku}'";
			$skukeyarray[$sku] = array('sku'=>$sku);
		}
		
		$skuarray = array_unique($skuarray);
		$skustr = "(".implode(',', $skuarray).")";
		$goodslists = OmAvailableModel::getTNameList("pc_goods","sku,goodsName","where sku in $skustr and is_delete=0");

		if(empty($goodslists)){
			self::$errCode = 201;
			self::$errMsg = "未找到对应的料号信息!";
			return false;
		}else{
			$res          = array();
			$skukeyresult = array();
			$resultskus	  = array();
			foreach($goodslists AS $key=>$goodslist){
				$resultskus[$goodslist['sku']] = array('');
				$pname_info = GroupRouteModel::getSkuPosition("where a.sku='{$goodslist['sku']}' and b.is_delete=0");
				if(!empty($pname_info)){
					$pname = $pname_info[0]['pName'];
				}else{
					$pname = null;
				}
				$goodslists[$key]['pName'] = $pname;
			}
			
			foreach($goodslists AS $goodslist){
				$skukeyarray[$goodslist['sku']] = $goodslist;
			}
			
			foreach($skukeyarray AS $skukey){
				$skukeyresult[] = $skukey;
			}
			
			$errorskus = array_diff_key($skukeyarray, $resultskus);
			
			$res['res_data'] = $skukeyresult;
			$res['res_errorsku'] = count($errorskus)>0 ? true : false;
			self::$errMsg = "料号验证成功";
			return $res;			
		}
	}
}
?>