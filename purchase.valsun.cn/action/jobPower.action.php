<?php
/*
*功能：管理岗位权限
*作者：冯赛明
*/
require_once(WEB_PATH.'model/jobpower.model.php');
class JobPowerAct
{		
	static $errCode	=	'0';
	static $errMsg	=	"";

	/*
	*功能：外接系统获取单个岗位权限信息
	*/
	public function act_getJobPower()
	{
		$systemName=$_REQUEST['systemName'];
		$jobPowerId=$_REQUEST['jobPowerId'];		
		$data=System::showSystem('system_id','system_name="'.$systemName.'"');
		$systemId=$data[0]['system_id'];
		$where='jobpower_id='.$jobPowerId.' AND jobpower_system_id='.$systemId ;
		$result=Jobpower::showJobpower('*',$where);
		if(!$result)
		{
			self::$errCode = '5401';
			self::$errMsg  = 'No data or get JobPower error';
			return false;
		}
		return $result;
	}
	
	/*
	*功能：外接系统获取其系统内所有岗位权限信息
	*/
	public function act_getAllJobPower()
	{
		$systemName=$_REQUEST['systemName'];
		$data=System::showSystem('system_id','system_name="'.$systemName.'"');
		$systemId=$data[0]['system_id'];
		$where=' user_system_id='.$systemId ;
		$result=Jobpower::showJobpower('*',$where);
		if(!$result)
		{
			self::$errCode = '5402';
			self::$errMsg  = 'No data or get all the JobPower error';			
			return false;
		}
		//print_r($result);
		return $result;
	}
	
	/*
	*功能：查询岗位权限信息
	*/
	public static function act_showJobpower($filed='*',$where='1',$order=' ',$limit=' ')
	{		
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5403';
			self::$errMsg ='You have no access to show jobpower';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::showJobpower($filed,$where,$order,$limit);
		if(!$data)
		{
			self::$errCode = '5404';
			self::$errMsg  = 'No data or show jobpower error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	
	/*
	*功能：获取所有系统岗位权限信息
	*/
	public static function act_getAllSystemJobPower($where='1')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5405';
			self::$errMsg ='You have no access to get all system job power';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::getJobpower($where);
		if(!$data)
		{
			self::$errCode = '5406';
			self::$errMsg  = 'No data or get all system JobPower error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*功能：增加岗位权限信息
	*/
	public static function act_addJobPower($data_array)
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5407';
			self::$errMsg ='You have no access to add JobPower';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::addJobpower($data_array);
		if(!$data)
		{
			self::$errCode = '5408';
			self::$errMsg  = 'No data or add JobPower error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*功能：修改岗位权限信息
	*/
	public static function act_updateJobPower($data_array,$where)
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5409';
			self::$errMsg ='You have no access to update JobPower';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::updateJobpower($data_array,$where);
		if(!$data)
		{
			self::$errCode = '5410';
			self::$errMsg  = 'No data or update JobPower error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*功能：修改岗位权限信息
	*/
	public static function act_deleteJobpower($where='')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5411';
			self::$errMsg ='You have no access to delete JobPower';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::deleteJobpower($where);
		if(!$data)
		{
			self::$errCode = '5412';
			self::$errMsg  = 'Delete JobPower error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*功能：改变单个岗位权限信息
	*/
	public function act_setJobPower()
	{
		$jobpowerId=$_REQUEST['jobpowerId'];
		$systemName=$_REQUEST['systemName'];
		$data=System::showSystem('system_id','system_name="'.$systemName.'"');
		$systemId=$data[0]['system_id'];		
		$where=' jobpower_id='.$jobpowerId.' AND jobpower_system_id='.$systemId ;

		$newJobpower=array('jobpower_power'=>$_REQUEST['newJobpower']);

		$result=Jobpower::updateJobpower($newJobpower,$where);
		if($result)
		{
			return array("Result"=>"True");
		}
		else
		{
			self::$errCode = '5413';
			self::$errMsg  = 'Set JobPower error';			
			return false;
		}		
	}	
	
	/*
	*功能：获取岗位权限对应岗位名称
	*/
	public static function act_getJobPowerNames($where=' 1 ')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5414';
			self::$errMsg ='You have no access to get JobPower names';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Jobpower::getJobPowerNames($where);
		if(!$data)
		{
			self::$errCode = '5415';
			self::$errMsg  = 'No data or get JobPower names error';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
}
?>