<?php
class packageCheckAct extends Auth{
	public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
	/*
     * 点货录入
     */
	public function act_packageCheck(){
		//print_r($_POST);
		//ob_start();
		
		$infoarr      =   $_POST['infoarr'];
        //print_r($infoarr);exit;
		$userId       =   $_SESSION['userId'];
		$checkUser    =   intval(trim($_POST['checkUser']));
        $storeId      =   intval(trim($_POST['storeId']));
        $storeId      =   $storeId ? $storeId : 1;
		if(empty($infoarr) || !$checkUser){
			self::$errCode = 201;
			self::$errMsg = "点货人或点货详情为空！请确认！";
			return false;
		}
		if(empty($userId)){
			self::$errCode = 202;
			self::$errMsg = "登录超时，请先登录！";
			return false;
		}
		$message = "";
		$sku_arr = array();
		$time    = date('YmdHis',time()); //批次号时间
        $rand_arr= range(10, 2000);  //批次号随机数组
		
		//foreach($infoarr as $key => $value){
//			$info = explode("*",$value);
//			$sku 	 	 = trim($info[0]);
//			$skuinfo	 = packageCheckModel::selectSku($sku);
//			if(!$skuinfo || empty($skuinfo)){
//				self::$errCode = 401;
//				self::$errMsg  = "此sku {$sku} 不存在";
//
//				return false;
//			}
//		}
		
		OmAvailableModel :: begin();
		foreach($infoarr as $key => $value){
            $insertArr      =   array();  //插入数据初始化
			$info = explode("*",$value);
			if(!empty($info[0])&&!empty($info[1])){
				$entryStatus = 0;
				$sku 	 	 = trim($info[0]);
				$amount 	 = trim($info[1]);
                if($amount <=0){
                    self::$errCode = 401;
				    self::$errMsg  = "此{$sku} :数量必须大于0";
                    return FALSE;
                }
				$skuinfo	 = packageCheckModel::selectSku($sku);  //检测sku是否存在
				if(!$skuinfo){
					self::$errCode = 401;
    				self::$errMsg  = "此sku {$sku} 不存在";
                    OmAvailableModel::rollback();
    				return false;
				}else{
					if(array_key_exists($sku,$sku_arr)){
						$sornum 	   = $sku_arr[$sku]+1;
						$sku_arr[$sku] = $sornum;
					}else{
						$sornum 	 = 1;
						$sku_new_arr = array($sku=>1);
						$sku_arr     = array_merge($sku_arr,$sku_new_arr);
					}
					$skuid      = $skuinfo[0]['id'];
					$purchaseId = $skuinfo[0]['purchaseId'];
					$insertArr['batchNum'] =   $time.$skuid.$sornum.array_shift($rand_arr);
				}
                
                //判断是否是包材料号
                $is_package =   preg_match("/^MT\d+$/", $sku);
                if($is_package){
                    $checkOnWaySku  =   0; //包材料号不许推送采购
                    $insertArr['ichibanNums']   =   $amount;
                    $insertArr['ichibanTime']   =   time(); 
                }else{
                    //验证sku在途数量是否足够
                    //print_r($amount);exit;
                    $checkOnWaySku = checkSkuPackage($sku, $amount);
                }
				if($checkOnWaySku==0){
					$skulocation = packageCheckModel::selectStore($sku, $storeId);
					if(!empty($skulocation)){
						$msg = packageCheckModel::updateStore($sku,$amount, $storeId);
					}else{
						$msg = packageCheckModel::insertStore($sku,$amount, $storeId);
					}
					if(!$msg){
						self::$errCode = 402;
						self::$errMsg  = "sku {$sku} 更新库存失败";
						OmAvailableModel :: rollback();
						return false;
					}
				}else{
					$entryStatus = 1;
				}
                
                $insertArr['sku']   =   $sku;
                $insertArr['num']   =   $amount;
                $insertArr['tallyUserId']   =   $checkUser;
                $insertArr['entryUserId']   =   $userId;
                $insertArr['entryTime']     =   time();
                $insertArr['purchaseId']    =   $purchaseId;
                $insertArr['storeId']       =   $storeId;
                $insertArr['entryStatus']   =   $entryStatus;
				
				$queryinfo   = packageCheckModel::insertRecord($insertArr);
				if($queryinfo){	
					if($checkOnWaySku==1){
						$message .= "<font color='#FF0000'>料号{$sku} 点货数量{$amount} 异常，需到异常录入确认数量并推送采购系统！</font><br>";
					}else{
						$message .= "料号{$sku} 点货数量{$amount} 录入系统成功！<br>";
					}
				}else{
					$arr['errCode'] = 403;
					OmAvailableModel :: rollback();
					$message .= "料号{$sku} 点货数量{$amount} 录入系统失败！请确认！<br>";
				}
			}
		}
		OmAvailableModel :: commit();
		
		return urlencode($message);
	}
	
