<?php
class AliMarketView extends BaseView{
	
	function __construct(){
		parent::__construct();
	}
	
	/** 
	*  速卖通EDM营销首页初始化
	* tags 
	* @param unknowtype 
	* @return return_type 
	* @author xuzhaoyang 
	* @date 2014-7-3下午2:50:18 
	* @version v1.0.0 
	*/
	public function view_index(){
	
		$sec_menue		= '4';
		$this->smarty->assign('sec_menue', $sec_menue);
		$this->smarty->assign('toptitle', '速卖通营销');
		$this->smarty->display('aliMarket.htm');
	}
	
	/** 
	* 速卖通EDM营销数据导入处理
	* tags 
	* @param unknowtype 
	* @return return_type 
	* @author xuzhaoyang 
	* @date 2014-7-3下午2:51:34 
	* @version v1.0.0 
	*/
	public function view_processEDMData(){
		$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
		$path       = WEB_PATH.'html/template/upload/EDMData/';
		if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path.$uploadfile)) {
			echo "<script>alert('导入完成！');</script>";
			$ismark       = 'yes';
		}else {
			echo "<script>alert('导入失败！');</script>";
			$ismark       = 'no';
		}
		$data      = array();
		$fileName  = $path.$uploadfile;
		$filePath  = $fileName;
		$PHPExcel  = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		if(!$PHPReader->canRead($filePath)){
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filePath)){
				echo 'no Excel';
				return ;
			}
		}
		
		$PHPExcel 		    = $PHPReader->load($filePath);
		$sheetcount         = $PHPExcel->getSheetCount();//Excell文件中的表数量
		$counter            = 0;
		$inserterrorarr     = array();
		$aliMarkObj     	= new AliMarketModel();
		for($i = 0; $i < $sheetcount;$i++){
			$c 				    = 2;
			$sheet      	    = $PHPExcel->getSheet($i);
			$sheetname          = $PHPExcel->getSheetNames($i);
			while(true){
				$aa				= 'A'.$c;  //店铺账号
				$bb				= 'B'.$c;  //客服名字
				$cc 			= 'C'.$c;  //邮箱
				$dd 			= 'D'.$c;  //店铺数字
			
				//获取要写入数据库的数据
				$seller_id 						= trim($sheet->getCell($aa)->getValue());
				if(empty($seller_id)){
					break;
				}
				$data['seller_id']				= isset($seller_id) ? addslashes($seller_id) : '';
				$customer_s 				    = trim($sheet->getCell($bb)->getValue());
				$data['customer_s']				= isset($customer_s) ? addslashes($customer_s) : '';
				$gmail 		            		= trim($sheet->getCell($cc)->getValue());
				$data['gmail']		            = isset($gmail) ? addslashes($gmail) : '';
				$shopnum        		        = trim($sheet->getCell($dd)->getValue());
				$data['shopnum']        		= isset($shopnum) ? addslashes($shopnum) : '';
				$data['pushtime']		        = time();  //导入时间
				$data['sendtime']				= '';      //推送时间
				$data['sendstatus']				= 0;       //推送状态
				$data['failure_reason']			= '';      //推送失败原因
				
				//将数据写入数据库
				$InsertResult                   = $aliMarkObj->insertEDMData($data);
				if($InsertResult === 'failure'){
					$inserterrorarr[] = $c;
				} else {
					$counter++;
				}
				$c++;
				}
		}
		if(!empty($inserterrorarr)){
			$errorstr = implode(',', $inserterrorarr);
			echo "<script>alert('总共导入{$counter}条数据,导入失败行数为{$errorstr}');</script>";
		} else {
		echo "<script>alert('总共导入{$counter}条数据,无导入失败数据');</script>";
		}
		echo "<script>location.href='index.php?mod=aliMarket&act=index';</script>";
		$sec_menue		            = '4';
		$this->smarty->assign('sec_menue', $sec_menue);
		$this->smarty->assign('toptitle', '速卖通营销');
		$this->smarty->display('aliMarket.htm');
	}
	public function exportExcellForEDM($header,$buyerinfoes) {
		$objPHPExcel = new PHPExcel();
		$objWriter   = new PHPExcel_Writer_Excel5($objPHPExcel);
		$start       = 1;
		$buyerinfoes = array_merge(array($header),$buyerinfoes);
		$objPHPExcel->setActiveSheetIndex(0);
		foreach ($buyerinfoes as $k=> $person){
			$startchar = 65;
			foreach ($person as $key=>$val){
				if($key === 'sendstatus'){
					if($val === '0'){
						$objPHPExcel->getActiveSheet()->setCellValue(''.chr($startchar).$start, '未发送');
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_ORANGE);
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					} elseif($val === '1'){
						$objPHPExcel->getActiveSheet()->setCellValue(chr($startchar).$start, '已发送');
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_GREEN);
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					} elseif($val === '-1'){
						$objPHPExcel->getActiveSheet()->setCellValue(chr($startchar).$start, '发送失败');
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
						$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					}
					$startchar++;
					continue;
				}
				if($key === 'pushtime' || $key === 'sendtime'){
					$val = empty($val) ? '' : date('Y-m-d H:i:s',$val);
					$objPHPExcel->getActiveSheet()->setCellValue(''.chr($startchar).$start, $val);
					$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$startchar++;
					continue;
				}
				$objPHPExcel->getActiveSheet()->setCellValue(chr($startchar).$start, $val);
				$objPHPExcel->getActiveSheet()->getStyle(chr($startchar).$start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getColumnDimension(chr($startchar))->setWidth(15);
	
				$startchar++;
			}
			$start++;
		}
		//$objPHPExcel->getActiveSheet()->setCellValue('J2', '未发送');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");;
		header('Content-Disposition:attachment;filename="客服统计报表.xls"');
		header("Content-Transfer-Encoding:binary");
		$objWriter->save('php://output');
	
	}
	
	public function sendEDMDataMail(){
		
	}
}