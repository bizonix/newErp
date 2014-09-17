<?php
/*
 * iqc完成检测数据显示、搜索、报表导出
 */
class IqcInfoView extends BaseView{

    public $memcacheObj = '';
	
//IQC完成检测数据 显示、搜索 页面渲染
    public function view_iqcScanList(){
		//global $memc_obj;
		$condition = array();
		$where = "";
		$d_status = 0;
		$isCombine = 1;
		$sku	   = "";
		$j_status  = 0;
		$t_status  = 0;
		$startTime = date("Y-m-d H:i:s", time());
		$endTime = date("Y-m-d H:i:s", time());
		if(isset($_POST) && !empty($_POST)){
			
			$sku      	= trim($_POST['sku']);
			$d_status 	= $_POST['d_status'];//导出类型:0->显示所以信息 1:->不良品统计表导出
			$isCombine  = $_POST['isCombine'];//是否合并：0->需要合并   1：->默认不合并
			$sellerId	= $_POST['sellerId'];//大卖家ID
			$j_status	= $_POST['j_status'];//检测类型
			$t_status	= $_POST['t_status'];//SKU分类检测
			
			if(!empty($_POST['startTime']) && !empty($_POST['endTime'])){
				$startTime	= strtotime(trim($_POST['startTime']));//开始时间
				$endTime	= strtotime(trim($_POST['endTime']));//结束时间
				$condition[] = "checkTime BETWEEN {$startTime} AND {$endTime}";
				$startTime	= trim($_POST['startTime']);//开始时间
				$endTime	= trim($_POST['endTime']);//结束时间
			}
			
			$condition[] = "sellerId = '{$sellerId}'";
			if(!empty($j_status)){
				$condition[] = "checkTypeID = '{$j_status}'";		
			}
			if(!empty($t_status)){
				$condition[] = "skuTypeCheckID = '{$t_status}'";	
			}
			if(!empty($sku)){
				$condition[] = "sku = '{$sku}'";
			}
			$combine = false;
			if($d_status == 1){
				if($isCombine == 0){
					$combine = true;
					$condition[] = "rejectsNum > 0 GROUP BY sku";
				}else{
					$condition[] = "rejectsNum > 0";
				}	
			}
			$where = "where ".implode(" and ",$condition);
			//echo $where;exit;
			//echo "<pre>"; print_r($condition); exit;
		}
		$IqcCompleteInfoAct = new IqcCompleteInfoAct();
		$total = $IqcCompleteInfoAct->act_getPageNum($where);
		$num      = 100;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where    .= " order by id desc ".$page->limit;
		//echo $where;exit;
		$iqcCompleteInfoList = $IqcCompleteInfoAct->act_iqcCompleteInfo($where,$combine);
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
		$this->smarty->assign('iqcCompleteInfoList',$iqcCompleteInfoList);
		$qcStandard  	 	 = new qcStandardAct();
		$detectionTypeArrList  	 = $qcStandard->act_detectionTypeList($this->where);
		
		$qcStandard  	 	 = new qcStandardAct();
		$skuTypeQcArrList  	 = $qcStandard->act_skuTypeQcList($this->where);
		$skuTypeQcArr = array();
		foreach($skuTypeQcArrList as $skuTypeList){
			$skuTypeQcArr[$skuTypeList['id']] = $skuTypeList['typeName'];	
		}
		
		$detectionTypeArr = array();
		foreach($detectionTypeArrList as $listValue){
			$detectionTypeArr[$listValue['id']] = $listValue['typeName'];	
		}
		/*
		$skuInfos = array();
		foreach($iqcCompleteInfoList as $infoListValue){
			$where = "sku='{$infoListValue['sku']}'";
			$where = base64_encode($where);
			//存储用户权限信息到memcache
			$memkey = md5("pc_goods".trim($where));
			$memresult = $memc_obj->get($memkey);
			if(!$memresult){
				$memresult = UserCacheModel::goodsInfosCache("*", $where);
			}
			$skuInfos[$infoListValue['sku']] = @$memresult['data'][0]['goodsName'];
		}
		$this->smarty->assign('skuInfos',$skuInfos);
		*/
		$this->smarty->assign('sku',$sku);
		$this->smarty->assign('d_status',$d_status);
		$this->smarty->assign('isCombine',$isCombine);
		$this->smarty->assign('j_status',$j_status);
		$this->smarty->assign('t_status',$t_status);
		$this->smarty->assign('detectionTypeArr',$detectionTypeArr);
		$this->smarty->assign('skuTypeQcArr',$skuTypeQcArr);
		$this->smarty->assign('detectionTypeArrList',$detectionTypeArrList);
		$this->smarty->assign('skuTypeQcArrList',$skuTypeQcArrList);
		
		$this->smarty->assign('startTime',$startTime);
		$this->smarty->assign('endTime',$endTime);
		
		
		$this->smarty->assign('secnev','3');               //二级导航
		$this->smarty->assign('module','QC已完成检测信息');
		$this->smarty->assign('username',$_SESSION['userName']);		
		$navarr = array("<a href='index.php?mod=iqcInfo&act=iqcScanList'>QC检测信息</a>",">>","QC已完成检测信息");
        $this->smarty->assign('navarr',$navarr);
		$this->smarty->display('iqcScanList.htm');
	}

////IQC完成检测数据导出	
	 public function view_iqcExportExcel(){
		header("Content-type:text/html;charset=utf-8");
		$condition = array();
		$where = "";
		$sku      	= trim($_POST['sku']);
		$d_status 	= $_POST['d_status'];//导出类型:0->显示所以信息 1:->不良品统计表导出
		$isCombine  = $_POST['isCombine'];//是否合并：0->需要合并   1：->默认不合并
		$sellerId	= $_POST['sellerId'];//大卖家ID
		$j_status	= $_POST['j_status'];//检测类型
		$t_status	= $_POST['t_status'];//SKU分类检测
		
		if(!empty($_POST['startTime']) && !empty($_POST['endTime'])){
			$startTime	= strtotime(trim($_POST['startTime']));//开始时间
			$endTime	= strtotime(trim($_POST['endTime']));//结束时间
			$condition[] = "checkTime BETWEEN {$startTime} AND {$endTime}";
			$startTime	= trim($_POST['startTime']);//开始时间
			$endTime	= trim($_POST['endTime']);//结束时间
		}
		
		$condition[] = "sellerId = '{$sellerId}'";
		if(!empty($j_status)){
			$condition[] = "checkTypeID = '{$j_status}'";		
		}
		if(!empty($t_status)){
			$condition[] = "skuTypeCheckID = '{$t_status}'";	
		}
		if(!empty($sku)){
			$condition[] = "sku = '{$sku}'";
		}
		$combine = false;
		if($d_status == 1){
			if($isCombine == 0){
				$combine = true;
				$condition[] = "rejectsNum > 0 GROUP BY sku";
			}else{
				$condition[] = "rejectsNum > 0";
			}	
		}
		$where = "where ".implode(" and ",$condition);
		$IqcCompleteInfoAct = new IqcCompleteInfoAct(); 
		$iqcCompleteInfoList = $IqcCompleteInfoAct->act_iqcCompleteInfo($where,$combine);
		
		//echo "<pre>"; print_r($iqcCompleteInfoList); exit;
		//检测类型
		$qcStandard  	 	 = new qcStandardAct();
		$detectionTypeArrList  	 = $qcStandard->act_detectionTypeList($this->where);
		$detectionTypeArr = array();
		foreach($detectionTypeArrList as $listValue){
			$detectionTypeArr[$listValue['id']] = $listValue['typeName'];	
		}
		
		//SKU分类检测
		$qcStandard  	 	 = new qcStandardAct();
		$skuTypeQcArrList  	 = $qcStandard->act_skuTypeQcList($this->where);
		$skuTypeQcArr = array();
		foreach($skuTypeQcArrList as $skuTypeList){
			$skuTypeQcArr[$skuTypeList['id']] = $skuTypeList['typeName'];	
		}
		//echo "<pre>";print_r($detectionTypeArrList); exit;
		$objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '导出类型');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '料号');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '名称');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '到货数');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '检测类型');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '抽检数');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'SKU分类检测');    
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '不良数');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '不良原因');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '检测人');    
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '检测时间');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '大卖家');
		
		//echo "<pre>"; print_r($iqcCompleteInfoList); exit;
		
		if($d_status == 1){
			$objPHPExcel->setActiveSheetIndex(0)->getCell('A2')->setValueExplicit('不良品统计表', PHPExcel_Cell_DataType::TYPE_STRING);
		}else{
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('A2')->setValueExplicit('检测信息统计', PHPExcel_Cell_DataType::TYPE_STRING);
		}
		$a = 2;
		if(!empty($iqcCompleteInfoList)){
			foreach($iqcCompleteInfoList as $key => $iqcExportArr) {
				//echo $skuTypeQcArr[$iqcExportArr['skuTypeCheckID']]; echo "<br>";	
				if(is_numeric($iqcExportArr['checkUser'])){
					$username = UserModel::getUsernameById($iqcExportArr['checkUser']);
					if(!empty($username)){
						$iqcExportArr['checkUser'] = $username;
					}
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$a, $iqcExportArr['sku']);//SKU导出类型修改 add by chenwei 2013.12.20
				//$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit($iqcExportArr['sku'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit(Deal_SC($iqcExportArr['goodsName']), PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit($iqcExportArr['arrivalNum'], PHPExcel_Cell_DataType::TYPE_STRING);					
				$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($detectionTypeArr[$iqcExportArr['checkTypeID']], PHPExcel_Cell_DataType::TYPE_STRING);			
				$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$a)->setValueExplicit($iqcExportArr['checkNum'], PHPExcel_Cell_DataType::TYPE_NUMERIC);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$a)->setValueExplicit(@$skuTypeQcArr[$iqcExportArr['skuTypeCheckID']], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit($iqcExportArr['rejectsNum'], PHPExcel_Cell_DataType::TYPE_NUMERIC);//TYPE_NUMERIC 数值型 ADD BY chenwei 2013.12.20
				$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$a)->setValueExplicit($iqcExportArr['rejectsReason'], PHPExcel_Cell_DataType::TYPE_STRING);      
				$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$a)->setValueExplicit($iqcExportArr['checkUser'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$a)->setValueExplicit(date("Y-m-d H:i:s",$iqcExportArr['checkTime']), PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('L'.$a)->setValueExplicit('赛维网络', PHPExcel_Cell_DataType::TYPE_STRING);	
				//echo $a.'&nbsp;&nbsp;&nbsp;'.$iqcExportArr['sku'].'&nbsp;&nbsp;&nbsp;'.$iqcExportArr['goodsName'].'<br/>';
				$a++;
			}			
		}
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:N500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(50);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(50);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(20); 
		
				
		$title		= "QcWorkInfoExport".date('Y-m-d');
		$titlename	= "QcWorkInfoExport".date('Y-m-d').".xls";
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
		header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment;filename={$titlename}");
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output'); 
		exit;
        

	 }
	
}