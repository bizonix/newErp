<?php

/*
 * 仓库内部销售出入库管理 internalIoSell.view.php
 * ADD BY chenwei 2013.8.23
 */

class InternalIoSellView extends BaseView {

    private $where = '';

    /*
     * 内部购买
     */
    public function view_internalBuyList() {
        $navlist = array(//面包屑
            array('url' => 'index.php?mod=internalIoSell&act=internalUseIostoreList', 'title' => '单据业务'),
            array('url' => 'index.php?mod=internalIoSell&act=internalUseIostoreList', 'title' => '内部使用'),
			array('url' => 'index.php?mod=internalIoSell&act=internalBuyList', 'title' => '内部使用申请单'),
        );
		$applyTime = date("Y-m-d H:i:s",time());
        $this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('curusername',$_SESSION['userName']);
		$this->smarty->assign('applyTime',$applyTime);
        /* start ---- 内部使用组的 单据类型 选择 ------*/
		$this->where = " where groupId = 1";
        $InternalIoSellManagement = new InternalIoSellManagementAct();
        $invoiceTypeArr = $InternalIoSellManagement->act_invoiceTypeList($this->where);
        $this->smarty->assign('invoiceTypeArr', $invoiceTypeArr);
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', 31);
		/* end ------*/
		/* start ---- 自动生成出库单据编码 ------*/
		$ordersn = getIostoreOrdersn();
		$this->smarty->assign('ordersn', $ordersn);
        $this->smarty->display('internalBuyList.htm');
		/* end ------*/
    }
	
