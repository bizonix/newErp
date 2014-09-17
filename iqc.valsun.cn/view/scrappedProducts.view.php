<?php


/*
 * 报废，内部处理的View
 */
class ScrappedProductsView extends BaseView {

	//系数列表展示
	public function view_getScrappedProductsList() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		//		$type = isset ($_GET['type']) ? $_GET['type'] : '';

		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$scrappedProductsAct = new ScrappedProductsAct();

		$where = 'WHERE 1=1 ';
		$total = $scrappedProductsAct->act_getScrappedProductsCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

		$scrappedProductsList = $scrappedProductsAct->act_getScrappedProductsList('*', $where);

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
			"报废和内部处理列表"
		);
		$this->smarty->assign('navarr', $navarr);
		$this->smarty->assign('module', '报废和内部处理列表');
		$this->smarty->assign('secnev', '3');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('username', $_SESSION['userName']);
		$this->smarty->assign('scrappedProductsList', $scrappedProductsList ? $scrappedProductsList : null);
		$this->smarty->display("scrappedProducts.html");
	}

	//报废，内部处理审核
	public function view_updateScrappedProducts() {
		if (!isset ($_SESSION['userId'])) { //检测用户是否登陆
			header('location:index.php?mod=login&act=index');
			exit;
		}
		$scrappedProductsAct = new ScrappedProductsAct();

		$scrappedId = isset ($_GET['scrappedId']) ? post_check($_GET['scrappedId']) : '';

		if (empty ($scrappedId)) { //为空时，跳转到列表页面，输出错误信息
			$status = '审核失败，id为空';
			header("location:index.php?mod=scrappedProducts&act=getScrappedProductsList&status=$status");
			exit;
		}
		$now = time();
		$set = "SET scrappedStatus='1',processTime='$now' ";
		$where = "WHERE id='$scrappedId' ";
		$affectRow = $scrappedProductsAct->act_updateScrappedProducts($set, $where);
		if ($affectRow) {
			$status = '审核成功';
		} else {
			$status = '审核失败';
		}
		header("location:index.php?mod=scrappedProducts&act=getScrappedProductsList&status=$status");
	}

}