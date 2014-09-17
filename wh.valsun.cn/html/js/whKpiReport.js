/*
 * 仓库报表导出管理--脚本总汇 whExportManage.js
 * add by chenwei 2013.11.11
 */

$(function(){	
	$("input#start1, input#end1").datetimepicker({
		beforeShow: customRange1,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
        dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
        monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
    });
	function customRange1(input) {
		return {minDate: (input.id == "end1" ? $("#start1").datepicker("getDate") : null),
			maxDate: (input.id == "start1" ? $("#end1").datepicker("getDate") : null)};
	}
	$("input#start2, input#end2").datetimepicker({
		beforeShow: customRange2,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
        dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
        monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
    });
	function customRange2(input) {
		return {minDate: (input.id == "end2" ? jQuery("#start2").datepicker("getDate") : null),
				maxDate: (input.id == "start2" ? jQuery("#end2").datepicker("getDate") : null)};
	}
	//发货组复核KPI
	$("input#start3, input#end3").datetimepicker({
		beforeShow: customRange3,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
        dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
        monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
	});	
	function customRange3(input) {
		return {minDate: (input.id == "end3" ? jQuery("#start3").datepicker("getDate") : null),
				maxDate: (input.id == "start3" ? jQuery("#end3").datepicker("getDate") : null)};
	}
    	//装车扫描（小包）的KPI
	$("input#start4, input#end4").datetimepicker({
		beforeShow: customRange4,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
        dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
        monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		timeText: '时:分:秒',
		hourText: '时',
		minuteText: '分',
		secondText: '秒',
		currentText: '当前时间',
		closeText: '关闭'
	});	
	function customRange4(input) {
		return {minDate: (input.id == "end4" ? jQuery("#start4").datepicker("getDate") : null),
				maxDate: (input.id == "start4" ? jQuery("#end4").datepicker("getDate") : null)};
	}
		
		
});

function xlsbaobiao(str){
	var start = document.getElementById('start'+str).value;
	var end = document.getElementById('end'+str).value;
	var mstatus = document.getElementById('mstatus'+str);
   // alert(start);return;
	var starttimes = start.split('-');
	var endtimes = end.split('-');
	var starttimeTemp = starttimes[0] + '/' + starttimes[1] + '/' + starttimes[2];
	var endtimesTemp = endtimes[0] + '/' + endtimes[1] + '/' + endtimes[2];
	var inttime = Date.parse(new Date(endtimesTemp))-Date.parse(new Date(starttimeTemp));
	if(start =='' ||end ==''){
	   alert('请输入时间');return;
	}
	var days = inttime/86400000+1;
	
	if(days>0) {
		//分拣
		if(str==1){
           	url = "index.php?mod=whKpiReport&act=report1&start="+start+"&end="+end;
        	window.open(url);
		}
				
		//分区复核KPI表导出
		if(str==2){
			url = "index.php?mod=whKpiReport&act=report2&start="+start+"&end="+end;
        	window.open(url);
		}
		
		//发货组复核KPI报表导出
		if(str==3){
        	url = "index.php?mod=whKpiReport&act=report_shipping_group&start="+start+"&end="+end;
        	window.open(url);
		}
        //发货组复核KPI报表导出
		if(str==4){
        	url = "index.php?mod=whKpiReport&act=report_loading&start="+start+"&end="+end;
        	window.open(url);
		}

	}else{
		alert('开始时间必须小于等于结束时间!');
	}
}

function getLocalTime(nS) { 
   return new Date(parseInt(nS)).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ").split(' ')[0];
} 
