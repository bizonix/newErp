<?php
/**
 * 与运输方式处理相关的类
 * 作者 涂兴隆
 */

class ShipingTypeModel
{
    public static $shippinglistinfo = NULL;
    public static $channellistinfo = NULL;
    /*
     * 获取全部的运输方式列表信息 同过api到运输方式管理系统获取
     */
    public static function getShipingTypeList ()
    {
        /*
         * 需向运输方式管理系统获取数据
         */
        global $memc_obj;
        if (self::$shippinglistinfo == NULL){
            self::$shippinglistinfo = $memc_obj->get_extral('trans_system_carrier');
        }
        return self::$shippinglistinfo;
    }
    
    /*
     * 运输方式id到运输方式名称映射函数 $id unsigned int 运输方式id
     */
    public static function getShipingNameById ($id)
    {
        $shippinglist = self::getShipingTypeList();
        return   $shippinglist[$id]['carrierNameCn'];
    }
    
    /*
     * 判断运输方式id是否是快递部门发货的id 
     * $shipingid 运输方式id 
     * 是返回 true 否 返回false
     */
    public static function isExpressShiping ($shipingid)
    {
        $exshiping = self::getExShipingId();
        if (in_array($shipingid, $exshiping)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 判断运输方式id是否是小包部门发货的id 
     * $shipingid 运输方式id 
     * 是返回 true 否 返回false
     */
    public static function isSmallpressShiping ($shipingid)
    {
        $small = self::getSmShipingId();
        if (in_array($shipingid, $small)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 判断运输方式id是否是国内部门发货的id
    * $shipingid 运输方式id
    * 是返回 true 否 返回false
    */
    public static function isInlandShiping ($shipingid)
    {
        $small = self::getInlandShippingId();
        if (in_array($shipingid, $small)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 获得属于快递的运输方式id数组
     */
    public static function getExShipingId(){
        return array(7,8,9,5,11,12,13,14,48,49,50,51,54,55,64,66,67,56,57,73);
    }
    
    /*
     * 获得属于小包的运输方式id数组
    */
    public static function getSmShipingId(){
        return array(1,2,3,4,10,6,27,28,29,39,40);
    }
    
    /*
     * 获取国内运输id数组
     */
    public static function getInlandShippingId(){
        return array(11,12,13,14);
    }
    
    /*
     * 订单类型id到描述字符串的转换函数
     */
    public static function typeIdTostr($typeid){
        switch ($typeid){
        	case 1:
        	    return '发货单';
        	    break;
        	case 2:
        	    return '配货单';
        }
        return '';
    }
    
    /*
     * 获得所有渠道列表信息
     */
    public static function getChannelInfolist(){
        global $memc_obj;
        if(self::$channellistinfo == NULL){
           self::$channellistinfo =  $memc_obj->get_extral('trans_system_channelinfo');
        }
        return self::$channellistinfo;
    }
    
    /*
     * 根据运输方式id和渠道id获得渠道信息
     * $shipid 运输方式id $channelid渠道id
     */
    public static function getChannelInfoWithID($shipid, $channelid){
        $chlist = self::getChannelInfolist();
        //print_r($chlist);exit;
        if ( array_key_exists($shipid, $chlist) && array_key_exists($channelid, $chlist[$shipid]) ) {
        	return $chlist[$shipid][$channelid];
        } else {
            return array();
        }
    }
    
    /*
     * 根据渠道id获得渠道名称
     * $shipid $channelid   运输方式id  渠道id
     */
    public static function getChannelNameByIds($shipid, $channelid){
        $channelinfo = self::getChannelInfoWithID($shipid, $channelid);
        if(empty($channelinfo)){
            return '';
        } else {
            return $channelinfo['channelName'];
        }
    }
}

