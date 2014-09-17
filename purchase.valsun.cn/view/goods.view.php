<?php
/**
 * 类名：GoodsView
 * 功能：货品资料视图层
 * 版本：1.0
 * 日期：2013/8/5
 * 作者：管拥军
 */
class GoodsView extends BaseView{

	//页面渲染输出
	public function view_index(){ 
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
		$this->smarty->display('products.htm');
	}    
	/*
	 * 功能：只显示搜索
	 * */ 
	public function view_goods_search_index(){
		$this->view_index();
	}
}
?>
