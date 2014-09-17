#!/bin/sh
count=`ps -ef | grep "daemon.js" | grep -v "grep" | wc -l`
if test $count -eq 0
then
nohup /usr/local/bin/node  /data/shell/daemon.js >> /data/shell/daemon.log
fi
