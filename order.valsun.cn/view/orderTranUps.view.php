<?php
/**
 * 类名：orderTranUpsView
 * 功能：UPS美国专线数据导入导出视图层
 * 版本：1.0
 * 日期：2014/03/01
 * 作者：管拥军
 */
class orderTranUpsView extends BaseView{

	//首页页面渲染
	public function view_index(){
		$this->smarty->assign('title','运费查询');
		$this->smarty->display('orderTranUps.htm');		
	}
	
	//查询页面渲染
	public function view_import(){
        // if (isset($_FILES['upfile']) && !empty($_FILES['upfile'])){
			// $uploadfile = date('YmdHis').'_'.rand(1,3009).".xml";
			// $fileName = WEB_PATH.'html/temp/ups_us_import/'.$uploadfile;
			// if (move_uploaded_file($_FILES['upfile']['tmp_name'], $fileName)) {
				// $filePath = $fileName;
			// }
		// }
		// if (substr($filePath,-3)!='xml') exit('导入的文件名格式错误！');
		// $carrier= 'UPS美国专线';
		// $xml 	= file_get_contents($filePath); //读取XML文件 可以是URL 
		// print_r($xml);
		// $this->smarty->assign('title','导入结果');
		// $this->smarty->display('orderTranUps.htm');	
		//$data	= XML_unserialize($xml); //返回一个数组.
	}
}
?>