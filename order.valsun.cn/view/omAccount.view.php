<?php
/**
 * OmAccountView
 *
 * @package order.valsun.cn
 * @author zqt
 * @copyright 2013
 * @version 1.0
 * @access public
 */
class OmAccountView extends BaseView {

	//展示收货管理表
	public function view_getAccountList() {
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
            $status = '<font color="red">数据异常，添加失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and account='$account' ";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '<font color="red">账号名称已存在，添加失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET account='$account',platformId='$platformId',appname='$appname',email='$email',suffix='$suffix',charger='$charger',addUser='$addUser',addTime='$addTime'";
        $affectRow = $omAvailableAct->act_addTNameRow($tName, $set);
        if($affectRow){
            $status = '<font color="green">添加成功</font>';
        }else{
            $status = '<font color="red">添加失败</font>';
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
        if(intval($id) == 0 || empty($account) || intval($platformId) == 0 || empty($appname) /*|| empty($email) || empty($suffix)*/){
            $status = '<font color="red">数据异常，修改失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and account='$account' AND id<>'$id' ";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if($count){
            $status = '<font color="red">账号名称已存在，修改失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $addUser = $_SESSION['sysUserId'];
        $addTime = time();
        $set = "SET account='$account',platformId='$platformId',appname='$appname',email='$email',suffix='$suffix',charger='$charger' ";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow !== false){
            $status = '<font color="green">修改成功</font>';
        }else{
            $status = '<font color="red">修改失败</font>';
        }
        header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
	}

    public function view_deleteAccountList(){
        $id = isset($_GET['id'])?post_check($_GET['id']):'';
        $status = '';
        if(intval($id) == 0){
            $status = '<font color="red">数据异常，删除失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_account';
        $where = "WHERE is_delete=0 and id='$id'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if(!$count){
            $status = '<font color="red">记录不存在，删除失败</font>';
            header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
        }
        $set = "SET is_delete=1 ";
        $where = "WHERE id='$id'";
        $affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
        if($affectRow){
            $status = '<font color="green">删除成功</font>';
        }else{
            $status = '<font color="red">删除失败</font>';
        }
        header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
	}

    //修改页面
	public function view_scanUpdateAccountList(){
        $omAvailableAct = new OmAvailableAct();
		$id = isset ($_GET['id']) ? post_check($_GET['id']) : '';
		if (intval($id) == 0) { //id为空时，跳转到列表页面，输出错误信息
			$status = '<font color="red">数据异常</font>';
			header("location:index.php?mod=omAccount&act=getAccountList&status=$status");
			exit;
		}
		$where = "WHERE id=$id ";
		$omAccountList = $omAvailableAct->act_getTNameList('om_account', '*', $where);

		if (empty ($omAccountList)) {
			$status = '<font color="red">找不到要修改记录的id</font>';
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
	
	//添加页面
	public function view_showUserCompense(){
		$OmAccountAct = new OmAccountAct();
		$StatusMenuAct = new StatusMenuAct();
		$uid = $_GET['uid'];
		//var_dump($_POST); exit;
		$showinfo = '';
		if(isset($_POST['action']) && !empty($_POST['action'])){
			if($OmAccountAct->act_addUserCompense($_POST)){
				$showinfo = "<font color='green'>添加权限成功！</font>";
			}else{
				$showinfo = "<font color='red'>添加权限失败!</font>";
			}
			$uid = $_POST['uid'];
		}
		if(empty($uid)){
			header("location:index.php?mod=user&act=index"); exit;
		}
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('showinfo', $showinfo);
		$UserCompenseList = $OmAccountAct->act_getUserCompenseList($uid);
		//echo "<pre>";
		//print_r($UserCompenseList); exit;
		//$visible_platform = array();
		//$visible_account  = array();
		//echo "<pre>";
		//var_dump($UserCompenseList);
		//$visible_platform = $UserCompenseList['visible_platform'];
		//$visible_account  = $UserCompenseList['visible_account'];
		$visible_movefolder = json_decode($UserCompenseList['visible_movefolder'],true);
		//$visible_showfolder = $UserCompenseList['visible_showfolder'];
		//$visible_editorder  = $UserCompenseList['visible_editorder'];
		$visible_platform_account = json_decode($UserCompenseList['visible_platform_account'], true);
		//var_dump($visible_platform_account); exit;
		/*if(empty($visible_movefolder)){
			echo "=============";
		}*/
		$key_visible_movefolder = array();
		if(is_array($visible_movefolder)){
			$key_visible_movefolder = array_keys($visible_movefolder);
		}
		$StatusMenu = $StatusMenuAct->act_getStatusMenuListById('*', ' WHERE groupId != 0 ');
		//var_dump($StatusMenu);
		$statusGroupLists = $StatusMenuAct->act_getMenuGroupList();
		//echo "<pre>"; print_r($statusGroupLists); exit;
		//$all_platform = $UserCompenseList['all_platform'];
		//var_dump($all_platform);
		//$all_account = $UserCompenseList['all_account'];
		//$all_account = $UserCompenseList['all_account'];
		$arr_all_platform_account = $UserCompenseList['arr_all_platform_account'];
		$editorder_options = array(1 => '平台','账号','买家ID','订单号','下单时间','付款时间','产品总金额','物流费用','订单金额','Transaction ID','币种','估算重量','买家选择发货物流','跟踪号','Full name','Street1','Street2','City','State','Country	','Postcode','Tel1','Tel2','Tel3','买家邮箱1','买家邮箱2','买家邮箱3','订单备注');
		$this->smarty->assign('editorder_options', $editorder_options);

		//$this->smarty->assign('visible_platform', $visible_platform);
		//$this->smarty->assign('visible_account', $visible_account);
		$this->smarty->assign('visible_platform_account', $visible_platform_account);
		$this->smarty->assign('arr_all_platform_account', $arr_all_platform_account);
		$this->smarty->assign('key_visible_movefolder', $key_visible_movefolder);
		$this->smarty->assign('visible_showfolder', $visible_showfolder);
		$this->smarty->assign('visible_editorder', $visible_editorder);
		
		//$this->smarty->assign('all_platform', $all_platform);
		//$this->smarty->assign('all_account', $all_account);
		
		$this->smarty->assign('StatusMenu', $StatusMenu);
		$this->smarty->assign('statusGroupLists', $statusGroupLists);
		
		$this->smarty->assign('toptitle', '订单系统细颗粒度权限控制');
        $this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '32');
		$this->smarty->display("showUserCompense.htm");
	}
	
}