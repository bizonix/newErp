<?php

/*
 * 平台View
 */
/**
 * OmPlatformView
 *
 * @package order.valsun.cn
 * @author zqt
 * @copyright 2013
 * @version 1.0
 * @access public
 */
class OmPlatformView extends BaseView {

	//展示收货管理表
	public function view_getOmPlatformList() {
	    $status = isset($_GET['status'])?$_GET['status']:'';
		$omAvailableAct = new OmAvailableAct();
		$where = 'WHERE is_delete=0 ';

		$total = $omAvailableAct->act_getTNameCount('om_platform', $where);
		$num = 10; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;
		$omPlatformList = $omAvailableAct->act_getTNameList('om_platform', '*', $where);
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
				'url' => 'index.php?mod=omPlatform&act=getOmPlatformList',
				'title' => '系统设置'
			),
			array (
				'url' => '',
				'title' => '平台管理'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台管理');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '31');
		$this->smarty->assign('show_page', $show_page);
        $this->smarty->assign('status', $status);
		$this->smarty->assign('omPlatformList', $omPlatformList); //循环列表
		$this->smarty->display("omPlatformList.htm");
	}

	public function view_addPlatformList(){
	    $platform = isset($_POST['platform'])?post_check($_POST['platform']):'';
        $status = '';
        if(empty($platform)){
            $status = '平台名称为空，添加失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_platform';
        $where = "WHERE is_delete=0 and platform='$platform'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '平台名称已存在，添加失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET platform='$platform',addUser='$addUser',addTime='$addTime'";
        $affectRow = $omAvailableAct->act_addTNameRow($tName, $set);
        if($affectRow){
        	//更新老系统
        	$returnInfo = OldsystemModel::erpSyncPlatform($platform,'insert');
        	if($returnInfo['res_code'] == 200){
        		$status = '添加成功';
        	}else{
        		$status = '更新老系统失败';
        	}
        }else{
            $status = '添加失败';
        }
        header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
	}

    public function view_updatePlatformList(){
        $id = isset($_GET['id'])?post_check($_GET['id']):'';
	    $platform = isset($_GET['platform'])?post_check($_GET['platform']):'';
        $status = '';
        if(intval($id) == 0){
            $status = '数据异常，修改失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        if(empty($platform)){
            $status = '平台名称为空，修改失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_platform';
        $where = "WHERE is_delete=0 and platform='$platform'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '平台名称已存在，修改失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        //获得修改前平台名称
        $platformInfo = $omAvailableAct->act_getTNameList($tName, 'platform', ' where id='.$id);
        $old_platform = $platformInfo[0]['platform'];       
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET platform='$platform'";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow !== false){
        	//更新老系统
        	$returnInfo = OldsystemModel::erpSyncPlatform($platform,'update',$old_platform);
        	if($returnInfo['res_code'] == 200){
        		$status = '修改成功';
        	}else{
        		$status = '更新老系统失败';
        	}
        }else{
            $status = '修改失败';
        }
        header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
	}

    public function view_deletePlatformList(){
        $id = isset($_GET['id'])?post_check($_GET['id']):'';
        $status = '';
        if(intval($id) == 0){
            $status = '数据异常，删除失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_platform';
        $where = "WHERE is_delete=0 and id='$id'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if(!$count){
            $status = '记录不存在，删除失败';
            header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
			exit;
        }
        //获取原始平台账号
        $platformInfo = $omAvailableAct->act_getTNameList($tName, 'platform', ' where id='.$id);
        $old_platform = $platformInfo[0]['platform'];
        $set = "SET is_delete=1 ";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow){
        	//更新老系统
        	$returnInfo = OldsystemModel::erpSyncPlatform($old_platform,'delete',$old_platform);
        	if($returnInfo['res_code'] == 200){
        		$status = '删除成功';
        	}else{
        		$status = '更新老系统失败';
        	}
        }else{
            $status = '删除失败';
        }
        header("location:index.php?mod=omPlatform&act=getOmPlatformList&status=$status");
	}
}