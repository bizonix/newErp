<?php
    class SkuConversionAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";


	function  act_getSkuConversionList(){
		$sku = $_GET['sku']?post_check(trim($_GET['sku'])):'';//sku
        
        $tName = 'pc_sku_conversion';
        $select = '*';
        $where = "WHERE is_delete=0 ";
        if(!empty($sku)){
            $where .= "AND (old_sku like'$sku%' or new_sku like'$sku%') ";
        }
        
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 100;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by auditStatus asc,id desc ".$page->limit;
		$skuConversionList = OmAvailableModel::getTNameList($tName, $select, $where);
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
        return array('skuConversionList'=>$skuConversionList,'show_page'=>$show_page);
	}
    
    function  act_addSkuConversion(){
		$old_sku = $_POST['old_sku']?post_check(trim($_POST['old_sku'])):'';//old_sku
        $new_sku = $_POST['new_sku']?post_check(trim($_POST['new_sku'])):'';//new_sku
        $addUserId = intval($_SESSION['userId']);
        $createdTime = time();
        
        if(empty($old_sku) || empty($new_sku)){
            self::$errCode = '101';
            self::$errMsg = "新/旧料号不能为空";
            return;
        }
        if($old_sku == $new_sku){
            self::$errCode = '109';
            self::$errMsg = "新/旧料号相同，不能转换";
            return;
        }
        if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$new_sku)){
            self::$errCode = '100';
            self::$errMsg = "新料号 $new_sku 格式不合法";
            return;
        }
        if($addUserId <= 0){
            self::$errCode = '103';
            self::$errMsg = "登陆超时，请重试";
            return;
        }
        $tName = 'pc_sku_conversion';
        $where = "WHERE is_delete=0 AND old_sku='$old_sku'";        
        $countOldSkuCon = OmAvailableModel::getTNameCount($tName, $where);//在料号转换表里查找是否已经存在该旧料号的信息
        if($countOldSkuCon){
            self::$errCode = '104';
            self::$errMsg = "旧料号 $old_sku 已经存在料号转换列表中";
            return;
        }
        
        $tName = 'pc_goods';
        $select = '*';
        $where = "WHERE is_delete=0 AND sku='$old_sku' order by id desc limit 1";    
        $oldSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//在产品表里找是否存在旧料号
        $oldSku = $oldSkuList[0];//旧料号的信息
        if(empty($oldSku)){
            self::$errCode = '105';
            self::$errMsg = "旧料号 $old_sku 在产品列表中不存在";
            return;
        }
        try{
            BaseModel::begin();
            
            $tName 	= 'pc_sku_conversion';
            $set 	= "SET old_sku='$old_sku',new_sku='$new_sku',addUserId='$addUserId',createdTime='$createdTime'";
            $flag	=	OmAvailableModel::addTNameRow($tName, $set);//添加转换记录
            if($flag){
            	self::$errCode = '200';
            	self::$errMsg = "旧料号 {$old_sku}转{$new_sku}添加成功";
            }
            BaseModel::commit();
            BaseModel::autoCommit();        
            return; 
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self::$errCode = '404';
            self::$errMsg = $e->getMessage();
            return;
        }
            
	}
    
	function  act_auditSkuConversion(){
		$id = $_GET['id']?post_check(trim($_GET['id'])):0;
		$id = intval($id);
		$auditUserId = intval($_SESSION['userId']);
		$auditTime = time();
		
		if($id <= 0){
			self::$errCode = '100';
			self::$errMsg = "异常";
			return;
		}
		$tName = 'pc_sku_conversion';
		$select = 'id ,old_sku,new_sku,addUserId,createdTime,auditStatus';
		$where = "WHERE id=$id and is_delete=0";
		$skuConNewSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//在料号转换表中找到id所在记录的新料号new_sku
		//var_dump($skuConNewSkuList[0]);exit;
		$skuConId 		= 	$skuConNewSkuList[0]['id'];
		$old_sku		=	$skuConNewSkuList[0]['old_sku'];
		$new_sku		=	$skuConNewSkuList[0]['new_sku'];
		$addUserId		=	$skuConNewSkuList[0]['addUserId'];
		$createdTime	=	$skuConNewSkuList[0]['createdTime'];
		$addUser		=	getPersonNameById($addUserId);
		$auditStatus	=	$skuConNewSkuList[0]['auditStatus'];
		$paArr			=	array('old_sku'=>$old_sku,'new_sku'=>$new_sku,'user'=>$addUser,'createdtime'=>$createdTime);
		//var_dump($paArr);exit;
		if(empty($skuConId)){
			self::$errCode = '104';
			self::$errMsg = "记录{$old_sku}不存在";
			return;
		}
		if($auditStatus==2){
			self::$errCode = '105';
			self::$errMsg = "记录{$old_sku} 已是审核状态  请不要重复审核";
			return;
		}
        $tName = 'pc_goods';
        $select = 'pmId,goodsWeight';
        $where = "WHERE is_delete=0 and sku='$new_sku' limit 1";
        $newSkuInfo = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($newSkuInfo[0]['pmId']) || empty($newSkuInfo[0]['goodsWeight'])){
            self::$errCode = '106';
			self::$errMsg = "新料号：{$new_sku} 重量/包材等信息缺失，审核失败！";
			return;
        }
        $tName = 'pc_goods_whId_location_raletion';
        $where = "WHERE sku='$new_sku' AND isHasLocation=1";
        $newSkuWlInfoCount = OmAvailableModel::getTNameCount($tName, $where);
        if(!$newSkuWlInfoCount){
            self::$errCode = '107';
			self::$errMsg = "新料号：{$new_sku} 无仓位，审核失败！";
			return;
        }
        //放开限制
        //$oldErpOrderStatusArr = UserCacheModel::getOpenSysApi('pc.erp.judgeSkuConverAudit',array('sku'=>$old_sku),'gw88');
