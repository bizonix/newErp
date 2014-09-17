<?php
/**
 * 类名：PartnerView
 * 功能：封装供应商管理模块相关的操作
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */


class PartnerView extends BaseView {

    /**
    * 构造函数
    * @return   void
    */
   	public function __construct() {
		parent:: __construct();
		if(isset($_GET["mod"]) && !empty($_GET["mod"])) {
            $mod=$_GET["mod"];
		}
		if(isset($_GET["act"]) && !empty($_GET["act"])) {
			$act=$_GET["act"];
		}
		$this->smarty->assign('act',$act);//模块权限
		$this->smarty->assign('mod',$mod);//模块权限
		$this->smarty->caching 		= false;
		$this->smarty->debugging 	= false;
		$this->smarty->assign("WEB_API", WEB_API);
		$this->smarty->assign("WEB_URL", WEB_URL);
        $_username	= isset($_SESSION['userName']) ? $_SESSION['userName'] : "";
        $this->smarty->assign('_username',$_username);
	}


    /**
    * 显示供应商列表的函数
    * @return   void
    */
    public function view_index() {
		error_reporting(0);
       	$this->smarty->assign("title", "供应商管理");
		$partner = new PartnerAct();
		$list = $partner->index();
		//print_r($list);
        $partnerList = $list['partnerInfo'];
		$pagenum = 100;
		$total = $list['totalNum'];
		$page = new Page($total, $pagenum);
		$pageStr = $page->fpage();
		$powerArr = array("潘旭东","肖金华","郑凤娇");
		if(in_array($_SESSION['userCnName'],$powerArr)){
			$this->smarty->assign("power",1);
		}else{
			$this->smarty->assign("power",0);
		}
        $this->smarty->assign("userLists", $partnerList);
	    $this->smarty->assign('pageStr',$pageStr);//分页输出   
		$this->smarty->display('partner.htm');
    }

    /**
    * 管理白名单
    * @return   void
    */
    public function view_whiteList() {
       	$this->smarty->assign("title", "优质供应商管理");
        $where   = "  AND  pp.status = '2' ";  //黑名单
        $keyword = post_check($_GET['keyword']);
        $type    = post_check($_GET['type']);
		//获取当前登录着可以看到的采购料号 add by guanyongjun 2013-11-18
		$res			= CommonAct::actGetPurchaseAccess();
		if (empty($res['power_ids'])) {
			$uids		= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		} else {
			$uids		= $res['power_ids'];
		}
		$where	.= " AND pp.purchaseuser_id IN({$uids})";
        if($keyword != '') {
            if($type == "all") {
                $where .= " and (pp.`company_name` like '$keyword%' or pp.`username` like '$keyword%' or pp.`tel` like '$keyword%' or pp.`phone` like '$keyword%' or pp.`fax` like '$keyword%' or pp.`e_mail` like '$keyword%' or pp.`QQ` like '$keyword%' or pp.`AliIM` like '$keyword%' or pp.`address` like '$keyword%' or pp.`note` like '$keyword%' or pu.`global_user_name` like '$keyword%' )";
            } else if ($type == 'purchaseuser') {
                $where .= " and pu.`global_user_name` like '$keyword%' ";
            } else {
                $where .= " and pp.`".$type."` like '$keyword%' ";
            }
        }
        $this->smarty->assign("keyword", $keyword);
        $this->smarty->assign("type", $type);
        $this->smarty->assign("option_values", array("all", "company_name", "username", "tel", "phone", "e_mail", "QQ", "AliIM", "purchaseuser"));
		$this->smarty->assign("option_output", array("全部类型", "公司名称", "姓名", "电话", "移动电话", "电子邮件", "QQ", "阿里旺旺", "采购员"));
        $this->smarty->assign("option_selected", $type);

        $perNum = 20;
        $field  = " pp.id,pp.company_name,ppt.category_name,pp.username,pp.`status`,pp.tel,pp.phone,pp.fax,pp.QQ,pp.AliIM,pp.e_mail,pp.shoplink,pp.address,pp.city,pp.email_status,pp.sms_status,pu.global_user_name as purchaser,pp.note,pc.company ";
        $list   = PartnerAct::act_getPage($where, $field, $perNum, "", 'CN');
        $partnerList = $list[0];
        foreach($partnerList as $key => $partner) {
            $partnerList[$key]['status'] = ($partner['status'] == 0 ? '黑名单': ($partner['status'] == 1 ? '正常' : '优质供应商'));
            $partnerList[$key]['email_status'] = $partner['email_status'] == 1 ? '是' : '否';
            $partnerList[$key]['sms_status']   = $partner['sms_status'] == 1 ? '是' : '否';
        }
        $this->smarty->assign("pageIndex", $list[1]);
        $this->smarty->assign("searchResults", $list[2]);
        $this->smarty->assign("userLists", $partnerList);
		$this->smarty->display('manageWhiteList.htm');
    }

