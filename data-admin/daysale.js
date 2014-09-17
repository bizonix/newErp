var p = require('procstreams');
var exec = require('child_process').exec;
var $ = require('jquery');
var async = require('async');
console.log(async);
//var daemonObj = require("/data/shell/config");
//var daemon_arr = daemonObj.daemon_arr;
//console.log(daemon_arr);
var mysql = require('mysql');

var conn = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '123456',
    database: 'cerp',
    port: 3306
});

var sku = "3306";
var sqls = {
    'getSkuSQL': 'SELECT * FROM ebay_goods limit 2',
    'getCombineskuSQL': "SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku LIKE '%["+sku+"]%' "
};

var tasks = ['getSkuSQL','getCombineskuSQL'];
async.eachSeries(tasks, function (item, callback) {
    console.log(item + " ==> " + sqls[item]);
    conn.query(sqls[item], function (err, res) {
		res.forEach(function(item){
			console.log();
			var sku = item.goods_sn;
			var sql = "SELECT goods_sn,goods_sncombine FROM ebay_productscombine WHERE truesku LIKE '%["+sku+"]%' "
			callback(err, sql);
		});
        
    });
	//conn.end();
}, function (err) {
    console.log("err: " + err);
});




