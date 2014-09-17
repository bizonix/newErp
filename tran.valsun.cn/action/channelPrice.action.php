<?php
/**
 * 类名：ChannelPriceAct
 * 功能：渠道运费动作处理层
 * 版本：1.0
 * 日期：2013/11/18
 * 作者：管拥军
 */
  
class ChannelPriceAct {
    public static $errCode	= 0;
	public static $errMsg	= "";
	//暂时仅支持以下渠道运费价目管理
	private static $chnameArr	= array('cpsf_fujian_quanzhou','cpsf_fujian_zhangpu','cpsf_shenzhen','cprg_fujian','cprg_fujian_zhangpu','cprg_fujian_quanzhou','cprg_shenzhen','hkpostsf_hk','hkpostrg_hk','ems_shenzhen','eub_shenzhen','eub_fujian','eub_jiete','dhl_shenzhen','fedex_shenzhen','globalmail_shenzhen','ups_calcfree','usps_calcfree','ups_us','ups_uk','ups_fr','ups_ger','sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen','ruston_packet_py','ruston_packet_gh','ruston_large_package','sg_dhl_gm_gh','sg_dhl_gm_py','zhengzhou_xb_py','zhengzhou_xb_gh','ruishi_xb_py','ruishi_xb_gh','bilishi_xb_py','bilishi_xb_gh','usps_first_class','ups_ground_commercia','sv_sure_post','aoyoubao_gh','aoyoubao_py');
	
	/**
	 * ChannelPriceAct::actList()
	 * 列出符合条件的数据并分页显示
	 * @param string $where 查询条件
	 * @param string $tab 运费价目表名
	 * @param integer $page 页码
	 * @param integer $pagenum 每页个数
	 * @return array 结果集数组
	 */
 	public function actList($tab, $where='1', $page=1, $pagenum=20){
		$res			= ChannelPriceModel::modList($tab, $where, $page, $pagenum);
		self::$errCode  = ChannelPriceModel::$errCode;
        self::$errMsg   = ChannelPriceModel::$errMsg;
        return $res;
    }

	/**
	 * ChannelPriceAct::actListCount()
	 * 返回某个条件结果统计的总数
	 * @param string $where 查询条件
	 * @param string $tab 运费价目表名
	 * @return integer 总数量 
	 */
	public function actListCount($tab, $where='1'){
		$res			= ChannelPriceModel::modListCount($tab, $where);
		self::$errCode  = ChannelPriceModel::$errCode;
        self::$errMsg   = ChannelPriceModel::$errMsg;
        return $res;
    }
	
	/**
	 * ChannelPriceAct::actModify()
	 * 返回某个运费价目的信息
	 * @param int $id 查询ID
	 * @param string $tab 运费价目表名
	 * @return array 
	 */
	public function actModify($tab, $id){
		$res			= ChannelPriceModel::modModify($tab, $id);
		self::$errCode  = ChannelPriceModel::$errCode;
        self::$errMsg   = ChannelPriceModel::$errMsg;
        return $res;
    }
	
