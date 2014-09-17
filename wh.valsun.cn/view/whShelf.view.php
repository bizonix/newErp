<?php
/*
 *上架操作页面
 *@author heminghua
 *
 */
class whShelfView extends CommonView{
	/*
	* 构造函数
	*/
    public function __construct() {
        parent::__construct();
    }
    /*
     * 上架页面
     * 
     */
    public function view_whShelf(){
        $navlist = array(array('url'=>'','title'=>'入库'),              //面包屑数据
                         array('url'=>'','title'=>'上架操作'),
        );
        $toplevel = 1;
        $secondlevel = "14";
        //$userName = $_SESSION['username'];
        //$this->smarty->assign('secnev','1');
        $toptitle = '上架操作';        //顶部链接
    	$this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        //$this->smarty->assign('toptitle', '货品资料管理');
        $this->smarty->display('whShelf.htm');
    
    }

}
?>    