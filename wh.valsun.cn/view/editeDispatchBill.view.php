<?php

/** 
 * @author 发货单编辑
 * 
 */
class EditeDispatchBillView extends CommonView
{

    /**
     * 构造函数
     */
    public function __construct ()
    {
        parent::__construct();
    }
    
    /*
     * 编辑表单页面
     */
    public function view_editeForm() {
    	$orderid = isset($_GET['orderid']) ? intval($_GET['orderid']) : 0;
    	if (empty($orderid)) {     //没有传入订单号
    	    $msgdata = array(
    	            'data'=>array('请指定发货单号！'),
    	            'link'=>$_SERVER['HTTP_REFERER']
    	    );
    	    goErrMsgPage($msgdata);
    	    exit();
    	}
    	
    	$po_obj = new PackingOrderModel();
    	$orderinfo = $po_obj->getOrderInfoById($orderid);
    	if (empty($orderinfo)) {
    		$msgdata = array(
    	            'data'=>array('单号不存在！'),
    	            'link'=>$_SERVER['HTTP_REFERER']
    	    );
    	    goErrMsgPage($msgdata);
    	    exit();
    	}
    	
    	//获得配货记录信息
    	$po_obj = new ShipingOrderDetailModel();
    	$orderrecords = $po_obj->getShippingOrderRecordsById($orderid);
    	if (!empty($orderrecords)) {    
    		$this->smarty->assign('actualweight',$orderrecords['actualWeight']);
    		$this->smarty->assign('actualshipfee', $orderrecords['actualShipping']);
    	}
    	
    	$this->smarty->assign('recipient',$orderinfo['username']);
    	$this->smarty->assign('email',$orderinfo['email']);
    	$this->smarty->assign('country',$orderinfo['countryName']);
    	$this->smarty->assign('abbreviation',$orderinfo['countrySn']);
    	$this->smarty->assign('state',$orderinfo['state']);
    	$this->smarty->assign('city',$orderinfo['city']);
    	$this->smarty->assign('street',$orderinfo['street']);
    	$this->smarty->assign('address2',$orderinfo['address2']);
    	$this->smarty->assign('address3',$orderinfo['address3']);
    	$this->smarty->assign('currency',$orderinfo['currency']);
    	$this->smarty->assign('shipping',$orderinfo['transportId']);
    	$this->smarty->assign('sellaccount',$orderinfo['account']);
    	$this->smarty->assign('status',$orderinfo['orderStatus']);
    	$this->smarty->assign('channel',$orderinfo['channelId']);
    	$this->smarty->assign('calcWeight',$orderinfo['calcWeight']);
    	$this->smarty->assign('calcShipping',$orderinfo['calcShipping']);
    	$this->smarty->assign('orderid',$orderid);
    	//print_r($orderinfo);exit;
    	
    	$shippinglist = ShipingTypeModel::getShipingTypeList();
    	$this->smarty->assign('shippinglist',$shippinglist);
    	
    	$navlist = array(           //面包屑
    	        array('url' => 'index.php?mod=dispatchBillQuery&act=showForm', 'title' => '出库'),
    	        array('url' => '', 'title' => '发货单编辑'),
    	);
    	$this->smarty->assign('navlist', $navlist);
    	
    	$toptitle = '发货单编辑--单号【'.$orderid.'】';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
    	
    	$toplevel = 2;      //顶层菜单
    	$this->smarty->assign('toplevel',$toplevel);
    	
    	$this->smarty->assign('secnev', 3);     //二级导航
    	
    	$this->smarty->display('showeditform.htm');
    }
    