    /**
    * 管理黑名单
    * @return   void
    */
    public function view_blackList() {
       	$this->smarty->assign("title", "黑名单管理");
        $where   = "  AND pp.status = '0' ";  //黑名单
		//获取当前登录着可以看到的采购料号 add by guanyongjun 2013-11-18
		$res			= CommonAct::actGetPurchaseAccess();
		if (empty($res['power_ids'])) {
			$uids		= isset($_SESSION[C('USER_AUTH_SYS_ID')]) ? $_SESSION[C('USER_AUTH_SYS_ID')] : 0;
		} else {
			$uids		= $res['power_ids'];
		}
		$where	.= " AND pp.purchaseuser_id IN({$uids})";
        $keyword = post_check($_GET['keyword']);
        $type    = post_check($_GET['type']);
        if($keyword != '') {
            if($type == "all") {
                $where .= " and (pp.`company_name` like '$keyword%' or pp.`username` like '$keyword%' or pp.`tel` like '$keyword%' or pp.`phone` like '$keyword%' or pp.`fax` like '$keyword%' or pp.`e_mail` like '$keyword%' or pp.`QQ` like '$keyword%' or pp.`AliIM` like '$keyword%' or pp.`address` like '$keyword%' or pp.`note` like '$keyword%' or pu.`global_user_name` like '$keyword%' )";
            } else if ($type == 'purchaseuser') {
                $where .= " and pu.`global_user_name` like '$keyword%' ";
            } else {
                $where .= " and pp.`".$type."` like '$keyword%' ";
            }
        }
        $this->smarty->assign("keyword", $keyword);
        $this->smarty->assign("type", $type);
        $this->smarty->assign("option_values", array("all", "company_name", "username", "tel", "phone", "e_mail", "QQ", "AliIM", "purchaseuser"));
		$this->smarty->assign("option_output", array("全部类型", "公司名称", "姓名", "电话", "移动电话", "电子邮件", "QQ", "阿里旺旺", "采购员"));
        $this->smarty->assign("option_selected", $type);

        $perNum=20;
        $field  = " pp.id,pp.company_name,ppt.category_name,pp.username,pp.`status`,pp.tel,pp.phone,pp.fax,pp.QQ,pp.AliIM,pp.e_mail,pp.shoplink,pp.address,pp.city,pp.email_status,pp.sms_status,pu.global_user_name as purchaser,pp.note,pc.company ";
        $list = PartnerAct::act_getPage($where, $field, $perNum, "", 'CN');
        $partnerList = $list[0];
        foreach($partnerList as $key => $partner) {
            $partnerList[$key]['status'] = ($partner['status'] == 0 ? '黑名单': ($partner['status'] == 1 ? '正常' : '优质供应商'));
            $partnerList[$key]['email_status'] = $partner['email_status'] == 1 ? '是' : '否';
            $partnerList[$key]['sms_status']   = $partner['sms_status'] == 1 ? '是' : '否';
        }
        $this->smarty->assign("pageIndex", $list[1]);
        $this->smarty->assign("searchResults", $list[2]);
        $this->smarty->assign("userLists", $partnerList);
        $this->smarty->assign("button_moveout", "移出黑名单");
		$this->smarty->display('manageBlackList.htm');
    }

