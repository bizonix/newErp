<?php
/*
 * B仓调拨入库上架操作
 */
class Pda_goodsAssignWhselfBAct extends Auth{
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
    	$where = "where sku='{$sku}' and tallyStatus=0 and is_delete=0 and num >0 and ichibanNums>0";
    	$list = packageCheckModel::selectList($where);
    	$ichibanNums = 0;
        $totalNums  = 0;
		if(empty($list)){
			self::$errCode = 444;
			self::$errMsg  = "无该料号点货信息";
			return $sku;
		}
		
    	foreach($list as $key=>$value){
    		$ichibanNums =  $ichibanNums+($value['ichibanNums']-$value['shelvesNums']); //可上架良品数
            $totalNums   += ($value['num']-$value['shelvesNums']); //可上架点货数
    	}
		$shelvesNums  =   $totalNums > $ichibanNums ? $ichibanNums : $totalNums;  //可上架数
        
    	$info = $this->findPositionRelation($sku);
		//$actualStock = whShelfModel::selectSkuNums($sku);
		//print_r($now_position);die;
    	//$res['ichibanNums']   = $ichibanNums;
    	//$res['actualStock'] = $actualStock['actualStock'];
    	$res['position'] 	  = $info['now_position'];
    	$res['storeposition'] = $info['now_storeposition'];
		$res['sku'] 		  = $sku;
    	//print_r($res);
		self::$errMsg = "输入数量或选择其他上架位置(共:".$shelvesNums.")";
		return $res;
    }
	
	/*
	*料号上架搜索料号的相关信息
	*/
    public function act_whSkuReturn(){
    	$sku = isset($_POST['sku'])?$_POST['sku']:"";
		$sku = get_goodsSn($sku);
    	$info = $this->findPositionRelation($sku);
    	$res['position'] 	  = $info['now_position'];
    	$res['storeposition'] = $info['now_storeposition'];
    	//print_r($res);
		self::$errMsg = "请输入数量或者选择其他上架位置";
		return $res;
    }
    
    /*
	*搜索指定仓位最近空仓位(3个)
	*/
    public function act_findPosition(){
    	$now_position 	   = isset($_POST['now_position'])?$_POST['now_position']:"";		
		$where 			   = " where pName='{$now_position}' and storeId=2";
		$now_position_info = whShelfModel::selectPosition($where);
		if(!$now_position_info || empty($now_position_info)){
			self::$errCode = "001";
			self::$errMsg  = "B仓不存在[{$now_position}]仓位信息";
			return false;
		}
		$res  = array('id'=>$now_position_info[0]['id']);
		self::$errMsg = "请选择上架位置";
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
    	$where = "where pId ={$skuId} and is_delete=0 and storeId =2";
    	$positioninfo = whShelfModel::selectRelation($where);
		//print_r($positioninfo);die;
    	foreach($positioninfo as $key =>$value){
    		if($value['type']==1){
				$where = " where id={$value['positionId']}";
				$info = whShelfModel::selectPosition($where);
				if($info){
					if(!empty($info[0]['pName'])){
						$now_position[] = array(
							'id'    => $value['id'],
							'pName' => $info[0]['pName'],
							'nums'  => $value['nums'],
						);
					}
				}
    		}
			if($value['type']==2){
				$where = " where id={$value['positionId']}";
				$info = whShelfModel::selectPosition($where);
				if($info){
					if(!empty($info[0]['pName'])){
						$now_storeposition[] = array(
							'id'    => $value['id'],
							'pName' => $info[0]['pName'],
							'nums'  => $value['nums'],
						);
					}
				}
			}
    	}
		$result['now_position'] 	 = $now_position;
		$result['now_storeposition'] = $now_storeposition;
		return $result;
	}
    
    //获取配货单信息
	function act_getGroupInfo(){
		$userId 		= $_SESSION['userId'];
		$shipOrderGroup = $_POST['order_group'];
		$group_sql      = WhGoodsAssignModel::getOrderGroup("*", array('assignNumber'=>$shipOrderGroup));
        //var_dump($group_sql);exit;
		if(empty($group_sql)){
			self::$errCode = "003";
			self::$errMsg  = "该调拨单号不存在，请重新输入!";
			return false;
		}else{
		  if($group_sql[0]['status'] != 106){
            self::$errCode = "003";
			self::$errMsg  = "该调拨单不在上架状态!";
			return false;
		  }
          
          if($group_sql[0]['status'] == 107){
            self::$errCode = "004";
			self::$errMsg  = "该调拨单已上架完毕!";
			return false;
		  }
            self::$errMsg  = "请扫描该调拨单下的料号!";
            self::$errCode = 0;
        	return array('group_id'=>$group_sql[0]['id']);
        }
	}
	
	//验证sku
	function act_checkSku(){
		$goodsAssignId  = intval(trim($_POST['now_group_id']));
		$sku 		    = trim($_POST['sku']);
		$sku       		= get_goodsSn($sku);
        if(empty($goodsAssignId)){
            self::$errCode  =   001;
            self::$errCode  =   '请先输入调拨单号!';
            return FALSE;
        }
		$sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku' and a.checkUid != 0");
		if(empty($sku_info)){
			self::$errCode = "002";
			self::$errMsg  = "该调拨单无此料号!";
			return FALSE;
		}
        if($sku_info['assignNum'] <= $sku_info['whselfNum']){
            self::$errCode = "004";
			self::$errMsg  = "该料号已完成调拨上架!";
			return FALSE;
        }
        $info = $this->findPositionRelation($sku);
        if(!empty($info)){
            $res['now_position_id'] = $info['now_position'][0]['id'];
            $res['now_position']    = $info['now_position'][0]['pName'];
        }else{
            $res['now_position_id'] = '';
            $res['now_position']    = '';
        }
    	$res['position'] 	  = $info['now_position'];
    	$res['storeposition'] = $info['now_storeposition'];
        $whselfNum          = $sku_info['assignNum'] - $sku_info['whselfNum'];
        self::$errCode      = 0;
        self::$errMsg       = "请输入上架数量!【{$whselfNum}】";
        $res['sku']         = $sku_info['sku'];
        return $res;
		//self::$errMsg  = "请输入该料号实际出库配货数量!";
        //echo $sku_info['inCheckNum'];
        //$inCheckNum    =  $sku_info['inCheckNum']+1;  //系统累计接收复核数量
        //print_r($sku_info);exit;
			
	}
     	
	/*
	*上架入库
	*/
	public function act_whShelf(){
		//print_r($_POST);
        $log_file   =   'whselfB_log/'.date('Ymd').'.txt';   //日志文件路径
        $date       =   date('Y-m-d H:i:s');
		$userCnName =   $_SESSION['userCnName'];
		$sku        =   trim($_POST['sku']);
		$sku        =   get_goodsSn($sku);
		$nums       =   $_POST['nums'];
        $now_position_id    =   intval(trim($_POST['now_position_id']));  //现在存放该料号的仓位id
        $goodsAssignId      =   intval(trim($_POST['now_group_id']));
        $positionId         =   intval(trim($_POST['position_id'])); //分配的仓位id
	    
        $assignList         =   WhGoodsAssignModel::getAssignList("and a.id = {$goodsAssignId}", '', '', '');
        if($assignList[0]['status'] != 106){
            self::$errCode = 400;
			self::$errMsg  = "该调拨单不在等待上架状态!";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
        }
		if(empty($sku)){
			self::$errCode = 401;
			self::$errMsg  = "sku不能为空";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
        if(empty($now_position_id) && !$positionId){
			self::$errCode = 401;
			self::$errMsg  = "上架仓位不能为空";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
        
        if($nums<1){
			self::$errCode = 403;
			self::$errMsg  = "上架数量不能小于1";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
        
        $sku_info = WhGoodsAssignModel::getDetail( $goodsAssignId ," and a.sku='$sku' and a.inCheckNum != 0");
		if(empty($sku_info)){
			self::$errCode = "404";
			self::$errMsg  = "该调拨单无此料号!";
			return FALSE;
		}
        $whselfNums =   $nums + $sku_info['whselfNum'];
        if($whselfNums > $sku_info['assignNum']){
            self::$errCode = "405";
			self::$errMsg  = "总上架数不能大于配货数!";
			return FALSE;
        }
		
		$where   = " where sku = '{$sku}'";
		$skuinfo = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 404;
			self::$errMsg  = "无该料号信息";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}else{
			$skuId 		= $skuinfo['id'];
			$purchaseId = $skuinfo['purchaseId'];
		}
        
	    $ioTypeId        =  35;  //35 调拨入库上架 出入库类型表id
		
		$return_num       = $nums;
		$in_positionId    = 0;
		$userId           = $_SESSION['userId'];
        //$where           =  "where pId ={$skuId} and positionId = {$now_position_id} and is_delete=0 and storeId = 2";
        
        TransactionBaseModel :: begin();
        if($now_position_id){ //存在料号仓位关系
            $positioninfo    =  whShelfModel::selectRelationShip('', '', 2, $now_position_id); //检测该料号是否有对应仓位关系
            /***无料号对应仓位的关系时更新关系表***/;
			$relationId      = $positioninfo[0]['id'];
            $positionId      = $positioninfo[0]['positionId']; //仓位id
			$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$relationId'");
			if(!$update_position){
				self::$errCode = 410;
				self::$errMsg = "更新仓位库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $update_position, $num, $select_now_store);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新仓位库存成功！'."{$sku}\r\n");
        }else if($positionId){ //没有料号仓位关系则插入一条数据
			$relationId      = whShelfModel::insertRelation($skuinfo['id'], $positionId, $nums, 2); //插入关系表
			//$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$relationId'");
			if(!$relationId){
				self::$errCode = 410;
				self::$errMsg = "插入仓位关系失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $update_position, $num, $select_now_store);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            write_log($log_file, date('Y-m-d H:i:s').'插入仓位关系成功！'."{$sku}\r\n");
        }
		
		/**** 更新总库存 *****/		
		$actualStock = whShelfModel::selectSkuNums($sku, 2);
        
		if(!empty($actualStock)){
			$where = "where sku='{$sku}' and storeId=2";
			$info  = whShelfModel::updateStoreNum($nums,$where);
			if(!$info){
				self::$errCode = 412;
				self::$errMsg = "更新总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $info, $nums, $where);
                write_log($log_file, $log_info);               
				TransactionBaseModel :: rollback();
				return false;
				
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
		}else{
			$info = whShelfModel::insertStore($sku,$nums,2);
			if(!$info){
				self::$errCode = 413;
				self::$errMsg = "更新总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $info, $sku, $nums);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
		}
		
		/**** 插入出入库记录 *****/
		$paraArr = array(
			'sku'     	 => $sku,
			'amount'  	 => $nums,
			'positionId' => $positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => $ioTypeId,
			'userId'	 => $userId,
			'reason'	 => '调拨入库上架',
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 414;
			self::$errMsg = "插入出入库记录失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                            $record, json_encode($paraArr));
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
        write_log($log_file, date('Y-m-d H:i:s').'插入入库记录成功！'."{$sku}\r\n");
		
		//更新调拨单料号上架数量
        $where  =   array('goodsassignId'=>$goodsAssignId, 'sku'=>$sku);
        $update =   array('whselfNum'=>$whselfNums);
        $info   =   WhGoodsAssignModel::updateAssignDetail($where, $update);
        if($info === FALSE){
            self::$errCode = 415;
			self::$errMsg = "更新上架数量失败";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s ,参数:s%\r\n", $sku, $date, self::$errMsg, $nums);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
        }
        
        //更新料号调拨库存
        $where  =   array('sku'=>$sku, 'storeId'=>$assignList[0]['outStoreId']);
        $update =   array('assignStock'=> "assignStock - $nums");
        $info   =   WhGoodsAssignModel::updateSkuLocation($where, $update);
        if($info == FALSE){
            self::$errCode = 416;
			self::$errMsg = "更新调拨库存失败";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s ,参数:s%\r\n", $sku, $date, self::$errMsg, $nums);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
        }
        
        /** 同步老ERP库存**/
        $pName  = whShelfModel::selectPositionInfo('pName', array('id'=>$positionId)); //获取仓位名称
        $pName  = $pName['pName'];
        $info   =   CommonModel::updateIoRecord($sku, $nums, 1, '仓库调拨入库上架', $_SESSION['userCnName'], $pName);
        //var_dump($info);exit;
        if($info['errCode'] != 200){
            TransactionBaseModel :: rollback();
            self::$errCode = "004";
			self::$errMsg  = "同步旧ERP库存失败!";
            $log_info      = sprintf("料号：%s, 时间：%s,信息:%s ,参数:%s\r\n", $sku, $date, self::$errMsg, is_array($info) ? json_encode($info) : $info);
            write_log($log_file, $log_info);
			return false;
        }
        	
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]上架成功！";
		return true;
	}

}
?>