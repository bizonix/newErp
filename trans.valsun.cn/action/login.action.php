<?php
class LoginAct{
    public static $errCode = 0;
    public static $errMsg = '';
    
    /*
     * 构造函数
     */
    public function __construct() {
    }
    
    /*
     * 登陆动作
     */
    public function act_login(){
        $username = trim($_POST['username']);   //用户名
        $password = trim($_POST['password']);   //密码
        if(empty($username) || empty($password)){   //用户名或密码为空
            self::$errCode = 1;
            self::$errMsg = '请正确填写用户名和密码!';
            return false;
        }//echo $username, $password;exit;
        $loginresult = Auth::login($username, $password,'1');
        $loginresult = json_decode($loginresult, true);   //json解码
        
        if($loginresult['errCode']!=0){     //登陆失败
            self::$errCode = 1;
            switch ($loginresult['errCode']) {
                case '0001':
                    self::$errMsg = '网络错误！';
                    break;
                case '1820':
                    self::$errMsg = '状态错误';
                    break;
                case '1821':
                    self::$errMsg = 'token过期！';
                    break;
                case '1822':
                    self::$errMsg = '用户名或密码不正确！';
                    break;
                default:
                    break;
            }
            return false;
        } else {    //登陆成功
            self::$errCode = 2;
            self::$errMsg = '';
            //添加数据到session
            $_SESSION['userId'] = $loginresult['userId'];
            $_SESSION['userName'] = $username;
            $_SESSION['userToken'] = $loginresult['userToken'];
            //缓存数据到memcache和数据库
            //var_dump($loginresult);exit;
            UserCacheModel::userInfoCache($loginresult['userToken'] ,$loginresult['userId']);
            return $loginresult;
        }
    }
}

?>