//        if($oldErpOrderStatusArr['errCode'] != 200){
//            self::$errCode = '108';
//			self::$errMsg = "旧料号：{$old_sku} 已经有处于配货/复核等状态订单，不能审核！";
//			return;
//        }
		try{
			BaseModel::begin();
			$tName = 'pc_sku_conversion';
			$set = "SET auditStatus='2',auditUserId='$auditUserId',auditTime='$auditTime'";
			$where = "WHERE id=$id and is_delete=0";
			
			//var_dump($data);exit;
			/* if(UserCacheModel::$errCode!=200){
				self::$errCode	= 1088;
				self::$errMsg	=  "erp中已存在{$old_sku}的转换记录";
				return;
			} */
			OmAvailableModel::updateTNameRow($tName, $set, $where);//先将转换记录修改
			$data=OmAvailableModel::newData2ErpInterfOpen("erp.insertPurchaseSkuConversion", $paArr,"88",false);   
			BaseModel::commit();
			BaseModel::autoCommit();
            $auditUserName = getPersonNameById($auditUserId);
            error_log(date('Y-m-d_H:i')." {$old_sku} 转 {$new_sku} 审核成功 by $auditUserName($auditUserId) \r\n",3,WEB_PATH."log/skuConversionLog.txt");
			self::$errCode	=	200;
			self::$errMsg	=	"{$old_sku}转{$new_sku}审核成功";
			return;
		}catch(Exception $e){
			BaseModel::rollback();
			BaseModel::autoCommit();
			self::$errCode = '404';
			self::$errMsg = $e->getMessage();
			return;
		}
	}
    
    //反审核操作
    function  act_unAuditSkuConversion(){
		$id = $_GET['id']?post_check(trim($_GET['id'])):0;
		$id = intval($id);
		$auditUserId = intval($_SESSION['userId']);
		$auditTime = time();
		
		if($id <= 0){
			self::$errCode = '100';
			self::$errMsg = "异常";
			return;
		}
        if($auditUserId <= 0){
			self::$errCode = '101';
			self::$errMsg = "登陆超时";
			return;
		}
		$tName = 'pc_sku_conversion';
		$select = 'id ,old_sku,new_sku,addUserId,createdTime,auditStatus';
		$where = "WHERE id=$id and is_delete=0";
		$skuConNewSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//在料号转换表中找到id所在记录的新料号new_sku
		//var_dump($skuConNewSkuList[0]);exit;
		$skuConId 		= 	$skuConNewSkuList[0]['id'];
		$old_sku		=	$skuConNewSkuList[0]['old_sku'];
		$new_sku		=	$skuConNewSkuList[0]['new_sku'];
		$addUserId		=	$skuConNewSkuList[0]['addUserId'];
		$createdTime	=	$skuConNewSkuList[0]['createdTime'];
		$addUser		=	getPersonNameById($addUserId);
		$auditStatus	=	$skuConNewSkuList[0]['auditStatus'];
		$paArr			=	array('old_sku'=>$old_sku,'new_sku'=>$new_sku);
		//var_dump($paArr);exit;
		if(empty($skuConId)){
			self::$errCode = '104';
			self::$errMsg = "记录{$old_sku}不存在";
			return;
		}
		if($auditStatus != 2){
			self::$errCode = '105';
			self::$errMsg = "记录{$old_sku} 不在审核状态，不能反审核";
			return;
		}
		try{
			BaseModel::begin();
			$tName = 'pc_sku_conversion';
			$set = "SET auditStatus='1',auditUserId='0',auditTime='0'";
			$where = "WHERE id=$id and is_delete=0";
			
			//var_dump($data);exit;
			/* if(UserCacheModel::$errCode!=200){
				self::$errCode	= 1088;
				self::$errMsg	=  "erp中已存在{$old_sku}的转换记录";
				return;
			} */
			OmAvailableModel::updateTNameRow($tName, $set, $where);//先将转换记录修改
			$data = OmAvailableModel::newData2ErpInterfOpen("erp.deletePurchaseSkuConversion", $paArr,"88",false);
			BaseModel::commit();
			BaseModel::autoCommit();
            $auditUserName = getPersonNameById($auditUserId);
            error_log(date('Y-m-d_H:i')." {$old_sku} 转 {$new_sku} 反审核成功 by $auditUserName($auditUserId) \r\n",3,WEB_PATH."log/skuConversionLog.txt");
			self::$errCode	=	200;
			self::$errMsg	=	"{$old_sku}转{$new_sku}反审核成功";
			return;
		}catch(Exception $e){
			BaseModel::rollback();
			BaseModel::autoCommit();
			self::$errCode = '404';
			self::$errMsg = $e->getMessage();
			return;
		}
	}
	
	function  act_alertSkuConversion(){
		$id = $_GET['id']?post_check(trim($_GET['id'])):0;
        $id = intval($id);
        if($id <= 0){
        	self::$errCode = '100';
        	self::$errMsg = "异常";
        	return;
        }
        $modifiedUserId = intval($_SESSION['userId']);
        $modifiedTime = time();
		$update_new_sku		=	$_REQUEST['update_new_sku']?post_check(trim($_REQUEST['update_new_sku'])):"";
		$update_old_sku		=	$_REQUEST['update_old_sku']?post_check(trim($_REQUEST['update_old_sku'])):"";
		$preoldsku		=	$_REQUEST['preoldsku']?post_check(trim($_REQUEST['preoldsku'])):"";
		$prenewsku		=	$_REQUEST['prenewsku']?post_check(trim($_REQUEST['prenewsku'])):"";;
		if($modifiedUserId <= 0){
			self::$errCode = '103';
			self::$errMsg = "登陆超时，请重试";
			return;
		}
		if(empty($update_old_sku)){
			self::$errCode = '101';
			self::$errMsg = "旧料号不能为空";
			return;
		}
		if(empty($update_new_sku)){
			self::$errCode = '101';
			self::$errMsg = "新料号不能为空";
			return;
		}
		
		/* if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$update_new_sku)){
			self::$errCode = '100';
			self::$errMsg = "新料号 $update_new_sku 格式不合法";
			return;
		}
		if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$update_old_sku)){
			self::$errCode = '100';
			self::$errMsg = "旧料号 $update_old_sku 格式不合法";
			return;
		} */
		$paArr			=	array(
						'oldsku'		=>	$update_old_sku,
						'newsku'		=>	$update_new_sku,
						'preoldsku'		=>	$preoldsku,
						'prenewsku'		=>	$prenewsku
				);
		try{
			BaseModel::begin();
			$tName = ' pc_sku_conversion';
			$set = " SET new_sku='$update_new_sku', old_sku='$update_old_sku', modifiedUserId='$modifiedUserId',modifiedTime='$modifiedTime'";
			$where = " WHERE id=$id and is_delete=0";
			OmAvailableModel::updateTNameRow($tName, $set, $where);//先将转换记录修改
			self::$errCode = '200';
			self::$errMsg = "修改转换记录成功，旧料号{$update_old_sku}转新料号{$update_new_sku} 记录修改成功";
			$data=OmAvailableModel::newData2ErpInterfOpen("erp.updatePurchaseSkuConversion", $paArr,"88",false);
			if($data['resCode']!=200){
				self::$errCode = '5004';
				self::$errMsg = "同步数据出错";
				return;
			}
			BaseModel::commit();
			BaseModel::autoCommit();
			return;
			}catch(Exception $e){
				BaseModel::rollback();
				BaseModel::autoCommit();
				self::$errCode = '4004';
				self::$errMsg = $e->getMessage();
				return;
			}
		
	}
	
	
    function  act_updateSkuConversion(){
		$id = $_GET['id']?post_check(trim($_GET['id'])):0;
        $id = intval($id);
        $new_sku = $_GET['new_sku']?post_check(trim($_GET['new_sku'])):'';//new_sku
        $modifiedUserId = intval($_SESSION['userId']);
        $modifiedTime = time();
        if($modifiedUserId <= 0){
        	self::$errCode = '103';
        	self::$errMsg = "登陆超时，请重试";
        	return;
        }
        if($id <= 0){
            self::$errCode = '100';
            self::$errMsg = "异常";
            return;
        }
        if(empty($new_sku)){
            self::$errCode = '101';
            self::$errMsg = "新料号不能为空";
            return;
        }
        if(!preg_match("/^[A-Z0-9]+(_[A-Z0-9]+)*$/",$new_sku)){
            self::$errCode = '100';
            self::$errMsg = "新料号 $new_sku 格式不合法";
            return;
        }
        if($modifiedUserId <= 0){
            self::$errCode = '103';
            self::$errMsg = "登陆超时，请重试";
            return;
        }
        $tName = 'pc_sku_conversion';
        $select = 'new_sku';
        $where = "WHERE id=$id and is_delete=0";        
        $skuConNewSkuList = OmAvailableModel::getTNameList($tName, $select, $where);//在料号转换表中找到id所在记录的新料号new_sku
        $skuConNewSku = $skuConNewSkuList[0]['new_sku'];
        if(empty($skuConNewSku)){
            self::$errCode = '104';
            self::$errMsg = "记录的新料号不存在";
            return;
        }
        if($skuConNewSku == $new_sku){
            self::$errCode = '111';
            self::$errMsg = "修改前后料号相同，无修改";
            return;
        }
        $tName = 'pc_goods';
        $select = '*';
        $where = "WHERE is_delete=0 AND sku='$skuConNewSku'";    
        $newSkuPcList = OmAvailableModel::getTNameList($tName, $select, $where);//在产品表里找是否存在未修改前的料号
        $newSkuPc = $newSkuPcList[0];//未修改前的料号信息
        if(empty($newSkuPc)){
            self::$errCode = '105';
            self::$errMsg = "修改前的料号 $skuConNewSku 在产品列表中不存在";
            return;
        }
        $newSkuPcSku = $newSkuPc['sku'];
        //$newSkuPcArr = array_filter(explode('_',$newSkuPc));//修改前的sku按照_来截取
//        $newSpuPc = $newSkuPcArr[0];//取得新料号的SPU
//        $newSkuArr = array_filter(explode('_',$new_sku));//修改后的sku按照_来截取
//        $newSpu = $newSkuArr[0];//取得新料号的SPU
//        if($newSpuPc != $newSpu){
//            self::$errCode = '107';
//            self::$errMsg = "修改前后的SPU不符，请检查";
//            return;
//        }
        try{
            BaseModel::begin();
            $tName = 'pc_sku_conversion';
            $set = "SET new_sku='$new_sku',modifiedUserId='$modifiedUserId',modifiedTime='$modifiedTime'";
            $where = "WHERE id=$id and is_delete=0";
            OmAvailableModel::updateTNameRow($tName, $set, $where);//先将转换记录修改
            
            $tName = 'pc_goods';
            $set = "SET goodsStatus=101,is_delete=1";//将未修改前的sku状态改为 料号转化，并delete
            $where = "WHERE is_delete=0 and sku='$newSkuPcSku'";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            
            $where = "WHERE is_delete=0 and sku='$new_sku'";
            $newSkuCount = OmAvailableModel::getTNameCount($tName, $where);
            if($newSkuCount){//如果新料号在产品中已经存在，则只添加转换记录
                self::$errCode = '200';
                self::$errMsg = "修改转换记录成功，新料号：$new_sku 已经存在记录";
            }else{//如果新料号在产品表中不存在，则自动添加旧料号的记录进去
                unset($newSkuPc['id']);//去掉旧记录的id
                $newSkuPc['sku'] = $new_sku;//将旧的sku换成新的sku
                OmAvailableModel::addTNameRow2arr($tName, $newSkuPc);
                self::$errCode = '200';
                self::$errMsg = "修改转换记录成功，新料号：$new_sku 记录已插入成功";
            }
            
            //下面对关联单料号的组合料号进行修改
            $tName = 'pc_sku_combine_relation';
            $select = '*';
            $where = "WHERE sku='$newSkuPcSku' group by combineSku";
            $skuRelationList = OmAvailableModel::getTNameList($tName, $select, $where);
            if(empty($skuRelationList)){
                self::$errMsg .= "<br/>其中，无关联组合料号被更新";
            }else{
                $combineSkuArr = array();
                foreach($skuRelationList as $value){
                    $combineSkuArr[] = $value['combineSku'];
                }
                $combineSkuStr = implode(',',$combineSkuArr);
                $set = "SET sku='$new_sku'";
                $where = "WHERE sku='$newSkuPcSku'";
                $affectRows = OmAvailableModel::updateTNameRow($tName, $set, $where);
                self::$errMsg .= "<br/>";
                self::$errMsg .= "其中，组合料号 $combineSkuStr 中共有 $affectRows 条单料号数据已更新";
            }      
            //////
            BaseModel::commit();
            BaseModel::autoCommit();        
            return;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self::$errCode = '404';
            self::$errMsg = $e->getMessage();
            return;
        }
            
	}
    
    
    
    
    
    }


?>