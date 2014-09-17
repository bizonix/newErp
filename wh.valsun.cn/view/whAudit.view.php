<?php

/*
 * 审核View
 */
class WhAuditView extends CommonView {

    //审核类型列表
	public function view_getWhAuditList() {
        $type = isset ($_GET['type']) ? $_GET['type'] : '';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$whAuditAct = new WhAuditAct();
		$where = "WHERE 1=1 ";
        if ($type == 'search') {
			            $invoiceTypeId = isset ($_GET['invoiceTypeId']) ? post_check($_GET['invoiceTypeId']) : '';
			            $storeId = isset ($_GET['storeId']) ? post_check($_GET['storeId']) : '';
			            if (intval($invoiceTypeId) != 0) {
							$where .= "AND invoiceTypeId='$invoiceTypeId' ";
						}
			            if (intval($storeId) != 0) {
							$where .= "AND storeId='$storeId' ";
						}
		}
		$total = $whAuditAct->act_getTNameCount('wh_audit_relation_list', $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY invoiceTypeId,auditLevel " . $page->limit;
        $whAuditList = $whAuditAct->act_getTNameList('wh_audit_relation_list', '*', $where);
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
				'url' => 'index.php?mod=whIoStore&act=getWhIoStoreList&ioType=1',
				'title' => '单据业务'
			),
			array (
				'url' => '',
				'title' => '审核等级列表'
			)
		);
		