	/*
     * 点货调整
     */
	public function act_adjustPackageCheck(){ 
		$userCnName = $_SESSION['userCnName'];
		$userId  = $_SESSION['userId'];
		$info    = isset($_POST['info'])?$_POST['info']:"";
		$infoarr = explode("*",$info);		
		$infoarr = array_filter($infoarr);
		$ids     = array();
		/*
		foreach($infoarr as $value){
			$value_arr   = explode("_",$value);
			$id 		 = $value_arr[0];
			$ids[]		 = $id;
		}
		
		$idstr       = implode(',',$ids);
		$where		 = "where id in($idstr) and shelvesNums!=0";
		$lists		 = packageCheckModel::selectList($where);
		
		if(!empty($lists)){
			self::$errCode=401;
			self::$errMsg = "调整录入有已上架，请用pda调整";
			return false;
		}
		*/
		OmAvailableModel :: begin();
		foreach($infoarr as $key => $value){
			$entryStatus = 0;
			$value_arr   = explode("_",$value);
			$id 		 = $value_arr[0];
			$num 		 = $value_arr[1];
			$where		 = "where id={$id}";
			$list		 = packageCheckModel::selectList($where);
			$beforeNum	 = $list[0]['num'];	
			$sku		 = $list[0]['sku'];	
			$ichibanNums = $list[0]['ichibanNums'];
			$shelvesNums = $list[0]['shelvesNums'];
			$now_num	 = $beforeNum+$num;
            /** 判断点货调整后数量是否为负数**/
            if($now_num < 0){
                self::$errCode= 203;
				self::$errMsg = "调整后数量必须大于等于0!";
                return FALSE;
            }
            
            /** 已打标但QC未返回良品不许修改**/
            if( $list[0]['printTime'] && !$list[0]['ichibanTime'] ){
                self::$errCode= 204;
				self::$errMsg = "请等待QC返回良品后再进行点货调整!";
                return FALSE;
            }
            
            /** add by Gary(yym) 添加点货调整时QC是否已返回良品数判断**/
            if($ichibanNums > 0 && ($beforeNum < $now_num) ){ //QC已返回良品数
                self::$errCode= 205;
				self::$errMsg = "已有良品数且调整后点货数大于调整前点货数，不许调整!";
                return FALSE;
            }
            /** end**/
            
            /** 已有上架数则不允许修改(除了许振铠)**/
            if($shelvesNums > 0){ //已有上架数
                if(in_array($_SESSION['userId'], array(318, 644))){
                     if($now_num < $shelvesNums){
                        self::$errCode= 212;
        				self::$errMsg = "调整后的数量不能小于已上架数量!";
                        return FALSE;
                     }
                     $checkOnWaySku   = 0; //无需判断在途数量
                }else{
                    self::$errCode= 206;
    				self::$errMsg = "该料号已上架，不许调整!";
                    return FALSE;
                }
            }
            /** end**/
            
            
			/** 判断点货数量是否符合在途数量**/
            if(!isset($checkOnWaySku)){ //已上架点货调整则跳过在途检测
                $postNum       = $list[0]['entryStatus'] != 0 ? $beforeNum : 0;  //如果不是正常点货状态，则把点货数传递给判断函数
    			$checkOnWaySku = checkSkuPackage($sku, $num, $postNum);
            }
            if($checkOnWaySku  == 0){
				$u_num = $num;
			}else{
				$entryStatus = 1;
				$u_num = -$beforeNum;
			}
            
			$updateinfo = packageCheckModel::updateRecord($id,$num,$entryStatus);
			if(!$updateinfo){
				self::$errCode= 207;
				self::$errMsg = "更新点货记录失败！";
				OmAvailableModel::rollback();
				return FALSE;
			}
			
			$insertinfo = packageCheckModel::insertAdjustRecord($id,$num,$beforeNum,$userId);			
			if(!$insertinfo){
				self::$errCode= 208;
				self::$errMsg = "插入调整记录失败！";
				OmAvailableModel::rollback();
				return FALSE;
			}
			
			$updatestore = packageCheckModel::updateStore($sku,$u_num);
			if(!updatestore){
				self::$errCode= 209;
				self::$errMsg = "更新仓库到货库存失败！";
				OmAvailableModel::rollback();
				return FALSE;
			}
			
			if($ichibanNums!=0 && $ichibanNums!=NULL){
				$updateIchibanNums = packageCheckModel::updateIchibanNums($num,$id);
                if($shelvesNums != 0){ //存在上架记录
                    if($now_num == $shelvesNums){ //更改后点货数如果等于上架数
                        $info   =   whShelfModel::updateTallyStatus($id, 0); //完结点货记录
                        if(!$info){
                            self::$errCode= 210;
            				self::$errMsg = "完结点货记录失败！";
            				OmAvailableModel::rollback();
            				return FALSE;
                        }
                    }
                }
                /** 暂时取消点货调整同步老ERP库存**/
				/*if($shelvesNums!=0){
					$updateshelvesNums = packageCheckModel::updateShelvesNums($num,$sku,$id);
					if(!$updateshelvesNums){
						self::$errCode= 210;
						self::$errMsg = "更新点货记录失败！";
						OmAvailableModel::rollback();
						return FALSE;
					}
					$update_onhand = CommonModel::adjustOut($sku,$num,$userCnName);
					if($update_onhand==0){
						self::$errCode = 211;
						self::$errMsg  = "更新旧erp库存失败";
						OmAvailableModel :: rollback();
						return FALSE;
					}
				}*/
			}
		}
		OmAvailableModel::commit();
		return TRUE;
	}
	
