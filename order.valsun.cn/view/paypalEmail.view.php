<?php
/**
 * paypal邮箱管理
 * @author heminghua
 */
class paypalEmailView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 列表显示页
     */
    public function view_paypalEmail() {
        global $memc_obj;
		
        $pagesize = 30;    //页面大小
		$where = "";
        $lists = paypalEmailModel::selectList($where);

		$num = count($lists);
		$pager = new Page($num,$pagesize);
		$lists = paypalEmailModel::selectList($where." ".$pager->limit);
		
		foreach($lists  as $key=> $value){
			$msg = paypalEmailModel::selectMsg($value['accountId']);
			$list[$key]['id'] = $value['id'];
			$list[$key]['status'] = $value['status'];
			$list[$key]['email'] = $value['email'];
			$list[$key]['account'] = $msg[0]['account'];
			$list[$key]['plateform'] = $msg[0]['platform'];
		}
		$this->smarty->assign("lists",$list);
		if ($num > $pagesize) {       //分页
            $pagestr =  $pager->fpage(array(0, 2, 3, 4, 5, 6, 7, 8, 9));
        } else {
            $pagestr =  $pager->fpage(array(0, 2, 3));
        }
		$this->smarty->assign('pagestr', $pagestr);
		$data = isset($_POST['data'])?$_POST['data']:"";
		$this->smarty->assign("successLog",$data);

        $toptitle = 'paypal邮箱管理页面';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		
        $navlist = array(
            array('url' => '', 'title' => '系统设置'),
            array('url' => '', 'title' => 'paypal邮箱管理'),
        );
        $this->smarty->assign('navlist', $navlist);
		
        $toplevel = 3;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = '33';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		
        $this->smarty->display('paypalEmail.htm');
    }
	public function view_addNewEmail(){
		$toptitle = '添加paypal邮箱';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		
        $navlist = array(
            array('url' => '', 'title' => '系统设置'),
            array('url' => 'index.php?act=paypalEmail&mod=paypalEmail', 'title' => 'paypal邮箱管理'),
            array('url' => '', 'title' => '添加paypal邮箱'),
        );
        $this->smarty->assign('navlist', $navlist);
		
		$lists = paypalEmailModel::selectAccount();
		$this->smarty->assign("lists",$lists);
		$this->smarty->assign("type","add");
        $toplevel = 3;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = '33';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		
        $this->smarty->display('addPaypalEmail.htm');
	}
	public function view_paypalEmailModify(){
		$toptitle = '修改paypal邮箱';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		
        $navlist = array(
            array('url' => '', 'title' => '系统设置'),
            array('url' => 'index.php?act=paypalEmail&mod=paypalEmail', 'title' => 'paypal邮箱管理'),
            array('url' => '', 'title' => '修改paypal邮箱'),
        );
        $this->smarty->assign('navlist', $navlist);
		
		$id = isset($_GET['id'])?trim($_GET['id']):"";
		$this->smarty->assign("id",$id);
		//echo "##".$id."##";
		$where = "WHERE id={$id}";
		$detail = paypalEmailModel::selectList($where);
		$accountId = $detail[0]['accountId'];
	    $email = $detail[0]['email'];
	    $status = $detail[0]['status'];
		//echo $status;
		$lists = paypalEmailModel::selectAccount();
		$this->smarty->assign("lists",$lists);
		$this->smarty->assign("type","modify");
		$this->smarty->assign("accountId",$accountId);
		$this->smarty->assign("email",$email);
		$this->smarty->assign("enable",$status);
        $toplevel = 3;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = '33';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		
        $this->smarty->display('addPaypalEmail.htm');
	}
}   