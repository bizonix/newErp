<?php
/**
 *回复模板页面
 * @author 涂兴隆
 */
class MessageTemplateView extends BaseView{
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /*
     * 显示模板列表页          ebay
     */
    public function view_showTemplateList(){
        $this->getTemplateList('ebay');
    }
    
    /*
     * 显示模板列表页          速卖通
     */
    public function view_showTemplateListAli(){
        $this->getTemplateList('aliexpress');
    }

    /*
     * 通用模板显示函数
     * $platform ebay, aliexpress
     */
    private function getTemplateList($platform){
        extract($this->generateInfo($platform));
        $pagesize = 100;
        $msgtpl_obj = new MessageTemplateModel();
        $all = $msgtpl_obj->getAllMessageNumber(' and platform='.$platformid.' and ownerid in (0, '.$_SESSION['globaluserid'].')');
        $page_obj = new Page($all, $pagesize);
        $usercache = new UserCacheModel();
        
        $templatelist = $msgtpl_obj->getAllTemplateList(' and ownerid in (0,'.$_SESSION['globaluserid']. ') and platform='.$platformid.' '.$page_obj->limit);
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
        $this->smarty->display('msgtemplatelist.htm');
    }
    
    
    /*
     * 编辑模板页面       ebay
     */
    public function view_editTemplateForm(){
        $this->editTemplate('ebay');
    }
    
     /*
     * 编辑模板页面       速卖通
     */
    public function view_editTemplateFormAli(){
        $this->editTemplate('aliexpress');
    }

    /*
     * 模板编辑通用页面
     * $platform    ebay, aliexpress
     */
     private function editTemplate($platform){
        extract($this->generateInfo($platform));
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
            $msgtpl_obj = new MessageTemplateModel();
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
        $this->smarty->display('msgtpleditform.htm');
     }
    
    /*
     * 模板编辑提交页面         ebay
     */
    public function view_tplDataSubmit(){
        $this->handleSubmit('ebay');
    }
    
    /*
     * 模板编辑提交页面         速卖通
     */
    public function view_tplDataSubmitAli(){
        $this->handleSubmit('aliexpress');
    }

