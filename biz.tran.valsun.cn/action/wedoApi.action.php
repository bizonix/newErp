<?php
/**
 * 类名：WedoApiAct
 * 功能：运德跟踪号生成管理动作处理层
 * 版本：1.0
 * 日期：2014/04/19
 * 作者：管拥军
 */
  
class WedoApiAct {
    public static $errCode	= 0;
	public static $errMsg	= "";

	/**
	 * WedoApiAct::actWedoSn()
	 * 列出符合条件的数据并分页显示
	 * @param string $condition 查询条件
	 * @param integer $curpage 页码
	 * @param integer $pagenum 每页个数
	 * @return array 
	 */
 	public function actWedoSn(){
		$data			= array();
		$condition		= '';
		//接收参数生成条件
		$curpage		= isset($_GET['page']) ? abs(intval($_GET['page'])) : 1;
		$type			= isset($_GET['type']) ? trim($_GET['type']) : '';
		$key			= isset($_GET['key']) ? post_check(trim($_GET['key'])) : '';
		$condition		.= "1";
		if ($type && $key) {
			if (!in_array($type,array('wedo_sn'))) redirect_to("index.php?mod=wedoApi&act=wedoSn");
			$condition	.= ' AND '.$type." = '".$key."'";
		}
		//获取符合条件的数据并分页
		$pagenum		= 20;
		$total			= WedoApiModel::modListCount($condition);
		$res			= WedoApiModel::modList($condition, $curpage, $pagenum);
		$page	 		= new Page($total, $pagenum, '', 'CN');
		$pageStr		= "";
		if ($res) {
			if ($total>$pagenum) {
				$pageStr = $page->fpage(array(0,1,2,3,4,5,6,7,8,9));
			} else {
				$pageStr = $page->fpage(array(0,1,2,3));
			}
		} else {
			$pageStr 	 = '暂无数据';
		}		
		//封装数据返回
		$data['key']	 = $key;
		$data['type']	 = $type;
		$data['lists']	 = $res;
		$data['pages']	 = $pageStr;
		self::$errCode   = WedoApiModel::$errCode;
        self::$errMsg    = WedoApiModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * WedoApiAct::actWedoSnAdd()
	 * 添加某个运德跟踪号生成
	 * @return array  
	 */
	public function actWedoSnAdd(){
		$data			= array();
		$data['gids']	= WedoApiModel::getGlobalUser();
        return $data;
    }
	
	/**
	 * WedoApiAct::actSaveOrderImport()
	 * 运德物流订单信息excell文件上传保存
	 * @return array  
	 */
	public function actSaveOrderImport(){
		$data	= array();
		$uid	= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if (isset($_FILES['upfile']) && !empty($_FILES['upfile'])){
			$fielName = $uid."_".date('YmdHis').'_'.rand(1,3009).".xls";
			$fileName = WEB_PATH.'html/temp/'.$fielName;
			if (move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				$filePath = $fileName;
			}
		}
		if (substr($filePath,-3)!='xls') {
			show_message($this->smarty,"导入的文件名格式错误！","index.php?mod=wedoApi&act=orderImport");
			return false;
		}
		
		//读取导入文件
		require_once WEB_PATH."lib/PHPExcel.php";
		$PHPExcel		= new PHPExcel(); 
		$PHPReader		= new PHPExcel_Reader_Excel2007();    
		if (!$PHPReader->canRead($filePath)) {      
			$PHPReader	= new PHPExcel_Reader_Excel5(); 
			if (!$PHPReader->canRead($filePath)) {
				show_message($this->smarty,"文件内容无法读取！","index.php?mod=wedoApi&act=orderImport");
				@unlink($filePath);
				return false;
			}
		}
		$PHPExcel 		= $PHPReader->load($filePath);
		$currentSheet 	= $PHPExcel->getSheet(0);
		$row			= 2;
		$res			= WedoApiModel::getWedoSnById($uid);
		if (empty($res)) {
			show_message($this->smarty,"您暂无权使用此开放业务，请联系系统管理员！","index.php?mod=wedoApi&act=orderImport");
			@unlink($filePath);
			return false;
		}
		$wedoSn			= isset($res['wedo_sn']) ? $res['wedo_sn'] : "";
		if (empty($wedoSn)) {
			show_message($this->smarty,"您暂时不能使用本功能，请联系系统管理员！","index.php?mod=wedoApi&act=orderImport");
			@unlink($filePath);
			return false;
		}
		while (true) {
			$flag		= true;
			$aa			= 'A'.$row;
			$bb			= 'B'.$row;
			$cc			= 'C'.$row;
			$dd			= 'D'.$row;
			$ee			= 'E'.$row;
			$ff			= 'F'.$row;
			$orderSn	= trim($currentSheet->getCell($aa)->getValue());
			$orderSn	= post_check(substr($orderSn,0,50));
			$platAccount= trim($currentSheet->getCell($bb)->getValue());
			$platAccount= post_check(substr($platAccount,0,30));
			$scanTime	= strtotime(trim($currentSheet->getCell($cc)->getValue()));
			$toCountry	= trim($currentSheet->getCell($dd)->getValue());
			$toCountry	= post_check(substr($toCountry,0,30));
			$toCity		= trim($currentSheet->getCell($ee)->getValue());
			$toCity		= post_check(substr($toCity,0,30));
			$weight		= trim($currentSheet->getCell($ff)->getValue());
			$weight		= round(floatval($weight),4);
			$addTime	= time();
			$carrierId	= 61;
			$channelId	= 85;
			if (empty($orderSn) && empty($buyId) && empty($scanTime) && empty($county)) break;
			$res		= 0;
			$where		= "add_user_id = '{$uid}' AND orderSn = '{$orderSn}' AND platAccount = '{$platAccount}' AND toCountry = '{$toCountry}' AND toCity = '{$toCity}' AND weight = '{$weight}'"; 
			$res		= WedoApiModel::modWedoNumListCount($where);
			if ($res > 0) {
				self::$errMsg	.= "添加失败：{$orderSn}---{$platAccount}---订单信息已存在！<br/>";
				$flag	= false;
			}
			if (empty($orderSn) || empty($platAccount) || empty($scanTime) || empty($toCountry) || empty($toCity) || empty($weight)) {
				self::$errMsg	.= "添加失败：{$orderSn}---{$platAccount}---{$scanTime}---{$toCountry}---{$toCity}---{$weight}---订单信息不全！<br/>";
				$flag	= false;
			}
			$data	= array (
						"orderSn"		=> $orderSn,	
						"wedoSn"		=> $wedoSn,	
						"carrierId"		=> $carrierId,	
						"channelId"		=> $channelId,	
						"toCountry"		=> $toCountry,	
						"platAccount"	=> $platAccount,	
						"scanTime"		=> $scanTime,	
						"addTime"		=> $addTime,	
						"add_user_id"	=> $uid,	
						"toCity"		=> $toCity,	
						"weight"		=> $weight,	
					);
			if ($flag) {
				$res	= WedoApiModel::saveWedoOrder($data);
				if (!$res) {
					self::$errMsg	.= "添加失败：".WedoApiModel::$errMsg."<br/>";
					break;
				}
			}
			$row++;
		}
		$data		= array("res"=>self::$errMsg);
        return $data;
    }
	
	/**
	 * WedoApiAct::actWedoSnModify()
	 * 返回某个运德跟踪号生成
	 * @param int $id 查询ID
	 * @return array 
	 */
	public function actWedoSnModify(){
		$data		= array();
		$id			= isset($_GET['id']) ? intval(trim($_GET['id'])) : 0;
		if (empty($id)) {
			show_message($this->smarty,"运德跟踪号生成ID不能为空？","");	
			return false;
		}
		$data['gid']	= $id;
		$data['gids']	= WedoApiModel::getGlobalUser();
		$data['res']	= WedoApiModel::modWedoSnModify($id);
		self::$errCode  = WedoApiModel::$errCode;
        self::$errMsg   = WedoApiModel::$errMsg;
		if (self::$errCode!=0) {
			show_message($this->smarty,self::$errMsg,"");	
			return false;
		}
        return $data;
    }

	/**
	 * WedoApiAct::act_addWedoSn()
	 * 添加运德跟踪号生成
	 * @param string $competence 授权内容
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_addWedoSn(){
        $gid		= isset($_POST["gid"]) ? intval($_POST["gid"]) : 0;
        $wedo_sn	= isset($_POST["wedo_sn"]) ? post_check($_POST["wedo_sn"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无数据添加权限！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 10001;
			self::$errMsg   = "运德跟踪号生成ID有误！";
			return false;
		}
		if (empty($wedo_sn)) {
			self::$errCode  = 10002;
			self::$errMsg   = "运德跟踪号生成规则有误！";
			return false;
		}
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"gid"			=> $gid,
			"wedo_sn"		=> $wedo_sn,
			"addTime"		=> time(),
			"add_user_id"	=> $uid,
		);
        $res				= WedoApiModel::addWedoSn($data);
		self::$errCode  	= WedoApiModel::$errCode;
        self::$errMsg   	= WedoApiModel::$errMsg;
		return $res;
    }

	/**
	 * WedoApiAct::act_updateWedoSn()
	 * 修改运德跟踪号生成
	 * @param string $competence 授权内容
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_updateWedoSn(){
		$gid		= isset($_POST["gid"]) ? intval($_POST["gid"]) : 0;
        $wedo_sn	= isset($_POST["wedo_sn"]) ? post_check($_POST["wedo_sn"]) : "";
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 20000;
			self::$errMsg   = "对不起,您无数据编辑权限！";
			return false;
		}
		if (empty($gid)) {
			self::$errCode  = 20001;
			self::$errMsg   = "运德跟踪号生成ID有误！";
			return false;
		}
		if (empty($wedo_sn)) {
			self::$errCode  = 20002;
			self::$errMsg   = "运德跟踪号生成规则有误！";
			return false;
		}		
		$uid				= $_SESSION[C('USER_AUTH_SYS_ID')];
		$data  = array(
			"gid"			=> $gid,
			"wedo_sn"		=> $wedo_sn,
			"editTime"		=> time(),
			"edit_user_id"	=> $uid,
		);
        $res			= WedoApiModel::updateWedoSn($gid, $data);
		self::$errCode  = WedoApiModel::$errCode;
        self::$errMsg   = WedoApiModel::$errMsg;
		return $res;
    }
	
	/**
	 * WedoApiAct::act_delWedoSn()
	 * 删除运德跟踪号生成
	 * @param int $gid 开放权限ID
	 * @return  bool
	 */
	public function act_delWedoSn(){
		$gid		= isset($_POST["id"]) ? intval(trim($_POST["id"])) : 0;
		$act		= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod		= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 30001;
			self::$errMsg   = "对不起,您无数据删除权限！";
			return false;
		}
		if (empty($gid) || !is_numeric($gid)) {
			self::$errCode  = 30000;
			self::$errMsg   = "开放用户权限ID有误！";
			return false;
		}
        $res			= WedoApiModel::delWedoSn($gid);
		self::$errCode  = WedoApiModel::$errCode;
        self::$errMsg   = WedoApiModel::$errMsg;
		return $res;
    }
	
