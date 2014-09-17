<?php
/**
 *清除停售零库存料号仓位
 * @author Gary
 */
class clearSkuLocationView extends CommonView {
    private $topLevel;
    private $firstTile;
    private $topTitle;
    /*
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
        $this->topLevel =   0;
        $this->firstTile =   '清空料号仓位';
        $this->topTitle =   '清空料号仓位';
    }
    
    /*
     *填写停售sku 
     */
    public function view_clearIndex(){
        self::bulidNav($this->topTitle, 110, 0);
        $msg    =   $_GET['msg'];
        $this->smarty->assign('msg', $msg);
        $this->smarty->display('ClearIndex.htm');
    }
	
	 /*
     *处理提交的sku
     */
	public function view_clearSkuPost(){
	    $skus      =   explode("\r\n", trim($_POST['skus']));
        $skus      =   array_filter($skus);
        if(empty($skus)){
            header("location:index.php?mod=clearSkuLocation&act=clearIndex&msg=请填写停售料号");
            exit;
        }
        $clearSku   =   new clearSkuLocationAct();
        $res        =   $clearSku->process_sku($skus);
        
		$this->smarty->assign('res', $res);
        
        self::bulidNav('停售料号仓位清空', 110, 0);	
        $this->smarty->display('showClearRes.htm');
	}
	
	/**
     * clearSkuLocationView::bulidNav()
     * 构建面包屑及二级菜单等相关信息
     * @param mixed $topTitle 标题
     * @param mixed $secondTitle 二级菜单名
     * @param string $secondLevel 二级菜单序号
     * @param mixed $topLevel   一级菜单序号
     * @param mixed $firstTitle 一级菜单名
     * @return void
     */
    public function bulidNav($topTitle, $secondLevel = '', $topLevel = '', $firstTitle = ''){
        
        $topLevel       =   $topLevel ? $topLevel : $this->topLevel; //一级菜单的序号  0 开始
        $firstTitle     =   $firstTitle ? $firstTitle : $this->firstTile;
        		
		$navlist        =   array(  //面包屑
                        			array('url' => 'index.php?mod=skuStock&act=searchSku', 'title' => '库存管理'),
                                    array('url' => '', 'title' => $firstTitle)
                        		);
		$secondlevel    =    $secondLevel ? $secondLevel : 110;

        $this->smarty->assign('toptitle', $topTitle);  //标题 
        $this->smarty->assign('navlist', $navlist);
        $this->smarty->assign('toplevel', $topLevel);
        $this->smarty->assign('secondlevel', $secondlevel);
    }
}
?>