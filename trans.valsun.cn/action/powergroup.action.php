<?php
/**
 * 管理权限组的action类
 */
class PowergroupAct {
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 验证数据正确性
     */
    public function act_groupValidateUnique(){
        $groupname = isset($_GET['groupname']) ? trim($_GET['groupname']) : '';
        if(empty($groupname)){
            self::$errCode = 0;
            self::$errMsg = '不能为空';
            return ;
        }
        
        if(strlen($groupname) > 30){    //不能超过30个字符
            self::$errCode = 0;
            self::$errMsg = '不能超过30个字符!';
            return ;
        }
        $groupmodel = new PowerActionGroupModel();
        $isexist = $groupmodel->checkGroupNameExists($groupname);    //检测用户名是否存在
        
        if($isexist){
            self::$errCode = 0;
            self::$errMsg = '名称已存在!';
            return ;
        }else{
            self::$errCode = 1;
            self::$errMsg = 'OK!';
            return ;
        }
    }
    
    /*
     * 删除某个权限分组
     */
    
    public function act_deletePowerGroup(){
        $gid = isset($_GET['gid']) ? abs(intval($_GET['gid'])) : 0;
        if(!$gid){  //未指定id
            self::$errCode = 0;
            self::$errMsg = '未指定分组';
            return;
        }
        $groupmodel = new PowerActionGroupModel();
        $groupmodel->deletGroup($gid);
        
        self::$errCode = 1;
        self::$errMsg = '删除成功';
        return;
    }
    
}

