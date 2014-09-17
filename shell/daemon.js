var p = require('procstreams');
var exec = require('child_process').exec;
var daemonObj = require("/data/shell/config");
var daemon_arr = daemonObj.daemon_arr;
//console.log(daemon_arr);

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
						//console.log(out2);
					});
				} 
			})
		})();
	}
},1000*2);