    /*
     * 模板编辑数据处理函数
     * $platform ebay, aliexpress
     */
     private function handleSubmit($platform){
        extract($this->generateInfo($platform)); 
        $data['title']          = isset($_POST['title']) ? $_POST['title'] : '';
        $data['topic']          = isset($_POST['topic']) ? $_POST['topic'] : '';
        $data['content']        = isset($_POST['content']) ? $_POST['content'] : '';
        $data['ordersn']        = isset($_POST['ordersn']) ? $_POST['ordersn'] : '';
        $data['incommonuse']    = isset($_POST['incommonuse']) ? $_POST['incommonuse'] : 0;
        $data['platform']       = $platformid;
        $data['iscommon']       = isset($_POST['iscommon']) ? $_POST['iscommon'] : 0;
        $data = array_map('mysql_real_escape_string', $data);   //字符串过滤
        $msgtpl_obj = new MessageTemplateModel();
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
     * ajax添加速卖通模板
     */
    public function view_addTemplateByAjax(){
        $title      = isset($_POST['title']) ? trim($_POST['title']) : FALSE;                     //标题
        $content    = isset($_POST['content'])   ? trim($_POST['content']) : FALSE;               //名称
//         print_r($_POST);exit;
        $data       = array();
        $data['topic']          = isset($_POST['topic']) ? $_POST['topic'] : '';
        $data['ordersn']        = isset($_POST['ordersn']) ? $_POST['ordersn'] : '';
        $data['incommonuse']    = isset($_POST['incommonuse']) ? $_POST['incommonuse'] : 0;
        $data['platform']       = 2;
        $data['iscommon']       = isset($_POST['iscommon']) ? $_POST['iscommon'] : 0;
        $data['title']          = $title;
        $data['content']        = $content;
        if (empty($title) || empty($content)) {
        	echo json_encode(array('errCode'=>0, 'msg'=>'数据缺失'));
        	exit;
        }
        $msgtpl_obj = new MessageTemplateModel();
        $res        = $msgtpl_obj->addTemplate($data, $_SESSION['globaluserid']);
        if ($res) {                                                                             //成功
            echo json_encode(array('errCode'=>1, 'msg'=>'成功'));
            exit;
        } else {
            echo json_encode(array('errCode'=>0, 'msg'=>'添加失败'));
            exit;
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
        
        $msgtpl_obj = new MessageTemplateModel();
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
     * 生成通用信息
     * $platform ebay aliexpress
     */
    private function generateInfo($platform){
        $returnarr  = array();
        switch ($platform) {
            case 'ebay':
                $returnarr['platformid']    = 1;
                $returnarr['gobackurl']     = 'index.php?mod=messageTemplate&act=showTemplateList';
                $returnarr['editUrl']       = 'index.php?mod=messageTemplate&act=editTemplateForm';
                $returnarr['submiturl']     = 'index.php?mod=messageTemplate&act=tplDataSubmit';
                $returnarr['sec_menueid']   = 2;
                break;
            default:
                $returnarr['platformid']    = 2;
                $returnarr['gobackurl']     = 'index.php?mod=messageTemplate&act=showTemplateListAli';
                $returnarr['submiturl']     = 'index.php?mod=messageTemplate&act=tplDataSubmitAli';
                $returnarr['sec_menueid']   = 3;
                $returnarr['editUrl']       = 'index.php?mod=messageTemplate&act=editTemplateFormAli';
                break;
        }
        return $returnarr;
    }
    
    /*
     * 速卖通运输方式模板列表
     */
    public function view_aliShipingTemplate(){
        $lp_obj         = new LocalPowerModel();
        $powerlist      = $lp_obj->getAliPowerlist($_SESSION['userId']);
        $accountlist    = $lp_obj->getAccountListByCatList($powerlist[field]);
        $accountlist[]  = -1;
        $condition_sql  = implode("' , '", $accountlist);
        $condition      = " and account in ('$condition_sql')";
        $shipTplMod_obj = new AliShipTemplateModel();
        $tplList        = $shipTplMod_obj->getAllTemplateInfoList($condition);
        $aliaccount_obj = new AliAccountModel();
        foreach ($tplList as &$tval){
            $tval['accountname']   = $aliaccount_obj->accountId2Name($tval['account']);
        }
        $this->smarty->assign('tpllist', $tplList);
        $this->smarty->assign('addurl', 'index.php?mod=messageTemplate&act=aliAddNewShipTemplate');
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('toptitle', 'message模板列表');
        $this->smarty->assign('third_menue', 1);
        $this->smarty->display('alishiptpl.htm');
    }
    
    /*
     * 速卖通 添加模板
     */
    public function view_aliAddNewShipTemplate(){
        $aliAccount_obj = new AliAccountModel();
        $accountlist    = $aliAccount_obj->getAllAliAccountList('name','asc');
        $this->smarty->assign('accountlist', $accountlist);
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('third_menue', 1);
        $this->smarty->assign('toptitle', '速卖通添加运费模板');
        $this->smarty->display('alishipeditform.htm');
    }
    
    /*
     * 数据提交 模板管理
     */
    public function view_aliAddNewShipTplSubmit(){
        $account    = isset($_POST['aliaccount']) ? trim($_POST['aliaccount']) : FALSE;
        if (empty($account)) {
        	$msgdata = array('data'=>array('请指定账号!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        }
        if (!isset($_FILES['tpl'])) {
        	$msgdata = array('data'=>array('请上传模板文件!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        }
        if ($_FILES['tpl']['error'] != 0) {                                         //文件上传出错
            $msgdata = array('data'=>array('文件上传出错!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        }
        $fileSuffix = getFileSuffix($_FILES['tpl']['name']);
        if ($fileSuffix != '.zip') {
        	$msgdata = array('data'=>array('请上传zip格式文件!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        }
        $AliShipModel_obj   = new AliShipTemplateModel();
        $storePath          = $AliShipModel_obj->storeTplFile($_FILES['tpl']['tmp_name']);
        if (FALSE === $storePath) {                                                 //存储失败
        	$msgdata = array('data'=>array(AliShipTemplateModel::$errMsg), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        }
        $result = $AliShipModel_obj->insertRelationShip($account, $storePath, $_FILES['tpl']['name']);
        if (FALSE === $result) {
        	$msgdata = array('data'=>array('数据库操作错误!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goErrMsgPage($msgdata);
            exit;
        } else {
            $msgdata = array('data'=>array('操作成功!'), 'link'=>'index.php?mod=messageTemplate&act=aliShipingTemplate');
            goOkMsgPage($msgdata);
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
     *速卖通运输方式模板管理
     */
    public function view_allocationTplList(){
        $alitplmodel_obj    = new AliShipTemplateModel();
        $aliAcount_obj      = new AliAccountModel();
        $accountlist        = $aliAcount_obj->getAllAliAccountList('name', 'asc');
        $accoutnShipRel     = array();                                                        //账号模板关联数组
        $accountTplSet      = array();
        foreach ($accountlist as $key=>$val){
            $accoutnShipRel[$key]   = $alitplmodel_obj->getTplListByAccount($key);
            $accountTplSet[$key]    = ($row=$alitplmodel_obj->getTplRow($key)) ? $row['tplid'] : 0; 
        }
        
        $this->smarty->assign('accountlist',$accountlist);
        $this->smarty->assign('ac_tpllist', $accoutnShipRel);
        $this->smarty->assign('acsettpl', $accountTplSet);
        $this->smarty->assign('toplevel', 2);
        $this->smarty->assign('sec_menue', 4);
        $this->smarty->assign('third_menue', 2);
        $this->smarty->assign('toptitle', '运输模板分配');
        $this->smarty->display('alishiptplallocation.htm');
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
}




