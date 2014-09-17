<?php
/**
 * 类名：AgreementView
 * 功能：协议管理模块相关的操作
 * 版本：1.0
 * 日期：2014/09/11
 * 作者：杨世辉
 */
class AgreementView extends BaseView {

	/**
	 * 构造函数
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
	 * 列表页
	 */
	public function view_index() {
		$data = AgreementModel::getList();
		$page = new Page($data['totalNum'], $pagenum = 100);
		$pageStr = $page->fpage();
		$this->smarty->assign("pageStr",$pageStr);
		$this->smarty->assign("listData",$data['listData']);
		$this->smarty->assign("title", "协议管理");
		$this->smarty->assign("companyTypeList",array('1'=>'企业法人','2'=>'个体经营'));
		$this->smarty->assign("statusList",array('1'=>'正常','2'=>'限制'));
		$this->smarty->display('agreementList.htm');
	}

	/**
	 * 添加
	 */
	public function view_add() {
		$this->smarty->assign("companyTypeList",array('1'=>'企业法人','2'=>'个体经营'));
		$this->smarty->assign('title','新增协议');
		$this->smarty->display('addAgreement.htm');
	}

	/**
	 * 修改
	 */
	public function view_edit() {
		$id = post_check($_GET['id']);
		$entity = AgreementModel::getById($id);
		//echo '<pre>';print_r($entity);exit;
		$this->smarty->assign('entity',$entity);
		$this->smarty->assign("companyTypeList",array('1'=>'企业法人','2'=>'个体经营'));
		$this->smarty->assign("statusList",array('1'=>'正常','2'=>'限制'));
		$this->smarty->assign('title','修改协议');
		$this->smarty->display('editAgreement.htm');
	}

	/**
	 * 导出
	 */
	public function view_export() {
		$where = '';
		$idStr = $_GET['idArr'];
		if(!empty($idStr)) {
			$where .= ' and pp.id in ('.$idStr.')';
		}
		$field  = " pp.id,pp.companyName,pp.companyType,pp.contactPerson,pp.expiration,pp.status,pp.is_delete,
				pp.addTime,pp.modifyTime,pp.addUserId,pp.modifyUserId ";
		$result = AgreementModel::getData($where, $field);
		if(empty($result)){
			die("无数据");
		}

		$companyTypeList = array('1'=>'企业法人','2'=>'个体经营');
		$statusList = array('1'=>'正常','2'=>'限制');
		require_once WEB_PATH."lib/PHPExcel.php";
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '公司名称');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '公司类型');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '联系人');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '协议到期时间');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '状态');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '是否删除');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '添加时间');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '添加人');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '修改时间');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '修改人');

		$a = 2;
		$userModel = UserModel::getInstance();
		foreach($result as $key => $val) {
			$companyType 	= $companyTypeList[$val['companyType']];
			$status       	= $statusList[$val['status']];
			$is_delete		= $val['is_delete'] == 1 ? '是' : '否';
			$where			= "where a.global_user_id='{$val['addUserId']}'";
			$userinfo 		= $userModel->getGlobalUserLists('a.global_user_name', $where, '', 'limit 1');
			$addUser		= $userinfo[0]['global_user_name'];
			$modifyTime		= '';
			$modifyUser		= '';
			if ($val['modifyTime']) {
				$modifyTime = date('Y-m-d H:i', strtotime($val['modifyTime']));
				$where		= "where a.global_user_id='{$val['modifyUserId']}'";
				$userinfo 	= $userModel->getGlobalUserLists('a.global_user_name', $where, '', 'limit 1');
				$modifyUser	= $userinfo[0]['global_user_name'];
			}

			$objPHPExcel->setActiveSheetIndex(0)->getCell('A'.$a)->setValueExplicit($val['companyName'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('B'.$a)->setValueExplicit($companyType, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('C'.$a)->setValueExplicit($val['contactPerson'], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('D'.$a)->setValueExplicit(date('Y-m-d', strtotime($val['expiration'])), PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('E'.$a)->setValueExplicit($status, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('F'.$a)->setValueExplicit($is_delete, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('G'.$a)->setValueExplicit(date('Y-m-d H:i', strtotime($val['addTime'])), PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('H'.$a)->setValueExplicit($addUser, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('I'.$a)->setValueExplicit($modifyTime, PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->setActiveSheetIndex(0)->getCell('J'.$a)->setValueExplicit($modifyUser, PHPExcel_Cell_DataType::TYPE_STRING);
			$a++;
		}

		$objPHPExcel->getActiveSheet(0)->getStyle('A1:R500')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(20);

		$title		= "AgreementInfo".date('Y-m-d');
		$titlename	= "AgreementInfo".date('Y-m-d').".xls";
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