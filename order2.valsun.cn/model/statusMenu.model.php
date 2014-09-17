<?php
/*
 *状态值
 *ADD BY hws
 * @modify by lzx ,date 20140601
 */
class StatusMenuModel extends CommonModel{
	
	public function __construct(){
		parent::__construct();
	}
	public function checkIsExists(){
		return false;
	}
	/**
	 * 获取文件夹列表信息
	 * @return array
	 * @author lzx
	 */
	public function getStatusMenuList(){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE is_delete=0 AND groupId != 0 ")->limit('*')->sort('order by sort asc')->select(array('mysql'), 1800);
	}
	
	
	/**
	 * 根据Id获取单条文件夹信息
	 * @return array
	 * @author yxd
	 */
	public function getStatusMenu($id){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE is_delete=0 and id=$id")->limit('*')->select(array('cache', 'mysql'), 1800);
	}
	
	/**
	 * 根据Id获取单条文件夹信息
	 * @return array
	 * @author yxd
	 */
	public function getStatusMenuGroupIdById($id){
		$StatusMenu = $this->getStatusMenuByCode($id);
		return isset($StatusMenu['groupId']) ? $StatusMenu['groupId'] : false;
	}
	
	/**
	 * 获取菜单分组信息
	 * @return array
	 * @author yxd
	 */
	public function getMenuGroup(){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE is_delete=0  ")->sort(" ORDER BY groupId ASC,statusCode ASC ")->limit('*')->select(array('mysql'), 1800);
	}
	/**
	 * 根据文件编号获取文件列表
	 * @return array
	 * @author lzx
	 */
	public function getStatusMenuByCode($cid){
		$statusmenu = $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE is_delete=0 AND statusCode=".intval($cid))->limit(1)->select(array('cache', 'mysql'), 1800);
		return isset($statusmenu[0]) ? $statusmenu[0] : false;
	}
	
	/**
	 * 根据文件类型获取文件列表
	 * @return array
	 * @author lzx
	 */
	public function getGroupMenuByCode($gid){
		$groupmenu = $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu_group WHERE is_delete=0 AND groupCode=".intval($gid))->limit(1)->select(array('cache', 'mysql'), 1800);
		return isset($groupmenu[0]) ? $groupmenu[0] : false;
	}
	
	/**
	 * 根据用户ID和状态ID获取用户对应的分类
	 * @param int $uid 用户id
	 * @param int $statusid 
	 * @return array
	 * @author lzx
	 */
	public function getTypeMenuByUserId($uid, $statusid){
		$status = $this->getStatusMenuByUserId($uid);
		return isset($status[$statusid]) ? $status[$statusid] : false;
	}
	
	/**
	 * 根据订单状态码获取状态名称
	 * @return array
	 * @author yxd
	 */
	public function getMenuGroupList($statusCode){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE statusCode='$statusCode'")->limit(1)->select(array('mysql'));
	}
	/**
	 * 获取用户对应的分类和状态码
	 * @param int $uid 用户id
	 * @return array
	 * @author lzx
	 */
    public function getStatusMenuByUserId($uid){
    	$mystatus = array();
    	$competence = M('UserCompetence')->getCompetenceByUserId($uid);
        $statusCodeStr = !empty($competence['visible_showfolder'])?$competence['visible_showfolder']:0;//防止$statusCodeStr为空，sql错误
    	$statusmenulists = $this->sql("SELECT statusCode,oType,groupId FROM ".C('DB_PREFIX')."status_menu WHERE is_delete=0 AND groupId>0 AND statusCode IN ($statusCodeStr)")->sort('')->limit('*')->select(array('cache', 'mysql'), 300);
    	foreach ($statusmenulists AS $statusmenulist){
    		$mystatus[$statusmenulist['groupId']][] = $statusmenulist['statusCode'];
    	}
    	return $mystatus;
	}
	/**
	 * 获取文件夹列表信息父目录子目录格式
	 * @return array
	 * @author andy
	 */
	public function getStatusMenuIndentList($groupId = 0){
		$top_arr = $this->getOrderStatusByGroupId($groupId);
		foreach ($top_arr as $key=>$val){
			$tmp_group_id = $val['groupId'];
			$tmp_id = $val['id'];
			$tmp = $this->getStatusMenuIndentList($tmp_id);
			if(!empty($tmp)){
				$top_arr[$key]['child'] = $tmp;
			}
		}
		return $top_arr;
	}
	/*
	 * 获取某一个父分类下的子分类(id为110的分类为根分类)
	 * added by andy
	 */
	public function getOrderStatusByGroupId($GroupId='0'){
		return $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu 
		WHERE   is_delete=0 and groupId='$GroupId'")->sort('order by sort asc')->select(array('mysql'));
	}
	/**
	 * 根据订单状态码获取状态名称
	 * @return array
	 * @param string statusCode 
	 * @param string field 需要返回的字段值 
	 * @author andy
	 */
	public function getOrderStatusByStatusCode($statusCode,$field='*'){
		$arr = $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE statusCode='$statusCode'")->limit(1)->select(array('mysql'));
		if(empty($arr)){
			return array();
		}
		if( in_array($field, array_keys($arr[0]) ) ){
			return $arr[0][$field];
		}
		return $arr[0];
	}
	/*
	 * 根据状态获取读取状态列表(最新版)
	 * added by andy
	 */
	public function act_changeOstatusId(){
		$ostatus = $_POST['ostatus'];
		$list = $this->sql("SELECT * FROM ".C('DB_PREFIX')."status_menu WHERE groupId='$ostatus'  AND is_delete=0")->select(array('mysql'));
		self::$errCode = StatusMenuModel::$errCode;
		self::$errMsg  = StatusMenuModel::$errMsg;
		if($list){
			return $list;
		}else{
			return false;
		}
	}

    /**
     * @param $id
     * @return mixed
     * 获取 statusName
     */
    public function getOrderStatusName($id){
        $statusNameList =  $this->sql("SELECT statusName FROM ".C('DB_PREFIX')."status_menu WHERE id='$id'  AND is_delete=0")->select();
        return $statusNameList;
    }
}
?>