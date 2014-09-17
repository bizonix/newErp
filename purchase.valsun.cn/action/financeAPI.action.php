<?php
/**
 * 类名: FinanceAPIAct
 * 功能：采购系统与财务系统交互业务逻辑类,返回数据到财务系统
 * 版本：1.0 
 * 日期：2014-03-03
 * 作者：王民伟
 */
class FinanceAPIAct {
  public static $errCode	 = 0;
	public static $errMsg	   = "";

	//根据采购订单号获取订单信息返回财务系统
  public function getPurOrderInfo(){
    $orderList  = isset($_GET['ordersn']) ? $_GET['ordersn'] : '';
    $data       = FinanceAPIModel::getPurOrderInfo($orderList);
    return json_encode($data);
  }

  //返回需要同步的供应商数据行数,用于分页请求
  public function getSupplierCount(){
      $count   = FinanceAPIModel::getSupplierCount();
      return $count;
  }

  //返回供应商信息
 	public function getSupplierInfo(){ 
      $page      = isset($_GET['page']) ? $_GET['page'] : 1;//申请页数
      $pagenum   = isset($_GET['pagenum']) ? $_GET['pagenum'] : 200;//申请每页行数
  		$data		   = FinanceAPIModel::getSupplierInfo($page, $pagenum);
      return json_encode($data);
  }

  //根据供应商名称返回供应商基础信息
  public function getSupplierInfoByName(){
      $name      = isset($_GET['companyname']) ? $_GET['companyname'] : '';
      $data      = FinanceAPIModel::getSupplierInfoByName($name);
      return json_encode($data);
  }

  //返回当天采购到货入库记录数
  public static function getPurInStockCount(){
      $data     = FinanceAPIModel::getPurInStockCount();
      return json_encode($data);
  }

  //返回当天采购入库信息,作为财务软件外购入库信息 2014-03-13
  public static function getPurInStockInfo(){
      $page      = isset($_GET['page']) ? $_GET['page'] : 1;//申请页数
      $pagenum   = isset($_GET['pagenum']) ? $_GET['pagenum'] : 200;//申请每页行数
      $data     = FinanceAPIModel::getPurInStockInfo($page, $pagenum);
      return json_encode($data);
  }

  //更新财务系统推送过来已付款的订单号
  public static function updHasPayOrder(){
      $orderArr = isset($_GET['orderArr']) ? $_GET['orderArr'] : '';
      if(!empty($orderArr)){
          $idArr    = json_decode($orderArr, true);
          $rtnData  = FinanceAPIModel::updHasPayOrder($idArr);
          if($rtnData){
              self::$errCode = '200';
          }else{
              self::$errCode = '201';
          }
      }else{
          self::$errCode = '404';
      }
      return self::$errCode;
  }
}
?>
