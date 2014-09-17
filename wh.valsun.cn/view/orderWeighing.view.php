<?php
/**
 * 称重扫描
 * @author heminghua
 */
class orderWeighingView extends CommonView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
	public function view_orderWeighing(){
		$config_path = 'images/cz';
		$time = date("Y/m/d",time());
		$dirPath = $config_path.'/'.$time;
		if (!is_dir($dirPath)){
			mkdirs($dirPath,0777);
		}
		$this->smarty->assign('time', $time);
		$navlist = array(array('url'=>'','title'=>'出库'),              //面包屑数据
				 array('url'=>'index.php?mod=waitWeighing&act=waitWeighingList','title'=>'待称重'),
				 array('url'=>'','title'=>'称重扫描<小包>'),
		);
        $secnev = 3;
        $toplevel = 2;
        $secondlevel = 26;
        //$userName = $_SESSION['username'];
        //$this->smarty->assign('secnev','1');
        
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('secnev',  $secnev);
        $this->smarty->assign('toplevel',  $toplevel);
        $this->smarty->assign('secondlevel',  $secondlevel);

		$partion_list = CommonModel::getChannelNameByIds('all');
		//$channel_list = CommonModel::getCarrierChannelByIds('all');
		$channel_list = C('MAILWAYCONFIG');

		$this->smarty->assign("partion_list",$partion_list);
		$this->smarty->assign("channel_list",$channel_list);
        //$this->smarty->assign('toptitle', '货品资料管理');
        $this->smarty->display('orderWeighing.htm');
	}
}
?>