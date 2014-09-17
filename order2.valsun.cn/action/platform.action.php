<?php
/*
 *提供账号和平台列表接口
 *@add by heminghua
 *@modify by : linzhengxiang ,date : 20140523
 */
class PlatformAct extends CheckAct{
	
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * 提供账号和平台列表接口
	 * @return array
	 * @author lzx
	 */
	public function act_getPlatformLists() {
		return M('Platform')->getPlatformLists();
	}
	
	/**
	 * 根据平台id获取平台名称
	 * @param int $id 平台编号
	 * @return string
	 * @author lzx
	 */
	public function act_getPlatformById($id){
	    $data = M('Platform')->getPlatformById($id, array('cache', 'mysql'), 600);
		return $data;
	}
	
	/**
	 * 新增平台信息
	 * @return array
	 * @author lzx
	 */
	public function act_insert(){
		$data = array();
		$data['platform']   = isset($_POST['platform']) ? trim($_POST['platform']) : '';
		$data['shortcode']  = isset($_POST['shortcode']) ? trim($_POST['shortcode']) : '';
		$data['suffix'] 	= isset($_POST['suffix']) ? trim($_POST['suffix']) : '';
		$data['addUser']	= get_userid();
		$data['addTime']	= time();
        return M('Platform')->insertData($data);
	}
	
	/**
	 * 修改平台信息
	 * @return array
	 * @author lzx
	 */
	public function act_update(){
		$data = array();
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$data['platform']  = isset($_POST['platform']) ? trim($_POST['platform']) : '';
		$data['shortcode'] = isset($_POST['shortcode']) ? trim($_POST['shortcode']) : '';
		$data['suffix'] 	 = isset($_POST['suffix']) ? trim($_POST['suffix']) : '';
        return M('Platform')->updateData($id, $data);
	}
	
	/**
	 *根据id获取平台信息
	 *@param id
	 *@return array
	 *@author yxd
	 */
	public function act_getPlatformInfoByid(){
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		return M('Platform')->getPlatformByid($id);
	}
	/**
	 * 逻辑删除平台信息
	 * @return array
	 * @author lzx
	 */
	public function act_delete(){
        return M('Platform')->deleteData($_GET['id']);
	}
}
?>	