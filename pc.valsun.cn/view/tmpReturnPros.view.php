<?php
class TmpReturnProsView {

	public $tp = "";
	public function __construct() {
		//@session_start();
		$htmlDir = WEB_PATH . "html/v1";
		$this->tp = new Template($htmlDir);
	}

	public function view_tmpReturnPros() {
		if (!isset ($_SESSION['userId'])) {
			header('Location:index.php?mod=login&act=index');
		}
		$tmpReturnProsAct = new TmpReturnProsAct();
		$productsAct = new ProductsAct(); //ProductsAct
		//添加sku
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$sku = isset ($_GET['sku']) ? post_check($_GET['sku']) : '';
		if ($type == 'add') {
			if (!empty ($sku)) {
				$whereProducts = "where sku='$sku' and productsStatus=3";
				$isExsit = $productsAct->act_getProductsCount($whereProducts); //查看添加的sku是否是pc_products中状态为5的（状态为文员确认归还）
				$status = '';
				if ($isExsit > 0) { //如果存在
					$where = "where sku='$sku'";
					$now = time();
					$skuExistCount = $tmpReturnProsAct->act_getTmpReturnProsCount($where); //查看添加的sku是否在退料临时表中存在
					$flag = 0; //标识是添加成功还是由于数量导致添加失败
					if ($skuExistCount > 0) { //如果存在
						$productsCountArr = $productsAct->act_getProducts('productsCount', $whereProducts);
						$productsCount = $productsCountArr[0]['productsCount']; //该sku在products表中的数量
						$skuCountArr = $tmpReturnProsAct->act_getTmpReturnPros("count", $where); //该sku在退料临时表中的数量
						$skuCount = $skuCountArr[0]['count'] + 1;
						if ($skuCount > $productsCount) {
							$flag = 1; //改变标识变量，输出提示
							$status = '添加失败,该sku已经扫描过且数量达到上限';
						} else {
							$set = "set count='$skuCount',createdTime='$now'";
							$tmpReturnProsAct->act_updateTmpReturnPros($set, "where sku='$sku'");
						}
					} else {
						$tmpReturnProsAct->act_addTmpReturnPros("set sku='$sku',count=1,createdTime='$now'");
					}
					if ($flag == 0) {
						$status = '添加成功';
					}
				} else {
					$status = '添加失败,文员确认收到列表中找不到该料号';
				}

			}
            header('Location:index.php?mod=TmpReturnPros&act=tmpReturnPros&status='.$status);
		}
		//展示tmp表中的记录
		$select = "*";
		$where = "order by createdTime desc";
		$productsList = $tmpReturnProsAct->act_getTmpReturnPros($select, $where);
		//删除tmp中的记录
		$this->tp->set_file("productsList", "tmpReturnPros.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("link", "productsLink.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("link", "link");
		$this->tp->parse("footer", "footer");
		$this->tp->set_var("title", "退还料号临时表");
		$this->tp->set_block("productsList", "list", "lists");
		$this->tp->set_var("username", $_SESSION['username']);
		$this->tp->set_var("status", $_GET['status']);
		if (!empty ($productsList)) {
			foreach ($productsList as $products) {
				$this->tp->set_var("t_id", $products['id']);
				$this->tp->set_var("t_sku", $products['sku']);
				$this->tp->set_var("t_count", $products['count']);
				$this->tp->set_var("t_createdTime", date("Y-m-d H:m:s", $products['createdTime']));
				$this->tp->parse("lists", "list", true);
			}
		}
		$this->tp->parse("buff", "productsList");
		$this->tp->p("buff");
	}

	public function view_applyReturnBill() {
		if (!isset ($_SESSION['userId'])) {
			header('Location:index.php?mod=login&act=index');
		}
		$tmpReturnProsAct = new TmpReturnProsAct();
		$select = "*";
		$where = "";
		$productsList = $tmpReturnProsAct->act_getTmpReturnPros($select, $where);
		if (!empty ($productsList)) {
			$jsonArr = array ();
			$type = "reTurn";
			$uid = $_SESSION['userId'];
			$usename = $_SESSION['usename'];
			$jsonArr['type'] = $type;
			$jsonArr['uid'] = $uid;
			$jsonArr['usename'] = $usename;
			$jsonArr['skuList'] = $productsList;
			var_dump(json_encode($jsonArr));
		} else {
			echo 'no data';
		}
	}

	public function view_clearReturnBill() {
		if (!isset ($_SESSION['userId'])) {
			header('Location:index.php?mod=login&act=index');
		}
		$tmpReturnProsAct = new TmpReturnProsAct();
		$where = "";
		$tmpReturnProsAct->act_deleteTmpReturnPros($where);
		header("Location: index.php?mod=TmpReturnPros&act=tmpReturnPros");
	}

}