	/*
     * 异常点货调整
     */
	public function act_adjustAbnormal(){
		$userId  = $_SESSION['userId'];
		$info = isset($_POST['info'])?$_POST['info']:"";
		$infoarr = explode("*",$info);
		
		$infoarr = array_filter($infoarr);
		OmAvailableModel :: begin();
		foreach($infoarr as $key => $value){
			$entryStatus = 0;
			$value_arr   = explode("_",$value);
			$id		  	 = $value_arr[0];
			$num 	 	 = $value_arr[1];
			$where	  	 = "where id={$id}";
			$list 	  	 = packageCheckModel::selectList($where);
            
            /** 如果异常已确认 不允许点货调整 add BY GARY**/
            if($list[0]['entryStatus'] == 2){
                self::$errCode=205;
				self::$errMsg = "异常已确认，不允许点货调整";
				return false;
            }
            /** end**/
            
			$beforeNum	 = $list[0]['num'];	
			$sku      	 = $list[0]['sku'];	
			$now_num  	 = $beforeNum+$num;
            
            $ichibanNums = $list[0]['ichibanNums'];  //良品数
			$shelvesNums = $list[0]['shelvesNums'];  //上架数
            /** 判断点货调整后数量是否为负数**/
            if($now_num <= 0){
                self::$errCode=203;
				self::$errMsg = "调整后数量必须大于0!";
                return false;
            }
            /** 已上架情况下不许调整**/
            if( $shelvesNums ){
                self::$errCode=203;
				self::$errMsg = "料号已有上架数,不能调整!";
                return false;
            }
            /** 判断有良品的情况下调整数量只能小于原先点货数量**/
            if( $ichibanNums && ($now_num > $beforeNum) ){
                self::$errCode=203;
				self::$errMsg = "良品存在时，点货调整后数量必须小于原点货数量!";
                return false;
            }
            if($ichibanNums){
                $updateIchibanNums = packageCheckModel::updateIchibanNums($num,$id);  //更新良品数
                if(!$updateIchibanNums){
                    self::$errCode=205;
    				self::$errMsg = "更新良品数失败!";
    				OmAvailableModel::rollback();
    				return false;
                }
            }
            
			/** 判断点货数量是否符合在途数量**/
			$checkOnWaySku = checkSkuPackage($sku, $num, $beforeNum);
			//$checkOnWaySku = 0;
			if($checkOnWaySku==0){
				$updatestore = packageCheckModel::updateStore($sku,$now_num);
				if(!updatestore){
					self::$errCode=205;
					self::$errMsg = "更新到货库存失败！";
					OmAvailableModel::rollback();
					return false;
				}
			}else{
				$entryStatus = 1;
			}			
			$updateinfo = packageCheckModel::updateRecord($id,$num,$entryStatus);
			if(!$updateinfo){
				self::$errCode=203;
				self::$errMsg = "更新点货记录失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			$insertinfo = packageCheckModel::insertAdjustRecord($id,$num,$beforeNum,$userId);			
			if(!$insertinfo){
				self::$errCode=204;
				self::$errMsg = "插入调整记录失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
		}
		OmAvailableModel::commit();
		return true;
	}
	
	//异常录入推送采购系统
	public function act_sureAb(){
		$id_arr    = $_POST['id'];
		$push_info = CommonModel::pushAbnormalPrint($id_arr);
		if($push_info){
			$update = packageCheckModel::updateEntryStatus($id_arr,$_SESSION['userId']);
			if($update){
				self::$errMsg  = "推送成功！";
				return true;
			}else{
				self::$errCode = "402";
				self::$errMsg  = "更新录入状态失败，请不要重复提交，并联系it！";
				return false;
			}
		}else{
			self::$errCode = "401";
			self::$errMsg  = "推送失败，请重试！";
			return false;
		}
	}
	
	
	//pda数量调整
	public function act_pdaAdjust(){
		$userCnName = $_SESSION['userCnName'];
		$userId  = $_SESSION['userId'];
		$groupid = trim($_POST['groupid']);
		$num 	 = intval(trim($_POST['num']));
		
		if($num==0){
			self::$errCode = "401";
			self::$errMsg  = "请输入正数";
			return false;
		}
		$list = packageCheckModel::getSKUByGroupId($groupid);
		if($list){
			OmAvailableModel :: begin();
			$entryStatus = 0;
			$nums 		 = -$num;	
			$id 		 = $list[0]['id'];
			$batchNum 	 = $list[0]['batchNum'];
			$sku 		 = $list[0]['sku'];
			$beforeNum	 = $list[0]['num'];
			$ichibanNums = $list[0]['ichibanNums'];
			$shelvesNums = $list[0]['shelvesNums'];
			$now_num	 = $beforeNum-$num;
			$u_num 		 = -$num;
            /** 判断点货调整后数量是否为负数**/
            if($now_num <= 0){
                self::$errCode=203;
				self::$errMsg = "调整后数量必须大于0!";
                return false;
            }
            /** 已上架情况下不许调整**/
            if( $shelvesNums ){
                self::$errCode=203;
				self::$errMsg = "料号已有上架数,不能调整!";
                return false;
            }
            /** 判断有良品的情况下调整数量只能小于原先点货数量**/
            if( $ichibanNums && ($now_num > $beforeNum) ){
                self::$errCode=203;
				self::$errMsg = "良品存在时，点货调整后数量必须小于原点货数量!";
                return false;
            }
            
			CommonModel::adjustPrintNum($batchNum,$nums);      //去除qc数量
			
			if($shelvesNums==0){
				//释放采购hold住数量
				$delCheckOnWaySku = CommonModel::checkOnWaySkuNum($sku,$beforeNum,2);
				//验证sku在途数量是否足够
				$checkOnWaySku = CommonModel::checkOnWaySkuNum($sku,$now_num,1);
				if($checkOnWaySku==0){
					$u_num = -$num;
				}else{
					$entryStatus = 1;
					$u_num = -$beforeNum;
				}
			}

			$updateinfo = packageCheckModel::updateRecord($id,$nums,$entryStatus);
			if(!$updateinfo){
				self::$errCode=203;
				self::$errMsg = "更新点货记录失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			$insertinfo = packageCheckModel::insertAdjustRecord($id,$nums,$beforeNum,$userId);			
			if(!$insertinfo){
				self::$errCode=204;
				self::$errMsg = "插入点货调整记录失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			$updatestore = packageCheckModel::updateStore($sku,$u_num);
			if(!updatestore){
				self::$errCode=205;
				self::$errMsg = "更新总库存失败！";
				OmAvailableModel::rollback();
				return false;
			}
			
			if($ichibanNums!=0 && $ichibanNums!=NULL){
				$updateIchibanNums = packageCheckModel::updateIchibanNums($nums,$id);
				if($shelvesNums!=0){
					$updateshelvesNums = packageCheckModel::updateShelvesNums($nums,$sku,$id);
					if(!$updateshelvesNums){
						self::$errCode=206;
						self::$errMsg = "更新上架库存失败！";
						OmAvailableModel::rollback();
						return false;
					}
					$update_onhand = CommonModel::adjustOut($sku,$nums,$userCnName);
					if($update_onhand==0){
						self::$errCode = 206;
						self::$errMsg = "更新旧erp库存失败";
						OmAvailableModel :: rollback();
						return false;
					}
				}
			}
			self::$errMsg  = "调整成功";
			OmAvailableModel::commit();
			return true;
			
		}else{
			self::$errCode = "402";
			self::$errMsg  = "该分组不存在";
			return false;
		}
	}
	
	//删除异常
	public function act_delOdd(){
		$id_arr    = $_POST['id'];
		foreach($id_arr as $key=>$id){
			$ent_list = packageCheckModel::selectList("where id={$id}");
            if(empty($ent_list)){
                unset($id_arr[$key]);
                continue;
            }
            if($ent_list[0]['entryStatus'] == 2){
                self::$errCode = "400";
				self::$errMsg  = "已推送采购的不许删除!";
                unset($id_arr[$key]);
				return false;
				break;
            }
            /** 已打标的异常不准删除 GARY**/
            if($ent_list[0]['printTime']){
                self::$errCode = "401";
				self::$errMsg  = "已打标,不能删除!";
				return false;
				break;
            }
		}

		$update = packageCheckModel::updateOdd($id_arr);
        /** 添加删除记录日志**/
        $log_file   =   'delete_package/'.date('Ymd').'.txt';   //日志文件路径
        $date       =   date('Y-m-d H:i:s');
        $log_data   =   sprintf('时间：%s, ids:%s, 操作人:%s'." \r\n", $date, is_array($id_arr) ? json_encode($id_arr) : $id_arr, $_SESSION['userCnName']);
        write_log($log_file, $log_data);
        /** end**/
		if($update){
			self::$errMsg  = "删除成功！";
			return true;
		}else{
			self::$errCode = "402";
			self::$errMsg  = "删除失败！";
			return false;
		}

	}
	
	/*
     * 贴标报表导出
     */
	public function act_export(){
		$checkUser = $_GET['checkUser'];
		$sku       = $_GET['sku'];
		$startdate = $_GET['startdate'];
		$enddate   = $_GET['enddate'];

		if(empty($checkUser)&&empty($sku)&&empty($startdate)&&empty($enddate)){
			echo "请选择导出条件";exit;
		}
		
		if(!empty($checkUser)){
			$where[] = "tallyUserId='{$checkUser}'";
		}
		
		if(!empty($sku)){
			$where[] = "sku = '{$sku}'";
		}
		if(!empty($startdate)){
			$start = strtotime($startdate);
			$where[] = "entryTime >{$start}";
		}
		if(!empty($enddate)){
			$end = strtotime($enddate);
			$where[] = "entryTime <{$end}";
		}
		$where = implode(" AND ",$where);
		$where = " where is_delete=0 and entryStatus=0 and ".$where;
		$lists = packageCheckModel::selectList($where);	
		
		$excel  = new ExportDataExcel('browser', "pointKpiExport".date('Y-m-d').".xls"); 
		$excel->initialize();
        /** edit by Gary**/
		$tharr = array("点货人","点货时间","SKU","数量","良品数","上架数","批次号",'打标时间', '贴标时间', '检测时间', '上架时间', '备注');
		$excel->addRow($tharr);
		
		foreach($lists as $list){
			$user        = getUserNameById($list['tallyUserId']);
			$time        = date('Y-m-d H:i:s',$list['entryTime']);
			$sku         = $list['sku'];
			$num         = $list['num'];
			$ichibanNums = $list['ichibanNums'];
			$shelvesNums = $list['shelvesNums'];
			$batchNum    = $list['batchNum'];
            
            //新增打标、贴标、检测、上架时间
            $printTime   = $list['printTime'] ? date('Y-m-d H:i:s', $list['printTime']) : '无';
            $pasteTime   = getSkuTime(array('type' => 'paste', 'tallyList' => $list['id']));
            $checkTime   = $list['ichibanTime'] ? date('Y-m-d H:i:s', $list['ichibanTime']) : '无';
            $inputArray  = array(
                                    'type'       => 'input',
                                    'sku'        => $list['sku'],
                                    'finishTime' => $list['finishTime'],
                                    'shelvesNums'=> $list['shelvesNums']
                                );
            $inputTime   = getSkuTime($inputArray);
            
			$tdarr	  = array($user,$time,$sku,$num,$ichibanNums,$shelvesNums,$batchNum, $printTime, $pasteTime, $checkTime, $inputTime, $list['note']);
        /** end**/
			$excel->addRow($tdarr);	
		}
	
		$excel->finalize();
		exit;
	}
	
	//更新采购系统推送回来重点,仓库系统异常料号更新状态  add by wangminwei 2014-04-03
	public function act_updUnusualSkuStatus(){
		$unOrderIdArr 	= isset($_GET["unOrderIdArr"]) ? $_GET['unOrderIdArr'] : '';
		if(!empty($unOrderIdArr)){
			$rtnData        = PackageCheckModel::updUnusualSkuStatus($unOrderIdArr);
			if($rtnData){
				self::$errCode  = "200";
				self::$errMsg  	= "更新成功！";
				return true;
			}else{
				self::$errCode = "404";
				self::$errMsg  = "更新失败！";
				return false;
			}
		}else{
			self::$errCode = "404";
			self::$errMsg  = "没有传值";
			return false;
		}
	}
    
    public function act_editPackageNote(){
        $id     =   intval(trim($_POST['id']));
        $note   =   trim($_POST['note']);
        //$note   =   iconv('GBK', 'UTF-8', $note);write_log('aa/aaa.txt', $note."\r\n");
        if($id){
            $a      =   packageCheckModel::update_note($id, $note);
            if($a === FALSE){
                self::$errCode = "001";
    			self::$errMsg  = "更新备注失败";
    			return false;
            }else{
                self::$errCode = 0;
    			self::$errMsg  = "更新备注成功";
    			return TRUE;
            }
        }
    }
}
?>