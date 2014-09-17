<?php
/*
 * 评价内容管理的类
 */
class contentView extends BaseView{  
	
	//评价内容模板列表
    public function view_contentList(){
		$keyword  	= isset($_GET['keyword']) ? post_check($_GET['keyword']) : '';		
		$where  = 'where a.is_delete=0 ';
		if($keyword){
			$where  .= " and content like '%$keyword%' ";
			$this->smarty->assign('keyword',$keyword);
		}		
		$cntAct 	= new contentAct();		
		$total 	 	= $cntAct->act_getContentNum($where);		
		$num      = 50;//每页显示的个数
		$page     = new Page($total,$num,'','CN');
		$where   .= " order by a.id desc ".$page->limit;			
		$contentList  = $cntAct->act_getContentList('a.*,b.global_user_name as addUser',$where);		
		if(!empty($_GET['page'])) {
			if(intval($_GET['page'])<=1 || intval($_GET['page'])>ceil($total/$num)) {
				$n=1;
			} else {
				$n=(intval($_GET['page'])-1)*$num+1;
			}
		}else{
			$n=1;
		}
		if($total>$num) {
			//输出分页显示
			$show_page = $page->fpage(array(0,2,3,4,5,6,7,8,9));
		} else {
			$show_page = $page->fpage(array(0,2,3));
		}		
		$this->smarty->assign('show_page',$show_page);		
		$this->smarty->assign('contentList',$contentList);
		//var_dump($contentList);
		//$accAct 	 = new AccountAct();
		//$accountList = $accAct->act_getAccountList('id,account','where platformId = 2 and is_delete = 0');	
		//$this->smarty->assign('accountList',$accountList);			
		$this->smarty->assign('state',$state);		
		//$this->smarty->assign('secnev','1');               //二级导航
		$this->smarty->assign('module','SKU等待领取');
		$this->smarty->assign('username',$_SESSION['userName']);		
		$navarr = array("<a href='index.php?mod=FeedbackManage&act=fbkList'>卖家评价</a>",">>","评价列表");
        $this->smarty->assign('navarr',$navarr);		
		$this->smarty->display('contentList.htm');
    }
    
    //添加评价内容
    public function view_contentAdd(){   
    	$this->smarty->display('contentAdd.htm');
    }
    
    //修改评价内容
    public function view_contentModify(){
    	$contentId  	= isset($_GET['id']) ? post_check($_GET['id']) : '';
    	$content		= '';
    	if ($contentId != '') {
    		$cntAct 	= new contentAct();
    		$content 	= $cntAct->act_getContentList('*',"where id = '$contentId'");  
    		//print_r($content);  		
    	}
    	$this->smarty->assign('contentId',$contentId);
    	$this->smarty->assign('content',$content[0]['content']);     	
    	$this->smarty->display('contentModify.htm');
    }
	
	
}