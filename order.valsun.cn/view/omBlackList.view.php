<?php

/*
 * 黑名单View
 */
/**
 * OmBlackListView
 *
 * @package order.valsun.cn
 * @author zyp
 * @copyright 2013
 * @version 1.0
 * @access public
 */
class OmBlackListView extends BaseView {

	public function view_getOmBlackList() {
	    $status = isset($_GET['status'])?$_GET['status']:'';
		$omAvailableAct = new OmAvailableAct();
		$where = 'WHERE is_delete=0 ';

		$total = $omAvailableAct->act_getTNameCount('om_blacklist', $where);
		$num = 100; //每页显示的个数
		$page = new Page($total, $num, '', 'CN');
		$where .= "ORDER BY id " . $page->limit;		
		
		$omBlackList = $omAvailableAct->act_getTNameList('om_blacklist', '*', $where);
		
		$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
		
		
		$phone				=	isset($_REQUEST['phone'])? $_REQUEST['phone'] : '';
		$platformUsername	=	isset($_REQUEST['platformUsername'])? $_REQUEST['platformUsername'] : '';
		$username			=	isset($_REQUEST['username'])? $_REQUEST['username'] : '';
		$usermail			=	isset($_REQUEST['usermail'])? $_REQUEST['usermail'] : '';
		$street				=	isset($_REQUEST['street'])? $_REQUEST['street'] : '';
		
		
		
		$platformId	=	isset($_REQUEST['platformId'])? $_REQUEST['platformId'] : '1';
		$account	=  $omAvailableAct->act_getTNameList('om_account','id,platformId,account','WHERE is_delete=0 AND platformId = '.$platformId);
		$accountAll	=  $omAvailableAct->act_getTNameList('om_account','id,platformId,account','WHERE is_delete=0');
		
		foreach($omBlackList as $k => $v){
			foreach($platform as $j){
				if($v['platformUsername'] == $j['id']){
					$omBlackList[$k]['platformUsername'] = $j['platform'];
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
		$navlist = array (//面包屑
	   array (
				'url' => 'index.php?mod=omPlatform&act=getOmPlatformList',
				'title' => '系统设置'
			),
			array (
				'url' => '',
				'title' => '平台黑名单'
			)
		);
		
		//添加黑名单更改平台的时候，用来存储已填写的账户名等数据，确保不会丢失
		$this->smarty->assign('platformUsername', $platformUsername);
		$this->smarty->assign('username', $username);
		$this->smarty->assign('usermail', $usermail);
		$this->smarty->assign('street', $street);
		$this->smarty->assign('phone', $phone);
		$this->smarty->assign('platformId', $platformId);
		$this->smarty->assign('platform', $platform);
		
		//传递系统配置等(页数,链接等)参数		
		$this->smarty->assign('navlist', $navlist);
		$this->smarty->assign('toptitle', '平台黑名单');
		$this->smarty->assign('toplevel', 3);
		$this->smarty->assign('secondlevel', '34');
		$this->smarty->assign('show_page', $show_page);
		$this->smarty->assign('status', $status);
		
		//传递查询出来的数据
		$this->smarty->assign('account', $account);
		$this->smarty->assign('accountAll', $accountAll);
		$this->smarty->assign('omBlackList', $omBlackList); //循环列表
		$this->smarty->display("omBlackList.htm");
	}

	public function view_addBlackList(){
		$omAvailableAct = new OmAvailableAct();
		$data	=	array();
		$data['platformUsername'] = isset($_REQUEST['platformUsername']) ? $_REQUEST['platformUsername'] : '';
		$data['username'] = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
		$data['usermail']	= isset($_REQUEST['usermail']) ? $_REQUEST['usermail'] : '';
		$data['street'] = isset($_REQUEST['street']) ? $_REQUEST['street'] : '';
		$data['phone'] = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '';
		$data['platformId']= isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : '';
		$postAccount = isset($_REQUEST['account']) ? $_REQUEST['account'] : '';
		$data['addTime']	=	time();
		$data['addUser']  =	'1';
		if($data['platformUsername'] == ''){
			$status	=	'平台名称不能为空';
			header('location:index.php?mod=omBlackList&act=getOmBlackList&status='.$status);exit;
		}
		$account	=	'';
		foreach($postAccount as $k => $v){
			$data['account'] =	$v;
			$ret	=  OmBlackListAct::act_insertBlackList($data, 'om_blacklist');
			if(!$ret){
				$state	=	'添加黑名单出错';
				header('location:index.php?mod=omBlackList&act=getOmBlackList&status='.$state);exit;
			}
		}
		$state	=	'添加成功';
		header('location:index.php?mod=omBlackList&act=getOmBlackList&status='.$state);exit;
	}

    public function view_updateBlackList(){
		$omAvailableAct = new OmAvailableAct();
		if(!isset($_REQUEST['platformUsername'])){			//读取要修改的数据并显示到黑名单修改页面
			$navlist = array (
							array (
								'url' => 'index.php?mod=omPlatform&act=getOmPlatformList',
								'title' => '系统设置'
							),
							array (
								'url' => 'index.php?mod=OmBlackList&act=getOmBlackList',
								'title' => '平台黑名单'
							),
							array (
								'url' => '',
								'title' => '修改黑名单'
							),
						);
			$platform	=  $omAvailableAct->act_getTNameList('om_platform','id,platform','WHERE is_delete=0');
			$omBlackList = $omAvailableAct->act_getTNameList('om_blacklist', '*', ' WHERE id = '.$_REQUEST['id']);
			$platformselected	=	isset($_REQUEST['platformId']) ? $_REQUEST['platformId'] : $omBlackList[0]['platformId'];
			$account	=  $omAvailableAct->act_getTNameList('om_account','account,id','WHERE is_delete=0 AND platformId = '.$platformselected);
			
			
			$this->smarty->assign('navlist', $navlist);
			$this->smarty->assign('toptitle', '平台黑名单');
			$this->smarty->assign('toplevel', 3);
			$this->smarty->assign('secondlevel', '34');	
			
			$this->smarty->assign('account', $account);
			$this->smarty->assign('platformselected', $platformselected);
			$this->smarty->assign('platform', $platform);
			$this->smarty->assign('omBlackList', $omBlackList[0]);
			$this->smarty->display("updateBlackList.htm");
		} else {
			//修改黑名单
			$id					=	$_REQUEST['id'];
			$platformUsername	=	$_REQUEST['platformUsername'];
			$username			=	$_REQUEST['username'];
			$usermail			=	$_REQUEST['usermail'];
			$street				=	$_REQUEST['street'];
			$phone				=	$_REQUEST['phone'];
			$platformId			=	$_REQUEST['platformId'];
			$account			=	$_REQUEST['account'];
			
			$status = '';
			
			$omAvailableAct = new OmAvailableAct();
			$tName = 'om_blacklist';
			$addUser = '2';
			$addTime = time();
			$set = "SET platformUsername='$platformUsername',username='$username',usermail='$usermail',street='$street',phone='$phone',addTime='$addTime',platformId='$platformId',account='$account'";
			$where = "WHERE id='$id'";
			$affectRow = $omAvailableAct->act_updateTNameRow($tName, $set, $where);
			if($affectRow !== false){
				$status = '修改成功';
			}else{
				$status = '修改失败';
			}
			header('location:index.php?mod=omBlackList&act=getOmBlackList&status='.$status);
		}
	}

    public function view_deleteBlackList(){
        $id = isset($_GET['id'])?post_check($_GET['id']):'';
        $status = '';
        if(intval($id) == 0){
            $status = '数据异常，删除失败';
            header("location:index.php?mod=omBlackList&act=getOmBlackList&status=$status");
			exit;
        }
        $omAvailableAct = new OmAvailableAct();
        $tName = 'om_blacklist';
        $where = "WHERE is_delete=0 and id='$id'";
        $count = $omAvailableAct->act_getTNameCount($tName, $where);
        if(!$count){
            $status = '记录不存在，删除失败';
            header("location:index.php?mod=omBlackList&act=getOmBlackList&status=$status");
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
        header("location:index.php?mod=omBlackList&act=getOmBlackList&status=$status");
	}
}