<?php
 
class ProductStockalarmView  extends BaseView{
	//采购预警首页渲染
	public function view_index(){
		//error_reporting(-1);
		$productStockalarm	= new ProductStockalarmAct();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$pid			= isset($_GET['pid']) ? intval($_GET['pid']) : 0;//供应商ID
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';//搜索条件
		$key			= isset($_GET['keyword']) ? post_check(trim($_GET['keyword'])) : '';//关键词
		$pcid			= isset($_GET['pcid']) ? intval($_GET['pcid']) : 0;//采购员ID
		$is_warn		= isset($_GET['is_warn']) ? intval($_GET['is_warn']) : '';//是否预警
		$status			= isset($_GET['status']) ? intval($_GET['status']) : '';//产品状态
		$condition		= "1";
		if (!empty($status)) {
			$condition	.= " AND a.goodsStatus = '{$status}'";
		}
		if (!empty($pcid)) {
			$condition	.= " AND a.purchaseId = '{$pcid}'";
		}else{
			$condition	.= " AND a.purchaseId = '{$_SESSION['sysUserId']}'";
		}

		if (!empty($is_warn)) {
			//$condition	.= " AND c.is_warning = '{$is_warn}'";
			$condition	.= " AND c.is_alert = '{$is_warn}'";
		}

		if($pid != ""){
			$skuData = new SkuAanalyzeAct();
			$skuArr = $skuData->getSkuFromPartner($pid);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}
		if ($type && $key) {
			if ($type == 'goodsName') {
				$condition	.= ' AND a.'.$type." like '%".$key."%'";
			}else if($type == "partner"){
				$skuArr = $this->getSkuByPartner($key);
				$skuStr = implode("','",$skuArr);
				$condition .= " and a.sku in ('{$skuStr}')";
			}else{
				$condition	.= " AND a.sku like '{$key}%'";
			}
		}else if(isset($key)){
				$condition	.= " AND a.sku like '%{$key}%'";
		}

		$condition	.= " AND a.is_delete=0";

		//获取符合条件的数据并分页
		$pagenum		= 100;//每页显示的个数
		$res			= $productStockalarm->actList($condition, $curpage, $pagenum);
		$total			= $productStockalarm->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		$pageStr = $page->fpage();

		$totalPageNum = ceil($total/$pagenum);
		//替换页面内容变量
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
        $this->smarty->assign('title','采购下单预警');
        $this->smarty->assign('key',$key);
        $this->smarty->assign('type',$type);
		$skuData = new SkuAanalyzeAct();
		$platformInfo = $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
        $this->smarty->assign('pid',$pid); 
        $this->smarty->assign('is_warn',$is_warn); 
        $this->smarty->assign('pcid',$pcid);
        $this->smarty->assign('status',$status);
        $this->smarty->assign('lists',$res);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		//$allPurchaser = $this->getPurchaseUserAll();
		$allPurchaser = $purchaseList;
		$this->smarty->assign('allPurchaser',$allPurchaser);
		$this->smarty->display('productStockalert.htm');
	}

