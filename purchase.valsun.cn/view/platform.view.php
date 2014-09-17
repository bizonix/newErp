<?php
class PlatformView extends BaseView{
	public function view_index(){ 
		error_reporting(0);
		global $dbConn;
		$mod	= $_GET['mod'];
		$act	= $_GET['act'];
		$enName = trim($_POST['enName']);
		$cnName = trim($_POST['cnName']);
		$now = time();
		$user = $_SESSION['userCnName'];
		$info = "";
		if(!($enName == "" && $cnName == "")){
			$sql = "INSERT INTO `ph_sale_platform`(`en_name`, `cn_name`,addUser, `addtime`) VALUES ('{$enName}','{$cnName}','{$user}',$now)";
			if($dbConn->execute($sql)){
				$info = "添加成功。。。。";
			}
		}
        $this->smarty->assign('title','销售平台管理');
        $this->smarty->assign('info',$info);
        $this->smarty->assign('mod',$mod);//模块权限
        $this->smarty->assign('act',$act);//操作权限
		$this->smarty->display('platform.htm');
	}    
}
?>
