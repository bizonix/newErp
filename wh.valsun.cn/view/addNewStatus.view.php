<?php
/**
 *点货信息
 * @author heminghua
 */
class addNewStatusView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     *点货页面 
     */
    public function view_addNewStatus(){
        
        $navlist = array(array('url'=>'index.php?mod=warehouseManagement&act=whStore','title'=>'仓库设置'),              //面包屑数据
                         array('url'=>'index.php?mod=LibraryStatus&act=libraryStatusList','title'=>'状态管理'),
                         array('url'=>'','title'=>'添加状态'),
                );
        $toplevel = 4;
        $secondlevel = "08";
        //$userName = $_SESSION['username'];
        $toptitle = '点货操作';        //顶部链接
    	//$this->smarty->assign('toptitle', $toptitle);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);
        
		$lists = addNewStatusModel::selectGroup();
		//print_r($lists);
		$this->smarty->assign("lists",$lists);
		
        $this->smarty->display('addNewStatus.htm');
    }
}
?>