	public function view_outStock(){
		//error_reporting(-1);
		$productStockalarm	= new ProductStockalarmAct();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$pid			= isset($_GET['pid']) ? intval($_GET['pid']) : 0;//供应商ID
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';//搜索条件
		$key			= isset($_GET['keyword']) ? post_check(trim($_GET['keyword'])) : '';//关键词
		$pcid			= isset($_GET['pcid']) ? intval($_GET['pcid']) : 0;//采购员ID
		$is_warn		= isset($_GET['is_warn']) ? intval($_GET['is_warn']) : '';//是否预警
		$status			= isset($_GET['status']) ? intval($_GET['status']) : '';//产品状态
		$arrivalDays	= isset($_GET['arrivalDays']) ? intval($_GET['arrivalDays']) : '';//可能到货天数
		$isSendMail		= isset($_GET['isSendMail']) ? intval($_GET['isSendMail']) : '';//是否已推送邮件
		$skuData = new SkuAanalyzeAct();
		//$condition		= "1";
		$condition	= "  a.goodsStatus != 2";
		if (!empty($pcid)) {
			$condition	.= " AND a.purchaseId = '{$pcid}'";
		}else{
			$condition	.= " AND a.purchaseId = '{$_SESSION['sysUserId']}'";
		}

		if (!empty($is_warn)) {
			//$condition	.= " AND c.is_warning = '{$is_warn}'";
			$condition	.= " AND c.is_alert = '{$is_warn}'";
		}

		if($pid != ""){
			$skuArr = $skuData->getSkuFromPartner($pid);
			$skuStr = implode("','",$skuArr);
			$condition  .= " AND a.sku in ('{$skuStr}')";
		}
		if ($type && $key) {
			if ($type == 'goodsName') {
				$condition	.= ' AND a.'.$type." like '%".$key."%'";
			}else if($type == "partner"){
				$skuArr = $this->getSkuByPartner($key);
				$skuStr = implode("','",$skuArr);
				$condition .= " and a.sku in ('{$skuStr}')";
			}else{
				$condition	.= " AND a.sku like '{$key}%'";
			}
		}
		//跟据到货天数筛选
		if(!empty($arrivalDays)) {
			if($arrivalDays	== '1') {
				$condition	.= " AND (c.addReachtime=0 OR c.reach_days=0)";
			}else{
				$condition	.= " AND c.addReachtime<>0 AND c.reach_days<>0";
			}
		}
		//跟据是否已推送邮件筛选
		if(!empty($isSendMail)) {
			if($isSendMail	== '1') {
				$condition	.= " AND (c.out_mark=1 OR c.out_mark=2)";
			}else{
				$condition	.= " AND c.out_mark=0";
			}
		}
		$condition	.= " AND c.out_alert=1 and c.everyday_sale!=0 and a.is_delete=0 and a.isNew=0";
		//获取符合条件的数据并分页
		$pagenum		= 100;//每页显示的个数
		$res			= $productStockalarm->actList($condition, $curpage, $pagenum);
		$total			= $productStockalarm->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		$pageStr = $page->fpage();

		$totalPageNum = ceil($total/$pagenum);
		//替换页面内容变量
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
        $this->smarty->assign('title','超卖控制');
        $this->smarty->assign('key',$key);
        $this->smarty->assign('type',$type);
		$platformInfo = $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
        $this->smarty->assign('pid',$pid); 
        $this->smarty->assign('is_warn',$is_warn); 
        $this->smarty->assign('pcid',$pcid);
        $this->smarty->assign('status',$status);
        $this->smarty->assign('lists',$res);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		$allPurchaser = $this->getPurchaseUserAll();
		$this->smarty->assign('allPurchaser',$allPurchaser);
		$this->smarty->display('outStock.htm');
	}



	//海外仓超卖控制
	public function view_owOutStock(){
		//error_reporting(-1);
		$skuData = new SkuAanalyzeAct();
		$skulists = $skuData->overseaOverControl();
		$total = $skulists["totalNum"];
		$skuInfo = $skulists["skuInfo"];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
        $this->smarty->assign('skuInfo',$skuInfo);
		$platformInfo = $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
		$skuAct = new SkuAct();
		$setContion = $skuAct->getSetting();
        $this->smarty->assign("setContion",$setContion);
		$totalPageNum = ceil($total/$pagenum);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
		//$allPurchaser = $this->getPurchaseUserAll();
		$allPurchaser = getPurchaseUserList();
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
		$this->smarty->assign('allPurchaser',$allPurchaser);
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		$this->smarty->display('owOutStock.htm');
	}

	public function getSkuByPartner($name){
		global $dbConn ;
		$sql = "select id from ph_partner where company_name like '%{$name}%' ";
		$sql = $dbConn->execute($sql);
		$partnerids = $dbConn->getResultArray($sql);
		$idArr = array();
		foreach($partnerids as $item){
			$idArr[] = $item['id'];
		}
		$idStr = implode(",",$idArr);
		$sql = "select sku from ph_user_partner_relation where partnerId in ({$idStr})";
		$sql = $dbConn->execute($sql);
		$skuInfo = $dbConn->getResultArray($sql);
		$skuArr = array();
		foreach($skuInfo as $item){
			$skuArr[] = $item['sku'];
		}
		return $skuArr;
	}

	public function view_analyze(){
		error_reporting(-1);
		$skuData = new SkuAanalyzeAct();
		$skulists = $skuData->index();
		$total = $skulists["totalNum"];
		$skuInfo = $skulists["skuInfo"];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
        $this->smarty->assign('title','销售库存数据分析');
        $this->smarty->assign('skuInfo',$skuInfo);
		$platformInfo = $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
		$totalPageNum = ceil($total/$pagenum);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
		$allPurchaser = $this->getPurchaseUserAll();
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
		$this->smarty->assign('allPurchaser',$allPurchaser);
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		$this->smarty->display('analyze.htm');
	}

	public function view_materia(){
		error_reporting(-1);
		$skuData = new SkuAanalyzeAct();
		$skulists = $skuData->get_materia_list();
		$total = $skulists["totalNum"];
		$skuInfo = $skulists["skuInfo"];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
        $this->smarty->assign('title','包材预警');
        $this->smarty->assign('skuInfo',$skuInfo);
		$platformInfo = $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
		$totalPageNum = ceil($total/$pagenum);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
		$allPurchaser = $this->getPurchaseUserAll();
		$this->smarty->assign('allPurchaser',$allPurchaser);
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		$this->smarty->display('materia.htm');
	}


