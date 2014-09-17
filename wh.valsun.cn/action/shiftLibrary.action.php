<?php
/*
 * 移库操作
 */
class ShiftLibraryAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }

	public function act_shiftLibrary(){
		$sku  		 = trim($_POST['sku']);
		$sku 		 = get_goodsSn($sku);
		$oldposition = trim($_POST['oldposition']);
		$newposition = trim($_POST['newposition']);
		$nums 		 = intval(trim($_POST['nums']));	
		if(empty($sku)){
			self::$errCode = 401;
			self::$errMsg  = "sku不能为空";
			return false;
		}
		if(empty($oldposition)){
			self::$errCode = 402;
			self::$errMsg  = "旧仓位号不能为空";
			return false;
		}
		if(empty($newposition)){
			self::$errCode = 403;
			self::$errMsg  = "新仓位号不能为空";
			return false;
		}
		if($oldposition==$newposition){
			self::$errCode = 404;
			self::$errMsg  = "新旧仓位号不能相同";
			return false;
		}
		
		$where   = " where sku = '{$sku}'";
		$skuinfo = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 404;
			self::$errMsg  = "无该料号信息";
			return false;
		}else{
			$skuId = $skuinfo['id'];
		}
		
		$old_positon_info = OmAvailableModel::getTNameList("wh_position_distribution","id","where pName='$oldposition' and storeId in(1,2)");
		if(empty($old_positon_info)){
			self::$errCode = 405;
			self::$errMsg  = "无旧仓位号信息";
			return false;
		}else{
			$old_location = $old_positon_info[0]['id'];
		}
		
		$new_positon_info = OmAvailableModel::getTNameList("wh_position_distribution","id,type","where pName='$newposition' and storeId in(1,2)");
		if(empty($new_positon_info)){
			self::$errCode = 406;
			self::$errMsg  = "无新仓位号信息";
			return false;
		}else{
			$new_location = $new_positon_info[0]['id'];
		}

		$old_sku_pos_info = OmAvailableModel::getTNameList("wh_product_position_relation","*","where pId='$skuId' and positionId='$old_location' and storeId in(1,2) and is_delete=0");
		if(empty($old_sku_pos_info)){
			self::$errCode = 407;
			self::$errMsg  = "无sku对应的旧仓位号信息";
			return false;
		}
		
		$new_sku_pos_info = OmAvailableModel::getTNameList("wh_product_position_relation","*","where pId='$skuId' and positionId='$new_location' and storeId in(1,2) and is_delete=0");
		if($nums>$old_sku_pos_info[0]['nums'] || empty($nums)){
			$change_nums = $old_sku_pos_info[0]['nums'];
		}else{
			$change_nums = $nums;
		}
		
		OmAvailableModel::begin();
		if(!empty($new_sku_pos_info)){
			if(empty($nums)){
				$tname_old = "wh_product_position_relation";
				$set_old   = "set nums=0,is_delete=1";
				$where_old = "where id={$old_sku_pos_info[0]['id']}";
				$update_old = OmAvailableModel::updateTNameRow($tname_old,$set_old,$where_old);
				if(!$update_old){
					self::$errCode = 408;
					self::$errMsg = "更新旧仓位库存失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
			}else{
				$tname_old = "wh_product_position_relation";
				$set_old   = "set nums=nums-{$change_nums}";
				$where_old = "where id={$old_sku_pos_info[0]['id']}";
				$update_old = OmAvailableModel::updateTNameRow($tname_old,$set_old,$where_old);
				if(!$update_old){
					self::$errCode = 409;
					self::$errMsg = "更新旧仓位库存失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
			}
			
			$tname_new = "wh_product_position_relation";
			$set_new   = "set nums=nums+{$change_nums}";
			$where_new = "where id={$new_sku_pos_info[0]['id']}";
			$update_new = OmAvailableModel::updateTNameRow($tname_new,$set_new,$where_new);
			if(!$update_new){
				self::$errCode = 410;
				self::$errMsg = "更新新仓位库存失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
		}else{
			if(empty($nums)){
				$tname_old = "wh_product_position_relation";
				$set_old   = "set nums=0,is_delete=1";
				$where_old = "where id={$old_sku_pos_info[0]['id']}";
				$update_old = OmAvailableModel::updateTNameRow($tname_old,$set_old,$where_old);
				if(!$update_old){
					self::$errCode = 411;
					self::$errMsg = "更新旧仓位库存失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
			}else{
				$tname_old = "wh_product_position_relation";
				$set_old   = "set nums=nums-{$change_nums}";
				$where_old = "where id={$old_sku_pos_info[0]['id']}";
				$update_old = OmAvailableModel::updateTNameRow($tname_old,$set_old,$where_old);
				if(!$update_old){
					self::$errCode = 412;
					self::$errMsg = "更新旧仓位库存失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
			}
			
			$tname_insert 	 = "wh_product_position_relation";
			$set_insert      = "set pId='$skuId',positionId='$new_location',nums='$change_nums',type={$new_positon_info[0]['type']}";
			$relation_insert = OmAvailableModel::insertRow($tname_insert,$set_insert);
			if(!$relation_insert){
				self::$errCode = 413;
				self::$errMsg = "插入关系表失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
			/*
			//更新仓位使用状态
			$update_position = OmAvailableModel::updateTNameRow("wh_position_distribution","set is_enable=1","where id=$new_location");
			if(!$update_position){
				self::$errCode = 414;
				self::$errMsg = "更新仓位使用状态失败！";
				TransactionBaseModel :: rollback();
				return false;
			}
			*/
		}
        //全部转移到新仓位，则将仓位同步到老ERP
        if(!$nums){
            $info   =   CommonModel::updateSkuLocation($sku, $newposition);
            //print_r($info);exit;
            if($info['res_code'] != 200){
                self::$errCode  =   414;
                self::$errMsg   =   '同步旧ERP仓位失败!';
                return FALSE;
            }
        }

		OmAvailableModel::commit();
		self::$errMsg = "料号[{$sku}]移库成功！";
		return true;
	}

	//sku查询
	function act_searchSku(){
		$sku = trim($_POST['sku']);
		$sku = get_goodsSn($sku);
		
		$eosr_arr = whShelfModel::selectSkuNums($sku);
		if(empty($eosr_arr)){
			self::$errCode = "401";
			self::$errMsg  = "找不到该料号的库存信息！";
			return false;
		}else{			
			$info = GroupRouteModel::getSkuPosition("where a.sku='{$sku}' and b.is_delete=0");
			self::$errCode = "400";
			self::$errMsg  = '成功搜索该料号信息!'.$sku;
			return $info;
		}
	}
}
?>