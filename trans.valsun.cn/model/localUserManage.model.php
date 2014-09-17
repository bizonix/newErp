<?php
/**
 * 管理本地用户信息类
 */
class localUserManageModel {
    public static $errCode = 0;
    public static $errMsg = '';
    private $dbconn = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获得本地缓存的所有用户信息 被删除的除外
     */
    public function getAllUserInfo(){
        $sql = "select * from trans_user where isdelete='0'";
        return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
    
    /*
     * 权限类型 数字 名称映射
     */
    public function powerTypeMapping($powerid){
        switch ($powerid) {
            case 1:     //独立权限
                return '独立';
                break;
            case 2:     //共享权限
                return '共享';
                break;
            default:
                return false;
                break;
        }
    }
    
    /*
     * 根据用户名来搜索用户列表
     */
    public function searchUserByUserName($username){
        $username = mysql_real_escape_string($username);
        $sql = "select * from trans_user where username like '%$username%'";
        $result =  $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        return $result;
    }
    
    /*
     * 从鉴权系统获得该系统的所有数据 并更新到本地
     */
    public function refreshAllUserInfo(){
        $alluserlist = Auth::getAllUserInfo();
        $alluserlist = json_decode($alluserlist, TRUE);
        
        foreach ($alluserlist as $value) {
            $oneuser = $this->getUserInfoById($value['uid']);
            if(empty($oneuser)){    //该用户不存在 则新增到系统
                $userdata = array('uid'=>$value['uid'], 'username'=>$value['userName']);
                $this->addNewUser($userdata);
            }else{  //该用户已经存在  则比较更新时间
                if($value['lastUpdateTime'] > $oneuser['updatetime']){  //该用户信息需要更新
                    $data = array('uid'=>$value['uid'], 'username'=>$value['userName']);
                    $this->updateOneUserInfo($data);
                }
            }
        }
    }
    
    /*
     * 根据某个用户的id获得用户本地用户信息
     * $id 用户id
     */
    public function getUserInfoById($id){
        $sql = "select * from trans_user where uid = $id";
        return $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 新增一条用户信息到系统
     */
    public function addNewUser($userdata){
        $userid = $userdata['uid'];
        $username = mysql_real_escape_string($userdata['username']);
        $powerlist = mysql_real_escape_string(serialize(array()));
        $time = time();
        $sql = "insert into trans_user values ($userid , '$username', '1', '$powerlist', '0', $time)";
        
        $this->dbconn->query($sql);
    }
    
    /*
     * 更新一个用户的信息
     */
    public function updateOneUserInfo($data){
        $uid = $data['uid'];
        $username = mysql_real_escape_string($data['username']);
        $time = time();
        $sql = "update trans_user set username='$username', updatetime=$time where uid=$uid";
        $this->dbconn->query($sql);
    }
    
    /*
     * 更新用户权限信息
     */
    public function updateUserPower($powrelist,$uid){
        $powerlist = mysql_real_escape_string(serialize($powrelist));
        $sql = "update trans_user set powerlist='$powerlist' where uid=$uid";
        $this->dbconn->query($sql);
    }
    
    /*
     *更具id号来删除一个本地权限用户 逻辑删除
     * $uid  用户id
     * 作者 涂兴隆
     */
    public function deleteUserById($uid){
        //$sql = "update trans_user set isdelete='1' where uid=$uid";
        $sql = "delete from trans_user where uid=$uid";
        $this->dbconn->query($sql);
    }
    
    /*
     * 将数据库的id权限数组转换成字符串表示的数组
     * $powerlist 用户权限数组
     */
    public function translatePowerList($powerlist){
        $power = array();
        $groupmodel = new PowerActionGroupModel();
        $powermodel = new powerActionModel();
        foreach ($powerlist as $key => $value) {
            $group = $groupmodel->getGroupInfoById($key);
            $power[$group['groupname']]   = array();
            foreach ($value as $v) {
                $powerinfo = $powermodel->getPowerInfoById($v);
                $power[$group['groupname']][] = $powerinfo['actcode'];
            }
        }
        return $power;
    }
    
}