	public function view_oversea(){
		error_reporting(0);
		$skuData 	= new SkuAanalyzeAct();
		$skulists 	= $skuData->overseaAlertInfo();
		$total 		= $skulists["totalNum"];
		$skuInfo 	= $skulists["skuInfo"];
		$pagenum 	= 100;
		$page 		= new Page($total, $pagenum);
		$pageStr 	= $page->fpage();
        $this->smarty->assign('title','海外仓下单预警');
        $this->smarty->assign('skuInfo',$skuInfo);
        $overCguserArr 	= array('龚永喜', '陈珠艺', '陈剑锋', '郑珍', '王芳', '英爱', '陈奕宏', '汤东东', '胡威');
        $loginName      = $_SESSION['userCnName'];
        $this->smarty->assign('loginName', $loginName);
        $this->smarty->assign('overCguserArr', $overCguserArr);
		$platformInfo 	= $skuData->getSalePlatform();
        $this->smarty->assign('platformInfo',$platformInfo);//销售平台数据 
		$skuAct 		= new SkuAct();
		$setContion 	= $skuAct->getSetting();
        $this->smarty->assign("setContion",$setContion);
		$totalPageNum 	= ceil($total/$pagenum);
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$partnerList	= getPartnerlist();
        $this->smarty->assign('partnerList',$partnerList);//供应商列表
		$allPurchaser 	= getPurchaseUserList();
		$tableColor 	= array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
		$this->smarty->assign('allPurchaser',$allPurchaser);
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
	    $this->smarty->assign('totalPageNum',$totalPageNum);  
		$this->smarty->display('oversea.htm');
	}

	public function getPurchaseUserAll(){
		global $dbConn;
		//$sql = "SELECT  a.global_user_id,a.global_user_name FROM  `power_global_user` as a left join power_job as b on a.global_user_job = b.job_id  where b.job_name like '%采购%' or a.global_user_id='{$_SESSION['sysUserId']}'";
		$sql = "SELECT  a.global_user_id,a.global_user_name FROM  `power_global_user` as a left join power_job as b on a.global_user_job = b.job_id  where b.job_name like '%采购%' ";
		$sql = $dbConn->execute($sql);
		$userInfo = $dbConn->getResultArray($sql);
		return $userInfo;
	}
	
 	/**
     * 海外仓到货立方数
     */
    public function view_overSkuVolume(){
		global $mod,$act;
        $keyword     	= isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $purwh 			= new PurToWhModel();
        $condition 		= '';
    	if (!empty($keyword)){
    		$rtnCguserArr   = $purwh->getCguserArrId($keyword);//获取可能匹配的采购员编号
			$cguserArr      = '';
			if(!empty($rtnCguserArr)){
				foreach($rtnCguserArr as $k => $v){
					$cguserArr .= $v['global_user_id'].',';
				}
				$cguserArr = "(".substr($cguserArr, 0, strlen($cguserArr) - 1).")";
			}
			if($cguserArr != ''){
				$condition  .= "AND (a.sku LIKE '%{$keyword}%' OR a.goodsName LIKE '%{$keyword}%' OR a.OverSeaSkuCharger IN {$cguserArr})";
			}else{
				$condition  .= "AND (a.sku LIKE '%{$keyword}%' OR a.goodsName LIKE '%{$keyword}%')";
			}
			
		}
        $page       	= isset($_GET['page']) ? $_GET['page'] : '1';
		$listInfo 		= $purwh->getOverSeaSkuVolume($condition, $page);
        $perNum 		= 200; 
		$totalNum 		= $listInfo["totalNum"];
		$list 			= $listInfo["goodsInfo"];
		$totalVolume    = 0;
		if(!empty($list)){
			foreach($list as $k => $v){
				$sku    			= $v['sku'];
				$length 			= $v['goodsLength'];
				$width  			= $v['goodsWidth'];
				$height 			= $v['goodsHeight'];
				$stock  			= $v['b_stock_cout'];
				$inboxqty 			= $v['inBoxQty'];
				$totalVolume   += $length * $width * $height * ($stock + $inboxqty);
			}
			$totalVolume = round($totalVolume / 1000000, 3);
		}
		$allTotalVolume = $purwh->getTotalVolume();
		$pageobj 		= new Page($totalNum, $perNum);
		$pageStr 		= $pageobj->fpage();
		$this->smarty->assign('pageStr', $pageStr);//分页输出
		$this->smarty->assign('list', $list);//循环赋值*/
		$this->smarty->assign('title','海外料号B仓库存立方数');
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->assign('totalVolume', $totalVolume);
		$this->smarty->assign('allTotalVolume', $allTotalVolume);
		$this->smarty->display('overSkuVolume.htm');
    }
}
?>
