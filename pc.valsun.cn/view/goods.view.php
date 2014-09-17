<?php
class GoodsView extends baseView {

	//页面渲染输出
	public function view_getGoodsList() {
		//调用action层， 获取列表数据
		$GoodsAct = new GoodsAct();
		$flag = false;
		$userId = $_SESSION['userId'];
		$seachdata = isset ($_GET['seachdata']) ? post_check($_GET['seachdata']) : '';
		$searchs = isset ($_GET['searchs']) ? $_GET['searchs'] : '';
		$isNew = isset ($_GET['isNew']) ? $_GET['isNew'] : '';
		$pid = isset ($_GET['pid']) ? $_GET['pid'] : '';
		$purchaseId = isset ($_GET['purchaseId']) ? $_GET['purchaseId'] : '';
		$goodsStatus = intval(isset ($_GET['goodsStatus']) ? $_GET['goodsStatus'] : '');
		$where = 'where is_delete=0 ';
		if (intval($searchs) != 0 && !empty ($seachdata)) {
			if ($searchs == 1) {
				$where .= "and spu='$seachdata' ";
				$flag = true;
			}
			elseif ($searchs == 2) {
				$where .= "and sku='$seachdata' ";
				$flag = true;
			}
		}
		if ($isNew) {
			if ($isNew == 1) {
				$where .= "and isNew='1' ";
				$flag = true;
			}
			elseif ($isNew == 2) {
				$where .= "and isNew='0' ";
				$flag = true;
			}
		}
		if ($pid) {
			//$where .= "and goodsCategory like'$pid%' ";//有bug
			$where .= "AND goodsCategory REGEXP '^$pid(-[0-9]+)*$' ";
			$flag = true;
		}
		if (intval($purchaseId) > 0) {
			$where .= "and purchaseId='$purchaseId' ";
			$flag = true;
		}
		if ($goodsStatus > 0) {
			$where .= "and goodsStatus='$goodsStatus' ";
			$flag = true;
		}

		$total = $GoodsAct->act_getGoodsListNum($where);

		$num = 50; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$select = '*';
		$where .= "order by sku desc " . $page->limit;

		if ($flag == false) {
			$productList = $GoodsAct->act_getGoodsListNum('WHERE 1=0');
		} else {
			$productList = $GoodsAct->act_getGoodsList($select, $where);
		}
		if (!empty ($productList)) {
			$countPro = count($productList);
			for ($i = 0; $i < $countPro; $i++) {
				if ($productList[$i]['purchaseId'] == $userId) { //登陆人员和采购是同一个人的时候，才显示供应商
					$productList[$i]['supplier'] = OmAvailableModel :: getParterNameBySku($productList[$i]['sku']);
				}
                
                if($i > 0 && $productList[$i]['spu'] == $productList[$i-1]['spu']){
                    $productList[$i]['visibleSpu'] = '';
                }else{
                    $productList[$i]['visibleSpu'] = $productList[$i]['spu'];
                }

			}
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
		$pidArr = explode('-', $pid);
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息'
			),
			array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息列表'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 11);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '产品列表');
		$this->smarty->assign('pidArr', $pidArr);
		$this->smarty->assign('productList', empty ($productList) ? array () : $productList);
		$this->smarty->display("goodsList.htm");
	}

