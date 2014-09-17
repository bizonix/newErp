var p = require('procstreams');
var exec = require('child_process').exec;
var daemonObj = require("/data/shell/config");
var daemon_arr = daemonObj.daemon_arr;
//console.log(daemon_arr);

setInterval(function(){
	for(var i=0;i<daemon_arr.length;i++){
		(function(){
			var shell_str ,restart_shell,num;
			shell_str = "kill -9 `ps -aef | grep '"+daemon_arr[i].name+"' | grep -v grep | awk '{print $2}'`";
			console.log(shell_str);
			exec(shell_str, function (err, output,stderr) {
				console.log(daemon_arr[i].name + "is been killed........");
			});
		})();
	}
},10000*30);


