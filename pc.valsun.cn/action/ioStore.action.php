<?php
    class IoStoreAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";


	function  act_getOutStoreList(){
		$spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';//spu
		$iostoreStatus = $_GET['iostoreStatus']?post_check(trim($_GET['iostoreStatus'])):0;//退料单状态，草稿或者已经发送至仓库
        $useTypeId = $_GET['useTypeId']?post_check(trim($_GET['useTypeId'])):0;//用途类型，制作，修改
        $isAudit = $_GET['isAudit']?post_check(trim($_GET['isAudit'])):0;//审核状态，通过或者不通过
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):1;//仓库
        $startdate = $_GET['startdate']?post_check(trim($_GET['startdate'])):0;//开始时间
        $enddate = $_GET['enddate']?post_check(trim($_GET['enddate'])):0;//结束时间
        
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 AND iostoreTypeId=1 ";
        
        if(!empty($spu)){
            $tNameDetail = 'pc_products_iostore_detail';
            $selectDetail = 'iostoreId';
            $whereDetail = "WHERE sku REGEXP '^$spu(_[A-Z0-9])*$' group by iostoreId";
            $instoreIdList = OmAvailableModel::getTNameList($tNameDetail, $selectDetail, $whereDetail);
            if(!empty($instoreIdList)){
                $instoreIdArr = array();
                foreach($instoreIdList as $value){
                    $instoreIdArr[] = $value['iostoreId'];
                }
                $instoreIdStr = implode(',',$instoreIdArr);
                if(empty($instoreIdStr)){
                    $where .= "AND 1=2 ";
                }else{
                    $where .= "AND id in($instoreIdStr) ";
                }
            }else{
                $where .= "AND 1=2 ";
            }
        }
        if(!empty($iostoreStatus)){
            $where .= "AND iostoreStatus='$iostoreStatus' ";
        }
        if(!empty($useTypeId)){
            $where .= "AND useTypeId='$useTypeId' ";
        }
        if(!empty($isAudit)){
            $where .= "AND isAudit='$isAudit' ";
        }
        if(!empty($whId)){
            $where .= "AND whId='$whId' ";
        }
        if(!empty($startdate)){
            $start = strtotime($startdate . ' 00:00:00');
        	$where .= "AND createdTime>='$start' ";
        }
        if(!empty($enddate)){
            $end = strtotime($enddate . ' 23:59:59');
        	$where .= "AND createdTime<='$end' ";
        }
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		$outStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $whList = getWhInfo();//获取仓库列表的接口
        $whArr = array();
        foreach($whList as $value){
            if(intval($value['id']) > 0){
                $whArr[$value['id']] = $value['whName'];
            }
        }
        if(!empty($outStoreList)){
            $countOutStoreList = count($outStoreList);
            for($i=0;$i<$countOutStoreList;$i++){
                $outStoreList[$i]['whName'] = $whArr[$outStoreList[$i]['whId']];
            }
        }
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
        return array('outStoreList'=>$outStoreList,'show_page'=>$show_page);
	}
    
    function  act_getInStoreList(){
        $spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';//spu
		$iostoreStatus = $_GET['iostoreStatus']?post_check(trim($_GET['iostoreStatus'])):0;//退料单状态，草稿或者已经发送至仓库
        $useTypeId = $_GET['useTypeId']?post_check(trim($_GET['useTypeId'])):0;//用途类型，制作，修改
        $isAudit = $_GET['isAudit']?post_check(trim($_GET['isAudit'])):0;//审核状态，通过或者不通过
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):1;//仓库
        $startdate = $_GET['startdate']?post_check(trim($_GET['startdate'])):0;//开始时间
        $enddate = $_GET['enddate']?post_check(trim($_GET['enddate'])):0;//结束时间
        
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 AND iostoreTypeId=2 ";//退料单 的iostoreTypeId=2
        
        if(!empty($spu)){
            $tNameDetail = 'pc_products_iostore_detail';
            $selectDetail = 'iostoreId';
            $whereDetail = "WHERE sku REGEXP '^$spu(_[A-Z0-9])*$' group by iostoreId";
            $instoreIdList = OmAvailableModel::getTNameList($tNameDetail, $selectDetail, $whereDetail);
            if(!empty($instoreIdList)){
                $instoreIdArr = array();
                foreach($instoreIdList as $value){
                    $instoreIdArr[] = $value['iostoreId'];
                }
                $instoreIdStr = implode(',',$instoreIdArr);
                if(empty($instoreIdStr)){
                    $where .= "AND 1=2 ";
                }else{
                    $where .= "AND id in($instoreIdStr) ";
                }
            }else{
                $where .= "AND 1=2 ";
            }
        }
        if(!empty($iostoreStatus)){
            $where .= "AND iostoreStatus='$iostoreStatus' ";
        }
        if(!empty($useTypeId)){
            $where .= "AND useTypeId='$useTypeId' ";
        }
        if(!empty($isAudit)){
            $where .= "AND isAudit='$isAudit' ";
        }
        if(!empty($whId)){
            $where .= "AND whId='$whId' ";
        }
        if(!empty($startdate)){
            $start = strtotime($startdate . ' 00:00:00');
        	$where .= "AND createdTime>='$start' ";
        }
        if(!empty($enddate)){
            $end = strtotime($enddate . ' 23:59:59');
        	$where .= "AND createdTime<='$end' ";
        }    
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		$inStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $whList = getWhInfo();//获取仓库列表的接口
        $whArr = array();
        foreach($whList as $value){
            if(intval($value['id']) > 0){
                $whArr[$value['id']] = $value['whName'];
            }
        }
        if(!empty($inStoreList)){
            $countInStoreList = count($inStoreList);
            for($i=0;$i<$countInStoreList;$i++){
                $inStoreList[$i]['whName'] = $whArr[$inStoreList[$i]['whId']];
            }
        }
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
        return array('inStoreList'=>$inStoreList,'show_page'=>$show_page);
	}

	function  act_getOutStoreDetailList(){
		$iostoreId = $_GET['iostoreId']?post_check(trim($_GET['iostoreId'])):0;//领料单状态，草稿或者已经发送至仓库
        $iostoreId = intval($iostoreId);
        if($iostoreId <= 0){
            $status = '领料单非法';
            header("Location:index.php?mod=products&act=getOutStoreList&status=$status");
            exit;
        }
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 AND id=$iostoreId ";
        $outStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $outStore = $outStoreList[0];
        if(empty($outStore)){
            $status = '系统不存在该领料单';
            header("Location:index.php?mod=products&act=getOutStoreList&status=$status");
            exit;
        }
        
        $tName = 'pc_products_iostore_detail';
        $select = '*';
        $where = "WHERE iostoreTypeId=1 AND is_delete=0 AND iostoreId=$iostoreId ";
        
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 1000;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		$outStoreDetailList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($outStoreDetailList)){
            $whInfoList = getWhInfo();//根据接口取得对应仓库信息
            $whArr = array();
            foreach($whInfoList as $value){
                if(intval($value['id']) > 0){
                    $whArr[$value['id']] = $value['whName'];
                }
            }
            $countStoreDetailList = count($outStoreDetailList);
            for($i=0;$i<$countStoreDetailList;$i++){
                $sku = $outStoreDetailList[$i]['sku'];
                $tName = 'pc_goods';
                $select = 'goodsName';
                $where = "WHERE sku='$sku'";
                $skuInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $outStoreDetailList[$i]['goodsName'] = $skuInfoList[0]['goodsName'];
                $tName = 'pc_goods_whId_location_raletion';
                $select = 'location,whId';
                $where = "WHERE sku='$sku'";
                $skuLocWhInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $outStoreDetailList[$i]['location'] = $skuLocWhInfoList[0]['location'];
                $outStoreDetailList[$i]['whName'] = $whArr[$skuLocWhInfoList[0]['whId']];
            }
        }
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
        return array('outStore'=>$outStore,'outStoreDetailList'=>$outStoreDetailList,'show_page'=>$show_page);
	}
    
    function  act_getInStoreDetailList(){
		$iostoreId = $_GET['iostoreId']?post_check(trim($_GET['iostoreId'])):0;//退料单状态，草稿或者已经发送至仓库
        $iostoreId = intval($iostoreId);
        if($iostoreId <= 0){
            $status = '退料单非法';
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE iostoreTypeId=2 AND is_delete=0 AND id=$iostoreId ";
        $inStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $inStore = $inStoreList[0];
        if(empty($inStore)){
            $status = '系统不存在该退料单';
            header("Location:index.php?mod=products&act=getInStoreList&status=$status");
            exit;
        }
        
        $tName = 'pc_products_iostore_detail';
        $select = '*';
        $where = "WHERE is_delete=0 AND iostoreId=$iostoreId ";
        
        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num      = 1000;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= "order by id desc ".$page->limit;
		$inStoreDetailList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($inStoreDetailList)){
            $whInfoList = getWhInfo();//根据接口取得对应仓库信息
            $whArr = array();
            foreach($whInfoList as $value){
                if(intval($value['id']) > 0){
                    $whArr[$value['id']] = $value['whName'];
                }
            }
            $countStoreDetailList = count($inStoreDetailList);
            for($i=0;$i<$countStoreDetailList;$i++){
                $sku = $inStoreDetailList[$i]['sku'];
                $tName = 'pc_goods';
                $select = 'goodsName';
                $where = "WHERE sku='$sku'";
                $skuInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $inStoreDetailList[$i]['goodsName'] = $skuInfoList[0]['goodsName'];
                $tName = 'pc_goods_whId_location_raletion';
                $select = 'location,whId';
                $where = "WHERE sku='$sku'";
                $skuLocWhInfoList = OmAvailableModel::getTNameList($tName, $select, $where);
                $inStoreDetailList[$i]['location'] = $skuLocWhInfoList[0]['location'];
                $inStoreDetailList[$i]['whName'] = $whArr[$skuLocWhInfoList[0]['whId']];
            }
        }
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
        return array('inStore'=>$inStore,'inStoreDetailList'=>$inStoreDetailList,'show_page'=>$show_page);
	}
    
    //单个添加领料/退料详细表中的sku
    function act_addIoStoreDetail() {
		$sku = $_POST['sku']?post_check(trim($_POST['sku'])):'';
        $iostoreId = intval($_POST['iostoreId']);
        $addUserId = intval($_SESSION['userId']);
        if($iostoreId <= 0){
            echo '无效单据';
            exit;
        }       
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 AND id=$iostoreId";
        $ioStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $ioStore = $ioStoreList[0];
        if(empty($ioStore)){
            echo '单据有误！';
            exit;
        }
        
        $mod = 'products';
        $act = 'getOutStoreDetailList';
        if($ioStore['iostoreTypeId'] == 2){
           $act = 'getInStoreDetailList';
        } 
        
        if($addUserId <= 0){
            $status = "登陆超时";
            header("Location:index.php?mod=$mod&act=$act&iostoreId=$iostoreId&status=$status");
            exit;
        }
        
        if(empty($sku)){
            $status = "SKU为空";
            header("Location:index.php?mod=$mod&act=$act&iostoreId=$iostoreId&status=$status");
            exit;
        }
        if(!isSkuExist($sku)){
            $status = "$sku 不存在";
            header("Location:index.php?mod=$mod&act=$act&iostoreId=$iostoreId&status=$status");
            exit;
        }
        
        $tName = 'pc_products_iostore_detail';
        $ioStoreDetailArr = array();
        $ioStoreDetailArr['iostoreId'] = $iostoreId;
        $ioStoreDetailArr['iostoreStatus'] = $ioStore['iostoreStatus'];
        $ioStoreDetailArr['iostoreTypeId'] = $ioStore['iostoreTypeId'];
        $ioStoreDetailArr['useTypeId'] = $ioStore['useTypeId'];
        $ioStoreDetailArr['sku'] = $sku;
        $ioStoreDetailArr['whId'] = $ioStore['whId'];
        $ioStoreDetailArr['addUserId'] = $addUserId;       
        $ioStoreDetailArr['addTime'] = time();
        $ioStoreDetailArr['isAudit'] = $ioStore['isAudit'];
        $ioStoreDetailArr['isComfirm'] = $ioStore['isComfirm'];
        OmAvailableModel::addTNameRow2arr($tName, $ioStoreDetailArr);
        $status = "$sku 添加成功";
        header("Location:index.php?mod=$mod&act=$act&iostoreId=$iostoreId&status=$status");
        exit;
	}
    
    //单个删除领料/退料详细表中的sku
    function act_deleteIoStoreDetailById() {
		$id = intval($_POST['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_products_iostore_detail';
        $select = 'sku';
        $where = "WHERE id=$id";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
        $sku = $skuList[0]['sku'];
        $set = "SET is_delete=1";
        $where = "WHERE id=$id";
        OmAvailableModel::updateTNameRow($tName, $set, $where);
        self :: $errCode = '200';
		self :: $errMsg = "$sku 删除成功";
		return true;
	}
    
    //删除整个领料/退料单
    function act_deleteIoStoreById() {
		$id = intval($_GET['id']);
        if($id <= 0){
            self :: $errCode = '101';
			self :: $errMsg = '无效记录，删除失败';
			return false;
        }
        $tName = 'pc_products_iostore';
        $select = 'iostoreTypeId';
        $where = "WHERE id=$id and is_delete=0";
        $iostoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        $iostoreTypeId = $iostoreList[0]['iostoreTypeId'];
        if(empty($iostoreTypeId)){
            echo '错误单号！';
            exit;
        }
        $mod = 'products';
        $act = 'getOutStoreList';
        if($iostoreTypeId == 2){
           $act = 'getInStoreList';
        }
        try{
            BaseModel::begin();
            $tName = 'pc_products_iostore';
            $set = "SET is_delete=1";
            $where = "WHERE id=$id";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            $tName = 'pc_products_iostore_detail';
            $set = "SET is_delete=1";
            $where = "WHERE iostoreId=$id";
            OmAvailableModel::updateTNameRow($tName, $set, $where);
            BaseModel::commit();
            BaseModel::autoCommit();
            $status = "删除成功";
            header("Location:index.php?mod=$mod&act=$act&status=$status");
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            $status = $e->getMessage();
            header("Location:index.php?mod=$mod&act=$act&status=$status");
        }
        
	}
    
    //获取未归还sku列表
    function  act_getIsNotBackSkuList(){
		$useTypeId = $_GET['useTypeId']?post_check(trim($_GET['useTypeId'])):0;
        $whId = $_GET['whId']?post_check(trim($_GET['whId'])):0;
        return getIsNotBackSkuList($useTypeId, $whId);
	}
    
    //产品部确认收货
    function  act_confirmReceivingByMFG(){
		$ioStoreId = !empty($_POST['ioStoreId'])?$_POST['ioStoreId']:0;
        $comfirmUserId = $_SESSION['userId'];
        $now = time();
        if(intval($ioStoreId) <= 0){
            self :: $errCode = '101';
    		self :: $errMsg = "id有误";
    		return false;
        }
        if(intval($comfirmUserId) <= 0){
            self :: $errCode = '102';
    		self :: $errMsg = "登陆超时，请重试";
    		return false;
        }
        $tName = 'pc_products_iostore';
        $select = '*';
        $where = "WHERE is_delete=0 AND isAudit=2 AND id='$ioStoreId'";
        $ioStoreList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(empty($ioStoreList)){
            self :: $errCode = '103';
    		self :: $errMsg = "该单据不存在或者未审核通过";
    		return false;
        }
        try{
            BaseModel::begin();
            //标记表头已经确认
            $dataIoStore = array();
            $dataIoStore['isComfirm'] = 2;//标记单据已经确认
            $dataIoStore['comfirmUserId'] = $comfirmUserId;
            $dataIoStore['comfirmTime'] = $now;
            OmAvailableModel::updateTNameRow2arr($tName, $dataIoStore, $where);
            //标记表体确认
            $tName = 'pc_products_iostore_detail';
            $dataIoStoreDetail = array();
            $dataIoStoreDetail['isComfirm'] = 2;
            $where = "WHERE iostoreId='$ioStoreId'";
            OmAvailableModel::updateTNameRow2arr($tName, $dataIoStoreDetail, $where);
            //如果是新品领料单，则产品部确认后，该单据下的sku才进入产品制作表
            if($ioStoreList[0]['iostoreTypeId'] == 1 && $ioStoreList[0]['useTypeId'] == 1){
                $select = 'sku';
                $skuList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($skuList as $value){
                    $sku = $value['sku'];
                    $tName = 'pc_products';
                    $dataProducts = array();
                    $dataProducts['sku'] = $sku;
                    OmAvailableModel::addTNameRow2arr($tName, $dataProducts);//将detail中的sku加入到产品制作表中
                }
            }           
            BaseModel::commit();
            BaseModel::autoCommit();
            self :: $errCode = '200';
    		self :: $errMsg = "确认收货成功";
    		return true;
        }catch(Exception $e){
            BaseModel::rollback();
            BaseModel::autoCommit();
            self :: $errCode = '404';
    		self :: $errMsg = $e->getMessage();
    		return false;            
        }
	}
    
    
    
}


?>