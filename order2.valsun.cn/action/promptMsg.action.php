<?php
class PromptMsgAct extends CheckAct{

	public function __construct(){
		parent::__construct();
	}

	/**
	 * 提供错误信息列表
	 * @return array
	 * @author lzx
	 */
	public function act_getPromptMsgLists() {
		return M('PromptMsg')->getPromptMsgLists($this->page, $this->perpage, $sort);
	}
	
	/**
	 * 提供错误信息数量
	 * @return array
	 * @author lzx
	 */
	public function act_getPromptMsgCount(){
		//var_dump($count);exit;
		return M('PromptMsg')->getPromptMsgCount();
	}
	
	public function act_insert(){
		$data    = array();
		$data['type']          = isset($_POST['MsgType']) ? trim($_POST['MsgType']) : '';
		$data['status']        = isset($_POST['MsgStatus']) ? trim($_POST['MsgStatus']) : '';
		$data['errormsg']      = isset($_POST['errprMsg']) ? trim($_POST['errprMsg']) : '';
		$data['creatorId']     = get_userid();
		$data['createTime']    = time();
		$data['is_delete']     = 0;
		return M('PromptMsg')->insertData($data);
	}
	
	public function act_delete(){
		$id    = isset($_GET['id']) ? trim($_GET['id']) : '';
		return M('PromptMsg')->deleteData($id);
	}
	public function act_update(){
		$data                         = array();
		$id	                          = isset($_POST['id']) ? trim($_POST['id']) : '';
		$data['type']                 = isset($_POST['type']) ? trim($_POST['type']) : '';
		$data['status']               = isset($_POST['status']) ? trim($_POST['status']) : '';
		$data['errormsg']    		  = isset($_POST['errormsg']) ? trim($_POST['errormsg']) : '';
		$data['lastmodifyuserId']     = get_userid();
		$data['lastmodefyTime']       = time();
		return M('PromptMsg')->updateData($id, $data);
	}
	
	/**
	 * 通过ID获取错误信息
	 * @param id
	 * @return array
	 * @author xyd
	 */
	
	public function act_getPromptMsgByid(){
		$id     = isset($_GET['id']) ? trim($_GET['id']) : "";
		return  M('PromptMsg')->getPromptMsgByid($id);	
	}
	
	private function act_getPromptMsgCondition(){
		
	}
}
?>