<?php
	define('SCRIPT_ROOT','/data/web/erpNew/order.valsun.cn/crontab/');	
	define('SCRIPT_ROOT_LOG','/data/web/erpNew/order.valsun.cn/crontab/ebay_order_cronjob_logs/');
	define('EBAY_RAW_DATA_PATH','/data/web/erpNew/order.valsun.cn/crontab/ebay_order_cronjob_raw_data/');
	define('WEB_PATH','/data/web/erpNew/order.valsun.cn/');
	define('WEB_PATH_LIB','/data/web/erpNew/order.valsun.cn/lib/');
	define('HTML_INCLUDE','/home/html_include/');
	//########configuration[php fetch_certain_php]##########
	define('PHP_CMD_FETCH_ORDER_CERTAIN',' /usr/bin/php '.SCRIPT_ROOT.'fetch_order_certain.php ');
	define('LOG_PATH_FETCH_ORDER_CERTAIN',SCRIPT_ROOT_LOG.'fetch_order_certain/%s/'.date('Y-m').'/'.date('d').'/');
?>
