<?php


/*
 * IQC待定信息库View
 */
class PendingProductsView extends BaseView {

	//不良品列表展示
	public function view_getPendingProductsList() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		//$type = isset($_GET['type'])?$_GET['type']:'';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$pendingProductsAct = new PendingProductsAct();

		$where = 'WHERE 1=1 ';
		$total = $pendingProductsAct->act_getPendingProductsCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

		$pendingProductsList = $pendingProductsAct->act_getPendingProductsList('*', $where);

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
			"IQC待定信息列表"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', 'IQC待定信息列表');
		$this->smarty->assign('secnev', '3');
		$this->smarty->assign("show_page", $show_page);

		$this->smarty->assign("status", $status);
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign("pendingProductsList", $pendingProductsList ? $pendingProductsList : null);
		$this->smarty->display("pendingProducts.html");
	}

	//修改图片，正常回测，待退回
	public function view_updatependingProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$pendingProductsAct = new pendingProductsAct();
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$pendingId = isset ($_GET['pendingId']) ? post_check($_GET['pendingId']) : '';
		$infoId = isset ($_GET['infoId']) ? post_check($_GET['infoId']) : '';

		if (empty ($type) || empty ($pendingId) || empty ($infoId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '处理失败，信息不完整';
			header("location:index.php?mod=pendingProducts&act=getpendingProductsList&status=$status");
			exit;
		}
		$status = 1; //默认状态，状态1为修改图片，2为图片完成，3为处理完成
		$select = '*';
		$where = "WHERE id='$pendingId' AND infoId='$infoId'";
		$pendingProductsList = $pendingProductsAct->act_getPendingProductsList($select, $where); //检测记录pendingId和infoId是否存在
		if (empty ($pendingProductsList)) {
			$status = '处理失败，不存在这条记录';
			header("location:index.php?mod=pendingProducts&act=getPendingProductsList&status=$status");
			exit;
		}
		if ($type == 'back') {
			$status = 2;
		}
		if ($type == 'return') {
			$status = 3;
		}
		$return = $pendingProductsAct->act_updatependingProducts($pendingProductsList, $status); //返回0或1
		$status = '处理成功';
		if ($return == 0) {
			$status = '处理失败';
		}
		header("location:index.php?mod=pendingProducts&act=getpendingProductsList&status=$status");
	}
}