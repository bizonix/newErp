<?php
/**
*类名：IQC检测
*功能：处理产品检测过程
*作者：hws
*
*/
class IqcDetectAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";	
	
	//获取sku技术检测规格
	function  act_getSkuInfo(){
		global $memc_obj;
		$sku_name = '';
		$cate     = '';
		$sku      = $_POST['sku'];
		if(is_numeric($sku)&& $sku>1000000){      //此sku为goods_code
			$goods_codes = WhStandardModel::goods_codeTosku($sku);
			$sku = $goods_codes['sku'];
		}
		//读取数据库
		$categoryStr = "";
		//$time1 = time();
		//SKU信息API接口
		$OmAvailableApiAct = new OmAvailableApiAct();
		$skuInfo = $OmAvailableApiAct->act_getGoodsInfoBySku($sku);
		
		$skuInfo = $skuInfo['data'];
		//$skuInfo       = $memc_obj->get_extral('pc_goods_'.$sku);
		$reSku         = $skuInfo['sku'];//接口返回SKU
		$reSpu         = $skuInfo['spu'];//接口返回SKU
		$skuName       = $skuInfo['goodsName'];//产品描述
		$purchaseId    = $skuInfo['purchaseId'];//采购人ID
		$goodsCategory = $skuInfo['goodsCategory'];//SKU分类path
		
		$qcCategoryListAct = new qcCategoryListAct();
		$where    = 'WHERE is_delete=0 and path= "'.$goodsCategory.'"';
		$categoryList = $qcCategoryListAct->act_getCategoryList('*',$where);
		
		$getCategoryArr = $qcCategoryListAct->act_getCategoryArr();//获取产品类别列表信息
		
		$sampleTypeListId = empty($categoryList) ? 1 : $categoryList[0]['sampleTypeId'];
		
		$qcStandard  	 	 = new qcStandardAct();
		$skuTypeQcArrList  	 = $qcStandard->act_skuTypeQcList('');
		$sampleTypeList      = array();
		$categoryInfo_ids    = array();
		$categoryInfo		 = array();
		foreach($skuTypeQcArrList as $skuTypeQcArrListValue){
			$categoryInfo[$skuTypeQcArrListValue['id']] = $skuTypeQcArrListValue['typeName'];
			$sampleTypeList[$skuTypeQcArrListValue['id']] = $skuTypeQcArrListValue['typeName']."  ".$skuTypeQcArrListValue['describe'];
		}
		
		$goodsCategoryNameArray = explode("-",$goodsCategory);
		$goodsCategoryName = array();
		foreach($goodsCategoryNameArray as $cvalue){
			$goodsCategoryName[] = $getCategoryArr[$cvalue];
		}
		$goodsCategoryNameStr = join('-',$goodsCategoryName);
		
		//$categoryInfo_ids = array_keys($categoryInfo_ids);
		//$categoryStr = $sampleTypeList[$sampleTypeListId];
		
		//$img_return = $OmAvailableApiAct->act_getAllPic($reSpu, 'F');//图片接口
		//var_dump($img_return); exit;
		
		$data = UserCacheModel::qccenterGetErpGoodscount($sku);
		$goods_count = $data['goods_count'];
		/*$goods_location = $data['goods_location'];
		$cguser = $data['cguser'];*/
		
		//$categoryL     = explode('-',$goodsCategory); 
		
		/*foreach($category as $c_key=>$categoryArr){
			if(in_array($categoryL[0],$categoryArr)){
				if($c_key == '1'){
					//SKU分类信息API接口
					$cate			  = 1;
					$goodsCategorySt  = $memc_obj->get_extral('pc_goods_category_'.$goodsCategory);
					$categoryStr      = "{$goodsCategorySt['name']}：功能检测";	
					break;
				}else if($c_key == '2'){
					//SKU分类信息API接口
					$cate			  = 2;
					$goodsCategorySt  = $memc_obj->get_extral('pc_goods_category_'.$goodsCategory);
					$categoryStr    = "{$goodsCategorySt['name']}：尺寸质量检测";
					break;
				}else if($c_key == '3'){
					//SKU分类信息API接口
					$cate			  = 3;
					$goodsCategorySt  = $memc_obj->get_extral('pc_goods_category_'.$goodsCategory);
					$categoryStr    = "{$goodsCategorySt['name']}：对图检测";
					break;
				}
			}else{
				continue;	
			}
		}*/
		
		$where           = "where sku='$sku' and is_delete=0 and sellerId=1 and getUserId='{$_SESSION['sysUserId']}' and detectStatus=1 order by getTime asc limit 1";
		$list            = WhStandardModel::getNowWhList("*",$where);
		$num             = $list[0]['num'];
		$infoid          = $list[0]['id'];
		$goods_location  = $list[0]['location'];
		$cguser          = UserModel::getUsernameById($list[0]['purchaseId']);
		//$goods_count     = 'TEST';
		$where2          = "where sampleTypeId='$sampleTypeListId' and minimumLimit<='$num' and maximumLimit>='$num' limit 1";
		$list2           = DetectStandardModel::getNowStandardList("*",$where2);
		//如果是正常回测，获取待定回测信息备注
		$pendingInfo = PendingProductsModel::getPendingProductsList('backNote', ' WHERE infoId='.$infoid);
		if($pendingInfo){
			$backNote = $pendingInfo[0]['backNote'];
		}
		//$time2 = time();
		//echo ($time2-$time1);
		if($list){
			self::$errCode = "200";
			$info = array();
			$info['info']     = "{$reSku} {$skuName} <br> 产品分类： {$goodsCategoryNameStr} <br>【 来货 {$num} 个，现有库存 ".$goods_count." 个，仓位：".$goods_location.",采购：{$cguser} 】";
			$info['category'] = $sampleTypeListId;
			$info['categoryInfo'] = $categoryInfo;
			$info['sku']      = $reSku;
			$info['spu']      = $reSpu;
			$info['num']      = $num;
			$info['infoid']   = $infoid;
			$info['cate']     = $list2;
			$info['images']   = $img_return['data']['Description'][0];
			$info['backNote'] = !empty($backNote) ? $backNote : '';
			return $info;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "未找到该料号[{$sku}]对于的{$_SESSION['userName']}领取记录!";
			return false;
		}
	}
	
	//获取检测类别信息
	function  act_getTypeInfo(){
		$cate  = $_POST['cate'];
		$num   = $_POST['num'];
		$sku   = $_POST['sku'];
		
		$where = "where sampleTypeId='$cate' and minimumLimit<='$num' and maximumLimit>='$num' limit 1";
		$list  = DetectStandardModel::getNowStandardList("*",$where);
		if($list){
			//$list[0]['sku'] = get_sku_imgName($sku);
			$list[0]['sku'] = $sku;
			return $list;
		}else{
			self::$errCode = "003";
			self::$errMsg  = "未找到该类别的检测标准!";
			return false;
		}
	}
	
	//提交检测
	function  act_subcheck(){
		$data		  = array();
		$id  	      = $_POST['id'];
		$num  	      = $_POST['num'];
		$rtnum  	  = $_POST['rtnum'];//检测退回抽检百分比
		$sku  		  = $_POST['sku'];
		$spu  		  = $_POST['spu'];
		$category	  = $_POST['categoryId'];
		$check_num    = $_POST['check_num'];
		$rejects_num  = $_POST['rejects_num'];
		$bad_reason   = post_check($_POST['bad_reason']);
		$sellerId	  = 1;
		$checkTypeID  = $_POST['checkTypeID'];//QC检测类型ID (入库产品检测,在仓产品检测,退回产品检测)
		$skuTypeCheckID   = $_POST['categoryId'];
		$completeStatus = 1;
		if($check_num == 0){
			self::$errCode = "004";
			self::$errMsg  = "抽检数目为零，请重试";
			return false;
		}
		if($rejects_num){
			$completeStatus = 3;	
		}
		$ichibanNum   = $num - $rejects_num;
		
		$log_file 	 = 'detectSku/'.date('Y-m-d').'.txt';
		$sample_info = WhStandardModel::getNowWhList('*', ' where id = '.$id);
		$msg = userCacheModel::updateWhichibanNum($sample_info[0]['printBatch'],$sku,$ichibanNum);
		$log_info = "更新仓库良品数：".date("Y-m-d H:i:s")."批次：{$sample_info[0]['printBatch']}，SKU:{$sku}，良品数：{$ichibanNum}\r\n";
		$log_info .= "接口返回信息：";
		if(is_array($msg)){
			foreach($msg as $key=>$info){
				$log_info .= sprintf("%s:%s ",$key,$info);
			}
		}else{
			$log_info .= sprintf("%s",$msg);
		}
		$log_info .= "\r\n";
		write_log($log_file,$log_info);
		if(!isset($msg['errCode']) || $msg['errCode'] != 0){
			self::$errCode = "004";
			self::$errMsg  = "推送良品数失败，请重试";
			return false;
		}

		if(!empty($rejects_num)){
			$return_rate = $rejects_num/$check_num*100;
			if($return_rate>$rtnum && $rtnum!=0){              					//退回比例
				$set = "SET infoId='$id',sku='$sku',returnNum='$num',note='$bad_reason',sellerId='$sellerId',startTime=".time();
				$res = ReturnProductsModel::addReturnProducts($set);
				if($res){
					$data = array(
						'detectorId' 	  => $_SESSION['sysUserId'],
						'detectStartTime' => time(),
						'detectStatus'    => 3,
						'completeStatus'  => $completeStatus,
						'typeId'   		  => $checkTypeID,
						'sampleTypeId'    => $category,
						'ichibanNum'      => $ichibanNum
					);
					if(WhStandardModel::update($data,"and id='$id'")){
						$c_data =  array(
							'infoId'		=> $id,
							'sku' 			=> $sku,
							'goodsName' 	=> $sample_info[0]['goodsName'],
							'arrivalNum' 	=> $num,
							'checkNum' 		=> $check_num,
							'rejectsNum' 	=> $rejects_num,
							'rejectsReason' => $bad_reason,
							'checkUser' 	=> $_SESSION['sysUserId'],
							'checkTime'	 	=> time(),
							'skuTypeCheckID'=> $skuTypeCheckID,
							'checkTypeID'  	=> $checkTypeID,
							'sellerId'      => $sellerId
						);
						IqcCompleteInfoModel::insertRow($c_data);
						
					/*	$data = UserCacheModel::qccenterUpdateErpGoodscount($sku,$rejects_num);
						
						$mailList['msg'] = $data;*/
						$purchaser = userModel::getUsernameById($sample_info[0]['purchaseId']);
						//$purchaser = userModel::getUsernameById();
						$from = userModel::getUsernameById($_SESSION['sysUserId']);
						$mailList['content'] = $from."在IQC检测中检测料号 ".$_POST['sku']." 时检测到了：".$_POST['num']."个不良品,请及时做处理!====".date("Y-m-d H:i:s")."====";
						$mailList['from'] = $from;
						$mailList['to'] = $purchaser;
						$mailList['type'] = "email";
						$mailList['callback'] = "";
						
						
						self::$errMsg  = "提交成功，请检测下一料号";
						return $mailList;
						//return true;
					}else{
						self::$errCode = "003";
						self::$errMsg  = "提交失败，请重试";
						return false;
					}
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
			}else{
				$set = "SET infoId='$id',sku='$sku',spu='$spu',defectiveNum='$rejects_num',note='$bad_reason',startTime=".time()." ";
				$res = DefectiveProductsModel::addDefectiveProducts($set);
				if($res){
					$data = array(
						'detectorId' 	  => $_SESSION['sysUserId'],
						'detectStartTime' => time(),
						'detectStatus'    => 3,
						'completeStatus'  => $completeStatus,
						'typeId'   		  => $checkTypeID,
						'sampleTypeId'    => $category,
						'ichibanNum'      => $ichibanNum
					);
					if(WhStandardModel::update($data,"and id='$id'")){
						$c_data =  array(
							'infoId'		=> $id,
							'sku' 			=> $sku,
							'goodsName' 	=> $sample_info[0]['goodsName'],
							'arrivalNum' 	=> $num,
							'checkNum' 		=> $check_num,
							'rejectsNum' 	=> $rejects_num,
							'rejectsReason' => $bad_reason,
							'checkUser' 	=> $_SESSION['sysUserId'],
							'checkTime'	 	=> time(),
							'skuTypeCheckID'=> $skuTypeCheckID,
							'checkTypeID'  	=> $checkTypeID,
							'sellerId'      => $sellerId
						);
						IqcCompleteInfoModel::insertRow($c_data);
						$purchaser = userModel::getUsernameById($sample_info[0]['purchaseId']);
						/*$data = UserCacheModel::qccenterUpdateErpGoodscount($sku,$rejects_num);
						
						
						$mailList['msg'] = $data;*/
						//$purchaser = userModel::getUsernameById(9);
						$from = userModel::getUsernameById($_SESSION['sysUserId']);
						$mailList['content'] = $from."在IQC检测中检测料号 ".$_POST['sku']." 时检测到了：".$_POST['num']."个不良品,请及时做处理!====".date("Y-m-d H:i:s")."====";
						$mailList['from'] = $from;
						$mailList['to'] = $purchaser;
						$mailList['type'] = "email";
						$mailList['callback'] = "";
						
						self::$errMsg  = "提交成功，请检测下一料号";
						return $mailList;
					}else{
						self::$errCode = "003";
						self::$errMsg  = "提交失败，请重试";
						return false;
					}
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
			}		
			
		}else{
			$data = array(
				'detectorId' 	  => $_SESSION['sysUserId'],
				'detectStartTime' => time(),
				'detectStatus'    => 3,
				'completeStatus'  => $completeStatus,
				'typeId'   		  => $checkTypeID,
				'sampleTypeId'    => $category,
				'ichibanNum'      => $num
			);
			if(WhStandardModel::update($data,"and id='$id'")){
				$c_data =  array(
					'infoId'		=> $id,
					'sku' 			=> $sku,
					'goodsName' 	=> $sample_info[0]['goodsName'],
					'arrivalNum' 	=> $num,
					'checkNum' 		=> $check_num,
					'rejectsNum' 	=> $rejects_num,
					'rejectsReason' => $bad_reason,
					'checkUser' 	=> $_SESSION['sysUserId'],
					'checkTime'	 	=> time(),
					'skuTypeCheckID'=> $skuTypeCheckID,
					'checkTypeID'  	=> $checkTypeID,
					'sellerId'      => $sellerId
				);
				IqcCompleteInfoModel::insertRow($c_data);
			
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}
	}
	
	//全部待定
	function  act_allDetermined($category=1,$skuTypeCheckID=1){
		$data		          = array();
		$data['infoId']       = $_POST['id'];
		$id				      = $_POST['id'];
		$data['spu']  		  = $_POST['spu'];
		$data['pendingNum']   = $_POST['num'];
		$data['sku']  		  = $_POST['sku'];
		$data['pendingStatus']= empty($_POST['rewrite_type']) ? 0 : 1;
		$data['note']         = post_check($_POST['wait_reason']);
		$data['modifyStatus'] = $_POST['rewrite_type'];
		$data['startTime']    = time();
		
		$sample_info = WhStandardModel::getNowWhList('*', ' where id = '.$id);
		//之前待定过，然后恢复检测的不能再待定
		$pendingStatus = $sample_info[0]['pendingStatus'];
		if($pendingStatus == 5){
			self::$errCode = '004';
			self::$errMsg  = '此料号已正常回测，不能再待定！';
			return false;
		}
		if($data['modifyStatus'] == 1){
			$modifynum = PendingProductsModel::getModifyNum($_POST['sku']);
			if($modifynum >= 3){
				self::$errCode = "001";
				self::$errMsg  = "此料号本月修改图片已超过三次！请见其全部检测未不良品！";
				return false;
			}
		}
		$log_file = 'skuDetermined/'.date("Y-m-d").'.txt';
		$data['purchaseId']   = $sample_info[0]['purchaseId'];
		$set = "SET ".array2sql($data);
		$res = PendingProductsModel::addPendingProducts($set);
		$log_info = "全部待定：".date("Y-m-d H:i:s")."批次：{$sample_info[0]['printBatch']}，SKU:{$_POST['sku']}，待定数：{$data['pendingNum']}\r\n";
		write_log($log_file,$log_info);
		
		if($res){
			$data = array(
				'detectorId' 	  => $_SESSION['sysUserId'],
				'detectStartTime' => time(),
				'detectStatus'    => 4
			);
			if(WhStandardModel::update($data,"and id='{$_POST['id']}'")){
				$c_data =  array(
							'sku' 			=> $_POST['sku'],
							'goodsName' 	=> $sample_info[0]['goodsName'],
							'arrivalNum' 	=> $_POST['num'],
							'checkNum' 		=> $check_num,
							'rejectsNum' 	=> 0,
							'rejectsReason' => '',//待定原因
							'checkUser' 	=> $_SESSION['sysUserId'],
							'checkTime'	 	=> time(),
							'skuTypeCheckID'=> $skuTypeCheckID,
							'checkTypeID'  	=> $category,
						);
				
				//$useract = new userAct();
				IqcCompleteInfoModel::insertRow($c_data);
				$purchaser = userModel::getUsernameById($sample_info[0]['purchaseId']);
				//$purchaser = userModel::getUsernameById(9);
				$from = userModel::getUsernameById($_SESSION['sysUserId']);
				$mailList['content'] = $from."在IQC检测中检测料号 ".$_POST['sku']." 时将其全部待定！数量：".$_POST['num'].",请及时做处理!====".date("Y-m-d H:i:s")."====";
				//$mailList['content'] = "发错了！不好意思。";
				$mailList['from'] = $from;
				$mailList['to'] = $purchaser;
				$mailList['type'] = "email";
				$mailList['callback'] = "";
				
				self::$errMsg  = "提交成功，请检测下一料号";
				return $mailList;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}else{
			self::$errCode = "003";
			self::$errMsg  = "提交失败，请重试";
			return false;
		}
		//TransactionBaseModel::commit();
	}
	
	//退回处理、库存不良品处理
	function  act_otherCheck(){
		$data		  = array();
		$typeid  	  = $_POST['typeid'];
		$id  	      = $_POST['id'];
		$num  	      = $_POST['num'];
		$sku  		  = $_POST['sku'];
		$spu  		  = $_POST['spu'];
		$check_num    = $_POST['check_num'];
		$rejects_num  = $_POST['rejects_num'];
		$checkTypeID  = $_POST['checkTypeID'];
		
		$bad_reason   = post_check($_POST['bad_reason']);
		$sample_info = WhStandardModel::getNowWhList('*', ' where id = '.$id);
		if($sample_info[0][typeId] != 3){
			self::$errCode = "001";
			self::$errMsg  = "此条记录不是属于退件检测的！请勿用退件检测！";
			return false;
		}
		$ichibanNum = $num-$rejects_num;
		$msg = userCacheModel::updateWhOrderBackichibanNum($sample_info[0]['printBatch'],$sku,$ichibanNum);
		if(!empty($rejects_num)){
			$set = "SET infoId='$id',sku='$sku',spu='$spu',defectiveNum='$rejects_num',note='$bad_reason',startTime=".time()." ";
			$res = DefectiveProductsModel::addDefectiveProducts($set);
			if($res){
				$data = array(
					'detectorId' 	  => $_SESSION['sysUserId'],
					'detectStartTime' => time(),
					'detectStatus'    => 3,
					'typeId'   		  => $typeid,
					'ichibanNum'   	  => $num-$rejects_num 
				);
				if(WhStandardModel::update($data,"and id='$id'")){
					$c_data =  array(
						'sku' 			=> $sku,
						'goodsName' 	=> $sample_info[0]['goodsName'],
						'arrivalNum' 	=> $num,
						'checkNum' 		=> $check_num,
						'rejectsNum' 	=> $rejects_num,
						'rejectsReason' => $bad_reason,
						'checkUser' 	=> $_SESSION['sysUserId'],
						'checkTime'	 	=> time(),
						'skuTypeCheckID'=> 1,
						'checkTypeID'  	=> $checkTypeID,
						'sellerId'		=> 1
					);
					IqcCompleteInfoModel::insertRow($c_data);
					if($checkTypeID == 3){
						$data = UserCacheModel::qccenterUpdateErpGoodscount($sku,$rejects_num);
						
					}
					self::$errMsg  = "提交成功，请检测下一料号";
					return $data;
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}else{
			$data = array(
				'detectorId' 	  => $_SESSION['sysUserId'],
				'detectStartTime' => time(),
				'detectStatus'    => 4,
				'typeId'   		  => $typeid,
				'ichibanNum'   	  => $num 
			);
			if(WhStandardModel::update($data,"and id='$id'")){
				$c_data =  array(
					'sku' 			=> $sku,
					'goodsName' 	=> $sample_info[0]['goodsName'],
					'arrivalNum' 	=> $num,
					'checkNum' 		=> $check_num,
					'rejectsNum' 	=> $rejects_num,
					'rejectsReason' => $bad_reason,
					'checkUser' 	=> $_SESSION['sysUserId'],
					'checkTime'	 	=> time(),
					'skuTypeCheckID'=> 1,
					'checkTypeID'  	=> $checkTypeID,
					'sellerId'		=> 1
				);
				IqcCompleteInfoModel::insertRow($c_data);
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
		}
	}

/*
 	* 库存不良品定期检测、不定时发现不良品检测程序，无需领取操作限制。
    * iqcDetect.action.php
    * add by chenwei 2013.12.11
 */

	function  act_getWhSkuInfo(){
		$sku   = $_POST['sku'];		
		/*
			*扫描SKU条码goods_code转换
		 */
		if(is_numeric($sku)&& $sku>1000000){      
			$goods_codes 	= WhStandardModel::goods_codeTosku($sku);
			$sku		 	= $goods_codes['sku'];
		}

		//SKU信息API接口
		$OmAvailableApiAct      = new OmAvailableApiAct();
		$skuInfo 			    = $OmAvailableApiAct->act_getGoodsInfoBySku($sku);		
		if($skuInfo['errCode'] == '201'){
			self::$errCode = "003";
			self::$errMsg  = "【{$sku}】：没有该SKU信息，请确认！";
			return false;
		}
		$skuInfo			    = $skuInfo['data'];
		$reSku         		    = $skuInfo['sku'];//接口返回SKU
		$reSpu        		    = $skuInfo['spu'];//接口返回SKU
		$skuName      		    = $skuInfo['goodsName'];//产品描述
		$purchaseId             = $skuInfo['purchaseId'];//采购人ID
		/*
			*产品分类信息ID格式 :  8-49-270		   
			*产品分类信息name格式 :  服装及配饰-女装-T恤衫  
		 */
		$goodsCategory 		    = $skuInfo['goodsCategory'];
		$goodsCategoryNameArray = explode("-",$goodsCategory);
		$qcCategoryListAct      = new qcCategoryListAct();		
		$getCategoryArr 		= $qcCategoryListAct->act_getCategoryArr();//获取产品类别列表信息	
		/*
		 * 产品分类 对比 检测类别 path ： sampleTypeId   1-15-422	 -> 服装类
		 * 返回数组：[2] => 3C电子-功能检测
		 * 参数：8-49-270
		 */
		$getSampleTypeArr 	    = $qcCategoryListAct->act_getSampleTypeName($goodsCategory);	
		
		$goodsCategoryName      = array();	
		foreach($goodsCategoryNameArray as $cvalue){
			$goodsCategoryName[] = $getCategoryArr[$cvalue];
		}
		$goodsCategoryNameStr    = join('-',$goodsCategoryName);
		if(!$getSampleTypeArr){
			self::$errCode = "003";
			self::$errMsg  = "【{$goodsCategoryNameStr}】：分类错误，请确认！";
			return false;
		}
		foreach($getSampleTypeArr as $key => $valArr){
			$sampleTypeKey = $key;
			$sampleTypeStr = $valArr;
		}

		/*
			*获取仓库信息：
				[goods_count] => 30
				[goods_location] => B0501
				[cguser] => 张文辉		   
		 */
		$whData			 = UserCacheModel::qccenterGetErpGoodscount($sku);	
		self::$errCode   = "200";
		$info 			 = array();
		$info['info']    = "产品描述：【{$sku}】{$skuName} <br> 产品分类： {$goodsCategoryNameStr} <br>现有库存： ".$whData['goods_count']." <br>仓位信息：【".$whData['goods_location']."】<br>采购信息：{$whData['cguser']} ";
		$info['spu']     = $reSpu;//返回图片系统SPU
		$info['sku']     = $reSku;//返回系统正确SKU
		$info['whNum']   = $whData['goods_count'];//返回比较数据：检测数不能超过实际库存数量
		$info['skuName'] = $skuName;//返回产品描述
		$info['sampleTypeId'] = $sampleTypeKey;//返回检测列别ID
		$info['sampleTypeStr'] = $sampleTypeStr;//返回检测方法
		return $info;		
	}	
	
/*
 	* 库存不良品定期检测、不定时发现不良品检测 提交数据
    	1.type = 1 :有不良品插入qc_sample_defective_products 和 qc_work_table
		2.type = 2 :无不良品插入 qc_work_table
    * add by chenwei 2013.12.12
 */
	function  act_whRegularInspection(){
		$data		  = array();
		$sku  		  = $_POST['sku'];
		$spu  		  = $_POST['spu'];
		$check_num    = $_POST['check_num'];
		$checkTypeID  = 2;
		$skuName	  = $_POST['skuName'];
		$reNum		  = $_POST['reNum'];
		$type		  = $_POST['type'];
		$sampleTypeId = $_POST['sampleTypeId'];

		if($type == 1){			
			$rejects_num  = $_POST['rejects_num'];		
			$bad_reason   = post_check($_POST['bad_reason']);
			$set 		  = "SET sku='$sku',spu='$spu',defectiveNum='$rejects_num',note='$bad_reason',startTime=".time()." ";
			/*
				* 插入不良品库表qc_sample_defective_products
			 */
			$res 		  = DefectiveProductsModel::addDefectiveProducts($set);
			if($res){
				$data = array(
					'sku' 			=> $sku,
					'goodsName' 	=> $skuName,
					'arrivalNum' 	=> $reNum,
					'checkNum' 		=> $check_num,
					'rejectsNum' 	=> $rejects_num,
					'rejectsReason' => $bad_reason,
					'checkUser' 	=> $_SESSION['sysUserId'],
					'checkTime'	 	=> time(),
					'skuTypeCheckID'=> $sampleTypeId,
					'checkTypeID'  	=> 2,
					'sellerId'		=> 1
				);
				/*
					* 插入完成记录表qc_work_table
				 */
				 
				$workTabl  = IqcCompleteInfoModel::insertRow($data);
				if($workTabl){
					/*
						* 更新老ERP系统库存接口
					 */
					 
					$oldErp    = UserCacheModel::qccenterUpdateErpGoodscount($sku,$rejects_num);				
					if($oldErp){
						self::$errCode = "200";
						self::$errMsg  = "提交成功，库存扣除成功，请检测下一料号！";
						return $oldErp;	
					}else{
						self::$errCode = "4444";
						self::$errMsg  = "qc检测不良品扣除ERP库存失败！请联系IT解决！";
						return false;
					}				
				}else{
					self::$errCode = "003";
					self::$errMsg  = "提交失败，请重试";
					return false;
				}
				
				
				/*测试用
				$oldErp =	IqcCompleteInfoModel::insertRow($data);
				if($oldErp){
					self::$errCode = "200";
					self::$errMsg  = "提交成功，请检测下一料号";
					return $oldErp;	
				}else{
					self::$errCode = "4444";
					self::$errMsg  = "qc检测不良品扣除ERP库存失败！请联系IT解决！";
					return false;
				}	
				*/	
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
			
		}else if($type == 2){
			/*
				* 插入完成记录表qc_work_table
			 */
			$data = array(
				'sku' 			=> $sku,
				'goodsName' 	=> $skuName,
				'arrivalNum' 	=> $reNum,
				'checkNum' 		=> $check_num,
				'rejectsNum' 	=> 0,
				'checkUser' 	=> $_SESSION['sysUserId'],
				'checkTime'	 	=> time(),
				'skuTypeCheckID'=> $sampleTypeId,
				'checkTypeID'  	=> 2,
				'sellerId'		=> 1
			);
			/*
				* 插入完成记录表qc_work_table
			 */
			if(IqcCompleteInfoModel::insertRow($data)){
				self::$errCode = "200";
				self::$errMsg  = "提交成功，请检测下一料号";
				return true;
			}else{
				self::$errCode = "003";
				self::$errMsg  = "提交失败，请重试";
				return false;
			}
			
		}
	}
	
}
?>