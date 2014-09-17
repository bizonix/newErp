<?php
class shipfeeAct extends Auth{
	static $errCode = 0;
	static $errMsg = "";
	function act_modify_cpsf_fujian(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$unitPrice = isset($_POST['unitPrice'])?$_POST['unitPrice']:"";
		$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_cpsf_fujian($id,$groupName,$countries,$unitPrice,$handlefee);
	}
	function act_modify_cpsf_shenzhen(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		
		//$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$firstweight = isset($_POST['firstweight'])?$_POST['firstweight']:"";
		//$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_cpsf_shenzhen($id,$groupName,$countries,$firstweight);
	}
	function act_modify_cprg_fujian(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$unitPrice = isset($_POST['unitPrice'])?$_POST['unitPrice']:"";
		$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";
		$msg = shipfeeModel::modify_cprg_fujian($id,$groupName,$countries,$unitPrice,$handlefee);
	   // return $msg;
	}
	function act_modify_ems_shenzhen(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$firstweight = isset($_POST['firstweight'])?$_POST['firstweight']:"";
		$firstweight0 = isset($_POST['firstweight0'])?$_POST['firstweight0']:"";
		$nextweight = isset($_POST['nextweight'])?$_POST['nextweight']:"";
		$files = isset($_POST['files'])?$_POST['files']:"";
		$declared_value = isset($_POST['declared_value'])?$_POST['declared_value']:"";
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_ems_shenzhen($id,$groupName,$countries,$firstweight,$firstweight0,$nextweight,$files,$declared_value);
	}
	function act_modify_eub_shenzhen(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		//$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$unitprice = isset($_POST['unitprice'])?$_POST['unitprice']:"";
		$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_eub_shenzhen($id,$groupName,$countries,$unitprice,$handlefee);
	}
	function act_modify_hkpostsf_hk(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		//$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$firstweight = isset($_POST['firstweight'])?$_POST['firstweight']:"";
		$nextweight = isset($_POST['nextweight'])?$_POST['nextweight']:"";
		$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_hkpostsf_hk($id,$groupName,$countries,$firstweight,$nextweight,$handlefee);
	}
	function act_modify_hkpostrg_hk(){
		$groupName = isset($_POST['groupName'])?$_POST['groupName']:"";
		//$channelName = isset($_POST['channelName'])?$_POST['channelName']:"";
		$countries = isset($_POST['countries'])?$_POST['countries']:"";
		$firstweight = isset($_POST['firstweight'])?$_POST['firstweight']:"";
		$nextweight = isset($_POST['nextweight'])?$_POST['nextweight']:"";
		$handlefee = isset($_POST['handlefee'])?$_POST['handlefee']:"";
		
		$id = isset($_POST['id'])?$_POST['id']:"";
		shipfeeModel::modify_hkpostrg_hk($id,$groupName,$countries,$firstweight,$nextweight,$handlefee);
	}
	function act_modify_dhl_shenzhen(){
		
		$objPHPExcel = new PHPExcel();
		$PHPReader=new PHPExcel_Reader_Excel2007();
	    if(!$PHPReader->canRead($filepath))
	    {
			$PHPReader=new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filepath))
			{
				echo 'no excel!';
				return false;
			}
	    }
		if(!empty($_FILES)){
		    // echo $_FILES['upfile']['name'];
			if($_FILES['upfile1']){
				$filename = date("Y-m-d H:i:s").rand(1,3009).".xls";
				if (move_uploaded_file($_FILES["upfile1"]["tmp_name"],"../xls/".$filename)){
					$filepath = "../xls/" . $filename;
				}else{
					self::$errCode = 444;
	                self::$errMsg = "文件上传失败！";
					return;
				}
				$PHPExcel=$PHPReader->load($filepath);
				$excellists = self::excel2array($PHPExcel,$filepath,0,11);
				for($i=1;$i<=10;$i++)
				{
					unset($excellists[$i]);
				}
				$weight_freight = array ();
			    $weightlists = array ();
			
				foreach($excellists as $key => $excellist ) {
					foreach($excellist as $k => $v ) {
						if ($k > 1) {
							$tekey = isset($excellists[$key-1])?$excellists[$key-1][1]:0;
							if(!empty($excellist[1])){
								$weightlists[$k-2][] = $tekey."-".$excellist[1].":".$v;
							}
						}
					}
				}
				
				foreach($weightlists as $key => $value){
					$weightfreight = implode(",",$value);
					$partition = $key + 1;
					shipfeeModel::modify_dhl_shenzhen1($weightfreight,$partition,1);
			    }
			}
			if($_FILES['upfile2']){
				$filename = date("Y-m-d H:i:s").rand(1,3009).".xls";
				if (move_uploaded_file($_FILES["upfile2"]["tmp_name"],"../xls/".$filename)){
					$filepath = "../xls/" . $filename;
				}else{
					self::$errCode = 444;
	                self::$errMsg = "文件上传失败！";
					return;
				}
				$PHPExcel=$PHPReader->load($filepath);
				//$sheet = $PHPExcel->getSheet(0);
				$excellists = self::excel2array($PHPExcel,$filepath,0,9);
				for($i = 1; $i <= 8; $i ++) {
					unset ($excellists1[$i] );
				}
				
				foreach ($excellists1 as $key1 => $excellist){
					$weightlist = '';
					
					if(isset($excellist[1])){

						$weightlist = "20-30:{$excellist[3]},30-70:{$excellist[4]},70-100:{$excellist[5]},100-300:{$excellist[6]},300-500:{$excellist[7]},500-:{$excellist[8]}";

						$partition = $key1 + 1;
						shipfeeModel::modify_dhl_shenzhen1($weightlist,$partition,2);

					}
				
				}
				$partition_list = excel2array($PHPExcel,$filepath,1,7);
				$country_arr_mode1 = array();
				$country_arr_mode2 = array();
				foreach ($partition_list as $key => $partition){
				    if($key>1){
						$country_arr_mode1[$partition[5]][] = "[".$partition[3]."]";
					    $country_arr_mode2[$partition[6]][] = "[".$patition[3]."]";
					}

				}
				foreach($country_arr_mode1 as $key => $country_arr){
					$countries_mode1 = implode(",",$country_arr);
					shipfeeModel::modify_dhl_shenzhen2($countries_mode1,$key,1);
				}
				foreach($country_arr_mode2 as $key => $country_arr){
					$countries_mode2 = implode(",",$country_arr);
					shipfeeModel::modify_dhl_shenzhen2($countries_mode2,$key,2);
				}
			}
		}
		


		
	}
	function act_modify_globalmail_shenzhen(){
	    $channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
	    
		if($_FILES['upfile']){
			$filename = date("Y-m-d H:i:s").rand(1,3009).".xls";
			if (move_uploaded_file($_FILES["upfile"]["tmp_name"],"../xls/".$filename)){
				$filepath = "../xls/" . $filename;
			}else{
				self::$errCode = 444;
				self::$errMsg = "文件上传失败！";
				return ;
			}
		}
		$PHPExcel = new PHPExcel();
		$PHPReader = new PHPExcel_Reader_Excel2007();
		
		if(!$PHPReader->canRead($filepath)){
			
			$PHPReader = new PHPExcel_Reader_Excel5();
			if(!$PHPReader->canRead($filepath)){
				echo "no execl";
				return false;
			}
		}
		$PHPExcel=$PHPReader->load($filepath);
		
		$excellists = self::excel2array($PHPExcel,$filepath,0,0);
		

		foreach($excellists as $k => $v){
            foreach($v as $key => $value){
				$data[$key][$k-1] = $value;
			}
		}
		
		foreach($data as $k => $v){
			$freight = array();
		    $fuel = array();
			foreach($v as $key =>$value){
				if($k>0&&$key>0){
					$value_arr = explode("_",$value);
					$freight[] = $data[0][$key].":".$value_arr[0];
					$fuel[] = $data[0][$key].":".$value_arr[1];
					
					
				}
			}	
			if($k>0){
			    
				$freight_str = implode(",",$freight);
				$fuel_str = implode(",",$fuel);
				
				shipfeeModel::modify_golbalmail_shenzhen($v[0],$freight_str,$fuel_str);
                
			}
		}
		header("Location:index.php?mod=shipfee&act=globalmail_shenzhen&channelName={$channelName}");
	}
	function modify_fedex_shenzhen(){
		$type = isset($_POST['type'])?$_POST['type']:"";
		$channelName = isset($_GET['channelName'])?$_GET['channelName']:"";
		
		$fuel = isset($_POST['fuel'])?$_POST['fuel']:"";
		
		if($_FILES['upfile']){
			$filename = date("Y-m-d H:i:s").rand(1,3009).".xls";
			if (move_uploaded_file($_FILES["upfile"]["tmp_name"],"../xls/".$filename)){
				$filepath = "../xls/" . $filename;
			}else{
				self::$errCode = 444;
				self::$errMsg = "文件上传失败！";
				return;
			}
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			
			if(!$PHPReader->canRead($filepath)){
				
				$PHPReader = new PHPExcel_Reader_Excel5();
				if(!$PHPReader->canRead($filepath)){
					self::$errCode = 344;
				    self::$errMsg = "no excel!";
					return false;
				}
			}
			$PHPExcel=$PHPReader->load($filepath);
			
			$excellists = self::excel2array($PHPExcel,$filepath,0,0);
			foreach($excellists as $key => $value){
				foreach($value as $k => $v){
					if($key>1&&$k>0){
						$country = $excellists[$key][0];
						$weight = $excellists[1][$k];
						$fee = $v;
						shipfeeModel::modify_fedex_shenzhen($type,$fuel,$country,$weight,$fee);
					}
				}
			}
		}else{
			self::$errCode = 304;
			self::$errMsg = "no files";
			return false;
		}
	}
	
	function excel2array($PHPExcel, $filename,$sheet=0, $rownums=0){
		$Worksheet = $PHPExcel->getSheet($sheet);
		$highestRow = $Worksheet->getHighestRow();
		$highestColumn = $Worksheet->getHighestColumn();
		$highestColumnIndex = empty($rownums) ? PHPExcel_Cell::columnIndexFromString($highestColumn) : $rownums;
		$excelData = array();
		for ($row=1; $row<=$highestRow; $row++) {
			for ($col = 0; $col < $highestColumnIndex; $col++) {
				if ($highestColumnIndex>100) break;
				$value = $Worksheet->getCellByColumnAndRow($col, $row)->getValue();
				$value = trim($value);
				/*if (preg_match("/^[0-9]+\.[0-9]+$/", $value)){
					$value = empty($num) ? $value : round_num($value, $num);
				}*/
				$excelData[$row][] = $value;
			}
		}
		return $excelData;
    }
}
?>