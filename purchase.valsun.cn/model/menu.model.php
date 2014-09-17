<?php
/**
*类名：Menu
*功能：菜单管理管理
*版本：2013-05-10
*作者：冯赛明
*
*/
class Menu{
	public static $dbConn;
	private static $table_name='power_menu';//岗位权限表
	static $errCode = '0';
	static $errMsg  = "";
	
	public function __construct()
	{			
	}	

	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	/*
	*方法功能：查询菜单信息
	*/
	public static function getMenus($filed=' * ',$where=' menu_isdelete="0" ',$order='   ',$limit=' ')
	{		
		self::initDB();
		$sql='select '.$filed.' from `'.self::$table_name.'` where '.$where.' '.$order.' '.$limit;
		//echo $sql;
		$result=self::$dbConn->query($sql);
		if(!empty($result))
		{
			$data_result = array();
			while($data=self::$dbConn->fetch_assoc($result))
			{		
				$data_result[]=$data;
			}
			//print_r($data_result);
			return $data_result;
		}else
		{
			self::$errCode = '1501';
			self::$errMsg  = "Menu getMenus error";
			return false;
		}			
	}
	
	/*
	*方法功能：新增菜单信息
	*说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	*其中key是要插入的表的字段名称,value是要给字段赋的值；
	*/
	public static function addMenu($data_array)
	{
 		self::initDB();
		if(is_array($data_array))//判断是否是数组
		{
			$field='';//要插入的字段
			$vaules='';//要插入的值
			foreach($data_array as $key=>$v)
			{
				//把数组内容转换为字符串格式，例如：`dept_name`='it',`dept_principal`='admin'
				$field.=' `'.$key.'`,'; 
				
				$values.=' \''.$v.'\',';
			}
			if(!empty($field) && !empty($values))
			{
				$field=substr($field,0,strlen($field)-1);//去除最后一个逗号
				$values=substr($values,0,strlen($values)-1);//去除最后一个逗号
				$sql='INSERT INTO `'.self::$table_name.'`('.$field.')  VALUES('.$values.')';
				//echo '<br/>'.$sql.'<br/>';
				$result=self::$dbConn->query($sql);
				if($result)
				{
					//echo '新增成功'.'<br/>';
					return true;//新增成功
				}				
			}			
		}else
		{
			self::$errCode = '1502';
			self::$errMsg  = "Menu addMenu error";
			return false;
		}	
	}
	
	/*
	* 方法功能：修改菜单信息
	* 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	* 其中key是要修改的表的字段名称,value是要给字段重新赋的值；$where参数是条件
	*/
	public static function updateMenu($data_array,$where)
	{
		self::initDB();
		if(is_array($data_array))//判断是否是数组
		{
			$setting='';
			foreach($data_array as $key=>$value)
			{
				$setting.=',`'.$key.'`="'.$value.'"'; //把数组内容转换为字符串格式，例如：`dept_name`='it',`dept_principal`='admin'
			}
			if(!empty($setting))
			{
				//$setting=substr($setting,0,strlen($setting)-1);//去除最后一个逗号
				$setting=ltrim($setting,',');//去除最左边的逗号
				$sql='UPDATE `'.self::$table_name.'` SET '.$setting.' where '.$where;
				$result=self::$dbConn->query($sql);
				if($result)
				{
					return true;//修改成功
				}				
			}			
		}else
		{
			self::$errCode = '1503';
			self::$errMsg  = "Menu updateMenu error";
			return false;
		}	
	}
	
	/*
	*方法功能：删除菜单信息
	*/
	public static function deleteMenu($where='')
	{
		$data_array=array('menu_isdelete'=>'1');
		if(self::updateMenu($data_array,$where))
		{
			return true;
		}else
		{
			self::$errCode = '1504';
			self::$errMsg  = "Menu deleteMenu error";
			return false;
		}
	}
}
?>
	