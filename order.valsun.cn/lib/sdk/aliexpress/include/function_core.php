<?php 
function write_log($file, $data){
	
	global $truename;

	$config_path = '/home/web_logs';
	list($filepath, $filename) = explode ( '/', $file );
	$dirPath = $config_path.'/'.$filepath;
	if (!is_dir($dirPath)){
    	mkdir( $dirPath);
    }
    $readpath = $dirPath.'/'.$filename;
	if (!$handle=fopen($readpath, 'a')) {
         return false;
    }
    if(flock($handle, LOCK_EX)) { 
	    if (fwrite($handle, $truename.'===='.$data) === FALSE) {
	        return false;
	    }
	    flock($handle, LOCK_UN);
    }
    fclose($handle);
    return true;
}


function get_sku_imgName($name) {
	if(strpos($name,"*")) {
		$nameArr = explode("*",$name);
		$name = $nameArr[1];
	}
	$pattern1 = '/(^0)(\d+)/i';
	$pattern2 = '/(\w+)_(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern3 = '/(\w+)(_M|_S|_L|_XL|_XXL|_XXXL)$/i';
	$pattern4 = '/(\w+)_(\w+)(_\d+)$/i';
	if(preg_match($pattern1,$name)){
		$replacement1 = '${2}';
		$trueName = preg_replace($pattern1, $replacement1, $name);
	}else if(preg_match($pattern2,$name)){
		$replacement2 = '${1}_${2}';
		$trueName = preg_replace($pattern2, $replacement2, $name);
	}else if(preg_match($pattern3,$name)){
		$replacement3 = '${1}';
		$trueName = preg_replace($pattern3, $replacement3, $name);
	}else if(preg_match($pattern4,$name)){
		$replacement4 = '${1}_${2}';
		$trueName = preg_replace($pattern4, $replacement4, $name);
	}else {
		$trueName = $name;
	}

	$url_file = "http://192.168.200.200:9998/imgs/".$trueName."-G.jpg";

	$ch = curl_init(); 
	$timeout = 10; 
	curl_setopt ($ch, CURLOPT_URL, $url_file); 
	curl_setopt($ch, CURLOPT_HEADER, 1); 
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 

	$contents = curl_exec($ch);
	if (preg_match("/404/", $contents)){
		if(strpos($trueName,"_")) {
			$nameArr = explode("_",$trueName);
			$trueName = $nameArr[0];
		}
		return $trueName;
	}else{
		return $trueName;
	}

}

function mkdirs($path){
	$path_out=preg_replace('/[^\/.]+\/?$/','',$path);
	if(!is_dir($path_out)){			
		mkdirs($path_out);
	}
	mkdir($path);
}

function write_a_file($file, $data){
	$tmp_dir = dirname($file);
	if(!is_dir($tmp_dir)){
		mkdirs($tmp_dir);
	}
	if (!$handle=fopen($file, 'a')) {
		 return false;
	}
	if(flock($handle, LOCK_EX)) { 
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function write_w_file($file, $data){
	$tmp_dir = dirname($file);
	if(!is_dir($tmp_dir)){
		mkdirs($tmp_dir);
	}
	if (!$handle=fopen($file, 'w')) {
		 return false;
	}
	if(flock($handle, LOCK_EX)) { 
		if (fwrite($handle, $data) === FALSE) {
			return false;
		}
		flock($handle, LOCK_UN);
	}
	fclose($handle);
	return true;
}

function read_file($file){
	if(!is_file($file)){
		return false;
	}
	return file_get_contents($file);;
}

function read_and_empty_file($file){
	if(!is_file($file)){
		return false;
	}
	$contents =  file_get_contents($file);
	if (!$handle=fopen($file, 'w')) {
		 return false;
	}
	return $contents;
}

function round_num($f, $n){
	$num = pow(10, $n);
	$intn = intval(round($f*$num));
	$r = $intn/$num;
	$r = $r + 0.00001;
	return str_replace(',', '', number_format($r,2));
}

function excel2array($PHPExcel, $filename, $rownums=0, $num=2){
	$Worksheet = $PHPExcel->getActiveSheet();
	$highestRow = $Worksheet->getHighestRow();
	$highestColumn = $Worksheet->getHighestColumn();
	$highestColumnIndex = empty($rownums) ? PHPExcel_Cell::columnIndexFromString($highestColumn) : $rownums;
	$excelData = array();
	for ($row=1; $row<=$highestRow; $row++) {
		for ($col = 0; $col < $highestColumnIndex; $col++) {
			if ($highestColumnIndex>100) break;
			$value = $Worksheet->getCellByColumnAndRow($col, $row)->getValue();
			if (preg_match("/^[0-9]+\.[0-9]+$/", $value)){
				$value = empty($num) ? $value : round_num($value, $num);
			}
			$excelData[$row][] = $value;
		}
	}
	return $excelData;
}
function array2excel(&$PHPExcel, $data, $colnums){
	$Worksheet = $PHPExcel->getActiveSheet();
	foreach ($data AS $key=>$value){
		$Worksheet->setCellValueByColumnAndRow($key, $colnums, $value);
	}
	return true;
}
function array2sql($array){
	$sql_array = array();
	foreach ($array AS $_k=>$_v){
		if (empty($_k)){
			continue;
		}
		$_v = trim($_v);
		//if (is_numeric($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){
		if (ctype_digit($_v)&&preg_match("/^[1-9][0-9]+$/", $_v)){ //modified by Herman.Xi is_numeric 对十六进制数判断不了 举例：0X792496944666339
			$sql_array[] = "{$_k}={$_v}";
		}else{
			$_v = Deal_SC($_v);
			$sql_array[] = "{$_k}='{$_v}'";
		}
	}
	return implode(',', $sql_array);
}
function get_columns($table){
	
	global $dbcon;
	
	$sql = "SHOW COLUMNS FROM {$table}";
	$sql = $dbcon->execute($sql);
	$columns = $dbcon->getResultArray($sql);
	$fields = array(); 
	foreach ($columns AS $column){
		array_push($fields, $column['Field']);
	}
	return $fields;
}
function array2strarray($array){
	$results = array();
	foreach ($array AS $_k=>$_v){
		$results[$_k] = "'{$_v}'";
	}
	return $results;
}
function multi2single($key, $arrays){
	$results = array();
	foreach ($arrays AS $array){
		array_push($results, $array[$key]);
	}
	return $results;
}
function Deal_SC($str){
	//处理特殊字符,add by　Herman.Xi @ 20130307
	$str  = str_replace("'","&acute;",$str);
	$str  = str_replace("\"","&quot;",$str);
	$tes = array("=" , ")" , "(" , "{", "}");
	foreach($tes as $v){
		$str = str_replace($v,"",$str);
	}
	return addslashes($str);
}
?>