	/**
	 * WedoApiAct::act_orderExport()
	 * 导出运德物流订单跟踪号信息
	 * @param string $timeNode 时间节点
	 * @return json string 
	 */
 	public function act_orderExport(){
		$uid			= intval($_SESSION[C('USER_AUTH_SYS_ID')]);
		if (empty($uid)) {
			self::$errCode  = 10001;
			self::$errMsg   = "对不起,您尚未登录！";
			return false;
		} 
		$timeNode		= isset($_GET['timeNode']) ? post_check(trim($_GET['timeNode'])) : '';
		$condition		= "1";
		$condition		.= " AND add_user_id = '{$uid}'";
		if (!empty($timeNode)) {
			if(!in_array($timeNode,array('scanTime','addTime'))) redirect_to("index.php?mod=wedoApi&act=orderExport");
			$startTime	= isset($_GET['startTime']) ? strtotime(trim($_GET['startTime'])." 00:00:00") : strtotime(date("Y-m-d",time())." 00:00:00");
			$endTime	= isset($_GET['endTime']) ? strtotime(trim($_GET['endTime'])." 23:59:59") : strtotime(date("Y-m-d",time())." 23:59:59");
			if ($startTime && $endTime) {
				$condition	.= ' AND '.$timeNode." BETWEEN '".$startTime."' AND "."'".$endTime."'";
			}
		}	
		$act			= isset($_REQUEST["act"]) ? post_check($_REQUEST["act"]) : "";
		$mod			= isset($_REQUEST["mod"]) ? post_check($_REQUEST["mod"]) : "";
		if	(!AuthUser::checkLogin($mod, $act)) {
			self::$errCode  = 10000;
			self::$errMsg   = "对不起,您无跟踪号数据导出权限！";
			return false;
		}
		$res			= WedoApiModel::orderWedoExport($condition);
		self::$errCode  = WedoApiModel::$errCode;
        self::$errMsg   = WedoApiModel::$errMsg;
        return $res;
    }
}
?>