	public function view_updateSkuSing() {
		$id = $_GET['id'] ? post_check(trim($_GET['id'])) : '';
		//检查spu是否非法
		$tName = 'pc_goods';
		$select = '*';
		$where = "WHERE id=$id and is_delete=0";
		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($skuList)) {
			$status = "SKU不存在";
			header("Location:index.php?mod=goods&act=getGoodsList&status=$status");
			exit;
		}
		$spu = $skuList[0]['spu'];
		$tName = 'pc_archive_spu_property_value_relation';
		$select = '*';
		$where = "WHERE spu='$spu'";
		$PPV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_input_value_relation';
		$INV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_link';
		$Link = OmAvailableModel :: getTNameList($tName, $select, $where);

        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息'
			),
			array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息列表'
			),
			array (
				'url' => "index.php?mod=goods&act=updateSkuSing&id=$id",
				'title' => "修改产品信息_{$skuList[0]['sku']}"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 11);
		$this->smarty->assign('title', 'SKU修改');
		$this->smarty->assign('id', $skuList[0]['id']);
		$this->smarty->assign('spu', $skuList[0]['spu']);
		$this->smarty->assign('sku', $skuList[0]['sku']);
		$this->smarty->assign('pid', $skuList[0]['goodsCategory']);
		$this->smarty->assign('goodsName', $skuList[0]['goodsName']);
		$this->smarty->assign('goodsCost', $skuList[0]['goodsCost']);
		$this->smarty->assign('goodsWeight', $skuList[0]['goodsWeight']);
		$this->smarty->assign('goodsNote', $skuList[0]['goodsNote']);
		$this->smarty->assign('goodsLength', $skuList[0]['goodsLength']);
		$this->smarty->assign('goodsWidth', $skuList[0]['goodsWidth']);
		$this->smarty->assign('goodsHeight', $skuList[0]['goodsHeight']);
		$this->smarty->assign('goodsColor', $skuList[0]['goodsColor']);
		$this->smarty->assign('goodsStatus', $skuList[0]['goodsStatus']);
		$this->smarty->assign('purchaseId', $skuList[0]['purchaseId']);
		$this->smarty->assign('pmId', $skuList[0]['pmId']);
		$this->smarty->assign('isPacking', $skuList[0]['isPacking']);
		$this->smarty->assign('goodsSize', $skuList[0]['goodsSize']);
		$this->smarty->assign('pmCapacity', $skuList[0]['pmCapacity']);
		$this->smarty->assign('isNew', $skuList[0]['isNew']);

		$pathImplodeStr = getAllPathBypid($skuList[0]['goodsCategory']);
		$this->smarty->assign('pathImplodeStr', $pathImplodeStr);
		$this->smarty->assign('PPV', $PPV);
		$this->smarty->assign('INV', $INV);
		$this->smarty->assign('Link', $Link);
		if ($skuList[0]['isNew'] == 1) { //是新品的话，不用加载图片
			$spuPicList = array ();
		} else {
			$spuPicList = getAllArtPicFromOpenSysSpu($skuList[0]['spu']);
		}
		$this->smarty->assign('spuPicList', $spuPicList);
		$this->smarty->display("updateSkuSing.htm");
	}

	public function view_updateSkuSingOn() {
		$id = $_POST['id'] ? post_check(trim($_POST['id'])) : '';
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
		$pid = $_POST['pid'] ? post_check(trim($_POST['pid'])) : '';
		$sku = $_POST['sku'] ? post_check(trim($_POST['sku'])) : '';
		$goodsName = $_POST['goodsName'] ? trim($_POST['goodsName']) : '';
		$goodsCost = $_POST['goodsCost'] ? post_check(trim($_POST['goodsCost'])) : '';
		$goodsNote = $_POST['goodsNote'] ? trim($_POST['goodsNote']) : '';
		$goodsStatus = $_POST['goodsStatus'] ? post_check(trim($_POST['goodsStatus'])) : '';
		$isNew = $_POST['isNew'] ? post_check(trim($_POST['isNew'])) : '';
		$goodsColor = $_POST['goodsColor'] ? post_check(trim($_POST['goodsColor'])) : '';
		$goodsSize = $_POST['goodsSize'] ? post_check(trim($_POST['goodsSize'])) : '';
        $userId = $_SESSION['userId'];
		if (intval($id) == 0) {
			$status = "非法id";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
        if (intval($userId) == 0) {
			$status = "登陆超时，请重新登陆";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
		if (empty ($pid)) {
			$status = "类别为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
		if (empty ($sku)) {
			$status = "sku为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
		if (empty ($goodsName)) {
			$status = "描述不能为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
        if (strpos($goodsName, '#') !== false || strpos($goodsNote, '#') !== false) {//如果描述中有#则报错
			$status = "$sku 的描述/备注 不能含有'井'号等特殊字符";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
		if (!is_numeric($goodsCost) || $goodsCost <= 0) {
			$status = "成本必须为正数";
			echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
            exit;
		}
		$tName = 'pc_goods';
        $select = 'goodsCost,goodsStatus';
		$where = "WHERE sku='$sku' and id=$id";
		$skuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty($skuList)) {
			$status = "系统中不存在 $sku";
			header("Location:index.php?mod=goods&act=getGoodsList&status=$status");
			exit;
		}
        try {
            BaseModel::begin();
    		$dataSku = array ();
    
    		$dataSku['goodsCategory'] = $pid;
    		$dataSku['goodsName'] = $goodsName;
            if($goodsName == '无'){//删除料号
                $status = "系统已经不允许删除料号了！";
                echo $status;
    			exit;
            }
            if($goodsCost != $skuList[0]['goodsCost']){//成本变化时
                $dataSku['goodsCost'] = $goodsCost;
                addCostBackupsModify($sku, $goodsCost, $userId);//添加成本历史记录
            }	
    		$dataSku['goodsNote'] = $goodsNote;
    		$dataSku['goodsStatus'] = $goodsStatus;
            if($goodsStatus != $skuList[0]['goodsStatus']){//状态变化时
                $dataSku['goodsUpdateTime'] = time();               
                $reason = $_POST['reason'] ? post_check(trim($_POST['reason'])) : '';
                addStatusBackupsModify($sku, $goodsStatus, $reason, $userId);//添加状态改变记录
            }
    		$dataSku['isNew'] = $isNew;
    		$dataSku['goodsColor'] = $goodsColor;
    		$dataSku['goodsSize'] = $goodsSize;
		
			$tName = 'pc_goods';
			$where = "WHERE id=$id";
			OmAvailableModel :: updateTNameRow2arr($tName, $dataSku, $where);
            $dataSkuArray2Sql = array2sql($dataSku);
            $addUserName = getPersonNameById($userId);
            error_log(date('Y-m-d_H:i')."——$sku $dataSkuArray2Sql by $addUserName \r\n",3,WEB_PATH."log/updateSkuSingLog.txt");
			//更新mem中的sku
            $tName = 'pc_goods';
            $select = '*';
            $where = "WHERE id=$id";
            $memInfo = OmAvailableModel::getTNameList($tName, $select, $where);
            $memInfo = $memInfo[0];
			$key = 'pc_goods_' . $sku;
			$value = $memInfo;
			setMemNewByKey($key, $value); //这里不保证能添加成功			
			//同步新数据到旧系统中
			$ebayGoods = array ();
			$ebayGoods['goods_id'] = $id;
			$ebayGoods['goods_name'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$goodsName);
			$ebayGoods['goods_sn'] = $sku;
			$ebayGoods['goods_price'] = $goodsCost;
			$ebayGoods['goods_cost'] = $goodsCost;
			$ebayGoods['goods_note'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$goodsNote);
			$ebayGoods['is_new'] = $isNew;            
			if($goodsStatus == 1){//在线
                $ebayGoods['isuse'] = 0;
            }elseif($goodsStatus == 51){//PK产品
                $ebayGoods['isuse'] = 51;
            }elseif($goodsStatus == 2){//停售
                $ebayGoods['isuse'] = 1;
            }elseif($goodsStatus == 3){//暂时停售
                $ebayGoods['isuse'] = 3;
            }else{//其余的都做下线处理
                $ebayGoods['isuse'] = 1;
            }            
            $ebayGoods['color'] = $goodsColor;
            $ebayGoods['size'] = $goodsSize;
			$res = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.updateGoods', $ebayGoods, 'gw88');
            BaseModel :: commit();
			BaseModel :: autoCommit();            
            $status = "$sku 修改成功";
            if(!empty($statusDel)){
               $status = $statusDel;
            }
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-2);
                  </script>';
            exit;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			echo $e->getMessage();
		}
	}

	public function view_getCombineList() {
		//调用action层， 获取列表数据
		$searchComField = isset ($_GET['searchComField']) ? post_check($_GET['searchComField']) : '';
        $fieldValue = isset ($_GET['fieldValue']) ? post_check($_GET['fieldValue']) : '';
        
        $flag = false;
		$tName = 'pc_goods_combine';
		$where = 'where is_delete=0 ';
		if(!empty($fieldValue)){
    		  if (intval($searchComField) == 1) {
    			$where .= "and combineSpu='$fieldValue' ";
                $flag = true;
    		}elseif(intval($searchComField) == 2){
    		    $tmpTName = 'pc_sku_combine_relation';
                $tmpSelect = 'combineSku';
                $tmpWhere = "WHERE sku REGEXP '^$fieldValue(_[A-Z0-9]+)*$'";
                $combineSkuList = OmAvailableModel::getTNameList($tmpTName, $tmpSelect, $tmpWhere);
                $combineSkuArr = array();
                foreach($combineSkuList as $value){
                    if(!empty($value['combineSku'])){
                        $combineSkuArr[] = "'".$value['combineSku']."'";
                    }
                }
                if(!empty($combineSkuArr)){
                    $combineSkuStr = implode(',', $combineSkuArr);
                    $where .= "and combineSku in($combineSkuStr) ";
                    $flag = true;
                }else{
                    $where .= "and 1=0 ";
                    $flag = true;
                }
    		}
		}
		$total = OmAvailableModel :: getTNameCount($tName, $where);
		$num = 50; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "order by combineSpu desc " . $page->limit;
		$select = '*';
        if ($flag == false) {
			$combineList = array();
		} else {
			$combineList = OmAvailableModel :: getTNameList($tName, $select, $where);
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
        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息'
			),
			array (
				'url' => 'index.php?mod=goods&act=getCombineList',
				'title' => '虚拟料号管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 12);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '虚拟料号列表');
		if (!empty ($combineList)) {
			$countCombineList = count($combineList);
			for ($i = 0; $i < $countCombineList; $i++) {
                if($i > 0 && $combineList[$i]['combineSpu'] == $combineList[$i-1]['combineSpu']){
                    $combineList[$i]['visibleSpu'] = '';
                }else{
                    $combineList[$i]['visibleSpu'] = $combineList[$i]['combineSpu'];
                }
             
				$combineSku = $combineList[$i]['combineSku'];
				$cwArr = getTrueCWForCombineSku($combineSku); //根据combineSku返回真实的成本及重量
				$combineList[$i]['totalCost'] = $cwArr['totalCost'];
				$combineList[$i]['totalWeight'] = $cwArr['totalWeight'];
                
                $trueSkuList = OmAvailableModel::getTrueSkuForCombine($combineSku);//获取真实料号数组
                
                $combineList[$i]['trueSkuListCount'] = count($trueSkuList);
                for($j=0;$j<$combineList[$i]['trueSkuListCount'];$j++){
                    $tName = 'pc_goods';
                    $select = 'goodsName';
                    $where = "WHERE sku='{$trueSkuList[$j]['sku']}'";
                    $goodsNameList = OmAvailableModel::getTNameList($tName, $select, $where);
                    $goodsName = $goodsNameList[0]['goodsName'];
                    $trueSkuList[$j]['goodsName'] = $goodsName;
                }
                $combineList[$i]['trueSkuList'] = $trueSkuList;
			}
		}
		$this->smarty->assign('combineList', empty ($combineList) ? null : $combineList);
		$this->smarty->display("combineList.htm");
	}

	public function view_updateCombine() {
		$id = $_GET['id'] ? post_check(trim($_GET['id'])) : '';
		//检查spu是否非法
		$tName = 'pc_goods_combine';
		$select = '*';
		$where = "WHERE id=$id and is_delete=0";
		$combineList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($combineList)) {
			$status = "SKU不存在";
			header("Location:index.php?mod=goods&act=getCombineList&status=$status");
			exit;
		};
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=goods&act=getGoodsList',
				'title' => '产品信息'
			),
			array (
				'url' => 'index.php?mod=goods&act=getCombineList',
				'title' => '虚拟料号管理'
			),
			array (
				'url' => "index.php?mod=goods&act=updateCombine&id=$id",
				'title' => "修改虚拟料号_{$combineList[0]['combineSku']}"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 12);
		$this->smarty->assign('title', '虚拟料号修改');
		$this->smarty->assign('combine', $combineList[0]);
		$this->smarty->display("updateCombine.htm");
	}

	public function view_updateCombineOn() {
		$id = $_POST['id'] ? post_check(trim($_POST['id'])) : '';
		$combineSpu = $_POST['combineSpu'] ? post_check(trim($_POST['combineSpu'])) : '';
		$combineSku = $_POST['combineSku'] ? post_check(trim($_POST['combineSku'])) : '';
		$combineLength = $_POST['combineLength'] ? post_check(trim($_POST['combineLength'])) : '';
		$combineWidth = $_POST['combineWidth'] ? post_check(trim($_POST['combineWidth'])) : '';
		$combineHeight = $_POST['combineHeight'] ? post_check(trim($_POST['combineHeight'])) : '';
		$combineNote = $_POST['combineNote'] ? post_check(trim($_POST['combineNote'])) : '';
		$skuArr = $_POST['sku'];
		$countArr = $_POST['count'];

        if (empty($combineSpu)) {
			$status = "combineSpu为空";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
		if (!empty ($combineLength)) {
			if (!is_numeric($combineLength) || $combineLength < 0) {
				$status = "长度必须为数字";
                echo '<script language="javascript">
                        alert("'.$status.'");
                        history.go(-1);
                      </script>';
				exit;
			}
		}
		if (!empty ($combineWidth)) {
			if (!is_numeric($combineWidth) || $combineWidth < 0) {
				$status = "宽度必须为数字";
                echo '<script language="javascript">
                        alert("'.$status.'");
                        history.go(-1);
                      </script>';
				exit;
			}
		}
		if (!empty ($combineHeight)) {
			if (!is_numeric($combineHeight) || $combineHeight < 0) {
				$status = "高度必须为数字";
                echo '<script language="javascript">
                        alert("'.$status.'");
                        history.go(-1);
                      </script>';
				exit;
			}
		}
		//检查spu是否非法
		if (intval($id) == 0) {
			$status = "非法id";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
		if (empty ($combineSku)) {
			$status = "combineSku为空";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
        if (!preg_match("/^$combineSpu(_[A-Z0-9]+)*$/", $combineSku)) {
			$status = "combineSku为空";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
        $tName = 'pc_goods_combine';
        $where = "WHERE is_delete=0 AND combineSku='$combineSku' AND id<>$id";//检测新sku是否已经存在
        $countNewSku = OmAvailableModel::getTNameCount($tName, $where);
        if($countNewSku){
            $status = "料号 $combineSku 已经存在";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
        }
		if (empty ($skuArr[0]) || empty ($countArr[0])) {
			$status = "至少要包含一条真实料号对应记录";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
		$tName = 'pc_goods_combine';
        $select = 'combineSku';
		$where = "WHERE id=$id and is_delete=0";
		$oldComSkuList = OmAvailableModel :: getTNameList($tName, $select, $where);
        $oldComSku = $oldComSkuList[0]['combineSku'];
		if (empty($oldComSku)) {
			$status = "料号 $oldComSku 不存在";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		};
		$count = count($skuArr);
		$countFlip = count(array_flip($skuArr));
		if ($count != $countFlip) {
			$status = "存在重复的真实料号记录";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
		}
		$tName = 'pc_goods';
		foreach ($skuArr as $value) {
			if (!empty ($value)) {
				$where = "WHERE sku='$value' and is_delete=0";
				$count = OmAvailableModel :: getTNameCount($tName, $where);
				if (!$count) {
					$status = "真实料号 $value 不存在";
                    echo '<script language="javascript">
                            alert("'.$status.'");
                            history.go(-1);
                          </script>';
					exit;
				}
			}
		}
		$dataCom = array ();
		$dataCom['combineSku'] = $combineSku;
		$dataCom['combineLength'] = $combineLength;
		$dataCom['combineWidth'] = $combineWidth;
		$dataCom['combineHeight'] = $combineHeight;
		$dataCom['combineNote'] = $combineNote;
		try {
			BaseModel :: begin();
			$tName = 'pc_goods_combine';
			$where = "WHERE id=$id";
			$affectRow = OmAvailableModel :: updateTNameRow2arr($tName, $dataCom, $where);
			if ($affectRow === false) {
				$status = "更新失败";
				throw new Exception('update combine error');
			}
			$dataRelation = array ();
			$dataRelationMem = array (); //用来更新mem中的detail数组
			for ($i = 0; $i < count($skuArr); $i++) {
				if (!empty ($skuArr[$i]) && !empty ($countArr[$i])) {
					$dataRelation[] = array (
						'combineSku' => $combineSku,
						'sku' => $skuArr[$i],
						'count' => $countArr[$i]
					);
					$dataRelationMem[] = array (
						'sku' => $skuArr[$i],
						'count' => $countArr[$i]
					);
				}
			}
			if (!empty ($dataRelation)) {
				$tName = 'pc_sku_combine_relation';
				$where = "WHERE combineSku='$combineSku'";
				$affectRow = OmAvailableModel :: deleteTNameRow($tName, $where);
				if ($affectRow === false) {
					throw new Exception('delete combine relation error');
				}
				foreach ($dataRelation as $value) {
					$insertId = OmAvailableModel :: addTNameRow2arr($tName, $value);
					if ($insertId === false) {
						throw new Exception('add combine relation error');
					}
				}
			}
			//将新添加的sku添加到mem中
			$key = 'pc_goods_combine_' . $combineSku;
			$dataCom['combineSpu'] = $combineSpu;
			$dataCom['detail'] = $dataRelationMem;
			$value = $dataCom;
            setMemNewByKey('pc_goods_combine_'.$oldComSku, '');//旧的设为空
			setMemNewByKey($key, $value); //这里不保证能添加成功

			BaseModel :: commit();
			BaseModel :: autoCommit();

			//同步新数据到旧系统中
			$ebayProductsCombine = array ();
			$ebayProductsCombine['id'] = $id;
            $ebayProductsCombine['old_goods_sn'] = $oldComSku;
			$ebayProductsCombine['goods_sn'] = $combineSku;
			$goods_sncombine = array ();
			$truesku = array ();
			foreach ($dataRelationMem as $value) {
				$str = '';
				$strTrue = '';
				$str = $value['sku'] . '*' . $value['count'];
				$strTrue = '[' . $value['sku'] . ']';
				$goods_sncombine[] = $str;
				$truesku[] = $strTrue;
			}
			$ebayProductsCombine['goods_sncombine'] = implode(',', $goods_sncombine);
			$ebayProductsCombine['notes'] = $combineNote;
			$ebayProductsCombine['ebay_user'] = 'vipchen';
			$ebayProductsCombine['createdtime'] = time();
			$ebayProductsCombine['truesku'] = implode(',', $truesku);
            $ret = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.updateCombine2', $ebayProductsCombine, 'gw88');
            $status = "$combineSku 更新成功";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    history.go(-1);
                  </script>';
			exit;
						
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			$status = "更新失败 ".$e->getMessage();
            echo '<script language="javascript">
                        alert("'.$status.'");
                        history.go(-1);
                        </script>';
		}
	}

	public function view_addCombineOn() {
		$combineSpu = $_POST['combineSpu'] ? post_check(trim($_POST['combineSpu'])) : '';
		if (empty ($combineSpu)) {
			$status = "空的SPU";
			echo '<script language="javascript">
                    alert("'.$status.'");      
                  </script>';
			exit;
		}
        if(intval($_SESSION['userId']) <= 0){
            $status = "登陆超时！";
            echo '<script language="javascript">
                    alert("'.$status.'");      
                  </script>';
			exit;
        }
		$tName = 'pc_auto_create_spu';
		$select = 'status';
		$where = "WHERE spu='$combineSpu' and is_delete=0";
		$autoSpuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($autoSpuList)) {
			$status = "自动生成SPU列表中不存在 $combineSpu";
			echo '<script language="javascript">
                    alert("'.$status.'");      
                  </script>';
			exit;
		}
		$amount = $_POST['amount'] ? post_check(trim($_POST['amount'])) : 0;
		$amount = intval($amount);
		if ($amount <= 0 || $amount > 500) {
			$status = "数量必须在1-500之间";
			echo '<script language="javascript">
                    alert("'.$status.'");      
                  </script>';
			exit;
		}
        $tName = 'pc_goods_combine';
        $where = "WHERE is_delete=0 AND combineSpu='$combineSpu'";
        $countComSpu = OmAvailableModel::getTNameCount($tName, $where);
        $tmpArr = array();
        for($index = 0; $index <= $amount; $index++){
			$combineSku = $_POST['combineSku' . $index] ? post_check(trim($_POST['combineSku' . $index])) : '';                
            $combineSku = $combineSpu . $combineSku;              
            if($index == 0 && $countComSpu > 0 && $combineSku == $combineSpu){
                $status = "存在为空的子料号，请检查！";
                echo '<script language="javascript">
                        alert("'.$status.'");      
                      </script>';
				exit;
            }
            if($index > 0 && $combineSku == $combineSpu){//只可能index=0时候，$combineSku==$combineSpu，其他表示无效
                continue;
            }
			$combineLength = $_POST['combineLength' . $index] ? post_check(trim($_POST['combineLength' . $index])) : '';
			$combineWidth = $_POST['combineWidth' . $index] ? post_check(trim($_POST['combineWidth' . $index])) : '';
			$combineHeight = $_POST['combineHeight' . $index] ? post_check(trim($_POST['combineHeight' . $index])) : '';
			$combineNote = $_POST['combineNote' . $index] ? trim($_POST['combineNote' . $index]) : '';
			$skuArr = $_POST['sku' . $index];
			$countArr = $_POST['count' . $index];
            if(strlen($combineSku) > 30){
                $status = "$combineSku 字符长度大于30，错误！";
				echo '<script language="javascript">
                        alert("'.$status.'");      
                      </script>';
				exit;
            }
			if (!preg_match("/^$combineSpu(_[A-Z0-9]+)*$/", $combineSku)) {
				$status = "$combineSku 不规范，请检查格式是否正确";
                echo '<script language="javascript">
                        alert("'.$status.'");      
                      </script>';
				exit;
			}
			if ($combineSku == $combineSpu && $amount > 1) {
				$status = "存在SKU为空的参数";
                echo '<script language="javascript">
                        alert("'.$status.'");
                      </script>';
				exit;
			}
            
            $tmpArr[] = $combineSku;            
			if (!empty ($combineLength)) {
				if (!is_numeric($combineLength) || $combineLength < 0) {
					$status = "$combineSku 长度必须为数字";
                    echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}
			}
			if (!empty ($combineWidth)) {
				if (!is_numeric($combineWidth) || $combineWidth < 0) {
					$status = "$combineSku 宽度必须为数字";
                    echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}
			}
			if (!empty ($combineHeight)) {
				if (!is_numeric($combineHeight) || $combineHeight < 0) {
					$status = "$combineSku 高度必须为数字";
                    echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}
			}
			if (empty ($skuArr[0]) || empty ($countArr[0])) {
				$status = "$combineSku 至少要包含一条不为空的真实料号对应记录";
                echo '<script language="javascript">
                        alert("'.$status.'");
                      </script>';
				exit;
			}

			$tName = 'pc_goods_combine';
			$where = "WHERE combineSku='$combineSku' and is_delete=0";
			$count = OmAvailableModel :: getTNameCount($tName, $where);
			if ($count) {
				$status = "$combineSku 已经存在";
                echo '<script language="javascript">
                        alert("'.$status.'");
                      </script>';
				exit;
			};
            if(count($skuArr) != count(array_unique($skuArr))){
                $status = "$combineSku 中存在重复的真实料号，请检查";
                echo '<script language="javascript">
                        alert("'.$status.'");
                      </script>';
				exit;
            }                        
			$tName = 'pc_goods';
			foreach ($skuArr as $value) {
    			if (!empty ($value)) {
    				$where = "WHERE sku='$value' and is_delete=0";
    				$count = OmAvailableModel :: getTNameCount($tName, $where);
    				if (!$count) {
    					$status = "$combineSku 真实料号 $value 不存在";
                        echo '<script language="javascript">
                                alert("'.$status.'");
                              </script>';
    					exit;
    				}
    			}
			}
            foreach($countArr as $value){
                if(intval($value) <= 0){
                    $status = "$combineSku 对应真实料号的数量有误";
                    echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit; 
                }
            }
        }        
        $tmpArr = array_filter($tmpArr);
        if(count($tmpArr) != count(array_unique($tmpArr))){
            $status = "存在重复的虚拟子料号，请检查！";
            echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
        }
		try {
		    $ebayGoodsArr = array();//同步数据数组
			BaseModel :: begin();
			for ($index = 0; $index <= $amount; $index++) {
				$combineSku = $_POST['combineSku' . $index] ? post_check(trim($_POST['combineSku' . $index])) : '';                
                $combineSku = $combineSpu . $combineSku;              
                if($index > 0 && $combineSku == $combineSpu){//只可能index=0时候，$combineSku==$combineSpu，其他表示无效
                    continue;
                }
				$combineLength = $_POST['combineLength' . $index] ? post_check(trim($_POST['combineLength' . $index])) : '';
				$combineWidth = $_POST['combineWidth' . $index] ? post_check(trim($_POST['combineWidth' . $index])) : '';
				$combineHeight = $_POST['combineHeight' . $index] ? post_check(trim($_POST['combineHeight' . $index])) : '';
				$combineNote = $_POST['combineNote' . $index] ? trim($_POST['combineNote' . $index]) : '';
				$skuArr = $_POST['sku' . $index];
				$countArr = $_POST['count' . $index];
				
				$dataCom = array ();
				$dataCom['combineSpu'] = $combineSpu;
				$dataCom['combineSku'] = $combineSku;
				$dataCom['combineCost'] = $combineCost;
				$dataCom['combineWeight'] = $combineWeight;
				$dataCom['combineLength'] = $combineLength;
				$dataCom['combineWidth'] = $combineWidth;
				$dataCom['combineHeight'] = $combineHeight;
				$dataCom['combineNote'] = $combineNote;
				$dataCom['combineUserId'] = $_SESSION['userId'];
                
				$dataCom['addTime'] = time();

				$tName = 'pc_goods_combine';
				$insertIdCom = OmAvailableModel :: addTNameRow2arr($tName, $dataCom);
				if (!$insertIdCom) {
					throw new Exception('add combine error');
				}

				$dataRelation = array ();
				$dataRelationMem = array ();
				for ($i = 0; $i < count($skuArr); $i++) {
					if (!empty ($skuArr[$i]) && !empty ($countArr[$i])) {
						$dataRelation[] = array (
							'combineSku' => $combineSku,
							'sku' => $skuArr[$i],
							'count' => $countArr[$i]
						);
						$dataRelationMem[] = array (
							'sku' => $skuArr[$i],
							'count' => $countArr[$i]
						);
					}
				}
				if (!empty ($dataRelation)) {
					$tName = 'pc_sku_combine_relation';
                    $where = "WHERE combineSku='$combineSku'";
                    $dataRelationCount = OmAvailableModel::getTNameCount($tName, $where);
                    if($dataRelationCount){
                        OmAvailableModel::deleteTNameRow($tName, $where);
                    }
					foreach ($dataRelation as $value) {
						if (!empty ($value['combineSku']) && !empty ($value['sku']) && !empty ($value['count'])) {
							$insertId = OmAvailableModel :: addTNameRow2arr($tName, $value);
						}
					}
				}
				if ($autoSpuList[0]['status'] != 2) {
					$tName = 'pc_auto_create_spu';
					$set = "SET status=2";
					$where = "WHERE spu='$combineSpu'";
					$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
				}
				//将新添加的sku添加到mem中
				$key = 'pc_goods_combine_' . $combineSku;
				$dataCom['detail'] = $dataRelationMem;
				$value = $dataCom;
				setMemNewByKey($key, $value); //这里不保证能添加成功

				//同步新数据到旧系统中
				$ebayProductsCombine = array ();
				$ebayProductsCombine['id'] = $insertIdCom;
				$ebayProductsCombine['goods_sn'] = $combineSku;
				$goods_sncombine = array ();
				$truesku = array ();
				foreach ($dataRelationMem as $value) {
					$str = '';
					$strTrue = '';
					$str = $value['sku'] . '*' . $value['count'];
					$strTrue = '[' . $value['sku'] . ']';
					$goods_sncombine[] = $str;
					$truesku[] = $strTrue;
				}
				$ebayProductsCombine['goods_sncombine'] = implode(',', $goods_sncombine);
				$ebayProductsCombine['notes'] = $combineNote;
				$ebayProductsCombine['goods_price'] = $combineCost;
				$ebayProductsCombine['goods_weight'] = $combineWeight;
				$ebayProductsCombine['cguser'] = getPersonNameById($_SESSION['userId']);
				$ebayProductsCombine['ebay_user'] = 'vipchen';
				$ebayProductsCombine['createdtime'] = time();
				$ebayProductsCombine['truesku'] = implode(',', $truesku);
                $ebayGoodsArr[] = $ebayProductsCombine;
			}
            BaseModel :: commit();
			BaseModel :: autoCommit();
            addSalerInfoForAny($combineSpu, 2, $_SESSION['userId'], $_SESSION['userId']);//add by zqt 20140519,添加销售人逻辑
            //同步数据到深圳ERP
            foreach($ebayGoodsArr as $value){
                $ret = OmAvailableModel :: newData2ErpInterfOpen('pc.erp.addGoodsCombine', $value, 'gw88'); 
            }
            
            $status = "添加成功";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    window.parent.location.href = "index.php?mod=goods&act=getCombineList&searchComField=1&fieldValue='.$combineSpu.'";
                  </script>';
			exit; 
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			echo $e->getMessage();
			$status = "添加失败，请联系系统技术部，谢谢";
            echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
		}
	}

	public function view_addSkuPm() {
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 15);
		$this->smarty->assign('title', '产品包材维护');
		$this->smarty->display("addSkuPm.htm");
	}

	//料号称重
	public function view_skuWeight() {
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 13);
		$this->smarty->assign('title', '产品重量维护');
		$this->smarty->display("skuWeight.htm");
	}

	//料号体积维护
	public function view_skuVolume() {
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 14);
		$this->smarty->assign('title', '产品体积维护');
		$this->smarty->display("addSkuVolume.htm");
	}

	//重量，体积，包材维护总页面
	public function view_skuVP() {
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 16);
		$this->smarty->assign('title', '产品重量体积维护');
		$this->smarty->display("skuVP.htm");
	}
   
   
   public function view_getSkuConversionList(){
        $skuConversionAct = new SkuConversionAct();
        $skuConversionArr = $skuConversionAct->act_getSkuConversionList();
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 1);
        $this->smarty->assign('twovar', 17);
        $this->smarty->assign('show_page', $skuConversionArr['show_page']);
		$this->smarty->assign('title', '料号转换');
        $this->smarty->assign('skuConversionList', empty($skuConversionArr['skuConversionList'])?array():$skuConversionArr['skuConversionList']);
		$this->smarty->display("skuConversionList.htm");
    }
    
    public function view_addSkuConversion(){
        $skuConversionAct = new SkuConversionAct();
        $skuConversionAct->act_addSkuConversion();       
        $status = $skuConversionAct::$errMsg;
        header("Location:index.php?mod=goods&act=getSkuConversionList&status=$status");
    }
    
    public function view_updateSkuConversion(){
        $skuConversionAct = new SkuConversionAct();
        $skuConversionAct->act_alertSkuConversion();       
        $status = $skuConversionAct::$errMsg;
        header("Location:index.php?mod=goods&act=getSkuConversionList&status=$status");
    }
    
    public function view_auditSkuConversion(){
    	$skuConversionAct = new SkuConversionAct();
    	$skuConversionAct->act_auditSkuConversion();    
    	$status = $skuConversionAct::$errMsg;
    	header("Location:index.php?mod=goods&act=getSkuConversionList&status=$status");
    }
    
    //反审核的view
    public function view_unAuditSkuConversion(){
    	$skuConversionAct = new SkuConversionAct();
    	$skuConversionAct->act_unAuditSkuConversion();    
    	$status = $skuConversionAct::$errMsg;
    	header("Location:index.php?mod=goods&act=getSkuConversionList&status=$status");
    }
    
    public function view_getSpuHscodeTaxList(){
        $goodsAct = new GoodsAct();
        $spuHscodeTaxList = $goodsAct->act_getSpuHscodeTaxList();
        $navlist = array (//面包屑
            array (
    			'url' => 'index.php?mod=goods&act=getGoodsList',
    			'title' => '产品信息'
 		    ),
 		    array (
    			'url' => 'index.php?mod=goods&act=getSpuHscodeTaxList',
    			'title' => 'SPU-海关编码'
            )
        );
    	$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 1);
        $this->smarty->assign('twovar', 18);
        $this->smarty->assign('show_page', $spuHscodeTaxList['show_page']);
    	$this->smarty->assign('title', 'SPU-海关编码');
        $this->smarty->assign('spuHscodeTaxList', empty($spuHscodeTaxList['spuHscodeTaxList'])?array():$spuHscodeTaxList['spuHscodeTaxList']);
    	$this->smarty->display("spuHscodeTaxList.htm");
    }
    
    public function view_getOverseaTaxList(){
        $spu = $_GET['spu']?post_check(trim($_GET['spu'])):'';//spu
        
        $tName = 'pc_spu_oversea_tax';
        $select = 'spu';
        $where = "WHERE is_delete=0 ";//退料单 的iostoreTypeId=

        if(!empty($spu)){
            $where .= "AND spu='$spu' ";
        }

        $total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100;//每页显示的个数
		$page = new Page($total,$num,'','CN');
		$where .= "order by id desc ".$page->limit;
		$spuList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($spuList)){
            $countSpuList = count($spuList);
            for($i=0;$i<$countSpuList;$i++){
                $tName = 'pc_spu_oversea_tax';
                $select = 'countryCode,tax';
                $where = "WHERE spu='{$spuList[$i]['spu']}'";
                $spuOverseaTaxList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($spuOverseaTaxList as $value){                            
                    if($value['countryCode'] == 'US'){
                        $spuList[$i]['USTax'] = $value['tax'];         
                    }
                }                
            }
        }
		if(!empty($_GET['page']))
		{
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num))
			{
				$n=1;
			}
			else
			{
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num)
		{
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		}else
		{
			$show_page = $page->fpage(array(0,2,3));
		}
        $navlist = array (//面包屑
            array (
    			'url' => 'index.php?mod=goods&act=getGoodsList',
    			'title' => '产品信息'
 		    ),
 		    array (
    			'url' => 'index.php?mod=goods&act=getSpuHscodeTaxList',
    			'title' => '海关仓税率列表'
            )
        );
    	$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 1);
        $this->smarty->assign('twovar', 19);
        $this->smarty->assign('show_page', $show_page);
    	$this->smarty->assign('title', '海关仓税率列表');
        $this->smarty->assign('spuList', empty($spuList)?array():$spuList);
    	$this->smarty->display("spuOverseaTaxList.htm");
    }
    
    //SKU重量审核列表
    public function view_getSkuWeightAuditList() {
		$sku = isset ($_GET['sku']) ? post_check($_GET['sku']) : '';
		$status = isset ($_GET['status']) ? post_check($_GET['status']) : '';
        $addUserId = isset ($_GET['addUserId']) ? post_check($_GET['addUserId']) : '';
        $auditerId = isset ($_GET['auditerId']) ? post_check($_GET['auditerId']) : '';
        $timeSearchType = isset ($_GET['timeSearchType']) ? post_check($_GET['timeSearchType']) : '';
        $startdate = isset ($_GET['startdate']) ? post_check($_GET['startdate']) : '';
        $enddate = isset ($_GET['enddate']) ? post_check($_GET['enddate']) : '';
        
		$tName = 'pc_goods_weight_audit';
		$select = '*';
		$where = 'WHERE is_delete=0 ';
		if (!empty ($sku)) {
			$where .= "AND sku='$sku' ";
		}
        if (intval($status) > 0) {
			$where .= "AND status='$status' ";
		}
		if (intval($addUserId) > 0) {
			$where .= "AND addUserId='$addUserId' ";
		}		
        if (intval($auditerId) > 0) {
			$where .= "AND auditerId='$auditerId' ";
		}
        if (intval($timeSearchType) > 0) {            
			if ($startdate != '') {
            	$start = strtotime($startdate . ' 00:00:00');
                if($timeSearchType == 1){
                    $where .= "AND addTime>='$start' ";
                }elseif($timeSearchType == 2){
                    $where .= "AND auditTime>='$start' ";
                }           	
            }
            if ($enddate != '') {
            	$end = strtotime($enddate . ' 23:59:59');
            	if($timeSearchType == 1){
                    $where .= "AND addTime<='$end' ";
                }elseif($timeSearchType == 2){
                    $where .= "AND auditTime<='$end' ";
                }
            }
		}
        
		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
        $where .= 'order by id desc ';
		$where .= $page->limit;
		$skuWeightAuditList = OmAvailableModel::getTNameList($tName, $select, $where);
        
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
		$navlist = array (//面包屑
           array (
			'url' => 'index.php?mod=goods&act=getGoodsList',
			'title' => '产品信息'
	       ),
		   array (
			'url' => 'index.php?mod=goods&act=getSkuWeightAuditList',
			'title' => 'SKU重量审核列表'
	       )    
        );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 1);
		$this->smarty->assign('twovar', 110);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', 'SKU重量审核列表');
		$this->smarty->assign('skuWeightAuditList', empty ($skuWeightAuditList) ? array() : $skuWeightAuditList);
		
        $this->smarty->display("skuWeightAuditList.htm");
	}
    
}
?>