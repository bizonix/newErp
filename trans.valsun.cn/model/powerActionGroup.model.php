<?php
/**
 * trans_power_actiongroup 表model
 */
class PowerActionGroupModel {
    public static $errCode = 0;
    public static $errMsg = '';
    private $dbconn = null;
    
    /*
     * 构造函数 初始化数据库连接
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 验证指定的组名在数据库中是否存在
     * 参数 $gname 组名
     */
    public function checkGroupNameExists($gname){
        $sql = "select groupname from trans_power_actiongroup where groupname = binary '$gname' ";
        //echo $sql;exit;
        $result = $this->dbconn->fetch_first($sql);
        if($result){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 增加一个组
     * $dataar 数据信息数组
     */
    public function addNewGroup($dataar){
        $groupname = mysql_real_escape_string($dataar['groupname']);
        $groupnamezh = mysql_real_escape_string($dataar['groupnamezh']);
        $time = time();
        //var_dump($dataar);exit;
        $sql = "insert into trans_power_actiongroup values (null, '$groupname', '$groupnamezh', '0', $time)";
        //echo $sql;exit;
        //return $this->dbconn->query($sql);
        return mysql_query($sql);
    }
    
    /*
     * 获取权限组的总数量 <不包含已经删除的>
     */
    public function getCountNum(){
        $sql = "select count(*) as num from trans_power_actiongroup where isdelete='0'";
        $row = $this->dbconn->fetch_first($sql);
        return $row['num'];
    }
    
    /*
     * 获得group的信息
     * $where 为条件语句
     */
    public function getGropList($where=''){
        $sql = "select * from trans_power_actiongroup $where";
        return $this->dbconn->fetch_array_all(mysql_query($sql));
    }
    
    /*
     * 根据gid获得组信息
     * $gid   id号
     */
    public function getGroupInfoById($gid){
        $sql = "select * from trans_power_actiongroup where id=$gid and isdelete='0'";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 更新组信息
     */
    public function updateGroupInfo($data, $gid){
        $time = time();
        $gname = mysql_real_escape_string($data['groupname']);
        $gnamezh = mysql_real_escape_string($data['groupnamezh']);
        $sql = "update trans_power_actiongroup set groupname='$gname', groupnamezh='$gnamezh' ,lastupdatetime=$time where id=$gid ";
        $this->dbconn->query($sql);
    }
    
    /*
     * 删除一个分组
     */
    public function deletGroup($gid){
       $sql = "update trans_power_actiongroup set isdelete='1' where id=$gid" ;
       //echo $sql;exit;
       $this->dbconn->query($sql);
    }
    
    /*
     * 搜索权限组
     * $keywords string 搜索关键字
     */
    public function searchGroup($keywords){
        $sql = "select * from trans_power_actiongroup where groupname like '%$keywords%' and isdelete='0'";
        $qre = mysql_query($sql);
        return $this->dbconn->fetch_array_all($qre);
    }
    
    /*
     * 获得全部的权限组列表
     */
    public function getAllPowerGroupList(){
        $sql = "select * from trans_power_actiongroup where isdelete = '0'";
        $qre = mysql_query($sql);
        return $this->dbconn->fetch_array_all($qre);
    }
}

