<?php
class sendReturnMailView extends BaseView {
	static $errCode	    =	0;
	static $errMsg	    =	"";
	static $connection	=	NULL;
	static $channel	=	"";
	static $sec_menue	= '9';
	/*
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	
	//获取被邮局退回的订单信息
	public function view_getReturnOrderInfo() {
		require_once(WEB_PATH.'lib/PHPExcel.php');
		$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
		$path       = WEB_PATH.'html/template/updload/returnOrderInfo/';
		if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path.$uploadfile)) {
			echo "<script>alert('导入成功！');</script>";
		} else {
			echo "<script>alert('导入失败！');</script>";
			exit;
		}
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
		
		$PHPExcel 		   = $PHPReader->load($filePath);
		$sheet      	   = $PHPExcel->getActiveSheet();
		$inserterrorarr    = array();
		$field             = array();
		/**取得一共有多少列*/
		$c 				   = 2;
		$sRMObj     	   = new sendReturnMailModel();
		$counter           = 0;
		while(true){
			$aa				= 'A'.$c;
			$bb				= 'B'.$c;
			$cc 			= 'C'.$c;
			$dd 			= 'D'.$c;
			$ee 			= 'E'.$c;
			$ff				= 'F'.$c;
			$gg				= 'G'.$c;
			$hh				= 'H'.$c;
			$ii				= 'I'.$c;
			$jj             = 'J'.$c;
			$kk				= 'K'.$c;
			$ll				= 'L'.$c;
			$mm             = 'M'.$c;
			//获取要写入数据库的数据
			
			$returntime				= trim($sheet->getCell($aa)->getValue());
			$returntime             = PHPExcel_Shared_Date::ExcelToPHP($returntime);
			$field['returntime']	= !empty($returntime) ? $returntime : '';
			$scantime 				= trim($sheet->getCell($bb)->getValue());
			$field['scantime']		= !empty($scantime) ? strtotime($scantime) : '';
			$transtype		 		= trim($sheet->getCell($cc)->getValue());
			$field['transtype']		= !empty($transtype) ? addslashes($transtype) : '';
			$tracknumber		 	= trim($sheet->getCell($dd)->getValue());
			$field['tracknumber']   = !empty($tracknumber) ? addslashes($tracknumber) : '';
			$orderid		 		= trim($sheet->getCell($ee)->getValue());
			$field['orderid']		= !empty($orderid) ? addslashes($orderid) : '';
			$account		 		= trim($sheet->getCell($ff)->getValue());
			$field['account']		= !empty($account) ? addslashes($account) : '';
			$manager			 	= trim($sheet->getCell($gg)->getValue());
			$field['manager']		= !empty($manager) ? addslashes($manager) : '';
			$tip			 	    = trim($sheet->getCell($hh)->getValue());
			$field['tip']			= !empty($tip) ? addslashes($tip) : '';
			$returnreason        	= trim($sheet->getCell($ii)->getValue());
			$field['returnreason']  = !empty($returnreason) ? addslashes($returnreason) : '';
			$sku  					= trim($sheet->getCell($jj)->getValue());
			$field['sku']			= !empty($sku) ? addslashes($sku) : '';
			$purchasing 			= trim($sheet->getCell($kk)->getValue());
			$field['purchasing']	= !empty($purchasing) ? addslashes($purchasing) : '';
			$note					= trim($sheet->getCell($ll)->getValue());
			$field['note']			= !empty($note) ? addslashes($note) : '';
			$itemname				= trim($sheet->getCell($mm)->getValue());
			$field['itemname']		= !empty($itemname) ? addslashes($itemname) : '';
			$field['sendstatus']    = 0;
			$field['sendtime']      = '';
			$field['failurereason']	= '';
			$field['pushtime']		= time();
			if(empty($sku)){
				break;
			}
			
			//将数据写入数据库
			$InsertResult    =  $sRMObj->insertOrderInfoIntoDatabase($field);
			if($InsertResult === 'failure'){
				$inserterrorarr[] = $c;
			} else {
				$counter++;
			}
			$c++;
		}
		if(!empty($inserterrorarr)){
			$errorstr = implode(',', $inserterrorarr);
			echo "<script>alert('总共导入{$counter}条数据,导入失败行数为{$errorstr}');</script>";
		} else {
			echo "<script>alert('总共导入{$counter}条数据,无导入失败数据');</script>";
		}
		
