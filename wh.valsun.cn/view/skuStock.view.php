<?php


/*
 * 库存信息View
 */
class SkuStockView extends CommonView {

	//搜索页面
	public function view_searchSku() {
		
		$where = "where 1=1 ";
		//$total_cost = SkuStockModel::getAllGoodsCost($where);
		$total_cost = 38947186.735;
		$this->smarty->assign('total_cost', $total_cost[0]['totalCost']);
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
                        array('url'=>'','title'=>'货品搜索'),
                );
                
		//仓库		
		$whName = WarehouseManagementModel::warehouseManagementModelList("where companyId=1");
		$this->smarty->assign('whName', $whName);	

		//类别
		$cate_f = SkuStockModel::getCategoryInfo(0);
		
		$this->smarty->assign('cate_f', $cate_f);
        $this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '货品搜索');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '01');
		$this->smarty->display("searchSku.htm");
		
	}

	//列表展示
	public function view_getskuStockList() {
		$type 		   = isset ($_GET['type']) ? $_GET['type'] : '';
		$searchContent = isset ($_GET['searchContent']) ? $_GET['searchContent'] : '';
		$online		   = isset ($_GET['online']) ? $_GET['online'] : '';
		$warehouse 	   = isset ($_GET['warehouse']) ? $_GET['warehouse'] : '';
		$isnew		   = isset ($_GET['isnew']) ? $_GET['isnew'] : '';
		$pid_one	   = isset ($_GET['pid_one']) ? $_GET['pid_one'] : '';
		$pid_two	   = isset ($_GET['pid_two']) ? $_GET['pid_two'] : '';
		$pid_three	   = isset ($_GET['pid_three']) ? $_GET['pid_three'] : '';
		$pid_four	   = isset ($_GET['pid_four']) ? $_GET['pid_four'] : '';
	
		$skuStockAct = new SkuStockAct();
		$where = 'WHERE a.is_delete=0 ';
		if(!empty($online)){
			$where .= "AND a.goodsStatus='$online' ";
			$this->smarty->assign('online', $online);
		}
		if(!empty($warehouse)){
			$where .= "AND b.storeId='$warehouse' ";
			$this->smarty->assign('warehouse', $warehouse);
		}
		if(is_numeric($isnew)){
			$where .= "AND a.isNew='$isnew' ";
			$this->smarty->assign('isnew', $isnew);
		}
		if(!empty($pid_four)){
			$cate   = $pid_one."-".$pid_two."-".$pid_three."-".$pid_four;
			$where .= "AND a.goodsCategory='$cate' ";
			$this->smarty->assign('pid_four', $pid_four);
			$this->smarty->assign('pid_three', $pid_three);
			$this->smarty->assign('pid_two', $pid_two);
			$this->smarty->assign('pid_one', $pid_one);
			
			$cate_four = $skuStockAct->act_getCategoryInfo($pid_three);
			$this->smarty->assign('cate_four', $cate_four);
			$cate_three = $skuStockAct->act_getCategoryInfo($pid_two);
			$this->smarty->assign('cate_three', $cate_three);
			$cate_two = $skuStockAct->act_getCategoryInfo($pid_one);
			$this->smarty->assign('cate_two', $cate_two);
		}else if(!empty($pid_three)){
			$cate   = $pid_one."-".$pid_two."-".$pid_three;
			$where .= "AND a.goodsCategory='$cate' ";
			$this->smarty->assign('pid_three', $pid_three);
			$this->smarty->assign('pid_two', $pid_two);
			$this->smarty->assign('pid_one', $pid_one);
			
			$cate_three = $skuStockAct->act_getCategoryInfo($pid_two);
			$this->smarty->assign('cate_three', $cate_three);
			$cate_two = $skuStockAct->act_getCategoryInfo($pid_one);
			$this->smarty->assign('cate_two', $cate_two);
		}else if(!empty($pid_two)){
			$cate   = $pid_one."-".$pid_two;
			$where .= "AND a.goodsCategory='$cate' ";
			$this->smarty->assign('pid_two', $pid_two);
			$this->smarty->assign('pid_one', $pid_one);
			
			$cate_two = $skuStockAct->act_getCategoryInfo($pid_one);
			$this->smarty->assign('cate_two', $cate_two);
		}else if(!empty($pid_one)){
			$where .= "AND a.goodsCategory='$pid_one' ";
			$this->smarty->assign('pid_one', $pid_one);
		}
		
		switch($type){
			case '1':
				$spuinfo = OmAvailableModel::getTNameList("pc_goods","sku","where spu='$searchContent'");
				if(empty($spuinfo)){
					$skuinfo = get_realskuinfo($searchContent);
					foreach($skuinfo as $sku=>$num){
						$sku_str .= "'".$sku."',";
					}
					$sku_str = trim($sku_str,',');
					$where .= "AND a.sku in (".$sku_str.") ";
				}else{
					foreach($spuinfo as $info){
						$sku_str .= "'".$info['sku']."',";
					}
					$sku_str = trim($sku_str,',');
					$where .= "AND a.sku in (".$sku_str.") ";
				}
				break;
			case '2':
				$positionIds = SkuStockModel :: getAllPositionIdByPName($searchContent);
				$pos = '';
				if(!empty($positionIds)){
					foreach($positionIds as $positionId){
						$pos .= $positionId['id'].",";
					}
				}else{
					$pos = 0;
				}
				$pos = trim($pos,',');
				$where .= "AND b.positionId in($pos) ";
				break;
			case '3':
				$where .= "AND a.goodsName='$searchContent' ";
				break;
			case '4':
				$purchaseId = getUserIdByName($searchContent);
				if(empty($purchaseId)){$purchaseId = 10000000;}
				$where .= "AND a.purchaseId='$purchaseId' ";
				break;
			case '5':
				$str  = '';
				$Supplier_id = CommonModel::getPartnerByName($searchContent);
				$paramArr['method'] = 'pc.getSkuByPartnerId';  //API名称
				$paramArr['pid'] 	= $Supplier_id;
				$Supplier_info 		= UserCacheModel::callOpenSystem($paramArr);
				if(!empty($Supplier_info['data'])){
					foreach($Supplier_info['data'] as $info){
						$str .=  "'".$info."',";
					}
					$str = trim($str,",");
					$str = "(".$str.")";
				}else{
					$str = "('no')";
				}
				$where .= "AND a.sku in $str ";
				break;			
			default:
				break;
		}
		
		$where .= "GROUP BY a.sku ";
		$total = $skuStockAct->act_getSkuStockCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY a.sku " . $page->limit;

		$skuStockList = $skuStockAct->act_getSkuStockList($where);
		if (!empty ($_GET['page'])) {
			if (intval($_GET['page']) <= 1 || intval($_GET['page']) > ceil($total / $num)) {
				$n = 1;
			} else {
				$n = (intval($_GET['page']) - 1) * $num +1;
			}
		} else {
			$n = 1;
		}
		if($total>$num){
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else{
			$show_page = $page->fpage(array(0,2,3));
		}
		$navlist = array(array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'库存管理'),              //面包屑数据
                        array('url'=>'index.php?mod=skuStock&act=searchSku','title'=>'货品搜索'),
                );
			
		$usermodel = UserModel::getInstance();
		$count = count($skuStockList);
		for ($i = 0; $i < $count; $i++) {
			//$skuStockList[$i]['category'] = CommonModel::getCateInfoByPath($skuStockList[$i]['goodsCategory']);      //类别
			$user_info = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$skuStockList[$i]['purchaseId']}'",'','limit 1');
			$skuStockList[$i]['pName'] = $user_info[0]['global_user_name'];
			
			//类别
			$skuStockList[$i]['cateName'] = SkuStockModel::getCategoryInfoByPath($skuStockList[$i]['goodsCategory']);
			
			//获取供应商
			$par['method']  = 'pc.getPartnerIdBySku';  //API名称
			$par['sku'] 	= $skuStockList[$i]['sku'];
			$sku_pname 		= UserCacheModel::callOpenSystem($par);
			if(!empty($sku_pname['data'])){
				$purchase = CommonModel::getPartnerByID($sku_pname['data']);
				$skuStockList[$i]['PartnerName'] = $purchase['username'];
			}
			
			//仓库
			if(!empty($skuStockList[$i]['storeId'])){
				$whName_info = WarehouseManagementModel::warehouseManagementModelList("where companyId=1 and id={$skuStockList[$i]['storeId']}");
				$skuStockList[$i]['whName'] = $whName_info[0]['whName'];
			}

			//图片		
			//$picUrl = getPicFromOpenSys($skuStockList[$i]['sku']);
			//$skuStockList[$i]['picUrl'] = $picUrl;
			
		}

		//仓库		
		$whName = WarehouseManagementModel::warehouseManagementModelList("where companyId=1");
		$this->smarty->assign('whName', $whName);	

		//类别
		$cate_f = SkuStockModel::getCategoryInfo(0);
		$this->smarty->assign('cate_f', $cate_f);

		$this->smarty->assign('type', $type);
		$this->smarty->assign('searchContent', $searchContent);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '库存信息列表');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', '01');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('skuStockList', $skuStockList ? $skuStockList : null); //循环列表
		$this->smarty->display("skuStock.htm");
	}

	//导入excel更新页面
	function view_scanUpdateBatchPosition() {
			$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=skuStock&act=getSkuStockList',
				'title' => '仓库'
			),
			array (
				'url' => 'index.php?mod=skuStock&act=getSkuStockList',
				'title' => '库存信息列表'
			),
			array (
				'url' => '',
				'title' => '批量更新仓位'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '批量更新仓位');
		$this->smarty->assign('toplevel', 0);
		$this->smarty->assign('secondlevel', 6);
		$this->smarty->display("whBatchUpdate.htm");
	}

	//批量更新仓位
	function view_updateBatchPosition() {
		$uploadfile = date("Y") . date("m") . date("d") . rand(1, 3009) . ".xls";
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (move_uploaded_file($_FILES['upfile']['tmp_name'], 'upload/' . $uploadfile)) {
				$status = "<font color='green'>文件上传成功！</font><br />";
				$fileName = 'upload/' . $uploadfile;
				$filePath = $fileName;
				require_once "../lib/PHPExcel.php";
				$PHPExcel = new PHPExcel();
				$PHPReader = new PHPExcel_Reader_Excel2007();
				if (!$PHPReader->canRead($filePath)) {
					$PHPReader = new PHPExcel_Reader_Excel5();
					if (!$PHPReader->canRead($filePath)) {
						echo 'no Excel';
						return;
					}
				}
				$PHPExcel = $PHPReader->load($filePath);
				$currentSheet = $PHPExcel->getSheet(0);
				/**取得一共有多少行*/
				$allRow = $currentSheet->getHighestRow();

				$skuStockAct = new SkuStockAct();

				/**从第二行开始输出，因为excel表中第一行为列名*/
				for ($c = 2; $c <= $allRow; $c++) {
					$aa = 'A' . $c; //sku
					$bb = 'B' . $c; //所在仓库
					$cc = 'C' . $c; //旧仓位
					$dd = 'D' . $c; //新仓位

					$sku = trim($currentSheet->getCell($aa)->getValue());
					$whName = trim($currentSheet->getCell($bb)->getValue());
					$oldPosition = trim($currentSheet->getCell($cc)->getValue());
					$newPosition = trim($currentSheet->getCell($dd)->getValue());

					if (empty ($sku) || empty ($whName) || empty ($oldPosition) || empty ($newPosition)) {
						echo "<font color='red'>第 $c 行 存在空列，该行更新失败</font><br />";
						continue;
					}
					$sku = str_pad(trim($sku), 3, '0', STR_PAD_LEFT);
					$tName = 'pc_goods';
					$select = 'id';
					$where = "WHERE sku='$sku'";
					$skuList = $skuStockAct->act_getTNameList($tName, $select, $where);
					if (empty ($skuList)) {
						echo "<font color='red'>第 $c 行 sku $sku 不存在，该行更新失败</font><br />";
						continue;
					}
					$skuId = $skuList[0]['id']; //wh_product_position_relation中的pid

					$tName = 'wh_store';
					$select = 'id';
					$where = "WHERE whName='$whName'";
					$whStoreList = $skuStockAct->act_getTNameList($tName, $select, $where);
					if (empty ($whStoreList)) {
						echo "<font color='red'>第 $c 行 仓库 $whName 不存在，该行更新失败</font><br />";
						continue;
					}
					$whStoreId = $whStoreList[0]['id'];

					$tName = 'wh_position_distribution';
					$select = 'id';
					$where = "WHERE pName='$oldPosition'";
					$positionList = $skuStockAct->act_getTNameList($tName, $select, $where);
					if (empty ($positionList)) {
						echo "<font color='red'>第 $c 行 旧仓位号 $oldPosition 不存在，该行更新失败</font><br />";
						continue;
					}
					$oldPositionId = $positionList[0]['id'];

					$tName = 'wh_position_distribution';
					$select = 'id';
					$where = "WHERE pName='$newPosition'";
					$positionList = $skuStockAct->act_getTNameList($tName, $select, $where);
					if (empty ($positionList)) {
						echo "<font color='red'>第 $c 行 新仓位号 $newPosition 不存在，该行更新失败</font><br />";
						continue;
					}
					$newPositionId = $positionList[0]['id'];

					$tName = 'wh_product_position_relation';
					$where = "WHERE is_delete=0 AND storeId='$whStoreId' AND positionId='$newPositionId'";
					$count = $skuStockAct->act_getTNameCount($tName, $where);
					if ($count > 0) {
						echo "<font color='red'>第 $c 行 新仓位 $newPosition 已经被占用，更新失败！</font><br/>";
						continue;
					}

					$tName = 'wh_product_position_relation';
					$set = "SET positionId='$newPositionId'";
					$where = "WHERE pid='$skuId' AND positionId='$oldPositionId' AND storeId='$whStoreId'";
					$affectRows = $skuStockAct->act_updateTNameRow($tName, $set, $where);
					if (!$affectRows) {
						echo "<font color='orange'>第 $c 行 无数据修改，更新失败！</font><br/>";
						continue;
					} else {
						echo "<font color='green'>第 $c 行 ，sku：$sku 更新成功！旧仓位为：$oldPosition ，新仓位为：$newPosition</font><br/>";
					}

				}
				/*============================================  end  =============================================================*/
				unlink($filePath); //删除导入文件
			}
			echo "<font color='red'> 文件上传失败！</font><br />";
		} else {
			echo "<font color='red'> 文件上传失败！</font><br />";
		}
	}
}