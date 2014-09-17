<?php
class FromOpenConfigAct extends CheckAct{

	public function __construct(){
		parent::__construct();
		$this->perpage    =50;
	}

	/**
	 * 提供错误信息列表
	 * @return array
	 * @author lzx
	 */
	public function act_getFromOpenConfigLists() {
		return M('FromOpenConfig')->getFromOpenConfigLists($this->page, $this->perpage);
	}
	
	/**
	 * 提供错误信息数量
	 * @return array
	 * @author lzx
	 */
	public function act_getFromOpenConfigCount(){
		return M('FromOpenConfig')->getFromOpenConfigCount();
	}
	
	public function act_insert(){
		$data    = array();
		$data['functionname']    = isset($_POST['functionname']) ? trim($_POST['functionname']) : '';
		$data['name']            = isset($_POST['fname']) ? trim($_POST['fname']) : '';
		$data['requesturl']      = isset($_POST['requesturl']) ? trim($_POST['requesturl']) : '';
		$data['method']          = isset($_POST['method']) ? trim($_POST['method']) : '';
		$data['format']          = isset($_POST['format']) ? trim($_POST['format']) : '';
		$data['v']               = isset($_POST['v']) ? trim($_POST['v']) : '';
		$data['username']        = isset($_POST['username']) ? trim($_POST['username']) : '';
		$data['cachetime']       = isset($_POST['cachetime']) ? trim($_POST['cachetime']) : '';
		return M('FromOpenConfig')->insertData($data);
	}
	
	public function act_delete(){
		$id    = isset($_GET['id']) ? trim($_GET['id']) : '';
		return M('FromOpenConfig')->deleteData($id);
	}
	public function act_update(){
		$data                     = array();
		$id	                      = isset($_POST['id']) ? trim($_POST['id']) : '';
		$data['functionname']     = isset($_POST['functionname']) ? trim($_POST['functionname']) : '';
		$data['name']             = isset($_POST['name']) ? trim($_POST['name']) : '';
		$data['requesturl']    	  = isset($_POST['requesturl']) ? trim($_POST['requesturl']) : '';
		$data['method']           = isset($_POST['method']) ? trim($_POST['method']) : '';
		$data['format']           = isset($_POST['format']) ? trim($_POST['format']) : '';
		$data['v']                = isset($_POST['v']) ? trim($_POST['v']) : '';
		$data['username']         = isset($_POST['username']) ? trim($_POST['username']) : '';
		$data['cachetime']        = isset($_POST['cachetime']) ? trim($_POST['cachetime']) : '';
		return M('FromOpenConfig')->updateData($id, $data);
	}
	
	/**
	 * 根据id获取接口信息
	 * @param id
	 * @return array
	 * @author yxd
	 */
	public function act_getfromOpenConfigByid(){
		$id	    = isset($_GET['id']) ? trim($_GET['id']) : '';
		return M('FromOpenConfig')->getfromOpenConfigByid($id);
	}
	/**
	 * 预留搜索条件控制
	 */
	private function act_getFromOpenConfigCondition(){
		
	}
}
?>