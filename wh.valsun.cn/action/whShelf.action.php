<?php
/*
 * 上架操作
 */
class whShelfAct extends Auth{
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
    	$sku    =  isset($_POST['sku']) ? $_POST['sku'] : "";
		$sku    =  get_goodsSn($sku); //获取转换后的真实料号
        $storeId=  intval(trim($_POST['storeId'])); //仓库id
        $storeId=  $storeId ? $storeId : 1;
    	$where  =  "where sku='{$sku}' and tallyStatus=0 and is_delete=0 and num >0 and ichibanNums>0 and storeId = '{$storeId}'";
        //echo $where;exit;
    	$list   =  packageCheckModel::selectList($where);
    	$ichibanNums = 0;
        $totalNums  = 0;
		if(empty($list)){
			self::$errCode = 444;
			self::$errMsg  = "无该料号点货信息";
			return $sku;
		}
        
        
        //判断是否是新品
        $is_new     =   self::judge_is_new($sku); //判断是否是新品上架
        //var_dump($is_new);exit;
        if($is_new === FALSE){ //不是新品 则判断是否有产品重量 没有则不许上架
            $sku_info   =   whShelfModel::selectSkuInfo('goodsWeight', array('sku'=>$sku)); //获取料号重量信息
            $goodsWeight    =   $sku_info[0]['goodsWeight']*1000; //KG换成g
            //var_dump($goodsWeight);
            if(!$goodsWeight){
                self::$errCode = 445;
    			self::$errMsg  = "该料号无重量信息,请更新重量后再上架！";
    			return $sku;
            }
        }
    	foreach($list as $key=>$value){
    		$ichibanNums =  $ichibanNums+($value['ichibanNums']-$value['shelvesNums']); //可上架良品数
            $totalNums   += ($value['num']-$value['shelvesNums']); //可上架点货数
    	}
		$shelvesNums  =   $totalNums > $ichibanNums ? $ichibanNums : $totalNums;  //可上架数
        
    	$info = $this->findPositionRelation($sku, $storeId);
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
	*邮局退回上架搜索料号的相关信息
	*/
    public function act_whSkuPostReturn(){
    	$sku = isset($_POST['sku'])?$_POST['sku']:"";
		$sku = get_goodsSn($sku);
    	$where = "where sku='{$sku}' and status=0";
    	$list = PostReturnModel::getReturnList("*",$where);
    	//$ichibanNums = 0;
		if(empty($list)){
			self::$errCode = 444;
			self::$errMsg  = "无该料号退回良品信息";
			return false;
		}
    	$info = $this->findPositionRelation($sku);
    	$res['position'] 	  = $info['now_position'];
    	$res['storeposition'] = $info['now_storeposition'];
    	//print_r($res);
		self::$errMsg = "请输入数量或者选择其他上架位置";
		return $res;
    }
	
