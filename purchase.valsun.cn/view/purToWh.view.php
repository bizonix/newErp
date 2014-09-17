<?php
include "/data/web/purchase.valsun.cn/lib/PHPExcel.php";
class PurToWhView extends BaseView{
	public function view_index(){
	 	@session_start();
		global $mod,$act;
        $this->smarty->assign('title','收货管理表');
        $this->smarty->assign('mod',$mod);//模块权限
		$ser_ordersn		= isset($_GET['ser_ordersn']) ? $_GET['ser_ordersn'] : '';
		$ser_sku			= isset($_GET['ser_sku']) ? $_GET['ser_sku'] : '';
		$ser_parnter		= isset($_GET['ser_parnter']) ? $_GET['ser_parnter'] : '';
		$ser_status         = isset($_GET['ser_status']) ? $_GET['ser_status'] : '';
		$ser_orderstu       = isset($_GET['ser_orderstu']) ? $_GET['ser_orderstu'] : '';
		$ser_cguser         = isset($_GET['ser_cguser']) ? $_GET['ser_cguser'] : '';
		$ser_starttime		= isset($_GET['ser_startTime']) ? $_GET['ser_startTime'] : '';
		$ser_endtime		= isset($_GET['ser_endTime']) ? $_GET['ser_endTime'] : '';
		$ser_receiptstu    = isset($_GET['ser_receiptstu']) ? $_GET['ser_receiptstu'] : '';
		$page       		= isset($_GET['page']) ? $_GET['page'] : '1';
		$loginname          = $_SESSION['userCnName'];
		$condition 			= '';
		if (!empty($ser_ordersn)){
			$condition  .= " AND ordersn = '{$ser_ordersn}'";
		}
		if (!empty($ser_sku)){
			$condition  .= " AND sku LIKE '{$ser_sku}%'";
		}
		if (!empty($ser_status)){
			$condition  .= " AND status = '{$ser_status}'";
		}
		if (!empty($ser_orderstu)){
			$condition  .= " AND order_stu = '{$ser_orderstu}'";
		}
		if (!empty($ser_receiptstu)){
			if($ser_receiptstu == 1){
				$condition  .= " AND actualcount = purcount";
			}else if($ser_receiptstu == 2){
				$condition  .= " AND actualcount < purcount";
			}
		}
		if (!empty($ser_parnter)){
			$condition  .= " AND parnter LIKE '{$ser_parnter}%'";
		}
		if (!empty($ser_cguser)){
			$condition  .= " AND cguser = '{$ser_cguser}'";
		}
		if (!empty($ser_starttime) && $ser_endtime >= $ser_starttime){
			$serstart = strpos($ser_starttime, ':')!==false ? strtotime($ser_starttime) : strtotime($ser_starttime." 00:00:00");
			$serend   = strpos($ser_endtime, ':')!==false ? strtotime($ser_endtime) : strtotime($ser_endtime." 23:59:59");
			$condition  .= " AND purtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		$purwh 			= new PurToWhAct();
		$listInfo 		= $purwh->getReceiptGoods($condition, $page);
        $perNum 		= 200; 
		$totalNum 		= $listInfo["totalNum"];
		$list 			= $listInfo["goodsInfo"];
		$listDetail     = $listInfo['detailInfo'];
		$pageobj 		= new Page($totalNum, $perNum);
		$pageStr 		= $pageobj->fpage();
		$purchaseList 	= getPurchaseUserList();
		$this->smarty->assign('loginname', $loginname);
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
		$this->smarty->assign('pageStr', $pageStr);//分页输出
		$this->smarty->assign('userid', $_SESSION['userId']);//登录用户userid
		$this->smarty->assign('list', $list);//循环赋值*/
		$this->smarty->assign('listDetail', $listDetail);
		$this->smarty->display('receiptGoods.htm');
	}
	
	/**
	 * 采购员-供应商下单月度搜索
	 * Enter description here ...
	 */
	public function view_mothIndex(){
	 	@session_start();
		global $mod,$act;
        $this->smarty->assign('title','收货管理表');
        $this->smarty->assign('mod',$mod);//模块权限
		$ser_parnter		= isset($_GET['ser_parnter']) ? $_GET['ser_parnter'] : '';
		$ser_cguser         = isset($_GET['ser_cguser']) ? $_GET['ser_cguser'] : '';
		$ser_starttime		= isset($_GET['ser_startTime']) ? $_GET['ser_startTime'] : '';
		$ser_endtime		= isset($_GET['ser_endTime']) ? $_GET['ser_endTime'] : '';
		$page       		= isset($_GET['page']) ? $_GET['page'] : '1';
		$loginname          = $_SESSION['userCnName'];
		$condition 			= '';
		if (!empty($ser_parnter)){
			$condition  .= " AND parnter = '{$ser_parnter}'";
		}
		if (!empty($ser_cguser)){
			$condition  .= " AND cguser = '{$ser_cguser}'";
		}
		if (!empty($ser_starttime) && $ser_endtime >= $ser_starttime){
			$serstart = strpos($ser_starttime, ':')!==false ? strtotime($ser_starttime) : strtotime($ser_starttime." 00:00:00");
			$serend   = strpos($ser_endtime, ':')!==false ? strtotime($ser_endtime) : strtotime($ser_endtime." 23:59:59");
			$condition  .= " AND purtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		$purwh 			= new PurToWhAct();
		$listInfo 		= $purwh->getReceiptGoods($condition, $page);
        $perNum 		= 200; 
		$totalNum 		= $listInfo["totalNum"];
		$list 			= $listInfo["goodsInfo"];
		$listDetail     = $listInfo['detailInfo'];
		$pageobj 		= new Page($totalNum, $perNum);
		$pageStr 		= $pageobj->fpage();
		
		$this->smarty->assign('loginname', $loginname);
		$this->smarty->assign('pageStr', $pageStr);//分页输出
		$this->smarty->assign('userid', $_SESSION['userId']);//登录用户userid
		$this->smarty->assign('list', $list);//循环赋值*/
		$this->smarty->assign('listDetail', $listDetail);
		$this->smarty->display('receiptGoods.htm');
	}
	
	public function view_add(){
		global $mod,$act;
		$nowTime = date('Y-m-d');
        $this->smarty->assign('title','采购员收货管理手工录入');
		$this->smarty->assign('nowTime', $nowTime);
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('purAddReceiptGoods.htm');
	}
	
	public function view_edit(){
		global $mod,$act;
		$ser_id		= isset($_GET['ser_id']) ? $_GET['ser_id'] : '';
		$purwh 		= new PurToWhAct();
		$dataInfo 	= $purwh->getReceiptGoodsById($ser_id);
		$recTime    = date('Y-m-d', time() - 24 * 60 *60);
        $this->smarty->assign('title','收货手工录入');
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->assign('dataInfo', $dataInfo);
		$this->smarty->assign('recTime', $recTime);
		$this->smarty->display('editReceiptGoods.htm');
	}
	
	public function view_autoAdd(){
		global $mod,$act;
		$nowTime = date('Y-m-d');
        $this->smarty->assign('title','采购员收货管理手工录入');
		$this->smarty->assign('nowTime', $nowTime);
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('purAutoAddReceiptGoods.htm');
	}
	
	/**
	 * 非系统下单导入
	 * Enter description here ...
	 */
	public function view_importOrder(){
		global $mod,$act;
        $this->smarty->assign('title','线下订单导入');
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('importReceiptOrder.htm');
	}
	
	/**
     * 非系统下单导入方法
     */
    public function view_importOrderData(){
    	require_once(WEB_PATH.'lib/PHPExcel.php');
    	$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
    	$path       = WEB_PATH.'html/upload/';
		if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path.$uploadfile)) {
			$importResult = "<font color=green>上传成功</font><br>";
			$ismark       = 'yes';
		}else {
   			$importResult =  "<font color=red>上传失败</font><br>"; 	
   			$ismark       = 'no';
		}
		$fileName = $path.$uploadfile;	
		$filePath = $fileName;
		$PHPExcel = new PHPExcel(); 
		$PHPReader = new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)){      
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)){      
				echo 'no Excel';
				return ;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$sheet      	= $PHPExcel->getActiveSheet();
		/**取得一共有多少列*/
		$c 				= 2;
		$importShow 	= '';
		$purModel     	= new PurToWhModel();
		while(true){
			$aa				= 'A'.$c;
			$bb				= 'B'.$c;
			$cc 			= 'C'.$c;
			$dd 			= 'D'.$c;
			$ff             = 'F'.$c;
			$gg 			= 'G'.$c;
			$ii 			= 'I'.$c;
			$jj             = 'J'.$c;
			
			$purTime 			= trim($sheet->getCell($aa)->getValue());
			$purTime            = strtotime($purTime);
			$orderSn 			= trim($sheet->getCell($bb)->getValue());
			$parnter 			= trim($sheet->getCell($cc)->getValue());
			$sku 				= trim($sheet->getCell($dd)->getValue());
			$purCount 			= trim($sheet->getCell($ff)->getValue());
			$purPrice 			= trim($sheet->getCell($gg)->getValue());
			$cguser 			= trim($sheet->getCell($ii)->getValue());
			$purNote 			= trim($sheet->getCell($jj)->getValue());
			
			if(empty($orderSn)){
				break;
			}
			
			$rtnCode   = $purModel->importOrderData($orderSn, $sku, $parnter, $purCount, $purPrice, $cguser, $purNote, $purTime);
			if($rtnCode == '200'){
				$importShow    .= "<font color=green>订单号[".$orderSn."]料号[".$sku."]添加成功</font><br/>";
			}else if($rtnCode == '202'){
				$importShow    .= "<font color=red>订单号[".$orderSn."]料号[".$sku."]已存在</font><br/>";
			}else if($rtnCode == '201'){
				$importShow    .= "<font color=red>订单号[".$orderSn."]料号[".$sku."]添加失败</font><br/>";
			}
			$c++;
		}
		$this->smarty->assign('importShow', $importShow);
		$this->smarty->assign('importResult', $importResult);
		$this->smarty->display('importReceiptOrderResult.htm');
    }
	
	public function view_receiptEveryDayExport(){
		global $mod,$act;
		$nowTime = date('Y-m-d');
        $this->smarty->assign('title','收货管理报表每日导出');
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('receiptEveryDayExport.htm');
	}

	public function view_exportOrder(){
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
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','订货价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','订货金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','采购员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','订货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','首次到货日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','首次到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1','到货日期-2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1','到货日期-3');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1','到货日期-4');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1','到货日期-5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1','到货日期-6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1','到货日期-7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1','到货日期-8');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA1','实收数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB1','数量核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC1','到货状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD1','出货单价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE1','价格核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF1','价格确认');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG1','收货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH1','实际应付货款');
		$ser_ordersn		= isset($_GET['ser_ordersn']) ? $_GET['ser_ordersn'] : '';
		$ser_sku			= isset($_GET['ser_sku']) ? $_GET['ser_sku'] : '';
		$ser_parnter		= isset($_GET['ser_parnter']) ? $_GET['ser_parnter'] : '';
		$ser_status         = isset($_GET['ser_status']) ? $_GET['ser_status'] : '';
		$ser_cguser         = isset($_GET['ser_cguser']) ? $_GET['ser_cguser'] : '';
		$ser_starttime		= isset($_GET['ser_startTime']) ? $_GET['ser_startTime'] : '';
		$ser_endtime		= isset($_GET['ser_endTime']) ? $_GET['ser_endTime'] : '';
		$condition 			= '';
		if (!empty($ser_ordersn)){
			$condition  .= " AND ordersn = '{$ser_ordersn}'";
		}
		if (!empty($ser_sku)){
			$condition  .= " AND sku = '{$ser_sku}'";
		}
		if (!empty($ser_status)){
			$condition  .= " AND status = '{$ser_status}'";
		}
		if (!empty($ser_parnter)){
			$condition  .= " AND parnter LIKE '{$ser_parnter}%'";
		}
		if (!empty($ser_cguser)){
			$condition  .= " AND cguser = '{$ser_cguser}'";
		}
		if (!empty($ser_starttime) && $ser_endtime >= $ser_starttime){
			$serstart = strpos($ser_starttime, ':')!==false ? strtotime($ser_starttime) : strtotime($ser_starttime." 00:00:00");
			$serend   = strpos($ser_endtime, ':')!==false ? strtotime($ser_endtime) : strtotime($ser_endtime." 23:59:59");
			$condition  .= " AND purtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		$purwh		= new PurToWhAct();
		$dataArr 	= $purwh->exportData($condition);
		$row 		= 2;
		foreach($dataArr as $k => $v){
			$id             = $v['id'];
			$addtime 		= date('Y/m/d', $v['purtime']);
			$ordersn     	= $v['ordersn'];
			$parnter		= $v['parnter'];
			$sku 			= $v['sku'];
			$nameinfo       = $purwh->getSkuName($sku);
			$skuname        = $nameinfo['goodsName'];
			$purcount 		= $v['purcount'];
			$purprice 		= $v['purprice'];
			$purmoney 		= $v['purcount'] * $v['purprice'];
			$cguser         = $v['cguser'];
			$purnote 		= $v['purnote'];
			$actualcount    = $v['actualcount'];
			$diffcount      = $actualcount - $purcount;
			if($actualcount >= $purcount){
				$stu = 'OK';
			}else{
				$stu = '-';
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $addtime);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $ordersn);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parnter);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $skuname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $purcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $purprice);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $purmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $cguser);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $purnote);
			$detailArr 	= $purwh->getReceiptGoodsDetailById($id);
			if(!empty($detailArr)){
				$signnum    = 0;
				foreach($detailArr as $kk => $vv){
					$_intime 	= $vv['intime'];
					$_incount 	= $vv['incount'] ;
					$_innote    = $vv['innote'];
					$_intime    = date('Y/m/d', $_intime);
					switch($signnum){
						case '1':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$row, $_incount);
							break;
						case '2':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$row, $_incount);
							break;
						case '3':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$row, $_incount);
							break;
						case '4':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.$row, $_incount);
							break;
						case '5':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$row, $_incount);
							break;
						case '6':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.$row, $_incount);
							break;
						case '7':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.$row, $_incount);
							break;
						default:
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$row, $_incount);
							break;
					}
					$signnum++;
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.$row, $actualcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.$row, $diffcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.$row, $stu);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.$row, $_innote);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH'.$row, '');
			$row++;
		}
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(60);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Z')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AA')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AB')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AC')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AD')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AE')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AF')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AG')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AH')->setWidth(20);
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
		$title		= "收货管理表".date('Y-m-d');
		$titlename	= $title.".xls";
	
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
	
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	//财务数据报表导出
	public function view_finExportOrder(){
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
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','订货价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','订货金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','采购员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','订货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','首次到货日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','首次到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1','到货日期-2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1','到货日期-3');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1','到货日期-4');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1','到货日期-5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1','到货日期-6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W1','到货日期-7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y1','到货日期-8');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA1','到货日期-9');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB1','到货数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC1','实收数量');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD1','数量核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE1','到货状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF1','出货单价格');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG1','价格核对');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH1','价格确认');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI1','实收产品货款金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ1','未到产品货款金额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK1','收货备注');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM1','已付货款全额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO1','已付款货订金');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ1','已付货款余额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS1','已付部份货款-1');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU1','已付部份货款-2');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW1','已付部份货款-3');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY1','已付部份货款-4');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA1','已付部份货款-5');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC1','已付部份货款-6');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD1','付款日期');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE1','已付部份货款-7');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF1','支付方式');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG1','已付货款总额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH1','实际应付货款');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI1','实际已付货款差额');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ1','付款状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK1','运费');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL1','付款备注');
		$ser_ordersn		= isset($_GET['ser_ordersn']) ? $_GET['ser_ordersn'] : '';
		$ser_sku			= isset($_GET['ser_sku']) ? $_GET['ser_sku'] : '';
		$ser_parnter		= isset($_GET['ser_parnter']) ? $_GET['ser_parnter'] : '';
		$ser_status         = isset($_GET['ser_status']) ? $_GET['ser_status'] : '';
		$ser_cguser         = isset($_GET['ser_cguser']) ? $_GET['ser_cguser'] : '';
		$ser_starttime		= isset($_GET['ser_startTime']) ? $_GET['ser_startTime'] : '';
		$ser_endtime		= isset($_GET['ser_endTime']) ? $_GET['ser_endTime'] : '';
		$condition 			= '';
		if (!empty($ser_ordersn)){
			$condition  .= " AND ordersn = '{$ser_ordersn}'";
		}
		if (!empty($ser_sku)){
			$condition  .= " AND sku = '{$ser_sku}'";
		}
		if (!empty($ser_status)){
			$condition  .= " AND status = '{$ser_status}'";
		}
		if (!empty($ser_parnter)){
			$condition  .= " AND parnter LIKE '{$ser_parnter}%'";
		}
		if (!empty($ser_cguser)){
			$condition  .= " AND cguser = '{$ser_cguser}'";
		}
		if (!empty($ser_starttime) && $ser_endtime >= $ser_starttime){
			$serstart = strpos($ser_starttime, ':')!==false ? strtotime($ser_starttime) : strtotime($ser_starttime." 00:00:00");
			$serend   = strpos($ser_endtime, ':')!==false ? strtotime($ser_endtime) : strtotime($ser_endtime." 23:59:59");
			$condition  .= " AND purtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		$purwh		= new PurToWhAct();
		$dataArr 	= $purwh->exportData($condition);
		$row 		= 2;
		//$tmpordersn = '';
		//$tmpfee     = '';
		//$totalrow   = count($dataArr) + 1;
		foreach($dataArr as $k => $v){
			$id             = $v['id'];
			$addtime 		= date('Y/m/d', $v['purtime']);
			$ordersn     	= $v['ordersn'];
			$parnter		= $v['parnter'];
			$sku 			= $v['sku'];
			$nameinfo       = $purwh->getSkuName($sku);
			$skuname        = $nameinfo['goodsName'];
			$purcount 		= $v['purcount'];
			$purprice 		= $v['purprice'];
			$purmoney 		= $v['purcount'] * $v['purprice'];//订货金额
			$cguser         = $v['cguser'];
			$purnote 		= $v['purnote'];
			$actualcount    = $v['actualcount'];
			$paymoney       = $v['purmoney'];//已付款金额
			$recmoney       = $actualcount * $purprice;//实收货款金额
			$unrecmoney     = $purmoney - $recmoney;//未收货款金额
			$paytime        = !empty($v['paytime']) ? date('Y/m/d', $v['paytime']) : '';
			$paymethod      = $v['paymethod'];
			$fee            = !empty($v['fee']) ? $v['fee'] : '';
			$diffmoney      = $paymoney - $recmoney;//实际已付货款差额
			$diffcount      = $actualcount - $purcount;
			$payresult      = '';
			if($actualcount >= $purcount){
				$stu = 'OK';
			}else{
				$stu = '-';
			}
			if($diffmoney == 0){
				$payresult = '结清';
			}else{
				$payresult = '应付';
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $addtime);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $ordersn);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $parnter);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $sku);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $skuname);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $purcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $purprice);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $purmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $cguser);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $paymethod);
			$detailArr 	= $purwh->getReceiptGoodsDetailById($id);
			if(!empty($detailArr)){
				$signnum    = 0;
				foreach($detailArr as $kk => $vv){
					$_intime 	= $vv['intime'];
					$_incount 	= $vv['incount'] ;
					$_innote    = $vv['innote'];
					$_intime    = date('Y/m/d', $_intime);
					switch($signnum){
						case '1':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N'.$row, $_incount);
							break;
						case '2':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$row, $_incount);
							break;
						case '3':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$row, $_incount);
							break;
						case '4':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('S'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('T'.$row, $_incount);
							break;
						case '5':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('U'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('V'.$row, $_incount);
							break;
						case '6':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('W'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('X'.$row, $_incount);
							break;
						case '7':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Y'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Z'.$row, $_incount);
							break;
						case '8':
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AA'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AB'.$row, $_incount);
							break;
						default:
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, $_intime);
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$row, $_incount);
							break;
					}
					$signnum++;
				}
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AC'.$row, $actualcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AD'.$row, $diffcount);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AE'.$row, $stu);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AF'.$row, $purprice);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AG'.$row, '0');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AH'.$row, 'OK');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AI'.$row, $recmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AJ'.$row, $unrecmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AK'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AL'.$row, $paytime);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AM'.$row, $paymoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AN'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AO'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AP'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AQ'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AR'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AS'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AT'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AU'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AV'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AW'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AX'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AY'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('AZ'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BA'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BB'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BC'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BD'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BE'.$row, '');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BF'.$row, $paymethod);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BG'.$row, $paymoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BH'.$row, $recmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BI'.$row, $diffmoney);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BJ'.$row, $payresult);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$row, $fee);
			/*
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$row, '');
			if($tmpordersn != $ordersn){
				$tmpordersn = $ordersn;
				if($row != 2){
					$tmprow = $row - 1;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$tmprow, $tmpfee);
				}
				$tmpfee     = $fee;
			}
			if($row == $totalrow){
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BK'.$totalrow, $fee);
			}
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('BL'.$row, '');
			*/
			$row++;
		}
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(60);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('S')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('T')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('U')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('V')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('W')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('X')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Y')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Z')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AA')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AB')->setWidth(12);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AC')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AD')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AE')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AF')->setWidth(12);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AG')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AH')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AI')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AJ')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AK')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AL')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AM')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AN')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AO')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AP')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AQ')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AR')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AS')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AT')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AU')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AV')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AW')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AX')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AY')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('AZ')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BA')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BB')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BC')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BD')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BE')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BF')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BG')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BH')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BI')->setWidth(18);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BJ')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BK')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('BL')->setWidth(15);
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
		$title		= "产品检测表".date('Y-m-d');
		$titlename	= $title.".xls";
	
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
	
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
	
	/**
	 * 料号海外负责人导入页面
	 */
	public function view_importOverSeaSkuPage(){
		global $mod,$act;
        $this->smarty->assign('title','料号海外负责人导入');
        $this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->display('importOverSeaSku.htm');
	}
	
	/**
	 * 料号海外负责人导入
	 */
	public function view_importOverSeaSku(){
    	require_once(WEB_PATH.'lib/PHPExcel.php');
    	$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
    	$path       = WEB_PATH.'html/upload/';
		if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path.$uploadfile)) {
			$importResult = "<font color=green>上传成功</font><br>";
			$ismark       = 'yes';
		}else {
   			$importResult =  "<font color=red>上传失败</font><br>"; 	
   			$ismark       = 'no';
		}
		$fileName = $path.$uploadfile;	
		$filePath = $fileName;
		$PHPExcel = new PHPExcel(); 
		$PHPReader = new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)){      
			$PHPReader = new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)){      
				echo 'no Excel';
				return ;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$sheet      	= $PHPExcel->getActiveSheet();
		/**取得一共有多少列*/
		$c 				= 2;
		$importShow 	= '';
		$purModel     	= new PurToWhModel();
		while(true){
			$aa				= 'A'.$c;
			$bb				= 'B'.$c;
			$sku 			= trim($sheet->getCell($aa)->getValue());
			$cguser 		= trim($sheet->getCell($bb)->getValue());
			if(empty($sku)){
				break;
			}
			$rtnCode   = $purModel->addOldSkuToOverSku($sku, $cguser);
			if($rtnCode == 200){
				$importShow    .= "<font color=green>料号[".$sku."]关联成功</font><br/>";
			}else if($rtnCode == 201){
				$importShow    .= "<font color=red>料号[".$sku."]已存在海外仓预警</font><br/>";
			}else if($rtnCode == 404){
				$importShow    .= "<font color=red>料号[".$sku."]不存在</font><br/>";
			}else if($rtnCode == 202){
				$importShow    .= "<font color=red>料号[".$sku."]关联失败</font><br/>";
			}
			$c++;
		}
		$this->smarty->assign('importShow', $importShow);
		$this->smarty->assign('importResult', $importResult);
		$this->smarty->display('importOverSeaSkuResult.htm');
    }
    
    //导出B仓库存立方数
    public function view_exportOverSkuVolume(){
    	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','料号');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','立方数(m)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','B仓库存');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','封箱库存');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','采购员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','长');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','宽');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','高');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','单位');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','描述');
    	$keyword     	= isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $purwh 			= new PurToWhModel();
        $condition 		= '';
    	if(!empty($keyword)){
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
		$listInfo 		= $purwh->exportOverSeaSkuVolume($condition);
		$totalVolume    = 0;
		$row            = 2;
		if(!empty($listInfo)){
			foreach($listInfo as $k => $v){
				$sku    			= $v['sku'];
				$length 			= $v['goodsLength'];
				$width  			= $v['goodsWidth'];
				$height 			= $v['goodsHeight'];
				$stock  			= $v['b_stock_cout'];
				$inboxqty 			= $v['inBoxQty'];
				$name               = $v['goodsName'];
				$cguserId           = $v['OverSeaSkuCharger'];
				$cguser             = getUserNameById($cguserId);
				$totalVolume        = $length * $width * $height * ($stock + $inboxqty) / 1000000;
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $sku);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $totalVolume);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $stock);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $inboxqty);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, $cguser);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $length);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $width);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $height);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, 'cm');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $name);
				$row++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(50);	
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:J'.$row)->getAlignment()->setWrapText(true);
		$title		= "B仓海外料号库存立方表".date('Y-m-d');
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
?>
