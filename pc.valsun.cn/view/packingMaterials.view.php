<?php
class PackingMaterialsView extends BaseView{

	//页面渲染输出
	public function view_getPmList() {
		//调用action层， 获取列表数据
		$packingMaterials = new PackingMaterialsAct();
		$status = $_GET['status']; //页面输出提示
		$select = '*';
		$where = "where is_delete=0 ";
		$total = $packingMaterials->act_getPmCount($where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$pmList = $packingMaterials->act_getPmList($select, $where . $page->limit);

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
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材管理'
			),
			array (
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材信息列表'
			),
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 3);
        $this->smarty->assign('twovar', 31);
		$this->smarty->assign('show_page', $show_page);
        $this->smarty->assign('pmList', $pmList);
		$this->smarty->assign("title", "包材管理");
		$this->smarty->assign("status", $status);
		$this->smarty->display("packingMaterials.htm");
	}

	public function view_updatePm() {
		$packingMaterials = new PackingMaterialsAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : ''; //pc_packing_materials表id
		if (empty ($id)) {
			$status = "系统id错误";
			header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
            exit;
		}
		$select = '*';
		$where = "where is_delete=0 and id='$id'";
		$productList = $packingMaterials->act_getPmList($select, $where);
        $pmList = $productList[0];
        $navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材管理'
			),
			array (
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材信息列表'
			),
			array (
				'url' => "index.php?mod=packingMaterials&act=updatePm&id=$id",
				'title' => '修改包材信息'
			),
		);
		$this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('onevar', 3);
        $this->smarty->assign('twovar', 31);
		$this->smarty->assign("title", "修改包材记录");
		$this->smarty->assign("pmList", $pmList);
        $this->smarty->display("updatePackingMaterials.htm");
	}

	public function view_addPm() {
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材管理'
			),
			array (
				'url' => 'index.php?mod=packingMaterials&act=getPmList',
				'title' => '包材信息列表'
			),
			array (
				'url' => 'index.php?mod=packingMaterials&act=addPm',
				'title' => '添加包材信息'
			),
		);
		$this->smarty->assign('navlist', $navlist);
	    $this->smarty->assign('onevar', 3);
        $this->smarty->assign('twovar', 31);
		$this->smarty->assign("title", "添加包材");
        $this->smarty->display("addPackingMaterials.htm");
	}

	public function view_updatePmOn() {
		$packingMaterials = new PackingMaterialsAct();
		$id = isset ($_POST['id']) ? post_check($_POST['id']) : '';
		$pmAlias = isset ($_POST['pmAlias']) ? post_check($_POST['pmAlias']) : '';
		$pmName = isset ($_POST['pmName']) ? post_check($_POST['pmName']) : '';
		$pmNotes = isset ($_POST['pmNotes']) ? post_check($_POST['pmNotes']) : '';
		$pmLength = isset ($_POST['pmLength']) ? post_check($_POST['pmLength']) : '';
		$pmWidth = isset ($_POST['pmWidth']) ? post_check($_POST['pmWidth']) : '';
		$pmHeight = isset ($_POST['pmHeight']) ? post_check($_POST['pmHeight']) : '';
		$pmCost = isset ($_POST['pmCost']) ? post_check($_POST['pmCost']) : '';
		$pmWeight = isset ($_POST['pmWeight']) ? post_check($_POST['pmWeight']) : '';
		$pmDimension = isset ($_POST['pmDimension']) ? post_check($_POST['pmDimension']) : '';
		$pmDivider = isset ($_POST['pmDivider']) ? post_check($_POST['pmDivider']) : '';
		$pmRatio = isset ($_POST['pmRatio']) ? post_check($_POST['pmRatio']) : '';
        if(intval($id) == 0){
            $status = "非法id";
			header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
            exit;
        }
		if (empty ($pmAlias) || empty ($pmName) || empty ($pmLength) || empty ($pmWidth) || empty ($pmHeight) || empty ($pmCost) || empty ($pmWeight) || empty ($pmDimension)) { //后台空数据验证
			$status = "修改失败，存在空数据";
			header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
            exit;
		}
		$countPmDivider = $packingMaterials->act_getPmCount("where pmName='$pmDivider' and is_delete=0");
		if (trim($pmDivider) != '' && $countPmDivider <= 0) {
			$status = "修改失败，不存在该包材类名为 '$pmDivider' 的记录（包材除数）";
		} else {
			$set = "set pmAlias='$pmAlias',pmNotes='$pmNotes',pmLength='$pmLength',pmWidth='$pmWidth',pmHeight='$pmHeight',pmCost='$pmCost',pmWeight='$pmWeight',pmDimension='$pmDimension',pmDivider='$pmDivider'";
			if (!empty ($pmRatio)) {
				$set .= ",pmRatio='$pmRatio'";
			}
			$where = "where is_delete=0 and id='$id'";
			$status = $packingMaterials->act_updatePm($set, $where);
			if ($status > 0) {
				$status = '修改成功';
                //将新的包材信息添加到mem中
                $key = 'pc_pm_'.$id;
                $dataPm = array();
                $dataPm['id'] = $id;
                $dataPm['pmAlias'] = $pmAlias;
                $dataPm['pmName'] = $pmName;
                $dataPm['pmNotes'] = $pmNotes;
                $dataPm['pmLength'] = $pmLength;
                $dataPm['pmWidth'] = $pmWidth;
                $dataPm['pmHeight'] = $pmHeight;
                $dataPm['pmCost'] = $pmCost;
                $dataPm['pmWeight'] = $pmWeight;
                $dataPm['pmDimension'] = $pmDimension;
                $dataPm['pmRatio'] = $pmRatio;
                $value = $dataPm;
                setMemNewByKey($key, $value);//这里不保证能添加成功
                //更新全部包材的mem信息
                $pmList = $packingMaterials->act_getPmList('*',"WHERE is_delete=0");
                if(!empty($pmList)){
                    setMemNewByKey('pc_pm_all', $pmList);//这里不保证能添加成功
                }
			} else {
				$status = '无修改数据';
			}
		}
		header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
	}

	public function view_deletePmOn() {
		$packingMaterials = new PackingMaterialsAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		$status = $packingMaterials->act_deletePm("where id='$id'");
		if ($status > 0) {
			$status = '删除成功';
		} else {
			$status = '删除失败';
		}
		header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
	}

	public function view_addPmOn() {
		$packingMaterials = new PackingMaterialsAct();
		$pmAlias = isset ($_POST['pmAlias']) ? post_check($_POST['pmAlias']) : '';
		$pmName = isset ($_POST['pmName']) ? post_check($_POST['pmName']) : '';
		$pmNotes = isset ($_POST['pmNotes']) ? post_check($_POST['pmNotes']) : '';
		$pmLength = isset ($_POST['pmLength']) ? post_check($_POST['pmLength']) : '';
		$pmWidth = isset ($_POST['pmWidth']) ? post_check($_POST['pmWidth']) : '';
		$pmHeight = isset ($_POST['pmHeight']) ? post_check($_POST['pmHeight']) : '';
		$pmCost = isset ($_POST['pmCost']) ? post_check($_POST['pmCost']) : '';
		$pmWeight = isset ($_POST['pmWeight']) ? post_check($_POST['pmWeight']) : '';
		$pmDimension = isset ($_POST['pmDimension']) ? post_check($_POST['pmDimension']) : '';
		$pmDivider = isset ($_POST['pmDivider']) ? post_check($_POST['pmDivider']) : '';
		$pmRatio = isset ($_POST['pmRatio']) ? post_check($_POST['pmRatio']) : '';
		if (empty ($pmRatio)) {
			$pmRatio = null;
		}
		if (empty ($pmAlias) || empty ($pmName) || empty ($pmLength) || empty ($pmWidth) || empty ($pmHeight) || empty ($pmCost) || empty ($pmWeight)) {
			$status = "添加失败，存在空数据";
			header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
            exit;
		}
		$countPmName = $packingMaterials->act_getPmCount("where pmName='$pmName' and is_delete=0");
		$countPmDivider = $packingMaterials->act_getPmCount("where pmName='$pmDivider' and is_delete=0");
		if ($countPmName > 0) {
			$status = "添加失败，已经存在该包材类名记录";
		}
		elseif (trim($pmDivider) != '' && $countPmDivider <= 0) {
			$status = " 添加失败，不存在该包材类名为 '$pmDivider' 的记录（包材除数）";
		} else {
			$set = "set pmAlias='$pmAlias',pmName='$pmName',pmNotes='$pmNotes',pmLength='$pmLength',pmWidth='$pmWidth',pmHeight='$pmHeight',pmCost='$pmCost',pmWeight='$pmWeight',pmDimension='$pmDimension',pmDivider='$pmDivider'";
			if (!empty ($pmRatio)) {
				$set .= ",pmRatio='$pmRatio'";
			}
			$insertId = $packingMaterials->act_addPm($set);
			if ($insertId) {
				$status = "添加成功";
                //将新的包材信息添加到mem中
                $key = 'pc_pm_'.$insertId;
                $dataPm = array();
                $dataPm['id'] = $insertId;
                $dataPm['pmAlias'] = $pmAlias;
                $dataPm['pmName'] = $pmName;
                $dataPm['pmNotes'] = $pmNotes;
                $dataPm['pmLength'] = $pmLength;
                $dataPm['pmWidth'] = $pmWidth;
                $dataPm['pmHeight'] = $pmHeight;
                $dataPm['pmCost'] = $pmCost;
                $dataPm['pmWeight'] = $pmWeight;
                $dataPm['pmDimension'] = $pmDimension;
                $dataPm['pmRatio'] = $pmRatio;
                $value = $dataPm;
                setMemNewByKey($key, $value);//这里不保证能添加成功
                //更新全部包材的mem信息
                $pmList = $packingMaterials->act_getPmList('*',"WHERE is_delete=0");
                if(!empty($pmList)){
                    setMemNewByKey('pc_pm_all', $pmList);//这里不保证能添加成功
                }
			} else {
				$status = "添加失败";
			}
		}
		header("Location:index.php?mod=packingMaterials&act=getPmList&status=$status");
	}

}
?>