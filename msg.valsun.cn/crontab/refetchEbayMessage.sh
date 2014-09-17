#!/bin/sh
#统一抓取ebay漏邮件  用法 ./xxx.sh [分钟数]
accounts=(easyshopping095 	#要处理的账号
	  beromantic520 
	  cndirectstore
	  allbestforu
	  easydealsmall
	  emallzone
	  enjoytrade99
	  eseasky68
	  eshoppingstar75
	  happyzone80 
	  swzeagoo 
	  ulifestar 
	  unicecho 
	  vobeau 
	  ishoppingclub68 
	  infourseas 
	  360beauty 
	  voguebase55 
	  sunwebhome 
	  estore456 
	  greatdeal456 
	  linemall 
	  365digital 
	  work4best 
	  hotdeal77 
	  cafase88 
	  befashion 
	  doeon 
	  freemart21cn 
	  mysoulfor 
	  newcandy789 
	  enicer 
	  choiceroad 
	  easydealhere 
	  sunwebzone 
	  easebon 
	  bestinthebox 
	  niceinthebox 
	  befdimall 
	  dresslink 
	  dealinthebox 
	  zealdora 
	  okmart88 
	  tradekoo 
	  elerose88 
	  cndirect55 
	  starangle88 
	  easydeal365 
	  utarget88 
	  betterdeals255 
	  estore2099 
	  itshotsale77 
	  easytrade2099 
	  happydeal88 
	  ishop2099 
	  eshop2098 
	  futurestar99 
	  befdi 
	  enjoy24hours 
	  enjoydeal99 
	  wellchange 
	  charmday88 
	  etrade77 
	  fiveseason88 
	  goonline55 
	  cndirect998 
	  easyshopping678 
	  keyhere 
	  betterlift99 
	  niceforu365 
	  digitalzone88 
	  worlddepot 
	  efashionforu
	  etradestar58
	  happyforu19 
	  cooforu 
	  ecnonline
	  edealsmart
	)
minutes=$1
if [ "$minutes" == "" ]
then
	echo '需指定时间'
	exit
fi
length=${#accounts[*]}											#账号数量
command='php' 													#运行命令
script='/data/web/msg.valsun.cn/crontab/fetch_messages.php'     #脚本目录
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