    /**
    * 添加供应商的函数
    * @return   void
    */
    public function view_addPartner() {
        $where = '';
        $field = " id, category_name ";
        $list  = PartnerTypeAct::act_getPartnerTypeList($where, $field);
        krsort($list);
        $option_values = array('0');
        $option_output = array('请选择');
        foreach($list as $key => $type) {
            $option_values[] = $type['id'];
            $option_output[] = $type['category_name'];
        }
		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign("option_values", $option_values);
		$this->smarty->assign("option_output", $option_output);
        $this->smarty->assign("option_selected", '0');
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
        $this->smarty->display('addPartner.htm');
    }

    /**
    * 编辑供应商信息的函数
    * @return   void
    */
    public function view_editPartner() {
        $this->smarty->assign('title','修改供应商信息');
        $partnerId = post_check($_GET['id']);
        $field  = " pp.limit_money,pp.limit_alert_money,pp.is_sign,pp.id,pp.company_name,ppt.category_name,pp.username,pp.type_id,pp.status,pp.tel,pp.phone,pp.fax,pp.QQ,pp.AliIM,pp.e_mail,pp.shoplink,pp.address,pp.city,pp.email_status,pp.sms_status,pu.global_user_name as purchaser,pp.note,pp.company_id,pp.purchaseuser_id ";
        $result = PartnerAct::act_getPartnerInfo($partnerId, $field);
        $this->smarty->assign("partnerInfo", $result[0]);
        $isSMSChecked   = $result[0]['sms_status'] == '1' ? 'checked="checked"' : '';
        $isEmailChecked = $result[0]['email_status'] == '1' ? 'checked="checked"' : '';
        $this->smarty->assign("isSMSChecked", $isSMSChecked);
        $this->smarty->assign("isEmailChecked", $isEmailChecked);

        $where = '';
        $field = ' `id`, `category_name` ';
        $list  = PartnerTypeAct::act_getPartnerTypeList($where, $field);
        krsort($list);
        $option_values = array();
        $option_output = array();
        $type_id       = 0;
        foreach($list as $key => $type) {
            $option_values[] = $type['id'];
            $option_output[] = $type['category_name'];
            if($result[0]['type_id'] == $type['id']) {
                $type_id = $type['id'];
            }
        }
        $this->smarty->assign("option_values", $option_values);
		$this->smarty->assign("option_output", $option_output);
        $this->smarty->assign("option_selected", $type_id);

        $field = ' `id`,`company` ';
        $list  = PartnerAct::act_getPartnerCompany($where, $field);
        $option_company_id   = array();
        $option_company_name = array();
        $company_id          = 0;
        foreach($list as $key => $company) {
            $option_company_id[]   = $company['id'];
            $option_company_name[] = $company['company'];
            if($result[0]['company_id'] == $company['id']) {
                $company_id = $company['id'];
            }
        }
        $this->smarty->assign("option_company_id", $option_company_id);
		$this->smarty->assign("option_company_name", $option_company_name);
        $this->smarty->assign("option_company_selected", $company_id);

        $field   = " global_user_id as id, global_user_login_name as username ";
		$where = " and  global_user_id in ({$_SESSION['access_id']})";
        $list    = PartnerAct::act_getPurchaserList($where, $field);
        $option_purchaser_id   = array();
        $option_purchaser      = array();
        $purchaser_id          = 0;
        foreach($list as $key => $purchaser) {
            $option_purchaser_id[]   = $purchaser['id'];
            $option_purchaser[]      = $purchaser['global_user_name'];
            if($result[0]['purchaseuser_id'] == $purchaser['id']) {
                $purchaser_id = $purchaser['id'];
            }
        }

		$purchaseList	= getPurchaseUserList();
        $this->smarty->assign('purchaseList',$purchaseList);//采购列表 
        $this->smarty->assign("option_purchaser_id", $option_purchaser_id);
		$this->smarty->assign("option_purchaser", $option_purchaser);
        $this->smarty->assign("option_purchaser_selected", $purchaser_id);
		$this->smarty->display('editPartner.htm');
    }

