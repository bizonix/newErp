<?php

class AmazonMessageTemplateView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
     
    
	/*
     * 显示模板列表页          amazon
     */
    public function view_showTemplateListAmazon(){
        $this->getTemplateList();
    }
    /*
     * 通用模板显示函数
     * $platform ebay, aliexpress
     */
    private function getTemplateList(){
    	extract($this->generateInfo());
        $pagesize = 100;
        $msgtpl_obj = new AmazonMessageTemplateModel();
        $all = $msgtpl_obj->getAllMessageNumber(' and ownerid in (0, '.$_SESSION['globaluserid'].')');
       
        $page_obj = new Page($all, $pagesize);
        $usercache = new UserCacheModel();
        
        $templatelist = $msgtpl_obj->getAllTemplateList(' and ownerid in (0,'.$_SESSION['globaluserid']. ') '.$page_obj->limit);
       
        foreach ($templatelist as &$tpval){
            $info = empty($tpval['ownerid']) ? $tpval['username'] ='公用' : $usercache->getUserInfoBySysId($tpval['ownerid'], 0);
            if (is_array($info)) {
                $tpval['username'] = $info['userName'];
            }
        }
        
        if ($all > $pagesize) {       //分页
            $pagestr =  $page_obj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $page_obj->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('addurl', $editUrl);
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('sec_menue', $sec_menueid);
        $this->smarty->assign('tpllist', $templatelist);
        $this->smarty->assign('toptitle', 'message模板列表');
        $this->smarty->display('msgtemplatelistAmazon.htm');
    }
    
    
    /*
     * 编辑模板页面      
     */
    public function view_editTemplateFormAmazon(){
        $this->editTemplate();
    }
   
 
    /*
     * 模板编辑通用页面
     * 
     */
     private function editTemplate(){
        extract($this->generateInfo());
        $tid = isset($_GET['tid']) ? $_GET['tid'] : 0;
        $actname = '添加';
        $tplinfo = array();
        if (!empty($tid)) {
            if (!is_numeric($tid)) {    //传入id为非数字
                $msgdata = array('data'=>array('id不合法!'), 'link'=>$gobackurl);
                goErrMsgPage($msgdata);
                exit;
            }
            $actname = '编辑';
            $msgtpl_obj = new AmazonMessageTemplateModel();
            $tplinfo = $msgtpl_obj->getTplInfoById($tid);
           
            if (empty($tplinfo)) {  //没找到信息 
                $msgdata = array('data'=>array('指定模板不存在!'), 'link'=>$gobackurl);
                goErrMsgPage($msgdata);
                exit;
            }
        }
        // print_r($tplinfo);exit;
        $this->smarty->assign('sec_menue', $sec_menueid);
        $this->smarty->assign('tid', $tid);
        $this->smarty->assign('act', $actname);
        $this->smarty->assign('tplinfo', $tplinfo);
        $this->smarty->assign('submiturl', $submiturl);
        $this->smarty->assign('gobackurl', $gobackurl);
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('toptitle', 'message模板编辑');
        $this->smarty->display('msgtpleditformAmazon.htm');
     }
    
    /*
     * 模板编辑提交页面       
     */
    public function view_tplDataSubmitAmazon(){
        $this->handleSubmit();
    }
    
   

    /*
     * 模板编辑数据处理函数
     * 
     */
     private function handleSubmit(){
        extract($this->generateInfo()); 
        $data['title']          = isset($_POST['title']) ? $_POST['title'] : '';
        $data['topic']          = isset($_POST['topic']) ? $_POST['topic'] : '';
        $data['content']        = isset($_POST['content']) ? $_POST['content'] : '';
        $data['ordersn']        = isset($_POST['ordersn']) ? $_POST['ordersn'] : '';
        $data['ispublic']    = isset($_POST['ispublic']) ? $_POST['ispublic'] : 0;
        
        $data['iscommon']       = isset($_POST['iscommon']) ? $_POST['iscommon'] : 0;
        $data = array_map('mysql_real_escape_string', $data);   //字符串过滤
        $msgtpl_obj = new AmazonMessageTemplateModel();
        $tid = isset($_POST['tid']) ? $_POST['tid'] : 0;
        if (empty($tid)) {  //新添加模板
            $res = $msgtpl_obj->addTemplate($data, $_SESSION['globaluserid']);
            if ($res) {
                $msgdata = array('data'=>array('添加成功!'), 'link'=>$gobackurl);
                goOkMsgPage($msgdata);
                exit;
            } else {
                $msgdata = array('data'=>array('添加失败!'), 'link'=>$gobackurl);
                goErrMsgPage($msgdata);
                exit;
            }
        } else {    //更新模板
        // print_r($data);exit;
            $res = $msgtpl_obj->updateTplInfo($data, $tid, $_SESSION['globaluserid']);
            if ($res) {
                $msgdata = array('data'=>array('更新成功!'), 'link'=>$gobackurl);
                goOkMsgPage($msgdata);
                exit;
            } else {
                $msgdata = array('data'=>array('更新失败!'), 'link'=>$gobackurl);
                goErrMsgPage($msgdata);
                exit;
            }
        }
    }
    
   
    
    /*
     * ajax删除template
     */
    public function view_ajaxDelTemplate(){
        $tid = isset($_GET['tid']) ? $_GET['tid'] : 0;
        if (!is_numeric($tid)) {                                    //tid不合法
            $msgar = array('code'=>7001, 'msg'=>'id不合法!');
            echo json_encode($msgar);
            exit;
        }
        if ($tid === 0) {                                           //没指定id
        	$msgar = array('code'=>7002, 'msg'=>'请指定id!');
            echo json_encode($msgar);
            exit;
        }
        
        $msgtpl_obj = new AmazonMessageTemplateModel();
        $delres = $msgtpl_obj->delTplById($tid);
        if ($delres) {                                              //删除成功
        	$msgar = array('code'=>7003, 'msg'=>'删除成功!');
            echo json_encode($msgar);
            exit;
        } else {
            $msgar = array('code'=>7004, 'msg'=>'删除失败!');
            echo json_encode($msgar);
            exit;
        }
    }

   
   
       
    /*
     * 删除模板
     */
    public function view_deleteShipTpl(){
        $pid    = isset($_GET['id']) ? intval(trim($_GET['id'])) : FALSE;
        $returndata = array('code'=>0, 'msg'=>'');
        if (empty($pid)) {
            $returndata['code'] = 600;
            $returndata['msg']  = '缺少参数!';
            echo json_encode($returndata);
            exit;
        }
        $alishipTpl_obj = new AliShipTemplateModel();
        $result         = $alishipTpl_obj->deleteTemplate($pid);
        if ($result) {
        	$returndata['code'] = 603;
            $returndata['msg']  = '成功!';
            echo json_encode($returndata);
            exit;
        } else {
            $returndata['code'] = 602;
            $returndata['msg']  = '操作失败!';
            echo json_encode($returndata);
            exit;
        }
    }
    
    
    /*
     * 设置模板
     */
    public function view_setAccountsTpl(){
        $id         = isset($_GET['id']) ? intval($_GET['id']) : FALSE;                             //模板id
        $account    = isset($_GET['account']) ? trim($_GET['account']) : FALSE;                     //账号 
        $returnData = array('code'=>0, 'msg'=>'');
        if (empty($id)) {                                                   //缺少模板
        	$returnData['code']    = 800;
        	$returnData['msg']     = '缺少参数';
        	echo json_encode($returnData); exit;
        }
        if (empty($returnData)) {                                           //缺少账号
            $returnData['code']    = 801;
            $returnData['msg']     = '缺少账号!';
            echo json_encode($returnData); exit;
        }
        $aliTplmodel_obj    = new AliShipTemplateModel();
        if ($aliTplmodel_obj->isTplExists($id) == FALSE) {                   //不存在的模板
            $returnData['code']    = 802;
            $returnData['msg']     = '模板不存在!';
            echo json_encode($returnData); exit;
        }
        $aliAccount_obj = new AliAccountModel();
        if (!$aliAccount_obj->aliAccountExists($account)) {
            $returnData['code']    = 803;
            $returnData['msg']     = '不存在的账号!';
            echo json_encode($returnData); exit;
        }
        $result = $aliTplmodel_obj->setAccountsTpl($account, $id);
        if($result){
            $returnData['code']    = 805;
            $returnData['msg']     = '成功!';
            echo json_encode($returnData); exit;
        } else {
            $returnData['code']    = 804;
            $returnData['msg']     = '设置失败!';
            echo json_encode($returnData); exit;
        }
    }
    
    public function view_showEbayTplList(){
        $ectm_obj   = new EbayCsTplManageModel();
        $tplList    = $ectm_obj->getTplList();
        foreach ($tplList as $key=>$row){
            $relCountry = $ectm_obj->getRelCountryByTplId($row['id']);
            $tplList[$key]['relc']    = $relCountry;
        }
        $this->smarty->assign('addurl','index.php?mod=messageTemplate&act=showAddCsTplForm');
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('sec_menue', 5);
        $this->smarty->assign('tpllist', $tplList);
        $this->smarty->display('msgtemplatelist_ebaycs.htm');
    }
    
    /*
     * 新增模板
     */
    public function view_showAddCsTplForm(){
        
        $rel_obj        = new CommonModel('msg_ebaycsrel');
        $countryList    = $rel_obj->findAll('*', " where tplId=0 order by groupId ");
        $finalList  = array();
        foreach ($countryList as $country){
            if (array_key_exists($country['groupId'], $finalList) ) {
            	$finalList[$country['groupId']][]    = $country;
            } else {
                $finalList[$country['groupId']]      = array($country);
            }
        }
        foreach ($finalList as $key=>$row){
            $finalList[$key]    = array_chunk($row, 6);
        }
        
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('countryList', $finalList);
        $this->smarty->assign('sec_menue', 5);
        $this->smarty->display('ebayCsmsgtpleditform.htm');
    }
    
    /*
     * 新增模板页面的表单提交数据
     */
    public function view_addNewCsTpl(){
        $name       = isset($_POST['title']) ? trim($_POST['title']) : '';
        $subject    = isset($_POST['topic']) ? trim($_POST['topic']) : '';
        $content    = isset($_POST['content']) ? trim($_POST['content']) : '';
        $countries  = isset($_POST['country']) ? $_POST['country'] : false;
        
        $ectm_obj   = new EbayCsTplManageModel();
        
        if (empty($name) || empty($subject) || empty($content)) {
            $msgdata = array('data'=>array('数据不完整!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        foreach ($countries as $country){                                       //国家代码必须存在 并且该国家还没有关联模板
            if ( !$ectm_obj->checkCountryCodeExists($country) || !$ectm_obj->checkAllowSet($country) ) {
                $msgdata = array('data'=>array('不合法的国家代码!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
                goErrMsgPage($msgdata);
                exit;
            }
        }
        
        $tplmodel   = new CommonModel('msg_ebaycstpl');
        $name       = htmlentities($name);
        $subject    = htmlentities($subject);
        $content    = htmlentities($content);
        $insertData = array('name'=>$name, 'subject'=>$subject, 'content'=>$content, 'updateTime'=>time(), 'is_delete'=>0);
        $insertId   = $tplmodel->insertNewRecord($insertData);
        if (FALSE === $insertId) {
            $msgdata = array('data'=>array('插入数据失败!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
            goErrMsgPage($msgdata);
            exit;
        } 
        
        $countryRel = new CommonModel('msg_ebaycsrel');
        $update     = array('tplId'=>$insertId);
        $countries  = CommonModel::transSafetySql($countries);
        
        foreach ($countries as $ccode){
            $countryRel->updateData($update, " where countryCode='$ccode'");
        }
        $msgdata = array('data'=>array('操作成功!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
        goOkMsgPage($msgdata);
        exit;
    }
    
    /*
     * ajax 删除模板
     */
    public function view_delCsTpl(){
        $returnData = array('code'=>0, 'msg'=>'');
        $tid        = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
        $tpl_model  = new CommonModel('msg_ebaycstpl');
        $tplInfo    = $tpl_model->findOne('*', " where id='$tid' ");
        if (!$tplInfo) {                                                                            //不存在的模板
        	$returnData['msg'] = '不存在的模板';
        	echo json_encode($returnData);
        	exit;
        }
        $upTpl  = $tpl_model->updateData(array('is_delete'=>1), " where id=$tid ");                 //设置为已删除
        
        $tplRel_obj = new CommonModel('msg_ebaycsrel');
        $tplRel_obj->updateData(array('tplId'=>0), " where tplId='$tid'");                          //将对应的模板设置为一已经删除
        
        $returnData['code'] = 1;
        echo json_encode($returnData);
        exit;
    }
    
    /*
     * 编辑售后推送模板
     */
    public function view_editCsTpl(){
        $tid        = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
        $tplobj     = new CommonModel('msg_ebaycstpl');
        $tplInfo    = $tplobj->findOne('*', " where id=$tid");
        if (!$tplInfo) {                                                                        //不存在模板信息
            $msgdata = array('data'=>array('不存在的模板!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        $ecrl_obj   = new CommonModel('msg_ebaycsrel');
        $country    = $ecrl_obj->findAll('*', " where tplid in (0, $tid)");
        $setedId    = array();
        foreach ($country as $key=>$row){
            if ($row['tplId'] != 0) {
            	$country[$key]['checked'] = 1;
            } else {
                $country[$key]['checked'] = 0;
            }
        }
        
        $countryList    = $country;
        $finalList  = array();
        foreach ($countryList as $country){
            if (array_key_exists($country['groupId'], $finalList) ) {
                $finalList[$country['groupId']][]    = $country;
            } else {
                $finalList[$country['groupId']]      = array($country);
            }
        }
//         print_r($finalList);exit;
        foreach ($finalList as $key=>$row){
            $finalList[$key]    = array_chunk($row, 6);
        }
        
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('countryList', $finalList);
        $this->smarty->assign('tplinfo', $tplInfo);
        $this->smarty->assign('sec_menue', 5);
        $this->smarty->display('ebayCsmsgtpleditform_edit.htm');
    }
    
    /*
     * 处理编辑表单提交
     */
    public function view_submitEditData(){
        $tid    = isset($_POST['tid'])   ? intval($_POST['tid']) : 0;
        $name       = isset($_POST['title']) ? trim($_POST['title']) : '';
        $subject    = isset($_POST['topic']) ? trim($_POST['topic']) : '';
        $content    = isset($_POST['content']) ? trim($_POST['content']) : '';
        $countries  = isset($_POST['country']) ? $_POST['country'] : false;
        
        $tpl_obj    = new CommonModel('msg_ebaycstpl');
        $tplRel_obj = new CommonModel('msg_ebaycsrel');
        $tplInfo    = $tpl_obj->findOne('*', " where id='$tid' ");
        if (empty($tplInfo)) {
            $msgdata = array('data'=>array('不存在的模板!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
            goErrMsgPage($msgdata);
            exit;
        }
        
        $tpl_obj->updateData(array('name'=>$name, 'subject'=>$subject, 'content'=>$content, 'updateTime'=>time()) , " where id='$tid'");
        $countries  = CommonModel::transSafetySql($countries);
        
        $tplRel_obj->updateData(array('tplId'=>0), " where tplId='$tid'");
        $contrySql  = implode("', '", $countries);
        $tplRel_obj->updateData(array('tplId'=>$tid), " where countryCode in ('$contrySql')");
        $msgdata = array('data'=>array('执行成功!'), 'link'=>'index.php?mod=messageTemplate&act=showEbayTplList');
        goOkMsgPage($msgdata);
        exit;
    }
    
    private function generateInfo(){
    	$returnarr  = array();
    	
    			$returnarr['gobackurl']     = 'index.php?mod=amazonMessageTemplate&act=showTemplateListAmazon';
    			$returnarr['editUrl']       = 'index.php?mod=amazonMessageTemplate&act=editTemplateFormAmazon';
    			$returnarr['submiturl']     = 'index.php?mod=amazonMessageTemplate&act=tplDataSubmitAmazon';
    			$returnarr['sec_menueid']   = 6;
 
    	return $returnarr;
    }
}




