<?php
/**
 * 类名：ActionAct
 * 功能：管理Action岗位信息
 * 版本：2013-08-13
 * 作者：林正祥
 */
class ActionAct{
	static $errCode	  = 0;
	static $errMsg	  = '';
	static $debug	  = false;
	static $_instance;
	private $is_count = false;

	public function __construct() {
		self::$debug = C('IS_DEBUG');
	}

	//单实例
    public static function getInstance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function count() {
		$this->is_count = true;
		return $this;
	}

	//获取控制器方法信息
	public function act_getActionById($actionid) {
		$actionid = intval($actionid);
		if($actionid === 0) {
			self::$errCode = '5806';
			self::$errMsg  = 'actionid is error';
			return array();
		}
		$actionsingle 	= ActionModel::getInstance();
		$filed 			= ' action_id,action_description,action_name,action_group_id,group_description,group_name,group_system_id';
		$where 			= " WHERE action_id='{$actionid}'";
		$actioninfo 	= $actionsingle->getActionInfo($filed, $where);
		return $this->_checkReturnData($actioninfo, array());
	}

	//获取控制器mod方法信息 modify guanyongjun 2013-08-22
	public function act_getActionGroupByName($groupname, $systemid) {
		if(!preg_match("/^[a-z_]*$/i", $groupname)) {
			self::$errCode = '5805';
			self::$errMsg  = 'groupname is error';
			return array();
		}
		$actionsingle 	= ActionModel::getInstance();
		$filed 			= ' action_id,action_description,action_name,action_group_id,group_description,group_name,group_system_id';
		$where 			= " WHERE group_name='{$groupname}' AND group_system_id = '{$systemid}'";
		$actioninfo 	= $actionsingle->getActionInfo($filed, $where);
		return $this->_checkReturnData($actioninfo, array());
	}

	//获取控制器act方法信息 modify guanyongjun 2013-08-22
	public function act_getActionByName($actionname, $groupid) {
		if (!preg_match("/^[a-z_]*$/i", $actionname)){
			self::$errCode = '5806';
			self::$errMsg  = 'actionname is error';
			return array();
		}
		$actionsingle 	= ActionModel::getInstance();
		$filed 			= ' action_id,action_description,action_name,action_group_id,group_description,group_name,group_system_id';
		$where 			= " WHERE action_name='{$actionname}' AND action_group_id={$groupid}";
		$actioninfo 	= $actionsingle->getActionInfo($filed, $where);
		return $this->_checkReturnData($actioninfo, array());
	}

	/*
	*功能：外接系统获取部门信息
	*/
	public function act_getActionLists($condition, $sort='', $limit='') {
		$actionmodel 	= new ActionModel();
		$filed 			= ' action_id,action_description,action_name,action_group_id,group_description,group_name,group_system_id';
		$where 			= !empty($condition)&&is_array($condition) ? implode(' AND ', $condition) : '';
		//获取条数
		if($this->is_count === true) {
			$this->is_count = false;
			$actioncount 	= $actionmodel->count()->getActionLists($filed, $where);
			return $this->_checkReturnData($actioncount, 0);
		}
		$orderby 		= empty($orderby) ? '' : " ORDER BY {$sort} ";
		$actionlists 	= $actionmodel->getActionLists($filed, $where, $orderby, $limit);
		return $this->_checkReturnData($actionlists, array());
	}

	private function _checkReturnData($data, $errreturn) {
		if($data === false) {
			self::$errCode = ActionModel::$errCode;
			self::$errMsg  = ActionModel::$errMsg;
			return $errreturn;
		} elseif(empty($data)) {
			self::$errCode = 5806;
			self::$errMsg  = 'There is no data!';
			if(self::$debug === true) {
				self::$errMsg .= 'The SQL is '.ActionModel::$errMsg;
			}
			return $errreturn;
		} else {
			self::$errCode = 1;
			self::$errMsg  = 'success';
			return $data;
		}
	}
}
?>