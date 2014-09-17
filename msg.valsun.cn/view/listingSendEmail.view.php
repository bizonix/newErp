<?php
/*
 * listing移除邮件推送列表页面
 */

class ListingSendEmailView extends BaseView {
    
    /*
     * 构造函数
     */
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * listing推送列表
     */
    public function view_index(){
    	$listModel = new ListingSendEmailModel();
    	$pagesize  = 20;
    	$totalNum  = $listModel->getAllMoveListingCount();//总数
    	$pageObj   = new Page($totalNum, $pagesize);
    	$listInfo  = $listModel->getAllMoveListing($pageObj->limit);
     	foreach($listInfo as $k => $v){
     		$itemId 			= $v['itemId'];
     		$detail[$itemId] 	= $listModel->getListingDetail($itemId);
     	}
    	if ($totalNum > $pagesize) {       //分页
            $pagestr =  $pageObj->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pageObj->fpage(array(0, 2, 3));
        }
        $this->smarty->assign('pagestr', $pagestr);
        $this->smarty->assign('listInfo', $listInfo);
        $this->smarty->assign('detail', $detail);
        $this->smarty->assign('toptitle', 'listing移除推送列表');
    	$this->smarty->display('listingsendemail.htm');
    }
	
    /**
     * 添加新的ItemId
     */
    public function view_ajaxInsertItemId(){
    	$itemId = isset($_GET['itemId']) ? trim($_GET['itemId']) : FALSE;//标题
    	if(isset($itemId)){
    		$list = new ListingSendEmailModel();
    		$isCount = $list->isExistItemId($itemId);
    		if($isCount != 0){
    			$result['code'] = '201';
    			$result['msg']  = 'ItemID已存在';
    			echo json_encode($result);
            	exit;
    		}else{
    			$rtn = $list->addNewListing($itemId);
    			switch($rtn){
    				case '1':
    					$result['code'] = '1';
    					$result['msg']  = '接口调用有误';
    					break;
    				case '2':
    					$result['code'] = '2';
    					$result['msg']  = '参数传递有误';
						break;
    				case '3':
    					$result['code'] = '3';
    					$result['msg']  = '检索不到买家记录';
						break;
    				case '200':
    					$result['code'] = '200';
    					$result['msg']  = 'ItemID添加成功';
    					break;
    				case '201':
    					$result['code'] = '200';
    					$result['msg']  = 'ItemID添加失败';
    					break;
    				default:
    					$result['code'] = '000';
    					$result['msg']  = '未定义';
    					break;
    			}
    			echo json_encode($result);
    		}
    	}
    }
    
     /**
     * 添加邮箱账号
     */
    public function view_ajaxInsertEbayAccount(){
    	$ebayAccount 	= isset($_GET['ebayAccount']) ? trim($_GET['ebayAccount']) : FALSE;
    	$ebayEmail 		= isset($_GET['ebayEmail']) ? trim($_GET['ebayEmail']) : FALSE;
    	$passWord 		= isset($_GET['passWord']) ? trim($_GET['passWord']) : FALSE;
    	if(empty($ebayAccount) || empty($ebayEmail) || empty($passWord)){
    		$result['code'] = '404';
    		$result['msg']  = '参数有误';
    		echo json_encode($result);
    		exit();
    	}
    	$list 	= new ListingSendEmailModel();
    	$rtn 	= $list->addEbayAccount($ebayAccount, $ebayEmail, $passWord);
    	switch($rtn){
    		case '0':
    			$result['code'] = '0';
    			$result['msg']  = '账号信息已存在';
    			break;
    		case '200':
    			$result['code'] = '200';
    			$result['msg']  = '账号信息添加成功';
    			break;
    		case '201':
    			$result['code'] = '201';
    			$result['msg']  = '账号信息添加失败';
    			break;
    		default:
    			$result['code'] = '300';
    			$result['msg']  = '未定义';
    			break;
    	}
    	echo json_encode($result);
    }
    	
}
