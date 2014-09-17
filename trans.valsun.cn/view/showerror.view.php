<?php
/**
 * 显示错误消息
 */
class showerrorView {
    private $tp_obj = null;
    
    /*
     * 构造函数
     */
    public function __construct() {
        $this->tp_obj = new Template(TEMPLATEPATH);
    }
    
    /*
     * 显示错误消息
     * 根据url中传过来的errordata字段显示错误消息
     * errordata 字段是经过urlencode之后的json数据
     */
    public function view_showerror(){
        $linkdefault = 'index.php?mod=login&act=index';
        $errmsg = isset($_GET['data']) ? 
                        json_decode(urldecode($_GET['data']),true) : array('msg'=>array('出错啦！！！'),'link'=>$linkdefault);
               
        $this->tp_obj->set_var('link',$errmsg['link']);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('errorpage', 'transerrpage.html');
        
        $this->tp_obj->set_block('errorpage', 'errmsg', 'emsglist');
        if(is_array($errmsg)){
            foreach ($errmsg['msg'] as $msgval){
                $this->tp_obj->set_var('msgstr', $msgval);
                $this->tp_obj->parse('emsglist', 'errmsg', TRUE);
            }
        }
        

        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('errorpage', 'errorpage');
        
        $this->tp_obj->p('errorpage');
    }
    
    /*
     * 显示成功消息
     * 根据url中传过来的okmsg字段显示错误消息
     * errordata 字段是经过urlencode之后的json数据
     */
    public function view_showok(){
        $linkdefault = 'index.php?mod=login&act=index';
        $errmsg = isset($_GET['data']) ? 
                        json_decode(urldecode($_GET['data']),TRUE) : array('msg'=>array('操作成功！！！'),'link'=>$linkdefault);
        
        $this->tp_obj->set_var('link',$errmsg['link']);
        
        $this->tp_obj->set_file('header', 'header.html');
        $this->tp_obj->set_file('footer', 'footer.html');
        $this->tp_obj->set_file('okpage', 'transokpage.html');
        
        $this->tp_obj->set_block('okpage', 'errmsg', 'emsglist');
        if(is_array($errmsg)){
            foreach ($errmsg['msg'] as $msgval){
                $this->tp_obj->set_var('msgstr', $msgval);
                $this->tp_obj->parse('emsglist', 'errmsg', TRUE);
            }
        }

        $this->tp_obj->parse('header', 'header');
        $this->tp_obj->parse('footer', 'footer');
        $this->tp_obj->parse('okpage', 'okpage');
        
        $this->tp_obj->p('okpage');
    }
}

