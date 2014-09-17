<?php
/**
 * 类名：CommonView
 * 功能：封装公共模块相关的类
 * 版本：1.0
 * 日期：2013/7/31
 * 作者：任达海
 */
 
if(!isset($_SESSION)){
    session_start();     
}
 
class CommonView {
    
    /**
    * 构造函数 
    */    
	public function __construct() {		  
	   if(!isset($_SESSION["username"])) {    	  
	       //header("location:index.php?mod=login&act=index");
	   }
	}
    
     /**
    * 渲染模板变量的函数
    * @return    void
    */
    private function view_lang() {
        self::$tp->set_var("sender", "发件人");
        self::$tp->set_var('receiver', '收件人');
   		self::$tp->set_var("contents", '内容');
    	self::$tp->set_var("time", '时间');        
        self::$tp->set_var("title", "用户登录");
        self::$tp->set_var('search_word', '搜索联系人');
   		self::$tp->set_var("search_button", '搜索');
    	self::$tp->set_var("usernames", '姓名');
        self::$tp->set_var("phones", '手机');
        self::$tp->set_var("emails", '邮箱');       
        self::$tp->set_var("delete", '删除');        
        self::$tp->set_var("deleting",'删除中...'); 
        self::$tp->set_var("delete_confirm", '确认要删除吗？');                      
        self::$tp->set_var("search_empty", '结果为空！'); 
        self::$tp->set_var("operat_failedMsg", '操作失败，错误信息:');     
        self::$tp->set_var("operat_success", '操作成功！');
        self::$tp->set_var("select_item", '请选择要操作的项！');  
        self::$tp->set_var("input_seach_condition", '请输入发件人或收件人！');
        self::$tp->set_var("emailNoticeList", "邮件记录");        
        self::$tp->set_var("smsNoticeList", "短信记录");   
        self::$tp->set_var("logou_url", "index.php?mod=login&act=logout");        
        self::$tp->set_var("lang_logOut_key", "退出");
 
        $userMenuStr = '';
        $userMenuStr .= '<li id="nav-userList"><a href="index.php?mod=user&act=userList">'.'通讯录'.'</a></li>';
        $userMenuStr .= '<li id="nav-emailNoticeList"><a href="index.php?mod=notice&act=emailNoticeList">'.'消息记录'.'</a></li>';
        self::$tp->set_var("userMenuStr", "$userMenuStr");        
	    self::$tp->set_var("lang_button_submit", 'submit');
        self::$tp->set_var("lang_button_turnBack", 'Back');  
        
         
     }		
}
?>