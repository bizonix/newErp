var p = require('procstreams');
var exec = require('child_process').exec;
var $ = require('jquery');
//var daemonObj = require("/data/shell/config");
//var daemon_arr = daemonObj.daemon_arr;
//console.log(daemon_arr);
var mysql = require('mysql');
var pool = mysql.createPool({
    host: 'localhost',
    user: 'cerp',
    password: '123456',
    database: 'cerp',
    port: 3306
});
console.log($);

var selectSQL ="SELECT account FROM `amazon_account`";
var cmdShell = "/usr/bin/php /data/web/erp.valsun.cn/amazon/src/MarketplaceWebServiceProducts/Samples/editMypriceForSKU.php ";
pool.getConnection(function (err, conn) {
    if (err) console.log("POOL ==> " + err);
    function query(){
        conn.query(selectSQL, function (err, res) {
            console.log(new Date());
            //console.log(res);
			var accountArr = res;
			accountArr.forEach(function(item){
				(function(){ //弄个闭包保存变量不受影响
					var sql = "SELECT count(*) as totalnum ,account from amazon_listing_info where account='"+item.account+"'";
					conn.query(sql,function(aerr,ares){
						console.log(ares);
						ares.forEach(function(item){
							var length = Math.ceil(item.totalnum/100);
							console.log(item.account+":"+length);
							for(var i = 0;i < length;i++){
								var newcmdShell = cmdShell + item.account + " " + i;
								exec(newcmdShell, function (err2, out2) {
									console.log(out2);
								});
							}
						});
					});
				})();

			});
            conn.release();
        });
    }
    query();
    setInterval(query, 1000*60*10); //10分钟跑一次
});

/*
setInterval(function(){
	for(var i=0;i<daemon_arr.length;i++){
		(function(){
			var shell_str ,restart_shell,num;
			shell_str = "ps -ef | grep "+daemon_arr[i].name+ " | grep -v 'grep' | wc -l";
			if(daemon_arr[i].log != ""){
				restart_shell = daemon_arr[i].cmd+" "+daemon_arr[i].file +" >> " + daemon_arr[i].log;
			}else{
				restart_shell = daemon_arr[i].cmd+" "+daemon_arr[i].file ;
			}
			num = daemon_arr[i].number;
			//console.log(restart_shell);
			console.log(shell_str);
			exec(shell_str, function (err, output,stderr) {
				if (err) throw err;
				if (output >= num){
					console.log('exist,don\'t need restart');
				}else{
					console.log(shell_str);
					exec(restart_shell, function (err2, out2) {
						console.log(restart_shell + "  restart now....");
					});
				} 
			})
		})();
	}
},1000*2);
*/


