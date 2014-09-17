<?php
/**
 * 类名：Dept
 * 功能：部门管理类
 * 版本：2013-08-12
 * 作者：林正祥
 */
class DeptAct{
	
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;
	static $_instance;
	private $is_count = false;
	
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
	
	public function count(){
		$this->is_count = true;
		return $this;
	}
	
	//显示部门信息
	public function act_getDeptById($deptid){
		
		$deptid = intval($deptid);
		if ($deptid===0){
			self::$errCode = '5806';
			self::$errMsg  = 'deptid is error';
			return array();
		}
		
		$deptsingle = DeptModel::getInstance();
		$filed = ' dept_company_id,dept_id,dept_name,dept_principal,company_name ';
		$where = " WHERE dept_id='{$deptid}'";
		$deptinfo = $deptsingle->getDeptInfo($filed, $where);
		return $this->_checkReturnData($deptinfo, array());
	}
	
	/*
	*功能：外接系统获取部门信息
	*/
	public function act_getDeptLists($condition=array(), $sort='', $limit=''){
		
		$deptmodel = new DeptModel();
		
		$filed =' dept_company_id,dept_id,dept_name,dept_principal,company_name ';
		$where = !empty($condition)&&is_array($condition) ? 'WHERE '.implode(' AND ', $condition) : '';
		//获取条数		
		if ($this->is_count===true){
			$this->is_count = false;
			$deptcount = $deptmodel->count()->getDeptLists($filed, $where);
			return $this->_checkReturnData($deptcount, 0);
		}
		$orderby = empty($sort) ? '' : " ORDER BY {$sort} ";
		$deptlists = $deptmodel->getDeptLists($filed, $where, $orderby, $limit);
		return $this->_checkReturnData($deptlists, array());
	}
	
	private function _checkReturnData($data, $errreturn){
		if ($data===false){
			self::$errCode = DeptModel::$errCode;
			self::$errMsg  = DeptModel::$errMsg;
			return $errreturn;
		}elseif (empty($data)){
			self::$errCode = 5806;
			self::$errMsg  = 'There is no data!';
			if (self::$debug===true){
				self::$errMsg .= 'The SQL is '.DeptModel::$errMsg;
			}
			return $errreturn;
		}else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
	
	/**
	 * DeptAct::act_insert()
	 * 新增部门act
	 * @return string 
	 */
	public function act_insert(){
		if(!isset($_POST['deptname']) || trim($_POST['deptname']) == ''){
			exit("部门名为空!");
		}
		if(!isset($_POST['principal']) || trim($_POST['principal']) == ''){
			exit("部门负责人为空!");
		}
		$deptname	= post_check(trim($_POST['deptname']));
		$principal	= post_check(trim($_POST['principal']));
		$newDept = array(
						'deptName'      => $deptname,//部门名称，类型varchar(20)，必须项
						'deptPrincipal' => $principal,//部门负责人，类型varchar(15)，必须项
						'company'       => '1',//公司编号，类型int(5)，必须项
					);
        $result		= DeptModel::deptInsert($newDept);
		return $result;
    }
	
	/**
	 * DeptAct::act_update()
	 * 修改部门act
	 * @return string 
	 */
	public function act_update(){
		if(!isset($_POST['deptname']) || trim($_POST['deptname']) == ''){
			exit("部门名为空!");
		}
		if(!isset($_POST['principal']) || trim($_POST['principal']) == ''){
			exit("部门负责人为空!");
		}
		if(!isset($_POST['deptId']) || trim($_POST['deptId']) == '' || !intval($_POST['deptId'])){
			exit("部门ID非法!");
		}
		$deptname	= post_check(trim($_POST['deptname']));
		$principal	= post_check(trim($_POST['principal']));
		$deptId		= intval($_POST['deptId']);
		$newDept = array(
						'deptId'     	=> $deptId,//部门名称，类型varchar(20)，必须项
						'deptName'      => $deptname,//部门名称，类型varchar(20)，必须项
						'deptPrincipal' => $principal,//部门负责人，类型varchar(15)，必须项
						'company'       => '1',//公司编号，类型int(5)，必须项
					);
        $result		= DeptModel::deptUpdate($newDept);
		return $result;
    }
	
	/**
	 * DeptAct::act_delete()
	 * 删除部门act
	 * @return string 
	 */
	public function act_delete(){
		$deptId		= intval($_POST['deptId']);
		if(!$deptId){
			return false;
			exit;
		}
		$result		= DeptModel::deptDelete($deptId);
		return $result;
    }	
}
?>