	/**
	 * ChannelPriceAct::act_addChannelPrice()
	 * 添加运费价目
	 * @param string $chname 渠道别名
	 * @param string $pr_group 分组名称
	 * @param float $pr_kilo 每公斤单价
	 * @param float $pr_discount 折扣
	 * @param string $pr_handlefee 手续费
	 * @param string $pr_country 国家列表
	 * @return  bool
	 */
	public function act_addChannelPrice(){
		$data			= array();
        $chname			= isset($_POST["chname"]) ? $_POST["chname"] : "";
        $pr_group		= isset($_POST["pr_group"]) ? post_check($_POST["pr_group"]) : "";
        $pr_country		= isset($_POST["pr_country"]) ? post_check($_POST["pr_country"]) : "";
        if(in_array($chname,array('dhl_shenzhen','fedex_shenzhen','globalmail_shenzhen'))) {
			$pr_kilo	= isset($_POST["pr_kilo"]) ? trim($_POST["pr_kilo"]) : '';
        } else {
			$pr_kilo	= isset($_POST["pr_kilo"]) ? floatval($_POST["pr_kilo"]) : 0;
        }
        $pr_discount	= isset($_POST["pr_discount"]) ? floatval($_POST["pr_discount"]) : 0;
		if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','fedex_shenzhen','globalmail_shenzhen','ups_calcfree','usps_calcfree','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$pr_handlefee 	= isset($_POST["pr_handlefee"]) ? post_check($_POST["pr_handlefee"]) : '';
			$pr_kilo_next	= isset($_POST["pr_kilo_next"]) ? post_check($_POST["pr_kilo_next"]) : '';
		} else {
			$pr_handlefee 	= isset($_POST["pr_handlefee"]) ? floatval($_POST["pr_handlefee"]) : 0;
			$pr_kilo_next	= isset($_POST["pr_kilo_next"]) ? floatval($_POST["pr_kilo_next"]) : 0;
		}
		if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$weightarr	= explode("-",$pr_kilo_next);
			$minweight	= floatval($weightarr[0]);
			$maxweight	= floatval($weightarr[1]);
		}
		$pr_file		= isset($_POST["pr_file"]) ? floatval($_POST["pr_file"]) : 0;
		$pr_air			= isset($_POST["pr_air"]) ? floatval($_POST["pr_air"]) : 0;
		if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$pr_other	= isset($_POST["pr_other"]) ? post_check($_POST["pr_other"]) : "";
		} else {
			$pr_other	= isset($_POST["pr_other"]) ? floatval($_POST["pr_other"]) : 0;
		}
        if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py','usps_first_class','ups_ground_commercia','sv_sure_post','yto_shenzhen','sto_shenzhen','yundaex_shenzhen','zto_express','best_express','gto_express','jym_shenzhen'))) {
			$pr_isfile	= isset($_POST["pr_isfile"]) ? floatval($_POST["pr_isfile"]) : 0;
		} else {
			$pr_isfile	= isset($_POST["pr_isfile"]) ? intval($_POST["pr_isfile"]) : 0;
		}
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10010;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if(!in_array($chname, self::$chnameArr)) {
			self::$errCode  = 10000;
			self::$errMsg   = "渠道参数有误！";
			return false;
		}
		if(empty($pr_group) && !in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','fedex_shenzhen','globalmail_shenzhen'))) {
			self::$errCode  = 10001;
			self::$errMsg   = "分组名称参数有误！";
			return false;
		}
		if(empty($pr_country) && !in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','eub_shenzhen','ups_calcfree','usps_calcfree','sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			self::$errCode  = 10002;
			self::$errMsg   = "国家列表参数有误！";
			return false;
		}
		$res		= ChannelPriceModel::getField($chname);
		if($chname == 'ems_shenzhen') {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_kilo_next, $pr_file, $pr_isfile);
		} else if($chname == 'fedex_shenzhen') {
			$val	= array($pr_kilo, $pr_kilo_next, $pr_country, $pr_handlefee, $pr_discount);
		} else if($chname == 'globalmail_shenzhen') {
			$val	= array($pr_kilo, $pr_country, $pr_handlefee, $pr_file);
		} else if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'))) {
			$val	= array($pr_kilo, $minweight, $maxweight, $pr_discount, $pr_handlefee , $pr_isfile);
		} else if($chname == 'ups_calcfree') {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next);
		} else if($chname == 'usps_calcfree') {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next);
		} else if(in_array($chname,array('eub_shenzhen','eub_fujian','eub_jiete'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_file, $pr_kilo_next, $pr_isfile);
		} else if(in_array($chname,array('sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen'))) {
			$val	= array($pr_group, $pr_file, $pr_kilo, $pr_kilo_next, $pr_isfile, $pr_discount, $pr_handlefee);
		} else if(in_array($chname,array('hkpostsf_hk','hkpostrg_hk'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_kilo_next, $pr_file);
		} else if(in_array($chname,array('ruston_packet_py','ruston_packet_gh','ruston_large_package'))) {
			$val	= array($pr_group, $pr_country, $pr_kilo, $pr_kilo_next, $pr_file, $pr_discount, $pr_handlefee);
		} else if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py'))) {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next, $pr_country, $pr_discount, $pr_isfile, $pr_air, $pr_other);
		} else if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$val	= array($pr_group, $pr_kilo, $pr_kilo_next, $pr_handlefee, $pr_country, $pr_discount, $pr_other);
		} else if(in_array($chname,array('bilishi_xb_py','bilishi_xb_gh'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount);
		} else if(in_array($chname,array('usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_discount, $minweight, $maxweight, $pr_other, $pr_isfile, $pr_air, $pr_file);
		} else if(in_array($chname,array('aoyoubao_py','aoyoubao_gh'))) {
			$val	= array($pr_group, $pr_country, $pr_kilo, $pr_discount, $pr_handlefee);
		} else {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount);
		}
		$fields		= explode(",", $res['edit']);
		foreach ($fields as $key=>$v) {
			$data[$v]			= $val[$key];	
		}
		$uid					= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data['add_user_id']	= $uid;
		$data['addTime']		= time();
        $res					= ChannelPriceModel::addChannelPrice($chname, $data);
		self::$errCode  		= ChannelPriceModel::$errCode;
        self::$errMsg   		= ChannelPriceModel::$errMsg;
		if(empty(self::$errCode)) {
			$cacheFee			= TransOpenApiModel::updateCacheTableFee($chname, $data);
		}
		return $res;
    }

	/**
	 * ChannelPriceAct::act_updateChannelPrice()
	 * 修改运费价目
	 * @param int $id 运费价目ID
	 * @param string $chname 渠道别名
	 * @param string $pr_group 分组名称
	 * @param float $pr_kilo 每公斤单价
	 * @param float $pr_discount 折扣
	 * @param string $pr_handlefee 手续费
	 * @param string $pr_country 国家列表
	 * @return  bool
	 */
	public function act_updateChannelPrice(){
        $data			= array();
		$id				= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
        $chname			= isset($_POST["chname"]) ? $_POST["chname"] : "";
        $pr_group		= isset($_POST["pr_group"]) ? post_check($_POST["pr_group"]) : "";
        $pr_country		= isset($_POST["pr_country"]) ? post_check($_POST["pr_country"]) : "";
        if(in_array($chname,array('dhl_shenzhen','fedex_shenzhen','globalmail_shenzhen'))) {
			$pr_kilo	= isset($_POST["pr_kilo"]) ? trim($_POST["pr_kilo"]) : '';
        } else {
			$pr_kilo	= isset($_POST["pr_kilo"]) ? floatval($_POST["pr_kilo"]) : 0;
        }
		$pr_discount	= isset($_POST["pr_discount"]) ? floatval($_POST["pr_discount"]) : 0;
		if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','fedex_shenzhen','globalmail_shenzhen','ups_calcfree','usps_calcfree','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$pr_handlefee 	= isset($_POST["pr_handlefee"]) ? post_check($_POST["pr_handlefee"]) : '';
			$pr_kilo_next	= isset($_POST["pr_kilo_next"]) ? post_check($_POST["pr_kilo_next"]) : '';
		} else {
			$pr_handlefee 	= isset($_POST["pr_handlefee"]) ? floatval($_POST["pr_handlefee"]) : 0;
			$pr_kilo_next	= isset($_POST["pr_kilo_next"]) ? floatval($_POST["pr_kilo_next"]) : 0;
		}
		if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$weightarr	= explode("-",$pr_kilo_next);
			$minweight	= floatval($weightarr[0]);
			$maxweight	= floatval($weightarr[1]);
		}
		$pr_file		= isset($_POST["pr_file"]) ? floatval($_POST["pr_file"]) : 0;
		$pr_air			= isset($_POST["pr_air"]) ? floatval($_POST["pr_air"]) : 0;
		if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$pr_other	= isset($_POST["pr_other"]) ? post_check($_POST["pr_other"]) : "";
		} else {
			$pr_other	= isset($_POST["pr_other"]) ? floatval($_POST["pr_other"]) : 0;
		}
        if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py','usps_first_class','ups_ground_commercia','sv_sure_post','yto_shenzhen','sto_shenzhen','yundaex_shenzhen','zto_express','best_express','gto_express','jym_shenzhen'))) {
			$pr_isfile	= isset($_POST["pr_isfile"]) ? floatval($_POST["pr_isfile"]) : 0;
		} else {
			$pr_isfile	= isset($_POST["pr_isfile"]) ? intval($_POST["pr_isfile"]) : 0;
		}
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20005;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if(!in_array($chname, self::$chnameArr)) {
			self::$errCode  = 10000;
			self::$errMsg   = "渠道运费参数有误！";
			return false;
		}
		if(empty($pr_group) && !in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','fedex_shenzhen','globalmail_shenzhen'))) {
			self::$errCode  = 10001;
			self::$errMsg   = "分组名称参数有误！";
			return false;
		}
		if(empty($pr_country) && !in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger','eub_shenzhen','ups_calcfree','usps_calcfree','sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen','usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			self::$errCode  = 10002;
			self::$errMsg   = "国家列表参数有误！";
			return false;
		}
		$res		= ChannelPriceModel::getField($chname);
		if($chname == 'ems_shenzhen') {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_kilo_next, $pr_file, $pr_isfile);
		} else if($chname == 'fedex_shenzhen') {
			$val	= array($pr_kilo, $pr_kilo_next, $pr_country, $pr_handlefee, $pr_discount);
		} else if($chname == 'globalmail_shenzhen') {
			$val	= array($pr_kilo, $pr_country, $pr_handlefee, $pr_file);
		} else if(in_array($chname,array('ups_us','ups_uk','ups_fr','ups_ger'))) {
			$val	= array($pr_kilo, $minweight, $maxweight, $pr_discount, $pr_handlefee , $pr_isfile);
		} else if($chname == 'ups_calcfree') {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next);
		} else if($chname == 'usps_calcfree') {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next);
		} else if(in_array($chname,array('eub_shenzhen','eub_fujian','eub_jiete'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_file, $pr_kilo_next, $pr_isfile);
		} else if(in_array($chname,array('sto_shenzhen','zto_shenzhen','yto_shenzhen','yundaex_shenzhen','best_shenzhen','jym_shenzhen','gto_shenzhen'))) {
			$val	= array($pr_group, $pr_file, $pr_kilo, $pr_kilo_next, $pr_isfile, $pr_discount, $pr_handlefee);
		} else if(in_array($chname,array('hkpostsf_hk','hkpostrg_hk'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount, $pr_kilo_next, $pr_file);
		} else if(in_array($chname,array('ruston_packet_py','ruston_packet_gh','ruston_large_package'))) {
			$val	= array($pr_group, $pr_country, $pr_kilo, $pr_kilo_next, $pr_file, $pr_discount, $pr_handlefee);
		} else if(in_array($chname,array('sg_dhl_gm_gh','sg_dhl_gm_py'))) {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_kilo_next, $pr_country, $pr_discount, $pr_isfile, $pr_air, $pr_other);
		} else if(in_array($chname,array('ruishi_xb_py','ruishi_xb_gh'))) {
			$val	= array($pr_group, $pr_kilo, $pr_kilo_next, $pr_handlefee, $pr_country, $pr_discount, $pr_other);
		} else if(in_array($chname,array('bilishi_xb_py','bilishi_xb_gh'))) {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount);
		} else if(in_array($chname,array('usps_first_class','ups_ground_commercia','sv_sure_post'))) {
			$val	= array($pr_group, $pr_kilo, $pr_handlefee, $pr_discount, $minweight, $maxweight, $pr_other, $pr_isfile, $pr_air, $pr_file);
		} else if(in_array($chname,array('aoyoubao_py','aoyoubao_gh'))) {
			$val	= array($pr_group, $pr_country, $pr_kilo, $pr_discount, $pr_handlefee);
		} else {
			$val	= array($pr_group, $pr_kilo, $pr_country, $pr_handlefee, $pr_discount);
		}
		$fields		= explode(",", $res['edit']);
		foreach ($fields as $key=>$v) {
			$data[$v]			= $val[$key];
		}
		$uid					= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data['edit_user_id']	= $uid;
		$data['editTime']		= time();
        $res					= ChannelPriceModel::updateChannelPrice($id, $chname, $data);
		self::$errCode  		= ChannelPriceModel::$errCode;
        self::$errMsg   		= ChannelPriceModel::$errMsg;
		if(empty(self::$errCode)) {
			$cacheFee			= TransOpenApiModel::updateCacheTableFee($chname, $data);
		}
		return $res;
    }
	
	/**
	 * ChannelPriceAct::act_batchChannelPrice()
	 * 批量修改某个渠道运费价目
	 * @param string $chname 渠道别名
	 * @param string $selItem 字段名
	 * @param string $itemVal 值
	 * @return  bool
	 */
	public function act_batchChannelPrice(){
        $data				= array();
        $chname				= isset($_POST["chname"]) ? $_POST["chname"] : "";
        $selItem			= isset($_POST["selItem"]) ? post_check($_POST["selItem"]) : "";
        $itemVal			= isset($_POST["itemVal"]) ? post_check($_POST["itemVal"]) : "";
		if(empty($selItem)) {
			self::$errCode  = 10001;
			self::$errMsg   = "批量修改项目参数有误！";
			return false;
		}
		if(empty($itemVal) && $itemVal<>0) {
			self::$errCode  = 10002;
			self::$errMsg   = "批量修改项目值有误！";
			return false;
		}		
		$act				= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod				= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10003;
			self::$errMsg   = "对不起,您暂无价目表数据批量编辑权限！";
			return false;
		}
		$res				= ChannelPriceModel::getField($chname);
		$fields				= explode(",", $res['batch']);
        if(!in_array($selItem, $fields)) {
			self::$errCode  = 10004;
			self::$errMsg   = "对不起,此价目表不支持此属性批量修改！";
			return false;			
        }
		$val	= array($itemVal);
		foreach ($fields as $key=>$v) {
			$data[$v]			= $val[$key];
		}
		$uid					= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data['edit_user_id']	= $uid;
		$data['editTime']		= time();
        $res					= ChannelPriceModel::batchChannelPrice($chname, $data);
		self::$errCode  		= ChannelPriceModel::$errCode;
        self::$errMsg   		= ChannelPriceModel::$errMsg;
		if(empty(self::$errCode)) {
			$cacheFee			= TransOpenApiModel::updateCacheTableFee($chname, $data);
		}
		return $res;
    }
	
	/**
	 * ChannelPriceAct::act_delChannelPrice()
	 * 删除运费价目
	 * @param int $id 运费价目ID
	 * @return  bool
	 */
	public function act_delChannelPrice(){
		$id			= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
        $chname		= isset($_POST["chname"]) ? $_POST["chname"] : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30002;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if(empty($id) || !is_numeric($id)) {
			self::$errCode  = 30000;
			self::$errMsg   = "运费价目ID有误！";
			return false;
		}
		if(!in_array($chname, self::$chnameArr)) {
			self::$errCode  = 30001;
			self::$errMsg   = "渠道运费参数有误！";
			return false;
		}
        $res				= ChannelPriceModel::delChannelPrice($chname, $id);
		self::$errCode  	= ChannelPriceModel::$errCode;
        self::$errMsg   	= ChannelPriceModel::$errMsg;
		if(empty(self::$errCode)) {
			$cacheFee		= TransOpenApiModel::updateCacheTableFee($chname, $data);
		}
		return $res;
    }
}
?>