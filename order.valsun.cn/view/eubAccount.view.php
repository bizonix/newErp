<?php
/**
 * EUB授权设置
 * @author heminghua
 */
class eubAccountView extends BaseView {
    /*
     * 构造函数
     */

    public function __construct() {
    	parent::__construct();
    }

    /*
     * 列表显示页
     */
    public function view_eubAccount() {
        global $memc_obj;
		
        
		

        $toptitle = 'EUB授权设置';             //头部title
        $this->smarty->assign('toptitle', $toptitle);
		
        $navlist = array(
            array('url' => '', 'title' => '系统设置'),
            array('url' => 'index.php?mod=omAccount&act=getAccountList', 'title' => '账号管理'),
            array('url' => '', 'title' => 'EUB授权设置'),
        );
        $this->smarty->assign('navlist', $navlist);
		
        $toplevel = 3;      //一级菜单的序号  0 开始
        $this->smarty->assign('toplevel', $toplevel);

        $secondlevel = '32';   //当前的二级菜单
        $this->smarty->assign('secondlevel', $secondlevel);
		$id = isset($_GET['id'])?trim($_GET['id']):"";
		$this->smarty->assign("id",$id);
		$data = isset($_GET['data'])?trim($_GET['data']):"";
		$this->smarty->assign("successLog",$data);
		$where = "where accountId={$id}";
		$lists = eubAccountModel::selectList($where);
		//print_r($lists);
		foreach($lists as $list){
			$this->smarty->assign('account',$list['account']);
			$this->smarty->assign('dev_id',$list['dev_id']);
			$this->smarty->assign('dev_sig',$list['dev_sig']);
			$this->smarty->assign('pname',$list['pname']);
			$this->smarty->assign('pcompany',$list['pcompany']);
			$this->smarty->assign('pcountry',$list['pcountry']);
			$this->smarty->assign('pprovince',$list['pprovince']);
			$this->smarty->assign('pcity',$list['pcity']);
			$this->smarty->assign('pdis',$list['pdis']);
			$this->smarty->assign('pstreet',$list['pstreet']);
			$this->smarty->assign('pzip',$list['pzip']);
			$this->smarty->assign('ptel',$list['ptel']);
			$this->smarty->assign('ptel2',$list['pte1']);
			$this->smarty->assign('pemail',$list['pemail']);
			$this->smarty->assign('dname',$list['dname']);
			$this->smarty->assign('dcompany',$list['dcompany']);
			$this->smarty->assign('dcountry',$list['dcountry']);
			$this->smarty->assign('dprovince',$list['dprovince']);
			$this->smarty->assign('dcity',$list['dcity']);
			$this->smarty->assign('ddis',$list['ddis']);
			$this->smarty->assign('dstreet',$list['dstreet']);
			$this->smarty->assign('dzip',$list['dzip']);
			$this->smarty->assign('dtel',$list['dtel']);
			$this->smarty->assign('demail',$list['demail']);
			$this->smarty->assign('shiptype',$list['shiptype']);
			echo $list['shiptype'];
			$this->smarty->assign('rname',$list['rname']);
			$this->smarty->assign('rcompany',$list['rcompany']);
			$this->smarty->assign('rcountry',$list['rcountry']);
			$this->smarty->assign('rprovince',$list['rprovince']);
			$this->smarty->assign('rcity',$list['rcity']);
			$this->smarty->assign('rdis',$list['rdis']);
			$this->smarty->assign('rstreet',$list['rstreet']);
		}
		
        $this->smarty->display('eubAccount.htm');
    }

}   