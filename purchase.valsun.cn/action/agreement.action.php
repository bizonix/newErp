<?php
/**
 * 类名：AgreementAct
 * 功能：管理协议Action
 * 版本：2014-09-11
 * 作者：杨世辉
 */
class AgreementAct {

	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;
	static $_instance;

	public function __construct(){
		self::$debug = C('IS_DEBUG');
	}

	//单实例
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * 添加
	 */
	public function addAgreement() {
		global $dbConn;
		$data = array(
			'companyName'      	=> post_check( ban2quan($_POST['companyName']) ),
			'companyType'      	=> post_check( $_POST['companyType'] ),
			'contactPerson'    	=> post_check( $_POST['contactPerson'] ),
			'expiration'       	=> post_check( $_POST['expiration'] ),
			'status'    		=> 1,//默认值
			'is_delete'        	=> 0,//默认值
			'addTime'        	=> date("Y-m-d H:i:s"),
			'addUserId'			=> $_SESSION['sysUserId']//统一用户系统ID
		);
		$data['status'] = strtotime($data['expiration']) < time() ? 2 : 1;
		$row = AgreementModel::getByCompanyName($data['companyName']);
		if (!empty($row)) {
			$arr = array('code'=>2, 'msg'=>'该公司名称已存在');
			return json_encode($arr);
		}
		$dataSql = array2sql($data);
		$sql = "insert into ph_agreement set {$dataSql}";
		if($dbConn->execute($sql)){
			$arr = array('code'=>1, 'msg'=>'添加成功');
		} else {
			$arr = array('code'=>0, 'msg'=>'添加失败');
		}
		return json_encode($arr);
	}

	/**
	 * 修改
	 */
	public function editAgreement() {
		global $dbConn;
		$data = array(
			'companyType'      	=> post_check( $_POST['companyType'] ),
			'contactPerson'    	=> post_check( $_POST['contactPerson'] ),
			'expiration'       	=> post_check( $_POST['expiration'] ),
			'is_delete'        	=> 0,//默认值
			'modifyTime'        => date("Y-m-d H:i:s"),
			'modifyUserId'		=> $_SESSION['sysUserId']//统一用户系统ID
		);
		$data['status'] = strtotime($data['expiration']) < time() ? 2 : 1;
		$id = post_check($_POST['id']);
		$where = "id='{$id}'";
		$dataSql = array2sql($data);
		$sql = "update ph_agreement set {$dataSql} where {$where}";
		if($dbConn->execute($sql)){
			$arr = array('code'=>1, 'msg'=>'添加成功');
		} else {
			$arr = array('code'=>0, 'msg'=>'添加失败');
		}
		return json_encode($arr);
	}


	/**
	 * 删除
	 */
	public function delAgreement() {
		global $dbConn;
		$data = array(
			'is_delete'   		=> 1,
			'modifyTime'  		=> date("Y-m-d H:i:s"),
			'modifyUserId'		=> $_SESSION['sysUserId']//统一用户系统ID
		);
		$ids = post_check($_POST['idArr']);
		$where = "id in (". implode(',', $ids) .") ";
		$dataSql = array2sql($data);
		$sql = "update ph_agreement set {$dataSql} where {$where}";
		if($dbConn->execute($sql)){
			$arr = array('code'=>1, 'msg'=>'添加成功');
		} else {
			$arr = array('code'=>0, 'msg'=>'添加失败');
		}
		return json_encode($arr);
	}

	/**
	 * check
	 */
	public function checkCompanyExist() {
		$companyName = $_POST['companyName'];
		$row = AgreementModel::getByCompanyName($companyName);
		if (!empty($row)) {
			$arr = array('code'=>1, 'msg'=>'公司名称已存在');
		} else {
			$arr = array('code'=>0, 'msg'=>'公司名称不存在');
		}
		return json_encode($arr);
	}

}