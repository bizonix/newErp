<?php
/*
 * 数据导出
 * @add by zqt
 */
class ExportExcelOutputView extends BaseView{

    public function __construct() {
    	parent::__construct();
    }

    public function view_index(){
        F('order');//引入function方法
        $CompetencePAList = get_userplatacountpower(get_userid());//获取登陆人的平台账号权限
        $ebayAccountList = array();//定义该登陆人在ebay平台上的可见账号数组
        
        foreach($CompetencePAList as $value){
            $ebayAccountList = $CompetencePAList[1];
        }
        $this->smarty->assign('ebayAccountList', $ebayAccountList);
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('ExportExcelOutput'));
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('ExportExcelOutput'));
        $this->smarty->assign('toptitle', '报表导出');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
		$this->smarty->display('exportExcelOutput.htm');
    }

	/**
	 * ebay数据测试导出
	 * @author zqt
	 */
    public function view_ebayTestOutputOn(){
		A('exportExcelOutput')->act_exceltestData();
    }

	/**
	 * ebay漏扫描数据导出
	 */
    public function view_ebayNoScanOutputOn(){
		A('exportExcelOutput')->act_ebayNoScanData();//act_ebayNoScanOutputOn();
    }
	/**
	 * 速卖通批量发货单订单格式化导出
	 * @author yxd
	 */
    public function view_aliexpressOrderFromat(){
    	A("exportExcelOutput")->act_aliExpressOrderFormatData();
    }
    /**
     * paypal 退款数据导出:
     * @author yxd
     */
    public function view_ebayRefundOutputOn(){
    	A("exportExcelOutput")->act_paypalRefundData();
    }
    /**
     * 手工退款数据导出
     * @author yxd
     */
    public function view_handRefundOutputOn(){
    	A("exportExcelOutput")->act_handRefundData();
    }
    /**
     * B2B销售报表数据新版导出
     * @author yxd
     */
    public function view_B2BSaleOutputOn(){
    	A("exportExcelOutput")->act_B2BSaleData();
    }
    /**
     * 国内-销售报表数据新版导出
     * @author
     */
    public function view_InnerSaleOutputOn(){
    	A("exportExcelOutput")->act_InnerSaleData();
    }
	/**
	 * 添加amazon线下订单页面
	 */
    public function view_insertAmazonOfflineOrder(){
		A('OrderAdd')->act_insertAmazonOfflineOrder();
    }

	/**
	 * 国内销售订单导入页面
	 */
    public function view_insertDomesticOrder(){
		A('OrderAdd')->act_insertDomesticOrder();
    }

    /**
	 * 添加速卖通订单
	 */
    public function view_insertAliexpressOrder(){
        A('OrderAdd')->act_insertAliexpressOrder();
		$errorinfo = A('OrderAdd')->act_getErrorMsg();
		$msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
		$this->notJump($msg, 'index.php?mod=orderAdd&act=addAliexpressOrder');
    }

    /**
	 * 添加速卖通线下订单
	 */
    public function view_insertAliexpressOfflineOrder(){
		A('OrderAdd')->act_insertAliexpressOfflineOrder();
    }

	/**
	 * 添加敦煌订单
	 */
    public function view_insertDhgateOrder(){
		A('OrderAdd')->act_insertDhgateOrder();
    }

	/**
	 * 添加诚信通订单
	 */
    public function view_insertTrustOrder(){
		A('OrderAdd')->act_insertTrustOrder();
    }

	/**
	 * 独立商城订单导入页面
	 */
    public function view_insertDresslinkOrder(){
		A('OrderAdd')->act_insertDresslinkOrder();
    }

    /**
	 * 添加普通线下导入（出口通订单）,获取用户信息
	 */
    public function view_getBuyerinfo(){
		//渲染代码
        $this->ajaxReturn(A('Order')->act_getBuyerinfo(), A('Order')->act_getErrorMsg());
    }

    /**
	 * 添加普通线下导入（出口通订单）,平台联动，获取对应平台的账号，连接权限
	 */
    public function view_getAccountListByPlatform(){
		//渲染代码
        $this->ajaxReturn(A('UserCompetence')->act_getAccountListByPlatform(get_userid(), $_POST['platformId']), A('UserCompetence')->act_getErrorMsg());
    }

    /**
	 * 添加普通线下导入（出口通订单）中的填充SKU信息
	 */
    public function view_getSkuInfo(){
		//渲染代码
        $this->ajaxReturn(A('OrderAdd')->act_getSkuInfo(), A('OrderAdd')->act_getErrorMsg());
    }
}