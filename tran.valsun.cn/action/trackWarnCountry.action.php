<?php
/**
 * 类名：TrackWarnCountryAct
 * 功能：目的地国家预警管理动作处理层
 * 版本：1.0
 * 日期：2014/05/23
 * 作者：管拥军
 */
  
class TrackWarnCountryAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	
	/**
	 * TrackWarnCountryAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data				= array();
		$condition			= '';
		$trackWarnCountry	= new TrackWarnCountryModel();
		//接收参数生成条件
		$curpage			= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type				= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key				= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition			.= "1";
		if($type && $key) {
			if(!in_array($type,array('countryName','trackName'))) redirect_to("index.php?mod=trackWarnCountry&act=index");
			$condition		.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum			= 20;
		$total				= $trackWarnCountry->modListCount($condition);
		$res				= $trackWarnCountry->modList($condition, $curpage, $pagenum);
		$page	 			= new Page($total, $pagenum, '', 'CN');
		$pageStr			= "";
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
		self::$errCode   	= TrackWarnCountryModel::$errCode;
        self::$errMsg    	= TrackWarnCountryModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * TrackWarnCountryAct::actAdd()
	 * 添加目的地国家预警信息页面
	 * @return array 
	 */
	public function actAdd(){
		$data			= array();
		$data['lists']	= TransOpenApiModel::getCarrier(2);
		$data['tracks']	= TransOpenApiModel::getTrackCarrierList();
		self::$errCode  = TransOpenApiModel::$errCode;
        self::$errMsg   = TransOpenApiModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }	
	
	/**
	 * TrackWarnCountryAct::actModify()
	 * 返回某个目的地国家预警的信息
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actModify(){
		$data			= array();
		$id				= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if(empty($id)) {
			show_message($this->smarty,"ID不能为空？","");	
			return false;
		}
		$data['id']		= $id;
		$data['lists']	= TransOpenApiModel::getCarrier(2);
		$data['tracks']	= TransOpenApiModel::getTrackCarrierList();
		$data['res']	= TrackWarnCountryModel::modModify($id);
		if(empty($data['res'])) {
			show_message($this->smarty,"数据为空，请返回确认条件!","");	
			return false;
		}
		self::$errCode  = TrackWarnCountryModel::$errCode;
        self::$errMsg   = TrackWarnCountryModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * TrackWarnCountryAct::act_addTrackWarnCountry()
	 * 添加目的地国家预警
	 * @param string $carrier_name 跟踪系统运输方式名
	 * @param string $ship_country 目的地国家名
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_addTrackWarnCountry(){
        $carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $ship_country	= isset($_POST["ship_country"]) ? post_check($_POST["ship_country"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : 0;
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10003;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($ship_id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式有误！";
			return false;
		}
		if (empty($ship_country)) {
			self::$errCode  = 10001;
			self::$errMsg   = "目的地国家名参数有误！";
			return false;
		}
		if (empty($carrier_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "跟踪运输方式名有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"trackName"		=> $carrier_name,
			"countryName"	=> $ship_country,
			"carrierId"		=> $ship_id,
			"addTime"		=> time(),
			"add_user_id"	=> $uid,
		);
        $res			= TrackWarnCountryModel::addTrackWarnCountry($data);
		self::$errCode  = TrackWarnCountryModel::$errCode;
        self::$errMsg   = TrackWarnCountryModel::$errMsg;
		return $res;
    }

	/**
	 * TrackWarnCountryAct::act_updateTrackWarnCountry()
	 * 修改目的地国家预警
	 * @param string $carrier_name 跟踪系统运输方式名
	 * @param string $ship_country 目的地国家名
	 * @param string $ship_id 运输方式ID
	 * @return  bool
	 */
	public function act_updateTrackWarnCountry(){
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$carrier_name	= isset($_POST["carrier_name"]) ? post_check($_POST["carrier_name"]) : "";
        $ship_country	= isset($_POST["ship_country"]) ? post_check($_POST["ship_country"]) : "";
        $ship_id		= isset($_POST["ship_id"]) ? post_check($_POST["ship_id"]) : "";
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10003;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "运输方式ID有误！";
			return false;
		}
		if (empty($ship_country)) {
			self::$errCode  = 10001;
			self::$errMsg   = "目的地国家名参数有误！";
			return false;
		}
		if (empty($carrier_name)) {
			self::$errCode  = 10002;
			self::$errMsg   = "跟踪运输方式名有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"trackName"		=> $carrier_name,
			"countryName"	=> $ship_country,
			"carrierId"		=> $ship_id,
			"editTime"		=> time(),
			"edit_user_id"	=> $uid,
		);
        $res				= TrackWarnCountryModel::updateTrackWarnCountry($id, $data);
		self::$errCode  	= TrackWarnCountryModel::$errCode;
        self::$errMsg   	= TrackWarnCountryModel::$errMsg;
		return $res;
    }
	
	/**
	 * TrackWarnCountryAct::act_delTrackWarnCountry()
	 * 删除目的地国家预警
	 * @param int $id 目的地国家预警ID
	 * @return  bool
	 */
	public function act_delTrackWarnCountry(){
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
			self::$errMsg   = "目的地国家预警ID有误！";
			return false;
		}
        $res			= TrackWarnCountryModel::delTrackWarnCountry($id);
		self::$errCode  = TrackWarnCountryModel::$errCode;
        self::$errMsg   = TrackWarnCountryModel::$errMsg;
		return $res;
    }
}
?>