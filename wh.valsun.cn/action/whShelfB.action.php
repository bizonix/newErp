<?php
/*
 * 上架操作
 */
class whShelfBAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	/*
	*上架搜索料号的相关信息
	*/
    public function act_whShelfSku(){
    	$sku = isset($_POST['sku'])?$_POST['sku']:"";
		$sku = get_goodsSn($sku);
    	/*
		$where = "where sku='{$sku}' and tallyStatus=0";
		$list = packageCheckModel::selectList($where);
		$ichibanNums = 0;
		if(empty($list)){
			self::$errCode = 444;
			self::$errMsg  = "无该料号点货信息";
			return false;
		}
		
    	foreach($list as $key=>$value){
    		$ichibanNums = $ichibanNums+($value['ichibanNums']-$value['shelvesNums']);
    	}
		*/
    	$info = $this->findPositionRelation($sku);
		//$actualStock = whShelfModel::selectSkuNums($sku);
		//print_r($now_position);die;
    	//$res['ichibanNums']   = $ichibanNums;
    	//$res['actualStock'] = $actualStock['actualStock'];
    	$res['position'] 	  = $info['now_position'];
    	$res['storeposition'] = $info['now_storeposition'];
		$res['sku'] 		  = $sku;
    	//print_r($res);
		self::$errMsg = "输入数量或选择其他上架位置";
		return $res;
    }
	
	/*
	*搜索sku仓位关系
	*/
    public function findPositionRelation($sku){
		$result			   = array();
		$now_position 	   = array();
		$now_storeposition = array();
		$where = "where sku='{$sku}'";
    	$skuinfo = whShelfModel::selectSku($where);
    	$skuId = $skuinfo['id'];
    	$where = "where pId ={$skuId} and is_delete=0 and storeId in(2)";
    	$positioninfo = whShelfModel::selectRelation($where);
		//print_r($positioninfo);die;
    	foreach($positioninfo as $key =>$value){
    		if($value['type']==1){
				$where = " where id={$value['positionId']}";
				$info = whShelfModel::selectPosition($where);
				if($info){
					$now_position[] = array(
						'id'    => $value['id'],
						'pName' => $info[0]['pName'],
						'nums'  => $value['nums'],
					);
				}
    		}
			if($value['type']==2){
				$where = " where id={$value['positionId']}";
				$info = whShelfModel::selectPosition($where);
				if($info){
					$now_storeposition[] = array(
						'id'    => $value['id'],
						'pName' => $info[0]['pName'],
						'nums'  => $value['nums'],
					);
				}
			}
    	}
		$result['now_position'] 	 = $now_position;
		$result['now_storeposition'] = $now_storeposition;
		return $result;
	}
	
	/*
	*搜索指定仓位最近空仓位(3个)
	*/
    public function act_findPosition(){
    	$now_position 	   = isset($_POST['now_position'])?$_POST['now_position']:"";		
		$where 			   = " where pName='{$now_position}' and storeId=2";
		$now_position_info = whShelfModel::selectPosition($where);
		if(!$now_position_info || empty($now_position_info)){
			self::$errCode = "003";
			self::$errMsg  = "系统中不存在[{$now_position}]仓位信息";
			return false;
		}
		
		$where 			     	 = " where is_enable=0 and type=1 and id!={$now_position_info[0]['id']} and storeId=2";
		$picking_position_list   = whShelfModel::selectPosition($where);         //未用仓位
		$where 			         = " where is_enable=0 and type=2 and id!={$now_position_info[0]['id']} and storeId=2";
		$nopicking_position_list = whShelfModel::selectPosition($where);		 //未用备货位
		
		$picking_arr = array();
		$picking_pname_arr = array();
		$show_picking_arr = array();
		
		$nopicking_arr = array();
		$nopicking_pname_arr = array();
		$show_nopicking_arr = array();
		if($now_position_info[0]['type']==1){
			$show_picking_arr[] = array(
				'id'	 => $now_position_info[0]['id'],
				'pName'  => $now_position_info[0]['pName']
			);
		}else if($now_position_info[0]['type']==2){
			$show_nopicking_arr[] = array(
				'id'	 => $now_position_info[0]['id'],
				'pName'  => $now_position_info[0]['pName']
			);
		}
		
		if($picking_position_list){
			foreach($picking_position_list as $picking_position){
				$distance = getDistance($now_position_info[0]['x_alixs'],$now_position_info[0]['y_alixs'],$now_position_info[0]['floor'],$picking_position['x_alixs'],$picking_position['y_alixs'],$picking_position['floor']);
				$picking_arr[$picking_position['id']] = $distance;
				$picking_pname_arr[$picking_position['id']] = $picking_position['pName'];
			}	
			asort($picking_arr);
			$i = 0;
			foreach($picking_arr as $p_key=>$picking_info){
				if($i>=3){
					break;
				}
				$show_picking_arr[] = array(
					'id'	 => $p_key,
					'pName'  => $picking_pname_arr[$p_key]
				);
				$i++;
			}
		}
		

		if($nopicking_position_list){
			foreach($nopicking_position_list as $nopicking_position){
				$distance = getDistance($now_position_info[0]['x_alixs'],$now_position_info[0]['y_alixs'],$now_position_info[0]['floor'],$nopicking_position['x_alixs'],$nopicking_position['y_alixs'],$nopicking_position['floor']);
				$nopicking_arr[$nopicking_position['id']] = $distance;
				$nopicking_pname_arr[$nopicking_position['id']] = $nopicking_position['pName'];
			}
			
			asort($nopicking_arr);
			$j = 0;
			foreach($nopicking_arr as $p_key=>$nopicking_info){
				if($j>=3){
					break;
				}
				$show_nopicking_arr[] = array(
					'id'	 => $p_key,
					'pName'  => $nopicking_pname_arr[$p_key]
				);
				$j++;
			}
		}
		
		$res['show_picking']   = $show_picking_arr;
    	$res['show_nopicking'] = $show_nopicking_arr;
		self::$errMsg = "请选择上架位置";
		return $res;
    }
	
	/*
	*上架入库
	*/
	public function act_whShelf(){
		//print_r($_POST);
		$userCnName = $_SESSION['userCnName'];
		$sku  = trim($_POST['sku']);
		$sku  = get_goodsSn($sku);
		$nums = $_POST['nums'];
		$select_now_position  = $_POST['select_now_position'];
		$select_now_store     = $_POST['select_now_store'];
		$select_hope_position = $_POST['select_hope_position'];
		$select_hope_store 	  = $_POST['select_hope_store'];	
		if(empty($sku)){
			self::$errCode = 401;
			self::$errMsg  = "sku不能为空";
			return false;
		}
		
		if(empty($select_now_position)&&empty($select_now_store)&&empty($select_hope_position)&&empty($select_hope_store)){
			self::$errCode = 401;
			self::$errMsg  = "上架位置不能为空";
			return false;
		}
		/*
		$where = "where sku='{$sku}' and tallyStatus=0";
		$tallying_list  = packageCheckModel::selectList($where);
		if(empty($tallying_list)){
			self::$errCode = 402;
			self::$errMsg  = "无该料号点货信息";
			return false;
		}else{
			$tallying_num = 0;
			foreach($tallying_list as $tallying){
				$tallying_num += $tallying['ichibanNums']-$tallying['shelvesNums'];
			}
			if($nums>$tallying_num){
				self::$errCode = 402;
				self::$errMsg  = "上架数不能大于点货良品数[{$tallying_num}]";
				return false;
			}
		}
		*/
		if($nums<1){
			self::$errCode = 403;
			self::$errMsg  = "上架数量不能小于1";
			return false;
		}
		
		$where   = " where sku = '{$sku}'";
		$skuinfo = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 404;
			self::$errMsg  = "无该料号信息";
			return false;
		}else{
			$skuId 		= $skuinfo['id'];
			$purchaseId = $skuinfo['purchaseId'];
		}
		/*
		$purInfo = CommonModel::endPurchaseOrder($sku,$nums);             //api获取采购订单处理情况
		if($purInfo!=0){
			self::$errCode = 405;
			self::$errMsg  = "完结采购订单出错,上架失败";
			return false;
		}
		
		//更新旧erp库存
		$update_onhand = CommonModel::updateOnhand($sku,$nums);
		if($update_onhand==0){
			self::$errCode = 415;
			self::$errMsg = "更新erp库存失败";
			return false;
		}*/
		
		$return_num = $nums;
		$in_positionId = 0;
		$userId = $_SESSION['userId'];
		TransactionBaseModel :: begin();
		
		/****插入采购未订单记录****/
		/*
		if($return_num>0){
			$where = " where sku = '{$sku}' and tallyStatus=0";
			$list  = whShelfModel::selectList($where);
			$purchaseId = $list[0]['purchaseId'];
			$totalNums = 0;
			foreach($list as $key=>$value){
				$totalNums += $value['num'];
			}
			if ($return_num==$nums){
				$reach_note = "sku[{$sku}]到货{$nums}个,未找到该料号的订单,请物料点货确认和采购补单!";
			}else{
				$reach_note = "sku[{$sku}]到货{$nums}个,入库完毕后还多余{$return_num}个,请物料点货确认和采购补单!";
			}
			$msg = whShelfModel::insertNoOrder($sku,$return_num,$totalNums,$purchaseId,$userId,$reach_note);
			if(!$msg){
				self::$errCode = whShelfModel::$errCode;
				self::$errMsg  = whShelfModel::$errMsg;
				return false;
			}
		}
		*/
		/***无料号对应仓位的关系时更新关系表***/
		if($select_hope_store!=0 || $select_hope_position!=0){
			$type = 1;
			$positionId = $select_hope_position;
			if($select_hope_store!=0){
				$type = 2;
				$positionId = $select_hope_store;
			}
			$in_positionId = $positionId;
			$tname = "wh_product_position_relation";
			$set   = "set pId='$skuId',positionId='$positionId',nums='$nums',type='$type',storeId=2";
			$insert_relation = OmAvailableModel::insertRow($tname,$set);
			if(!$insert_relation){
				self::$errCode = 408;
				self::$errMsg = "插入关系表失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
			
			//更新仓位使用状态
			$update_position = OmAvailableModel::updateTNameRow("wh_position_distribution","set is_enable=1","where id=$positionId");
			if($update_position===false){
				self::$errCode = 409;
				self::$errMsg = "更新仓位使用状态失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
		}
		
		//更新指定仓位存货数量
		if($select_now_store!=0){
			$positioninfo  = whShelfModel::selectRelation("where id={$select_now_store}");
			$in_positionId = $positioninfo[0]['positionId'];
			$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$select_now_store'");
			if(!$update_position){
				self::$errCode = 410;
				self::$errMsg = "更新仓位库存失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
		}
		
		if($select_now_store==0 && $select_hope_position==0 && $select_hope_store==0){
			$positioninfo  = whShelfModel::selectRelation("where id={$select_now_position}");
			$in_positionId = $positioninfo[0]['positionId'];
			$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$select_now_position'");
			if(!$update_position){
				self::$errCode = 411;
				self::$errMsg = "更新仓位库存失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
		}
		
		/**** 更新总库存 *****/
		$actualStock = whShelfModel::selectSkuNums($sku,2);
		if(!empty($actualStock)){
			$where = "where sku='{$sku}' and storeId=2";
			$info  = whShelfModel::updateStoreNum($nums,$where);
			if(!$info){
				self::$errCode = 412;
				self::$errMsg = "更新总库存失败！";
				TransactionBaseModel :: rollback();
				return false;
				
			}
		}else{
			$info = packageCheckModel::insertStore($sku,$nums,2);
			if(!$info){
				self::$errCode = 412;
				self::$errMsg = "更新总库存失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
		}

		/**** 插入出入库记录 *****/
		$paraArr = array(
			'sku'     	 => $sku,
			'amount'  	 => $nums,
			'positionId' => $in_positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => 13,
			'userId'	 => $userId,
			'reason'	 => '上架入库',
			'storeId'    => 2,
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 413;
			self::$errMsg = "插入出入库记录失败！";
			TransactionBaseModel :: rollback();
			return false;
			
		}
		/*
		//更新点货记录状态
		$where = "where sku='{$sku}' and tallyStatus=0 and ichibanNums>0";
    	$list  = packageCheckModel::selectList($where);
		$i = 0;
		while($list[$i]&&$nums){
			$need_nums = $list[$i]['ichibanNums']-$list[$i]['shelvesNums'];
			if($nums >= $need_nums){
				//更改状态
				$msg = whShelfModel::updateTallyStatus($list[$i]['id'],$need_nums);
				if(!$msg){
					self::$errCode = 413;
					self::$errMsg  = "更新点货记录状态失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
				$nums = $nums-$need_nums;
			}else{
				$msg = whShelfModel::updateShelfNum($list[$i]['id'],$nums);
				if(!$msg){
					self::$errCode = 414;
					self::$errMsg  = "更新点货记录已上架数量失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
				$nums = 0;
			}
			$i++;
		}
		*//*
		$purInfo = CommonModel::endPurchaseOrder($sku,$return_num);             //api获取采购订单处理情况
		if($purInfo!=0){
			self::$errCode = 405;
			self::$errMsg  = "完结采购订单出错,上架失败";
			TransactionBaseModel :: rollback();
			return false;
		}
		
		//更新旧erp库存
		$position_info = PositionModel::getPositionList("pName","where id={$in_positionId}");
		$update_onhand = CommonModel::updateOnhand($sku,$return_num,$userCnName,$position_info[0]['pName']);
		if($update_onhand==0){
			self::$errCode = 415;
			self::$errMsg = "更新旧erp库存失败";
			TransactionBaseModel :: rollback();
			return false;
		}
		*/
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]上架成功！";
		return true;
	}
	

}
?>