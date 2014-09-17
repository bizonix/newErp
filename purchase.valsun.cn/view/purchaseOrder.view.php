<?php
class PurchaseOrderView extends BaseView {
    public function __construct(){
		parent:: __construct();
		if(isset($_GET["mod"]) && !empty($_GET["mod"])){
			$mod=$_GET["mod"];
		}
		if(isset($_GET["act"]) && !empty($_GET["act"])){
			$act=$_GET["act"];
		}
		$this->smarty->assign('act',$act);//模块权限
		$this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->caching 		= false;
		$this->smarty->debugging 	= false;
		$this->smarty->assign("WEB_API", WEB_API);
		$this->smarty->assign("WEB_URL", WEB_URL);
	}

	public function view_index(){
		error_reporting(0);
		$PO	= new PurchaseOrderAct();
		$number1 = $PO->calcNumber(1);
		$number2 = $PO->calcNumber(2);
		$number3 = $PO->calcNumber(3);
		$number4 = $PO->calcNumber(4);
		$data = $PO->index();
		$orderList = $data['orderInfo'];
		$total = $data['totalNum'];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
		$purchaseList = getPurchaseUserList();
		$powerArr = array("李美琴","陈月葵","潘旭东","陈小霞","覃云云","肖金华","郑凤娇","罗莹","周聪","萧秋月","刘念","覃云云","蔡丽宏","李玲");
		if(in_array($_SESSION['userCnName'],$powerArr)){
			$this->smarty->assign("power",1);
		}else{
			$this->smarty->assign("power",0);
		}
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
        $this->smarty->assign("pageStr",$pageStr);
		$this->smarty->assign("number1", $number1);
		$this->smarty->assign("number2", $number2);
		$this->smarty->assign("number3", $number3);
		$this->smarty->assign("number4", $number4);
		$this->smarty->assign('orderList',$orderList);//采购列表
		$this->smarty->assign('purchaseList',$purchaseList);//采购员列表
		$this->smarty->display("purchaseOrder.htm");
	}


	public function view_pickupList(){
		$PO	= new PurchaseOrderAct();
		$number1 = $PO->calcOwNumber(1);
		$number2 = $PO->calcOwNumber(2);
		$number3 = $PO->calcOwNumber(3);
		$number4 = $PO->calcOwNumber(4);
		$number5 = $PO->calcOwNumber(5);
		$data = $PO->pickOrder();
		$orderList = $data['orderInfo'];
		$total = $data['totalNum'];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
		$purchaseList = getPurchaseUserList();
		$powerArr = array("李美琴","陈月葵","潘旭东","陈小霞","覃云云","肖金华","郑凤娇","罗莹","周聪");
		if(in_array($_SESSION['userCnName'],$powerArr)){
			$this->smarty->assign("power",1);
		}else{
			$this->smarty->assign("power",0);
		}
		$tableColor = array("active","success"," ","warning"," ","danger");
        $this->smarty->assign("tableColor",$tableColor);
        $this->smarty->assign("pageStr",$pageStr);
		$this->smarty->assign("number1", $number1);
		$this->smarty->assign("number2", $number2);
		$this->smarty->assign("number3", $number3);
		$this->smarty->assign("number4", $number4);
		$this->smarty->assign("number5", $number5);
		$this->smarty->assign('orderList',$orderList);//采购列表
		$this->smarty->assign('purchaseList',$purchaseList);//采购员列表
		$this->smarty->display("pickupOrder.htm");
		//$this->smarty->display("purchaseOrder.htm");
	}

	public function view_editPurchaseOrder(){
		$id = $_GET['id'];
		$PO = new PurchaseOrderAct();
		$partnerList = $PO->partnerList();
		$storeList = $PO->storeList();
		$PhOrder = $PO->phOrder($id);
		$mainOrderInfo = $PO->getMainOrderInfo($id);
		$purchaseList	= getPurchaseUserList();
		//$partnerList	= CommonAct::actGetPartnerList();
		//$partnerList	= getPartnerlist();
		$this->smarty->assign("title","编辑采购订单页面");
		//$this->smarty->assign('partnerList',$partnerList);//供应商列表
		$this->smarty->assign('purchaseList',$purchaseList);//采购列表
		$this->smarty->assign("id",$id);
		$this->smarty->assign("storeList",$storeList);
		$this->smarty->assign("partnerList",$partnerList);
		$this->smarty->assign("PhOrder",$PhOrder);
		$this->smarty->assign("mainOrderInfo",$mainOrderInfo);//采购订单主表信息
		//$this->smarty->display("editPurchaseOrder.htm");
		$this->smarty->display("editOrder.htm");
	}

