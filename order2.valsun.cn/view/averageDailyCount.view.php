<?php
class AverageDailyCountView extends BaseView {
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    public function view_index(){
        //面包屑
        $navlist = array (
            array ('url' => 'index.php?mod=Platform&act=index', 'title' => '系统设置'),
            array ('url' => 'index.php?mod=averageDailyCount&act=index', 'title' => '日均量策略设置'),
        );
        F('Order');
        $OA                 = A('averageDailyCount');
        $perpage 	        = $OA->act_getPerpage();
        $averageDailyCount  = $OA->act_getAverageDailyCount();
        $pageclass 	        = new Page($averageDailyCount, $perpage, '', 'CN');
        $pageformat         = $averageDailyCount>$perpage ? array(0,1,2,3,4,5,6,7,8,9) : array(0,1,2,3,4);

        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('averageDailyCount'));
        $this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('averageDailyCount'));
        $this->smarty->assign('TopmenuList', A('Topmenu')->act_getTopmenuLists()); //循环列表
        $this->smarty->assign('averageDailyCountList', $OA->act_getAverageDailyCountList());
        $this->smarty->assign('platform',A('Platform')->act_getPlatformLists());
        $this->smarty->assign('userInfo', M('InterfacePower')->key('id')->getAllUserIdUserNameInfo());  //抓取本公司所有的用户信息
        $this->smarty->assign('toptitle', '日均量策略设置');
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('show_page', $pageclass->fpage($pageformat));
        $this->smarty->display("averageDailyCountIndex.htm");
    }

    public function view_add(){
        $navlist = array (//面包屑
            array (
                'url' => 'index.php?mod=Platform&act=index',
                'title' => '系统设置'
            ),
            array (
                'url' => 'index.php?mod=averageDailyCount&act=index',
                'title' => '日均量策略设置'
            ),
            array (
                'url' => '',
                'title' => '添加SKU'
            )
        );
        F('order');
        $OrderTime1 = date('Y-m-d').' 00:00:00';
        $OrderTime2 = date('Y-m-d').' 23:59:59';
        $this->smarty->assign('plataccount', get_userplatacountpower(get_userid()));
        $this->smarty->assign('OrderTime1', $OrderTime1);
        $this->smarty->assign('OrderTime2', $OrderTime2);
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '添加SKU');
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('averageDailyCount') );
        $this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('averageDailyCount'));
        $this->smarty->display("averageDailyCountAdd.htm");
    }

    public function view_edit(){
        $navlist = array (//面包屑
            array (
                'url' => 'index.php?mod=Platform&act=index',
                'title' => '系统设置'
            ),
            array (
                'url' => 'index.php?mod=averageDailyCount&act=index',
                'title' => '日均量策略设置'
            ),
            array (
                'url' => '',
                'title' => '修改'
            )
        );
        F('order');
        $this->smarty->assign('plataccount', get_userplatacountpower(get_userid()));
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toptitle', '添加SKU');
        $this->smarty->assign('toplevel',A('Topmenu')->act_getToplevel('averageDailyCount') );
        $this->smarty->assign('secondlevel',A('Topmenu')->act_getSecondlevel('averageDailyCount'));
        $this->smarty->assign('averageDailyCountList',A('AverageDailyCount')->act_getAverageDailyCountListById());
        $this->smarty->display("averageDailyCountEdit.htm");
    }


    public function view_insert(){
        if(!A('AverageDailyCount')->act_insert()){
            $errorinfo    = A('Account')->act_getErrorMsg();
            $msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
            $this->error($msg, 'index.php?mod=AverageDailyCount&act=index');
        }else{
            $this->success(get_promptmsg(200, '添加'), 'index.php?mod=AverageDailyCount&act=index&rc=reset');
        }
    }

    public function view_update(){
        if(!A('AverageDailyCount')->act_update()){
            $errorinfo    = A('AverageDailyCount')->act_getErrorMsg();
            $msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
            $this->error($msg, 'index.php?mod=AverageDailyCount&act=index');
        }else {
            $this->success(get_promptmsg(200, '修改成功'), 'index.php?mod=AverageDailyCount&act=index&rc=reset');
        }
    }

    public function view_delete(){
        if(!A('AverageDailyCount')->act_delete()){
            $errorinfo    = A('AverageDailyCount')->act_getErrorMsg();
            $msg          = empty($errorinfo) ? get_promptmsg(10021) : implode('<br>', $errorinfo);
            $this->error($msg, 'index.php?mod=AverageDailyCount&act=index');
        }else {
            $this->success(get_promptmsg(200, '删除成功'), 'index.php?mod=AverageDailyCount&act=index&rc=reset');
        }
    }

}