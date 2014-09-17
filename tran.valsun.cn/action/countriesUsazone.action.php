<?php
/**
 * 类名：CountriesUsazoneAct
 * 功能：美国邮政分区管理动作处理层
 * 版本：1.2
 * 日期：2013/12/16
 * 作者：管拥军
 */
  
class CountriesUsazoneAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * CountriesUsazoneAct::actIndex()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actIndex(){
		$data				= array();
		$condition			= '1';
		$countriesUsazone	= new CountriesUsazoneModel();
		//接收参数生成条件
		$curpage			= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type				= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key				= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		if ($type && $key) {
			if (!in_array($type,array('zone','zip_code'))) redirect_to("index.php?mod=countriesUsazone&act=index");
			if ($type=='zone') $condition	.= ' AND '.$type." = '".$key."'";
			if ($type=='zip_code') $condition	.= ' AND '.$type." like '%".$key."%'";
		}
		//获取符合条件的数据并分页
		$pagenum			= 20;
		$total				= $countriesUsazone->modListCount($condition);
		$res				= $countriesUsazone->modList($condition, $curpage, $pagenum);
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
		self::$errCode   	= CountriesUsazoneModel::$errCode;
        self::$errMsg    	= CountriesUsazoneModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }	
	
	/**
	 * CountriesUsazoneAct::actAdd()
	 * 添加美国邮政分区信息页面
	 * @return array 
	 */
	public function actAdd(){
		$data			= array();
		$data['lists']	= TransitCenterModel::modList(1,1,200);
		self::$errCode  = TransitCenterModel::$errCode;
        self::$errMsg   = TransitCenterModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }
	
	/**
	 * CountriesUsazoneAct::actModify()
	 * 返回某个美国邮政分区信息
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
		$data['lists']	= TransitCenterModel::modList(1,1,200);
		$data['res']	= CountriesUsazoneModel::modModify($id);
		if(empty($data['res'])) {
			show_message($this->smarty,"数据为空，请返回确认条件!","");	
			return false;
		}
		self::$errCode  = CountriesUsazoneModel::$errCode;
        self::$errMsg   = CountriesUsazoneModel::$errMsg;
		if(self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * CountriesUsazoneAct::act_addCountriesUsazone()
	 * 添加美国邮政分区
	 * @param string $ow_zip_code 邮编
	 * @param string $ow_zone 分区
	 * @param int $transitId 转运中心ID
	 * @return  bool
	 */
	public function act_addCountriesUsazone(){
        $ow_zip_code		= isset($_POST["ow_zip_code"]) ? post_check($_POST["ow_zip_code"]) : "";
        $ow_zone			= isset($_POST["ow_zone"]) ? post_check($_POST["ow_zone"]) : "";
        $transitId			= isset($_POST["transitId"]) ? intval($_POST["transitId"]) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(empty($ow_zip_code) || empty($ow_zip_code)) {
			self::$errCode  = 10000;
			self::$errMsg   = "美国邮政分区邮编或分区有误！";
			return false;
		}
		if(empty($transitId)) {
			self::$errCode  = 10002;
			self::$errMsg   = "转运中心ID有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"zip_code"		=> $ow_zip_code,
								"zone"			=> $ow_zone,
								"transitId"		=> $transitId,
								"add_time"		=> time(),
								"add_userid"	=> $uid,
							);
        $res				= CountriesUsazoneModel::addCountriesUsazone($data);
		self::$errCode 	 	= CountriesUsazoneModel::$errCode;
        self::$errMsg  		= CountriesUsazoneModel::$errMsg;
		return $res;
    }

	/**
	 * CountriesUsazoneAct::act_updateCountriesUsazone()
	 * 修改美国邮政分区
	 * @param string $ow_zip_code 邮编
	 * @param string $ow_zone 分区
	 * @param int $transitId 转运中心ID
	 * @return  bool
	 */
	public function act_updateCountriesUsazone(){
		$id					= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$ow_zip_code		= isset($_POST["ow_zip_code"]) ? post_check($_POST["ow_zip_code"]) : "";
        $ow_zone			= isset($_POST["ow_zone"]) ? post_check($_POST["ow_zone"]) : "";
        $transitId			= isset($_POST["transitId"]) ? intval($_POST["transitId"]) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10002;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "美国邮政分区ID有误！";
			return false;
		}
		if(empty($ow_zip_code) || empty($ow_zip_code)) {
			self::$errCode  = 10001;
			self::$errMsg   = "美国邮政分区邮编或分区有误！";
			return false;
		}
		if(empty($transitId)) {
			self::$errCode  = 10003;
			self::$errMsg   = "转运中心ID有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  				= array(
								"zip_code"		=> $ow_zip_code,
								"zone"			=> $ow_zone,
								"transitId"		=> $transitId,
								"modify_time"	=> time(),
								"modify_userid"	=> $uid,
							);
        $res				= CountriesUsazoneModel::updateCountriesUsazone($id, $data);
		self::$errCode 	 	= CountriesUsazoneModel::$errCode;
        self::$errMsg   	= CountriesUsazoneModel::$errMsg;
		return $res;
    }
	
	/**
	 * CountriesUsazoneAct::act_delCountriesUsazone()
	 * 删除美国邮政分区
	 * @param int $id 美国邮政分区ID
	 * @return  bool
	 */
	public function act_delCountriesUsazone(){
		$id					= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 10000;
			self::$errMsg   = "美国邮政分区ID有误！";
			return false;
		}
        $res				= CountriesUsazoneModel::delCountriesUsazone($id);
		self::$errCode  	= CountriesUsazoneModel::$errCode;
        self::$errMsg   	= CountriesUsazoneModel::$errMsg;
		return $res;
    }
}
?>