	public function view_editOwOrder(){
		$id = $_GET['id'];
		$PO = new PurchaseOrderAct();
		$partnerList = $PO->partnerList();
		$storeList = $PO->storeList();
		$PhOrder = $PO->phOrder($id);
		$mainOrderInfo = $PO->getMainOwOrderInfo($id);
		//$partnerList	= CommonAct::actGetPartnerList();
		//$partnerList	= getPartnerlist();
		$this->smarty->assign("title","编辑B仓备货订单页面");
		$this->smarty->assign("id",$id);
		$this->smarty->assign("partnerList",$partnerList);
		$this->smarty->assign("PhOrder",$PhOrder);
		$this->smarty->assign("mainOrderInfo",$mainOrderInfo);//采购订单主表信息
		//$this->smarty->display("editPurchaseOrder.htm");
		$this->smarty->display("editOwOrder.htm");
	}

	public function view_purchasehistoryprice(){
		if(isset($_GET["sku"]) && !empty($_GET["sku"])){
			$sku = $_GET["sku"];
		}else{
			echo "<script>window.history.back();</script>";
			return;
		}
		$PO = new PurchaseOrderAct;
		$list = $PO->purchasehistoryprice($sku);
		$this->smarty->assign("list",$list);
		$this->smarty->assign("title","历史采购价格");
		$this->smarty->display("purchasehistoryprice.htm");
	}

	public function view_searchCode(){
		$this->smarty->display("searchCode.htm");
	}

	public function view_system_adjust_transport(){
		$PO = new PurchaseOrderAct();
		$adjust_transport = $PO->system_adjust_transport($where='');
		$adjust_transport_list = $adjust_transport[0];
		$fpage = $adjust_transport[1];
		$total = $adjust_transport[2];
		$this->smarty->assign("fpage",$fpage);
		$this->smarty->assign("adjust_transport",$adjust_transport_list);
		$this->smarty->assign("title","特殊运输调整");
		$this->smarty->display("system_adjust_transport.htm");
	}

	public function view_checkSuperOrder(){
		@session_start();
		$auditStatusList = array('0'=>'未处理','1'=>'审核通过','2'=>'拦截');
		$superorderAct = new SuperorderAuditAct();
		$data = $superorderAct->getList();
		$listData = $data['listData'];
		$total = $data['totalNum'];
		$pagenum = 100;
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
		$this->smarty->assign("auditStatusList",$auditStatusList);
		$this->smarty->assign("pageStr",$pageStr);
		$this->smarty->assign("listData",$listData);
		$this->smarty->assign("title","超大订单审核");
		$this->smarty->display("bigOrder.htm");
	}

	public function view_checkSuperOrder_old(){
		//$purid          = $_SESSION[C('USER_AUTH_SYS_ID')];//采购员ID
		$purid = $_SESSION['sysUserId'];
		$pursename = $_SESSION['userCnName'];
		$orderListArr 	= CommonAct::getBigOrders_old($pursename);
		$orderListArr = json_decode($orderListArr,true);
		$orderList 		= $orderListArr["data"];
		//$data           = json_decode($orderList,true);
		/*
		foreach($data as $k){
			$skuInfo 	= $k['sku'];//sku详细信息
			$sku     	= $skuInfo['sku'];
			$warnInfo 	= PurchaseOrderAct::getWarnInfoBySku($sku);//获取每日均量、实际库存
		}
		 */
		//print_r($orderList);
		$this->smarty->assign("orderList",$orderList);
		$this->smarty->assign("skuInfo",$skuInfo);
		$this->smarty->assign("warnInfo",$warnInfo);
		$this->smarty->assign("purid",$purid);
		$this->smarty->assign("title","超大订单审核");
		$this->smarty->display("checkSuperOrder_old.htm");
	}

