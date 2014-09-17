<?php
/**
 * 类名：TrackNumberAct
 * 功能：跟踪号管理动作处理层
 * 版本：1.0
 * 日期：2014/06/05
 * 作者：管拥军
 */
  
class TrackNumberAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * TrackNumberAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data			= array();
		$trackNumber	= new TrackNumberModel();
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$carrierId		= isset($_GET['carrierId']) ? abs(intval($_GET['carrierId'])) : 0;
		$channelId		= isset($_GET['channelId']) ? abs(intval($_GET['channelId'])) : 0;
		$country		= isset($_GET['country']) ? post_check($_GET['country']) : '';
		$selectId		= isset($_GET['selectId']) ? abs(intval($_GET['selectId'])) : -1;
		$condition		= "1";
		if($type && $key) {
			if(!in_array($type,array('trackNumber','orderId'))) redirect_to("index.php?mod=trackNumber&act=index");
			$condition	.= ' AND a.'.$type." = '".$key."'";
		}
		if(!empty($carrierId)) {
			$condition	.= " AND a.carrierId = '{$carrierId}'";
		}
		if(!empty($channelId)) {
			$condition	.= " AND a.channelId = '{$channelId}'";
		}
		if(!empty($country)) {
			$condition	.= " AND a.countrys = '{$country}'";
		}
		if(empty($selectId)) {
			$condition	.= " AND a.orderId = '{$selectId}'";
		}
		if($selectId == 1) {
			$condition	.= " AND a.orderId > 0";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20; //每页显示的个数
		$total			= $trackNumber->modListCount($condition);
		$res			= $trackNumber->modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if($res) {
			if($total>$pagenum) {
				$pageStr 	= $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr 	= $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 	= '暂无数据';
		}
		//封装数据返回
		$data['key']	 	= $key;
		$data['type']	 	= $type;
		$data['lists']	 	= $res;
		$data['pages']	 	= $pageStr;
		$data['countrys']	= TransOpenApiModel::getCountriesStandard();
		$data['carriers']	= TransOpenApiModel::getCarrier(2);
		$data['carrierId']	= $carrierId;
		$data['selectId']	= $selectId;
		$data['country']	= $country;
		self::$errCode   	= TrackNumberModel::$errCode;
        self::$errMsg    	= TrackNumberModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
			
	/**
	 * TrackNumberAct::actBatchTrackNumberImport()
	 * 批量导入跟踪号信息
	 * @return array 
	 */
	public function actBatchTrackNumberImport(){
		$data				= array();
		$carrierId			= isset($_POST['carrierId']) ? abs(intval($_POST['carrierId'])) : 0;
		$channelId			= isset($_POST['nodeListItem']) ? abs(intval($_POST['nodeListItem'])) : 0;
		$country			= isset($_POST['country']) ? post_check($_POST['country']) : "";
		$data['countrys']	= TransOpenApiModel::getCountriesStandard();
		$data['lists']		= TransOpenApiModel::getCarrier(2);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
		if(empty($carrierId)) {
			show_message($this->smarty,"运输方式参数非法","");	
			return false;
		}	
		$uid 				= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if(isset($_FILES['upfile']) && !empty($_FILES['upfile'])){
			$fielName 		= $uid."_track_number_".date('YmdHis').'_'.rand(1,3009).".xls";
			$fileName 		= WEB_PATH.'html/temp/'.$fielName;
			if(move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				$filePath 	= $fileName;
			}
		}
		if(substr($filePath,-3) != 'xls') {
			show_message($this->smarty,"导入的文件名格式错误！","index.php?mod=trackNumber&act=trackNumberImport");
			return false;
		}
		
		//读取导入文件
		require_once WEB_PATH."lib/PHPExcel.php";
		$PHPExcel		= new PHPExcel(); 
		$PHPReader		= new PHPExcel_Reader_Excel2007();    
		if(!$PHPReader->canRead($filePath)) {      
			$PHPReader	= new PHPExcel_Reader_Excel5(); 
			if(!$PHPReader->canRead($filePath)) {
				show_message($this->smarty,"文件内容无法读取！","index.php?mod=trackNumber&act=trackNumberImport");
				@unlink($filePath);
				return false;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$currentSheet 	= $PHPExcel->getSheet(0);
		$row			= 1;
		while(1) {
			$flag		= true;
			$aa			= 'A'.$row;
			$tracknum	= trim($currentSheet->getCell($aa)->getValue());
			$tracknum	= mysql_real_escape_string(substr($tracknum,0,30));
			if(empty($tracknum)) break;
			if($row == 1) {
				if($tracknum != '跟踪号') {
					echo '<font color="red">文件导入失败，导入模版内容有误,请勿修改表头</font>';
					@unlink($filePath);
					break;
				}			
			} else {
				if(!(preg_match("/^[A-Z0-9]{1,30}$/",$tracknum))) {
					echo '<font color="red">跟踪号内容: '.$tracknum.' 格式错误,跟踪号内容只能由大写字母和数字组成且长度不超过30,请修正后再次导入！</font>';
					break;
				}		
				$res		= 0;
				$where		= "carrierId = '{$carrierId}' AND trackNumber = '{$tracknum}'"; 
				$res		= TrackNumberModel::modListCount($where);
				if($res > 0) {
					self::$errMsg	.= "添加失败：{$carrierId}---{$tracknum}---跟踪信息已存在！<br/>";
					$flag	= false;
				}
				$datas		= array (
								"carrierId"		=> $carrierId,	
								"channelId"		=> $channelId,	
								"trackNumber"	=> $tracknum,	
								"countrys"		=> $country,	
								"addTime"		=> time(),	
								"add_user_id"	=> $uid,	
							);
				if($flag) {
					$res	= TrackNumberModel::addTrackNumber($datas);
					if(!$res) {
						self::$errMsg	.= "添加失败：".TrackNumberModel::$errMsg."<br/>";
						break;
					}
				}
			}		
			$row++;
		}
		$data['res']		= self::$errMsg;
		return $data;
    }
	
	/**
	 * TrackNumberAct::actAdd()
	 * 添加跟踪号信息
	 * @return array 
	 */
	public function actAdd(){
		$data				= array();
		$data['countrys']	= TransOpenApiModel::getCountriesStandard();
		$data['lists']		= TransOpenApiModel::getCarrier(2);
		self::$errCode  	= TransOpenApiModel::$errCode;
        self::$errMsg   	= TransOpenApiModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackNumberAct::actModify()
	 * 返回某个跟踪号的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data				= array();
		$id					= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			return false;
		}
		$data['id']			= $id;
		$data['countrys']	= TransOpenApiModel::getCountriesStandard();
		$data['lists']		= TransOpenApiModel::getCarrier(2);
		$data['res']		= TrackNumberModel::modModify($id);
		self::$errCode  	= TrackNumberModel::$errCode;
        self::$errMsg   	= TrackNumberModel::$errMsg;
		if(self::$errCode != 0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
		$data['chList']		= TransOpenApiModel::getCarrierChannel($data['res']['carrierId']);
		return $data;
    }	
	
	/**
	 * TrackNumberAct::act_addTrackNumber()
	 * 添加跟踪号
	 * @param string $trackNumber 跟踪号
	 * @param string $country 跟踪号所属国家（如果有）
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_addTrackNumber(){
        $trackNumber	= isset($_POST["trackNumber"]) ? post_check($_POST["trackNumber"]) : "";
        $country		= isset($_POST["country"]) ? post_check($_POST["country"]) : "";
        $carrierId		= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
        $channelId		= isset($_POST["channelId"]) ? abs(intval(trim($_POST["channelId"]))) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($carrierId)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if(empty($trackNumber)) {
			self::$errCode  = 10001;
			self::$errMsg   = "跟踪号有误！";
			return false;
		}
		if(!(preg_match("/^[A-Z0-9]{1,30}$/",$trackNumber))) {
			self::$errCode  = 10003;
			self::$errMsg   = "跟踪号内容:{$trackNumber}格式错误,跟踪号内容只能由大写字母和数字组成且长度不超过30,请修改过来!";
			return false;
		}		
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"trackNumber"	=> $trackNumber,
			"countrys"		=> $country,
			"carrierId"		=> $carrierId,
			"channelId"		=> $channelId,
			"addTime"		=> time(),
			"add_user_id"	=> $uid,
		);
        $res				= TrackNumberModel::addTrackNumber($data);
		self::$errCode  	= TrackNumberModel::$errCode;
        self::$errMsg   	= TrackNumberModel::$errMsg;
		return $res;
    }

	/**
	 * TrackNumberAct::act_updateTrackNumber()
	 * 修改跟踪号
	 * @param string $trackNumber 跟踪号
	 * @param string $country 跟踪号所属国家（如果有）
	 * @param string $carrierId 运输方式ID
	 * @return  bool
	 */
	public function act_updateTrackNumber(){
		$id				= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
        $trackNumber	= isset($_POST["trackNumber"]) ? post_check($_POST["trackNumber"]) : "";
        $country		= isset($_POST["country"]) ? post_check($_POST["country"]) : "";
        $carrierId		= isset($_POST["carrierId"]) ? abs(intval(trim($_POST["carrierId"]))) : 0;
        $channelId		= isset($_POST["channelId"]) ? abs(intval(trim($_POST["channelId"]))) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
		if(empty($carrierId)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if(empty($trackNumber)) {
			self::$errCode  = 10002;
			self::$errMsg   = "跟踪号有误！";
			return false;
		}
		if(!(preg_match("/^[A-Z0-9]{1,30}$/",$trackNumber))) {
			self::$errCode  = 10003;
			self::$errMsg   = "跟踪号内容:{$trackNumber}格式错误,跟踪号内容只能由大写字母和数字组成且长度不超过30,请修改过来!";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"trackNumber"	=> $trackNumber,
			"countrys"		=> $country,
			"carrierId"		=> $carrierId,
			"channelId"		=> $channelId,
			"editTime"		=> time(),
			"edit_user_id"	=> $uid,
		);
        $res				= TrackNumberModel::updateTrackNumber($id, $data);
		self::$errCode  	= TrackNumberModel::$errCode;
        self::$errMsg  		= TrackNumberModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackNumberAct::act_delTrackNumber()
	 * 删除跟踪号
	 * @param int $id 跟踪号ID
	 * @return  bool
	 */
	public function act_delTrackNumber(){
		$id			= isset($_POST["id"]) ? abs(intval(trim($_POST["id"]))) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "ID有误！";
			return false;
		}
        $res			= TrackNumberModel::delTrackNumber($id);
		self::$errCode  = TrackNumberModel::$errCode;
        self::$errMsg   = TrackNumberModel::$errMsg;
		return $res;
    }
}
?>