		$usermodel = UserModel::getInstance();
		//审核人
		$count = count($whAuditList);
		for($i=0;$i<$count;$i++){
			$user_info 		  			= $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$whAuditList[$i]['auditorId']}'",'','limit 1');
			$whAuditList[$i]['auditor'] = $user_info[0]['global_user_name'];
		}
		
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '审核列表');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '011');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('whAuditList', $whAuditList); //循环列表
		$this->smarty->display("whAuditList.htm");
	}

    //修改页面
	public function view_scanUpdateAuditList(){
        $whAuditAct = new WhAuditAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		if (empty ($id)) { //id为空时，跳转到列表页面，输出错误信息
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=whAudit&act=getWhAuditList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$whAuditList = $whAuditAct->act_getTNameList('wh_audit_relation_list', '*', $where);

		if (empty ($whAuditList)) {
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=whAudit&act=getWhAuditList&status=$status");
			exit;
		} else {
			$value = $whAuditList[0];
		}
		//设置修改页面上指定字段的值
		$navlist = array (//面包屑
		array (
				'url' => 'index.php?mod=goodsInfo&act=showSearchForm',
				'title' => '仓库'
			),
			array (
				'url' => 'index.php?mod=whAudit&act=getWhAuditList',
				'title' => '审核列表'
			),
            array (
				'url' => '',
				'title' => '修改审核列表记录'
			)
		);
		
		$usermodel = UserModel::getInstance();
		//审核人
		$user_info 		  			= $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$value['auditorId']}'",'','limit 1');
		$auditorName			    = $user_info[0]['global_user_name'];

		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改审核列表记录');
        $this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '011');
		$this->smarty->assign("id", $value['id']);
		$this->smarty->assign("invoiceTypeId", $value['invoiceTypeId']);
		$this->smarty->assign("auditorName", $auditorName);
		$this->smarty->assign("auditorId", $value['auditorId']);
		$this->smarty->assign("auditLevel", $value['auditLevel']);
        $this->smarty->assign("is_enable", $value['is_enable']);
        $this->smarty->assign("storeId", $value['storeId']);
		$this->smarty->assign("invoiceTypeName", WhIoStoreModel::getInvoiceTypeNameById($value['invoiceTypeId']));
		$this->smarty->assign("whName", WhIoStoreModel ::getWhNameById($value['storeId']));
		$this->smarty->display("whUpdateAuditList.htm");
	}

    //修改页面
	public function view_scanAddAuditList(){
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=goodsInfo&act=showSearchForm',
				'title' => '仓库'
			),
			array (
				'url' => 'index.php?mod=whAudit&act=getWhAuditList',
				'title' => '审核列表'
			),
            array (
				'url' => '',
				'title' => '添加审核等级'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '添加审核列表记录');
        $this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '011');
		$this->smarty->display("whAddAuditList.htm");
	}

    //添加当前仓库和单据类型的审核等级页面
	public function view_scanAddAuditListThis(){
	    $whAuditAct = new WhAuditAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		if (empty ($id)) { //id为空时，跳转到列表页面，输出错误信息
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=whAudit&act=getWhAuditList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$whAuditList = $whAuditAct->act_getTNameList('wh_audit_relation_list', '*', $where);

		if (empty ($whAuditList)) {
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=whAudit&act=getWhAuditList&status=$status");
			exit;
		} else {
			$value = $whAuditList[0];
		}
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=goodsInfo&act=showSearchForm',
				'title' => '仓库'
			),
			array (
				'url' => 'index.php?mod=whAudit&act=getWhAuditList',
				'title' => '审核列表'
			),
            array (
				'url' => '',
				'title' => '添加审核等级'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '添加审核等级');
        $this->smarty->assign('toplevel', 3);
        $this->smarty->assign('secondlevel', '011');
		$this->smarty->assign('value', $value);
		$this->smarty->display("whAddAuditListThis.htm");
	}

    //修改数据后，post过来的view
	public function view_updateAuditList(){
        $whAuditAct = new WhAuditAct();
		$id = isset ($_POST['id']) ? post_check($_POST['id']) : '';
        $auditLevel = isset ($_POST['auditLevel']) ? post_check($_POST['auditLevel']) : '';//审核级别
        $auditorId = isset ($_POST['auditorId']) ? post_check($_POST['auditorId']) : '';//审核人ID
        $invoiceTypeId = isset ($_POST['invoiceTypeId']) ? post_check($_POST['invoiceTypeId']) : '';//单据类型不能修改
        $storeId = isset ($_POST['storeId']) ? post_check($_POST['storeId']) : '';//仓库位置不能修改
        $is_enable = isset ($_POST['is_enable']) ? post_check($_POST['is_enable']) : '';//是否启用状态

        if (intval($id) == 0 || intval($is_enable) == 0 || intval($auditorId) == 0 || intval($auditLevel) == 0) { //id为空时，跳转到列表页面，输出错误信息
			$status = '数据异常，修改失败';
			header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$count = $whAuditAct->act_getTNameCount('wh_audit_relation_list', $where);

		if (empty ($count)) {
			$status = '不存在该id记录，修改失败';
			header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
			exit;
		}

        $where = "WHERE invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' AND auditorId='$auditorId' AND auditLevel='$auditLevel' AND is_enable='$is_enable'";
        $count = $whAuditAct->act_getTNameCount('wh_audit_relation_list', $where);
        if ($count) {
			$status = '记录已存在，修改失败';
			header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
			exit;
		}

        $set = "SET auditorId='$auditorId',auditLevel='$auditLevel',is_enable='$is_enable' ";
        $where = "WHERE id=$id ";
        $affectRows = $whAuditAct->act_updateTNameRow('wh_audit_relation_list', $set, $where);
		if($affectRows){
		  $status = '修改成功';
		}else{
		  $status = '无数据被修改';
		}
        header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
	}

    //修改数据后，post过来的view
	public function view_addAuditList(){
		//echo "<pre>";print_r($_POST);exit;
        $whAuditAct    = new WhAuditAct();
        $invoiceTypeId = isset ($_POST['invoiceTypeId']) ? post_check($_POST['invoiceTypeId']) : '';
        $auditorId     = isset ($_POST['auditorId']) ? post_check($_POST['auditorId']) : '';
		$auditorName   = isset ($_POST['auditorName']) ? trim($_POST['auditorName']) : '';
        $auditLevel    = isset ($_POST['auditLevel']) ? post_check($_POST['auditLevel']) : '';
        $storeId       = isset ($_POST['storeId']) ? post_check($_POST['storeId']) : '';
        $is_enable     = isset ($_POST['is_enable']) ? post_check($_POST['is_enable']) : '';

        if (intval($is_enable) == 0 || intval($invoiceTypeId) == 0 || intval($auditorId) == 0 || intval($auditLevel) == 0 || intval($storeId) == 0) { //id为空时，跳转到列表页面，输出错误信息
			$status = '数据异常，添加失败';
			header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
			exit;
		}
        $where = "WHERE invoiceTypeId='$invoiceTypeId' AND storeId='$storeId' AND auditLevel='$auditLevel' AND auditorId='$auditorId'";
        $count = $whAuditAct->act_getTNameCount('wh_audit_relation_list', $where);
        if ($count) {
			$status = '已经存在该记录，添加失败';
			header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
			exit;
		}
        $now = time();
        $set = "SET invoiceTypeId='$invoiceTypeId',auditorId='$auditorId',auditLevel='$auditLevel',storeId='$storeId',createdTime='$now',is_enable='$is_enable' ";
        $affectRows = $whAuditAct->act_addTNameRow('wh_audit_relation_list', $set);
		if($affectRows){
		  $status = '添加成功';
		}else{
		  $status = '添加失败';
		}
        header("location:index.php?mod=WhAudit&act=getWhAuditList&status=$status");
	}

    //审核记录
    public function view_getWhAuditRecords() {
        $type = isset ($_GET['type']) ? $_GET['type'] : '';
		$status = isset ($_GET['status']) ? $_GET['status'] : '';
		$whAuditAct = new WhAuditAct();
		$where = "WHERE 1=1 ";
        if ($type == 'search') {
			            $ordersn = isset ($_GET['ordersn']) ? post_check($_GET['ordersn']) : '';
			            $auditStatus = isset ($_GET['auditStatus']) ? post_check($_GET['auditStatus']) : '';
                        $cStartTime = isset ($_GET['cStartTime']) ? post_check($_GET['cStartTime']) : '';
                        $cEndTime = isset ($_GET['cEndTime']) ? post_check($_GET['cEndTime']) : '';
			            if (!empty ($ordersn)) {
							$where .= "AND ordersn='$ordersn' ";
						}
			            if (intval($auditStatus) != 0) {
							$where .= "AND auditStatus='$auditStatus' ";
						}
                        if (!empty ($cStartTime)) {
                            $startTime = strtotime($cStartTime.'00:00:00');
							$where .= "AND auditTime >='$startTime' ";
						}
                        if (!empty ($cEndTime)) {
							$endTime = strtotime($cEndTime.'23:59:59');
							$where .= "AND auditTime <='$endTime' ";
						}
		}
		$total = $whAuditAct->act_getTNameCount('wh_audit_records', $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id DESC " . $page->limit;
		$whAuditRecords = $whAuditAct->act_getTNameList('wh_audit_records', '*', $where);
        for($i=0;$i<$total;$i++){
            $whAuditList = $whAuditAct->act_getTNameList('wh_audit_relation_list', '*', "WHERE id='{$whAuditRecords[$i]['auditRelationId']}'");
            $whAuditList0 = $whAuditList[0];
            $whAuditRecords[$i]['nowAuditLevel'] = $whAuditList0['auditLevel'];//当前审核等级
            $whAuditRecords[$i]['auditorId'] = $whAuditList0['auditorId'];//当前审核等级的审核人
            //取得当前等级下对应的最大审核等级
            $whAuditList = $whAuditAct->act_getTNameList('wh_audit_relation_list', 'auditLevel', "WHERE invoiceTypeId='{$whAuditList0['invoiceTypeId']}' AND storeId='{$whAuditList0['storeId']}' ORDER BY auditLevel DESC");
            $whAuditList0 = $whAuditList[0];
            $whAuditRecords[$i]['auditLevel'] = $whAuditList0['auditLevel'];//当前审核等级对应的最大审核等级
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
				'url' => 'index.php?mod=whIoStore&act=getWhIoStoreList&ioType=1',
				'title' => '单据业务'
			),
			array (
				'url' => '',
				'title' => '审核记录信息'
			)
		);
		
		$usermodel = UserModel::getInstance();
		//审核人
		$count = count($whAuditRecords);
		for($i=0;$i<$count;$i++){
			$user_info 		  			   = $usermodel->getGlobalUserLists('global_user_name',"where a.global_user_id='{$whAuditRecords[$i]['auditorId']}'",'','limit 1');
			$whAuditRecords[$i]['auditor'] = $user_info[0]['global_user_name'];
		}
		
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '审核记录信息');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '012');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		$this->smarty->assign('whAuditRecords', $whAuditRecords); //循环列表
		$this->smarty->display("whAuditRecords.htm");
	}
}