#!/bin/sh
#统一抓取ebay漏邮件  用法 ./xxx.sh [分钟数]
accounts=( 	#要处理的账号
	cn1501287427
	cn1500439756
	cn1501595926
	cn1501540493
	cn1001711574
	cn1501578304
	cn1510304665
	cn1500053754
	cn1500053764
	cn1501288484
	cn1501288533
	cn1500688658
	cn1500688776
	cn1500514393
	cn1500514645
	cn1500225927
	cn1500226033
	cn1500293372
	cn1500293467
	cn1500152269
	cn1500152370
	cn1501578269
	cn1501595496
	cn1500439632
	cn1501287406
	cn1501534536
	cn1501637888
	cn1501638006
	cn1001711552
	3acyber
	szsunweb
	cn1000421358
	cn1000616054
	beauty365
	cn1000960806
	caracc88
	cn1000983412
	bagfashion789
	cn1000983826
	cn1000999030
	cn1001315312
	cn1001428059
	szfinejo
	cn1001392417
	cn1001377688
	cn1001424576
	cn1001379555
	cn1001656836
	cn1001718385
	cn1001718610
	cn1510513243
	cn1510515579
	cn1510506505
	cn1510509503
	cn1510509744
	cn1510509429
	cn1510517588
	cn1510514024
	cn1510886356
	cn1510891016
	cn1510893085
	cn1510895038
	cn1510930486
	cn1510890054
	cn1510893199
	cn1510893515
	)
minutes=$1
if [ "$minutes" == "" ]
then
	echo '需指定时间'
	exit
fi
length=${#accounts[*]}											#账号数量
command='php' 													#运行命令
script='/data/web/msg.valsun.cn/crontab/fetch_ali_message.php'     #脚本目录
echo "当前执行命令 === "$command
echo "当前执行脚本 === "$script
echo ''
echo "======  开始执行  ====== "
for((i=0; i<length; i++))
do
  echo '当前处理第 '$i' 个账号'
  echo '====== 当前处理账号：'${accounts[i]}' ======='
  echo '执行命令 ==> '$command $script ${accounts[i]} $minutes
  $command $script ${accounts[i]} $minutes
done 