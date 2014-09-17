<?php
/**
*类名：Company
*功能：管理公司信息
*时间：2013-07-10
*版本：V1.0
*作者：冯赛明
*
*/
class Company{
	public  static $dbConn;
	private static $table_power_company = 'power_company';//公司管理表	
	private static $_instance;
	static $errCode='0';
	static $errMsg='';	

	public function __construct()
	{
	}

	public static function initDB(){
		global $dbConn;
		self::$dbConn =	$dbConn;
	}
	
	//单实例
    public static function getInstance(){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	/*
	*方法功能：系统用户数据
	*/
	public static function getCompany($filed='*',$where=' 1 ',$order=' ',$limit=' ')
	{
		self::initDB();
		$sql='select '.$filed.' from `'.self::$table_power_company.'` where '.$where.' '.$order.' '.$limit;
		
		$result=self::$dbConn->query($sql);
		
		if($result)
		{
			$data_result = array();
			while($data=self::$dbConn->fetch_assoc($result))
			{	
				$data_result[]=$data;
			}
			if($data_result)
			{	
				return $data_result;
			}			
		}		
		self::$errCode = '1901';
		self::$errMsg  = "No data or getCompany error";
		return false;				
	}
	
	/*
	* 方法功能：修改公司信息
	* 说明：传递过来的$data_array必须是一维关联数组，例如：array('key'=>'value'),
	* 其中key是要修改的表的字段名称,value是要给字段重新赋的值
	*/
	public static function updateCompany($data_array,$where='')
	{
		self::initDB();
		if(is_array($data_array))//判断是否是数组
		{
			$setting='';			
			foreach($data_array as $key=>$value)
			{
				//把数组格式转换为字符串格式(例如：`dept_name`='it',`dept_principal`='admin')
				$setting.=','.$key.'=\''.($value).'\''; 				
			}
			if(!empty($setting))
			{   
				$setting=ltrim($setting,',');//去除最后一个逗号
				$sql='UPDATE `'.self::$table_power_company.'` SET '.$setting.' where '.$where;
				$result=self::$dbConn->query($sql);
				if($result)
				{					
					return true;
				}				
			}			
		}else
		{
			self::$errCode = '1902';
			self::$errMsg  = "Update company error";
			return false;
		}
	}	
	
	/*
	*功能：新增公司信息
	*说明：传递过来的$data_array必须是一维关联数组，例如：array('key'=>'value'),
	*其中key是要插入的表的字段名称,value是要给字段赋的值；
	*/
	public static function addCompany($data_array)
	{
		self::initDB();
		if(is_array($data_array))//判断是否是数组
		{			
			$field='';//要插入的字段
			$vaules='';//要插入的值
			foreach($data_array as $key=>$v)
			{				
				//把数组内容转换为字符串格式，例如：`dept_name`='it',`dept_principal`='admin'
				$field.=',`'.$key.'`'; 				
				$values.=',\''.addslashes($v).'\'';		
			}
			if(!empty($field) && !empty($values))
			{	
				$field=ltrim($field,',');//去除最后一个逗号
				$values=ltrim($values,',');//去除最后一个逗号
				$sql='INSERT INTO `'.self::$table_power_company.'`('.$field.')  VALUES('.$values.')';
				
				$result=self::$dbConn->query($sql);
				if($result)
				{					
					return true;//新增成功
				}				
			}			
		}else
		{
			self::$errCode = '1903';
			self::$errMsg  = "Add User error";
			return false;
		}
	}
	
	/*
	*功能：删除公司信息
	*/
	public static function deleteCompany($where='')
	{		
		$data_array=array('company_isdelete'=>'1');
		if(self::updateCompany($data_array,$where))
		{
			return true;
		}else
		{
			self::$errCode = '1904';
			self::$errMsg  = "Delete company error";
			return false;
		}
	}
	
	/*
	*功能：获取公司名称
	*/
	public static function getCompanyName($where=' 1 ')
	{		
		self::initDB();
		$sql='select `company_id`,`company_name` from `'.self::$table_power_company.'` where '.$where;  
		$result=self::$dbConn->query($sql);		
		if($result)
		{
			$data_result = array();
			while($data=self::$dbConn->fetch_assoc($result))
			{	
				$data_result[$data['company_id']]=$data['company_name'];
			}
			if($data_result)
			{	
				return $data_result;
			}			
		}	
		else
		{
			self::$errCode = '1905';
			self::$errMsg  = "No data or getCompanyName error";
			return false;
		}
	}
}
?>