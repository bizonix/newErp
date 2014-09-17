<?php


/*
 * 不良品信息库View
 */
class DefectiveProductsView extends BaseView {

	//不良品列表展示
	public function view_getDefectiveProductsList() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		//$type = isset($_GET['type'])?$_GET['type']:'';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$defectiveProductsAct = new DefectiveProductsAct();

		$where = 'WHERE 1=1 ';
		$total = $defectiveProductsAct->act_getDefectiveProductsCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

		$defectiveProductsList = $defectiveProductsAct->act_getDefectiveProductsList('*', $where);

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
			"<a href='index.php?mod=iqcInfo&act=iqcScanList'>IQC检测信息</a>",
			">>",
			"不良品列表"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '不良品列表');
		$this->smarty->assign('secnev', '3');
		$this->smarty->assign("show_page", $show_page);

		$this->smarty->assign("status", $status);
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign("defectiveProductsList", $defectiveProductsList ? $defectiveProductsList : null);
		$this->smarty->display("defectiveProducts.html");
	}

	//不良品列表移动到报废、内部处理、待退回页面展示
	public function view_moveDefectiveProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$defectiveProductsAct = new DefectiveProductsAct();

		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$defectiveId = isset ($_GET['defectiveId']) ? post_check($_GET['defectiveId']) : '';
		$infoId = isset ($_GET['infoId']) ? post_check($_GET['infoId']) : '';

		if (empty ($type) || empty ($defectiveId) || empty ($infoId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '处理失败，信息不完整';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		$select = '*';
		$where = "WHERE id='$defectiveId'";
		$defectiveProductsList = $defectiveProductsAct->act_getDefectiveProductsList($select, $where);
		if (empty ($defectiveProductsList)) {
			$status = '处理失败，不存在这条记录';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		$navarr = array (
			"<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",
			">>",
			"<a href='index.php?mod=defectiveProducts&act=getDefectiveProductsList'>不良品列表</a>",
			">>",
			"不良品处理"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '不良品处理页面');
		$this->smarty->assign('secnev', '3');
		$this->smarty->assign("defectiveId", $defectiveId);
		$this->smarty->assign("spu", $defectiveProductsList[0]['spu']);
		$this->smarty->assign("sku", $defectiveProductsList[0]['sku']);
		$this->smarty->assign("type", $type);
		$this->smarty->assign("defectiveNum", $defectiveProductsList[0]['defectiveNum']);
		$this->smarty->assign("processedNum", $defectiveProductsList[0]['processedNum']);
		$this->smarty->assign("leftNum", $defectiveProductsList[0]['defectiveNum'] - $defectiveProductsList[0]['processedNum']);
		$this->smarty->assign("infoId", $infoId);
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign("defectiveProductsList", $defectiveProductsList);
		$this->smarty->display("moveDefectiveProducts.html");
	}

	//不良品列表采购审核
	public function view_auditDefectiveProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$defectiveProductsAct = new DefectiveProductsAct();
		$audit = isset ($_GET['audit']) ? $_GET['audit'] : '';
		;
		$defectiveId = isset ($_GET['defectiveId']) ? post_check($_GET['defectiveId']) : '';
		$infoId = isset ($_GET['infoId']) ? post_check($_GET['infoId']) : '';

		if (empty ($audit) || empty ($defectiveId) || empty ($infoId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '审核失败，信息不完整';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		$select = 'defectiveNum';
		$where = "WHERE id='$defectiveId' AND infoId='$infoId'";
		$defectiveProductsList = $defectiveProductsAct->act_getDefectiveProductsList($select, $where); //检测记录defectiveId和infoId是否存在
		if (empty ($defectiveProductsList)) {
			$status = '审核失败，不存在这条记录';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		if ($audit == 'audit') { //审核操作
			$now = time();
			$set = "SET defectiveStatus='1',auditTime='$now' ";
			$where = "WHERE id='$defectiveId' ";
			$affectRow = $defectiveProductsAct->act_updateDefectiveProducts2($set, $where);
			$status = '审核成功';
			if (!$affectRow) {
				$status = '审核失败';
			}
		} else {
			$status = '审核失败';
		}
		header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
	}

	//不良品列表采购审核后移动到报废或者移动到内部处理）
	public function view_updateDefectiveProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$defectiveProductsAct = new DefectiveProductsAct();
		$type = isset ($_POST['type']) ? $_POST['type'] : '';
		$defectiveId = isset ($_POST['defectiveId']) ? post_check($_POST['defectiveId']) : '';
		$infoId = isset ($_POST['infoId']) ? post_check($_POST['infoId']) : '';
		$num = isset ($_POST['num']) ? post_check($_POST['num']) : ''; //传过来的数量
		$note = isset ($_POST['note']) ? post_check($_POST['note']) : '';

		if (empty ($type) || empty ($defectiveId) || empty ($infoId) || empty ($num)) { //为空时，跳转到列表页面，输出错误信息
			$status = '处理失败，信息不完整';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		$scrappedStatus = 1; //默认报废的状态为1（内部处理为2,待退回为3）
		$select = 'defectiveNum';
		$where = "WHERE id='$defectiveId' AND infoId='$infoId'";
		$defectiveProductsList = $defectiveProductsAct->act_getDefectiveProductsList($select, $where); //检测记录defectiveId和infoId是否存在
		if (empty ($defectiveProductsList)) {
			$status = '处理失败，不存在这条记录';
			header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
			exit;
		}
		if ($type == 'inter') {
			$scrappedStatus = 2;
		}
		if ($type == 'return') {
			$scrappedStatus = 3;
		}
		$return = $defectiveProductsAct->act_updateDefectiveProducts($defectiveId, $infoId, $num, $note, $scrappedStatus); //返回0或1
		$status = '处理成功';
		if ($return == 0) {
			$status = '处理失败';
		}
		header("location:index.php?mod=DefectiveProducts&act=getDefectiveProductsList&status=$status");
	}
}