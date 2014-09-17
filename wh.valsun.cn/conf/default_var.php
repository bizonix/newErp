<?php
/**
*功能：定义常用变量
*版本：2014-08-12
*作者：czq
*/

/**配货单状态**/
define('WAVE_WAITING_GET_GOODS',1);  //待配货
define('WAVE_PROCESS_GET_GOODS',2);  //配货中
define('WAVE_FINISH_GET_GOODS',3);   //配货完成

/********发货单状态***********/

/***小包快递通用状态***/
define('PKS_WGETGOODS', 402);     		//待配货
define('PKS_PROCESS_GET_GOODS',305); 	//配货中
define('PKS_WIQC', 403);          		//（已分拣）待复核
define('PKS_WAITING_SORTING',404); 		//（已配货）待分拣
define('PKS_PRINT_SHIPPING_INVOICE',409); //（已分区复核）待打印面单
define('PKS_WAITING_LOADING',501); 			//（已发货复核）待装车
define('PKS_DONE', 502);          			//（已装车）已发货
define('PKS_ABANDONED_INVOICE' ,900);//废弃发货单
define('PKS_UNUSUAL_SHIPPING_INVOICE', 901);    //异常发货单

/**小包发货单状态**/
define('PKS_WWEIGHING', 405);     		//(已复核)待包装称重
define('PKS_WDISTRICT', 406);     		//（已申请运输方式和跟踪信息）待分区
define('PKS_DISTRICT_CHECKING',408);		//（已分区）待分区复核
define('PKS_WAITING_SHIPPING_CHECKING',500); //（已打印面单）待发货复核        

/*       配货单流程             */
define('PKT_NORMAL', 1);        //单料号
define('PKT_COMBINATION', 2);   //多料号

/* 打印订单流程 */
define('PR_WPRINT', 1001);      //待打印
define('PR_LOCK', 1002);        //锁定
define('PR_PRINTED', 1003);     //已经打印


define('PKS_UNUSUAL', 900);       		//异常订单
define('PKS_INLANDWWEIGHING', 803); 	//国内快递待称重
?>