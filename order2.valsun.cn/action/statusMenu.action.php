<?php
/*
 * 类名：订单流程状态管理
 * 功能：订单流程状态信息
 * 作者：hws
 * last modified by Herman.Xi @20131205
 * 修改：linzhengxiang @ 20140601
 */
class StatusMenuAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	
	
	public function act_getStatusMenuList(){
		return M('StatusMenu')->getStatusMenuList();
	}
	
	
	/**
	 * 组装 订单状态码和状态名称数组
	 * @return array
	 * @author yxd
	 */
	public function act_getMenuGroupList($menu){
		if(!empty($menu)){
			$group = array();
			foreach($menu as $info){
				if($info['groupId'] == '0'){
					$group[$info['id']] = '一级分组';
					continue;
				}
				$group_info = M('statusMenu')->getMenuGroupList($info['groupId']);
				$group[$info['id']] =  $group_info[0]['statusName'];
			}
		
		}
		return $group;
	}
	
	/**
	 * 根据Id获取单条文件夹信息
	 * @return array
	 * @author yxd
	 */
	public function act_getStatusMenu(){
		$id    = isset($_GET['id']) ? $_GET['id'] : "";
		return M('StatusMenu')->getStatusMenu($id);
	}
	
	
	/**
	 * 获取菜单分组信息
	 * @return array
	 * @author yxd
	 */
	public function act_getMenuGroup(){
		return M('StatusMenu')->getMenuGroup();
	}
	
	/**
	 * 根据Id删除文件信息   异步实现
	 * @return array
	 * @author yxd
	 */
	public function act_delete(){
		$id = trim($_POST['id']);
		if(M('StatusMenu')->deleteData($id)){
			self::$errMsg[200]     = get_promptmsg(200,"删除");
			return array('state' => 'ok');
		}else{
			return array('state' => 'no');
		}
	}
	/**
	 * 增加文件夹信息
	 * @return array
	 * @author yxd
	 */
	public function act_insert(){
		$data['statusName']      = isset($_POST['statusName']) ? $_POST['statusName'] : "";
		$data['statusCode']      = isset($_POST['statusCode']) ? $_POST['statusCode'] : "";
		$data['groupId']         = isset($_POST['groupId']) ? $_POST['groupId'] : "";
		$data['sort']            = isset($_POST['sort']) ? $_POST['sort'] : "";
		$data['note']            = isset($_POST['note']) ? $_POST['note'] : "";
		return M('StatusMenu')->insertData($data);
	}
	
	/**
	 * 修改文件夹信息
	 * @return boolean
	 * @author yxd
	 */
	public function act_update(){
		$id                      = isset($_POST['menuId']) ? $_POST['menuId'] : "";
		$data['statusName']      = isset($_POST['statusName']) ? $_POST['statusName'] : "";
		$data['statusCode']      = isset($_REQUEST['statusCode']) ? $_REQUEST['statusCode'] : "";
		$data['groupId']         = isset($_POST['groupId']) ? $_POST['groupId'] : "";
		$data['sort']            = isset($_POST['sort']) ? $_POST['sort'] : "";
		$data['note']            = isset($_POST['note']) ? $_POST['note'] : "";
		foreach($data as $key=>$value){
			if(empty($value)){
				unset($data[$key]);
			}
		}
		return M('StatusMenu')->updateData($id,$data);
	}
	/**
	 * 根据文件编号获取文件列表
	 * @return array
	 * @author lzx
	 */
	public function act_getStatusMenuByCode($cid){
		return M('StatusMenu')->getStatusMenuByCode($cid);
	}

	/**
	 * 根据文件编号获取文件列表
	 * @return array
	 * @author lzx
	 */
	public function act_getGroupMenuByCode($gid){
		return M('StatusMenu')->getGroupMenuByCode($gid);
	}
	
	/**
	 * 获取用户对应的分类和状态码
	 * @param int $uid 用户id
	 * @return array
	 * @author lzx
	 */
	public function act_getStatusMenuByUserId($uid){
		return M('StatusMenu')->getStatusMenuByUserId($uid);
	}

	/**
	 * 根据用户ID和状态ID获取用户对应的分类
	 * @param int $uid 用户id
	 * @param int $statusid
	 * @return array
	 * @author lzx
	 */
	public function act_getTypeMenuByUserId($uid, $statusid){
		return M('StatusMenu')->getTypeMenuByUserId($uid,$statusid);
	}

    /**
     * @param int $id
     * @param int $uId
     * @return mixed
     * 读取订单的分类添加权限控制
     */
    public function act_getOrderStatusByGroupId($id=0,$uId = 0){
        if($uId == 0){
            $uId = get_userid();
        }
        $mycompetences = A('UserCompetence')->act_getCompetenceByUserId($uId);
        $visibleShowfolder = explode(',',$mycompetences['visible_showfolder']);
        $orderStatus = M('statusMenu')->getOrderStatusByGroupId($id);
        if($orderStatus){
            foreach($orderStatus as $k=>$statusList){
                $id = $statusList['id'];
                if(!in_array($id,$visibleShowfolder)){
                    unset($orderStatus[$k]);
                }
            }
        }
        return $orderStatus;
    }
}
?>