<?php
/**
 * 类名：ShipfeeQueryAct
 * 功能：运德跟踪号生成管理动作处理层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
  
class ShipfeeQueryAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	
	/**
	 * ShipfeeQueryAct::actSaveBatch()
	 * 批量运费查询excell文件上传保存
	 * @return array  
	 */
	public function actSaveBatch(){
		$data	= array();
		$res	= array();
		$uid	= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if(isset($_FILES['upfile']) && !empty($_FILES['upfile'])) {
			$fielName = $uid."_".date('YmdHis').'_'.rand(1,3009).".xls";
			$fileName = WEB_PATH.'html/temp/'.$fielName;
			if(move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				$filePath = $fileName;
			}
		}
		if(substr($filePath,-3)!='xls') {
			show_message($this->smarty,"导入的文件名格式错误！","index.php?mod=ShipfeeQuery&act=batch");
			return false;
		}
		
		//读取导入文件
		require_once WEB_PATH."lib/PHPExcel.php";
		$PHPExcel		= new PHPExcel(); 
		$PHPReader		= new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)) {      
			$PHPReader	= new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)) {
				show_message($this->smarty,"文件内容无法读取！","index.php?mod=ShipfeeQuery&act=batch");
				@unlink($filePath);
				return false;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$currentSheet 	= $PHPExcel->getSheet(0);
		$row			= 1;
		$shipArr		= array(8,9);	//依次为DHL,联邦
		$flag			= true;
		while(true) {
			$aa			= 'A'.$row;
			$bb			= 'B'.$row;
			$country	= trim($currentSheet->getCell($aa)->getValue());
			$country	= substr($country,0,50);
			$weight		= trim($currentSheet->getCell($bb)->getValue());
			if($row==1) {
				if ($country!=='国家' || $weight!='重量') {
					show_message($this->smarty,"EXCELL表头不能随意更改","index.php?mod=ShipfeeQuery&act=batch");
					@unlink($filePath);
					return false;
				}
			} else {
				$weight		= round(floatval($weight),3);
				$dhlFee		= TransOpenApiAct::fixCarrierQueryNew(8, $country, $weight);
				$fedexFee	= TransOpenApiAct::fixCarrierQueryNew(9, $country, $weight);
				if(empty($country) && empty($weight)) break;
				array_push($res,array("country"=>$country, "weight"=>$weight, "dhlFee"=>$dhlFee['fee'], "fedexFee"=>$fedexFee['fee']));
			}
			$row++;
		}
		$tharr			= array("国家", "重量", "DHL运费", "FEDEX运费");
		$data['res']	= "<a href=".self::exportXls($tharr, $res)." target='_blank'><font color=\"green\">批量运费查询成功，点我下载</font></a>";
		return $data;
	}
	
	/**
	 * ShipfeeQueryAct::exportXls()
	 * 导出xls文件
	 * @param array $res 结果值
	 * @return string 文件路径
	 */
	private function exportXls($tharr, $res){
		$data  	= array();
		$tdarr 	= array();
		$dates 	= array();
		$pos   	= array();
		$filename	= 'batch_shipfee_info_'.date('Y-m-d',time()).'_'.$_SESSION[C('USER_AUTH_SYS_ID')];;
		$fileurl	= WEB_URL."temp/".$filename.".xls";
		$filepath	= WEB_PATH."html/temp/".$filename.".xls";
		array_push($data, $tharr);
		foreach ($res as $v) {
			$tdarr	= array(
				$v['country'],
				$v['weight'],
				round(floatval($v['dhlFee']),3),
				round(floatval($v['fedexFee']),3),
			);
			array_push($data, $tdarr);
		}		
		require_once WEB_PATH."lib/php-export-data.class.php";
		$excel = new ExportDataExcel('file');
		$excel->filename = $filepath; 
		$excel->initialize();
		foreach($data as $row) {
			$excel->addRow($row);
		}  
		$excel->finalize(); 
		unset($data);
		if (file_exists($filepath)) {
			return $fileurl;
		} else {	
			return "";
		}
	}
	
	/**
	 * ShipfeeQueryAct::actBatchShipfeeQueryImport()
	 * 批量运费验证信息导入导出
	 * @return array 
	 */
	public function actBatchShipfeeQueryImport(){
		$data				= array();
		$uid 				= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if(isset($_FILES['upfile']) && !empty($_FILES['upfile'])){
			$fielName 		= $uid."_batch_shipfee_".date('YmdHis').'_'.rand(1,3009).".xls";
			$fileName 		= WEB_PATH.'html/temp/'.$fielName;
			if(move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				$filePath 	= $fileName;
			}
		}
		if(substr($filePath,-3) != 'xls') {
			show_message($this->smarty,"导入的文件名格式错误！","index.php?mod=shipfeeQuery&act=shipfeeQueryImport");
			@unlink($filePath);
			exit;
		}
		
		//读取导入文件
		require_once WEB_PATH."lib/PHPExcel.php";
		//如果读取的表较大，需要调整内存和时间限制
		ini_set('memory_limit', '20M');
		// ini_set('max_execution_time', '2');
		$PHPExcel		= new PHPExcel(); 
		$PHPReader		= new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)) {      
			$PHPReader	= new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)) {
				show_message($this->smarty,"文件内容无法读取！","index.php?mod=shipfeeQuery&act=shipfeeQueryImport");
				@unlink($filePath);
				exit;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$currentSheet 	= $PHPExcel->getSheet(0);
		//取得共有多少列,若不使用此静态方法，获得的$col是文件列的最大的英文大写字母  
		$cols			=PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());  
		$rows			=$currentSheet->getHighestRow();
		if($rows > C('EXCELL_ROWS_MAX')) {
			show_message($this->smarty,"单个EXCELL文件行数超过系统处理最大值: <b>".C('EXCELL_ROWS_MAX')."</b> 行!","index.php?mod=shipfeeQuery&act=shipfeeQueryImport");
			@unlink($filePath);
			exit;
		}		
		$row			= 1;
		$res			= array();
		while(1) {
			$flag		= true;
			$rowFlag	= true;
			$country	= '';
			$weight		= 0;
			$tracknum	= '';
			$carrier	= '';
			$carrierId	= 0;
			$fee		= 0;
			$totalFee	= 0;
			$carrierNew	= '';
			$aa			= 'A'.$row;
			$bb			= 'B'.$row;
			$cc			= 'C'.$row;
			$dd			= 'D'.$row;
			$ee			= 'E'.$row;
			$ff			= 'F'.$row;
			$gg			= 'G'.$row;
			$country	= post_check(trim($currentSheet->getCell($aa)->getValue()));
			$tracknum	= post_check(trim($currentSheet->getCell($bb)->getValue()));
			$weight		= post_check(trim($currentSheet->getCell($cc)->getValue()));
			$carrier	= post_check(trim($currentSheet->getCell($dd)->getValue()));
			$carrierNew	= post_check(trim($currentSheet->getCell($ee)->getValue()));
			$totalFee	= post_check(trim($currentSheet->getCell($ff)->getValue()));
			$fee		= post_check(trim($currentSheet->getCell($gg)->getValue()));
			if(empty($country)) break;
			if($row == 1) {
				if($country != '国家' || $tracknum != '挂号条码' || $weight != '重量' || $carrier != '运输方式' || $carrierNew != '最优运输方式' || $totalFee != '总运费' || $fee != '折扣运费') {
					show_message($this->smarty,'<font color="red">文件导入失败，导入模版内容有误,请勿修改表头</font>',"index.php?mod=shipfeeQuery&act=shipfeeQueryImport");
					@unlink($filePath);
					exit;
				}
				$res []	= array("国家","挂号条码","重量","运输方式","最优运输方式","总运费","折扣运费");
			} else {
				if(!empty($tracknum)) {
					$carrierId	= self::getErpCarrierId($carrier);
				}
				if(empty($carrierId)) {
					$fees		= TransOpenApiAct::act_getBestCarrierNew(1,$country,$weight);
				} else {
					$fees		= TransOpenApiAct::act_fixCarrierQueryNew($carrierId,$country,$weight);
				}
				if(empty($fees)) {
					self::$errMsg	.= "运费校验失败：{$carrier}==={$country}==={$weight}！<br/>";
					$res []		= array($country,$tracknum,$weight,$carrier,'','','');
				} else {
					$fee		= $fees['fee'];
					$totalFee	= $fees['totalFee'];
					$carrierNew	= self::getErpCarrierId($fees['carrierId'],true);
					$res []		= array($country,$tracknum,$weight,$carrier,$carrierNew,$totalFee,$fee);
				}				
			}	
			$row++;
		}
		$fileName		= 'batch_shipfee_infos_'.$uid;
		$fileUrl		= WEB_URL."temp/".date('Ymd')."/".$fileName.".xls";
		$filePath		= WEB_PATH."html/temp/".date('Ymd')."/".$fileName.".xls";
		require_once WEB_PATH."lib/php-export-data.class.php";
		$excel 			= new ExportDataExcel('file');
		$excel->filename = $filePath; 
		$excel->initialize();
		foreach($res as $row) {
			$excel->addRow($row);
		}  
		$excel->finalize(); 
		unset($res);
		$data['url']		= $fileUrl;
		$data['res']		= self::$errMsg;
		return $data;
    }
	
	/**
	 * ShipfeeQueryAct::getErpCarrierId()
	 * 获取ERP运输方式对应的物流系统运输方式ID
	 * @param string $carrier ERP运输方式
	 * @return int
	 */
	private function getErpCarrierId($carrier,$is_id=false){
		$type			= strtolower($carrier);
		$carrierArr		= array(
							"中国邮政挂号"		=> "2",
							"香港小包挂号" 		=> "4",
							"ems" 				=> "5",
							"eub" 				=> "6",
							"dhl" 				=> "8",
							"fedex" 			=> "9",
							"global mail" 		=> "10",
							"ups ground" 		=> "46",
							"usps" 				=> "47",
							"新加坡小包挂号"	=> "52",
							"德国邮政挂号"		=> "53",
							"ups美国专线"		=> "62",
							"ups英国专线"		=> "96",
							"ups法国专线"		=> "97",
							"ups德国专线"		=> "98",
							"俄速通挂号"		=> "79",
							"俄速通大包"		=> "81",
							"俄速通平邮"		=> "80",
							"香港小包平邮"		=> "3",
							"新加坡dhl gm平邮"	=> "84",
							"瑞士小包平邮"		=> "87",
							"中国邮政平邮"		=> "1",
							"新加坡dhl gm挂号"	=> "83",
							"郑州小包挂号"		=> "86",
							"瑞士小包挂号"		=> "88",
							"比利时小包eu"		=> "89",
							"澳邮宝挂号"		=> "93",
						);
		if($is_id) {
			$carrierArrs	= array_flip($carrierArr);
			$carrier		= $carrierArrs[$type];
		} else {
			$carrier		= $carrierArr[$type];
		}
		return $carrier;
	}	
}
?>