<?php
/*
 * 添加订单
 * @add by lzx ,date 20140609
 */
class OrderAddView extends BaseView{

    public function __construct() {
    	parent::__construct();
    }

	/**
	 * 添加普通线下订单页面（目前是出口通订单添加）
	 * 需要和对应的用户平台、账号权限对应，只有自己有权限的才能添加
	 * 获取相关权限  A('UserCompetence')->act_getCompetenceByUserId(get_userid())
	 *
	 */
    public function view_addOfflineOrder(){
		//渲染代码
        /**测试代码
        $orderDetail = array();
        $orderDetail['orderDetail']['omOrderId'] = 11973922;
        $orderDetail['orderDetail']['recordNumber'] = 1111122;
        $orderDetail['orderDetail']['itemPrice'] = 11;
        $orderDetail['orderDetail']['ORsku'] = '001';
        $orderDetail['orderDetail']['sku'] = '002';
        $orderDetail['orderDetail']['onlinesku'] = '003';
        $orderDetail['orderDetail']['amount'] = 2;
        $orderDetail['orderDetail']['shippingFee'] = 1;
        $orderDetail['orderDetail']['reviews'] = 2;
        $orderDetail['orderDetail']['createdTime'] = time();
        $orderDetail['orderDetail']['storeId'] = 1;
        $orderDetail['orderDetail']['is_delete'] = 0;
        $orderDetail['orderDetailExtension']['itemId'] = 111111111;
        $orderDetail['orderDetailExtension']['transId'] = 233333;
        $orderDetail['orderDetailExtension']['itemTitle'] = 'dddddddddd';
        $orderDetail['orderDetailExtension']['itemURL'] = '111111111';
        $orderDetail['orderDetailExtension']['shippingType'] = 'sdsddddd';
        $orderDetail['orderDetailExtension']['FinalValueFee'] = 4.23;
        $orderDetail['orderDetailExtension']['FeeOrCreditAmount'] = 4.23;
        $orderDetail['orderDetailExtension']['ListingType'] = 'ddddddd';
        $orderDetail['orderDetailExtension']['note'] = '111111111';
        M('OrderAdd')->insertOrderDetailPerfect($orderDetail);
        **/
        F('order');
        $showPlatformList = array(3);//目前允许前端可见的平台id数组
        $platformList = array_keys(get_userplatacountpower(get_userid())?get_userplatacountpower(get_userid()):array());//获取登陆人的平台权限
        $platformList = array_intersect($showPlatformList, $platformList);
        $this->smarty->assign('platform_list', $platformList);
        $Shiping = M('InterfaceTran')->getCarrierList(2);//获取所有的运输方式
		$this->smarty->assign('Shiping', $Shiping);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 21;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '订单添加');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
		$this->smarty->display('orderAdd.htm');
    }

    /**
	 * aliexpress订单导入页面
	 */
    public function view_addAliexpressOrder(){
		//渲染代码
        $aliexpressAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 2);//速卖通平台下的权限账号
        $this->smarty->assign('aliexpressAccountList', $aliexpressAccountList);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 24;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '速卖通订单导入');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
		$this->smarty->display('aliexpressImport.htm');
    }

    /**
	 * 添加aliexpress线下订单页面
	 */
    public function view_addAliexpressOfflineOrder(){
		//渲染代码
        $aliexpressAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 2);//速卖通平台下的权限账号
        $this->smarty->assign('aliexpressAccountList', $aliexpressAccountList);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 25;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '速卖通线下订单导入');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
		$this->smarty->display('aliexpressUnderlineImport.htm');
    }

    /**
	 * 敦煌订单导入页面
	 */
    public function view_addDhgateOrder(){
		//渲染代码
        $dhgateAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 4);//DH平台下的权限账号
        $this->smarty->assign('dhgateAccountList', $dhgateAccountList);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 26;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '敦煌订单导入');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
		$this->smarty->display('dhgateImport.htm');
    }

	/**
	 * 诚信通订单导入页面
	 */
    public function view_addTrustOrder(){
		//渲染代码
        $aliexpressAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 2);//DH平台下的权限账号
        $this->smarty->assign('aliexpressAccountList', $aliexpressAccountList);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 29;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '诚信通订单导入');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
		$this->smarty->display('trustImport.htm');
    }

    /**
	 * 添加ebay线下订单页面
	 */
    public function view_addEbayOfflineOrder(){
		//渲染代码
    }

	/**
	 * 添加amazon线下订单页面
	 */
    public function view_addAmazonOfflineOrder(){
		//渲染代码
    }

	/**
	 * 国内通用订单导入页面
	 */
    public function view_addDomesticOrder(){
		//渲染代码
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 22;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '国内通用订单导入');
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
        $this->smarty->display('underLineOrderImport.htm');
    }

	/**
	 * 国内销售订单导入页面
	 */
    public function view_addDomesticSaleOrder(){
		//渲染代码
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 220;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '国内销售订单导入');
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
        $this->smarty->display('underLineSaleOrderImport.htm');
    }

	/**
	 * 独立商城订单导入页面
	 */
    public function view_addDresslinkOrder(){
		//渲染代码
        $dlAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 8);//dl平台下的权限账号
        $cnAccountList = A('UserCompetence')->act_getAccountListByPlatform(get_userid(), 10);//cn平台下的权限账号
        $cndlAccountList = $dlAccountList + $cnAccountList;
        //print_r($cndlAccountList);exit;
        $this->smarty->assign('cndlAccountList', $cndlAccountList);
        $toplevel = 2;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);
        $secondlevel = 210;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '独立商城订单导入');
		$this->smarty->assign('curusername', get_username());//SESSION['userName']
        $this->smarty->assign('httpHost','http://'.$_SERVER['HTTP_HOST']);
		$this->smarty->display('cndlImport.htm');
    }

	/**
	 * 添加普通线下订单页面
	 * 需要和对应的用户平台、账号权限对应，只有自己有权限的才能添加
	 * 获取相关权限  A('UserCompetence')->act_getCompetenceByUserId(get_userid())
	 * 以下为demo
	 * @return null
	 * @author lzx
	 */
    public function view_insertOfflineOrder(){
		if (A('OrderAdd')->act_insertOfflineOrder()){
			$this->success(A('OrderAdd')->act_getErrorMsg(), 'index.php?mod=orderAdd&act=addOfflineOrder');
		}else {
			$errorinfo    = A('OrderAdd')->act_getErrorMsg();
    		$msg          = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
    		$this->notJump($msg, 'index.php?mod=orderAdd&act=addOfflineOrder');
		}
    }

	/**
	 * 添加ebay线下订单页面
	 */
    public function view_insertEbayOfflineOrder(){
		A('OrderAdd')->act_insertEbayOfflineOrder();
    }

	/**
	 * 添加amazon线下订单页面
	 */
    public function view_insertAmazonOfflineOrder(){
		A('OrderAdd')->act_insertAmazonOfflineOrder();
    }

	/**
	 * 国内通用订单导入页面
	 */
    public function view_insertDomesticOrder(){
		A('OrderAdd')->act_insertDomesticOrder();
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addDomesticOrder');
    }

	/**
	 * 国内销售订单导入页面
	 */
    public function view_insertDomesticSaleOrder(){
		A('OrderAdd')->act_insertDomesticOrder();
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addDomesticSaleOrder');
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
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addAliexpressOfflineOrder');
    }

	/**
	 * 添加敦煌订单
	 */
    public function view_insertDhgateOrder(){
		A('OrderAdd')->act_insertDhgateOrder();
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addDhgateOrder');
    }

	/**
	 * 添加诚信通订单
	 */
    public function view_insertTrustOrder(){
		A('OrderAdd')->act_insertTrustOrder();
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addTrustOrder');
    }

	/**
	 * 独立商城订单导入页面
	 */
    public function view_insertDresslinkOrder(){
		A('OrderAdd')->act_insertDresslinkOrder();
        $errorinfo = A('OrderAdd')->act_getErrorMsg();
        $msg       = empty($errorinfo) ? get_promptmsg(10016) : implode('<br>', $errorinfo);
        $this->notJump($msg, 'index.php?mod=orderAdd&act=addDresslinkOrder');
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