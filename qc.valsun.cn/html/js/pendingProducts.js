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
	
    $('.updatePic').click(function(){
        var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("是否对其修改图片？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=updatePic&infoId="+infoId+"&pendingId="+pendingId;
        }
	});

    $('.back').click(function(){
        var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0 && status != 2){
            return;
        }
        if(confirm("要进行重新回测处理？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=back&infoId="+infoId+"&pendingId="+pendingId;
        }
	});


    $('.return').click(function(){
	    var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("要进行待退回处理？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=return&infoId="+infoId+"&pendingId="+pendingId;
        }
	});

});
