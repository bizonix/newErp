<?php
/*
 * 小包订单复核
 *add by:hws
 */
class OrderReviewView extends BaseView{  
	//配货清单出库页面
    public function view_orderReview(){
		$config_path = 'images/fh';
		$time = date("Y/m/d",time());
		$dirPath = $config_path.'/'.$time;
		if (!is_dir($dirPath)){
			mkdirs($dirPath,0777);
		}
		$this->smarty->assign('time', $time);
		$this->smarty->assign('curusername', $_SESSION['userName']);
		$toptitle = '复核扫描';        //顶部链接
        $this->smarty->assign('toptitle', $toptitle);
        
        $toplevel = 2;      //顶层菜单
        $this->smarty->assign('toplevel',$toplevel);
        
        $secondlevel = '24';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
	
		$this->smarty->display('orderReview.htm');
    }

}