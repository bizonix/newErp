<?php
/*
*功能：管理ActionGroup
*作者：冯赛明
*/
require_once(WEB_PATH.'model/actiongroup.model.php');
class ActionGroupAct
{
	static $errCode='0';
	static $errMsg ='';
	
	public function __construct()
	{		
	}	
	
	/*
	*方法功能：查询操作权限组信息
	*/
	public static function act_showActionGroup($filed='*',$where=' 1 ',$order='',$limit='')
	{		
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5101';
			self::$errMsg ='You have no access to show actionGroup';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=ActionGroup::showActionGroup($filed,$where,$order,$limit);
		if(!$data)
		{
			self::$errCode = '5102';
			self::$errMsg  = 'No data or show actionGroup error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*功能：获取Action信息
	*/
	public static function act_getActionGroupName($where=' 1 ')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5103';
			self::$errMsg ='You have no access to get actionGroup name';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=ActionGroup::getActionGroupName($where);
		if(!$data)
		{
			self::$errCode = '5104';
			self::$errMsg  = 'No data or get actionGroup name error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*方法功能：新增操作权限组信息
	*说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	*其中key是要插入的表的字段名称,value是要给字段赋的值；
	*/
	public static function act_addActionGroup($data_array)
	{
	    if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5105';
			self::$errMsg ='You have no access to add actionGroup';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=ActionGroup::addActionGroup($data_array);
		if(!$data)
		{
			self::$errCode = '5106';
			self::$errMsg  = 'Add actionGroup error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	* 方法功能：修改操作权限组信息
	* 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	* 其中key是要修改的表的字段名称,value是要给字段重新赋的值；$where参数是条件
	*/
	public static function act_updateActionGroup($data_array,$where)
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5107';
			self::$errMsg ='You have no access to update actionGroup';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=ActionGroup::updateActionGroup($data_array,$where);
		if(!$data)
		{
			self::$errCode = '5108';
			self::$errMsg  = 'Update actionGroup error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*方法功能：删除操作权限组信息
	*/
	public static function act_deleteActionGroup($where='')
	{		
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5109';
			self::$errMsg ='You have no access to delete actionGroup';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=ActionGroup::deleteActionGroup($where);
		if(!$data)
		{
			self::$errCode = '5110';
			self::$errMsg  = 'Delete actionGroup error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}	
}
?>