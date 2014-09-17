<?php

/*
 * 账号View
 */
/**
 * OmAccountView
 *
 * @package order.valsun.cn
 * @author zqt
 * @copyright 2013
 * @version 1.0
 * @access public
 */
class PermissionsView extends BaseView {

	//展示平台统一用户列表信息
	public function view_getUserList() {
	    $type = isset($_GET['type'])?$_GET['type']:'';
	    $status = isset($_GET['status'])?$_GET['status']:'';
		$omAvailableAct = new OmAvailableAct();
		$where = 'WHERE is_delete=0 ';
        if($type == 'search'){
            $accountId = isset($_GET['accountId'])?$_GET['accountId']:'';
            $platformId = isset($_GET['platformId'])?$_GET['platformId']:'';
            if(intval($accountId) != 0){
                $where .= "AND id='$accountId' ";
            }
            if(intval($platformId) != 0){
                $where .= "AND platformId='$platformId' ";
            }
        }
		$omAccountList = $omAvailableAct->act_getTNameList('om_account', '*', $where);
		$total = $omAvailableAct->act_getTNameCount('om_account', $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;

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
				'title' => '平台账号'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '账号管理');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '32');
		$this->smarty->assign('show_page', $show_page);
        $this->smarty->assign('status', $status);
		$this->smarty->assign('omAccountList', $omAccountList); //循环列表
		$this->smarty->display("omAccountList.htm");
	}

	public function view_addAccountList(){
	    $account = isset($_POST['account'])?post_check($_POST['account']):'';
        $platformId = isset($_POST['platformId'])?post_check($_POST['platformId']):'';
        $appname = isset($_POST['appname'])?post_check($_POST['appname']):'';
        $email = isset($_POST['email'])?post_check($_POST['email']):'';
        $suffix = isset($_POST['suffix'])?post_check($_POST['suffix']):'';
        $charger = isset($_POST['charger'])?post_check($_POST['charger']):'';
        $status = '';
        if(empty($account) || intval($platformId) == 0 || empty($appname) || empty($email) || empty($suffix)){
            $status = '数据异常，添加失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and account='$account' ";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '账号名称已存在，添加失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET account='$account',platformId='$platformId',appname='$appname',email='$email',suffix='$suffix',charger='$charger',addUser='$addUser',addTime='$addTime'";
        $affectRow = $omAvailableAct->act_addTNameRow($tName, $set);
        if($affectRow){
            $status = '添加成功';
        }else{
            $status = '添加失败';
        }
        header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
	}

    public function view_updateAccountList(){
        $id = isset($_POST['id'])?post_check($_POST['id']):'';
	    $account = isset($_POST['account'])?post_check($_POST['account']):'';
        $platformId = isset($_POST['platformId'])?post_check($_POST['platformId']):'';
        $appname = isset($_POST['appname'])?post_check($_POST['appname']):'';
        $email = isset($_POST['email'])?post_check($_POST['email']):'';
        $suffix = isset($_POST['suffix'])?post_check($_POST['suffix']):'';
        $charger = isset($_POST['charger'])?post_check($_POST['charger']):'';
        $status = '';
        if(intval($id) == 0 || empty($account) || intval($platformId) == 0 || empty($appname) || empty($email) || empty($suffix)){
            $status = '数据异常，修改失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and account='$account' AND id<>'$id' ";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '账号名称已存在，修改失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET account='$account',platformId='$platformId',appname='$appname',email='$email',suffix='$suffix',charger='$charger' ";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow !== false){
            $status = '修改成功';
        }else{
            $status = '修改失败';
        }
        header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
	}

    public function view_deleteAccountList(){
        $id = isset($_GET['id'])?post_check($_GET['id']):'';
        $status = '';
        if(intval($id) == 0){
            $status = '数据异常，删除失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and id='$id'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if(!$count){
            $status = '记录不存在，删除失败';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $set = "SET is_delete=1 ";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow){
            $status = '删除成功';
        }else{
            $status = '删除失败';
        }
        header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
	}

    //修改页面
	public function view_scanUpdateAccountList(){
        $omAvailableAct = new OmAvailableAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		if (intval($id) == 0) { //id为空时，跳转到列表页面，输出错误信息
			$status = '数据异常';
			header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$omAccountList = $omAvailableAct->act_getTNameList('om_account', '*', $where);

		if (empty ($omAccountList)) {
			$status = '找不到要修改记录的id';
			header("location:index.php?mod=omAccount&act=getOmAccountList&status=$status");
			exit;
		} else {
			$value = $omAccountList[0];
		}
		//设置修改页面上指定字段的值
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=omPlatform&act=getOmPlatformList',
				'title' => '系统设置'
			),
			array (
				'url' => 'index.php?mod=omAccount&act=getAccountList',
				'title' => '平台账号'
			),
            array (
				'url' => '',
				'title' => '修改账号'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '修改账号');
        $this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '32');
		$this->smarty->assign("id", $value['id']);
        $this->smarty->assign("account", $value['account']);
        $this->smarty->assign("appname", $value['appname']);
        $this->smarty->assign("platformId", $value['platformId']);
        $this->smarty->assign("email", $value['email']);
        $this->smarty->assign("suffix", $value['suffix']);
        $this->smarty->assign("charger", $value['charger']);
		$this->smarty->display("omUpdateAccountList.htm");
	}

    //添加页面
	public function view_scanAddAccountList(){
		$navlist = array (//面包屑
	array (
				'url' => 'index.php?mod=omPlatform&act=getOmPlatformList',
				'title' => '系统设置'
			),
			array (
				'url' => 'index.php?mod=omAccount&act=getAccountList',
				'title' => '平台账号'
			),
            array (
				'url' => '',
				'title' => '添加账号'
			)
		);
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '添加账号');
        $this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '32');
		$this->smarty->display("omAddAccountList.htm");
	}
}