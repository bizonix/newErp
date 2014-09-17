<?php
/*
 * 公共数据库操作类
 */
class CommonModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbConn         = NULL;
    private $table          = NULL;
    public  $lastSql        = '';                                                   //最近一次执行的sql
    
    /*
     * 构造函数
     */
    function __construct($tableName) {
        global $dbConn;
        $this->dbConn   = $dbConn;
        $this->table    = $tableName;
    }
    
    /*
     * 获得当前操作的表名
     */
    public function getCurTbname(){
        return $this->table;
    }
    
    /*
     * 重新设置表名
     */
    public function reSetTbName($tbName){
        $this->table    = $tbName;
    }
    
    /*
     * 插入新行   成功返回新的自增ID 失败返回false
     */
    public function insertNewRecord($data){
        $data       = self::transSafetySql($data);
        $keys       = array_keys($data);
        $feildSql   = implode(",", $keys);
        $dataSql    = implode("', '", $data);
        $table      = $this->table;
        $sql        = "insert into $table ( $feildSql ) values ('$dataSql')";
        $this->lastSql  = $sql;
        $query      = $this->dbConn->query($sql);
        if ($query) {
        	return $this->dbConn->insert_id();
        } else {
            return FALSE;
        }
    }
    
    /*
     * 更新数据 成功 返回受影响的行数 失败返回false
     */
    public function updateData($data, $where=''){
        $data   = self::transSafetySql($data);
        $sqlAe  = array();
        foreach ($data as $key=>$val){
            $sqlAe[]  = "$key='$val'";
        }
        $table  = $this->table;
        $dataSql    = implode(", ", $sqlAe);
        $updateSql  = "update $table set $dataSql $where";
        $this->lastSql  = $updateSql;
        $query          = $this->dbConn->query($updateSql);
        if ($query) {
        	return  $this->dbConn->affected_rows();
        } else {
            return FALSE;
        }
    }
    
    /*
     * 过滤sql特殊字符
     */
    public static function transSafetySql($data){
        foreach ($data as $key => $val){
            $data[$key] = mysql_real_escape_string($val);
        }
        return $data;
    }
    
    /*
     * 查询一条数据
     */
    public function findOne($fields, $where){
        if (is_array($fields)) {
        	$fieldSql  = implode(", ", $fields);
        } else {
            $fieldSql   = ' * ';
        }
        $table  = $this->table;
        $sql    = "select $fieldSql from $table $where";
        return $this->dbConn->fetch_first($sql);
    }
    
    /*
     * 查询结果集
     */
    public function findAll($fields, $where){
        if (is_array($fields)) {
            $fieldSql  = implode(", ", $fields);
        } else {
            $fieldSql   = ' * ';
        }
        $table  = $this->table;
        $sql    = "select $fieldSql from $table $where";
        return $this->dbConn->fetch_array_all($this->dbConn->query($sql));
    }
}

?>