<?php
/*
 *
 * @package order.valsun.cn
 * @author zqt
 * @copyright 2013
 * @version 1.0
 * @access public
 * @add by : linzhengxiang ,date : 20140530
 */
class UserCompetenceView extends BaseView {

	/**
     * 构造函数
     */
    public function __construct() {
    	parent::__construct();
    }
	
	/**
	 * 订单系统相关操作权限获取
	 *@eturn array
	 *@author lzx 
	 *modify by yxd 2014/7/17
	 */
	public function view_edit(){
		F('order');
		//编辑权限控制， 存在隐患BUG， 需要增加是否有编辑这个用户的权限
		$navlist = array (
				array ('url' => 'index.php?mod=user&act=index', 'title' => '授权管理'),
				array ('url' => 'index.php?mod=user&act=index', 'title' => '用户列表'),
				array ('url' => '#','title' =>'订单系统相关操作权限获取')
		);
		$uid                 = isset($_GET['uid'])&intval($_GET['uid'])>0 ? intval($_GET['uid']) : 0;
		$competences         = A('UserCompetence')->act_getCompetenceByUserId($uid);
		$mycompetences       = A('UserCompetence')->act_getCompetenceByUserId(get_userid()); //需要设置session的用户id, 还需要增加文件夹移动权限
		$groupLists          = M('StatusMenu')->getOrderStatusByGroupId();//文件夹分组列表
		$statusLists         = A('StatusMenu')->act_getStatusMenuList();//文件夹状态列表
		$visibleCarrier      = json_decode($competences['visible_carrier'],true);

		$visibleEditorder    = explode(',',$competences['visible_editorder']);
        $myVisibleEditorder  = explode(',',$mycompetences['visible_editorder']);

		$visibleCarrier0     = $visibleCarrier[0];//快递
		$visibleCarrier1     = $visibleCarrier[1];//非快递
		$myvisibleCarrier    = json_decode($competences['visible_carrier'],true);
		$editorder_options   = C('EDITORDEROPTIONS');

		$this->smarty->assign('platform_account', json_decode($competences['visible_platform_account'],true));//被修改者平台账号控制权限
		$this->smarty->assign('myplatform_account', json_decode($mycompetences['visible_platform_account'],true));//修改者平台账号控制权限
		$this->smarty->assign('showfolder', explode(',', $competences['visible_showfolder']));//被修改者文件夹显示权限
		$this->smarty->assign('myshowfolder', explode(',', $mycompetences['visible_showfolder']));//修改者文件夹显示权限
		$this->smarty->assign('movefolder', $competences['visible_movefolder']);//被修改者文件夹移动权限
		$this->smarty->assign('mymovefolder', $mycompetences['visible_movefolder']);//修改者文件夹移动权限
		$this->smarty->assign('statusLists',$statusLists);//文件状态分组
		$this->smarty->assign('groupLists',$groupLists);//文件状态
		$this->smarty->assign("editorderOptions",$editorder_options);
        $this->smarty->assign('myVisibleEditorder',$myVisibleEditorder);
        $this->smarty->assign('visibleEditorder',$visibleEditorder);
        $this->smarty->assign('carrierListk',A('PlatformToCarrier')->act_getCarrierFromApi(0));//快递
        $this->smarty->assign('carrierListnk',A('PlatformToCarrier')->act_getCarrierFromApi(1));//非快递
        $this->smarty->assign('visibleCarrier0',$visibleCarrier0);
        $this->smarty->assign('visibleCarrier1',$visibleCarrier1);
        $this->smarty->assign('toptitle', '订单系统细颗粒度权限控制');
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('User') );
		$this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('User'));
    	$this->smarty->assign('navlist', $navlist);
    	$this->smarty->assign('toptitle', '授权管理');
		$this->smarty->display("compenseEdit.htm");
	}
	
	/**
	 * 更新平台账号控制权限
	 * @author yxd
	 */
	public function view_replace(){
		$UC = A('UserCompetence');
		if ($UC->act_replace()){
			$this->success(implode('<br>', $UC->act_getErrorMsg()), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}else{
			$this->error(implode('<br>', $UC->act_getErrorMsg(), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset"));
		}
	}
	/**
	 * 更新文件夹显示权限
	 * @author yxd 
	 */
	public function view_saveShowfolder(){
		$UC    = A('UserCompetence');
		if ($UC->act_saveShowfolder()){
				$this->success("操作成功", "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
			}else{
				$this->error(implode('<br>', $UC->act_getErrorMsg()), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
			}
		}
	/**
	 * 更新文件夹移动权限
	 * @author yxd
	 */
	public function view_saveMovefolder(){
		$UC    = A('UserCompetence');
		if ($UC->act_saveMovefolder()){
			$this->success("操作成功", "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}else{
			$this->error(implode('<br>', $UC->act_getErrorMsg()), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}
	}
	
	/**
	 * 更新订单编辑权限
	 * @author yxd 
	 */
	public function view_saveEditorder(){
		$UC    = A('UserCompetence');
		$UC->act_saveEditorder();
		
		if ($UC->act_saveEditorder()){
			$this->success("操作成功", "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}else{
			$this->error(implode('<br>', $UC->act_getErrorMsg()), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}
	}
	/**
	 * 更新可见运输方式权限
	 * @author yxd
	 */
	public function view_saveCarrier(){
		$UC    = A('UserCompetence');
		if ($UC->act_saveCarrier()){
			$this->success("操作成功", "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}else{
			$this->error(implode('<br>', $UC->act_getErrorMsg()), "index.php?mod=UserCompetence&act=edit&uid={$_POST['uid']}&rc=reset");
		}
	}	
		
		
	}
	
	
?>