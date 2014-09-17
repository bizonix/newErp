<?php


/*
 * 待退回产品的View
 */
class ReturnProductsView extends BaseView {

	//系数列表展示
	public function view_getReturnProductsList() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		//		$type = isset ($_GET['type']) ? $_GET['type'] : '';

		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$returnProductsAct = new ReturnProductsAct();

		$where = 'WHERE 1=1 ';
		$total = $returnProductsAct->act_getReturnProductsCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

		$returnProductsList = $returnProductsAct->act_getReturnProductsList('*', $where);

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
			"待退回处理列表"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '待退回处理列表');
		$this->smarty->assign('secnev', '3');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign('returnProductsList', $returnProductsList ? $returnProductsList : null);
		$this->smarty->display("returnProducts.html");
	}

	//待退回审核
	public function view_auditReturnProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$returnProductsAct = new ReturnProductsAct();

		$returnId = isset ($_GET['returnId']) ? post_check($_GET['returnId']) : '';

		if (empty ($returnId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '失败，id为空';
			header("location:index.php?mod=returnProducts&act=getReturnProductsList&status=$status");
			exit;
		}
		$now = time();
		$set = "SET returnStatus='1',auditTime='$now' ";
		$where = "WHERE id='$returnId' ";
		$affectRow = $returnProductsAct->act_updateReturnProducts($set, $where);
		if ($affectRow) {
			$status = '审核成功';
		} else {
			$status = '审核失败';
		}
		header("location:index.php?mod=returnProducts&act=getReturnProductsList&status=$status");
	}

	//打包操作
	public function view_updateReturnProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$returnProductsAct = new ReturnProductsAct();

		$returnId = isset ($_GET['returnId']) ? post_check($_GET['returnId']) : '';

		if (empty ($returnId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '失败，id为空';
			header("location:index.php?mod=returnProducts&act=getReturnProductsList&status=$status");
			exit;
		}
		$now = time();
		$set = "SET returnStatus='2',startTime='$now',lastModified='$now' ";
		$where = "WHERE id='$returnId' ";
		$affectRow = $returnProductsAct->act_updateReturnProducts($set, $where);
		if ($affectRow) {
			$status = '处理成功';
		} else {
			$status = '处理失败';
		}
		header("location:index.php?mod=ReturnProducts&act=getReturnProductsList&status=$status");
	}

}