		echo "<script>location.href='index.php?mod=sendEbayCaseMail&act=deliverMailToBuyers';</script>";
		$this->smarty->assign('sec_menue', self::$sec_menue);
		$this->smarty->display('sendReturnMail.htm');
	}
	
	
	/** 
	* 导出买家信息
	* tags 
	* @param   
	* @return   向浏览器直接输出excell
	* @author xuzhaoyang 
	* @date 2014-6-25下午1:17:04 
	* @version v1.0.0 
	*/
	public function exportExcellForBuyerInfo ($header,$buyerinfoes) {
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
				if($key === 'returntime' || $key === 'sendtime' || $key === 'pushtime' || $key === 'scantime' || $key === 'create_time'){
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
	
	/** 
	* 根据搜索条件显示买家信息 
	* tags 
	* @param 无 
	* @return return_type 
	* @author xuzhaoyang 
	* @date 2014-6-25下午1:22:29 
	* @version v1.0.0 
	*/
	public function view_deliverMailToBuyers(){
		/* $buyerObj     	= new sendEbayCaseMailModel();
		$buyerObj->deleteError(); */
		$account 	  = isset($_GET['account']) ? $_GET['account'] : '';
		$time         = isset($_GET['time']) ? $_GET['time'] : '';
		$export  	  = isset($_GET['export']) ? TRUE : FALSE;
		$sendstatus   = isset($_GET['sendstatus']) ? $_GET['sendstatus'] : 0;
		$timestamp	  = strtotime($time);
		$secm_obj     = new sendEbayCaseMailModel();
		$buyerinfoes = $secm_obj->getEbayBuyerInfo(mysql_escape_string($account),$timestamp,$sendstatus);
		$header      = array('编号','通知退件日期','扫描日期','物流渠道','跟踪号','订单号','账户','账户负责人',
					   '账户负责人处理意见','退件原因','SKU','采购员','备注','品名','推送邮件状态','邮件推送时间',
					   '推送失败原因','数据插入时间');
		if($export){
			$this->exportExcellForBuyerInfo($header,$buyerinfoes);
			exit;
		}
		foreach ($buyerinfoes as &$buyer){
			$buyer['transaction_date']     = date('Y-m-d H:i:s',$buyer['transaction_date']);
			$buyer['delay_arrive_time']    = date('Y-m-d H:i:s',$buyer['delay_arrive_time']);
			$buyer['buyer_try_open_time']  = date('Y-m-d H:i:s',$buyer['buyer_try_open_time']);
			$buyer['send_time']            = empty($buyer['send_time']) ? '' : date('Y-m-d H:i:s',$buyer['send_time']);
			$buyer['create_time']          = date('Y-m-d H:i:s',$buyer['create_time']);
			
		}
		
		$this->smarty->assign('status', $sendstatus);
		$this->smarty->assign('sec_menue', self::$sec_menue);  
		$this->smarty->assign('account', $account);
		$this->smarty->assign('time', $time);
		$this->smarty->assign('header', $header);
		$this->smarty->assign('infoes', $buyerinfoes);                                                                 //顶层菜单
		$this->smarty->display('sendReturnMail.htm');
	}
	
	/** 
	* 异步改变邮件发送状态 
	* tags 
	* @param unknowtype 
	* @return return_type 
	* @author xuzhaoyang 
	* @date 2014-6-25下午8:44:47 
	* @version v1.0.0 
	*/
	public function view_ajaxChangeMailStatus(){
		$id 	  		= isset($_POST['id']) ? $_POST['id'] : '';
		$status         = isset($_POST['status']) ? $_POST['status'] : '';
		$buyerObj     	= new sendEbayCaseMailModel();
		$result         = $buyerObj->changeEmailStatus($id, $status);
		if($result){
			echo json_encode(array('CODE'=>'110','MSG'=>'修改成功'));
		} else {
			echo json_encode(array('CODE'=>'111','MSG'=>'修改失败'));
		}
	}
}