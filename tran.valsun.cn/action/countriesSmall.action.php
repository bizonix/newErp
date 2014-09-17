<?php
/**
 * 类名：CountriesSmallAct
 * 功能：小语种国家列表管理动作处理层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
  
class CountriesSmallAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CountriesSmallAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($where='1', $page=1, $pagenum=20){
		$res			= CountriesSmallModel::modList($where, $page, $pagenum);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
        return $res;
    }

	/**
	 * CountriesSmallAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @return integer 总数量 
	 */
	public function actListCount($where='1'){
		$res			= CountriesSmallModel::modListCount($where);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesSmallAct::actModify()
	 * 返回某个小语种国家的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify($id){
		$res			= CountriesSmallModel::modModify($id);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
        return $res;
    }
	
	/**
	 * CountriesSmallAct::actBatchCountriesSmallImport()
	 * 批量导入小语种国家信息
	 * @return array 
	 */
	public function actBatchCountriesSmallImport(){
		$data				= array();
		$uid 				= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if(isset($_FILES['upfile']) && !empty($_FILES['upfile'])){
			$fielName 		= $uid."_small_country_".date('YmdHis').'_'.rand(1,3009).".xls";
			$fileName 		= WEB_PATH.'html/temp/'.$fielName;
			if(move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				$filePath 	= $fileName;
			}
		}
		if(substr($filePath,-3) != 'xls') {
			show_message($this->smarty,"导入的文件名格式错误！","index.php?mod=countriesSmall&act=countriesSmallImport");
			@unlink($filePath);
			exit;
		}
		
		//读取导入文件
		require_once WEB_PATH."lib/PHPExcel.php";
		$PHPExcel		= new PHPExcel(); 
		$PHPReader		= new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)) {      
			$PHPReader	= new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)) {
				show_message($this->smarty,"文件内容无法读取！","index.php?mod=countriesSmall&act=countriesSmallImport");
				@unlink($filePath);
				exit;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$currentSheet 	= $PHPExcel->getSheet(0);
		//取得共有多少列,若不使用此静态方法，获得的$col是文件列的最大的英文大写字母  
		// $cols			=PHPExcel_Cell::columnIndexFromString($currentSheet->getHighestColumn());  
		// $rows			=$currentSheet->getHighestRow();
		$row			= 1;
		while(1) {
			$flag		= true;
			$rowFlag	= true;
			$country	= '';
			$small1		= '';
			$small2		= '';
			$small3		= '';
			$small4		= '';
			$small5		= '';
			$aa			= 'A'.$row;
			$bb			= 'B'.$row;
			$cc			= 'C'.$row;
			$dd			= 'D'.$row;
			$ee			= 'E'.$row;
			$ff			= 'F'.$row;
			$country	= post_check(trim($currentSheet->getCell($aa)->getValue()));
			$small1		= post_check(trim($currentSheet->getCell($bb)->getValue()));
			$small2		= post_check(trim($currentSheet->getCell($cc)->getValue()));
			$small3		= post_check(trim($currentSheet->getCell($dd)->getValue()));
			$small4		= post_check(trim($currentSheet->getCell($ee)->getValue()));
			$small5		= post_check(trim($currentSheet->getCell($ff)->getValue()));
			if(empty($country)) break;
			if($row == 1) {
				if($country != '标准英文国家' || $small1 != '小语种名称1' || $small2 != '小语种名称2' || $small3 != '小语种名称3' || $small4 != '小语种名称4' || $small5 != '小语种名称5') {
					echo '<font color="red">文件导入失败，导入模版内容有误,请勿修改表头</font>';
					@unlink($filePath);
					break;
				}			
			} else {
				$smArr				= array();
				$res 				= 0;
				$res 				= CountriesStandardModel::modListCount("1 AND countryNameEn = '{$country}'");
				if(empty($res)) {
					self::$errMsg	.= "添加失败：{$country}信息在标准国家中不存在！<br/>";
					$rowFlag 		= false;
				} else {
					$smArr []		= $small1;
					$smArr []		= $small2;
					$smArr []		= $small3;
					$smArr []		= $small4;
					$smArr []		= $small5;
				}
				foreach($smArr as $sCountry) {
					if($rowFlag) {
						if(empty($sCountry)) continue;
						$res		= 0;
						$where		= "1 AND countryName = '{$country}' AND small_country = '{$sCountry}'"; 
						$res		= CountriesSmallModel::modListCount($where);
						if($res > 0) {
							self::$errMsg	.= "添加失败：标准英文国家{$country}---{$sCountry}---小语种信息已存在！<br/>";
							$flag	= false;
						}
						$data  		= array(
										"small_country"		=> $sCountry,
										"countryName"		=> $country,
										"conversionType"	=> 1,
										"createdTime"		=> time(),
									);
						if($flag) {
							$res				= CountriesSmallModel::addCountriesSmall($data);
							if(!$res) {
								self::$errMsg	.= "添加失败：".CountriesSmallModel::$errMsg."<br/>";
								continue;
							}
						}
					}
				}
			}	
			$row++;
		}
		$data['res']		= self::$errMsg;
		return $data;
    }

	/**
	 * CountriesSmallAct::act_addCountriesSmall()
	 * 添加小语种国家
	 * @param string $small_name 小语种名称
	 * @param string $en_name 标准国家英文名称
	 * @param string $code_name 转换码
	 * @return  bool
	 */
	public function act_addCountriesSmall(){
        $small_name	= isset($_POST["small_name"]) ? post_check($_POST["small_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $code_name	= isset($_POST["code_name"]) ? post_check($_POST["code_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($small_name) || empty($small_name)) {
			self::$errCode  = 10000;
			self::$errMsg   = "小语种国家中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"small_country"	=> $small_name,
			"countryName"	=> $en_name,
			"conversionType"=> $code_name,
			"createdTime"	=> time(),
		);
        $res			= CountriesSmallModel::addCountriesSmall($data);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
		return $res;
    }

	/**
	 * CountriesSmallAct::act_updateCountriesSmall()
	 * 修改小语种国家
	 * @param string $small_name 小语种名称
	 * @param string $en_name 标准国家英文名称
	 * @param string $code_name 转换码
	 * @return  bool
	 */
	public function act_updateCountriesSmall(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$small_name	= isset($_POST["small_name"]) ? post_check($_POST["small_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $code_name	= isset($_POST["code_name"]) ? post_check($_POST["code_name"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据修改权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "小语种国家ID有误！";
			return false;
		}
		if (empty($small_name) || empty($small_name)) {
			self::$errCode  = 10001;
			self::$errMsg   = "小语种国家中文名称或英文名称有误！";
			return false;
		}
		$data  = array(
			"small_country"	=> $small_name,
			"countryName"	=> $en_name,
			"conversionType"		=> $code_name,
		);
        $res			= CountriesSmallModel::updateCountriesSmall($id, $data);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
		return $res;
    }
	
	/**
	 * CountriesSmallAct::act_delCountriesSmall()
	 * 删除小语种国家
	 * @param int $id 小语种国家ID
	 * @return  bool
	 */
	public function act_delCountriesSmall(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "小语种国家ID有误！".$id;
			return false;
		}
        $res			= CountriesSmallModel::delCountriesSmall($id);
		self::$errCode  = CountriesSmallModel::$errCode;
        self::$errMsg   = CountriesSmallModel::$errMsg;
		return $res;
    }
}
?>