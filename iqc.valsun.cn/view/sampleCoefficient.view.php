<?php


/*
 * iqc检测系数View
 */
class SampleCoefficientView extends BaseView {

	//系数列表展示
	public function view_getSampleCoefficientList() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$sampleCoefficientAct = new SampleCoefficientAct();

		$where = 'WHERE 1=1 ';
		if ($type == 'search') {
			$cName = isset ($_GET['cName']) ? $_GET['cName'] : '';
			$sampleTypeId = isset ($_GET['sampleTypeId']) ? $_GET['sampleTypeId'] : '';
			if (!empty ($cName)) {
				$where .= "AND cName='$cName' ";
			}
			if (!empty ($sampleTypeId)) {
				$where .= "AND sampleTypeId='$sampleTypeId' ";
			}
		}
		$total = $sampleCoefficientAct->act_getSampleCoefficientCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

		$sampleCoefficientList = $sampleCoefficientAct->act_getSampleCoefficientList('*', $where);
		for ($i = 0; $i < count($sampleCoefficientList); $i++) { //将id对应Name及Num添加到数组中
			$sampleCoefficientList[$i]['sampleTypeName'] = SampleCoefficientModel :: getSampleTypeNameById($sampleCoefficientList[$i]['sampleTypeId']);
			$sampleCoefficientList[$i]['sizeCodeNum'] = SampleCoefficientModel :: getSizeCodeNumById($sampleCoefficientList[$i]['sizeCodeId']);
		}
		if (!empty ($_GET['page'])) {
			if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
				$n = 1;
			} else {
				$n = (intval($_GET['page']) - 1) * $num +1;
			}
		} else {
			$n = 1;
		}
		if ($total > $num) {
			//输出分页显示
			$show_page = $page->fpage(array (
				0,
				2,
				3,
				4,
				5,
				6,
				7,
				8,
				9
			));
		} else {
			$show_page = $page->fpage(array (
				0,
				2,
				3
			));
		}
		$navarr = array (
			"<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",
			">>",
			"样本系数列表"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '样本系数列表');
		$this->smarty->assign('secnev', '4');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('username', $_SESSION['userName']);

		$this->smarty->assign('sampleCoefficientList', $sampleCoefficientList ? $sampleCoefficientList : null); //循环列表
		$this->smarty->assign('sampleTypeList', SampleCoefficientModel :: getCoefficientSampleTypeName()); //系数表中存在的样本类型
		$this->smarty->assign('cNameList', SampleCoefficientModel :: getSampleCoefficientList("distinct(cName)", '')); //系数表中存在的系数名称
		$this->smarty->display("sampleCoefficient.html");
	}

	//修改页面展示
	public function view_updateScanSampleCoefficient() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$sampleCoefficientAct = new SampleCoefficientAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		if (empty ($id)) { //id为空时，跳转到列表页面，输出错误信息
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$sampleCoefficientList = $sampleCoefficientAct->act_getSampleCoefficientList('*', $where);

		if (empty ($sampleCoefficientList)) {
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		} else {
			$value = $sampleCoefficientList[0];
		}
		//设置修改页面上指定字段的值
		$navarr = $navarr = array (
			"<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",
			">>",
			"<a href='index.php?mod=sampleCoefficient&act=getSampleCoefficientList'>系数列表</a>",
			">>",
			"修改系数"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '修改系数');
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign("id", $value['id']);
		$this->smarty->assign("cName", $value['cName']);
		$this->smarty->assign("sampleTypeId", $value['sampleTypeId']);
		$this->smarty->assign("sizeCodeId", $value['sizeCodeId']);
		$this->smarty->assign("sampleTypeName", SampleCoefficientModel :: getSampleTypeNameById($value['sampleTypeId']));
		$this->smarty->assign("sizeCodeNum", SampleCoefficientModel :: getSizeCodeNumById($value['sizeCodeId']));
		$this->smarty->assign("Ac", $value['Ac']);
		$this->smarty->assign("Re", $value['Re']);
		$this->smarty->assign("Al", $value['Al']);
		$this->smarty->assign("Rt", $value['Rt']);
		$this->smarty->assign("is_open", $value['is_open']);
		$this->smarty->display("updateSampleCoefficient.html");
	}

	//提交修改内容跳转
	public function view_updateSampleCoefficient() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$sampleCoefficientAct = new SampleCoefficientAct();

		$id = isset ($_POST['id']) ? post_check($_POST['id']) : '';
		$sampleTypeId = isset ($_POST['sampleTypeId']) ? post_check($_POST['sampleTypeId']) : '';
		$sizeCodeId = isset ($_POST['sizeCodeId']) ? post_check($_POST['sizeCodeId']) : '';
		$Ac = isset ($_POST['Ac']) ? post_check($_POST['Ac']) : '';
		$Re = isset ($_POST['Re']) ? post_check($_POST['Re']) : '';
		$Al = isset ($_POST['Al']) ? post_check($_POST['Al']) : '';
		$Rt = isset ($_POST['Rt']) ? post_check($_POST['Rt']) : '';

		if (empty ($id) || empty ($sampleTypeId) || empty ($sizeCodeId) || trim($Ac) == '' || trim(Re) == '') { //为空时，跳转到列表页面，输出错误信息
			$status = '修改失败，存在必填项为空的字段';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		}
		$set = "SET Ac='$Ac',Re='$Re',Al='$Al',Rt='$Rt' ";
		$where = "WHERE id=$id ";
		$affectRow = $sampleCoefficientAct->act_updateSampleCoefficient($set, $where);
		$status = '无内容被修改';
		if ($affectRow >= 1) {
			$flag = DetectStandardModel :: updateSampleStandard();
			if ($flag)
				$status = '修改成功,检测标准已自动更新';
		}
		header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
	}

	//启动系数
	public function view_onSampleCoefficient() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$sampleCoefficientAct = new SampleCoefficientAct();
		$sampleTypeOnId = isset ($_GET['sampleTypeOnId']) ? post_check($_GET['sampleTypeOnId']) : '';
		$cNameOn = isset ($_GET['cNameOn']) ? post_check($_GET['cNameOn']) : '';

		if (empty ($sampleTypeOnId) || empty ($cNameOn)) { //为空时，跳转到列表页面，输出错误信息
			$status = '启动失败，id为空';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		}

		$return = $sampleCoefficientAct->act_onSampleCoefficient($cNameOn, $sampleTypeOnId); //启动该系数
		if ($return) {
			$flag = DetectStandardModel :: updateSampleStandard();
			if ($flag) {
				$status = '启动成功,检测标准已自动更新';
			} else {
				$status = '启动成功,自动更新失败';
			}
		} else {
			$status = '找不到指定记录或已经启动';
		}
		header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
	}

	//添加页面展示
	public function view_addScanSampleCoefficient() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}

		//设置修改页面上指定字段的值
		$navarr = $navarr = array (
			"<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",
			">>",
			"<a href='index.php?mod=sampleCoefficient&act=getSampleCoefficientList'>系数列表</a>",
			">>",
			"添加系数"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '添加系数');
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign('sampleTypeList', SampleCoefficientModel :: getSampleType()); //所有的样本类型
		$this->smarty->assign('sizeCodeList', SampleCoefficientModel :: getSizeCode()); //所有的样本大小
		$this->smarty->display("addSampleCoefficient.html");
	}

	//添加系数完成后跳转
	public function view_addSampleCoefficient() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$sampleCoefficientAct = new SampleCoefficientAct();

		$cName = isset ($_POST['cName']) ? post_check(trim($_POST['cName'])) : '';
		$sampleTypeId = isset ($_POST['sampleTypeId']) ? post_check($_POST['sampleTypeId']) : '';
		$sizeCodeId = isset ($_POST['sizeCodeId']) ? post_check($_POST['sizeCodeId']) : '';
		$Ac = isset ($_POST['Ac']) ? post_check($_POST['Ac']) : '';
		$Re = isset ($_POST['Re']) ? post_check($_POST['Re']) : '';
		$Al = isset ($_POST['Al']) ? post_check($_POST['Al']) : '';
		$Rt = isset ($_POST['Rt']) ? post_check($_POST['Rt']) : '';

		if (empty ($cName) || empty ($sampleTypeId) || empty ($sizeCodeId) || trim($Ac) == '' || trim(Re) == '' || trim($Al) == '' || trim(RT) == '') { //为空时，跳转到列表页面，输出错误信息
			$status = '添加失败，存在必填项为空的字段';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		}
		$where = "WHERE cName='$cName' AND sampleTypeId='$sampleTypeId' AND sizeCodeId='$sizeCodeId' ";
		$sampleCoefficientList = $sampleCoefficientAct->act_getSampleCoefficientList('id', $where);
		if (!empty ($sampleCoefficientList)) {
			$status = '添加失败，已有相同的系数、样本类型、样本大小记录';
			header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
			exit;
		}
		$set = "SET cName='$cName',sampleTypeId='$sampleTypeId',sizeCodeId='$sizeCodeId',Ac='$Ac',Re='$Re',Al='$Al',Rt='$Rt' ";
		$affectRow = $sampleCoefficientAct->act_addSampleCoefficient($set);
		if ($affectRow >= 1) {
			$status = '添加成功';
		} else {
			$status = '添加失败';
		}
		header("location:index.php?mod=sampleCoefficient&act=getSampleCoefficientList&status=$status");
	}

}