    /*
     * 编辑页面提交表单
     */
    public function view_editeSubmit(){
        
        $recipient = isset($_POST['recipient']) ? trim($_POST['recipient']) : '';   //收件人
        $recipient = mysql_real_escape_string($recipient);
        
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';   //邮箱
        $email = mysql_real_escape_string($email);
        
        $countryname = isset($_POST['countryname']) ? trim($_POST['countryname']) : ''; //国家
        $countryname = mysql_real_escape_string($countryname);
        //echo $countryname;exit;
        
        $abbreviation = isset($_POST['abbreviation']) ? trim($_POST['abbreviation']) : ''; //国家简称
        $abbreviation = mysql_real_escape_string($abbreviation);
        
        $state = isset($_POST['state']) ? trim($_POST['state']) : ''; //州/省
        $state = mysql_real_escape_string($state);
        
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';  //市
        $city = mysql_real_escape_string($city);
        
        $street = isset($_POST['street']) ? trim($_POST['street']) : ''; //街道
        $street = mysql_real_escape_string($street);
        
        $address2 = isset($_POST['address2']) ? trim($_POST['address2']) : ''; //地址二
        $address2 = mysql_real_escape_string($address2);
        
        $address3 = isset($_POST['address3']) ? trim($_POST['address3']) : ''; //地址三
        $address3 = mysql_real_escape_string($address3);
        
        $currency = isset($_POST['currency']) ? trim($_POST['currency']) : ''; //币种
        $currency = mysql_real_escape_string($currency);
        
        $shipping = isset($_POST['shippingtype']) ? intval($_POST['shippingtype']) : 0; //运输方式id
        $sellaccount = isset($_POST['sellaccount']) ? trim($_POST['sellaccount']) : ''; //销售账号
        $sellaccount = mysql_real_escape_string($sellaccount);
        
        //$channel = isset($_POST['channel']) ? intval($_POST['channelId']) : 0;  //渠道id
        $calcWeight = isset($_POST['calcWeight']) ? floatval($_POST['calcWeight']) : 0; //估算重量
        $calcShipping = isset($_POST['calcShipping']) ? floatval($_POST['calcShipping']) : 0; //计算运费
        $actualweight = isset($_POST['actualweight']) ? floatval($_POST['actualweight']) : 0; //实际重量
        $acturalfee = isset($_POST['acturalfee']) ? floatval($_POST['acturalfee']) : 0; //实际重量
        $orderid = isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
        if($orderid < 1){    //id不合法
            $data = array('data'=>'发货单号不正确！', 'link'=>'index.php?mod=dispatchBillQuery&act=showForm');
            goErrMsgPage($data);
            exit;
        }
        
        $str = "
                username='$recipient', email='$email', countryName='$countryname',countrySn='$abbreviation',
                state='$state', city='$city', street='$street', address2='$address2', address3='$address3',
                currency='$currency', transportId=$shipping, account='$sellaccount', calcWeight='$calcWeight',
                calcShipping=$calcShipping
               ";
        //echo $str;exit;
        $sod_obj = new ShipingOrderDetailModel();
        $recordinfo = $sod_obj->getShippingOrderRecordsById($orderid);
        if (!empty($recordinfo)){   //更新记录信息
            $str1 = " actualWeight=$actualweight , actualShipping=$acturalfee";
            $ur = $sod_obj->updateRecords($str1, ' and shipOrderId='.$orderid);
            if (!$ur) { //更新失败
                $data = array('data'=>array('更新失败！'), 'link'=>'index.php?mod=dispatchBillQuery&act=showForm');
                goErrMsgPage($data);
                exit;
            }
        }
        
        $po_obj = new PackingOrderModel();
        
        $upre = $po_obj->updateShipingorder($str, ' and id='.$orderid);
        if ($upre) {    //更新成功
        	$data = array('data'=>array('更新成功！'), 'link'=>'index.php?mod=dispatchBillQuery&act=showForm');
        	goOkMsgPage($data);
        	exit;
        } else {
            $data = array('data'=>array('更新失败！'), 'link'=>'index.php?mod=dispatchBillQuery&act=showForm');
            goErrMsgPage($data);
            exit;
        }
    }
}

?>