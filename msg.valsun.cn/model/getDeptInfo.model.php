<?php
/*
 * 获得部门信息
 */
class GetDeptInfoModel{
    private $dbconn = null;
    public static $errMsg = '';
    public static $errCode = 0;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
    }
    
    /*
     * 根据部门名称获取部门信息 和 公司id
     */
    public function getDepartmentInfoByName($name, $companyId){
        $name   = mysql_real_escape_string($name);
        $dept   = "('eBay客服一部', 'eBay客服二部')";
        $deptNew = "('".$name."')";
        $sql    = "select * from power_dept where dept_name IN {$deptNew} and dept_company_id=$companyId and dept_isdelete=0";
        return $this->dbconn->fetch_first($sql);
    }

    public function getDepartName($name, $companyId){
        $sql    = "select * from power_dept where dept_name = '{$name}' and dept_company_id=$companyId and dept_isdelete=0";
        return $this->dbconn->fetch_first($sql);
    }
    
    public function getDepart($name, $companyId){
        $sql    = "select * from power_dept where dept_name IN {$name} and dept_company_id=$companyId and dept_isdelete=0";
        $query 	= $this->dbconn->query($sql);
        return $this->dbconn->fetch_array($query);
    }
}

?>