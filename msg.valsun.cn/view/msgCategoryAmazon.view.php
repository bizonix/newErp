<?php
/*
 * 消息分类列表页面
 */

class msgCategoryAmazonView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
   /*
     * 显示分类列表 Amazon
    */
    public function view_categoryListAmazon(){
    
    	/* -----  获取全部的分类列表  -----*/
    	$msgcat_obj  =  new amazonmessagecategoryModel();
    	$lp_ojb      =  new LocalPowerAmazonModel();
        $powerlist   =  $lp_ojb->getAmazonPowerlist($_SESSION['userId']);//获得该用户能看到的邮件目录id
                   
        //print_r($powerlist);
        if (empty($powerlist)) {
        	$filsql     = '0';
        } else {
            $filsql     = $powerlist;
        }
        $arrlist    = $msgcat_obj->getAllCategoryInfoList(' and id in ('.$filsql.') order by id desc');
        $msg_obj    = new amazonmessageModel();
        foreach ($arrlist as &$listval){
           /* ---- 计算已经回复的数量  ---- */
           $replyed_num = $msg_obj->getAmazonNumber($listval['id'], array(2,3));
           $listval['replyed'] = $replyed_num;
           
           /* ---- 计算未回复的数量  ---- */
           $noreply_num = $msg_obj->getAmazonNumber($listval['id'], array(0));
           $listval['noreply'] = $noreply_num;
        }
          $msg_obj->addAccountToGlobal();
         $msg_obj->addAccountToLocal();
         $msg_obj->turnState(); 
        $this->smarty->assign('sec_menue', 3);
        $this->smarty->assign('toplevel', 1);
        $this->smarty->assign('categorylist', $arrlist);
        $this->smarty->assign('toptitle', 'Amazon message类别列表');
        $this->smarty->display('msgcategorylistAmazon.htm');
    }
    
   
    /*
     * 添加message 针对amazon message分类
     */
    public function view_addNewCategoryAmazon(){
        $this->addNewCategory();
    }
    
    /*
     * 添加 文件夹或者修改文件夹（通过看是否传入cid）
     * 
     */
    private function addNewCategory(){
        $cid            = isset($_GET['cid']) ? $_GET['cid'] : 0;
        $account        = isset($_GET['account']) ?mysql_escape_string( $_GET['account']) : 0;
        $catinfo 		= array();
        $msgcat_obj        = new amazonmessagecategoryModel();
        
        $catname        = isset($_POST['catname'])?$_POST['catname']:'';
        $amazonaccount  = isset($_POST['account']) ? $_POST['account'] : 0;
       
        if($amazonaccount){
        	$msgcat_obj->getSiteGmailByAJAX($amazonaccount);
        	exit();
        }
        
      
        if($catname){
        	$in_catlist = $msgcat_obj->getCategoryInfoByCatname($catname);
        	if($in_catlist){
        		die('该分类名已经存在');
        	} else {
        		die('该分类名可以使用');
        	}
        }
        extract($this->platformRelate());                                                  //特定平台相关信息
        
        
        
        
        if ($cid !== 0) {   //为编辑
            $catinfo = $msgcat_obj->getCategoryInfoById($cid);
            $rules_array = explode(',', $catinfo['rules']);
        }
        
        $alphabet = generate_alphabet();                                                         //字母表枚举
        // var_dump($alphabet);exit;
        
        if ($cid) {
            $actname = '编辑';
        } else {
            $actname = '添加';
        }
        $accounts    = array();
        
        $accounts    = $msgcat_obj->getAllAccount();
        $submiturl  = 'index.php?mod=msgCategoryAmazon&act=editmessageCategory';
        	
        
        $site_gmail_arr =  $msgcat_obj->getSiteGmail($account);
        $sites     = array();
        $mailboxes = array();
        foreach ($site_gmail_arr as $var){
        	if(!in_array($var['site'], $sites)){
        		$sites[]     = $var['site'];
        	}
        	if(!in_array($var['gmail'], $mailboxes)){
        		$mailboxes[] = $var['gmail'];
        	}
        	
        }
        $this->smarty->assign('submiturl', $submiturl);
        $this->smarty->assign('gobackurl',$gobackurl);
        $this->smarty->assign('rules', $rules_array);
        $this->smarty->assign('alphabet', $alphabet);
        $this->smarty->assign('accounts', $accounts);
        $this->smarty->assign('sites', $sites);
        $this->smarty->assign('mailboxes', $mailboxes);
        $this->smarty->assign('sec_menue', $secondmenue);
        $this->smarty->assign('act',$actname);
        $this->smarty->assign('cid',$cid);
        $this->smarty->assign('catinfo', $catinfo);
        $this->smarty->assign('toptitle', 'message分类编辑');
        $this->smarty->assign('toplevel', 1);
        $this->smarty->display('msgcategoryeditformAmazon.htm');//显示添加分类信息页面
    }
    
    
   
    /*
     * 编辑amazon message分类数据提交
    */
    public function view_editmessageCategory(){
    	$this->editMsgCategoryDataSubmit();
    }
    
    /*
     * 分类编辑数据提交
     * 
     */
    private function  editMsgCategoryDataSubmit(){
        $data['name']       = isset($_POST['catname']) ? trim($_POST['catname']) : '' ;                 //名称
        $data['rules']      = isset($_POST['alphabet']) ? $_POST['alphabet'] : array() ;            
        $data['account']    = isset($_POST['account']) ? trim($_POST['account']) : '' ;                 //账号
        $data['site']       = isset($_POST['site']) ? trim($_POST['site']) : '' ;
        $data['gmail']      = isset($_POST['gmail']) ? trim($_POST['gmail']) : '' ;
        $data['notes']      = isset($_POST['notes']) ? trim($_POST['notes']) : '' ;                     //备注
        extract($this->platformRelate($type));                                                          //特定平台相关信息
        
        $data['rules']      = array_intersect($data['rules'], generate_alphabet());   //计算交集      以确保规则正确
        $data['rules']      = implode(',', $data['rules']);
        
       
        $data = array_map('mysql_real_escape_string', $data);
        $msgcat_obj = new amazonmessagecategoryModel();
       	$lpower_obj = new LocalPowerAmazonModel();
       	$global_obj = new GetLoacalUserModel();
       	//print_r($_SESSION);
       	$creater    = $global_obj->getRealNameByGlobalId($_SESSION['globaluserid']);
       	$data['creater']=$creater['global_user_name'];
       	$data['createtime']=time();
        if ($data['account'] == -1) {                                                                   //未分配账号
            $promptdata = array('data'=>array('请分配账号!'), 'link'=>$gobackurl);
            goErrMsgPage($promptdata);
        }
        $cid = isset($_POST['cid']) ? trim($_POST['cid']) : 0;
       
        //这下面是判断是新增分类还是编辑分类，然后执行相应操作
        if ($cid === 0) {                                                                               //新增加分类
     	 try{
        		$result = $msgcat_obj->addNewCategory($data);
      	}catch(Exception $e){
			$promptdata = array('data'=>array('新增分类失败!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
 }
            
         if (empty($result)){                                                                       //不存在的id
                $promptdata = array('data'=>array('不合法的id!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            } else {
            	/*----- 将新增的分类的权限给创建者 -----*/
            try{
            	$new_cid = $msgcat_obj->getCategoryInfoByCatname($data['name']);
            	$lpower_obj->updatePowerByAddClass($_SESSION['userId'], $new_cid['id']);
            } catch(Exception $e){
            	print_r($new_cid);
            	/* $promptdata = array('data'=>array('权限更新失败!'), 'link'=>$gobackurl);
            	goErrMsgPage($promptdata); */
            	exit;
            }
           } 
        } else {                                                                                        //更新分类
            /*----- 如果是更新 则需确保数据分类是否和操作的向对应 -----*/
            $catinfo    = $msgcat_obj->getCategoryInfoById($cid);
           
            if (empty($catinfo)){                                                                       //不存在的id
                $promptdata = array('data'=>array('不合法的id!'), 'link'=>$gobackurl);
                goErrMsgPage($promptdata);
                exit;
            }
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
     * ajax删除Amazon邮件目录
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
        $msg_obj = new amazonmessageModel();
        $num     = $msg_obj->getNumber($cid);
        if($num){
        	$msgar = array('code'=>6005, 'msg'=>'该分类下邮件不为空，不能删除!');
        	echo json_encode($msgar);
        	exit;
        }
        
        $msgcat_obj = new amazonmessagecategoryModel();
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
     * 生成Amazon平台相关的信息 返回键的超链接 等。。。
     */
    private function platformRelate(){
        $returnresult 					=  array();
        $returnresult['gobackurl']      =  'index.php?mod=msgCategoryAmazon&act=categoryListAmazon';    //Amazon
        $returnresult['secondmenue']    =  3;
        return $returnresult;
    }
}
