<?php
/*
 *权限model类 
 */

class powerActionModel {
    public $errorCode = 0;
    public $errorMsg = '';
    private $dbconn;
    
    /*
     * 构造函数
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获得系统所有的power列表
     * $limit 输出offset
     */
    public function getPowerList($where){
        $sql = "select p.id, p.actnamezh, pg.groupname, p.actcode, p.lastupdatetime from trans_power_actiongroup as pg join trans_power_actions as p on pg.id=p.gid where pg.isdelete='0' and p.isdelete='0' ".$where;
        //echo $sql;exit;
        $qre = mysql_query($sql);
        return $this->dbconn->fetch_array_all($qre);
    }
    
    /*
     * 获得有效的power总数
     */
    public function getPowerCount(){
        $sql = "select count(*) as num from trans_power_actiongroup as pg join trans_power_actions as p on pg.id=p.gid where pg.isdelete='0' and p.isdelete='0'";
        //echo $sql;exit;
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 验证指定组的某个code是否存在
     * $gid 组id $code 权限码
     */
    public function checkCodeExist($gid, $code){
        $code = mysql_real_escape_string($code);
        $sql = "select count(*) as num from trans_power_actions where gid=$gid and actcode= binary '$code'";
        $row = $this->dbconn->fetch_first($sql);
        if($row['num']>0){  //存在
            return TRUE;
        }  else {   //不存在
            return FALSE;
        }
    }
    
    /*
     * 添加新权限到系统
     */
    public function addNewPower($gid, $powername, $desc){
        $powername = mysql_real_escape_string($powername);
        $desc = mysql_real_escape_string($desc);
        $time = time();
        $sql = "insert into trans_power_actions values (null, $gid, '$powername', '$desc','0',$time)";
        $this->dbconn->query($sql);
    }
    
    /*
     * 根据id获得power的信息
     */
    public function getPowerInfoById($id){
        $sql = "select * from trans_power_actions where id=$id";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 更新power信息
     */
    public function updatePower($pid, $gid, $powername, $powerdesc){
        $powername = mysql_real_escape_string($powername);
        $desc = mysql_real_escape_string($powerdesc);
        $time = time();
        $sql = "update trans_power_actions set gid=$gid, actcode='$powername',actnamezh='$desc' ,lastupdatetime=$time where id=$pid";
        return $this->dbconn->query($sql);
    }
    
    /*
     * 搜索权限
     * $gid 所属组id $keywords关键字
     */
    public function searchPower($gid, $keywords){
        $keywords = mysql_real_escape_string($keywords);
        $sql = "select p.* , pg.groupname from trans_power_actiongroup as pg join trans_power_actions as p on pg.id=p.gid where p.actcode like '%$keywords%' and p.gid=$gid and p.isdelete='0' and pg.isdelete='0' ";
        
        $r = mysql_query($sql);
        return $this->dbconn->fetch_array_all($r);
    }
    
    /*
     * 删除权限
     */
    public function deletePower($pid){
        $time = time();
        $sql = "update trans_power_actions set isdelete='1' where id=$pid";
        $this->dbconn->query($sql);
    }
}

