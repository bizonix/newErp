<?php
/**
*功能：定义有关运输方式选择策略所用的常量
*版本：2014-08-08
*作者：zqt
*拿来当作相关配置文件
* 其中'TRANSPORT_STRATEGY_DIMENSION_ARRAY' 对应的值为账号约束条件的维度数组
* 'TRANSPORT_STRATEGY_BEST_TIME_ARRAY' 对应的值为最佳时效运输方式设置，其中
* 'allowType' 允许满足的类型，1为满足任意一项，2为满足所有条件（只有两个条件，symbolCondition1,symbolCondition2这两个）
* 'symbolCondition1' 条件一的数字符号，允许为 >,<,>=,<=;（前端是select选择）;
* 'digitCondition1'  条件一的数字，类型为decimal(10,2);
* 'symbolCondition2' 条件2，类比'symbolCondition1';
* 'digitCondition2' 条件2，类比'digitCondition1';
* 'transportId' 最佳运输方式id
*/

if (!defined('WEB_PATH')) exit();
//全局配置信息
return  array(
	"TRANSPORT_STRATEGY_DIMENSION_ARRAY" => array('1'=>'国家','2'=>'币种','3'=>'金额','4'=>'运输方式'),//账号约束条件维度数组
    "TRANSPORT_STRATEGY_CURRENCY_ARRAY" => array('1'=>array('CN'=>'美元','EN'=>'USD','symbol'=>'$'),'2'=>array('CN'=>'澳大利亚元','EN'=>'AUD','symbol'=>''),'3'=>array('CN'=>'加元','EN'=>'CAD','symbol'=>''),'4'=>array('CN'=>'捷克克朗','EN'=>'CZK','symbol'=>''),'5'=>array('CN'=>'欧元','EN'=>'EUR','symbol'=>''),'6'=>array('CN'=>'英镑','EN'=>'GBP','symbol'=>''),'7'=>array('CN'=>'菲律宾比索','EN'=>'PHP','symbol'=>''),'8'=>array('CN'=>'瑞典克朗','EN'=>'SEK','symbol'=>''),'9'=>array('CN'=>'新币','EN'=>'SGD','symbol'=>'')),//币种对应id及相关数组
	"TRANSPORT_STRATEGY_BEST_TIME_ARRAY" => array('allowType'=>'1','symbolCondition1'=>'>=','digitCondition1'=>'0','symbolCondition2'=>'<=','digitCondition2'=>'0','transportId'=>'1'),//最佳时效运输方式数组
    "TRANSPORT_STRATEGY_PRIORITY" => array('1'=>'1','2'=>'2','3'=>'3'),//优先级数组
);

?>