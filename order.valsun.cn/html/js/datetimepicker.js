//add by Herman.Xi 2012-11-30
$(function() {	
   	$("input#applyTime1, input#applyTime2").datetimepicker({
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
		return {minDate: (input.id == "applyTime2" ? jQuery("#applyTime1").datepicker("getDate") : null),
			maxDate: (input.id == "applyTime1" ? jQuery("#applyTime2").datepicker("getDate") : null)};
	}
    
   	$("input#OrderTime1, input#OrderTime2").datetimepicker({
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
		return {minDate: (input.id == "OrderTime2" ? jQuery("#OrderTime1").datepicker("getDate") : null),
			maxDate: (input.id == "OrderTime1" ? jQuery("#OrderTime2").datepicker("getDate") : null)};
	}
    
	
});