	/*
	*搜索sku仓位关系
	*/
    public function findPositionRelation($sku, $storeId = 1){
		$result			   = array();
		$now_position 	   = array();
		$now_storeposition = array();
		$where = "where sku='{$sku}'";
    	$skuinfo = whShelfModel::selectSku($where);
    	$skuId = $skuinfo['id'];
    	//$where = "where pId ={$skuId} and is_delete=0 and storeId in ({$storeId})";
    	$positioninfo = whShelfModel::selectRelationShip($skuId, '', $storeId);
		//print_r($positioninfo);die;
    	foreach($positioninfo as $key =>$value){
    		if($value['type']==1){
				$where = " where id={$value['positionId']} and is_enable = 1";
				$info = whShelfModel::selectPosition($where);
				if($info){
					if(!empty($info[0]['pName'])){
                        if(preg_match("/WH|HW/", $info[0]['pName']) && $storeId == 1){ //判断是否是A仓上架且是B仓仓位
                            $positionId     =   8290;
                            $id =   whShelfModel::insertRelation($skuId, $positionId, 0);
                            $now_position   =   array();
                            $now_position[] =   array('id' => $id, 'pName'=>'TEMPATB', 'nums'=>0);
                            break;
                        }
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
	
	/*
	*搜索指定仓位最近空仓位(3个)
	*/
    public function act_findPosition(){
    	$now_position 	   = isset($_POST['now_position'])?$_POST['now_position']:"";		
		$where 			   = " where pName='{$now_position}'";
		$now_position_info = whShelfModel::selectPosition($where);
		if(!$now_position_info || empty($now_position_info)){
			self::$errCode = "003";
			self::$errMsg  = "系统中不存在[{$now_position}]仓位信息";
			return false;
		}
		
		$where 			     	 = " where is_enable=0 and type=1 and id!={$now_position_info[0]['id']}";
		$picking_position_list   = whShelfModel::selectPosition($where);         //未用仓位
		$where 			         = " where is_enable=0 and type=2 and id!={$now_position_info[0]['id']}";
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
        $log_file   =   'update_onhandle/'.date('Ymd').'.txt';   //日志文件路径
        $date       =   date('Y-m-d H:i:s');
		$userCnName = $_SESSION['userCnName'];
		$sku  = trim($_POST['sku']);
		$sku  = get_goodsSn($sku);
		$nums = $_POST['nums'];
		$select_now_position  = $_POST['select_now_position'];
		$select_now_store     = $_POST['select_now_store'];
		$select_hope_position = $_POST['select_hope_position'];
		$select_hope_store 	  = $_POST['select_hope_store'];
        $storeId           =    intval(trim($_POST['storeId']));
        $storeId           =    $storeId ? $storeId : 1;
	
		if(empty($sku)){
			self::$errCode = 401;
			self::$errMsg  = "sku不能为空";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
        
        if(preg_match("/^MT\d+$/", $sku)){
            self::$errCode = 402;
			self::$errMsg  = "包材料号请在包材入库添加入库！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
        }
		
		if(empty($select_now_position)&&empty($select_now_store)&&empty($select_hope_position)&&empty($select_hope_store)){
			self::$errCode = 401;
			self::$errMsg  = "上架位置不能为空";
			return false;
		}
        
        if($nums<1){
			self::$errCode = 403;
			self::$errMsg  = "上架数量不能小于1";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
		
		$where = "where sku='{$sku}' and tallyStatus=0 and entryStatus = 0 and is_delete = 0 and num >0 and ichibanNums>0";
		$tallying_list  = packageCheckModel::selectList($where);
		if(empty($tallying_list)){
			self::$errCode = 402;
			self::$errMsg  = "无该料号点货信息";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}else{
			$tallying_num = 0;  //良品总数减去上架总数
            $total_num    = 0;  //点货总数减去上架总数
			foreach($tallying_list as $tallying){
				$tallying_num += $tallying['ichibanNums']-$tallying['shelvesNums'];
                $total_num    += $tallying['num']-$tallying['shelvesNums'];
			}
			if($nums>$tallying_num){
				self::$errCode = 402;
				self::$errMsg  = "上架数不能大于点货良品数[{$tallying_num}]";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
                write_log($log_file, $log_info);
				return false;
			}
            
            if($nums>$total_num){
				self::$errCode = 402;
				self::$errMsg  = "上架数不能大于点货总数[{$total_num}]";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
                write_log($log_file, $log_info);
				return false;
			}
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
        
        /**
         * 增加新品采购入库类型判断
         *@author Gary(yym)
         * 2014-04-01 
         * start
         */
         $is_new          =  self::judge_is_new($sku);
		 $ioTypeId        =  $is_new === TRUE ? 33 : 13;  //13为采购入库 33为新品采购入库 ，都是出入库类型表id
        /** end */
        
        /** 检测该料号及数量是否已在临时表中存放**/
        $temp_record      =  whShelfModel::getWhselfTempRecord($sku, $nums);
        if(empty($temp_record)){   //不存在失败记录
            $key          =  md5($sku.$nums.time().$_SESSION['userId']);
            //$waitNum      =  $nums;//CommonModel::checkOnWaySkuNum($sku);
        }else{
            $key          =  $temp_record['rand_key'];
            //$waitNum      =  $temp_record['num'];
            //日志
            $log_info      = sprintf("料号：%s, 时间：%s, 失败记录: %s \r\n", $sku, $date, json_encode($temp_record));
            write_log($log_file, $log_info);
        }
        //$key              =  empty($temp_record) ? substr(md5($sku.$nums.time().$_SESSION['userId']), 0, 16) : $temp_record['rand_key']; //生成判断key值
        /** end**/
        
        /** 取消判断上架数量是否满足采购在途数量**/
        /*if($nums > $waitNum){
            self::$errCode = 405;
			self::$errMsg  = "上架数不能大于订单在途数量";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s ,上架数量:%s, 在途数量：%s \r\n", $sku, $date, self::$errMsg, $nums, $waitNum);
            write_log($log_file, $log_info);
			return false;
        }
        $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s ,上架数量:%s,在途数量：%s \r\n", $sku, $date, '小于在途数量', $nums,$waitNum);
        */
	
		$return_num       = $nums;
		$in_positionId    = 0;
		$userId           = $_SESSION['userId'];
		TransactionBaseModel :: begin();
		
		/***无料号对应仓位的关系时更新关系表***/
		if($select_hope_store!=0 || $select_hope_position!=0){
			$type = 1;
			$positionId = $select_hope_position;
			if($select_hope_store!=0){
				$type = 2;
				$positionId = $select_hope_store;
			}
			$in_positionId = $positionId;
			//$tname = "wh_product_position_relation";
//			$set   = "set pId='$skuId',positionId='$positionId',nums='$nums',type='$type'";
			$insert_relation = whShelfModel::insertRelation($skuId, $positionId, $nums, $storeId, $type);
			if(!$insert_relation){
				self::$errCode = 408;
				self::$errMsg = "插入关系表失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $insert_relation, $tname, $set);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
			write_log($log_file, date('Y-m-d H:i:s').'插入关系表成功'."{$sku}\r\n");
			//更新仓位使用状态
			$update_position = OmAvailableModel::updateTNameRow("wh_position_distribution","set is_enable=1","where id=$positionId");
			if($update_position===false){
				self::$errCode = 409;
				self::$errMsg = "更新仓位使用状态失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数: %s \r\n", $sku, $date, self::$errMsg,
                                            $update_position, $positionId);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新仓位使用状态成功！'."{$sku}\r\n");
		}
		
		//更新指定仓位存货数量
		if($select_now_store!=0){
			$positioninfo  = whShelfModel::selectRelation("where id={$select_now_store}");
			$in_positionId = $positioninfo[0]['positionId'];
			$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$select_now_store'");
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
		}
		
		if($select_now_store==0 && $select_hope_position==0 && $select_hope_store==0){
			$positioninfo  = whShelfModel::selectRelation("where id={$select_now_position}");
			$in_positionId = $positioninfo[0]['positionId'];
			$update_position = whShelfModel::updateProductPositionRelation($nums,"where id='$select_now_position'");
			if(!$update_position){
				self::$errCode = 411;
				self::$errMsg = "更新仓位库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $update_position, $nums, $select_now_position);
                write_log($log_file, $log_info);
				TransactionBaseModel :: rollback();
				return false;
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新仓位库存成功！'."{$sku}\r\n");
		}
		
		/**** 更新总库存 *****/		
		$actualStock = whShelfModel::selectSkuNums($sku, $storeId);
		if(!empty($actualStock)){
			$where = "where sku='{$sku}' and storeId={$storeId}";
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
			$info = whShelfModel::insertStore($sku,$nums, $storeId);
			if(!$info){
				self::$errCode = 412;
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
			'positionId' => $in_positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => $ioTypeId,
			'userId'	 => $userId,
			'reason'	 => '上架入库',
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 413;
			self::$errMsg = "插入出入库记录失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                            $record, json_encode($paraArr));
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
        write_log($log_file, date('Y-m-d H:i:s').'插入入库记录成功！'."{$sku}\r\n");
		
		//更新点货记录状态
		$where = "where sku='{$sku}' and tallyStatus=0 and ichibanNums>0 and is_delete=0 and num >0 and ichibanNums>0";
    	$list  = packageCheckModel::selectList($where);
		$i = 0;
		while($list[$i]&&$nums){
			$need_nums = $list[$i]['ichibanNums']-$list[$i]['shelvesNums']; //良品数与上架数差值
            $list_nums = $list[$i]['num']   -   $list[$i]['shelvesNums'];  //点货数与上架数差值
            $need_nums = $list_nums > $need_nums ? $need_nums : $list_nums;
			if($nums >= $need_nums){
				//更改状态
				$msg = whShelfModel::updateTallyStatus($list[$i]['id'],$need_nums);
				if(!$msg){
					self::$errCode = 413;
					self::$errMsg  = "更新点货记录状态失败！";
                    $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $msg, $list[$i]['id'], $need_nums);
                    write_log($log_file, $log_info);
					TransactionBaseModel :: rollback();
					return false;
				}
                write_log($log_file, date('Y-m-d H:i:s').'更新点货记录状态成功！'."{$sku}\r\n");
				$nums = $nums-$need_nums;
			}else{
				$msg = whShelfModel::updateShelfNum($list[$i]['id'],$nums);
				if(!$msg){
					self::$errCode = 414;
					self::$errMsg  = "更新点货记录已上架数量失败！";
                    $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $msg, $list[$i]['id'], $nums);
                    write_log($log_file, $log_info);
					TransactionBaseModel :: rollback();
					return false;
				}
                write_log($log_file, date('Y-m-d H:i:s').'更新点货记录已上架数量成功！'."{$sku}\r\n");
				$nums = 0;
			}
			$i++;
		}
		
		
		$position_info = PositionModel::getPositionList("pName","where id={$in_positionId}");
		if(empty($position_info[0]['pName'])){
			self::$errCode = 415;
			self::$errMsg = "上架仓位不能为空";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
        
        $time    =  time();  //添加时间戳
        $status  =  1;  //临时表数据状态
        
        //更新旧erp库存
		$update_onhand = CommonModel::updateOnhand($sku,$return_num,$userCnName,$position_info[0]['pName'], '', $time, $key);
		if($update_onhand['errCode'] != 200){
			self::$errCode = 415;
			self::$errMsg  = "更新旧erp库存失败";
            $status        = 0;
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s, %s, %s \r\n", $sku, $date, self::$errMsg,
                                            is_array($update_onhand) ? json_encode($update_onhand) : $update_onhand, $sku, $return_num, $userCnName, $position_info[0]['pName']);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
            
            //将临时数据存入上架临时表中状态
            whShelfModel::insertFailSku($sku, $return_num, $_SESSION['userId'], $status, $key);
            TransactionBaseModel :: commit();
			return false;
		}
        $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s, %s, %s \r\n", $sku, $date, '更新旧erp库存成功',
                                            is_array($update_onhand) ? json_encode($update_onhand) : $update_onhand, $sku, $return_num, $userCnName, $position_info[0]['pName']);
        write_log($log_file, $log_info);
        
        /** 完结采购订单**/
        $purInfo =  CommonModel::endPurchaseOrder($sku, $return_num, $time, $key);             //api获取采购订单处理情况
		if( !isset($purInfo['errorCode']) || $purInfo['errorCode'] != 0){
		    $status        = 0;  //临时表数据状态
			self::$errCode = 405;
			self::$errMsg  = "完结采购订单出错,上架失败";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            is_array($purInfo) ? json_encode($purInfo) : $purInfo, $sku, $return_num);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
            //将临时数据存入上架临时表中状态
            whShelfModel::insertFailSku($sku, $return_num, $_SESSION['userId'], $status, $key);
            TransactionBaseModel :: commit();
			return false;
		}
        
        $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, '完结采购订单成功',
                                            is_array($purInfo) ? json_encode($purInfo) : $purInfo, $sku, $return_num);
        write_log($log_file, $log_info);
        
        //将临时数据存入上架临时表中状态
        //$where  =   array('sku'=>$sku, 'num'=>$return_num, 'rand_key'=>$key);
        if(!empty($temp_record)){ //有失败记录则更新临时表状态
            $update =   array('status'=>1);
            whShelfModel::updateFailSku($key, $update);
        }
		
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]上架成功！";
		return true;
	}
	
	//订单确认
	function  act_allSure(){
		$userId  = $_SESSION['userId'];
		$id_arr  = $_POST['id'];
		$f_count = count($id_arr);
		$id      = implode(',',$id_arr);		
		$where   = "where id in(".$id.") and isConfirm=0";
		$record_list = OmAvailableModel::getTNameList("wh_abnormal_purchase_orders","*",$where);
		$s_count 	 = count($record_list);
		if($f_count!=$s_count){
			self::$errCode = "401";
			self::$errMsg  = "当前包含有不用审核的订单，请确认！";
			return false;
		}
		$updata = OmAvailableModel::updateTNameRow("wh_abnormal_purchase_orders","set isConfirm=1,confirmUserId='{$userId}'",$where);
		if($updata){
			return true;
		}else{
			self::$errCode = "402";
			self::$errMsg  = "确认失败！";
			return false;
		}
		
	}
	
	/*
	*料号入库
	*/
	public function act_whSkuShelf(){
		//print_r($_POST);
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
		
		if($nums<1){
			self::$errCode = 403;
			self::$errMsg  = "入库数量不能小于1";
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
		$userId = $_SESSION['userId'];
		$in_positionId = 0;
		TransactionBaseModel :: begin();

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
			$set   = "set pId='$skuId',positionId='$positionId',nums='$nums',type='$type'";
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
		$where = "where sku='{$sku}'";
		$info  = whShelfModel::updateStoreNumOnly($nums,$where);
		if(!$info){
			self::$errCode = 412;
			self::$errMsg = "更新总库存失败！";
			TransactionBaseModel :: rollback();
			return false;
			
		}
		
		/**** 插入出入库记录 *****/
		$paraArr = array(
			'sku'     	 => $sku,
			'amount'  	 => $nums,
			'positionId' => $in_positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => 14,
			'userId'	 => $userId,
			'reason'	 => '退货料号入库',
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 413;
			self::$errMsg = "插入出入库记录失败！";
			TransactionBaseModel :: rollback();
			return false;
			
		}
		
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]入库成功！";
		return true;
	}

	/*
	*邮局退回入库
	*/
	public function act_whReturnSkuShelf(){
		//print_r($_POST);
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
		
		$where = "where sku='{$sku}' and status=0";
		$tallying_list  = PostReturnModel::getReturnList("*",$where);
		if(empty($tallying_list)){
			self::$errCode = 402;
			self::$errMsg  = "无该料号退回信息";
			return false;
		}else{
			$tallying_num = 0;
			foreach($tallying_list as $tallying){
				$tallying_num += $tallying['ichibanNums']-$tallying['shelvesNums'];
			}
			if($nums>$tallying_num){
				self::$errCode = 402;
				self::$errMsg  = "上架数不能大于qc良品数[{$tallying_num}]";
				return false;
			}
		}
		
		if($nums<1){
			self::$errCode = 403;
			self::$errMsg  = "入库数量不能小于1";
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
		$userId = $_SESSION['userId'];
		$in_positionId = 0;
		TransactionBaseModel :: begin();

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
			$set   = "set pId='$skuId',positionId='$positionId',nums='$nums',type='$type'";
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
		$where = "where sku='{$sku}'";
		$info  = whShelfModel::updateStoreNumOnly($nums,$where);
		if(!$info){
			self::$errCode = 412;
			self::$errMsg = "更新总库存失败！";
			TransactionBaseModel :: rollback();
			return false;
			
		}
		
		/**** 插入出入库记录 *****/
		$paraArr = array(
			'sku'     	 => $sku,
			'amount'  	 => $nums,
			'positionId' => $in_positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => 14,
			'userId'	 => $userId,
			'reason'	 => '退货料号入库',
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 413;
			self::$errMsg = "插入出入库记录失败！";
			TransactionBaseModel :: rollback();
			return false;
			
		}
		
		//更新邮局退回记录状态
		$where = "where sku='{$sku}' and status=0 and ichibanNums>0";
    	$list  = PostReturnModel::getReturnList("*",$where);
		$i = 0;
		while($list[$i]&&$nums){
			$need_nums = $list[$i]['ichibanNums']-$list[$i]['shelvesNums'];
			if($nums >= $need_nums){
				//更改状态
				$msg = PostReturnModel::updateReturnStatus($list[$i]['id'],$need_nums);
				if(!$msg){
					self::$errCode = 413;
					self::$errMsg  = "更新邮局退回记录状态失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
				$nums = $nums-$need_nums;
			}else{
				$msg = PostReturnModel::updateReturnShelfNum($list[$i]['id'],$nums);
				if(!$msg){
					self::$errCode = 414;
					self::$errMsg  = "更新邮局退回记录已上架数量失败！";
					TransactionBaseModel :: rollback();
					return false;
				}
				$nums = 0;
			}
			$i++;
		}
		
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]入库成功！";
		return true;
	}
    
    /**
     * whShelfAct::act_whPackageShelf()
     * 包材入库
     * @return void
     */
    public function act_whPackageShelf(){
        $userCnName = $_SESSION['userCnName'];
        $sku    =   trim($_POST['sku']) ? trim($_POST['sku']) : '';
        $nums   =   intval(trim($_POST['nums']));
        $log_file   =   'packageWheself/'.date('Y-m-d').'.txt';
        $date       =   date('Y-m-d H:i:s');
        if(!$sku){
            self::$errCode = 001;
			self::$errMsg  = "sku不能为空";
			return false;
        }
        
        if(!preg_match("/^MT\d+$/", $sku)){
            self::$errCode = 002;
			self::$errMsg  = "该模块只能入库包材!";
			return false;
        }
        
        if(!$nums){
            self::$errCode = 003;
			self::$errMsg  = "数量不能为空";
			return false;
        }
        
        $where = "where sku='{$sku}' and tallyStatus=0 and entryStatus = 0 and is_delete = 0";
		$tallying_list  = packageCheckModel::selectList($where);
		if(empty($tallying_list)){
			self::$errCode = 004;
			self::$errMsg  = "无该料号点货信息";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}else{
			$tallying_num = 0;
			foreach($tallying_list as $tallying){
				$tallying_num += $tallying['ichibanNums']-$tallying['shelvesNums'];
			}
			if($nums>$tallying_num){
				self::$errCode = 005;
				self::$errMsg  = "上架数不能大于点货良品数[{$tallying_num}]";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
                write_log($log_file, $log_info);
				return false;
			}
		}
		
		if($nums<1){
			self::$errCode = 006;
			self::$errMsg  = "上架数量不能小于1";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}
		
		$where   = " where sku = '{$sku}'";
		$skuinfo = whShelfModel::selectSku($where);
		if(empty($skuinfo)){
			self::$errCode = 007;
			self::$errMsg  = "无该料号信息";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s \r\n", $sku, $date, self::$errMsg);
            write_log($log_file, $log_info);
			return false;
		}else{
			$skuId 		= $skuinfo['id'];
			$purchaseId = $skuinfo['purchaseId'];
		}        
		
		$return_num       = $nums;
		$in_positionId    = 0;
		$userId           = $_SESSION['userId'];
		TransactionBaseModel :: begin();
		
		/**** 更新总库存 *****/		
		$actualStock = whShelfModel::selectSkuNums($sku);
		if(!empty($actualStock)){
			$where = "where sku='{$sku}' and storeId=1";
			$info  = whShelfModel::updateStoreNum($nums,$where);
			if(!$info){
				self::$errCode = 008;
				self::$errMsg = "更新总库存失败！";
                $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $info, $nums, $where);
                write_log($log_file, $log_info);               
				TransactionBaseModel :: rollback();
				return false;
				
			}
            write_log($log_file, date('Y-m-d H:i:s').'更新总库存成功！'."{$sku}\r\n");
		}else{
			$info = whShelfModel::insertStore($sku,$nums,1);
			if(!$info){
				self::$errCode = 009;
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
			'positionId' => $in_positionId,
			'purchaseId' => $purchaseId,
			'ioType'	 => 2,
			'ioTypeId'   => 13,
			'userId'	 => $userId,
			'reason'	 => '上架入库',
		);
		$record = CommonModel::addIoRecores($paraArr);     //出库记录
		if(!$record){
			self::$errCode = 010;
			self::$errMsg = "插入出入库记录失败！";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s \r\n", $sku, $date, self::$errMsg,
                                            $record, json_encode($paraArr));
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
        write_log($log_file, date('Y-m-d H:i:s').'插入入库记录成功！'."{$sku}\r\n");
		
		//更新点货记录状态
		$where = "where sku='{$sku}' and tallyStatus=0 and ichibanNums>0 and is_delete=0";
    	$list  = packageCheckModel::selectList($where);
		$i = 0;
		while($list[$i]&&$nums){
			$need_nums = $list[$i]['ichibanNums']-$list[$i]['shelvesNums'];
			if($nums >= $need_nums){
				//更改状态
				$msg = whShelfModel::updateTallyStatus($list[$i]['id'],$need_nums);
				if(!$msg){
					self::$errCode = 011;
					self::$errMsg  = "更新点货记录状态失败！";
                    $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $msg, $list[$i]['id'], $need_nums);
                    write_log($log_file, $log_info);
					TransactionBaseModel :: rollback();
					return false;
				}
                write_log($log_file, date('Y-m-d H:i:s').'更新点货记录状态成功！'."{$sku}\r\n");
				$nums = $nums-$need_nums;
			}else{
				$msg = whShelfModel::updateShelfNum($list[$i]['id'],$nums);
				if(!$msg){
					self::$errCode = 012;
					self::$errMsg  = "更新点货记录已上架数量失败！";
                    $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            $msg, $list[$i]['id'], $nums);
                    write_log($log_file, $log_info);
					TransactionBaseModel :: rollback();
					return false;
				}
                write_log($log_file, date('Y-m-d H:i:s').'更新点货记录已上架数量成功！'."{$sku}\r\n");
				$nums = 0;
			}
			$i++;
		}
        $time    =  time();  //添加时间戳
        /*$purInfo = CommonModel::endPurchaseOrder($sku,$return_num, $time);             //api获取采购订单处理情况
		if( !isset($purInfo['errorCode']) || $purInfo['errorCode'] != 0){
			self::$errCode = 405;
			self::$errMsg  = "完结采购订单出错,上架失败";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s \r\n", $sku, $date, self::$errMsg,
                                            json_encode($purInfo), $sku, $return_num);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
        write_log($log_file, date('Y-m-d H:i:s').'完结采购订单成功'."{$sku}\r\n");*/
        
        //更新旧erp库存
		$update_onhand = CommonModel::updateOnhand($sku,$return_num,$userCnName, 0, '', $time);
		if($update_onhand['errCode'] != 200){
			self::$errCode = 415;
			self::$errMsg = "更新旧erp库存失败";
            $log_info      = sprintf("料号：%s, 时间：%s,错误信息:%s,返回值：%s, 参数:%s, %s, %s \r\n", $sku, $date, self::$errMsg,
                                            is_array($update_onhand) ? json_encode($update_onhand) : $update_onhand, $sku, $return_num, $userCnName);
            write_log($log_file, $log_info);
			TransactionBaseModel :: rollback();
			return false;
		}
		write_log($log_file, date('Y-m-d H:i:s').'更新旧erp库存成功'."{$sku}\r\n");
		
		TransactionBaseModel :: commit();
		self::$errMsg = "料号[{$sku}]上架成功！";
		return true;
        
    }
    
    /**
     * whShelfAct::judge_is_new()
     * 判断该料号是否是新品
     * @param string $sku 料号
     * @return bool $return
     */
    function judge_is_new($sku){
        $return =   FALSE;
        $sku    =   trim($sku);
        if($sku){
            /*$spu    =   explode('_', $sku);
            $spu    =   $spu[0];
            //查询入库上架记录
            $spu_records=   WhIoRecordsModel::selectIoRecords('id', array('sku'=>$spu ,'ioTypeId in'=>array(13,33)));
            $return     =   empty($relation) ? TRUE : FALSE;
            //var_dump($return);exit;
            if($return){ //SPU没有入库记录则*/
                //检测sku是否有入库记录
                $sku_records    =   WhIoRecordsModel::selectIoRecords('id', array('sku'=>$sku ,'ioTypeId in'=>array(13,33)));
                //print_r($sku_records);exit;
                $return         =   empty($sku_records) ? TRUE : FALSE;
            //}
        }
        return $return;
    }
}
?>
