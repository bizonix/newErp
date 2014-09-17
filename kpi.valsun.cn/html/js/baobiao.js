function outProduct(){
	var start = $("#start").val();
	var end = $("#end").val();
	alert(end);
	if(!$.trim(start)){
		alert("请选择起始时间！");
		return false;
	}else if(!$.trim(end)){		
		alert("请选择终止时间！");
		return false;
	}
	/*$.post('../api/json.php?mod=kpiList&act=outProduct',{"start":start,"end":end},function (msg){
				alert(msg);
				if(msg.data !== ""){

					var objhtml = "<br><a href='http://erp.valsun.cn/kpi.valsun.cn/xls/"+msg.data+"'>"+msg.data+"</a>";
					$("#outProductFile").html(objhtml);
			

					}
				
				
	});*/

	$.ajax({
		type:"POST",
		url:"../api/json.php?mod=kpiList&act=outProduct",
		//dataType:"jsonp",
		data:{"start":start,"end":end},
		//data:"start="+start+"end="+end;
		jsonp:"jsonpcallback",
		success:function(msg){
			//console.log(msg);
			//result = parseJSON(msg);
			//alert(msg.data);
			if(msg.data !== ""){

			    var objhtml = "<br><a href='http://erp.valsun.cn/kpi.valsun.cn/xls/"+msg.data+"'>"+msg.data+"</a>";
				$("#outProductFile").html(objhtml);
			

			}
		}
	});
	
}
/*function checkform(){
	var start = $("#start").val();
	var end = $("#end").val();
	//alert(end);
	if(!$.trim(start)){
		alert("请选择起始时间！");
		return false;
	}else if(!$.trim(end)){		
		alert("请选择终止时间！");
		return false;
	}
	return true;

}*/

function outKpi(){
	var start = $("#start1").val();
	var end = $("#end1").val();
	if(!$.trim(start)){
		alert("请选择起始时间！");
		return false;
	}else if(!$.trim(end)){		
		alert("请选择终止时间！");
		return false;
	}
	//alert(start);
	//alert(end);
	$.ajax({
		type:"POST",
		url:"../api/json.php?mod=kpiList&act=outKpi",
		dataType:"jsonp",
		data:{"start":start,"end":end},
		
		success:function(msg){
		    alert(msg);
			//var data = eval("("+msg+")");
			//alert(data);
			//console.log(msg);
			//result = parseJSON(msg);
			if(msg.data!=""){
			    var objhtml = "<br><a href='http://erp.valsun.cn/kpi.valsun.cn/xls/"+msg.data+"'>"+msg.data+"</a>";
				$("#outKpiFile").html(objhtml);
				/*$.ajax({
					type:"POST",
					url:"http://192.168.200.188/kpi.valsun.cn/api/json.php?mod=kpiList&act=downloadFile",
					dataType:"jsonp",
					data:{"filepath":msg.data},
					//data:"start="+start+"end="+end;
					jsonp:"jsonpcallback",
					success:function(){}
			    });*/
			
			
			}
			//window.location.href = result.data.url;
		}
	});
			/*$.post('../api/json.php?mod=kpiList&act=outKpi',{"start":start,"end":end},function (msg){
				console.log(msg.data);
				//result = $.parseJSON(msg);
				if(typeof(msg.data.errCode) != "undefined"){
					$("#errormess").html(msg.data.errMsg);
					$("#username").focus();
					return false;
				}
				window.location.href = msg.data.url;
				//window.location.href = "index.php?mod=product&act=getPcList";
			},"jsonp");*/
	
}
$(document).ready(function(){
	
	$("input#start, input#end").datetimepicker({
		beforeShow: customRange,
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
	function customRange(input) {
		return {minDate: (input.id == "end" ? jQuery("#start").datepicker("getDate") : null),
			maxDate: (input.id == "start" ? jQuery("#end").datepicker("getDate") : null)};
	}
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
	
	$("input#start1_new, input#end1_new").datetimepicker({
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
		return {minDate: (input.id == "end1_new" ? $("#start1_new").datepicker("getDate") : null),
			maxDate: (input.id == "start1_new" ? $("#end1_new").datepicker("getDate") : null)};
	}
	
	$("input#start2, input#end2").datetimepicker({
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
		return {minDate: (input.id == "end2" ? $("#start2").datepicker("getDate") : null),
			maxDate: (input.id == "start2" ? $("#end2").datepicker("getDate") : null)};
	}
	
	$("input#start2_new, input#end2_new").datetimepicker({
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
		return {minDate: (input.id == "end2_new" ? $("#start2_new").datepicker("getDate") : null),
			maxDate: (input.id == "start2_new" ? $("#end2_new").datepicker("getDate") : null)};
	}
	
	$("input#start3, input#end3").datetimepicker({
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
		return {minDate: (input.id == "end3" ? $("#start3").datepicker("getDate") : null),
			maxDate: (input.id == "start3" ? $("#end3").datepicker("getDate") : null)};
	}
	$("input#start4, input#end4").datetimepicker({
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
		return {minDate: (input.id == "end4" ? $("#start4").datepicker("getDate") : null),
			maxDate: (input.id == "start4" ? $("#end4").datepicker("getDate") : null)};
	}
	$("input#start4_new, input#end4_new").datetimepicker({
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
		return {minDate: (input.id == "end4_new" ? $("#start4_new").datepicker("getDate") : null),
			maxDate: (input.id == "start4_new" ? $("#end4_new").datepicker("getDate") : null)};
	}
	$("input#start5, input#end5").datetimepicker({
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
		return {minDate: (input.id == "end5" ? $("#start5").datepicker("getDate") : null),
			maxDate: (input.id == "start5" ? $("#end5").datepicker("getDate") : null)};
	}

	$("input#start6, input#end6").datetimepicker({
		beforeShow: customRange6,
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
	function customRange6(input) {
		return {minDate: (input.id == "end6" ? $("#start6").datepicker("getDate") : null),
			maxDate: (input.id == "start6" ? $("#end6").datepicker("getDate") : null)};
	}

	$("input#start7, input#end7").datetimepicker({
		beforeShow: customRange7,
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
	function customRange7(input) {
		return {minDate: (input.id == "end7" ? $("#start7").datepicker("getDate") : null),
			maxDate: (input.id == "start7" ? $("#end7").datepicker("getDate") : null)};
	}
});