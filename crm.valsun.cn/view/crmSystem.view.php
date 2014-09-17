<?php
/**
 * 客户关系管理系统 crmSystem.view.php
 * @author chenwei
 */
class CrmSystemView extends BaseView {
	
	 private $where = '';
	 private $table = '';

	//查询页面渲染
	public function view_crmSystemList() {
		//基础代码准备
		$CrmSystem = new CrmSystemAct();	
		
		//搜索操作
		$condition  = array();
		$orderByStr = "";
		if(isset($_POST) && !empty($_POST)){	
		
			$keyWordsType = trim($_POST['keyWordsType']);//搜索字段名称
			$keyWords	  = trim($_POST['keyWords']);//搜索填写内容
			if(!empty($keyWords)){
				$condition[] = "{$keyWordsType} = '{$keyWords}'";				
			}
			
			$salesAccountList = trim($_POST['salesAccountList']);//账号
			if(!empty($salesAccountList)){
				$condition[] = "salesaccount = '{$salesAccountList}'";
			}
			
			$platformList = trim($_POST['platformList']);//平台
			if(!empty($platformList)){
				$condition[] = "platform = '{$platformList}'";
			}
			
			$sortType     = trim($_POST['sortType']);//排序
			if(!empty($sortType)){
				if($sortType == 'totalpayDesc'){
					$orderByStr = " ORDER BY totalpay DESC ";//降序
				}
				
				if($sortType == 'totalpayAsc'){
					$orderByStr = " ORDER BY totalpay ASC ";//升序
				}
				
				if($sortType == 'totaltimesDesc'){
					$orderByStr = " ORDER BY totaltimes DESC ";//降序
				}
				
				if($sortType == 'totaltimesAsc'){
					$orderByStr = " ORDER BY totaltimes ASC ";//升序
				}
			}	
			if(empty($condition)){
				$this->where = $orderByStr;
			}else{	
				$this->where = "WHERE ".implode(" and ",$condition).$orderByStr;
			}
		}else{
			//默认条件按中金额排序
			$this->where = " ORDER BY totalpay DESC ";
		}
		
		
		//分页
		$total = $CrmSystem->act_getPageNum();
		$num      = 100;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$this->where    .= $page->limit;
		
        $crmListArr = $CrmSystem->act_crmSysermList($this->where);
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

		//面包屑
		$navlist = array (array ('url' => 'index.php?mod=crmSystem&act=crmSystemList','title' => '客户中心'),
						  array ('url' => 'index.php?mod=crmSystem&act=crmSystemList','title' => '客户关系列表'));
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toplevel', 0);		
		$this->smarty->assign('crmListArr', $crmListArr);
		$this->smarty->assign('keyWordsType',$keyWordsType);
		$this->smarty->assign('keyWords',$keyWords);
		$this->smarty->assign('salesAccountList',$salesAccountList);
		$this->smarty->assign('platformList',$platformList);
		$this->smarty->assign('sortType',$sortType);
		$this->smarty->display("crmSystemList.htm");
	}
	
	/*
     * 客户关系管理XLS表格导出
     */
	 public function view_crmSystemExportExcel(){
		// echo "<pre>";print_r($_POST);exit;
		header("Content-type:text/html;charset=utf-8");
		//基础代码准备
		$CrmSystem = new CrmSystemAct();
		$condition  = array();
		$orderByStr = "";		
		$keyWordsType = trim($_POST['keyWordsType']);//搜索字段名称
		$keyWords	  = trim($_POST['keyWords']);//搜索填写内容
		if(!empty($keyWords)){
			$condition[] = "{$keyWordsType} = '{$keyWords}'";				
		}
		
		$salesAccountList = trim($_POST['salesAccountList']);//账号
		if(!empty($salesAccountList)){
			$condition[] = "salesaccount = '{$salesAccountList}'";
		}
		
		$platformList = trim($_POST['platformList']);//平台
		if(!empty($platformList)){
			$condition[] = "platform = '{$platformList}'";
		}
		
		$sortType     = trim($_POST['sortType']);//排序
		if(!empty($sortType)){
			if($sortType == 'totalpayDesc'){
				$orderByStr = " ORDER BY totalpay DESC ";//降序
			}
			
			if($sortType == 'totalpayAsc'){
				$orderByStr = " ORDER BY totalpay ASC ";//升序
			}
			
			if($sortType == 'totaltimesDesc'){
				$orderByStr = " ORDER BY totaltimes DESC ";//降序
			}
			
			if($sortType == 'totaltimesAsc'){
				$orderByStr = " ORDER BY totaltimes ASC ";//升序
			}
		}	
		if(empty($condition)){
			$this->where = $orderByStr;
		}else{	
			$this->where = "WHERE ".implode(" and ",$condition).$orderByStr;
		}
	
		$crmListArr = $CrmSystem->act_crmSysermList($this->where);
			
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

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '姓名/客户ID');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '邮件');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '客户电话');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '所在国家');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '总购买金额');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '总购买次数');
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '销售账号');    
    	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '平台');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '最新购买时间');
		$a = 2;
		if(!empty($crmListArr)){
			//单据信息
			foreach($crmListArr as $key => $crmListExportArr) {
				$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit(@$crmListExportArr['clientname'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit(@$crmListExportArr['email'], PHPExcel_Cell_DataType::TYPE_STRING);				
				$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit(@$crmListExportArr['phone'], PHPExcel_Cell_DataType::TYPE_STRING);	
				$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit($crmListExportArr['country'], PHPExcel_Cell_DataType::TYPE_STRING);			
				$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($crmListExportArr['totalpay'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$a)->setValueExplicit($crmListExportArr['totaltimes'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$a)->setValueExplicit($crmListExportArr['salesaccount'], PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit($crmListExportArr['platform'], PHPExcel_Cell_DataType::TYPE_STRING);      
				$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$a)->setValueExplicit(date("Y-m-d H:i:s",$crmListExportArr['lastbuytime']), PHPExcel_Cell_DataType::TYPE_STRING);
				$a++;		
			}			
		}
		$objPHPExcel->getActiveSheet(0)->getStyle('A1:I1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(20);
			
		$title		= "crmInfo".date('Y-m-d');
		$titlename	= "crmInfo".date('Y-m-d').".xls";
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