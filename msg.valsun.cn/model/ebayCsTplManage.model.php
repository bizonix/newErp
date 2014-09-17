<?php
/*
 * ebay售后邮件推送管理
 */
class EbayCsTplManageModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn         = NULL;
    
    function __construct() {
        global $dbConn;
        $this->dbConn   = $dbConn;
    }
    
    /*
     * 获得可用的模板列表
     */
    public function getTplList(){
        $sql    = "select * from msg_ebaycstpl where is_delete=0";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 获得一个与某个模板相关的国家
     */
    public function getRelCountryByTplId($tplId){
        $sql    = "select * from msg_ebaycsrel where tplId='$tplId'";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 获得可选的国家简称列表
     */
    public function getAllowedCountry(){
        $sql    = "select * from msg_ebaycsrel where tplId=0";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
    
    /*
     * 检查一个国家代码是否存在
     */
    public function checkCountryCodeExists($code){
        $sql    = "select * from msg_ebaycsrel where countryCode='$code' ";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 简称当前国家是可以设置模板
     */
    public function checkAllowSet($code){
        $sql    = "select * from msg_ebaycsrel where countryCode='$code' ";
        $row    = $this->dbConn->fetch_first($sql);
        if ($row) {
        	if ($row['tplId']!=0) {
        		return FALSE;
        	} else {
        	    return TRUE;
        	}
        } else {
            return FALSE;
        }
    }
}

?>