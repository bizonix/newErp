<?php

/*
 * 运费查询页
 */

class queryView {
    private $tp_obj = null;
    
    /*
     * 初始化模板常量
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    public function view_showform(){
        $navar = array('运输方式查询');  //导航数据
        
        $queryobj = new shipfeeQueryModel();
        $addrlist = $queryobj->getAllShipAddrList();        //发货地列表
        $countrylist = $queryobj->getStandardCountryName(); //标准国家名称列表
        $carrierlist = $queryobj->getCarrierAllList();      //获得所有的运输方式

        $this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->set_var('module','运输方式查询');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     //生导航
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->set_file('center', 'transportquery.html');
        
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist'); //设置导航
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }
        
        $this->tp_obj->set_block('center', 'addr', 'addr_l');    //发货地列表
        foreach ($addrlist as $value) {
            $this->tp_obj->set_var('name', $value['addressNameCn']);
            $this->tp_obj->set_var('id', $value['id']);
            $this->tp_obj->parse('addr_l', 'addr', TRUE);
        }
        
        $this->tp_obj->set_block('center', 'countrylist', 'country_l');     //国家列表
        foreach ($countrylist as $cunval) {
            $this->tp_obj->set_var('countryname', $cunval['countryNameEn']);
            $this->tp_obj->set_var('cid', $cunval['id']);
            $this->tp_obj->set_var('cncountryname', $cunval['countryNameCn']);
            $this->tp_obj->parse('country_l', 'countrylist', TRUE);
        }
        $this->tp_obj->set_var('username', $_SESSION['userName']);
        $this->tp_obj->set_block('center', 'carrier', 'carrier_l');     //运输方式列表
        foreach ($carrierlist as $carval) {
            $this->tp_obj->set_var('cid', $carval['id']);
            $this->tp_obj->set_var('carriername', $carval['carrierNameCn']);
            $this->tp_obj->parse('carrier_l', 'carrier', TRUE);
        }
        
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
    
    /*
     * 根据查询信息返回结果集
     */
    public function view_getquerylist(){
        $shippingaddr = isset($_POST['shaddr']) ? abs(intval($_POST['shaddr'])) : 0;    //发货地
        $target = isset($_POST['stdcountry']) ? abs(intval($_POST['stdcountry'])) : 0;                //发往国际
        $weight = isset($_POST['weight']) ? abs(floatval($_POST['weight'])) : 0;        //重量
        $carrier = isset($_POST['carrier']) ? abs(intval($_POST['carrier'])) : 0;   //运输方式
        $postcode = isset($_POST['postcode']) ? trim($_POST['postcode']) : '';
        
        if(empty($shippingaddr)){   //没指定发货地
            $urldata = array('msg'=>array('请选择发货地'),'link'=>'index.php?mod=query&act=showform');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        if(empty($target)){   //没指定发往国家
            $urldata = array('msg'=>array('请输入发往国际'),'link'=>'index.php?mod=query&act=showform');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        if(empty($weight)){   //没有重量
            $urldata = array('msg'=>array('请输入包裹重量！'),'link'=>'index.php?mod=query&act=showform');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        
        $queryobj = new shipfeeQueryModel();
        
        /* 检测指定标准国家是否存在 */
        $stdcountryinfo = $queryobj->getStdCountryNameById($target);
        $carrierlist = $queryobj->getCarrierAllList();      //获得所有的运输方式
        
        if(empty($stdcountryinfo)){    //没有找到对应的标准国家名称信息 报错
            $urldata = array('msg'=>array('没找到国家！'),'link'=>'index.php?mod=query&act=showform');
            $urldata = urlencode(json_encode($urldata));
            header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
            exit;
        }
        //var_dump($stdcountryinfo['countryNameEn']);
        
        /*根据发货地获取相应的发货方式列表*/
        $shiplist = $queryobj->getShipListByShipaddr($shippingaddr);
        //print_r($shiplist);
        //echo $carrier;exit;
        if(!empty($carrier)){   //如果选择了运输方式 验证改运输方式是否存在于选择的发货地
            $exist = FALSE;
            foreach($shiplist as $shval){
                if($shval['id'] == intval($carrier)){       
                    $exist = TRUE;
                    unset($shiplist);
                    $shiplist = array($shval);
                    break;
                }
            }
            if(!$exist){    //不存在 则报错退出
                $urldata = array('msg'=>array('发货地和发货方式不匹配！'),'link'=>'index.php?mod=query&act=showform');
                $urldata = urlencode(json_encode($urldata));
                header('location:index.php?mod=showerror&act=showerror&data='.$urldata);
                exit;
            }
        }
        //var_dump($shiplist);exit;
        /* 计算每一种发货方式的运费 */
        $shipcalculateresult = array();     //运输方式的计算结果集
        foreach ($shiplist as $shipval){
            $result = array();
            $channel = $queryobj->getChannelId($stdcountryinfo['countryNameEn'], $shipval['id']);
            //var_dump($channel);
            if(empty($channel)){    //没找到合适的渠道信息 则跳过该运输方式
                continue;
            }
            $result['chname'] = $channel['channelName'];        //渠道名
            $result['carriername'] = $shipval['carrierNameCn']; //运输方式名
            $result['paname'] = $channel['partitionName'];      //分区名称
            //var_dump($channel);
            $carriercountryname = $queryobj->translateStdCountryNameToShipCountryName($stdcountryinfo['countryNameEn'], $shipval['id']);

            $re = $queryobj->calculateShipfee($channel['channelAlias'], $weight, $carriercountryname, array('postcode'=>$postcode));
            if(!$re){   //返回false 则跳过改运输方式
                continue;
            }
            $result['shipfee'] = $re['fee'];
            $result['rate'] = $re['discount'];
            //$shipcalculateresult[] = array('chanel'=>$channel, 'fee'=>$reusult);
            $shipcalculateresult[] = $result;
        }
        //var_dump($shipcalculateresult);exit;
        
        /***         生成页面            */
        $navar = array('运输方式查询','>',"$stdcountryinfo[countryNameEn]|$weight KG");  //导航数据
        
        $queryobj = new shipfeeQueryModel();
        $addrlist = $queryobj->getAllShipAddrList();        //发货地列表
        $countrylist = $queryobj->getStandardCountryName(); //标准国家名称列表
        
        $this->tp_obj->set_file('header','header.html');
        $this->tp_obj->set_file('footer','footer.html');
        $this->tp_obj->set_var('module','运输方式查询');
        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->set_file('navdiv','transmanagernav.html');     //生导航
        $this->tp_obj->parse('navdiv', 'navdiv');
        $this->tp_obj->set_file('center', 'queryresult.html');
        
        $this->tp_obj->set_block('navdiv', 'navlist', 'llist'); //设置导航
        foreach ($navar as $nav){
            $this->tp_obj->set_var('location', $nav);
            $this->tp_obj->parse('llist','navlist', TRUE );
        }
        
        $this->tp_obj->set_block('center', 'addr', 'addr_l');    //发货地列表
        foreach ($addrlist as $value) {
            $this->tp_obj->set_var('name', $value['addressNameCn']);
            $this->tp_obj->set_var('id', $value['id']);
            $this->tp_obj->parse('addr_l', 'addr', TRUE);
        }
        
        /*  国家列表  */
        $this->tp_obj->set_block('center', 'countrylist', 'country_l');
        
        foreach ($countrylist as $cunval) {
            $this->tp_obj->set_var('countryname', $cunval['countryNameEn']);
            $this->tp_obj->set_var('cid', $cunval['id']);
            if($cunval['id'] == $target ){//echo $target;
                $this->tp_obj->set_var('selected', 'selected=selected');
            }else{
                $this->tp_obj->set_var('selected', '');
            }
            $this->tp_obj->set_var('cncountryname', $cunval['countryNameCn']);
            $this->tp_obj->parse('country_l', 'countrylist', TRUE);
        }
        
        $this->tp_obj->set_var('postcode', $postcode);
        
        $this->tp_obj->set_block('center', 'carrier', 'carrier_l');     //运输方式列表
        //$this->tp_obj->set_var('carselected', 'selected=selected');
        foreach ($carrierlist as $carval) {
            $this->tp_obj->set_var('cid', $carval['id']);
            if($carval['id'] == $carrier){
                $this->tp_obj->set_var('carselected', 'selected=selected');
            }else{
                $this->tp_obj->set_var('carselected', '');
            }
            $this->tp_obj->set_var('carriername', $carval['carrierNameCn']);
            $this->tp_obj->parse('carrier_l', 'carrier', TRUE);
        }
        $this->tp_obj->set_var('weight', $weight);
        
        $this->tp_obj->set_block('center', 'rowlist', 'row_l');
        foreach ($shipcalculateresult as $rlist) {
            $this->tp_obj->set_var('carrier', $rlist['carriername']);
            $this->tp_obj->set_var('channel', $rlist['chname']);
            $this->tp_obj->set_var('area', $rlist['paname']);
            $this->tp_obj->set_var('fee', $rlist['shipfee']);
            $this->tp_obj->set_var('discount', $rlist['rate']);
            $this->tp_obj->parse('row_l', 'rowlist', TRUE);
        }
        
        $this->tp_obj->parse('center', 'center');
        $this->tp_obj->p('center');
    }
}