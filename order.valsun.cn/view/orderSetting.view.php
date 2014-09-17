<?php
/*
 * 订单属性管理
 *add by:hws
 */
class OrderSettingView extends BaseView{  
	//订单属性管理页面页面
    public function view_property(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$OrderSettingAct = new OrderSettingAct();
		$property        = $OrderSettingAct->act_getPropertyList("*","where storeId=1");
		$this->smarty->assign('property', $property);
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'','title'=>'订单属性'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 36;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '订单属性列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('orderAttr.htm');
    }
	
	//新增订单属性
	public function view_addOrderAttr(){
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=orderSetting&act=property','title'=>'订单属性'),
						array('url'=>'','title'=>'新增订单属性'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '新增订单属性');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 36;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('ordreAttrAdd.htm');
	}
	
	//修改属性
	public function view_editOrderAttr(){
		$id        = intval($_GET['id']);
		$orderAttr = OrderAttrModel::getOrderAttrList("*","where id=$id");
		$this->smarty->assign('orderAttr',$orderAttr); 		

		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=orderSetting&act=property','title'=>'订单属性'),
						array('url'=>'','title'=>'修改订单属性'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '修改订单属性');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 36;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('ordreAttrAdd.htm');
	}
		
	//提交增/改属性
	public function view_sureAddAttr(){
		$OrderSettingAct = new OrderSettingAct();
		$is_ok          = $OrderSettingAct->act_sureAddAttr();
		if($is_ok){
			if($is_ok==2){
				$state = '属性已存在';
			}else{
				$state = '操作成功';
			}
		}else{
			$state = '操作失败';
		}
		header('location:index.php?mod=orderSetting&act=property&state='.$state);exit;
	}
	

}