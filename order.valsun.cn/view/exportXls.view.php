<?php
/*
 * 添加订单
 *add by:hws
 */

class ExportXlsView extends BaseView{  
	
    //汇率管理页面
    public function view_index(){

        $toptitle = '报表导出页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('toplevel', 5);
		
		$OmAccountAct = new OmAccountAct();

		$reportAct		= new ExcelExportAct;

    	$ebayAccountList = $OmAccountAct->act_getEbayAccountList();
    	$b2bAccountList  = $OmAccountAct->act_getB2BAccountList();
		$neweggAccountList  = $OmAccountAct->act_getNeweggAccountList();
		$ebayAccountList	= $OmAccountAct->act_ebayaccountAllList();
		$amazonAccountList	= $OmAccountAct->act_amazonaccountAllList();
		$dresslinkAccountList	= $OmAccountAct->act_dresslinkaccountAllList();
    	$aliexpressAccountList  = $OmAccountAct->act_getAccountListByPid(2);
		$innerAccountList  = $OmAccountAct->act_getINNERAccountList();
		$aliexpressAccountList = json_decode($aliexpressAccountList,true);
		
		$transAPI   =   new TransAPIAct();
		$transType	=	$transAPI->act_getChannelistByApi();
		
		//var_dump($aliexpressAccountList);
        $allAccountList  = $OmAccountAct->act_getAllAccountList(); 

		$priceInfoUrl	= $reportAct->act_priceInfoReport();

		$this->smarty->assign("ebayAccountList", $ebayAccountList);
		$this->smarty->assign("b2bAccountList", $b2bAccountList);
		$this->smarty->assign("neweggAccountList", $neweggAccountList);
		$this->smarty->assign("innerAccountList", $innerAccountList);
		$this->smarty->assign("dresslinkAccountList", $dresslinkAccountList);
		$this->smarty->assign("aliexpressAccountList", $aliexpressAccountList);
        $this->smarty->assign("allAccountList", $allAccountList);
		$this->smarty->assign("amazonAccountList", $amazonAccountList);
		$this->smarty->assign("transType", $transType);
		$this->smarty->assign("priceInfoUrl",$priceInfoUrl);
		
	
		$startTime	= date('Y-m-d ').' 00:00:00';
		$endTime	= date('Y-m-d ').' 23:59:59';
		$chkTime    = date('Y-m-d ');
		$this->smarty->assign("curStartTime", $startTime);
		$this->smarty->assign("curEndTime", $endTime);
		$this->smarty->assign("chkTime", $chkTime);//单个时间
		$this->smarty->display("exportXls.htm");

    }
	public function view_aliexpress_app(){
		//print_r($_GET);
		$start = isset($_GET['start'])?$_GET['start']:"";
		$end = isset($_GET['end'])?$_GET['end']:"";
		$account = isset($_GET['account'])?iconv("UTF-8","UTF-8",$_GET['account']):"";
		$accounts = explode("#",$account);
		$account = implode(",",$accounts);
		$start_time = strtotime($start);
		$end_time = strtotime($end);
		$where = " where createdTime between {$start_time} and {$end_time} and accountId in ({$account})";
		$appraises = OmAvailableModel::getTNameList("om_order_detail_appraisal","*",$where);
		//print_r($appraises);exit;
		$exporter = new ExportDataExcel("browser", "appraise".$date.".xls");
		
		$exporter->initialize(); // starts streaming data to web browser
		$exporter->addRow(array("留评价日期","发货日期", "Store Name","Buyer ID","订单号","料号","数量", "国家","包装员","原评价","original feedback content","差评原因1","差评原因2","备注"));
        foreach($appraises as $key=>$value){
			$order = OmAvailableModel::getTNameList("om_unshipped_order","*","where id={$value['omOrderId']}");
			//print_r($order);
			$account_name = OmAvailableModel::getTNameList("om_account","*","where id={$value['accountId']}");
			$userInfo  = OmAvailableModel::getTNameList("om_unshipped_order_userInfo","*","where omOrderId={$value['omOrderId']}");
			$reasons = OmAvailableModel::getTNameList("om_order_refund_reason","*"," where typeId=3");
			$shippedTime = !empty($order[0]['ShippedTime'])?date("Y-m-d",$order[0]['ShippedTime']):"";
			$reason = array();
			foreach($reasons as $k => $v){
				$reason[$v['id']] = $v['reason'];
			}
			
			if($value['type']==0){
				$type = "中评";
			}else{
				$type = "差评";
			}
			$exporter->addRow(array(date("Y-m-d",$value['createdTime']),$shippedTime,$account_name[0]['account'],$userInfo[0]['platformUsername'],$order[0]['recordNumber'],$value['sku'],$value['amount'],$userInfo[0]['countryName'],"",$type,"",$reason[$value['reason1']],$reason[$value['reason2']],$value['remark']));
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
		exit();
	}
	
	public function view_hand_refund(){
		//print_r($_GET);
		$start = isset($_GET['start'])?$_GET['start']:"";
		$end = isset($_GET['end'])?$_GET['end']:"";
		$account = isset($_GET['account'])?iconv("UTF-8","UTF-8",$_GET['account']):"";
		$accounts = explode("#",$account);
		$account = implode(",",$accounts);
		$start_time = strtotime($start);
		$end_time = strtotime($end);
		$where = " where createdTime between {$start_time} and {$end_time} and accountId in ({$account})";
		$appraises = OmAvailableModel::getTNameList("om_order_detail_appraisal","*",$where);
		//print_r($appraises);exit;
		$exporter = new ExportDataExcel("browser", "Manual_Refund".date('Y-m-d').".xls");
		
		$exporter->initialize(); // starts streaming data to web browser
		$exporter->addRow(array("扫描日期","Store", "订单编号","买家ID","仓位号","料号","数量","国家","包裹总金额","币种","包装员","退款原因","paypal","备注","退款日期","运输方式","退款金额","物品总金额","币种","退款比例","标记","操作员","统计员"));
        foreach($appraises as $key=>$value){
			$order = OmAvailableModel::getTNameList("om_unshipped_order","*","where id={$value['omOrderId']}");
			//print_r($order);
			$account_name = OmAvailableModel::getTNameList("om_account","*","where id={$value['accountId']}");
			$userInfo  = OmAvailableModel::getTNameList("om_unshipped_order_userInfo","*","where omOrderId={$value['omOrderId']}");
			$reasons = OmAvailableModel::getTNameList("om_order_refund_reason","*"," where typeId=3");
			$shippedTime = !empty($order[0]['ShippedTime'])?date("Y-m-d",$order[0]['ShippedTime']):"";
			$reason = array();
			foreach($reasons as $k => $v){
				$reason[$v['id']] = $v['reason'];
			}
			
			if($value['type']==0){
				$type = "中评";
			}else{
				$type = "差评";
			}
			$exporter->addRow(array(date("Y-m-d",$value['createdTime']),$shippedTime,$account_name[0]['account'],$userInfo[0]['platformUsername'],$order[0]['recordNumber'],$value['sku'],$value['amount'],$userInfo[0]['countryName'],"",$type,"",$reason[$value['reason1']],$reason[$value['reason2']],$value['remark']));
		}
		$exporter->finalize(); // writes the footer, flushes remaining data to browser.
		
		exit();
	}
    
    
    public function view_ebayTest(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_ebayTest();
        //$fileName = $exportXlsAct->act_ebayTest();
        //header("Location:$fileName");
    }
    public function view_ebayNoScan(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_ebayNoScan();
    }
    public function view_aliBatchShipOrderFormat(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_aliBatchShipOrderFormat();
    }
    public function view_paypalRefund(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_paypalRefund();
    }
    public  function view_aliTagShipLog(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_aliTagShipLog();
    }
    public function view_b2bSale(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_b2bSale();
        //$fileName = $exportXlsAct->act_b2bSale();
        //header("Location:$fileName");
    }
	
	//新蛋帐号数据导出
    public function view_newegg_export(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_newegg_export();
    }
    
	//邮资报表数据导出
    public function view_xlsbaobiao4(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_xlsbaobiao4();
    }
    
    public function view_innerSale(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_innerSale();
        //$fileName = $exportXlsAct->act_b2bSale();
        //header("Location:$fileName");
    }
    
    public function view_amazonSale(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_amazonSale();
        //$fileName = $exportXlsAct->act_b2bSale();
        //header("Location:$fileName");
    }
    
    public function view_dressLinkSale(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_dressLinkSale();
        //$fileName = $exportXlsAct->act_b2bSale();
        //header("Location:$fileName");
    }

	//亚马逊入库订单数据导出
    public function view_amazonInStockExport(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_amazonInStockExport();
    }

	//手工退款数据导出
    public function view_manualRefundxls(){
        $exportXlsAct = new ExcelExportAct();
        $exportXlsAct->act_manualRefundxls();
    }
	//海外仓销售报表-新版导出	
	public function view_ebayOversea() {
		$exportXlsAct = new ExcelExportAct();
		$exportXlsAct->act_ebayOversea();
	}
	
	//新EUB跟踪号报表导出
	public function view_eubTrucknumber () {
		$exportXlsAct = new ExcelExportAct();
		$eubTrucknumberData = $exportXlsAct->act_eubTrucknumber();
		if(!$eubTrucknumberData) {
			//echo $exportXlsAct::$errCode.": ".$exportXlsAct::$errMsg;
			exit;
		}
	}

	public function view_repeatShipments(){
		$exportXlsAct = new ExcelExportAct();
		$eubTrucknumberData = $exportXlsAct->act_repeatShipments();
	}
}