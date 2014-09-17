<?php
/**
 *显示公共消息的页面
 * @author 涂兴隆
 */
class ShowMessageView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }
    
    /*
     * 显示错误页面
     */
    public function view_showErrMsg(){
        $linkdefault = 'index.php?mod=login&act=index';
        $errmsg = isset($_GET['data']) ? 
                        json_decode(urldecode($_GET['data']),true) : array('msg'=>array('出错啦！！！'),'link'=>$linkdefault);
//         print_r($errmsg);exit;
        $this->smarty->assign('messagelist', $errmsg['data']);
        $this->smarty->assign('gobackurl', $errmsg['link']);
        $this->smarty->display('showerror.html');
    }
    
    /*
     * 显示成功提示页面
     */
    public function view_showOkMsg(){
        $linkdefault = 'index.php?mod=login&act=index';
        $errmsg = isset($_GET['data']) ? 
                        json_decode(urldecode($_GET['data']),true) : array('msg'=>array('操作成功！！！'),'link'=>$linkdefault);
        $this->smarty->assign('messagelist', $errmsg['data']);
        $this->smarty->assign('gobackurl', $errmsg['link']);
        $this->smarty->display('showok.html');
    }
}
