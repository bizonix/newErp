<?php
include_once WEB_PATH.'lib/opensys_functions.php';
class sendEbayCaseMailView extends BaseView {
	static $errCode	=	0;
	static $errMsg	=	"";
	
	/*
	 * 构造函数
	 */
	public function __construct(){
		parent::__construct();
	}
	
	//获取试图开case的买家信息
	public function view_getTryOpenCaseBuyerMsg() {
		require_once(WEB_PATH.'lib/PHPExcel.php');
		$uploadfile = date("Y").date("m").date("d").rand(1,100).".xlsx";
		$path       = WEB_PATH.'html/template/updload/ebayBuyerInfo/';
		if(move_uploaded_file($_FILES['upfile']['tmp_name'], $path.$uploadfile)) {
			echo "<script>alert('导入成功！');</script>";
			$ismark       = 'yes';
		}else {
			echo "<script>alert('导入失败！');</script>";
			$ismark       = 'no';
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
		
		$PHPExcel 		= $PHPReader->load($filePath);
		$sheet      	= $PHPExcel->getActiveSheet();
		/**取得一共有多少列*/
		$c 				= 2;
		$buyerObj     	= new sendEbayCaseMailModel();
		$counter        = 0;
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
		
			//获取要写入数据库的数据
			$seller_id 				= trim($sheet->getCell($aa)->getValue());
			$seller_id				= isset($seller_id) ? addslashes($seller_id) : '';
			$item_id 				= trim($sheet->getCell($bb)->getValue());
			$item_id				= isset($item_id) ? addslashes($item_id) : '';
			$transaction_item 		= trim($sheet->getCell($cc)->getValue());
			$transaction_item		= isset($transaction_item) ? addslashes($transaction_item) : '';
			$transaction_date 		= trim($sheet->getCell($dd)->getValue());
			$transaction_date		= isset($transaction_date) ? strtotime($transaction_date) : '';
			$delay_arrive_time 		= trim($sheet->getCell($ee)->getValue());
			$delay_arrive_time		= isset($delay_arrive_time) ? strtotime($delay_arrive_time) : '';
			$buyer_id		 		= trim($sheet->getCell($ff)->getValue());
			$buyer_id				= isset($buyer_id) ? addslashes($buyer_id) : '';
			$buyer_try_open_time 	= trim($sheet->getCell($gg)->getValue());
			$buyer_try_open_time 	= isset($buyer_try_open_time) ? strtotime($buyer_try_open_time) : '';
			$transaction_id 	    = trim($sheet->getCell($hh)->getValue());
			$transaction_id 	    = isset($transaction_id) ? $transaction_id : '';
			$create_time			= time();
			if(empty($seller_id)){
				break;
			}
			//将数据写入数据库
			$InsertResult        = $buyerObj->insertBuyerMsgIntoDatabase($seller_id, $item_id, $transaction_item, $transaction_date, $delay_arrive_time, $buyer_id, $buyer_try_open_time, $transaction_id, $create_time);
			
			$c++;
		}
		$counter = $c-2;
		echo "<script>alert('总共导入{$counter}条数据');</script>";
		$sec_menue		= '8';
		$this->smarty->assign('sec_menue', $sec_menue);
		$this->smarty->display('sendEbayCaseMail.htm');
	}
	
	//新增获取试图开case的买家信息并推送邮件页面
	/* public function view_deliverMailToBuyers() {
		$sec_menue		= '8';
		$this->smarty->assign('sec_menue', $sec_menue);                                                                     //顶层菜单
		$this->smarty->display('sendEbayCaseMail.htm');
	} */
	
	
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
			 	 if($key === 'is_send_mail'){
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
				if($key === 'transaction_date' || $key === 'delay_arrive_time' || $key === 'buyer_try_open_time' || $key === 'send_time' || $key === 'create_time'){
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
		$secm_obj  = new sendEbayCaseMailModel();
		$buyerinfoes = $secm_obj->getEbayBuyerInfo(mysql_escape_string($account),$timestamp,$sendstatus);
		$header      = array('编号','ebay账号','物品编号','刊登标题','交易时间','预计最迟送达日期','买家账号','买家试图反映日期','交易编号','推送邮件状态','邮件推送时间','推送失败原因','数据插入时间');
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
		$sec_menue	  = '8';
		$this->smarty->assign('status', $sendstatus);
		$this->smarty->assign('sec_menue', $sec_menue);  
		$this->smarty->assign('account', $account);
		$this->smarty->assign('time', $time);
		$this->smarty->assign('header', $header);
		$this->smarty->assign('infoes', $buyerinfoes);                                                                 //顶层菜单
		$this->smarty->display('sendEbayCaseMail.htm');
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