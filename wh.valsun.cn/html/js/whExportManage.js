/*
 * 仓库报表导出管理--脚本总汇 whExportManage.js
 * add by chenwei 2013.11.11
 */

$(function(){	
	$("input#start1.datetime, input#end1.datetime").datepicker({
		beforeShow: customRange,
		dateFormat: 'yy-mm-dd',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		currentText: '今天',
		closeText: '关闭',
		showClearButton:true,
		showButtonPanel: true
	});	
	function customRange(input) {
		return {minDate: (input.id == "end1" ? jQuery("#start1").datepicker("getDate") : null),
				maxDate: (input.id == "start1" ? jQuery("#end1").datepicker("getDate") : null)};
	}
	
	$("input#start2.datetime, input#end2.datetime").datepicker({
		beforeShow: customRange,
		dateFormat: 'yy-mm-dd',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		currentText: '今天',
		closeText: '关闭',
		showClearButton:true,
		showButtonPanel: true
	});	
	function customRange(input) {
		return {minDate: (input.id == "end2" ? jQuery("#start2").datepicker("getDate") : null),
				maxDate: (input.id == "start2" ? jQuery("#end2").datepicker("getDate") : null)};
	}
	
	$("input#start3.datetime, input#end3.datetime").datepicker({
		beforeShow: customRange,
		dateFormat: 'yy-mm-dd',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		currentText: '今天',
		closeText: '关闭',
		showClearButton:true,
		showButtonPanel: true
	});	
	function customRange(input) {
		return {minDate: (input.id == "end3" ? jQuery("#start3").datepicker("getDate") : null),
				maxDate: (input.id == "start3" ? jQuery("#end3").datepicker("getDate") : null)};
	}
		
});

function xlsbaobiao(str){
	var start = document.getElementById('start'+str).value;
	var end = document.getElementById('end'+str).value;
	var mstatus = document.getElementById('mstatus'+str);
	var starttimes = start.split('-');
	var endtimes = end.split('-');
	var starttimeTemp = starttimes[0] + '/' + starttimes[1] + '/' + starttimes[2];
	var endtimesTemp = endtimes[0] + '/' + endtimes[1] + '/' + endtimes[2];
	var inttime = Date.parse(new Date(endtimesTemp))-Date.parse(new Date(starttimeTemp));
	
	var days = inttime/86400000+1;
	
	if(days>0) {
		//价格信息表导出
		if(str==1){
			var downloaddata = '';
			mstatus.innerHTML = '';
			for(var i=0; i<days; i++){
				var date = getLocalTime((Date.parse(new Date(endtimesTemp))-i*86400000));
				downloaddata += '<a style=" background-color:inherit; color:#0092dc; border:none; padding:0;text-decoration:underline;" href="./download/priceInfo_'+date+'_'+date+'.xls" target="_blank">价格信息表导出时间_'+date+'</a><br><br>';
			}
			mstatus.innerHTML = downloaddata;
		}
				
		//组合料号价格信息表导出
		if(str==2){
			var downloaddata = '';
			mstatus.innerHTML = '';
			for(var i=0; i<days; i++){
				var date = getLocalTime((Date.parse(new Date(endtimesTemp))-i*86400000));
				downloaddata += '<a style=" background-color:inherit; color:#0092dc; border:none; padding:0;text-decoration:underline;" href="http://192.168.200.122/exportFile/everyday_priceInfo_zuHeSku/zuHeSkuPriceInfo_'+date+'_'+date+'.xls" target="_blank">组合料号价格信息表导出时间_'+date+'</a><br><br>';
			}
			mstatus.innerHTML = downloaddata;
		}
		
		//重量报表导出
		if(str==3){
			var downloaddata = '';
			mstatus.innerHTML = '';
			for(var i=0; i<days; i++){
				var date = getLocalTime((Date.parse(new Date(endtimesTemp))-i*86400000));
				downloaddata += '<a style=" background-color:inherit; color:#0092dc; border:none; padding:0;text-decoration:underline;" href="http://192.168.200.122/exportFile/everyday_weightExport/weightExport_'+date+'_'+date+'.xls" target="_blank">重量报表导出时间_'+date+'</a><br><br>';
			}
			mstatus.innerHTML = downloaddata;
		}

	}else{
		alert('开始时间必须小于等于结束时间!');
	}
}

function getLocalTime(nS) { 
   return new Date(parseInt(nS)).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ").split(' ')[0];
} 
