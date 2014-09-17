<?php
/**
 * 显示错误消息
 */
class showerrorView extends BaseView{
       
    /*
     * 显示错误消息
     * 根据url中传过来的errordata字段显示错误消息
     * errordata 字段是经过urlencode之后的json数据
     */
    public function view_showerror(){
		$linkdefault = 'index.php?mod=login&act=index';
        $errmsg = isset($_GET['data']) ? json_decode(urldecode($_GET['data']),true) : array('msg'=>array('出错啦！！！'),'link'=>$linkdefault);
		//echo "<pre>";print_r($errmsg);exit;
		
		//二级导航
		$navarr = array("<a href='index.php?mod=sampleStandard&act=skuTypeQcList'>IQC检测标准</a>",">>","系统错误信息");
        $this->smarty->assign('navarr',$navarr);			
		$this->smarty->assign('secnev','4');              
		$this->smarty->assign('module','系统错误信息');
		$this->smarty->assign('username',$_SESSION['userName']);
		$link = $errmsg['link'];
		$this->smarty->assign('link',$link);		
		
		if(is_array($errmsg)){
		  $msgval = $errmsg['msg'][0];
          $this->smarty->assign('msgval',$msgval);            
        }
		$this->smarty->display('qcErrorPage.html');
    }
    
    /*
     * 显示成功消息
     * 根据url中传过来的okmsg字段显示错误消息
     * errordata 字段是经过urlencode之后的json数据
     
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
    }*/
}

