<?php
/**
 * AutoCreateSpuView
 *
 * @package ftpPc.valsun.cn
 * @author blog.anchen8.net
 * @copyright 2013
 * @version $Id$
 * @access public
 */
class AutoCreateSpuView extends BaseView {

	public function view_getAutoCreatePrefixList() {
		//调用action层， 获取列表数据

		$omAvailableAct = new OmAvailableAct();
        $companyId = intval($_SESSION['companyId']);
		$tName = 'pc_auto_create_spu_prefix';
		$select = '*';
		$where = 'WHERE isUse=1 ';
        $where .= "AND companyId='$companyId'";
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= $page->limit;
		$spuPrefixList = $omAvailableAct->act_getTNameList($tName, $select, $where);

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
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => '自动生成SPU'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 21);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '自动生成SPU');
		$this->smarty->assign('spuPrefixList', empty ($spuPrefixList) ? null : $spuPrefixList);
		$this->smarty->display("autoCreateSpu.htm");
	}

	public function view_getAutoCreateSpuList() {
		//调用action层， 获取列表数据
		$status = $_GET['status'];
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		$isSingSpu = $_GET['isSingSpu'] ? post_check(trim($_GET['isSingSpu'])) : '';
		$autoStatus = $_GET['autoStatus'] ? post_check(trim($_GET['autoStatus'])) : '';
		$purchaseId = $_GET['purchaseId'] ? post_check(trim($_GET['purchaseId'])) : '';
        $hasToSaler = $_GET['hasToSaler'] ? post_check(trim($_GET['hasToSaler'])) : '';
        $isAgree = $_GET['isAgree'] ? post_check(trim($_GET['isAgree'])) : '';
        $platformId = $_GET['platformId'] ? post_check(trim($_GET['platformId'])) : '';
        $salerId = $_GET['salerId'] ? post_check(trim($_GET['salerId'])) : '';
        $isExsitWebMaker = $_GET['isExsitWebMaker'] ? post_check(trim($_GET['isExsitWebMaker'])) : '';
        $webMakerId = $_GET['webMakerId'] ? post_check(trim($_GET['webMakerId'])) : '';
        $webMakeIsAgree = $_GET['webMakeIsAgree'] ? post_check(trim($_GET['webMakeIsAgree'])) : '';
        $productsNewSpu = $_GET['productsNewSpu'] ? post_check(trim($_GET['productsNewSpu'])) : '';
		$omAvailableAct = new OmAvailableAct();
		$tName = 'pc_auto_create_spu';
		$select = '*';
		$where = 'WHERE is_delete=0 ';
		if (!empty ($spu)) {
			$where .= "AND spu='$spu' ";
		}
		if (intval($isSingSpu) == 1 || intval($isSingSpu) == 2) {
			$where .= "AND isSingSpu='$isSingSpu' ";
		}
		if (intval($autoStatus) == 1 || intval($autoStatus) == 2) {
			$where .= "AND status='$autoStatus' ";
		}
		if (intval($purchaseId) != 0) {
			$where .= "AND purchaseId='$purchaseId' ";
		}
        if(!empty($hasToSaler)){
            $hasToSalerSpuList = getHasToSalerSpuByIPS(intval($isSingSpu), $isAgree, $platformId, $salerId);//已经分配的spuList
            $spuArr = array();
            foreach($hasToSalerSpuList as $value){
                $spuArr[] = "'".$value['spu']."'";
            }
            $spuStr = implode(',',$spuArr);
            if($hasToSaler == 1){//已经分配
                if(!empty($spuStr)){
                    $where .= "AND spu in($spuStr) ";
                }else{
                    $where .= "AND 1=2 ";
                }
            }elseif($hasToSaler == 2){//未分配
                if(!empty($spuStr)){
                    $where .= "AND spu not in($spuStr) ";
                }else{
                    $where .= "AND 1=2 ";
                }
            }
        }
        if(!empty($isExsitWebMaker)){
            $isExsitWebMakerSpuList = getIsExsitWebMakerSpuByIW(intval($isSingSpu), $webMakerId, $webMakeIsAgree);//已经分配的spuList
            $spuArr = array();
            foreach($isExsitWebMakerSpuList as $value){
                $spuArr[] = "'".$value['spu']."'";
            }
            $spuStr = implode(',',$spuArr);
            if($isExsitWebMaker == 1){//有产品制作人
                if(!empty($spuStr)){
                    $where .= "AND spu in($spuStr) ";
                }else{
                    $where .= "AND 1=2 ";
                }
            }elseif($isExsitWebMaker == 2){//无产品制作人
                if(!empty($spuStr)){
                    $where .= "AND spu not in($spuStr) ";
                }else{
                    $where .= "AND 1=2 ";
                }
            }
        }
        if($productsNewSpu == 1){//新品下单的SPU
            $productsNewSpuArr = getNotAuditAndNotTakeSpuList();
            if(!empty($productsNewSpuArr)){
                $spuArr = array();
                foreach($productsNewSpuArr as $value){
                    $spuArr[] = "'".$value."'";
                }
                $spuStr = implode(',',$spuArr);
                if(!empty($spuStr)){
                    $where .= "AND spu in($spuStr) ";
                }else{
                    $where .= "AND 1=2 ";
                }
            }
        }
		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= 'order by createdTime desc ' . $page->limit;
		$spuList = $omAvailableAct->act_getTNameList($tName, $select, $where);
        if(!empty($spuList)){
            $countSpuList = count($spuList);
            for($i=0;$i<$countSpuList;$i++){
                if($spuList[$i]['isSingSpu'] == 1){
                    $tName = 'pc_spu_saler_single';
                }else{
                    $tName = 'pc_spu_saler_combine';
                }
                $select = 'platformId,salerId,isAgree';
                $where = "WHERE spu='{$spuList[$i]['spu']}'";
                $salerList = OmAvailableModel::getTNameList($tName, $select, $where);
                foreach($salerList as $value){
                    $salerName = getPersonNameById($value['salerId']);
                    if($value['platformId'] == 1){
                        $spuList[$i]['ebaySalerId'] = $value['salerId'];
                        $spuList[$i]['ebaySaler'] = $salerName;
                        $spuList[$i]['ebayIsAgree'] = $value['isAgree'];
                    }elseif($value['platformId'] == 2){
                        $spuList[$i]['aliexpressSalerId'] = $value['salerId'];
                        $spuList[$i]['aliexpressSaler'] = $salerName;
                        $spuList[$i]['aliexpressIsAgree'] = $value['isAgree'];
                    }elseif($value['platformId'] == 11){
                        $spuList[$i]['amazonSalerId'] = $value['salerId'];
                        $spuList[$i]['amazonSaler'] = $salerName;
                        $spuList[$i]['amazonIsAgree'] = $value['isAgree'];
                    }elseif($value['platformId'] == 14){
                        $spuList[$i]['overseaSalerId'] = $value['salerId'];
                        $spuList[$i]['overseaSaler'] = $salerName;
                        $spuList[$i]['overseaIsAgree'] = $value['isAgree'];
                    }

                }
                $tName = 'pc_spu_web_maker';
                $select = 'webMakerId,isAgree,isTake,isComplete';
                $where = "WHERE is_delete=0 AND spu='{$spuList[$i]['spu']}' order by id desc limit 1";
                $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
                $spuList[$i]['webMakerId'] = $spuWebMakerList[0]['webMakerId'];
                $spuList[$i]['webMaker'] = getPersonNameById($spuWebMakerList[0]['webMakerId']);
                $spuList[$i]['webMakerIsAgree'] = $spuWebMakerList[0]['isAgree'];
                $spuList[$i]['webMakerIsTake'] = $spuWebMakerList[0]['isTake'];
                $spuList[$i]['webMakerIsComplete'] = $spuWebMakerList[0]['isComplete'];
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
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
				'title' => '生成SPU列表管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 22);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', '生成SPU列表管理');
		$this->smarty->assign('spuList', empty ($spuList) ? array() : $spuList);
		$this->smarty->display("spuList.htm");
	}

	public function view_deleteAutoCreateSpu() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		$purchaseId = $_SESSION['userId'];
		if (!preg_match("/^[A-Z]{2}[0-9]{6}$/", $spu)) {
			$status = "非法spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$where = "WHERE spu='$spu' and purchaseId='$purchaseId' and status=1 and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "该spu不属于你或者是该spu已经进系统";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$set = "SET is_delete=1";
		$where = "WHERE spu='$spu'";
		$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if (!$affectRow) {
			$status = "删除失败";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$status = "删除 $spu 成功";
		header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
	}

	public function view_editAutoCreateSpuCate() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		$purchaseId = $_SESSION['userId'];
		if (empty ($spu)) {
			$status = "非法spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$where = "WHERE spu='$spu' and purchaseId='$purchaseId' and is_delete=0 and isSingSpu=1";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "条件异常";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_goods';
		$select = 'goodsCategory';
		$where = "WHERE spu='$spu'";
		$pcList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($pcList)) {
			header("Location:index.php?mod=autoCreateSpu&act=editAutoCreateSpu&spu=$spu&pid={$pcList[0]['goodsCategory']}");
			exit;
		}
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
				'title' => '生成SPU列表管理'
			),
			array (
				'url' => "index.php?mod=autoCreateSpu&act=editAutoCreateSpuCate&spu=$spu",
				'title' => "SPU类别编辑_$spu"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 22);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('title', 'SPU类别编辑');
		$this->smarty->assign('spu', $spu);
		$this->smarty->display("editAutoCreateSpuCate.htm");
	}

	public function view_editAutoCreateSpu() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		$pid = $_GET['pid'] ? post_check(trim($_GET['pid'])) : '';
		if (empty ($spu)) {
			$status = "spu为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (empty ($pid)) {
			$status = "类别为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$where = "WHERE spu='$spu' and is_delete=0 and isSingSpu=1";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "系统不存在符合的 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_goods_category';
		$where = "WHERE path='$pid' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "系统不存在该类别";
			header("Location:index.php?mod=autoCreateSpu&act=editAutoCreateSpuCate&spu=$spu&status=$status");
			exit;
		}
		$where = "WHERE path like'$pid-%' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count) {
			$status = "产品档案只能建立在最小分类下，请选择最小分类";
			header("Location:index.php?mod=autoCreateSpu&act=editAutoCreateSpuCate&spu=$spu&status=$status");
			exit;
		}

		$pathImplodeStr = getAllPathBypid($pid); //取的$pid下所有父类及自己的path字符串，形如当$pid=1-2-3时，$pathImplodeStr=1,1-2,1-2-3;
		$navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
				'title' => '生成SPU列表管理'
			),
			array (
				'url' => "index.php?mod=autoCreateSpu&act=editAutoCreateSpuCate&spu=$spu",
				'title' => "SPU类别编辑_$spu"
			),
			array (
				'url' => "index.php?mod=autoCreateSpu&act=editAutoCreateSpu&spu=$spu&pid=$pid",
				'title' => "SPU编辑_$spu"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 22);
		$this->smarty->assign('title', 'SPU档案编辑');
		$this->smarty->assign('spu', $spu);
		$this->smarty->assign('pid', $pid);
		$this->smarty->assign('pathImplodeStr', $pathImplodeStr);
		//$this->smarty->assign('pid_str', $pid_str);
		$this->smarty->display("editAutoCreateSpuSing.htm");
	}

	public function view_editAutoCreateSpuOn() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
		$pid = $_POST['pid'] ? post_check(trim($_POST['pid'])) : '';

		///////////////////检查spu,pid是否非法
		if (empty ($spu)) {
			$status = "spu为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (empty ($pid)) {
			$status = "类别为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$select = 'sort,purchaseId,isSingSpu';
		$where = "WHERE spu='$spu' and is_delete=0";
		$autoSpuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($autoSpuList)) {
			$status = "系统不存在 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_spu_archive';
		$where = "WHERE spu='$spu' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count) {
			$status = "产品档案中已经有 $spu 的信息";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$tName = 'pc_goods_category';
		$where = "WHERE path='$pid' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "系统不存在该类别";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		//获取网页传来的数据
		$dataSpu = array (); //spu档案属性
		$dataSpu['spu'] = $spu;
		$dataSpu['categoryPath'] = $pid;

		$dataSpu['spuName'] = $_POST['spuName'] ? trim($_POST['spuName']) : ''; //描述
		$dataSpu['spuPurchasePrice'] = $_POST['spuPurchasePrice'] ? post_check(trim($_POST['spuPurchasePrice'])) : '';//采购成本
		$dataSpu['spuLowestPrice'] = $_POST['spuLowestPrice'] ? post_check(trim($_POST['spuLowestPrice'])) : '';//平台最低售价（USD）
		$dataSpu['spuCalWeight'] = $_POST['spuCalWeight'] ? post_check(trim($_POST['spuCalWeight'])) : '';//估算重量
		$dataSpu['referMonthSales'] = $_POST['referMonthSales'] ? post_check(trim($_POST['referMonthSales'])) : '';//参考月销量

        $dataSpu['minNum'] = $_POST['minNum'] ? post_check(trim($_POST['minNum'])) : '';//起订量
        $dataSpu['platformId'] = $_POST['platformId'] ? post_check(trim($_POST['platformId'])) : '';//对应平台
        $dataSpu['freight'] = $_POST['freight'] ? post_check(trim($_POST['freight'])) : 0;//运费
        
        $spId = $_POST['spId'];//特殊属性的数组 add zqt 20140819
        
        $dataSpu['secretInfo'] = $_POST['secretInfo'] ? post_check(trim($_POST['secretInfo'])) : '';//隐私信息，目前为被PK料号信息

		$dataSpu['lowestUrl'] = $_POST['lowestUrl'] ? post_check(trim($_POST['lowestUrl'])) : '';//最低价链接
		$dataSpu['bidUrl'] = $_POST['bidUrl'] ? post_check(trim($_POST['bidUrl'])) : '';//参考listing链接
		if (empty ($dataSpu['spuName'])) {
			$status = "描述不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (!is_numeric($dataSpu['spuPurchasePrice']) || $dataSpu['spuPurchasePrice'] <= 0) {
			$status = "采购价必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (!is_numeric($dataSpu['spuLowestPrice']) || $dataSpu['spuLowestPrice'] <= 0) {
			$status = "市场最低价必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (!is_numeric($dataSpu['spuCalWeight']) || $dataSpu['spuCalWeight'] <= 0) {
			$status = "估算重量必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (intval($dataSpu['referMonthSales']) == 0) {
			$status = "参考月销量必须为大于0的正数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
        if (intval($dataSpu['minNum']) <= 0) {
			$status = "起订量必须为大于0的正数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
        if (intval($dataSpu['platformId']) <= 0) {
			$status = "对应平台选择有误";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
        if (!is_numeric($dataSpu['freight']) || $dataSpu['freight'] < 0) {
			$status = "运费必须为非负数";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (empty ($dataSpu['lowestUrl'])) {
			$status = "最低价链接不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		if (empty ($dataSpu['bidUrl'])) {
			$status = "参考listing链接不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}

		$dataSpu['spuNote'] = $_POST['spuNote'] ? trim($_POST['spuNote']) : '';
		$dataSpu['spuStatus'] = $_POST['spuStatus'] ? post_check(trim($_POST['spuStatus'])) : 1;
		$dataSpu['spuSort'] = $autoSpuList[0]['sort'];
		$dataSpu['purchaseId'] = $autoSpuList[0]['purchaseId'];
		$dataSpu['isSingSpu'] = $autoSpuList[0]['isSingSpu'];
		$dataSpu['spuCreatedTime'] = time();

		$dataLink = array (); //spuLink属性
		$dataLinkTmp['spu'] = $spu;
		$linkUrlArr = $_POST['linkUrl'];
		$linkNoteArr = $_POST['linkNote'];
		for ($i = 0; $i < count($linkUrlArr); $i++) {
			$dataLinkTmp['spu'] = $spu;
			$dataLinkTmp['linkUrl'] = $linkUrlArr[$i];
			$dataLinkTmp['linkNote'] = $linkNoteArr[$i];
			$dataLink[] = $dataLinkTmp;
		}
        //add 20140526 筛选的PK SKU
        $pkSkuArr = !empty($_POST['pkSku'])?$_POST['pkSku']:array();
        if($dataSpu['spuStatus'] == 51 && count($pkSkuArr) <= 0){//状态为pk
            $tmpStatus = "请填写完PK的SPU后点击'筛选SKU'按钮选择所PK的SKU，谢谢！";
            echo '<script language="javascript">
                    alert("'.$tmpStatus.'");
                    history.back();
                  </script>';
			exit;
        }
        if(!preg_match("/^[A-Z0-9]+$/",$dataSpu['secretInfo']) && !empty($dataSpu['secretInfo'])){
            $tmpStatus = "被PK的SPU只能是大写字母和数字字符串！";
            echo '<script language="javascript">
                    alert("'.$tmpStatus.'");
                    history.back();
                  </script>';
			exit;
        }
        //
		$dataPro = array ();
		$pathImplodeStr = getAllPathBypid($pid);
		$tName = 'pc_archive_property';
		$select = '*';
		$where = "WHERE categoryPath IN ($pathImplodeStr)";
		$proList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($proList)) {
			foreach ($proList as $value) {
				$tmpArr = array ();
				if ($value['isRadio'] == 1) { //单选
					$tmpArr['spu'] = $spu;
					$tmpArr['propertyId'] = $value['id'];
					$tmpArr['propertyValueId'] = $_POST['pro' . $value['id']]; //单选时，$_POST['pro'.$value['id']]存放的是一个字符串
					$dataPro[] = $tmpArr;
				} else { //多选
					//多选时，$_POST['pro'.$value['id']]存放的是一个数组,固定某个propertyId下的多个值
					$tmpPostValueArr = $_POST['pro' . $value['id']];
					if (!empty ($tmpPostValueArr)) {
						foreach ($tmpPostValueArr as $value2) {
							$tmpArr['spu'] = $spu;
							$tmpArr['propertyId'] = $value['id'];
							$tmpArr['propertyValueId'] = $value2;
							$dataPro[] = $tmpArr;
						}
					}

				}

			}
		}
		$dataInp = array ();
		$tName = 'pc_archive_input';
		$select = '*';
		$where = "WHERE categoryPath IN ($pathImplodeStr)";
		$inpList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($inpList)) {
			foreach ($inpList as $value) {
				$tmpArr = array ();
				$tmpArr['spu'] = $spu;
				$tmpArr['inputId'] = $value['id'];
				$tmpArr['inputValue'] = $_POST['inp' . $value['id']];
				$dataInp[] = $tmpArr;
			}
		}

		try {
			BaseModel :: begin();
			//pc_spu_archive中插入数据
			$tName = 'pc_spu_archive';
			$insertIdSpu = OmAvailableModel :: addTNameRow2arr($tName, $dataSpu);
			if (!$insertIdSpu) {
				throw new Exception('add pc_spu_archive error');
			}
            
            //add zqt 20140819 添加特殊属性
            if(!empty($spId) && is_array($spId)){
                foreach($spId as $propertyIdValue){
                    $propertyIdValue = intval($propertyIdValue);
                    $tName = 'pc_special_property_spu';
                    $where = "WHERE spu='$spu' and propertyId=$propertyIdValue";
                    $existSpuCount = OmAvailableModel::getTNameCount($tName, $where);
                    if(!$existSpuCount){
                        $dataTmpArr = array();
                        $dataTmpArr['propertyId'] = $propertyIdValue;
                        $dataTmpArr['spu'] = $spu;
                        OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                    }
                }                                
            }
            
            
            //add 20140526 添加PK细分SKU
            foreach($pkSkuArr as $value){
                $tName = 'pc_spu_archive_pk_sku';
                $dataPkSkuArr['spu'] = $spu;
                $dataPkSkuArr['sku'] = $value;
                OmAvailableModel::addTNameRow2arr($tName, $dataPkSkuArr);
            }

			//pc_archive_spu_link中插入数据
			$tName = 'pc_archive_spu_link';
			foreach ($dataLink as $value) {
				if (!empty ($value['linkUrl'])) {
					$insertIdLink = OmAvailableModel :: addTNameRow2arr($tName, $value);
					if ($insertIdLink === false) {
						throw new Exception('add pc_archive_spu_link error');
					}
				}
			}
			//pc_archive_spu_property_value_relation中插入数据
			$tName = 'pc_archive_spu_property_value_relation';
			if (!empty ($dataPro)) {
				foreach ($dataPro as $value) {
					if (!empty ($value['propertyValueId'])) {
						$insertIdPro = OmAvailableModel :: addTNameRow2arr($tName, $value);
						if ($insertIdPro === false) {
							throw new Exception('add pc_archive_spu_property_value_relation error');
						}
					}
				}
			}
			//pc_archive_spu_input_value_relation中插入数据
			$tName = 'pc_archive_spu_input_value_relation';
			if (!empty ($dataInp)) {
				foreach ($dataInp as $value) {
					if (trim($value['inputValue']) != '') {
						$insertIdPro = OmAvailableModel :: addTNameRow2arr($tName, $value);
						if ($insertIdPro === false) {
							throw new Exception('add pc_archive_spu_property_value_relation error');
						}
					}
				}
			}
			//将pc_auto_create_spu中该spu的记录标记为已经进入系统
			$tName = 'pc_auto_create_spu';
			$set = 'SET status=2';
			$where = "WHERE spu='$spu'";
			$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
			if ($affectRow === false) {
				throw new Exception('update pc_auto_create_spu status error');
			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
			$status = "$spu 产品档案建立成功";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$spu");
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
		}
	}

	public function view_getSpuArchiveList() {
		$omAvailableAct = new OmAvailableAct();
		$status = isset ($_GET['status']) ? post_check($_GET['status']) : '';
		$spu = isset ($_GET['spu']) ? post_check($_GET['spu']) : '';
		$auditStatus = isset ($_GET['auditStatus']) ? $_GET['auditStatus'] : '';
        $spuStatus = isset ($_GET['spuStatus']) ? $_GET['spuStatus'] : '';
		$purchaseId = isset ($_GET['purchaseId']) ? $_GET['purchaseId'] : '';
        $isPPVRecord = isset ($_GET['isPPVRecord']) ? $_GET['isPPVRecord'] : '';//有无属性记录搜索
        $haveSizePPV = isset ($_GET['haveSizePPV']) ? $_GET['haveSizePPV'] : '';//是否有尺码属性值记录
        $isMeasureRecord = isset ($_GET['isMeasureRecord']) ? $_GET['isMeasureRecord'] : '';//有无尺寸测量记录搜索
		$pid = isset ($_GET['pid']) ? $_GET['pid'] : '';
        $dept = isset ($_GET['dept']) ? $_GET['dept'] : '';
        $startdate = isset ($_GET['startdate']) ? $_GET['startdate'] : '';
        $enddate = isset ($_GET['enddate']) ? $_GET['enddate'] : '';

		$tName = 'pc_spu_archive';
		$select = '*';
		$where = 'WHERE is_delete=0 ';
		if (!empty ($spu)) {
			$where .= "AND spu='$spu' ";
		}
		if (!empty ($auditStatus)) {
			$where .= "AND auditStatus='$auditStatus' ";
		}
        if (!empty ($spuStatus)) {
            if(isAccessAll('autoCreateSpu', 'auditSpuArchive')){//如果有审核权限的人，则可以看到所有状态的人员的记录
                $where .= "AND spuStatus='$spuStatus' ";
            }else{//如果没有审核权限的人，只能看到自己对应所在状态的记录
                $where .= "AND spuStatus='$spuStatus' AND purchaseId='{$_SESSION['userId']}' ";
            }
		}
		if (!empty ($pid)) {
			$where .= "AND categoryPath REGEXP '^$pid(-[0-9]+)*$' ";
		}
		if (intval($purchaseId) != 0) {
			$where .= "AND purchaseId='$purchaseId' ";
		}
        if(intval($isPPVRecord) != 0){
            $spuList = getSpuPPV();
            if(!empty($spuList)){
                $tmpArr = array();
                foreach($spuList as $value){
                    $tmpArr[] = "'".$value['spu']."'";
                }
                $tmpStr = implode(',',$tmpArr);
                if($isPPVRecord == 1){//无记录
                    $where .= "AND spu not in($tmpStr) ";
                }elseif($isPPVRecord == 2){//有记录
                    $where .= "AND spu in($tmpStr) ";
                }
            }
        }
        if(intval($haveSizePPV) != 0){
            $spuList = getSpuListByPropertyName('尺码');//获取具有尺码属性关联的spuList
            if(!empty($spuList)){
                $tmpArr = array();
                foreach($spuList as $value){
                    $tmpArr[] = "'".$value['spu']."'";
                }
                $tmpStr = implode(',',$tmpArr);
                if($haveSizePPV == 1){//无记录
                    $where .= "AND spu not in($tmpStr) ";
                }elseif($haveSizePPV == 2){//有记录
                    $where .= "AND spu in($tmpStr) ";
                }
            }
        }
        if(intval($isMeasureRecord) != 0){
            $spuList = OmAvailableModel::getTNameList('pc_archive_spu_input_size_measure', 'spu', 'group by spu');
            if(!empty($spuList)){
                $tmpArr = array();
                foreach($spuList as $value){
                    $tmpArr[] = "'".$value['spu']."'";
                }
                $tmpStr = implode(',',$tmpArr);
                if($isMeasureRecord == 1){//无记录
                    $where .= "AND spu not in($tmpStr) ";
                }elseif($isMeasureRecord == 2){//有记录
                    $where .= "AND spu in($tmpStr) ";
                }
            }
        }
        $dept = intval($dept);
        $spuArchiveDepArr = getSpuArchiveDetArr();
        if(array_key_exists($dept, $spuArchiveDepArr)){//检查要所搜的部门是否包含在定义内
            $personIdArr = OmAvailableModel::getAllPersonIdByDeptId($dept);//每个销售部下面的人员id的arr
            $personStr = implode(',',array_filter($personIdArr));
            if(!empty($personStr)){
                $where .= "AND purchaseId in($personStr) ";
            }
        }
        if ($startdate != '') {
        	$start = strtotime($startdate . ' 00:00:00');
        	$where .= "AND spuCreatedTime>='$start' ";
        }
        if ($enddate != '') {
        	$end = strtotime($enddate . ' 23:59:59');
        	$where .= "AND spuCreatedTime<='$end' ";
        }

		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY auditTime DESC,spuCreatedTime DESC ".$page->limit;
		$spuArchiveList = $omAvailableAct->act_getTNameList($tName, $select, $where);

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
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getSpuArchiveList',
				'title' => 'SPU档案管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 23);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', 'SPU档案管理');
        //取得搜索类别的记录
        $pidArr = explode('-',$pid);
        $this->smarty->assign('pidArr', $pidArr);
		$this->smarty->assign('status', $status);
        if(!empty($spuArchiveList)){
            $countSpuArchiveList = count($spuArchiveList);
            $allSpuStatusName = displayAllSpuStatus();
            for($i=0;$i<$countSpuArchiveList;$i++){
                $spuArchiveList[$i]['spuStatusName'] = $allSpuStatusName[$spuArchiveList[$i]['spuStatus']]['statusName'];
            }
        }
		$this->smarty->assign('spuArchiveList', empty ($spuArchiveList) ? null : $spuArchiveList);
		$this->smarty->display("spuArchiveList.htm");
	}

	public function view_scanSpuArchive() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		///////////////////检查spu是否非法
		$tName = 'pc_spu_archive';
		$select = '*';
		$where = "WHERE spu='$spu' and is_delete=0";
		$spuArchiveList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuArchiveList)) {
			$status = "SPU档案中不存在 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status");
			exit;
		}
		$tName = 'pc_archive_spu_property_value_relation';
		$select = '*';
		$where = "WHERE spu='$spu'";
		$PPV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_input_value_relation';
		$INV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_link';
		$Link = OmAvailableModel :: getTNameList($tName, $select, $where);

        //add 20140526 获取SPUPKSKU信息
        $tName = 'pc_spu_archive_pk_sku';
		$spuPkSkuList = OmAvailableModel :: getTNameList($tName, $select, $where);
        $tmpArr = array();
        foreach($spuPkSkuList as $value){
            $tmpArr[] = $value['sku'];
        }
        $spuPkSkuList = $tmpArr;

        $tName = 'pc_goods';
        $select = 'sku';
        $where = "WHERE is_delete=0 and spu='{$spuArchiveList[0]['secretInfo']}'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);

        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getSpuArchiveList',
				'title' => 'SPU档案管理'
			),
			array (
				'url' => "index.php?mod=autoCreateSpu&act=scanSpuArchive&spu=$spu",
				'title' => "浏览SPU档案_$spu"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 23);
		$this->smarty->assign('title', 'SPU档案管理');
		$this->smarty->assign('spu', $spuArchiveList[0]['spu']);
		$this->smarty->assign('pid', $spuArchiveList[0]['categoryPath']);
		$this->smarty->assign('spuName', $spuArchiveList[0]['spuName']);
		$this->smarty->assign('spuPurchasePrice', $spuArchiveList[0]['spuPurchasePrice']);
		$this->smarty->assign('spuLowestPrice', $spuArchiveList[0]['spuLowestPrice']);
		$this->smarty->assign('referMonthSales', $spuArchiveList[0]['referMonthSales']);
		$this->smarty->assign('lowestUrl', $spuArchiveList[0]['lowestUrl']);
		$this->smarty->assign('bidUrl', $spuArchiveList[0]['bidUrl']);
		$this->smarty->assign('spuCalWeight', $spuArchiveList[0]['spuCalWeight']);
		//$this->smarty->assign('isPacking', $spuArchiveList[0]['isPacking']);
        $this->smarty->assign('minNum', $spuArchiveList[0]['minNum']);
        $this->smarty->assign('freight', $spuArchiveList[0]['freight']);
        $this->smarty->assign('platformId', $spuArchiveList[0]['platformId']);
        $this->smarty->assign('secretInfo', $spuArchiveList[0]['secretInfo']);

		$this->smarty->assign('spuNote', $spuArchiveList[0]['spuNote']);
		$this->smarty->assign('spuStatus', $spuArchiveList[0]['spuStatus']);
		$this->smarty->assign('auditStatus', $spuArchiveList[0]['auditStatus']);
        $this->smarty->assign('purchaseId', $spuArchiveList[0]['purchaseId']);

		$this->smarty->assign('PPV', $PPV);
		$this->smarty->assign('INV', $INV);
		$this->smarty->assign('Link', $Link);
        $this->smarty->assign('skuList', $skuList);
        $this->smarty->assign('spuPkSkuList', $spuPkSkuList);
		$pathImplodeStr = getAllPathBypid($spuArchiveList[0]['categoryPath']);

        $tName = 'pc_goods';
        $where = "WHERE spu='$spu' and isNew=0";
        $countSpuSku = OmAvailableModel::getTNameCount($tName, $where);
        if($countSpuSku){//该spu对应的sku是老品的时候才加载图片
            $spuPicList = getAllArtPicFromOpenSysSpu($spu);
        }else{
            $spuPicList = array();
        }
        $tName = 'pc_special_property_spu';
        $select = 'propertyId';
        $where = "WHERE spu='$spu'";
        $pspsList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($pspsList)){
            $pspsIdArr = array();
            foreach($pspsList as $pspsValue){
                $pspsIdArr[] = $pspsValue['propertyId'];
            }
            $this->smarty->assign('spId', $pspsIdArr);
        }
        $this->smarty->assign('spuPicList', $spuPicList);
		$this->smarty->assign('pathImplodeStr', $pathImplodeStr);
		$this->smarty->display("scanSpuArchive.htm");
	}

	public function view_auditSpuArchive() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		$auditStatus = $_GET['auditStatus'] ? post_check(trim($_GET['auditStatus'])) : '';

        $seach_spu = isset ($_GET['seach_spu']) ? $_GET['seach_spu'] : '';
        $seach_spuStatus = isset ($_GET['seach_spuStatus']) ? $_GET['seach_spuStatus'] : '';
        $seach_auditStatus = isset ($_GET['seach_auditStatus']) ? $_GET['seach_auditStatus'] : '';
		$seach_purchaseId = isset ($_GET['seach_purchaseId']) ? $_GET['seach_purchaseId'] : '';
		$seach_pid = isset ($_GET['seach_pid']) ? $_GET['seach_pid'] : '';
        $seach_isPPVRecord = isset ($_GET['seach_isPPVRecord']) ? $_GET['seach_isPPVRecord'] : '';
        $seach_haveSizePPV = isset ($_GET['seach_haveSizePPV']) ? $_GET['seach_haveSizePPV'] : '';
        $seach_isMeasureRecord = isset ($_GET['seach_isMeasureRecord']) ? $_GET['seach_isMeasureRecord'] : '';
        $seach_dept = isset ($_GET['seach_dept']) ? $_GET['seach_dept'] : '';
        $seach_page = isset ($_GET['seach_page']) ? $_GET['seach_page'] : '';
        $seach_startdate = isset ($_GET['seach_startdate']) ? $_GET['seach_startdate'] : '';
        $seach_enddate = isset ($_GET['seach_enddate']) ? $_GET['seach_enddate'] : '';
        $auditorId = $_SESSION['userId'];
        $auditTime = time();
        if(intval($auditorId) <= 0){
            $status = "未登陆用户";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
        }
		//检查spu是否非法
		if (!preg_match("/^[A-Z]{2}[0-9]{6}$/", $spu)) {
			$status = "非法spu";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if ($auditStatus != 2 && $auditStatus != 3) {
			$status = "非法审核状态";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$auditStr = $auditStatus == 2 ? '审核通过' : '审核不通过';
		$tName = 'pc_spu_archive';
		$select = '*';
		$where = "WHERE spu='$spu' and auditStatus=1 and is_delete=0";
		$spuArchiveList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuArchiveList)) {
			$status = "SPU档案中不存在未审核的$spu";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$set = "SET auditStatus=$auditStatus,auditorId=$auditorId,auditTime='$auditTime'";
		$where = "WHERE spu='$spu'";
		$affectRow = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if (!$affectRow) {
			$status = "$spu $auditStr 失败";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		    $status = "$spu $auditStr 成功";
            header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
	}

	public function view_updateSpuArchive() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		///////////////////检查spu是否非法
		$tName = 'pc_spu_archive';
		$select = '*';
		$where = "WHERE spu='$spu' and is_delete=0";
		$spuArchiveList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuArchiveList)) {
			$status = "SPU档案中不存在 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status");
			exit;
		}
		$tName = 'pc_archive_spu_property_value_relation';
		$select = '*';
		$where = "WHERE spu='$spu'";
		$PPV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_input_value_relation';
		$INV = OmAvailableModel :: getTNameList($tName, $select, $where);

		$tName = 'pc_archive_spu_link';
		$Link = OmAvailableModel :: getTNameList($tName, $select, $where);

        //add 20140526 获取SPUPKSKU信息
        $tName = 'pc_spu_archive_pk_sku';
		$spuPkSkuList = OmAvailableModel :: getTNameList($tName, $select, $where);
        $tmpArr = array();
        foreach($spuPkSkuList as $value){
            $tmpArr[] = $value['sku'];
        }
        $spuPkSkuList = $tmpArr;

        $tName = 'pc_goods';
        $select = 'sku';
        $where = "WHERE is_delete=0 and spu='{$spuArchiveList[0]['secretInfo']}'";
        $skuList = OmAvailableModel::getTNameList($tName, $select, $where);

        $navlist = array (//面包屑
	        array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getSpuArchiveList',
				'title' => 'SPU档案管理'
			),
			array (
				'url' => "index.php?mod=autoCreateSpu&act=updateSpuArchive&spu=$spu",
				'title' => "SPU档案修改_$spu"
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 23);
		$this->smarty->assign('title', 'SPU档案修改');
		$this->smarty->assign('spu', $spuArchiveList[0]['spu']);
		$this->smarty->assign('pid', $spuArchiveList[0]['categoryPath']);
        $this->smarty->assign('pidArr', explode('-',$spuArchiveList[0]['categoryPath']));
		$this->smarty->assign('spuName', $spuArchiveList[0]['spuName']);
		$this->smarty->assign('spuPurchasePrice', $spuArchiveList[0]['spuPurchasePrice']);
        $this->smarty->assign('spuCalWeight', $spuArchiveList[0]['spuCalWeight']);
		$this->smarty->assign('spuLowestPrice', $spuArchiveList[0]['spuLowestPrice']);
		$this->smarty->assign('referMonthSales', $spuArchiveList[0]['referMonthSales']);

        $this->smarty->assign('minNum', $spuArchiveList[0]['minNum']);
        $this->smarty->assign('freight', $spuArchiveList[0]['freight']);
        $this->smarty->assign('platformId', $spuArchiveList[0]['platformId']);
        $this->smarty->assign('secretInfo', $spuArchiveList[0]['secretInfo']);

		$this->smarty->assign('lowestUrl', $spuArchiveList[0]['lowestUrl']);
		$this->smarty->assign('bidUrl', $spuArchiveList[0]['bidUrl']);


		$this->smarty->assign('spuNote', $spuArchiveList[0]['spuNote']);
		$this->smarty->assign('spuStatus', $spuArchiveList[0]['spuStatus']);
		$this->smarty->assign('auditStatus', $spuArchiveList[0]['auditStatus']);

		$this->smarty->assign('PPV', $PPV);
		$this->smarty->assign('INV', $INV);
		$this->smarty->assign('Link', $Link);

        $this->smarty->assign('spuPkSkuList', $spuPkSkuList);
        $this->smarty->assign('skuList', $skuList);

		$pathImplodeStr = getAllPathBypid($spuArchiveList[0]['categoryPath']);
		$this->smarty->assign('pathImplodeStr', $pathImplodeStr);

	    $tName = 'pc_goods';
        $where = "WHERE spu='$spu' and isNew=0";
        $countSpuSku = OmAvailableModel::getTNameCount($tName, $where);
        if($countSpuSku){//该spu对应的sku是老品的时候才加载图片
            $spuPicList = getAllArtPicFromOpenSysSpu($spu);
        }else{
            $spuPicList = array();
        }
        $tName = 'pc_special_property_spu';
        $select = 'propertyId';
        $where = "WHERE spu='$spu'";
        $pspsList = OmAvailableModel::getTNameList($tName, $select, $where);
        if(!empty($pspsList)){
            $pspsIdArr = array();
            foreach($pspsList as $pspsValue){
                $pspsIdArr[] = $pspsValue['propertyId'];
            }
            $this->smarty->assign('spId', $pspsIdArr);
        }
        $this->smarty->assign('spuPicList', $spuPicList);
		$this->smarty->display("updateSpuArchive.htm");
	}

	public function view_updateSpuArchiveOn() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
        $pid = $_POST['pid'] ? post_check(trim($_POST['pid'])) : '';
		$seach_spu = isset ($_POST['seach_spu']) ? $_POST['seach_spu'] : '';
        $seach_auditStatus = isset ($_POST['seach_auditStatus']) ? $_POST['seach_auditStatus'] : '';
        $seach_spuStatus = isset ($_POST['seach_spuStatus']) ? $_POST['seach_spuStatus'] : '';
		$seach_purchaseId = isset ($_POST['seach_purchaseId']) ? $_POST['seach_purchaseId'] : '';
		$seach_pid = isset ($_POST['seach_pid']) ? $_POST['seach_pid'] : '';
        $seach_isPPVRecord = isset ($_POST['seach_isPPVRecord']) ? $_POST['seach_isPPVRecord'] : '';
        $seach_haveSizePPV = isset ($_POST['seach_haveSizePPV']) ? $_POST['seach_haveSizePPV'] : '';
        $seach_isMeasureRecord = isset ($_POST['seach_isMeasureRecord']) ? $_POST['seach_isMeasureRecord'] : '';
        $seach_dept = isset ($_POST['seach_dept']) ? $_POST['seach_dept'] : '';
        $seach_page = isset ($_POST['seach_page']) ? $_POST['seach_page'] : '';
        $seach_startdate = isset ($_POST['seach_startdate']) ? $_POST['seach_startdate'] : '';
        $seach_enddate = isset ($_POST['seach_enddate']) ? $_POST['seach_enddate'] : '';
		//检查spu,pid是否非法
		if (empty ($spu)) {
			$status = "spu为空";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (empty ($pid)) {
			$status = "类别为空";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$select = 'sort,purchaseId,isSingSpu';
		$where = "WHERE spu='$spu' and is_delete=0";
		$autoSpuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($autoSpuList)) {
			$status = "自动生成SPU中不存在 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$tName = 'pc_spu_archive';
		$where = "WHERE spu='$spu' and categoryPath='$pid' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "产品档案中不存在 $spu 的信息";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$tName = 'pc_goods_category';
		$where = "WHERE path='$pid' and is_delete=0";
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if (!$count) {
			$status = "系统不存在该类别";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		//获取网页传来的数据
		$dataSpu = array (); //spu档案属性
		$dataSpu['spuName'] = $_POST['spuName'] ? trim($_POST['spuName']) : ''; //描述
		$dataSpu['spuPurchasePrice'] = $_POST['spuPurchasePrice'] ? post_check(trim($_POST['spuPurchasePrice'])) : '';
		$dataSpu['spuLowestPrice'] = $_POST['spuLowestPrice'] ? post_check(trim($_POST['spuLowestPrice'])) : '';
		$dataSpu['spuCalWeight'] = $_POST['spuCalWeight'] ? post_check(trim($_POST['spuCalWeight'])) : '';
		$dataSpu['referMonthSales'] = $_POST['referMonthSales'] ? post_check(trim($_POST['referMonthSales'])) : '';

        $spId = $_POST['spId'];//特殊属性数组 add zqt 20140819

        $dataSpu['minNum'] = $_POST['minNum'] ? post_check(trim($_POST['minNum'])) : '';//起订量
        $dataSpu['platformId'] = $_POST['platformId'] ? post_check(trim($_POST['platformId'])) : '';//对应平台
        $dataSpu['freight'] = $_POST['freight'] ? post_check(trim($_POST['freight'])) : 0;//运费
        $dataSpu['secretInfo'] = $_POST['secretInfo'] ? post_check(trim($_POST['secretInfo'])) : '';//隐私信息，目前为被PK料号信息

        $dataSpu['lowestUrl'] = $_POST['lowestUrl'] ? post_check(trim($_POST['lowestUrl'])) : '';
		$dataSpu['bidUrl'] = $_POST['bidUrl'] ? post_check(trim($_POST['bidUrl'])) : '';
		if (empty ($dataSpu['spuName'])) {
			$status = "描述不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (!is_numeric($dataSpu['spuPurchasePrice']) || $dataSpu['spuPurchasePrice'] <= 0) {
			$status = "采购价必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (!is_numeric($dataSpu['spuLowestPrice']) || $dataSpu['spuLowestPrice'] <= 0) {
			$status = "市场最低价必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (!is_numeric($dataSpu['spuCalWeight']) || $dataSpu['spuCalWeight'] <= 0) {
			$status = "估算重量必须是正数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (intval($dataSpu['referMonthSales']) == 0) {
			$status = "参考月销量必须为大于0的正数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
        if (intval($dataSpu['minNum']) <= 0) {
			$status = "起订量必须为大于0的正数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
        if (intval($dataSpu['platformId']) <= 0) {
			$status = "对应平台选择有误";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
        if (!is_numeric($dataSpu['freight']) || $dataSpu['freight'] < 0) {
			$status = "运费必须为非负数";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (empty ($dataSpu['lowestUrl'])) {
			$status = "最低价链接不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		if (empty ($dataSpu['bidUrl'])) {
			$status = "参考listing链接不能为空";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
			exit;
		}
		$dataSpu['spuNote'] = $_POST['spuNote'] ? post_check(trim($_POST['spuNote'])) : '';
		$dataSpu['spuStatus'] = $_POST['spuStatus'] ? post_check(trim($_POST['spuStatus'])) : 1;
        $dataSpu['spuCreatedTime'] = time();//最后一次修改的时间
		$dataLink = array (); //spuLink属性
		$dataLinkTmp['spu'] = $spu;
		$linkUrlArr = $_POST['linkUrl'];
		$linkNoteArr = $_POST['linkNote'];
		for ($i = 0; $i < count($linkUrlArr); $i++) {
			$dataLinkTmp['spu'] = $spu;
			$dataLinkTmp['linkUrl'] = $linkUrlArr[$i];
			$dataLinkTmp['linkNote'] = $linkNoteArr[$i];
			$dataLink[] = $dataLinkTmp;
		}

        //add 20140526 筛选的PK SKU
        if($dataSpu['spuStatus'] == 51 && empty($dataSpu['secretInfo'])){
            $tmpStatus = "PK状态下，PK的SPU不能为空！";
            echo '<script language="javascript">
                    alert("'.$tmpStatus.'");
                    history.back();
                  </script>';
			exit;
        }
        if(!preg_match("/^[A-Z0-9]+$/",$dataSpu['secretInfo']) && !empty($dataSpu['secretInfo'])){
            $tmpStatus = "被PK的SPU只能是大写字母和数字字符串！";
            echo '<script language="javascript">
                    alert("'.$tmpStatus.'");
                    history.back();
                  </script>';
			exit;
        }
        $pkSkuArr = !empty($_POST['pkSku'])?$_POST['pkSku']:array();
        if($dataSpu['spuStatus'] == 51){//状态为pk
            $tName = 'pc_spu_archive';
            $select = 'auditTime';
            $where = "WHERE is_delete=0 and spu='$spu'";
            $auditTimeList = OmAvailableModel::getTNameList($tName, $select, $where);
            if($auditTimeList[0]['auditTime'] > 1401465600 && count($pkSkuArr) <= 0){//审核时间大于这个时候开始验证，否则不验证pkSku的值
                $tmpStatus = "请填写完PK的SPU后点击'筛选SKU'按钮选择所PK的SKU，谢谢！";
                echo '<script language="javascript">
                        alert("'.$tmpStatus.'");
                        history.back();
                      </script>';
    			exit;
            }

        }
        //

		$pathImplodeStr = getAllPathBypid($pid);
		$dataPro = array ();
		$tName = 'pc_archive_property';
		$select = '*';
		$where = "WHERE categoryPath IN ($pathImplodeStr)";
		$proList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($proList)) {
			foreach ($proList as $value) {
				$tmpArr = array ();
				if ($value['isRadio'] == 1) { //单选
					$tmpArr['spu'] = $spu;
					$tmpArr['propertyId'] = $value['id'];
					$tmpArr['propertyValueId'] = $_POST['pro' . $value['id']]; //单选时，$_POST['pro'.$value['id']]存放的是一个字符串
					$dataPro[] = $tmpArr;
				} else { //多选
					//多选时，$_POST['pro'.$value['id']]存放的是一个数组,固定某个propertyId下的多个值
					$tmpPostValueArr = $_POST['pro' . $value['id']];
					if (!empty ($tmpPostValueArr)) {
						foreach ($tmpPostValueArr as $value2) {
							$tmpArr['spu'] = $spu;
							$tmpArr['propertyId'] = $value['id'];
							$tmpArr['propertyValueId'] = $value2;
							$dataPro[] = $tmpArr;
						}
					}

				}

			}
		}
		$dataInp = array ();
		$tName = 'pc_archive_input';
		$select = '*';
		$where = "WHERE categoryPath IN ($pathImplodeStr)";
		$inpList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (!empty ($inpList)) {
			foreach ($inpList as $value) {
				$tmpArr = array ();
				$tmpArr['spu'] = $spu;
				$tmpArr['inputId'] = $value['id'];
				$tmpArr['inputValue'] = $_POST['inp' . $value['id']];
				$dataInp[] = $tmpArr;
			}
		}
		try {
			BaseModel :: begin();
			//pc_spu_archive中插入数据
			$tName = 'pc_spu_archive';
			$where = "WHERE spu='$spu'";
			$affectRowSpu = OmAvailableModel :: updateTNameRow2arr($tName, $dataSpu, $where);
			if ($affectRowSpu === false) {
				throw new Exception('add pc_spu_archive error');
			}
            
            //add zqt 20140711 添加特殊属性
            if(!empty($spId) && is_array($spId)){
                $tName = 'pc_special_property_spu';
                $where = "WHERE spu='$spu'";
                OmAvailableModel::deleteTNameRow($tName, $where);
                foreach($spId as $propertyIdValue){
                    $propertyIdValue = intval($propertyIdValue);
                    $dataTmpArr = array();
                    $dataTmpArr['propertyId'] = $propertyIdValue;
                    $dataTmpArr['spu'] = $spu;
                    OmAvailableModel::addTNameRow2arr($tName, $dataTmpArr);
                }                                
            }
            
            //add 20140526 更新PK细分SKU
            $tName = 'pc_spu_archive_pk_sku';
            $where = "WHERE spu='$spu'";
            OmAvailableModel::deleteTNameRow($tName, $where);//先删掉之前关联的记录
            foreach($pkSkuArr as $value){
                $tName = 'pc_spu_archive_pk_sku';
                $dataPkSkuArr['spu'] = $spu;
                $dataPkSkuArr['sku'] = $value;
                OmAvailableModel::addTNameRow2arr($tName, $dataPkSkuArr);
            }
			//pc_archive_spu_link中插入数据
			$tName = 'pc_archive_spu_link';
			if (!empty ($dataLink)) {
				$affectRowDelLnk = OmAvailableModel :: deleteTNameRow($tName, $where);
				if ($affectRowDelLnk === false) {
					throw new Exception('delete pc_archive_spu_link error');
				}
				foreach ($dataLink as $value) {
					if (!empty ($value['linkUrl'])) {
						$insertIdLink = OmAvailableModel :: addTNameRow2arr($tName, $value);
						if ($insertIdLink === false) {
							throw new Exception('add pc_archive_spu_link error');
						}
					}
				}
			}

			//pc_archive_spu_property_value_relation中插入数据
			$tName = 'pc_archive_spu_property_value_relation';
			if (!empty ($dataPro)) {
				$affectRowDelPro = OmAvailableModel :: deleteTNameRow($tName, $where);
				if ($affectRowDelPro === false) {
					throw new Exception('delete pc_archive_spu_property_value_relation error');
				}
				foreach ($dataPro as $value) {
					if (!empty ($value['propertyValueId'])) {
						$insertIdPro = OmAvailableModel :: addTNameRow2arr($tName, $value);
						if ($insertIdPro === false) {
							throw new Exception('add pc_archive_spu_property_value_relation error');
						}
					}
				}
			}
			//pc_archive_spu_input_value_relation中插入数据
			$tName = 'pc_archive_spu_input_value_relation';
			if (!empty ($dataInp)) {
				$affectRowDelInp = OmAvailableModel :: deleteTNameRow($tName, $where);
				if ($affectRowDelInp === false) {
					throw new Exception('delete pc_archive_spu_input_value_relation error');
				}
				foreach ($dataInp as $value) {
					if (trim($value['inputValue']) != '') {
						$insertIdPro = OmAvailableModel :: addTNameRow2arr($tName, $value);
						if ($insertIdPro === false) {
							throw new Exception('add pc_archive_spu_property_value_relation error');
						}
					}
				}
			}
            //添加尺寸测量项
            $spuPPVrelationList = isExistForSpuPPV($spu, '尺码');//先获取该spu下是否已经存在尺寸的属性值
            if(!empty($spuPPVrelationList)){//如果该spu下存在尺码属性值
                foreach($spuPPVrelationList as $valueRelation){//循环遍历，将尺码下的属性值全部遍历出来，如S,M,X,XL等
                    $tName = 'pc_archive_input';
                    $select = '*';
                    $where = "WHERE textStatus=1 AND categoryPath IN ($pathImplodeStr)";
                    $inputList = OmAvailableModel::getTNameList($tName, $select, $where);//根据$pid，获取该类别下的尺寸测量属性值，如胸围，腰围，衣长，
                    foreach($inputList as $valueInput){
                        $inputName = $valueInput['inputName'];//根据inputId获取对应名称
                        $PPVname = getPPVForPPVId($valueRelation['propertyValueId']);//根据ppvId获取对应的名称
                        $postNameValue = $_POST[$inputName.$PPVname] ? post_check(trim($_POST[$inputName.$PPVname])) : '';//$inputName.$PPVname为前台传过来的对应值
                        //echo $postNameValue.'<br/>';
                        if(!empty($postNameValue) && !empty($inputName) && !empty($PPVname)){//如果前端传过来的值不为空的话
                            $tName = 'pc_archive_spu_input_size_measure';
                            $where = "WHERE spu='$spu' and sizeName='$PPVname' and inputName='$inputName'";
                            $spuMeasureCount = OmAvailableModel::getTNameCount($tName, $where);
                            if($spuMeasureCount){//记录存在的话，则修改
                                $set = "SET valued='$postNameValue'";
                                OmAvailableModel::updateTNameRow($tName, $set, $where);
                            }else{//不存在则插入
                                $dataMeasure = array();
                                $dataMeasure['spu'] = $spu;
                                $dataMeasure['sizeName'] = $PPVname;
                                $dataMeasure['inputName'] = $inputName;
                                $dataMeasure['valued'] = $postNameValue;
                                $dataMeasure['addUserId'] = $_SESSION['userId'];
                                $dataMeasure['addTime'] = time();
                                OmAvailableModel::addTNameRow2arr($tName, $dataMeasure);
                            }

                        }
                    }
                }
            }
			BaseModel :: commit();
			BaseModel :: autoCommit();
			$status = "$spu 产品档案修改成功";
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
            $status = $e->getMessage();
			header("Location:index.php?mod=autoCreateSpu&act=getSpuArchiveList&status=$status&spu=$seach_spu&spuStatus=$seach_spuStatus&auditStatus=$seach_auditStatus&purchaseId=$seach_purchaseId&pid=$seach_pid&isPPVRecord=$seach_isPPVRecord&haveSizePPV=$seach_haveSizePPV&isMeasureRecord=$seach_isMeasureRecord&dept=$seach_dept&page=$seach_page&startdate=$seach_startdate&enddate=$seach_enddate");
		}
	}

	public function view_addSku() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';
		//检查spu,pid是否非法
		if (empty ($spu)) {
			$status = "非法spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status&spu=$spu");
			exit;
		}
		$tName = 'pc_auto_create_spu';
		$select = 'sort,purchaseId,isSingSpu';
		$where = "WHERE spu='$spu' and is_delete=0";
		$autoSpuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($autoSpuList)) {
			$status = "自动生成SPU列表中不存在 $spu";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
		$isSingSpu = $autoSpuList[0]['isSingSpu']; //单/虚拟料号
		if ($isSingSpu == 1) { //单料号
			$tName = 'pc_spu_archive';
			$select = '*';
			$where = "WHERE spu='$spu' and is_delete=0";
			$spuArchiveList = OmAvailableModel :: getTNameList($tName, $select, $where);
			if (empty ($spuArchiveList)) {
				$status = "未找到 $spu 的产品档案信息";
				header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
				exit;
			}
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
					'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
					'title' => 'SPU管理'
				),
				array (
					'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
					'title' => '生成SPU列表管理'
				),
				array (
					'url' => "index.php?mod=autoCreateSpu&act=addSku&spu=$spu",
					'title' => "添加单料号_$spu"
				)
			);
			$this->smarty->assign('navlist', $navlist);
			$this->smarty->assign('onevar', 2);
			$this->smarty->assign('twovar', 22);
			$this->smarty->assign('title', '添加单料号');
			$this->smarty->assign('spu', $spuArchiveList[0]['spu']);
			$this->smarty->assign('pid', $spuArchiveList[0]['categoryPath']);
			$this->smarty->assign('spuName', $spuArchiveList[0]['spuName']);
			$this->smarty->assign('spuPurchasePrice', $spuArchiveList[0]['spuPurchasePrice']);
			$this->smarty->assign('purchaseId', $spuArchiveList[0]['purchaseId']);
			$this->smarty->assign('spuLowestPrice', $spuArchiveList[0]['spuLowestPrice']);
			$this->smarty->assign('referMonthSales', $spuArchiveList[0]['referMonthSales']);
			$this->smarty->assign('lowestUrl', $spuArchiveList[0]['lowestUrl']);
			$this->smarty->assign('bidUrl', $spuArchiveList[0]['bidUrl']);
			$this->smarty->assign('spuLength', $spuArchiveList[0]['spuLength']);
			$this->smarty->assign('spuWidth', $spuArchiveList[0]['spuWidth']);
			$this->smarty->assign('spuCalWeight', $spuArchiveList[0]['spuCalWeight']);
			$this->smarty->assign('spuHeight', $spuArchiveList[0]['spuHeight']);
			$this->smarty->assign('isPacking', $spuArchiveList[0]['isPacking']);
			$this->smarty->assign('pmId', $spuArchiveList[0]['pmId']);
			$this->smarty->assign('spuNote', $spuArchiveList[0]['spuNote']);
			$this->smarty->assign('spuStatus', $spuArchiveList[0]['spuStatus']);
			$this->smarty->assign('auditStatus', $spuArchiveList[0]['auditStatus']);

			$pathImplodeStr = getAllPathBypid($spuArchiveList[0]['categoryPath']);
			$this->smarty->assign('pathImplodeStr', $pathImplodeStr);

            $isColor = isExistForSpuPPV($spu,'颜色');//检测该spu是否关联了颜色属性值
            $isSize = isExistForSpuPPV($spu,'尺码');

            $isSpuExist = isSpuExist($spu);

            $this->smarty->assign('isColor', $isColor);
			$this->smarty->assign('isSize', $isSize);
            $this->smarty->assign('isSpuExist', $isSpuExist);

            $isRelatedColor = isRelatedWithPidAndPP($spuArchiveList[0]['categoryPath'], '颜色');//检测该类是否关联了颜色属性
            $isRelatedSize = isRelatedWithPidAndPP($spuArchiveList[0]['categoryPath'], '尺码');

            $this->smarty->assign('isRelatedColor', $isRelatedColor);
			$this->smarty->assign('isRelatedSize', $isRelatedSize);

			$this->smarty->assign('PPV', $PPV);
			$this->smarty->assign('INV', $INV);
			$this->smarty->assign('Link', $Link);
			$this->smarty->assign('onevar', 2);
			$this->smarty->assign('twovar', 22);

			$this->smarty->display("addSkuSing.htm");
		} else { //虚拟料号
            $amount = $_GET['amount'] ? post_check(trim($_GET['amount'])) : 0;
            $amount = intval($amount);
            if($amount <= 0 || $amount >100){
                $status = "数量必须在1-100之间";
				header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
				exit;
            }
            $navlist = array (//面包屑
	            array (
					'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
					'title' => 'SPU管理'
				),
				array (
					'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreateSpuList',
					'title' => '生成SPU列表管理'
				),
				array (
					'url' => "index.php?mod=autoCreateSpu&act=addSku&spu=$spu",
					'title' => "添加虚拟料号_$spu"
				)
			);
			$this->smarty->assign('navlist', $navlist);
			$this->smarty->assign('onevar', 2);
			$this->smarty->assign('twovar', 22);
			$this->smarty->assign('title', '添加虚拟料号');
			$this->smarty->assign('combineSpu', $spu);
            $this->smarty->assign('amount', $amount);
			$this->smarty->display("addCombine.htm");
		}
	}

	public function view_addSkuSingOn() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
		$pid = $_POST['pid'] ? post_check(trim($_POST['pid'])) : '';
		$id = $_POST['id'] ? post_check(trim($_POST['id'])) : '';
		$goodsStatus = $_POST['goodsStatus'] ? post_check(trim($_POST['goodsStatus'])) : '';
		$isNew = $_POST['isNew'] ? post_check(trim($_POST['isNew'])) : '';
		if (!preg_match("/^[A-Z0-9]+$/", $spu)) {
			$status = "非法spu";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
		if (empty ($pid)) {
			$status = "类别为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
		$skuArr = $_POST['sku'];
		$goodsNameArr = $_POST['goodsName'];
		$goodsCostArr = $_POST['goodsCost'];
		$goodsNoteArr = $_POST['goodsNote'];
		$goodsColorArr = $_POST['goodsColor'];
		$goodsSizeArr = $_POST['goodsSize'];
		if (!isset($skuArr[0])) {
			$status = "空的SKU记录";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
		if (empty ($skuArr[0]) && !empty ($skuArr[1])) {
			$status = "只能是一个子料号情况下，SKU才能为空";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
		if (count($skuArr) != count(array_unique($skuArr))) {
			$status = "错误，存在重复的SKU";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
        $userId = $_SESSION['userId'];
        if(intval($userId) <= 0){
            $status = "登陆超时，请重新登陆！";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
        }
        for ($i = 0; $i < count($skuArr); $i++) {
				$sku = post_check($spu.trim($skuArr[$i]));
                if(strlen($sku) > 30){
                    $status = "$sku 字符长度大于30，错误！";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
                }
                if(!preg_match("/^$spu(_[A-Z0-9]+)*$/", $sku)){
                    $status = "$sku 格式非法";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
                }
				$goodsName = !empty ($goodsNameArr[$i]) ? $goodsNameArr[$i] : '';
				$goodsCost = !empty ($goodsCostArr[$i]) ? $goodsCostArr[$i] : 0;
				$goodsNote = !empty ($goodsNoteArr[$i]) ? $goodsNoteArr[$i] : '';
				$goodsColor = !empty ($goodsColorArr[$i]) ? $goodsColorArr[$i] : 0;
				$goodsSize = !empty ($goodsSizeArr[$i]) ? $goodsSizeArr[$i] : 0;

				if ($i >= 1 && $sku == $spu) {
					continue;
				}

				if (empty ($goodsName)) {
					$status = "$sku 的描述不能为空";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}
                if (strpos($goodsName, '#') !== false || strpos($goodsNote, '#') !== false) {//如果描述中有#则报错
					$status = "$sku 的描述/备注 不能含有'井'号等特殊字符";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}
				if (!is_numeric($goodsCost) || $goodsCost <= 0) {
					$status = "$sku 的成本必须为正数";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}

				$tName = 'pc_goods';
				$where = "WHERE is_delete=0 AND sku='$sku'";
				$count = OmAvailableModel :: getTNameCount($tName, $where);
				if ($count) {
					$status = "$sku 已经存在";
					echo '<script language="javascript">
                            alert("'.$status.'");
                          </script>';
					exit;
				}

        }


		try {
            $ebayGoodsArr = array();//同步数据数组
			BaseModel :: begin();
			for ($i = 0; $i < count($skuArr); $i++) {
				$sku = post_check($spu.trim($skuArr[$i]));

				$goodsName = !empty ($goodsNameArr[$i]) ? $goodsNameArr[$i] : '';
				$goodsCost = !empty ($goodsCostArr[$i]) ? $goodsCostArr[$i] : 0;
				$goodsNote = !empty ($goodsNoteArr[$i]) ? $goodsNoteArr[$i] : '';
				$goodsColor = !empty ($goodsColorArr[$i]) ? $goodsColorArr[$i] : 0;
				$goodsSize = !empty ($goodsSizeArr[$i]) ? $goodsSizeArr[$i] : 0;

				if ($i >= 1 && $sku == $spu) {
					continue;
				}

				$dataSku = array ();
				$dataSku['spu'] = $spu;
				$dataSku['goodsCategory'] = $pid;
				$dataSku['purchaseId'] = $userId;

				$dataSku['goodsCreatedTime'] = time();
				$dataSku['goodsSort'] = intval(substr($spu, 2));
				$dataSku['sku'] = $sku;
				$dataSku['goodsName'] = $goodsName;
				$dataSku['goodsCost'] = $goodsCost;
				$dataSku['goodsNote'] = $goodsNote;
				$dataSku['goodsStatus'] = $goodsStatus;
				$dataSku['isNew'] = $isNew;
				$dataSku['goodsColor'] = $goodsColor;
				$dataSku['goodsSize'] = $goodsSize;

				$tName = 'pc_goods';
				$insertId = OmAvailableModel :: addTNameRow2arr($tName, $dataSku);

                addWeightBackupsModify($sku, 0, $userId, false);//添加默认的重量变化记录
                addVolumeBackupsModify($sku, 0, 0, 0, $userId, false);//添加默认的体积变化记录
                addPmBackupsModify($sku, 0, 1, $userId, false);//添加默认的包材变化记录
                addCostBackupsModify($sku, $goodsCost, $userId, false);//添加默认的成本变化记录
                addStatusBackupsModify($sku, $goodsStatus, '', $userId, false);//添加默认的状态变化记录
				//将新添加的sku添加到mem中
				$key = 'pc_goods_' . $sku;
				$value = $dataSku;
				setMemNewByKey($key, $value); //这里不保证能添加成功

				//同步新数据到旧系统中
				$ebayGoods = array ();
				$ebayGoods['goods_id'] = $insertId;
				$ebayGoods['goods_name'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$goodsName);
				$ebayGoods['goods_sn'] = $sku;
				$ebayGoods['goods_price'] = $goodsCost;
				$ebayGoods['goods_cost'] = $goodsCost;
				//$ebayGoods['goods_weight'] = $goodsWeight;
				$ebayGoods['goods_note'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",$goodsNote);
				$ebayGoods['goods_category'] = $pid;
				//$ebayGoods['isPacking'] = $isPacking;
				$ebayGoods['ebay_user'] = 'vipchen';
				//$ebayGoods['factory'] = $partnerId;
				$ebayGoods['cguser'] = empty ($_SESSION['userId']) ? '' : getPersonNameById($_SESSION['userId']);
				//$ebayGoods['capacity'] = $pmCapacity;
				//$ebayGoods['ebay_packingmaterial'] = empty($pmId)?'':PackingMaterialsModel::getPmNameById($pmId);
				$ebayGoods['add_time'] = time();
				$ebayGoods['spu'] = $spu;
				$ebayGoods['goods_code'] = $insertId +1000000;

                $ebayGoods['color'] = $goodsColor;
                $ebayGoods['size'] = $goodsSize;

                if($goodsStatus == 1){//在线
                    $ebayGoods['isuse'] = 0;
                }elseif($goodsStatus == 51){//PK产品
                    $ebayGoods['isuse'] = 51;
                }else{//其余的都做下线处理
                    $ebayGoods['isuse'] = 1;
                }
                $ebayGoodsArr[] = $ebayGoods;

			}
			BaseModel :: commit();
			BaseModel :: autoCommit();
            //同步数据到深圳ERP
            foreach($ebayGoodsArr as $value){
                $res = OmAvailableModel::newData2ErpInterfOpen('pc.erp.addGoods',$value,'gw88');
            }

			$status = "添加成功";
            echo '<script language="javascript">
                    alert("'.$status.'");
                    window.parent.location.href = "index.php?mod=goods&act=getGoodsList&searchs=1&status='.$status.'&seachdata='.$spu.'";
                  </script>';
			exit;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			$status = $e->getMessage();
            echo '<script language="javascript">
                    alert("'.'添加失败——'.$status.'");
                    window.parent.location.href = "index.php?mod=goods&act=getGoodsList&searchs=1&status='.$status.'&seachdata='.$spu.'";
                  </script>';
			exit;
		}

	}

    //审核不通过spu档案列表
    public function view_getNoPassSpuList() {
		$omAvailableAct = new OmAvailableAct();

		$spu = isset ($_GET['spu']) ? post_check($_GET['spu']) : '';
		$purchaseId = isset ($_GET['purchaseId']) ? post_check($_GET['purchaseId']) : '';
		$pid = isset ($_GET['pid']) ? post_check($_GET['pid']) : '';
        $isCounterAudit = isset ($_GET['isCounterAudit']) ? post_check($_GET['isCounterAudit']) : '';

		$tName = 'pc_spu_archive_no_pass_record';
		$select = '*';
		$where = 'WHERE 1=1 ';
		if (!empty ($spu)) {
			$where .= "AND spu='$spu' ";
		}
		if (!empty ($pid)) {
			$where .= "AND categoryPath REGEXP '^$pid(-[0-9]+)*$' ";
		}
		if (intval($purchaseId) != 0) {
			$where .= "AND purchaseId='$purchaseId' ";
		}
        if($isCounterAudit == 1 || $isCounterAudit == 2){
            $where .= "AND isCounterAudit='$isCounterAudit' ";
        }

		$total = $omAvailableAct->act_getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
        $where .= 'order by id desc ';
		$where .= $page->limit;
		$spuNoPassList = $omAvailableAct->act_getTNameList($tName, $select, $where);
        if(!empty($spuNoPassList)){
            $platformList = getAllPlatformInfo();
            $platformArr = array();
            $spuStatusArr = displayAllSpuStatus();
            foreach($platformList as $value){
                $platformArr[$value['id']] = $value['platform'];
            }
            $countSpuNoPass = count($spuNoPassList);
            for($i=0;$i<$countSpuNoPass;$i++){
                $spuNoPassList[$i]['platformName'] = $platformArr[$spuNoPassList[$i]['platformId']];
                $spuNoPassList[$i]['spuStatusName'] = $spuStatusArr[$spuNoPassList[$i]['spuStatus']]['statusName'];
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
			$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			),
			array (
				'url' => 'index.php?mod=autoCreateSpu&act=getNoPassSpuList',
				'title' => 'SPU审核不通过管理'
			),

		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 25);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', 'SPU审核不通过管理');
        //取得搜索类别的记录
        $pidArr = explode('-',$pid);
        $this->smarty->assign('pidArr', $pidArr);
		$this->smarty->assign('spuNoPassList', empty ($spuNoPassList) ? null : $spuNoPassList);
		$this->smarty->display("spuNoPassList.htm");
	}

    //更新SPU相关人
    public function view_updateSpuPerson() {
		$spu = $_GET['spu'] ? post_check(trim($_GET['spu'])) : '';

		$tName = 'pc_auto_create_spu';
		$select = 'isSingSpu,purchaseId';
		$where = "WHERE spu='$spu' and is_delete=0";
		$autoCreateSpuList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($autoCreateSpuList)) {
			$status = "SPU列表中不存在 $spu !";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
		}
        if(!getIsAccess($autoCreateSpuList[0]['purchaseId']) && !isSpuExistBySpuAndPurchaseId($spu, $_SESSION['userId']) && !isAccessAll('autoCreateSpu','isAccessIntoUpdatePersonPower')){
            $status = "无权限!";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
        }
        if($autoCreateSpuList[0]['isSingSpu'] == 1){//单料号
            $tName = 'pc_spu_saler_single';
        }elseif($autoCreateSpuList[0]['isSingSpu'] == 2){
            $tName = 'pc_spu_saler_single';
        }else{
            $status = "基础数据 单/虚拟料号有误!";
			header("Location:index.php?mod=autoCreateSpu&act=getAutoCreateSpuList&status=$status");
			exit;
        }
		$select = '*';
		$where = "WHERE is_delete=0 AND spu='$spu'";
		$spuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
        foreach($spuSalerList as $value){
            if($value['platformId'] == 1){
                $ebaySalerId = $value['salerId'];
                $ebayIsAgree = $value['isAgree'];
            }elseif($value['platformId'] == 2){
                $aliexpressSalerId = $value['salerId'];
                $aliexpressIsAgree = $value['isAgree'];
            }elseif($value['platformId'] == 11){
                $amazonSalerId = $value['salerId'];
                $amazonIsAgree = $value['isAgree'];
            }elseif($value['platformId'] == 14){
                $overseaSalerId = $value['salerId'];
                $overseaIsAgree = $value['isAgree'];
            }
        }
        $tName = 'pc_spu_web_maker';
        $select = 'webMakerId,isAgree,isTake';
        $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
        $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);

		$navlist = array (//面包屑
	            array (
				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
				'title' => 'SPU管理'
			    ),
			    array (
				'url' => "index.php?mod=autoCreateSpu&act=updateSpuPerson&spu=$spu",
				'title' => "更新SPU相关人_$spu"
			    )
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 22);
		$this->smarty->assign('title', '更新SPU相关人');
		$this->smarty->assign('spu', $spu);
        $this->smarty->assign('isSingSpu', $autoCreateSpuList[0]['isSingSpu']);
		$this->smarty->assign('purchaseId', $autoCreateSpuList[0]['purchaseId']);
		$this->smarty->assign('ebaySalerId', $ebaySalerId);
		$this->smarty->assign('ebayIsAgree', $ebayIsAgree);
        $this->smarty->assign('aliexpressSalerId', $aliexpressSalerId);
		$this->smarty->assign('aliexpressIsAgree', $aliexpressIsAgree);
        $this->smarty->assign('amazonSalerId', $amazonSalerId);
		$this->smarty->assign('amazonIsAgree', $amazonIsAgree);
        $this->smarty->assign('overseaSalerId', $overseaSalerId);
		$this->smarty->assign('overseaIsAgree', $overseaIsAgree);
        $this->smarty->assign('webMakerId', $spuWebMakerList[0]['webMakerId']);
        $this->smarty->assign('webMakerIsAgree', $spuWebMakerList[0]['isAgree']);//add 0513 是否同意
        $this->smarty->assign('webMakerIsTake', $spuWebMakerList[0]['isTake']);

		$this->smarty->display("updateSpuPerson.htm");
	}

    public function view_updateSpuPersonOn() {
		$spu = $_POST['spu'] ? post_check(trim($_POST['spu'])) : '';
        $isSingSpu = $_POST['isSingSpu'] ? post_check(trim($_POST['isSingSpu'])) : 0;
		$ebaySalerId = $_POST['ebaySalerId'] ? post_check(trim($_POST['ebaySalerId'])) : 0;
		$aliexpressSalerId = $_POST['aliexpressSalerId'] ? post_check(trim($_POST['aliexpressSalerId'])) : 0;
        $amazonSalerId = $_POST['amazonSalerId'] ? post_check(trim($_POST['amazonSalerId'])) : 0;
        $overseaSalerId = $_POST['overseaSalerId'] ? post_check(trim($_POST['overseaSalerId'])) : 0;
        $webMakerId = $_POST['webMakerId'] ? post_check(trim($_POST['webMakerId'])) : 0;

        if($isSingSpu != 1 && $isSingSpu != 2){
            $status = "基础数据 单/虚拟料号出错！";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
        }
        if(intval($_SESSION['userId']) <= 0){
            $status = "登陆超时，请重新登陆";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
        }
        if(intval($ebaySalerId) <=0 && intval($aliexpressSalerId) <=0 && intval($amazonSalerId) <=0 && !isAccessAll('autoCreateSpu','isCanUpdateWebMakerPower')){
            $status = "ebay/aliexpress/amazon 平台中至少要存在一个销售人员记录才能提交";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
        }
		try {
		    BaseModel :: begin();
            $status = '';
		    if($isSingSpu == 1){
		      $tName = 'pc_spu_saler_single';
		    }else{
		      $tName = 'pc_spu_saler_combine';
		    }
            $select = 'isAgree,salerId';
            if(!empty($ebaySalerId)){
                $where = "WHERE is_delete=0 AND spu='$spu' AND platformId=1";//ebay平台
                $ebaySpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($ebaySpuSalerList)){//更新时如果人员改动会更新是否接手状态，但是不会更新是否被销售同意/拒绝状态
                    if($ebaySpuSalerList[0]['salerId'] != $ebaySalerId){
                        $dataEbayArr = array();
                        $dataEbayArr['salerId'] = $ebaySalerId;
                        $dataEbayArr['isAgree'] = 1;//add by zqt 20140421,如果换人了的话，则变为待定状态
                        $dataEbayArr['addTime'] = time();
                        OmAvailableModel::updateTNameRow2arr($tName, $dataEbayArr, $where);
                    }
                }else{//插入新数据时，则默认销售同意/拒绝状态为待定
                    $dataEbayArr = array();
                    $dataEbayArr['spu'] = $spu;
                    $dataEbayArr['salerId'] = $ebaySalerId;
                    $dataEbayArr['platformId'] = 1;
                    $dataEbayArr['addTime'] = time();
                    $dataEbayArr['isAgree'] = 1;
                    OmAvailableModel::addTNameRow2arr($tName, $dataEbayArr);
                }
            }

            if(!empty($aliexpressSalerId)){
                $where = "WHERE is_delete=0 AND spu='$spu' AND platformId=2";//ali平台
                $ebaySpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($ebaySpuSalerList)){//更新时如果人员改动会更新是否接手状态，但是不会更新是否被销售同意/拒绝状态
                    if($ebaySpuSalerList[0]['salerId'] != $aliexpressSalerId){
                        $dataEbayArr = array();
                        $dataEbayArr['salerId'] = $aliexpressSalerId;
                        $dataEbayArr['isAgree'] = 1;//add by zqt 20140421,如果换人了的话，则变为待定状态
                        $dataEbayArr['addTime'] = time();
                        OmAvailableModel::updateTNameRow2arr($tName, $dataEbayArr, $where);
                    }
                }else{//插入新数据时，则默认销售同意/拒绝状态为待定
                    $dataEbayArr = array();
                    $dataEbayArr['spu'] = $spu;
                    $dataEbayArr['salerId'] = $aliexpressSalerId;
                    $dataEbayArr['platformId'] = 2;
                    $dataEbayArr['addTime'] = time();
                    $dataEbayArr['isAgree'] = 1;
                    OmAvailableModel::addTNameRow2arr($tName, $dataEbayArr);
                }
            }
            if(!empty($amazonSalerId)){
                $where = "WHERE is_delete=0 AND spu='$spu' AND platformId=11";//amazon平台
                $ebaySpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($ebaySpuSalerList)){//更新时如果人员改动会更新是否接手状态，但是不会更新是否被销售同意/拒绝状态
                    if($ebaySpuSalerList[0]['salerId'] != $amazonSalerId){
                        $dataEbayArr = array();
                        $dataEbayArr['salerId'] = $amazonSalerId;
                        $dataEbayArr['isAgree'] = 1;//add by zqt 20140421,如果换人了的话，则变为待定状态
                        $dataEbayArr['addTime'] = time();
                        OmAvailableModel::updateTNameRow2arr($tName, $dataEbayArr, $where);
                    }
                }else{//插入新数据时，则默认销售同意/拒绝状态为待定
                    $dataEbayArr = array();
                    $dataEbayArr['spu'] = $spu;
                    $dataEbayArr['salerId'] = $amazonSalerId;
                    $dataEbayArr['platformId'] = 11;
                    $dataEbayArr['addTime'] = time();
                    $dataEbayArr['isAgree'] = 1;
                    OmAvailableModel::addTNameRow2arr($tName, $dataEbayArr);
                }
            }
            if(!empty($overseaSalerId)){
                $where = "WHERE is_delete=0 AND spu='$spu' AND platformId=14";//amazon平台
                $ebaySpuSalerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(!empty($ebaySpuSalerList)){//更新时如果人员改动会更新是否接手状态，但是不会更新是否被销售同意/拒绝状态
                    if($ebaySpuSalerList[0]['salerId'] != $overseaSalerId){
                        $dataEbayArr = array();
                        $dataEbayArr['salerId'] = $overseaSalerId;
                        $dataEbayArr['isAgree'] = 1;//add by zqt 20140421,如果换人了的话，则变为待定状态
                        $dataEbayArr['addTime'] = time();
                        OmAvailableModel::updateTNameRow2arr($tName, $dataEbayArr, $where);
                    }
                }else{//插入新数据时，则默认销售同意/拒绝状态为待定
                    $dataEbayArr = array();
                    $dataEbayArr['spu'] = $spu;
                    $dataEbayArr['salerId'] = $overseaSalerId;
                    $dataEbayArr['platformId'] = 14;
                    $dataEbayArr['addTime'] = time();
                    $dataEbayArr['isAgree'] = 1;
                    OmAvailableModel::addTNameRow2arr($tName, $dataEbayArr);
                }
            }
            if(!empty($webMakerId)){
                $tName = 'pc_spu_web_maker';
                $select = 'webMakerId,isAgree,isTake';
                $where = "WHERE is_delete=0 AND spu='$spu' order by id desc limit 1";
                $spuWebMakerList = OmAvailableModel::getTNameList($tName, $select, $where);
                if(empty($spuWebMakerList)){//不存在该SPU的指派记录
                    $dataWebMakerArr = array();
                    $dataWebMakerArr['spu'] = $spu;
                    $dataWebMakerArr['isSingSpu'] = $isSingSpu;
                    $dataWebMakerArr['webMakerId'] = $webMakerId;
                    $dataWebMakerArr['isAgree'] = 1;//add by 20140513,待定状态
                    $dataWebMakerArr['addTime'] = time();
                    OmAvailableModel::addTNameRow2arr($tName, $dataWebMakerArr);
                }else{//存在指派记录时
                    if($spuWebMakerList[0]['webMakerId'] != $webMakerId){//提交的人和之前的不一致则修改/添加，否则不变
                        if($spuWebMakerList[0]['isTake'] == 1){//如果是已经被工程师领取，则表示接手
                            $dataWebMakerArr = array();
                            $dataWebMakerArr['spu'] = $spu;
                            $dataWebMakerArr['isSingSpu'] = $isSingSpu;
                            $dataWebMakerArr['webMakerId'] = $webMakerId;
                            $dataWebMakerArr['addTime'] = time();
                            $dataWebMakerArr['isAgree'] = $spuWebMakerList[0]['isAgree'];//add by 20140513,同意/拒绝状态不变
                            $dataWebMakerArr['isHandsOn'] = 1;
                            OmAvailableModel::addTNameRow2arr($tName, $dataWebMakerArr);
                        }else{//如果未被领取，则只是修改记录
                            $dataWebMakerArr = array();
                            $dataWebMakerArr['webMakerId'] = $webMakerId;
                            $dataWebMakerArr['addTime'] = time();
                            $dataWebMakerArr['isAgree'] = 1;//add by 20140513,未领取的话，重新变成待定状态
                            OmAvailableModel::updateTNameRow2arr($tName, $dataWebMakerArr, $where);
                        }
                    }
                }

            }
            BaseModel::commit();
            BaseModel::autoCommit();
			$status = "更新成功";
            echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		} catch (Exception $e) {
			BaseModel :: rollback();
			BaseModel :: autoCommit();
			$status = $e->getMessage();
            echo '<script language="javascript">
                    alert("'.'添加失败——'.$status.'");
                  </script>';
			exit;
		}
	}

    //修改流程列表
    public function view_getSpuModityRecordList() {
		$spu = isset ($_GET['spu']) ? post_check($_GET['spu']) : '';
        $recordType = isset ($_GET['recordType']) ? post_check($_GET['recordType']) : '';
		$PEId = isset ($_GET['PEId']) ? post_check($_GET['PEId']) : '';
		$status = isset ($_GET['status']) ? post_check($_GET['status']) : '';
        $addUserId = isset ($_GET['addUserId']) ? post_check($_GET['addUserId']) : '';
        $timeSearchType = isset ($_GET['timeSearchType']) ? post_check($_GET['timeSearchType']) : '';
        $startdate = isset ($_GET['startdate']) ? post_check($_GET['startdate']) : '';
        $enddate = isset ($_GET['enddate']) ? post_check($_GET['enddate']) : '';

		$tName = 'pc_spu_modify_record';
		$select = '*';
		$where = 'WHERE is_delete=0 ';
		if (!empty ($spu)) {
			$where .= "AND spu='$spu' ";
		}
        if (intval($recordType) > 0) {
			$where .= "AND recordType='$recordType' ";
		}
		if (intval($PEId) > 0) {
			$where .= "AND PEId='$PEId' ";
		}
		if (intval($status) > 0) {
			$where .= "AND status='$status' ";
		}
        if (intval($addUserId) > 0) {
            if(intval($addUserId) == 9999){
                $where .= "AND addUserId='0' ";
            }else{
                $where .= "AND addUserId='$addUserId' ";
            }
		}
        if (intval($timeSearchType) > 0) {
			if ($startdate != '') {
            	$start = strtotime($startdate . ' 00:00:00');
                if($timeSearchType == 1){
                    $where .= "AND addTime>='$start' ";
                }elseif($timeSearchType == 2){
                    $where .= "AND handleTime>='$start' ";
                }elseif($timeSearchType == 3){
                    $where .= "AND completeTime>='$start' ";
                }
            }
            if ($enddate != '') {
            	$end = strtotime($enddate . ' 23:59:59');
            	if($timeSearchType == 1){
                    $where .= "AND addTime<='$end' ";
                }elseif($timeSearchType == 2){
                    $where .= "AND handleTime<='$end' ";
                }elseif($timeSearchType == 3){
                    $where .= "AND completeTime<='$end' ";
                }
            }
		}

		$total = OmAvailableModel::getTNameCount($tName, $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
        $where .= 'order by id desc ';
		$where .= $page->limit;
		$spuModityRecordList = OmAvailableModel::getTNameList($tName, $select, $where);

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
        				'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
        				'title' => 'SPU管理'
    			       ),
        			   array (
        				'url' => 'index.php?mod=autoCreateSpu&act=getSpuModityRecordList',
        				'title' => 'SPU产品修改/优化管理'
    			       )
                   );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
		$this->smarty->assign('twovar', 26);
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('title', 'SPU产品修改/优化管理');
		$this->smarty->assign('spuModityRecordList', empty ($spuModityRecordList) ? array() : $spuModityRecordList);

        $this->smarty->display("spuModifyRecordList.htm");
	}

    public function view_getSpuModifyDetail() {
		$id = $_GET['id'] ? post_check(trim($_GET['id'])) : '';
		$tName = 'pc_spu_modify_record';
		$select = '*';
		$where = "WHERE is_delete=0 and id='$id'";
		$spuModifyRecordList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuModifyRecordList)) {
			$status = "记录不存在";
			echo '<script language="javascript">
                    alert("'.$status.'");
                  </script>';
			exit;
		}
		$navlist = array (//面包屑
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
			'title' => 'SPU管理'
	       ),
		   array (
			'url' => 'index.php?mod=autoCreateSpu&act=getSpuModityRecordList',
			'title' => 'SPU产品修改/优化管理'
	       ),
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=getSpuModifyDetail'."&id=$id",
			'title' => 'SPU修改记录详情'
	       )
        );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 26);
        $this->smarty->assign('title', 'SPU修改记录详情');
        $this->smarty->assign('spuModifyRecord', $spuModifyRecordList[0]);
        $this->smarty->display("spuModifyDetail.htm");
	}

    public function view_addSpuModityRecord(){
        $navlist = array (//面包屑
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
			'title' => 'SPU管理'
	       ),
		   array (
			'url' => 'index.php?mod=autoCreateSpu&act=getSpuModityRecordList',
			'title' => 'SPU产品修改/优化管理'
	       ),
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=addSpuModityRecord',
			'title' => '添加SPU修改记录'
	       )
        );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 26);
        $this->smarty->assign('title', '添加SPU修改记录');
        $this->smarty->display("addSpuModityRecord.htm");
	}

    public function view_appendSpuModityRecord() {
		$id = $_GET['id'] ? post_check(trim($_GET['id'])) : '';
		$tName = 'pc_spu_modify_record';
		$select = '*';
		$where = "WHERE is_delete=0 and id='$id'";
		$spuModifyRecordList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuModifyRecordList)) {
			$status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
        if(!getIsAccess($spuModifyRecordList[0]['addUserId'])){
            $status = "无权限！";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
        if(!empty($spuModifyRecordList[0]['appendContent1'])){
            $status = "已经修订过！";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
        }
		$navlist = array (//面包屑
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
			'title' => 'SPU管理'
	       ),
		   array (
			'url' => 'index.php?mod=autoCreateSpu&act=getSpuModityRecordList',
			'title' => 'SPU产品修改/优化管理'
	       ),
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=appendSpuModityRecord'."&id=$id",
			'title' => '修订SPU修改记录'
	       )
        );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 26);
        $this->smarty->assign('title', '修订SPU修改记录');
        $this->smarty->assign('spuModifyRecord', $spuModifyRecordList[0]);
        $this->smarty->display("appendSpuModityRecord.htm");
	}

    public function view_updateSpuModifyRecordPEId() {
		$id = $_GET['id'] ? post_check(trim($_GET['id'])) : '';
		$tName = 'pc_spu_modify_record';
		$select = '*';
		$where = "WHERE is_delete=0 and id='$id'";
		$spuModifyRecordList = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (empty ($spuModifyRecordList)) {
			$status = "记录不存在";
			echo '<script language="javascript">
                        alert("'.$status.'");
                        </script>';
					exit;
		}
		$navlist = array (//面包屑
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=getAutoCreatePrefixList',
			'title' => 'SPU管理'
	       ),
		   array (
			'url' => 'index.php?mod=autoCreateSpu&act=getSpuModityRecordList',
			'title' => 'SPU产品修改/优化管理'
	       ),
           array (
			'url' => 'index.php?mod=autoCreateSpu&act=updateSpuModifyRecordPEId'."&id=$id",
			'title' => '修改指派产品工程师'
	       )
        );
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('onevar', 2);
        $this->smarty->assign('twovar', 26);
        $this->smarty->assign('title', '修订SPU修改记录');
        $this->smarty->assign('spuModifyRecord', $spuModifyRecordList[0]);
        $this->smarty->display("updateSpuModifyRecordPEId.htm");
	}



}
?>