    /**
    * 导入供应商信息的函数
    * @return   void
    */
    public function view_import() {
		$this->smarty->display('importPartner.htm');
    }

    /**
    * 保存导入的供应商信息
    * @return   void
    */
    public function view_importSave() {
        PartnerAct::act_importSave();
    }

    /**
    * 导出供应商信息的函数
    * @return   void
    */
    public function view_export() {
		error_reporting(-1);
        $where = '';
        $idStr = $_GET['idArr'];
        if(!empty($idStr)) {
            $where .= ' and pp.id in ('.$idStr.')';
        }
        $field  = " pp.id,pp.company_name,ppt.category_name,pp.username,pp.status,pp.tel,pp.phone,pp.fax,pp.QQ,pp.AliIM,pp.e_mail,pp.shoplink,pp.address,pp.city,pp.email_status,pp.sms_status,pu.global_user_name as purchaser,pp.note,pc.company ";
		$partner = new PartnerAct();
        $result = $partner->act_getData($where, $field);
        if(empty($result)){
        	echo "无数据";
        	return false;
        }
        //print_r($result);exit;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '单位名称');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '姓名');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '单位类型');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '电话');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '移动电话');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '传真');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'QQ');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '邮件');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '阿里旺旺');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '店铺链接');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '所属城市');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', '地址');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', '状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', '支持短信');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', '支持邮件');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', '采购员');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', '关联公司');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', '备注');

        $a = 2;
        foreach($result as $key => $partner) {
            $status       = $partner['status'] == 0 ? '黑名单' : $partner['status'] == 1 ? '正常':'优质供应商';
            $email_status = $partner['email_status'] == 1 ? '是' : '否';
            $sms_status   = $partner['sms_status'] == 1 ? '是' : '否';
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit($partner['company_name'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit($partner['username'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit($partner['category_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit($partner['tel'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($partner['phone'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$a)->setValueExplicit($partner['fax'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$a)->setValueExplicit($partner['QQ'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit($partner['e_mail'], PHPExcel_Cell_DataType::TYPE_STRING);
       	    $objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$a)->setValueExplicit($partner['AliIM'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$a)->setValueExplicit($partner['shoplink'], PHPExcel_Cell_DataType::TYPE_STRING);
           	$objPHPExcel->setActiveSheetIndex(0)->getCell('K'.$a)->setValueExplicit($partner['city'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('L'.$a)->setValueExplicit($partner['address'], PHPExcel_Cell_DataType::TYPE_STRING);
           	$objPHPExcel->setActiveSheetIndex(0)->getCell('M'.$a)->setValueExplicit($status, PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('N'.$a)->setValueExplicit($email_status, PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('O'.$a)->setValueExplicit($sms_status, PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('P'.$a)->setValueExplicit($partner['purchaser'], PHPExcel_Cell_DataType::TYPE_STRING);
        	$objPHPExcel->setActiveSheetIndex(0)->getCell('Q'.$a)->setValueExplicit($partner['company'], PHPExcel_Cell_DataType::TYPE_STRING);
       	    $objPHPExcel->setActiveSheetIndex(0)->getCell('R'.$a)->setValueExplicit($partner['note'], PHPExcel_Cell_DataType::TYPE_STRING);
            $a++;
        }

        $objPHPExcel->getActiveSheet(0)->getStyle('A1:R500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(35);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(50);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setWidth(50);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('R')->setWidth(50);

        $title		= "PartnerInfo".date('Y-m-d');
        $titlename	= "PartnerInfo".date('Y-m-d').".xls";
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename={$titlename}");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
?>
