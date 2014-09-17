<?php
/**
 * 类名: FinejoFactoryAct
 * 功能：芬哲服装厂ERP数据同步信息业务逻辑类
 * 版本：1.0 
 * 日期：2014-02-27
 * 作者：王民伟
 */
class FinejoFactoryAct {
  public static $errCode	 = 0;
	public static $errMsg	   = "";

	//返回需要同步的数据行数,用于分页请求
  public function getItemInfoCount(){
      $count          = FinejoFactoryModel::getItemInfoCount();
      return $count;
  }

  //根据请求类型返回信息
 	public function getItemInfo(){
      $type   = $_GET["type"];
      if($type == 'getItemInfo'){
          $page             = $_GET['page'];//早申请页数
          $pagenum          = $_GET['pagenum'];//申请每页行数
  		    $data		          = FinejoFactoryModel::getItemInfo($page, $pagenum);
          return json_encode($data);
      }
  }
}
?>
