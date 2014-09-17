<?php
/*
 * 消息分类列表页面
 */

class msgCategoryView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 显示分类列表 ebay
     */
    public function view_categoryList(){
        
        /* -----  获取全部的分类列表  -----*/
        $msgcat_obj = new messagecategoryModel();
        $lp_ojb     = new LocalPowerModel();
        $powerlist  = $lp_ojb->getEbayPowerlist($_SESSION['userId']);
        if (empty($powerlist['field'])) {
        	$filsql = '0';
        } else {
            $filsql     = implode(', ', $powerlist['field']);
        }
        $arrlist    = $msgcat_obj->getAllCategoryInfoList(' and id in ('.$filsql.') order by category_name');
        $msg_obj    = new messageModel();
        if (in_array('-1', $powerlist['field'])) {
        	$arrlist[]   = array('id'=>-1, 'category_name'=>'迷途文件夹', 'ebay_account'=>'');
        }
        foreach ($arrlist as &$listval){
           /* ---- 计算某个分类下已经回复的数量  ---- */
           $replyed_num = $msg_obj->getNumber($listval['id'], array(2,3));
           $listval['replyed'] = $replyed_num;
           
           /* ---- 计算某个分类下未回复的数量  ---- */
           $noreply_num = $msg_obj->getNumber($listval['id'], array(0));
           $listval['noreply'] = $noreply_num;
        }
        $this->smarty->assign('sec_menue', 1);
        $this->smarty->assign('toplevel', 1);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message类别列表');
        $this->smarty->display('msgcategorylist.htm');
    }
    
    /*
     * 显示分类列表 速卖通
    */
    public function view_categoryListAli(){
    
        /* -----  获取全部的分类列表  -----*/
        $msgcat_obj = new messagecategoryModel();
        $lp_ojb     = new LocalPowerModel();
        $powerlist  = $lp_ojb->getAliPowerlist($_SESSION['userId']);
        // print_r($powerlist);exit;
        if (empty($powerlist['field'])) {
            $filsql = '0';
        } else {
            $filsql     = implode(', ', $powerlist['field']);
        }//echo $filsql;exit;
        $arrlist    = $msgcat_obj->getAllCategoryInfoList(' and id in ('.$filsql.') and platform=2 order by category_name');
        $msg_obj    = new AliOderMessageModel();
        foreach ($arrlist as &$listval){
            /* ---- 计算某个分类下已经读取的数量 <订单留言>  ---- */
            $replyed_num_order = $msg_obj->culculateNumberOrder(" and fieldId=$listval[id] and hasread=1");
            $listval['replyed_order'] = $replyed_num_order;
            
            /* ---- 计算某个分类下未读的数量  <订单留言>---- */
            $noreply_num_order = $msg_obj->culculateNumberOrder(" and fieldId=$listval[id] and hasread=0");
            $listval['noreply_order'] = $noreply_num_order;
            
            /* ---- 计算某个分类下已读的数量 <站内信>  ---- */
            $replyed_num_site = $msg_obj->culculateNumberSite(" and fieldId=$listval[id] and hasread=1");
            $listval['replyed_site'] = $replyed_num_site;
            
            /* ---- 计算某个分类下未读的数量  <站内信>---- */
            $noreply_num_site = $msg_obj->culculateNumberSite(" and fieldId=$listval[id] and hasread=0");
            $listval['noreply_site'] = $noreply_num_site;
        }
        $this->smarty->assign('sec_menue', 2);
        $this->smarty->assign('toplevel', 1);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'message类别列表-速卖通');
        $this->smarty->display('msgcategorylistAli.htm');
    }
    
    /*
     * 添加message 针对ebaymessage分类
     */
    public function view_addNewCategory(){
        $this->addNewCategory('ebay');
    }
    
    /*
     * 添加message 针对速卖通分类
    */
    public function view_addNewCategoryAli(){
        $this->addNewCategory('aliexpress');
    }
    
    /*
     * 添加 文件夹
     * $type 针对添加的类型  ebay、aliexpress
     */
    private function addNewCategory($type){
        $cid        = isset($_GET['cid']) ? $_GET['cid'] : 0;
        extract($this->platformRelate($type));                                                  //特定平台相关信息
        $catinfo = array();
        $msg_obj = new messagecategoryModel();
        if ($cid !== 0) {   //为编辑
            $catinfo = $msg_obj->getCategoryInfoById($cid);
            if (empty($catinfo)) { //没找到信息
                $promptdata = array('data'=>array('分类不存在!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            }
            if ($catinfo['platform'] != $platformid) {
            	$promptdata = array('data'=>array('没有权限编辑!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            }
            $rules_array = explode(',', $catinfo['rules']);
        }
        
        $alphabet = generate_alphabet();                                                         //字母表枚举
        // var_dump($alphabet);exit;
        
        if ($cid) {
            $actname = '编辑';
        } else {
            $actname = '添加';
        }
        $accouts    = array();
        if ($platformid == 1) {         //ebay平台 导入ebay平台账号
            include_once WEB_PATH.'lib/global_ebay_accounts.php';
            sort($GLOBAL_EBAY_ACCOUNT);
            $accouts    = $GLOBAL_EBAY_ACCOUNT;
            $submiturl  = 'index.php?mod=msgCategory&act=editmessageCategory';
        } 
        if ($platformid == 2) {         //速卖通 导入速卖通平台账号
            include_once WEB_PATH.'lib/ali_keys/common.php';
            ksort($erp_user_mapping);
            $accouts    = array_keys($erp_user_mapping);
            $submiturl  = 'index.php?mod=msgCategory&act=editmessageCategoryAli';
        }
        // print_r($rules_array);exit;
        $this->smarty->assign('submiturl', $submiturl);
        $this->smarty->assign('gobackurl',$gobackurl);
        $this->smarty->assign('rules', $rules_array);
        $this->smarty->assign('alphabet', $alphabet);
        $this->smarty->assign('accounts', $accouts);
        $this->smarty->assign('sec_menue', $secondmenue);
        $this->smarty->assign('act',$actname);
        $this->smarty->assign('cid',$cid);
        $this->smarty->assign('catinfo', $catinfo);
        $this->smarty->assign('toptitle', 'message分类编辑');
        $this->smarty->assign('toplevel', 1);
        $this->smarty->display('msgcategoryeditform.htm');
    }
    
    
    /*
     * 编辑message分类数据提交
     */
    public function view_editmessageCategory(){
        $this->editMsgCategoryDataSubmit('ebay');
    }
    
    /*
     * 编辑message分类数据提交 速卖通
    */
    public function view_editmessageCategoryAli(){
        $this->editMsgCategoryDataSubmit('aliexpress');
    }
    
    /*
     * 分类编辑数据提交
     * $type  类型 ebay、aliexpress
     */
    private function  editMsgCategoryDataSubmit($type){
        $data['name']       = isset($_POST['catname']) ? trim($_POST['catname']) : '' ;                 //名称
        $data['rules']      = isset($_POST['alphabet']) ? $_POST['alphabet'] : array() ;            
        $data['account']    = isset($_POST['account']) ? trim($_POST['account']) : '' ;                 //账号
        $data['notes']      = isset($_POST['notes']) ? trim($_POST['notes']) : '' ;                     //备注
        extract($this->platformRelate($type));                                                          //特定平台相关信息
        
        $data['rules']      = array_intersect($data['rules'], generate_alphabet());                     //计算交集      以确保规则正确
        $data['rules']      = implode(',', $data['rules']);
        
        $data['platform']   = $platformid;
        $data = array_map('mysql_real_escape_string', $data);
        $msgcat_obj = new messagecategoryModel();
        if ($data['account'] == -1) {                                                                   //未分配账号
            $promptdata = array('data'=>array('请分配账号!'), 'link'=>$gobackurl);
            goErrMsgPage($promptdata);exit;
        }
        $cid = isset($_POST['cid']) ? trim($_POST['cid']) : 0;
        if ($cid === 0) {                                                                               //新增加分类
            
            $result = $msgcat_obj->addNewCategory($data);
        } else {                                                                                        //更新分类
            /*----- 如果是更新 则需确保数据分类是否和操作的向对应 -----*/
            $catinfo    = $msgcat_obj->getCategoryInfoById($cid);
            if (empty($catinfo)){                                                                       //不存在的id
                $promptdata = array('data'=>array('不合法的id!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            }
            if ($catinfo['platform'] != $data['platform']) {                                            //hack行为
                $promptdata = array('data'=>array('不合法的id!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            }
            unset($data['platform']);                                                                   //不运行更新所属平台
            $result =$msgcat_obj->updateCategoryInfo($cid, $data);
        }
        
        if ($result) {
            $promptdata = array('data'=>array('操作成功!'), 'link'=>$gobackurl);
            goOkMsgPage($promptdata);
            exit;
        } else {
            $promptdata = array('data'=>array('操作失败!'), 'link'=>$gobackurl);
            goErrMsgPage($promptdata);
            exit;
        }
    }
    
    /*
     * ajax删除messagecategory
     */
    public function view_ajaxDelCategory(){
        $cid = isset($_GET['cid']) ? $_GET['cid'] : 0;
        if (!is_numeric($cid)) {                                                //传入数据非数字
        	$msgar = array('code'=>6001, 'msg'=>'id不合法');
         	echo json_encode($msgar);
        	exit;
        }
        if ($cid === 0) {                                                       //没有传入分类id
        	$msgar = array('code'=>6004, 'msg'=>'未指定分类id');
         	echo json_encode($msgar);
        	exit;
        }
        
        $msgcat_obj = new messagecategoryModel();
        $result = $msgcat_obj->delCategoryById($cid);
        if ($result) {                                                          //删除成功
        	$msgar = array('code'=>6002, 'msg'=>'删除成功!');
        	echo json_encode($msgar);
        	exit;
        } else {
            $msgar = array('code'=>6003, 'msg'=>'删除失败!');
        	echo json_encode($msgar);
        	exit;
        }
    }
    
    /*
     * 生成平台相关的信息
     */
    private function platformRelate($plateform){
        $returnresult   = array();
        switch ($plateform){
        	case 'ebay':                                                                               //ebay
        	    $returnresult['platformid']   = 1;
        	    $returnresult['gobackurl']    = 'index.php?mod=msgCategory&act=categoryList';
                $returnresult['secondmenue']  = 1;
        	    break;
        	case 'aliexpress':                                                                         //速卖通
        	    $returnresult['platformid']   = 2;
        	    $returnresult['gobackurl']    = 'index.php?mod=msgCategory&act=categoryListAli';
        	    $returnresult['secondmenue']  = 2;
        	    break;
        }
        return $returnresult;
    }
}
