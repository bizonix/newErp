<?php
/*
 * 汇率管理
 *add by:hws
 */
class CurrencyView extends BaseView{  
	//汇率管理页面
    public function view_currency(){
		$state  = isset($_GET['state'])?post_check($_GET['state']):'';
		$this->smarty->assign('state',$state);
		$CurrencyAct = new CurrencyAct();
		$currency    = $CurrencyAct->act_getCurrencyList("*","where 1");
		$this->smarty->assign('currency', $currency);
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'','title'=>'汇率管理'),
                );
        $this->smarty->assign('navlist', $navlist);
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 38;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
        $this->smarty->assign('toptitle', '汇率列表');
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('currency.htm');
    }
	
	//新增汇率
	public function view_addCurrency(){
		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=currency&act=currency','title'=>'汇率管理'),
						array('url'=>'','title'=>'添加货币'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '新增订单属性');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 38;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('currencyAdd.htm');
	}
	
	//修改汇率
	public function view_editCurrency(){
		$id        = intval($_GET['id']);
		$currency  = CurrencyModel::getCurrencyList("*","where id=$id");
		$this->smarty->assign('currency',$currency); 		

		$navlist = array(array('url'=>'','title'=>'系统设置'),              //面包屑数据
                        array('url'=>'index.php?mod=currency&act=currency','title'=>'汇率管理'),
						array('url'=>'','title'=>'修改汇率'),
                );
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '修改汇率');
		$toplevel = 3;      //一级菜单的序号
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = 38;   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$this->smarty->assign('curusername', $_SESSION['userName']);
	
		$this->smarty->display('currencyAdd.htm');
	}
		
	//提交增/改汇率
	public function view_sureAddCurr(){
		$CurrencyAct = new CurrencyAct();
		$is_ok       = $CurrencyAct->act_sureAddCurr();
		if($is_ok){
			if($is_ok==2){
				$state = '货币已存在';
			}else{
				$state = '操作成功';
			}
		}else{
			$state = '操作失败';
		}
		header('location:index.php?mod=currency&act=currency&state='.$state);exit;
	}
	

}