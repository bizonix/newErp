<?php
/**
 * 类名：CarrierManageView
 * 功能：运输方式管理视图层
 * 版本：1.0
 * 日期：2013/10/21
 * 作者：管拥军
 */
class CarrierManageView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$carrierManage	= new CarrierManageAct();
        $this->smarty->assign('title','运输方式管理');
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		= "1";
		if ($type && $key) {
			if (!in_array($type,array('carrierNameEn','carrierNameCn'))) redirect_to("index.php?mod=carrierManage&act=index");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;//每页显示的个数
		$res			= $carrierManage->actList($condition, $curpage, $pagenum);
		$total			= $carrierManage->actListCount($condition);//页面总数量
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr	= $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr	= $page->fpage(array(0,1,2,3));
			}
		}else{
			$pageStr = '暂无数据';
		}
		//替换页面内容变量
        $this->smarty->assign('key',$key);//关键词 
        $this->smarty->assign('type',$type);//循环赋值 
        $this->smarty->assign('lists',$res);//循环赋值   
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('carrierManage.htm');
	}
	
	//添加页面渲染
	public function view_add(){
        $addrlist 		= TransOpenApiModel::getShipAddress();//发货地列表
        $this->smarty->assign('addrlist',$addrlist);
        $platFormlist 	= TransOpenApiModel::getPlatForm("ALL");//平台列表
        $this->smarty->assign('platFormlist',$platFormlist);
	    $this->smarty->assign('title','添加运输方式');
		$this->smarty->display('carrierManageAdd.htm');		
	}
	
	//修改页面渲染
	public function view_modify(){
	    $this->smarty->assign('title','修改运输方式');
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id) || !is_numeric($id)) {
			redirect_to("index.php?mod=carrierManage&act=index");
			exit;
		}
		$carrierManage	= new CarrierManageAct();
		$res			= $carrierManage->actModify($id);
	    $this->smarty->assign('cn_name',$res['carrierNameCn']);   
	    $this->smarty->assign('en_name',$res['carrierNameEn']);   
	    $this->smarty->assign('ship_ali',$res['carrierAli']);   
	    $this->smarty->assign('ship_abb',$res['carrierAbb']);   
	    $this->smarty->assign('ship_index',$res['carrierIndex']);   
	    $this->smarty->assign('ship_logo',$res['carrierLogo']);   
	    $this->smarty->assign('ship_type',$res['type']);   
	    $this->smarty->assign('min_weight',$res['weightMin']);   
	    $this->smarty->assign('max_weight',$res['weightMax']);   
	    $this->smarty->assign('ship_day',$res['timecount']);   
	    $this->smarty->assign('ship_note',$res['note']);   
	    $this->smarty->assign('is_track',$res['is_track']);   
	    $this->smarty->assign('id',$res['id']);
        $platFormlist 	= TransOpenApiModel::getPlatForm("ALL");//平台列表
        $this->smarty->assign('platFormlist',$platFormlist);
        $res 			= CarrierManageModel::listCarrierPlatForm($id);//运输方式对应平台列表
		$platList		= array();
		foreach ($res as $v) {
			array_push($platList,$v['platformId']);
		}
		$this->smarty->assign('platList',$platList);
        $addrlist 		= TransOpenApiModel::getShipAddress();//发货地列表
        $this->smarty->assign('addrlist',$addrlist);
		$res			= ShippingAddressModel::getAddByCarrierId($id);
		$ship_add		= isset($res['id']) ? $res['id'] : 0;
	    $this->smarty->assign('ship_add',$ship_add);
		$this->smarty->display('carrierManageModify.htm');		
	}

	//insert页面渲染
	public function view_insert(){
		$errMsg		= "";
		$res		= 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $ship_ali	= isset($_POST["ship_ali"]) ? post_check($_POST["ship_ali"]) : "";
        $ship_abb	= isset($_POST["ship_abb"]) ? post_check($_POST["ship_abb"]) : "";
        $ship_logo	= isset($_POST["ship_logo"]) ? post_check($_POST["ship_logo"]) : "";
        $ship_index	= isset($_POST["ship_index"]) ? post_check($_POST["ship_index"]) : "";
		$plat_arr	= isset($_POST["plat_name"]) ? $_POST["plat_name"] : "";
		$ship_add	= isset($_POST["ship_add"]) ? post_check($_POST["ship_add"]) : 0;
		$ship_type	= isset($_POST["ship_type"]) ? post_check($_POST["ship_type"]) : "";
		$min_weight	= isset($_POST["min_weight"]) ? post_check($_POST["min_weight"]) : 0;
		$max_weight	= isset($_POST["max_weight"]) ? post_check($_POST["max_weight"]) : 0;
		$ship_day	= isset($_POST["ship_day"]) ? post_check($_POST["ship_day"]) : 0;
		$ship_note	= isset($_POST["ship_note"]) ? post_check($_POST["ship_note"]) : "";
		$is_track	= isset($_POST["is_track"]) ? post_check($_POST["is_track"]) : 1;
		if (empty($cn_name)) $errMsg   .= "运输方式中文名称有误！<br/>";
		if (empty($en_name)) $errMsg   .= "运输方式英文名称有误！<br/>";
		if (empty($ship_ali)) $errMsg   .= "运输方式简称有误！<br/>";
		if (empty($ship_abb) || !(preg_match("/^[A-Z]{5,5}$/",$ship_abb))) $errMsg   .= "运输方式简码有误！<br/>";
		if (empty($ship_index) || !(preg_match("/^[A-Z]{1,1}$/",$ship_index))) $errMsg   .= "运输方式字母索引有误！<br/>";
		if (!count($plat_arr)) $errMsg .= "所属平台有误！<br/>";
		if (empty($ship_add)) $errMsg  .= "发货地址有误！<br/>";
		if (!in_array($ship_type,array("0","1"))) $errMsg   .= "物流类型有误！<br/>";
		$where	= "(carrierNameEn = '{$en_name}' OR carrierNameCn = '{$cn_name}' OR carrierAbb = '{$ship_abb}')"; 
        $res	= CarrierManageModel::modListCount($where);
		if ($res > 0) $errMsg	.= "添加失败：运输方式已存在，中文名或英文名或简码都不能重复！";
		
		if (!$errMsg) {
			$data  = array(
				"carrierNameCn"	=> $cn_name,
				"carrierNameEn"	=> $en_name,
				"carrierAli"	=> $ship_ali,
				"carrierAbb"	=> $ship_abb,
				"carrierLogo"	=> $ship_logo,
				"carrierIndex"	=> $ship_index,
				"plat_arr"		=> $plat_arr,
				"type"			=> $ship_type,
				"ship_add"		=> $ship_add,
				"weightMin"		=> $min_weight,
				"weightMax"		=> $max_weight,
				"timecount"		=> $ship_day,
				"note"			=> $ship_note,
				"createdTime"	=> time(),
				"is_track"		=> $is_track,
			);
			$carrierObj		= new CarrierManageAct();
			$res			= $carrierObj->actAddCarrierManage($data);
			if ($res) {
				$errMsg		= "添加成功！";
			}
		}
	    $this->smarty->assign('title','添加运输方式结果');
	    $this->smarty->assign('errMsg',$errMsg);
		$this->smarty->display('carrierManageAdd.htm');		
	}
	
	//update页面渲染
	public function view_update(){
		$errMsg		= "";
		$id			= isset($_POST["act-id"]) ? abs(intval(post_check($_POST["act-id"]))) : 0;
		$cn_name	= isset($_POST["cn_name"]) ? post_check($_POST["cn_name"]) : "";
        $en_name	= isset($_POST["en_name"]) ? post_check($_POST["en_name"]) : "";
        $ship_ali	= isset($_POST["ship_ali"]) ? post_check($_POST["ship_ali"]) : "";
        $ship_abb	= isset($_POST["ship_abb"]) ? post_check($_POST["ship_abb"]) : "";
        $ship_logo	= isset($_POST["ship_logo"]) ? post_check($_POST["ship_logo"]) : "";
        $ship_index	= isset($_POST["ship_index"]) ? post_check($_POST["ship_index"]) : "";
		$ship_add	= isset($_POST["ship_add"]) ? post_check($_POST["ship_add"]) : 0;
		$plat_arr	= isset($_POST["plat_name"]) ? $_POST["plat_name"] : "";
		$ship_type	= isset($_POST["ship_type"]) ? post_check($_POST["ship_type"]) : "";
		$min_weight	= isset($_POST["min_weight"]) ? post_check($_POST["min_weight"]) : 0;
		$max_weight	= isset($_POST["max_weight"]) ? post_check($_POST["max_weight"]) : 0;
		$ship_day	= isset($_POST["ship_day"]) ? post_check($_POST["ship_day"]) : 0;
		$ship_note	= isset($_POST["ship_note"]) ? post_check($_POST["ship_note"]) : "";
		$is_track	= isset($_POST["is_track"]) ? post_check($_POST["is_track"]) : 1;
		if (empty($id)) $errMsg   .= "运输方式ID有误！<br/>";
		if (empty($cn_name)) $errMsg   .= "运输方式中文名称有误！<br/>";
		if (empty($en_name)) $errMsg   .= "运输方式英文名称有误！<br/>";
		if (empty($ship_ali)) $errMsg   .= "运输方式简称有误！<br/>";
		if (empty($ship_abb) || !(preg_match("/^[A-Z]{5,5}$/",$ship_abb))) $errMsg   .= "运输方式简码有误！<br/>";
		if (empty($ship_index) || !(preg_match("/^[A-Z]{1,1}$/",$ship_index))) $errMsg   .= "运输方式字母索引有误！<br/>";
		if (!count($plat_arr)) $errMsg .= "所属平台有误！<br/>";
		if (empty($ship_add)) $errMsg  .= "发货地址有误！<br/>";
		if (!in_array($ship_type,array("0","1"))) $errMsg   .= "物流类型有误！<br/>";
		$res	= 0;
		$where	= "id <> {$id} AND (carrierNameEn = '{$en_name}' OR carrierNameCn = '{$cn_name}' OR carrierAbb = '{$ship_abb}')"; 
        $res	= CarrierManageModel::modListCount($where);
		if ($res > 0) $errMsg	.= "修改失败：运输方式已存在，中文名或英文名或简码都不能重复！";
		if (!$errMsg) {
			$data  = array(
				"carrierNameCn"	=> $cn_name,
				"carrierNameEn"	=> $en_name,
				"carrierAli"	=> $ship_ali,
				"carrierAbb"	=> $ship_abb,
				"carrierLogo"	=> $ship_logo,
				"carrierIndex"	=> $ship_index,
				"plat_arr"		=> $plat_arr,
				"type"			=> $ship_type,
				"ship_add"		=> $ship_add,
				"weightMin"		=> $min_weight,
				"weightMax"		=> $max_weight,
				"timecount"		=> $ship_day,
				"note"			=> $ship_note,
				"is_track"		=> $is_track,
			);
			$carrierObj		= new CarrierManageAct();
			$res			= $carrierObj->actUpdateCarrierManage($id, $data);
			if ($res) {
				$errMsg		= "修改成功！";
			}
		}
	    $this->smarty->assign('title','修改运输方式结果');
	    $this->smarty->assign('errMsg',$errMsg);
		$this->smarty->display('carrierManageModify.htm');		
	}
}
?>