	/*
     * 内部使用列表默认显示、搜索、导出
     */
    public function view_internalUseIostoreList() {	
		//基础代码准备
		$InternalIoSellManagement = new InternalIoSellManagementAct();	
		
		//搜索操作
		$condition = array();
		$ordersn = "";
		$choose_status = 0;
		$ioStatus	   = 0;
		$startTime = date("Y-m-d 00:00:00",time());
		$endTime = date("Y-m-d 23:59:59",time());
		if(isset($_POST) && !empty($_POST)){
			$ordersn      			= trim($_POST['ordersnInput']);//单据号
			$ioTypeinvoiceChoose	= $_POST['ioTypeinvoiceChoose'];//出入库单类型
			$ioStatus				= $_POST['ioStatus'];//审核状态
			
			if(!empty($_POST['startTime']) && !empty($_POST['endTime'])){
				$startTime	= strtotime(trim($_POST['startTime']));//开始时间
				$endTime	= strtotime(trim($_POST['endTime']));//结束时间
				$condition[] = "createdTime BETWEEN {$startTime} AND {$endTime}";
				$startTime	= trim($_POST['startTime']);//开始时间
				$endTime	= trim($_POST['endTime']);//结束时间
			}else{
				$condition[] = "createdTime BETWEEN ".strtotime($startTime)." and ".strtotime($endTime);
			}
			
			if(!empty($ordersn)){
				$condition[] = "ordersn = '{$ordersn}'";
			}
			
			if(!empty($ioTypeinvoiceChoose)){
				$condition[] = "invoiceTypeId = '{$ioTypeinvoiceChoose}'";
				$choose_status = $ioTypeinvoiceChoose;
			}else{
				$condition[] = "invoiceTypeId in (1,2,3,4,5)";
			}
			
			if(!empty($ioStatus)){
				$condition[] = "ioStatus = '{$ioStatus}'";
			}	
		
			$this->where = "WHERE ".implode(" and ",$condition)." and is_delete = 0 ";
			
		}else{
			//默认显示列表条件 invoiceTypeId in (1,2,3,4,5) 内部使用5种类型
			$this->where = "WHERE invoiceTypeId in (1,2,3,4,5) and createdTime BETWEEN ".strtotime($startTime)." and ".strtotime($endTime)." and is_delete = 0 ";
		}
	
		
		/*/分页
		$total = $InternalIoSellManagement->act_getPageNum($this->where);
		$num      = 5;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$this->where    .= " order by id desc ".$page->limit;
		*/
		//单据表
        $iostoreArr = $InternalIoSellManagement->act_iostoreList($this->where);
		/*
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
		$this->smarty->assign('show_page',$show_page);
		*/
		
		//单据类型
		$this->where = " where groupId = 1";//内部使用分组单据类型
        $invoiceTypeArr = $InternalIoSellManagement->act_invoiceTypeList($this->where);		
		$invoiceNameArr = array();//出入库单据类型
		foreach($invoiceTypeArr as $invoiceName){
			$invoiceNameArr[$invoiceName['id']] = $invoiceName['invoiceName'];
		}
		//付款方式
		$payMethods = array();
		$paymentMethodsArr = $InternalIoSellManagement->act_changeCategoriesSkip();
		foreach($paymentMethodsArr as $payValue){
			$payMethods[$payValue['id']] = $payValue['method'];
		}
		
		//单据明细		
		$iostoreDetails = array();
		$iostoreDetailNum = array();//合计件数
		$iostoreDetailDue = array();//应付款总金额
		if(!empty($iostoreArr)){
			foreach($iostoreArr as $ioId){
				$this->where = " where iostoreId = ".$ioId['id'];
				$iostoredetailArr = $InternalIoSellManagement->act_iostoredetailList($this->where);
				if(empty($iostoredetailArr)){
					continue;
				}
				$sumNum = 0;
				$due = 0;
				foreach($iostoredetailArr as $keyId=>$ioNumInfo){
					$sumNum += $ioNumInfo['amount'];
					$due += ($ioNumInfo['cost'] * $ioNumInfo['amount']);
					
					//仓位ID转换
					$whereStr = " WHERE is_enable = 1 and type = 1 and id = {$ioNumInfo['positionId']}";
					$iostoredetailArr[$keyId]['positionId'] = $InternalIoSellManagement->act_positionIdToName($whereStr);
					
					//采购ID转换
					$usermodel     = UserModel::getInstance();
					$whereStr	   = "where a.global_user_id=".$ioNumInfo['purchaseId'];         
					$cgUser	       = $usermodel->getGlobalUserLists('global_user_name',$whereStr,'','');//$cgUser[0]['global_user_name'];	
					$iostoredetailArr[$keyId]['purchaseId'] = $cgUser[0]['global_user_name'];
				}
				$iostoreDetailNum[$ioId['id']] = $sumNum;
				$iostoreDetailDue[$ioId['id']] = $due;
				$iostoreDetails[$ioId['id']] = $iostoredetailArr;
			}
		}

		if(!empty($iostoreArr)){
			$usermodel = UserModel::getInstance();
			//申请人
			$count = count($iostoreArr);
			for($i=0;$i<$count;$i++){
				$user_info 		  			   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$iostoreArr[$i]['userId']}'",'','limit 1');
				$iostoreArr[$i]['userName'] = $user_info[0]['global_user_name'];
				$user_info 		  			   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$iostoreArr[$i]['operatorId']}'",'','limit 1');
				$iostoreArr[$i]['operatorName'] = $user_info[0]['global_user_name'];
			}
		}
		
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', 31);
		$this->smarty->assign('ordersn',$ordersn);
		$this->smarty->assign('choose_status',$choose_status);
		$this->smarty->assign('ioStatus',$ioStatus);
		$this->smarty->assign('startTime',$startTime);
		$this->smarty->assign('endTime',$endTime);
		$this->smarty->assign('iostoreDetailNum', $iostoreDetailNum);
		$this->smarty->assign('iostoreDetailDue', $iostoreDetailDue);
		$this->smarty->assign('iostoreDetails', $iostoreDetails);
		$this->smarty->assign('payMethods', $payMethods);
		$this->smarty->assign('invoiceNameArr', $invoiceNameArr);
		if(empty($iostoreArr)){
			$this->smarty->assign('iostoreArr', null);
		}else{
			$this->smarty->assign('iostoreArr', $iostoreArr);	
		}					
		$navlist = array(//面包屑
            array('url' => 'index.php?mod=internalIoSell&act=internalUseIostoreList', 'title' => '单据业务'),
            array('url' => 'index.php?mod=internalIoSell&act=internalUseIostoreList', 'title' => '内部使用'),
        );	
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('curusername',$_SESSION['userName']);	
        $this->smarty->display('internalUseIostoreList.htm');
    }
	
	/*
     * 内部使用申请单提交单据
     */
	public function view_internalBuySubmit() {		
		//单据明细组装
		$newArrays = array();
		if(is_array($_POST)){
			foreach($_POST['sku'] as $pkey => $pvalue){
				$shippingArr            = array();
				$newArray['sku']        = $pvalue;
				$newArray['amount']     = $_POST['num'][$pkey];
				$newArray['cost']       = $_POST['price'][$pkey];			
				//$shippingArr	        = explode("-",$_POST['shipping'][$pkey]);
				$newArray['positionId'] = $_POST['shippingId'][$pkey];//仓位ID
				$newArray['purchaseId'] = $_POST['purchaseId'][$pkey];
				$newArrays[] = $newArray;
			}	
		}
	
		//单据表头数据组装
		$storeInfoArr = array();	
		$invoiceTypeId = $_POST['ioTypeinvoiceChoose'];//出入库单据类型列表ID
		
		$this->where = " where id = {$invoiceTypeId}";
        $InternalIoSellManagement = new InternalIoSellManagementAct();
        $invoiceTypeArr = $InternalIoSellManagement->act_invoiceTypeList($this->where);
		$ioType =  $invoiceTypeArr[0]['ioType'];
		if($ioType == 0){
			$ioType = 1;//出库类型
		}else if($ioType == 1){
			$ioType = 2;//入库类型
		}
		
		$userId = $_SESSION['userId'];//申请人ID (未转换)$_POST['userId']
		$ordersn = $_POST['ordersn'];//单据号（单据编码）
		$note	= trim($_POST['noteInput']);//备注
		$paymentMethodsId = isset($_POST['paymentMethods']) ? $_POST['paymentMethods'] : 3;//付款方式
		$companyId = 1;//公司ID
		$storeId   = 1;//出库ID
		 
		$storeInfoArr['invoiceTypeId'] = $invoiceTypeId;
		$storeInfoArr['ioType'] = $ioType;
		$storeInfoArr['userId'] = $userId;
		$storeInfoArr['ordersn'] = $ordersn;
		$storeInfoArr['note'] = $note;
		$storeInfoArr['paymentMethodsId'] = $paymentMethodsId;
		$storeInfoArr['companyId'] = $companyId;
		$storeInfoArr['storeId'] = $storeId;
		
		//插入表头
		$whIoStoreAct = new WhIoStoreAct();
        $internalBuyList = $whIoStoreAct->act_addWhIoStoreForWh($storeInfoArr); //返回0 插入错误  返回刚刚插入的ID 成功  插入明细表
		if($internalBuyList != 0){
			//插入明细
			foreach($newArrays as $insertVal){
				$insertVal['iostoreId'] = $internalBuyList;//出入库单据编号(id)
				//一条一条插入
				$storeDetailList = $whIoStoreAct->act_addWhIoStoreDetailForWh($insertVal);	
				//if($storeDetailList){
					//echo $insertVal['sku']."提交成功!"."<br>";
				//}
			}								
			header("location:index.php?mod=internalIoSell&act=internalUseIostoreList");
		}else{
			header("location:index.php?mod=internalIoSell&act=internalBuyList");
		}
	}
	
	/*
     * 内部使用表格导出
     */
	 public function view_internalIoSellExportExcel(){
		header("Content-type:text/html;charset=utf-8");
		//基础代码准备
		$InternalIoSellManagement = new InternalIoSellManagementAct();	
		$condition = array();
		$ordersn      			= trim($_POST['ordersnInput']);//单据号
		$ioTypeinvoiceChoose	= $_POST['ioTypeinvoiceChoose'];//出入库单类型
		$ioStatus				= $_POST['ioStatus'];//审核状态
		
		if(!empty($_POST['startTime']) && !empty($_POST['endTime'])){
			$startTime	= strtotime(trim($_POST['startTime']));//开始时间
			$endTime	= strtotime(trim($_POST['endTime']));//结束时间
			$condition[] = "createdTime BETWEEN {$startTime} AND {$endTime}";
			$startTime	= trim($_POST['startTime']);//开始时间
			$endTime	= trim($_POST['endTime']);//结束时间
		}else{
			$condition[] = "createdTime BETWEEN ".strtotime($startTime)." and ".strtotime($endTime);
		}
		
		if(!empty($ordersn)){
			$condition[] = "ordersn = '{$ordersn}'";
		}
		
		if(!empty($ioTypeinvoiceChoose)){
			$condition[] = "invoiceTypeId = '{$ioTypeinvoiceChoose}'";
		}else{
			$condition[] = "invoiceTypeId in (1,2,3,4,5)";
		}
		
		if(!empty($ioStatus)){
			$condition[] = "ioStatus = '{$ioStatus}'";
		}	
	
		$this->where = "WHERE ".implode(" and ",$condition)." and is_delete = 0 ";

		$iostoreArr = $InternalIoSellManagement->act_iostoreList($this->where);
		
		//单据类型
		$this->where = " where groupId = 1";//内部使用分组单据类型
        $invoiceTypeArr = $InternalIoSellManagement->act_invoiceTypeList($this->where);		
		$invoiceNameArr = array();//出入库单据类型
		foreach($invoiceTypeArr as $invoiceName){
			$invoiceNameArr[$invoiceName['id']] = $invoiceName['invoiceName'];
		}
		//付款方式
		$payMethods = array();
		$paymentMethodsArr = $InternalIoSellManagement->act_changeCategoriesSkip();
		foreach($paymentMethodsArr as $payValue){
			$payMethods[$payValue['id']] = $payValue['method'];
		}
		
		//单据明细		
		$iostoreDetails = array();
		$iostoreDetailNum = array();//合计件数
		$iostoreDetailDue = array();//应付款总金额
		if(!empty($iostoreArr)){
			foreach($iostoreArr as $ioId){
				$this->where = " where iostoreId = ".$ioId['id'];
				$iostoredetailArr = $InternalIoSellManagement->act_iostoredetailList($this->where);
				if(empty($iostoredetailArr)){
					continue;
				}
				$sumNum = 0;
				$due = 0;
				foreach($iostoredetailArr as $ioNumInfo){
					$sumNum += $ioNumInfo['amount'];
					$due += $ioNumInfo['cost'];
				}
				$iostoreDetailNum[$ioId['id']] = $sumNum;
				$iostoreDetailDue[$ioId['id']] = $due;
				$iostoreDetails[$ioId['id']] = $iostoredetailArr;
			}
		}
		
		//准备导出
		require_once '../lib/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '单据号');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '申请类型');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '出入类型');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '申请人');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '付款方式');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '申请时间');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '备注');    
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '审核状态');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '审核人');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '审核时间');    
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '合计件数');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '总金额');
		$a = 2;
		if(!empty($iostoreArr)){
			//单据信息
			foreach($iostoreArr as $key => $iostoreExportArr) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit(@$iostoreExportArr['ordersn'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit($invoiceNameArr[$iostoreExportArr['invoiceTypeId']], PHPExcel_Cell_DataType::TYPE_STRING);
				if($iostoreExportArr['ioType'] == 1){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit('出库', PHPExcel_Cell_DataType::TYPE_STRING);	
				}else if($iostoreExportArr['ioType'] == 2){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit('入库', PHPExcel_Cell_DataType::TYPE_STRING);	
				}else{
					$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);	
				}
				
				$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit($iostoreExportArr['userId'], PHPExcel_Cell_DataType::TYPE_STRING);			
				$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($payMethods[$iostoreExportArr['paymentMethodsId']], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$a)->setValueExplicit(date("Y-m-d H:i:s",$iostoreExportArr['createdTime']), PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$a)->setValueExplicit(@trim($iostoreExportArr['note']), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($iostoreExportArr['ioStatus'] == 1){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit("未审核", PHPExcel_Cell_DataType::TYPE_STRING); 
				}else if($iostoreExportArr['ioStatus'] == 2){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit("审核通过", PHPExcel_Cell_DataType::TYPE_STRING); 
				}else if($iostoreExportArr['ioStatus'] == 3){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit("审核不通过", PHPExcel_Cell_DataType::TYPE_STRING); 
				}else{
					$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING); 
				}
				     
				$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$a)->setValueExplicit($iostoreExportArr['operatorId'], PHPExcel_Cell_DataType::TYPE_STRING);
				if(empty($iostoreExportArr['endTime'])){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$a)->setValueExplicit("无", PHPExcel_Cell_DataType::TYPE_STRING);
				}else{
					$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$a)->setValueExplicit(date("Y-m-d H:i:s",$iostoreExportArr['endTime']), PHPExcel_Cell_DataType::TYPE_STRING);
				}
				$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$a)->setValueExplicit($iostoreDetailNum[$iostoreExportArr['id']], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('L'.$a)->setValueExplicit($iostoreDetailDue[$iostoreExportArr['id']], PHPExcel_Cell_DataType::TYPE_STRING);
				$a++;
				//单据SKU明细
				$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit("SKU", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit("数量", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit("单价(RMB)", PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit("采购", PHPExcel_Cell_DataType::TYPE_STRING);
				$a++;
				
				foreach($iostoreDetails[$iostoreExportArr['id']] as $detailList){
					$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit("", PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit($detailList['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit($detailList['amount'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit($detailList['cost'], PHPExcel_Cell_DataType::TYPE_STRING);
					$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($detailList['purchaseId'], PHPExcel_Cell_DataType::TYPE_STRING);
					$a++;		
							
				}		
			}			
		}
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:L500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(50);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(10); 
		
				
		$title		= "internalIoSellInfo".date('Y-m-d');
		$titlename	= "internalIoSellInfo".date('Y-m-d').".xls";
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); 
		exit;
	 }
	 
	 /*
     * 审核通过入口
     */
    public function view_internalIoSellApproved(){
        $InternalIoSellManagement = new InternalIoSellManagementAct();
		//$type = $_GET['type'];
		$this->where = " WHERE id =".$_GET['approvedId'];		
        $approvedList = $InternalIoSellManagement->act_internalIoSellApproved($this->where);
		if($approvedList){
			header("location:index.php?mod=internalIoSell&act=internalUseIostoreList");
		}else{
			echo "系统错误！！！";exit;	
		}
    }
	
	 /*
     * 拒绝入口
     */
    public function view_internalIoSellAbandon(){
        $InternalIoSellManagement = new InternalIoSellManagementAct();
		$this->where = " WHERE id =".$_GET['approvedId'];		
        $abandonList = $InternalIoSellManagement->act_internalIoSellAbandon($this->where);
		if($abandonList){
			header("location:index.php?mod=internalIoSell&act=internalUseIostoreList");
		}else{
			echo "系统错误！！！";exit;	
		}
    }
	 
}