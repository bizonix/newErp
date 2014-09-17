$(function(){
	$("input#startTime, input#endTime").datetimepicker({
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
		return {minDate: (input.id == "endTime" ? jQuery("#startTime").datepicker("getDate") : null),
			maxDate: (input.id == "startTime" ? jQuery("#endTime").datepicker("getDate") : null)};
	}
		
	$("#d_status").change(function(){
		//alert($(this).val());
		if($(this).val()==1){
			$("#span_is_combine").css("display","");
		}else{
			$("#span_is_combine").css("display","none");
		}
	});
	$("button[id='exportExcelButton']").click(function(){
		var thisForm = $("form[id='iqcScanListFrom']");
		var actionName = thisForm.attr("action");
		thisForm.attr("action", "index.php?mod=iqcInfo&act=iqcExportExcel");
		thisForm.submit();
		thisForm.attr("action", actionName);
	});
	
});