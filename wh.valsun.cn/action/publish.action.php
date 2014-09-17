<?php
/**
 * PublishAct
 * 图片队列推送
 * @package 仓库系统
 * @author Gary
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class PublishAct extends Auth{
    static $errCode =   0;
    static $errMsg  =   '';
    function __construct(){
        parent::__construct();
        define('AMQP_DEBUG', false);
        //define('MQ_HOST', '115.29.188.246');
//        define('MQ_PORT', 5672);
//        define('MQ_USER', 'valsun_tran');
//        define('MQ_PASS', 'tranabc');
//        define('MQ_VHOST', 'valsun_tran');
        define('MQ_HOST', '192.168.200.198');
        define('MQ_PORT', 5672);
        define('MQ_USER', 'xiaojinhua');
        define('MQ_PASS', 'jinhua');
        define('MQ_VHOST', 'mq_vhost1');
    }
    
    public function act_receive_image(){
        $image_url  =   trim($_POST['msg']);
        if(strpos($image_url, 'image') === FALSE){
            self::$errCode  =   301;
            self::$errMsg   =   '非法参数';
            return FALSE;
        }
        $data   =   array("fileUrl"=>$image_url);
        self::publish_data($data);
    }
    
    //发布信息到队列
    function publish_data($data){
        //print_r($data);exit;
    	//$ch_arr		= array('','tran_pic_exchange','tran_pic_exchange1','tran_pic_exchange2','tran_pic_exchange3','tran_pic_exchange4','tran_pic_exchange5');
    	//$qu_arr		= array('','tran_pic_queue','tran_pic_queue1','tran_pic_queue2','tran_pic_queue3','tran_pic_queue4','tran_pic_queue5');
    	//$i			= rand(1,5);
    	$exchange 	= 'tran_pic_exchange';
    	$queue 		= 'tran_pic_queue';
    	//$exchange	= $ch_arr[$i];
    	//$queue 		= $qu_arr[$i];
    	$conn 		= new RabbitMQClass(MQ_USER, MQ_PASS, MQ_VHOST, MQ_HOST, MQ_PORT);
    	if(function_exists('write_log')) {
    		write_log('trans_pic_mq/'.date("Y-m-d").'.txt', date("Y-m-d H:i:s").":".$exchange."===".$queue."===".$data."\n");	
    	}
    	$conn->queue_publish($exchange, $data, 'direct');
        self::$errCode  =   200;
        self::$errMsg   =   '推送成功';
        return TRUE;
    }
    
}
?>