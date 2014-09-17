<?php
/*
 * 速卖通平台账号运输方式模板管理
 */
class AliShipTemplateModel {
    public static $errCode  = 0;
    public static $errMsg   = '';
    private $dbconn = NULL;
    private $tplStorPath    = '';
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn   = $dbConn;
        $this->tplStorPath  = WEB_PATH.C('SHIP_TPL');           //模板文件存储目录
    }
    
    /*
     * 获得全面模板信息
     */
    public function getAllTemplateInfoList( $where=''){
        $sql    ="select * from msg_alishiptpl where 1 $where order by account";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 存储模板文件
     * $tmpFile 文件上传后的临时文件
     */
    public function storeTplFile($tmpFile){
        $uniqueId       = uniqid();
        $finalFileName  = $uniqueId.'.zip';
        $fullName       = $this->tplStorPath.$finalFileName;
        $result = move_uploaded_file($tmpFile, $fullName);
        if ($result == FALSE) {                                 //文件存储失败
        	self::$errMsg  = '文件存储失败';
        	return FALSE;
        }
        $result = $this->extractTplZipFile($fullName, $this->tplStorPath.$uniqueId.'/');
        if (FALSE  === $result) {
        	return FALSE;
        }
        return $fullName ;
    }
    
    /*
     * 解压模板zip文件
     * $zipFile     要打开的zip文件目录
     * $directory   加压目录
     */
    private function extractTplZipFile($zipFile, $directory){
        $zipFp  = zip_open($zipFile);
        if (!is_resource($zipFp)) {                              //打开zip文件失败
        	self::$errMsg  = 'zip文件损坏!';
        	return FALSE;
        }
        if(!is_dir($directory)){                                 //目录不存在 则创建之
            if (!mkdir($directory)){
                self::$errMsg   = '创建解压目录失败！';
                return FALSE;
            }
        } else {                                                //目录存在 则删除目录中的全部普通文件
            $dirFp  = opendir($directory);
            while ( FALSE !== ($enties = readdir($dirFp)) ){
                if (is_file($directory.$enties)) {
                	unlink($directory.$enties);
                }
            } 
        }
        //解压文件到文件夹
        while (is_resource($entry = zip_read($zipFp))){
            $content    = '';
            while ($d = zip_entry_read($entry)){
                $content    .= $d;
            }
            $name       = zip_entry_name($entry);
            $name       = strtolower($name);
            file_put_contents($directory.$name, $content, LOCK_EX);
        }
        zip_close($zipFp);
        return true;
    }
    
    /*
     * 存储模板文件和速卖通账号的管理关系
     * $account     账号id
     * $filePath    模板文件存储位置
     * $name        模板文件名称
     */
    public function insertRelationShip($account, $filePath, $name){
        $account    = mysql_real_escape_string($account);
        $filePath   = mysql_real_escape_string($filePath);
        $name       = mysql_real_escape_string($name);
        $sql        = "insert into msg_alishiptpl (`account`, `name`, `filepath`) values ('$account', '$name', '$filePath')";
        $result     = $this->dbconn->query($sql);
        return $result;
    }
    
    /*
     * 删除一个模板
     */
    public function deleteTemplate($pid){
        $sql    = 'delete from msg_alishiptpl where id='.$pid;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获取某个账号下面的模板列表
     */
    public function getTplListByAccount($account){
        $account    = mysql_real_escape_string($account);
        $sql        = 'select * from msg_alishiptpl where account='."'$account'";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 获得某个账号的设置的管理模板
     */
    public function getTplRow($account){
        $account   = mysql_real_escape_string($account);
        $sql    = 'select * from msg_alishipallocation where account='."'$account'";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 设置某个账号的模板
     */
    public function setAccountsTpl($account, $tplId){
        $rowinfo    = $this->getAccountSetInfo($account);
        if (empty($rowinfo)) {                                                  //未被设置过 则插入新的数据
        	$sql   = 'insert into msg_alishipallocation (account, tplid) values '."('$account', '$tplId')";
        	return $this->dbconn->query($sql);
        } else {
            $sql    = 'update msg_alishipallocation set tplid='.$tplId.' where account='."'$account'";
            return $this->dbconn->query($sql);
        }
    }
    
    /*
     * 某个模板是否存在
     */
    public function isTplExists($id){
        $sql    = 'select id from msg_alishiptpl where id='.$id;
        $row    = $this->dbconn->fetch_first($sql);
        if (empty($row)) {
        	return FALSE;
        } else {
            return true;
        }
    }
    
    /*
     * 获取某个账号的模板设置信息
     */
    public function getAccountSetInfo($account){
        $account    = mysql_real_escape_string($account);
        $sql        = 'select * from msg_alishipallocation where account='."'$account'";//echo $sql;exit;
        $rowinfo    = $this->dbconn->fetch_first($sql);
        return $rowinfo;
    }
    
    /*
     * 根据id获取模板信息
     */
    public function getTplInfoById($id){
        $sql    = 'select * from msg_alishiptpl where id='.$id;
        return $this->dbconn->fetch_first($sql);
    }
}





