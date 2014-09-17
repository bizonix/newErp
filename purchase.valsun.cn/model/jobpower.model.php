<?php
/**
*类名：Jobpower
*功能：岗位权限管理
*开发时间：2013-05-10
*作者：冯赛明
*
*/

class Jobpower{
	public static $dbConn;
	private static $power_jobpower='power_jobpower';//岗位权限表
	private static $power_job='power_job';
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
	*方法功能：查询岗位权限信息
	*/
	public static function showJobpower($filed=' * ',$where=' 1 ',$order='  ',$limit=' ')
	{		
		self::initDB();
		$sql='select '.$filed.' from `'.self::$power_jobpower.'` where '.$where.' '.$order.' '.$limit;
		//echo $sql;
		$result=self::$dbConn->query($sql);
		if(!empty($result))
		{
			$data_result = array();
			while($data=self::$dbConn->fetch_assoc($result))
			{		
				$data_result[]=$data;
			}
			return $data_result;
		}else
		{
			self::$errCode = '1401';
			self::$errMsg  = "Jobpower showJobpower error";
			return false;
		}		
	}
	
	/*
	*方法功能：
	*/
	public static function getJobpower($where='1')
	{		
		self::initDB();
		$sql='select `jobpower_id`,`jobpower_job_id` from `'.self::$power_jobpower.'` where'.$where;
		//echo $sql;
		$result=self::$dbConn->query($sql);
		if(!empty($result))
		{
			$data_result = array();
			while($data=self::$dbConn->fetch_assoc($result))
			{		
				$data_result[$data['jobpower_id']]=$data['jobpower_job_id'];
			}
			return $data_result;
		}else
		{
			self::$errCode = '1402';
			self::$errMsg  = "Jobpower getJobpower error";
			return false;
		}		
	}  
	
	/*
	*方法功能：新增岗位权限信息
	*说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	*其中key是要插入的表的字段名称,value是要给字段赋的值；
	*/
	public static function addJobpower($data_array)
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
				
				$values.=' \''.($v).'\',';
			}
			if(!empty($field) && !empty($values))
			{
				$field=substr($field,0,strlen($field)-1);//去除最后一个逗号
				$values=substr($values,0,strlen($values)-1);//去除最后一个逗号
				$sql='INSERT INTO `'.self::$power_jobpower.'`('.$field.')  VALUES('.$values.')';
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
			self::$errCode = '1403';
			self::$errMsg  = "Jobpower addJobpower error";
			return false;
		}	
	}
	
	/*
	* 方法功能：修改岗位权限表的信息
	* 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	* 其中key是要修改的表的字段名称,value是要给字段重新赋的值；$where参数是条件
	*/
	public static function updateJobpower($data_array,$where)
	{
		self::initDB();
		if(is_array($data_array))//判断是否是数组
		{
			$setting='';
			foreach($data_array as $key=>$value)
			{
				//把数组内容转换为字符串格式，例如：`dept_name`='it',`dept_principal`='admin'
				$setting.=' `'.$key.'`=\''.($value).'\','; 
			}
			if(!empty($setting))
			{
				$setting=substr($setting,0,strlen($setting)-1);//去除最后一个逗号
				$sql='UPDATE `'.self::$power_jobpower.'` SET '.$setting.' where '.$where;
				//echo $sql;
				$result=self::$dbConn->query($sql);
				if($result)
				{
					return true;//修改成功
				}				
			}			
		}else
		{
			self::$errCode = '1404';
			self::$errMsg  = "Jobpower updateJobpower error";
			return false;
		}	
	}
	
	/*
	*方法功能：删除岗位权限信息
	*/
	public static function deleteJobpower($where='')
	{		
		$data_array=array('jobpower_isdelete'=>'1');
		if(self::updateJobpower($data_array,$where))
		{
			return true;
		}else
		{
			self::$errCode = '1405';
			self::$errMsg  = "Jobpower deleteJobpower error";
			return false;
		}	
	}
	
	 /*
	*获取岗位名称
	*/
	public static function getJobPowerNames($where='1')
	{
		self::initDB();
		$sql='select power_job.job_name,power_job.job_dept_id,power_job.job_company_id,power_jobpower.jobpower_id from `'.self::$power_job.'` as power_job left join '.self::$power_jobpower.' as power_jobpower on power_job.job_id=power_jobpower.jobpower_job_id where '.$where;
		//echo $sql;
		if($result=self::$dbConn->query($sql))
		{
			$rt=array();
			while($data=self::$dbConn->fetch_assoc($result))
			{
				$rt=array($data['jobpower_id'],$data['job_company_id'],$data['job_name'],$data['job_dept_id']);
			}
			return $rt;
		}
		self::$errCode = '1406';
		self::$errMsg  = "Get Job Names error";
		return false;
	}
}
?>
	