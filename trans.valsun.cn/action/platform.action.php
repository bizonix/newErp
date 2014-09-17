<?php
/*
 * 平台管理action层页面 platform.action.php
 * ADD BY 陈伟 2013.7.26
 */
class platformAct extends Auth{
	static $errCode	=	0;
	static $errMsg	=	"";
	public $where   =   "";
	/*
     * 构造函数 初始化数据库连接
     */
    public function __construct($where = '') {
        $this->where = $where;
    }
	

	
	/*
     * 平台管理数据调用->分页计算总条数
     */
	function  act_getPlatformListNum(){
		//调用model层获取数据
		$platformModel = new platformModel();
		$num 				  =	$platformModel->getPlatformListNum();
		if($num){
			return $num;
		}else{
			return false;
		}
	}
	
	
	/*
     * 平台管理数据调用
     */
	function  act_platformManage($where=''){
		//调用model层获取数据
		$platformModel = new platformModel();
		$list =	$platformModel->platformManageList($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 提交新平台
     */
	function  act_platformAddIn($platformArr){
		//调用model层获取数据
		$platformModel = new platformModel();
		$list =	$platformModel->platformAddIn($platformArr);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * UPDATE平台
     */
	function  act_platformEditUp($platformEditArr,$where){
		
		//调用model层获取数据
		$platformModel = new platformModel();
		$list =	$platformModel->platformEditUp($platformEditArr,$where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
	/*
     * 删除平台
     */
	function  act_platformDel($where){
		//调用model层获取数据
		$platformModel = new platformModel();
		$list =	$platformModel->platformDel($where);
		if($list){
			return $list;
		}else{
			return false;
		}
	}
	
		/*
         * ajax检测平台是否重复
         */
        public function act_checkPlatformExist(){
            $name     = trim($_GET['name']);
            //调用model层获取数据
			$platformModel = new platformModel();
            $is = $platformModel->checkPlatformExist($name);
            if($is){    //存在
                self::$errCode = 0;
				self::$errMsg  = '已存在！';
				return false;
            }else{ //不存在
                self::$errCode = 1;
				self::$errMsg  = 'OK';
				return true;
            }
        }
	
	
}
?>
