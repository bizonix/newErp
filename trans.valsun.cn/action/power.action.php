<?php

class PowerAct {
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 确定权限code是否组内唯一
     */
    public function act_powerValidateUnique(){  
        $gid = isset($_GET['gid']) ? abs(intval($_GET['gid'])) : 0;
        $powercode = isset($_GET['code']) ? trim($_GET['code']) : 0;
        if(empty($gid)){    //未指定所属组
            self::$errCode = 0;
            self::$errMsg = '未指定所属组！';
            return;;
        }
        
        if(empty($powercode)){  //没有指定权限代码
            self::$errCode = 0;
            self::$errMsg = '未指定代码！';
            return;
        }
        
        $groupmode = new PowerActionGroupModel();
        $row = $groupmode->getGroupInfoById($gid);
        if(empty($row)){    //组id不正确
            self::$errCode = 0;
            self::$errMsg = '指定组不存在！';
            return;
        }
        
        $powermodel = new powerActionModel();
        $isexist = $powermodel->checkCodeExist($gid, $powercode);
        if($isexist){
            self::$errCode = 0;
            self::$errMsg = '改代码已使用，请重填！';
            return;
        } else {
            self::$errCode = 1;
            self::$errMsg = 'OK！';
            return;
        }
    }
    
    /*
     * 删除权限
     */
    public function act_deletePower(){
        $pid = isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 0;
        //echo $pid;exit;
        if(!$pid){
            self::$errCode = 0;
            self::$errMsg = '请指定要删除的权限！';
            return;
        }
        $powermodel = new powerActionModel();
        $powermodel->deletePower($pid);
        self::$errCode = 1;
        self::$errMsg = '删除完成！';
        return;
    }
    
    /*
     *更新系统所有用户信息大本地数据库
     */
    public function act_updateAllUsers(){
        $usermanager = new localUserManageModel();
        $usermanager->refreshAllUserInfo();
        self::$errCode = 1;
        self::$errMsg = '更新完成！';
        return;
    }
    
    /*
     * 删除某一个权限系统用户
     * 作者 涂兴隆
     */
    public function act_deleteUser(){
        $uid = isset($_GET['uid']) ? abs(intval($_GET['uid'])) : 0;
        if(empty($uid)){    //没有传入id 报错
            self::$errCode = 0;
            self::$errMsg = '请指定要删除的用户!';
            return;
        }
        $usermanager = new localUserManageModel();
        $usermanager->deleteUserById($uid);
        self::$errCode = 1;
        self::$errMsg = '删除成功!';
        return;
    }
    
}
