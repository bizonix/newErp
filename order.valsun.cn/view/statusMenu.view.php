<?php
/*
 * 订单流程状态管理
 *add by:hws
 */
class StatusMenuView extends BaseView{
	//订单流程状态页面
    public function view_statusMenu(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$StatusMenuAct = new StatusMenuAct();
		$menu      	   = $StatusMenuAct->act_getStatusMenuList("*","where is_delete=0 and storeId=1");
		$this->smarty->assign('menu', $menu);
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'','title'=>'订单流程'),
                );
        $this->smarty->assign('navlist', $navlist);
		
		//流程状态分组
		if(!empty($menu)){
			$group = array();
			foreach($menu as $info){
				if($info['groupId'] == '0'){
					$group[$info['id']] = '一级分组';
					continue;
				}
				$group_info = StatusMenuGroupModel::getMenuGroupList("statusName","where statusCode='{$info['groupId']}'");
				$group[$info['id']] =  $group_info[0]['statusName'];
			}

		}
		$this->smarty->assign('group', $group);
		
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 39;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '订单流程');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('statusMenu.htm');
    }
	
	//新增订单流程状态
	public function view_addMenu(){
		$group = StatusMenuGroupModel::getMenuGroupList("*","where storeId=1 and groupId = 0 and is_delete=0");
		$this->smarty->assign('group', $group);
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=StatusMenu&act=statusMenu','title'=>'订单流程'),
						array('url'=>'','title'=>'新增流程'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '新增流程');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 39;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('menuAdd.htm');
	}
	
	//修改订单流程状态
	public function view_editMenu(){
		$id   = intval($_GET['id']);
		$menu = StatusMenuModel::getStatusMenuList("*","where id=$id");
		$this->smarty->assign('menu',$menu); 		

		$group = StatusMenuGroupModel::getMenuGroupList("*","where storeId=1 and groupId = 0 and is_delete=0");
		$this->smarty->assign('group', $group);
		
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=StatusMenu&act=statusMenu','title'=>'订单流程'),
						array('url'=>'','title'=>'修改流程'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '修改流程');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 39;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('menuAdd.htm');
	}
		
	//提交增/改订单流程状态
	public function view_sureAddMenu(){
		$StatusMenuAct = new StatusMenuAct();
		$is_ok         = $StatusMenuAct->act_sureAddMenu();
		if($is_ok){
			if($is_ok==2){
				$state = '流程已存在';
			}else{
				$state = '操作成功';
			}
		}else{
			$state = '操作失败';
		}
		header('location:index.php?mod=StatusMenu&act=statusMenu&state='.$state);exit;
	}
	

}