	//add by wxb 2013/09/20
	function view_purchase_sku_conversion(){
		$PO = new PurchaseOrderAct;
		$purchase_sku_conversion = $PO->purchase_sku_conversion();
		$this->smarty->assign("purchase_sku_conversion",$purchase_sku_conversion);
		$this->smarty->display("purchase_sku_conversion.htm");
	}

	function view_exportOrder(){
		$idlist = isset($_GET["data"]) ? $_GET['data'] : '';
		$data   = array();
		if(!empty($idlist)){
			$data   = explode(',',$idlist);
			$PO 		 = new PurchaseOrderAct;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
										 ->setLastModifiedBy("Maarten Balliauw")
										 ->setTitle("Office 2007 XLSX Test Document")
										 ->setSubject("Office 2007 XLSX Test Document")
										 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Test result file");

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','订货日期');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','订单号');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','供应商');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','料号');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','产品描述');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','订货数量');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','单价');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','金额');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','采购员');
			$dataArr = $PO->actExportOrder($data);
			$row = 2;
			$rownum = count($dataArr);
			for($ii = 0; $ii < $rownum; $ii++){
				$list = $dataArr[$ii];
				foreach($list as $v){
					$addtime 		= $v['addtime'];
					$recordnumber 	= $v['recordnumber'];
					$parname		= $v['parname'];
					$purname 		= $v['purname'];
					$sku 			= $v['sku'];
					$name 			= $v['name'];
					$price 			= $v['price'];
					$count 			= $v['count'];
					$totalmoney 	= $v['totalmoney'];
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, date("Y/m/d", $addtime));
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $recordnumber);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parname);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $name);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $count);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $price);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $totalmoney);
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $purname);
					$row++;
				}
			}
			$objPHPExcel->getActiveSheet(0)->getStyle('A1:N'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(80);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(25);
			$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);

			$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
			$title		= "Files_purchase".date('Y-m-d');
			$titlename	= $title.".xls";

			$objPHPExcel->getActiveSheet()->setTitle($title);
			$objPHPExcel->setActiveSheetIndex(0);

			header('Content-Type: application/vnd.ms-excel');
			header("Content-Disposition: attachment;filename={$titlename}");
			header('Cache-Control: max-age=0');

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
	}

	public function view_passSku(){
		$ebayid		= isset($_POST['ebayid']) ? trim($_POST['ebayid']) 	: false;
		$detailid	= isset($_POST['detailid']) ? trim($_POST['detailid']): false;
		$type		= isset($_POST['type']) ? trim($_POST['type']): false;
		$sku		= isset($_POST['sku']) ? trim($_POST['sku']) 			: false;
		$passornot	= isset($_POST['passornot']) ? trim($_POST['passornot']) 			: false;
		$pcontent	= isset($_POST['pcontent']) ? trim($_POST['pcontent']): false;
		$paramArr= array(
		/* API系统级输入参数 Start */
		'method'	=> 'erp.check.order',  //API名称
		'format'	=> 'json',  //返回格式
		'v'			=> '1.0',   //API版本号/
		'username'  => C('OPEN_SYS_USER'),
		'ebay_id' 	=> $ebayid,
		'detail_id' => $detailid,
		'sku' 		=> $sku,
		'type' 		=> $type,
		'check_status' => $passornot,
		'pcontent'	=> $pcontent
		);
		//print_r($paramArr);exit;
		$data 	= callOpenSystem($paramArr,"local");
		//var_dump($data);exit;
		//$data 	= json_decode($data, true);
		echo $data;
	}
	//跨订单迁移料号到货数量 add by wangminwei 2014-04-22
	public function view_moveOrderSku(){
		global $mod,$act;
		$nowTime = date('Y-m-d');
        $this->smarty->assign('title','订单料号转移');
		$this->smarty->assign('nowTime', $nowTime);
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('moveOrderSku.htm');
	}
}
?>
