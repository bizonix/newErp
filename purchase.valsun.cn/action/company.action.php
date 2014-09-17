<?php
/**
*类名：CompanyAct
*功能：管理公司信息
*时间：2013-07-10
*版本：V1.0
*作者：冯赛明
*
*/
require_once(WEB_PATH.'model/company.model.php');
class CompanyAct
{
	static $errCode='0';
	static $errMsg ='';
	
	public function __construct()
	{		
	}
	
	//功能：获取公司信息
	public static function act_getCompany($filed=' * ',$where=' 1 ',$order=' ',$limit=' ')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有该操作权限
		{
			self::$errCode='5901';
			self::$errMsg ='You have no access to get company info';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Company::getCompany($filed,$where,$order,$limit);
		if(!$data)
		{
			self::$errCode=Company::$errCode;
			self::$errMsg =Company::$errMsg;
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	//功能：新增公司信息
	public static function act_addCompany($data_array)
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5902';
			self::$errMsg ='You have no access to add Company';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Company::addCompany($data_array);
		if(!$data){
			self::$errCode=Company::$errCode;
			self::$errMsg =Company::$errMsg;
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}	
		
	/*
	* 功能：修改公司信息
	* 说明：传递过来的$data_array必须是关联数组，例如：array('key'=>'value'),
	* 其中key是要修改的表的字段名称,value是要给字段重新赋的值；$where参数是条件
	*/
	public static function act_updateCompany($data_array,$where='')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5903';
			self::$errMsg ='You have no access to update Company';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Company::updateCompany($data_array,$where);
		if(!$data)
		{
			self::$errCode=Company::$errCode;
			self::$errMsg =Company::$errMsg;
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	* 功能：删除公司信息
	* 说明：这里的删除只是逻辑的删除
	*/
	public static function act_deleteCompany($where='')
	{
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5904';
			self::$errMsg ='You have no access to delete Company';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Company::deleteCompany($where);
		if(!$data)
		{
			self::$errCode=Company::$errCode;
			self::$errMsg =Company::$errMsg;
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return true;
	}	
	
	/*
	*功能：获取公司名称
	*/
	public static function act_getCompanyName($where=' 1 ')
	{		
		if(!Auth::checkAccess(__CLASS__,__FUNCTION__))//判断Power系统用户是否有权限
		{
			self::$errCode='5905';
			self::$errMsg ='You have no access to get Company name';
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		$data=Company::getCompanyName($where);
		if(!$data)
		{
			self::$errCode=Company::$errCode;
			self::$errMsg =Company::$errMsg;
			echo json_encode(array('errCode'=>self::$errCode,'errMsg'=>self::$errMsg));
			return false;
		}
		return $data;
	}
	
	/*
	*功能：外接系统获取公司信息
	*/
	public function act_getApiCompany()
	{
		$where=' company_isdelete="0" ';
		$filed=' `company_id` as companyId,`company_name` as companyName,`company_principal` as companyPrincipal,`company_address` as companyAddress,`company_phone` as companyPhone ';		
		$result=Company::getCompany($filed,$where);
		if(!$result)
		{
			self::$errCode = '5906';
			self::$errMsg  = 'No data or get api company error';
			return false;
		}
		return $result;
	}
}
?>