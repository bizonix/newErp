<?php
class TopmenuAct extends CheckAct{

	public function __construct(){
		parent::__construct();
		$this->perpage = 50;
	}

	/**
	 * 提供导航信息列表
	 * @return array
	 * @author yxd
	 */
	public function act_getTopmenuLists() {
		$condition    = $this->act_getTopmenuCondition();
		return M('Topmenu')->getTopmenuLists($condition,$this->page);
	}
	/**
	 * 获取鉴权mod和act
	 * @return array
	 * @author yxd
	 */
	public function act_getmenutree(){
		
		$accesslists    = M('InterfacePower')->getUserPower(get_usertoken());
		return $accesslists;
	}
	
	/**
	 * 获取一级导航
	 * @return array;
	 * @author yxd
	 */
	public function act_getmenuTop1(){
		$menutop    = M('Topmenu')->getmenuTop1();
		return   $menutop;
	}
	
	/**
	 * 获取二级导航
	 * @return array;
	 * @author yxd
	 */
	public function act_getmenuTop2(){
		$menutop    = M('Topmenu')->getmenuTop2();
		return   $menutop;
	}
	/**
	 * 获取导航数量
	 * @return array
	 * @author yxd
	 */
	public function act_getTopmenuCount(){
		$condition    = $this->act_getTopmenuCondition();
		return M('Topmenu')->getTopmenuCount($condition);
	}
	
	/**
	 * 插入新数据
	 * @param array data
	 * @return boolean
	 * @author yxd
	 */
	public function act_insert(){
		$data                  = array();
		$data['name']          = isset($_POST['name']) ? trim($_POST['name']) : '';
		$data['url']           = isset($_POST['url']) ? trim($_POST['url']) : '';
		$data['model']         = isset($_POST['model']) ? trim($_POST['model']) : '';
		$data['position']      = isset($_POST['position']) ? trim($_POST['position']) : '';
		$data['sort']          = isset($_POST['sort']) ? trim($_POST['sort']) : '';
	 /* $data['creatorId']     = get_userid();
		$data['createTime']    = time(); */   //预留
		$data['is_disable']     = isset($_POST['is_disable']) ? trim($_POST['is_disable']) : '';
		if($data['position']==1){
			$data['pid']      = 0;
			$data['model']    = isset($_POST['modeltopInput']) ? trim($_POST['modeltopInput']) : '';
		}
		if($data['position']==2){
			$pid            = isset($_POST['modeltopSelect']) ? trim($_POST['modeltopSelect']) : '';
			$data['pid']    = $pid;
		}
		if($data['position']==3){
            $pid            = isset($_POST['modeltopSelect2']) ? trim($_POST['modeltopSelect2']) : '';;
			$data['pid']    = $pid;
		}  
		return M('Topmenu')->insertData($data);
	}
	
	/**
	 * 根据id删除记录
	 * @param int id
	 * @return boolean
	 * @author yxd
	 */
	public function act_delete(){
		$id    = isset($_GET['id']) ? trim($_GET['id']) : '';
		return M('Topmenu')->deleteData($id);
	}
	
	/**
	 * 根据id更新记录
	 * @param int id, array data
	 * @return boolean
	 * @author yxd
	 */
	public function act_update(){
		$data                  = array();
		$id	                   = isset($_POST['id']) ? trim($_POST['id']) : '';
	    $data['name']          = isset($_POST['name']) ? trim($_POST['name']) : '';
		$data['url']           = isset($_POST['url']) ? trim($_POST['url']) : '';
		$data['model']         = isset($_POST['model']) ? trim($_POST['model']) : '';
		$data['position']      = isset($_POST['position']) ? trim($_POST['position']) : '';
		$data['sort']          = isset($_POST['sort']) ? trim($_POST['sort']) : '';
		$data['is_disable']     = isset($_POST['is_disable']) ? trim($_POST['is_disable']) : '';
	    if($data['position']==1){
			$data['pid']      = 0;
			$data['model']    = isset($_POST['modeltopInput']) ? trim($_POST['modeltopInput']) : '';
		}
		if($data['position']==2){
			$pid            = isset($_POST['modeltopSelect']) ? trim($_POST['modeltopSelect']) : '';
			$data['pid']    = $pid;
		}
		if($data['position']==3){
            $pid            = isset($_POST['modeltopSelect2']) ? trim($_POST['modeltopSelect2']) : '';;
			$data['pid']    = $pid;
		}  
		return M('Topmenu')->updateData($id, $data);
	}
	
	/**
	 * 通过ID获取错误信息
	 * @param id
	 * @return array
	 * @author xyd
	 */
	
	public function act_getTopmenuByid(){
		$id     = isset($_GET['id']) ? trim($_GET['id']) : "";
		return  M('Topmenu')->getTopmenuByid($id);	
	}
	public function act_getToplevel($model){
		return M('Topmenu')->getPidSortByMod($model);
	
	}
	public function act_getSecondlevel($model){
		return M('Topmenu')->getSortByMod($model);
	}
	/**
	 * @param string $model
	 * @return array 
	 * @author yxd
	 */
	public function act_getMenuByModel($model){
		return M('Topmenu')->getMenuByModel($model);
	}
	private function act_getTopmenuCondition(){
		$data['name']         = $_GET['name'];
		foreach($data as $key=>$value){
			if($value){
				$data["$key"]    = array('$e'=>"$value");
			}
		}
		$data['is_delete']       = array('$e'=>0);
		return $data;
	}
}
?>