<?php
class NewPreView {

	public $tp = ""; //模板

	public function __construct() {
		$htmlDir = WEB_PATH . "html/v1";
		$this->tp = new Template($htmlDir); //实例化模板引擎
	}

	//页面渲染输出
	public function view_getNewGoodsList() {
		$newGoodsArr = array ();
		if (!isset ($_SESSION['userId'])) {
			header('Location:index.php?mod=login&act=index');
		}
		$newPreViewAct = new ProductAct();
		$type = isset ($_GET['type']) ? $_GET['type'] : '';
		$sku = isset ($_GET['sku']) ? post_check($_GET['sku']) : '';
		$startdate = isset ($_GET['startdate']) ? post_check($_GET['startdate']) : '';
		$enddate = isset ($_GET['enddate']) ? post_check($_GET['enddate']) : '';
		$where = 'where isNew=1 and is_delete=0 ';
		if ($type == 'search') {
			if (!empty ($sku)) {
				$where .= "and sku like'$sku%' ";
			}
			if (!empty ($startdate)) {
				$tmpstart = strtotime($startdate . ' 00:00:00');
				$where .= "and goodsCreatedTime>=$tmpstart ";
			}
			if (!empty ($enddate)) {
				$tmpend = strtotime($enddate . ' 23:59:59');
				$where .= "and goodsCreatedTime<=$tmpend ";
			}
		}
		$total = $newPreViewAct->act_getNewGoodsListNum($where); //新品数量
		$skuCount = $total; //新品sku总数
		$spuCount = $newPreViewAct->act_getNewGoodsListNum($where . 'group by spu'); //新品spu总数
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "order by id asc " . $page->limit;
		$newGoodsArr = $newPreViewAct->act_getNewGoodsList("*", $where); //所有新品信息数组

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
		$this->tp->set_var("skuCount", $skuCount);
		$this->tp->set_var("spuCount", $spuCount);
		$this->tp->set_var("sku", $sku);
		$this->tp->set_var("show_page", $show_page);
		$this->tp->set_var("startdate", $startdate);
		$this->tp->set_var("enddate", $enddate);
		$this->tp->set_file("newGoodsArr", "newGoodsList.html");
		$this->tp->set_file("header", "header.html");
		$this->tp->set_file("footer", "footer.html");
		$this->tp->parse("header", "header");
		$this->tp->parse("footer", "footer");
		$this->tp->set_block("newGoodsArr", "list", "lists");
		$this->tp->set_var("username", $_SESSION['username']);
		if (!empty ($newGoodsArr)) {
			for ($i = 0; $i < count($newGoodsArr); $i++) {
				$spu = $newGoodsArr[$i]['spu']; //$i时的spu
				if ($i > 0) {
					if ($newGoodsArr[$i]['spu'] == $newGoodsArr[$i -1]['spu']) { //如果该spu和$i-1的spu相同，则不显示
						$spu = '';
					}
				}
				$this->tp->set_var("t_id", $newGoodsArr[$i]['id']);
				$this->tp->set_var("t_time", date("Y-m-d", $newGoodsArr[$i]['goodsCreatedTime']));
				$this->tp->set_var("spu", $spu);
				$this->tp->set_var("t_sku", $newGoodsArr[$i]['sku']);
				$this->tp->set_var("goodsName", $newGoodsArr[$i]['goodsName']);
				$this->tp->parse("lists", "list", true);
			}
		}

		$this->tp->parse("buff", "newGoodsArr");
		$this->tp->p("buff"); //输出缓存
	}

	public function view_apiTest() {
		if (!isset ($_SESSION['userId'])) {
			header('Location:index.php?mod=login&act=index');
		}
		$jsonArr = array ();
		$jsonIdCount = array ();
		$id = isset ($_POST['id']) ? $_POST['id'] : '';
		$arridCount = array_filter(explode(",", $id));
		foreach ($arridCount as $arr) {
			$idCount = explode("*", $arr);
			$id = $idCount[0];
			$count = $idCount[1];
			$jsonIdCount[$id] = $count;
		}
		$jsonArr['idCount'] = $jsonIdCount;
		$jsonArr['userId'] = $_SESSION['userId'];
		$jsonArr['type'] = '领料单';
		$jsonArr['iostoreNum'] = 'PO20130717';
		$jsonArr = json_encode($jsonArr);
		print_r($jsonArr);
	}

}
?>