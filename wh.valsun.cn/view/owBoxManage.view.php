<?php
/*
 * 箱号管理
 */
class OwBoxManageView extends BaseView {
    
    /*
     * 箱号申请页面
     */
    public function view_applyBox(){
        $status  = isset($_GET['status']) ? intval($_GET['status']) : '';   
    	$navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'箱号申请'),
        );
        $boxObj    		= new BoxManageModel();
        $pageSize   	= 98;
        $count      	= $boxObj->calcCount($status);
        $perCount       = ceil($count / 11);
        $pageObj      	= new Page($count, $pageSize);
        $boxApplyRecord = $boxObj->getApplyBoxRecord($status, $pageObj->limit);
        $sign			= 0;
        $ismark         = 0;
        $useArr         = array();
        foreach($boxApplyRecord as $k => $v){
        	$isuse 					= $v['isuse'];
        	$useArr[$v['boxnum']] 	= $isuse;
        	if($sign % 14 != 0){
        		$arr[$i][$sign] = $v['boxnum'];
        	}else{
        		$i++;
        		$arr[$i][$sign] = $v['boxnum'];
        	}
        	$sign++;
        }
        $toplevel 		= 2;                                                                      //顶层菜单
        $secondlevel 	= '214';    
     	if ($count > $pageSize) {
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('toplevel',$toplevel);  
        $this->smarty->assign('count', $count);          
        $this->smarty->assign('pagestr', $pagestr);                                                //当前的二级菜单
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('third', 2);
        $this->smarty->assign('boxApplyRecord', $arr);
        $this->smarty->assign('useArr', $useArr);
        $this->smarty->assign('status', $status);
        $this->smarty->display('owBoxNumApply.htm');
    }
    
    /*
     * 打印箱号
     */
    public function view_printBox(){
        $number = isset($_GET['number']) ? intval($_GET['number']) : 0;
        if ($number == 0) {
        	echo "没有数量!";
        	exit;
        }
        $box_Obj    = new BoxManageModel();
        $result     = $box_Obj->applyBoxNum($number);
        include WEB_PATH.'html/template/v1/printBoxNumber.htm';
    }
    
    /**
     * 打印箱号
     * add name:wangminwei
     * add time:2014-05-17
     */
    public function view_rePrintBox(){
    	$boxArr = isset($_GET['boxArr']) ? $_GET['boxArr'] : '';
    	if(!empty($boxArr)){
    		$arrList 	= explode(',', $boxArr);
	        /*
    		$sign		= 0;
    		foreach($arrList as $k => $v){
	        	if($sign % 2 != 0){
	        		$arr[$i][1] = $v;
	        	}else{
	        		$i++;
	        		$arr[$i][0] = $v;
	        	}
	        	$sign++;
	        }*/
    	}
    	$this->smarty->assign('arr', $arrList);
    	$this->smarty->display('rePrintBox.htm');
    }
    
    /*
     * 箱号管理
     */
    public function view_boxManage(){
        $ordersn    = isset($_GET['ordersn']) ? trim($_GET['ordersn']) : '';                 //状态 
        $status     = isset($_GET['status']) ? intval($_GET['status']) : FALSE;                 //状态 
        $boxId      = isset($_GET['boxid'])  ? trim($_GET['boxid']): FALSE;                     //箱号
        $sku        = isset($_GET['sku'])  ? trim($_GET['sku']): FALSE;                     //箱号
        $startTime  = isset($_GET['startTime']) ? trim($_GET['startTime']) : FALSE;             //开始时间
        $endTime    = isset($_GET['endTime'])   ? trim($_GET['trim']) : FALSE;                  //结束时间
        
        $whereSql   = '';
     	if (!empty($ordersn)) {
        	$whereSql .= " and a.replenshId='$ordersn' ";
        }
        if (!empty($status)) {
        	$whereSql .= " and a.status='$status' ";
        }
        if (!empty($boxId)) {
            $bxid   = intval($boxId);
        	$whereSql  .= " and a.boxid='$bxid' ";
        }
    	if (!empty($sku)) {
        	$whereSql .= " and b.sku = '$sku' ";
        }
        if (!empty($startTime)) {
            $startTimeStamp = strtotime($startTime);
        	$whereSql  .= " and a.addtime>$startTimeStamp ";
        }
        
        if (!empty($endTime)) {
            $endTimeStamp = strtotime($endTime);
            $whereSql  .= " and a.addtime<$endTimeStamp ";
        }
        $pageSize   = 200;
        $box_obj    = new BoxManageModel();
        $count      = $box_obj->culCount($whereSql);
        $pageObj      = new Page($count, $pageSize);
        $orderby      = " ORDER BY a.boxid DESC ";
        $boxinfoList  = $box_obj->getListBoxInfo($whereSql.$orderby.$pageObj->limit);
        foreach ($boxinfoList as &$boxInfo){
            $boxInfo['timestr']     = date('Y-m-d H:i:s', $boxInfo['addtime']);
            $boxInfo['replenshId']  = empty($boxInfo['replenshId']) ? '未关联补货单' : $boxInfo['replenshId']; 
            $boxInfo['sendtime']    = empty($boxInfo['sendScanTime']) ? '未发柜' : date('Y-m-d H:i:s', $boxInfo['sendScanTime']);
            $boxInfo['statusStr']   = BoxManageModel::status2Name($boxInfo['status']);
            $detailInfo = $box_obj->getBoxSkuDetail($boxInfo['boxid']);
            $boxInfo['skudetail']  = $detailInfo;
            $boxInfo['addUserName'] = getUserNameById($boxInfo['adduser']);
        }
        $navlist = array(                                                                   //面包屑
                array('url'=>'','title'=>'海外仓补货'),
                array('url'=>'','title'=>'箱号管理'),
        );
        
        if ($count > $pageSize) {
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        
        $toplevel = 2;                                                                      //顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
        
        $secondlevel = '214';                                                                //当前的二级菜单
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('boxList', $boxinfoList);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('third', 3);
        $this->smarty->assign('status', $status);
        $this->smarty->assign('orderSn', $ordersn);
        $this->smarty->assign('boxid', $boxId);
        $this->smarty->assign('sku', $sku);
        $this->smarty->assign('starttime', $startTime);
        $this->smarty->assign('endtime', $endTime);
        $this->smarty->display('owBoxManage.htm');
    }
    
    /**
     * 批量导入箱子信息页面
     */
    public function view_importBoxInfo(){
    	$navlist = array(                                                                   //面包屑
            array('url'=>'','title'=>'海外仓补货'),
            array('url'=>'','title'=>'箱号管理'),
            array('url'=>'','title'=>'箱号导入'),
        );
    	$toplevel = 2; 
    	$this->smarty->assign('navlist', $navlist);                                                                     //顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
    	$this->smarty->display('importBoxInfo.htm');
    }
    
    /**
     * 批量导入箱子信息方法
     */
    public function view_importBoxData(){
    	require_once(WEB_PATH.'lib/PHPExcel.php');
    	$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
    	$path       = WEB_PATH.'html/template/v1/updload/boxinfo/';
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
		$c 			= 2;
		$importShow = '';
		$boxObj     = new BoxManageModel();
		while(true){
			$aa				= 'A'.$c;
			$bb				= 'B'.$c;
			$cc 			= 'C'.$c;
			$dd 			= 'D'.$c;
			$ee 			= 'E'.$c;
			$ff             = 'F'.$c;
			$boxId 			= trim($sheet->getCell($aa)->getValue());
			$length 		= trim($sheet->getCell($bb)->getValue());
			$width 			= trim($sheet->getCell($cc)->getValue());
			$high 			= trim($sheet->getCell($dd)->getValue());
			$weight 		= trim($sheet->getCell($ee)->getValue());
			$netWeight 		= trim($sheet->getCell($ff)->getValue());
			if(empty($boxId)){
				break;
			}
			
			$rtnCode        = $boxObj->batchUpdBoxInfo($boxId, $length, $width, $high, $weight, $netWeight);
			if($rtnCode == 'success'){
				$importShow    .= "<font color=green>箱号[".$boxId."]更新成功</font><br/>";
			}else if($rtnCode == 'failure'){
				$importShow    .= "<font color=red>箱号[".$boxId."]更新失败</font><br/>";
			}else if($rtnCode == 'statusError'){
				$importShow    .= "<font color=red>箱号[".$boxId."]未复核，不能更新</font><br/>";
			}else if($rtnCode == 'null'){
				$importShow    .= "<font color=red>箱号[".$boxId."]不存在</font><br/>";
			}else if($rtnCode == 'moreWeight'){
				$importShow    .= "<font color=red>箱号[".$boxId."]净重大于毛重，请复查</font><br/>";
			}
			$c++;
		}
		$navlist = array(                                                                   //面包屑
            array('url'=>'','title'=>'海外仓补货'),
            array('url'=>'','title'=>'箱号管理'),
            array('url'=>'','title'=>'箱号信息导入结果'),
        );
    	$toplevel = 2; 
    	$this->smarty->assign('navlist', $navlist);                                                                     //顶层菜单
        $this->smarty->assign('toplevel', $toplevel);
		$this->smarty->assign('importShow', $importShow);
		$this->smarty->assign('importResult', $importResult);
		$this->smarty->display('importBoxInfoResult.htm');
    }
    
    /**
     * 导出料号与箱号关联报表
     */
    public function view_exportBoxInfo(){
    	require_once(WEB_PATH.'lib/PHPExcel.php');
    	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','备货单号Shipment ID');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1','箱号CTN NO.');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1','料号SKU');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1','每箱个数');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1','优先上架Priority for Putaway');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1','纸箱长度L(cm)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1','纸箱宽度W(cm)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1','纸箱高度H(cm)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1','体积CBM');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1','每箱净重N.W(kg)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1','每箱毛重G.W(kg)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1','中文描述Goods Desc(CN)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1','英文描述 Goods Desc');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1','材质Material');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1','单价U/P(RMB)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1','总价TTL(RMB)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1','单价U/P(USD)');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1','总价TTL(USD)');
    	$serOrderSn         = isset($_GET['orderSn']) ? $_GET['orderSn'] : '';
		$serStatus          = isset($_GET['status']) ? $_GET['status'] : '';
		$serSku				= isset($_GET['sku']) ? $_GET['sku'] : '';
		$serStartTime		= isset($_GET['startTime']) ? $_GET['startTime'] : '';
		$serEndTime			= isset($_GET['endTime']) ? $_GET['endTime'] : '';
		$condition 			= '';
		if (!empty($serOrderSn)){
			$condition  .= " AND a.replenshId = '{$serOrderSn}'";
		}
		if (!empty($serSku)){
			$condition  .= " AND b.sku = '{$serSku}'";
		}
		if (!empty($serStatus)){
			$condition  .= " AND a.status = '{$serStatus}'";
		}
		if (!empty($serStartTime) && $serEndTime >= $serStartTime){
			$serstart = strpos($serStartTime, ':')!==false ? strtotime($serStartTime) : strtotime($serStartTime." 00:00:00");
			$serend   = strpos($serEndTime, ':')!==false ? strtotime($serEndTime) : strtotime($serEndTime." 23:59:59");
			$condition  .= " AND a.addtime BETWEEN '{$serstart}' AND '{$serend}'";
		}
		$boxModel 	= new BoxManageModel();
		$rtnData 	= $boxModel->getListBoxInfo($condition);
		if(!empty($rtnData)){
			$row 	= 2;
			foreach($rtnData as $k => $v){
				$orderSn 		= $v['replenshId'];
				$boxId   		= $v['boxid'];
				$length   		= $v['length'];
				$width      	= $v['width'];
				$high       	= $v['high'];
				$volume         = round($v['volume'] / 1000000, 3); 
				$grossWeight 	= $v['grossWeight'];
				$netWeight   	= $v['netWeight'];
				$addtime        = date('Y-m-d', $v['addtime']);
				$status         = $v['status'];
				$userName       = getUserNameById($v['adduser']);
				$sku            = $v['sku'];
				$num            = $v['num'];
				$skuBase        = $boxModel->getSkuBaseInfo($sku);
				$skuName        = $skuBase['goodsName'];
				$skuPrice       = $skuBase['goodsCost'];
				$totalRmb       = $skuPrice * $num;
				switch($status){
					case '1':
						$stu = '已配货';
						break;
					case '2':
						$stu = '已复核';
						break;
					case '3':
						$stu = '已装柜';
						break;
					case '4':
						$stu = '海外已收货';
						break;
					default:
						$stu = '';
						break;
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $orderSn);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$row, $boxId);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$row, $sku);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$row, $num);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$row, '');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$row, $length);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$row, $width);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$row, $high);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$row, $volume);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$row, $netWeight);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$row, $grossWeight);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$row, $skuName);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M'.$row, '');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O'.$row, $skuPrice);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P'.$row, $totalRmb);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q'.$row, '');
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R'.$row, '');
				$row++;
			}
		}
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(30);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(20);	
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(20);
		$title		= "装柜清单".date('Y-m-d');
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
