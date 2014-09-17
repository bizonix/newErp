#!/bin/sh
if test $( pgrep -f 'sync_print_info.php' | wc -l ) -eq 0
then
	/usr/local/bin/php /data/web/order.valsun.cn/crontab/sync_print_info.php >> /home/ebay_order_cronjob_logs/sync_print_info/sync_print_info.log
fi
