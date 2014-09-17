<?php
/**
 * 类名：skuInfoView
 * 功能：订单导出excel
 * 版本：2013-12-20
 * 作者：贺明华
 */
class skuInfoView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 列表显示页
     */
	public function view_skuInfo(){
		$sku = isset($_GET['sku'])?trim($_GET['sku']):"";
		//$spu = ExportsToXlsModel::getGoods($sku);
		$spu = GoodsModel::getSkuList($sku);
		$spu = $spu['spu'];
		$where  = "where spu = '{$spu}'";
		$skuinfo = OmAvailableModel::getTNameList("pc_goods","*",$where);
		
		$skuStock = array();
		foreach($skuinfo as $key=>$value){
			$sku = $value['sku'];
			//获取库存
			$skuStock = WarehouseAPIModel::getSkuStock($sku);
			//获取料号信息
			//$skumsg = ExportsToXlsModel::getGoods($sku);
			$skumsg = GoodsModel::getSkuList($sku);
			//获取缓存表信息
			$where = "where sku='{$sku}'";
			$skuStatics = OmAvailableModel::getTNameList("om_sku_daily_status","*",$where);
			
			$skuStockList = array();
			$skuStockList['nums'] 				= $skuStock;
			$skuStockList['sku'] 					= $skumsg['sku'];
			$skuStockList['spu'] 					= $skumsg['spu'];
			$skuStockList['goodsName'] 			= $skumsg['goodsName'];
			$skuStockList['goodsCost'] 			= $skumsg['goodsCost'];
			$skuStockList['goodsWeight'] 			= $skumsg['goodsWeight'];
			$skuStockList['AverageDailyCount'] 	= $skuStatics['AverageDailyCount'];
			$skuStockList['waitingSendCount'] 	= $skuStatics['waitingSendCount'];
			$skuStockList['xuniCount'] 			= $skuStatics['waitingSendCount'];
			$skuStockList['goodsStatus'] 			= $skumsg['goodsStatus'];
			$path						 				= $skumsg['goodsCategory'];
			$cateName					 				= GoodsModel::getCategoryInfoByPath($path);
			$skuStockList['cateName']				= $cateName['name'];
			$skuStockList['isNew'] 				= $skumsg['isNew'];
			$skuStockList['pmId'] 				= $skumsg['pmId'];
			$pmName										= GoodsModel::getMaterInfoById($skumsg['pmId']);
			$skuStockList['pmName']				= $pmName['pmName'];
			//print_r($pmName);
			$pName										= $skumsg['purchaseId'];
			$skuStockList['pName'] 				= UserModel::getUsernameById($pName);
			$skuStockList['isPacking'] 			= $skumsg['isPacking'];
			$skuStockList['whName'] 				= "深圳A仓";
			$skuStock_arr[] = $skuStockList;
		}
		$this->smarty->assign("skuStockList",$skuStock_arr);
		//print_r($skuStockList);
		$this->smarty->display("skuInfo.htm");
	}

}   