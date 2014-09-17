<?php
/*
 * mysql通用方法
 */
class MysqlModel {
    private $dbconn         = null;
    public static $errno    = 0;
    public static $errMsg   = '';
    
    function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     *查询结果集
     *$tabke  表名
     *$field  要查询的字段 eg:array('field1', 'field2', ... .'fieldN')
     *$where  查询条件 eg : where aa=xxx and bb=xxx ...
     *返回值结果数组
     */
    public function queryList($table, $field, $where){
        $returnData = array();                                                          //返回的结果集
        $fieldSql   = implode(',', $field);
        $sql        = "select $fieldSql from $table $where";
        $result     = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if (!empty($result)) {
        	$returnData    = $result;
        }
        return $returnData;
    }
    
    /*
     * 查询一条信息
     * $table 表名
     * $field 查询的字段名列表 eg:array('field1', 'field2', ... .'fieldN')
     * $where  查询条件 eg : where aa=xxx and bb=xxx ...
     * 返回值 返回一条记录信息  没找到则返回空数组
     */
    public function getOne($table, $field, $where){
        $returnData     = array();
        $fieldSql       = implode(', ', $field);
        $sql            = "select $fieldSql from $table $where limit 1";
        $result         = $this->dbconn->fetch_first($sql);
        if (!empty($result)) {
        	$returnData    = $result;
        }
        return $returnData;
    }
    
    /*
     * 更新数据表数据
     * $table  表名
     * $where  查询条件 eg : where aa=xxx and bb=xxx ...
     * $data 更新的数据
     * 返回值  返回受影响的行数
     */
    public function update($table,$data,$where){
        $dataSql    = '';
        $dataAr     = array();
        foreach ($data as $key=>$value){
            $dataAr[]   = "$key='$value'";
        }
        $dataSql    = implode(', ', $dataAr);
        $sql        = "update $table set $dataSql $where ";//echo $sql;exit;
        $this->dbconn->query($sql);
        $effectedRows = $this->dbconn->affected_rows();
        return $effectedRows;
    }
    
    /*
     * 插入一行新记录
     * $table   表名
     * $data    要插入的数据 格式 array('field1'=>xx, 'field1'=>xx, ... 'fieldN'=>xx)
     * 返回值              插入后生成的id
     */
    public function insertNewRecord($table, $data){
        $fields     = array_keys($data);
        $fieldSql   = implode(', ', $fields);
        $values     = implode("', '", $data);
        $values     = "'$values'";
        $sql        = "insert into $table ($fieldSql) values ($values)";
        $this->dbconn->query($sql);
        $newId      = $this->dbconn->insert_id();
        return $newId;
    }
    
    /*
     * 返回一条查询语句的结果集
     * $sql     一条完整的sql语句
     */
    public function getQueryResult($sql){
        return $data       = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
}

?>