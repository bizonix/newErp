/*
 * 内部使用出入库单操作列表  internalUseIostoreList.js
 * ADD BY chenwei 2013.8.29
 */

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
	
	//内部使用申请单
	$("button#internalUseIostoreAdd").click(function(){
		window.location.href = "index.php?mod=internalIoSell&act=internalBuyList";			
	});	
	
	//导出到EXCLS
	$("button[id='exportExcelButton']").click(function(){
		var thisForm = $("form[id='internalUseIostoreFrom']");
		var actionName = thisForm.attr("action");
		thisForm.attr("action", "index.php?mod=internalIoSell&act=internalIoSellExportExcel");
		thisForm.submit();
		thisForm.attr("action", actionName);
	});
	
	//单据审核操作(出库按钮)
	$('button[name="approvedOut"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var orderids = this_tr.find('input[name="orderids"]').val();
		if(confirm("确定要审核出库通过吗？")){
			window.location.href = "index.php?mod=internalIoSell&act=internalIoSellApproved&approvedId="+orderids;
		}
	});
	
	//单据审核操作(入库按钮)
	$('button[name="approvedIn"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var orderids = this_tr.find('input[name="orderids"]').val();
		if(confirm("确定要审核入库通过吗？")){
			window.location.href = "index.php?mod=internalIoSell&act=internalIoSellApproved&approvedId="+orderids;
		}
	});
	
	//拒绝（出库）
	$('button[name="unApproveOut"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var orderids = this_tr.find('input[name="orderids"]').val();
		if(confirm("确定要弃用此单据吗？")){
			window.location.href = "index.php?mod=internalIoSell&act=internalIoSellAbandon&approvedId="+orderids;
		}
	});
	
	//拒绝（入库）
	$('button[name="unApproveIn"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var orderids = this_tr.find('input[name="orderids"]').val();
		if(confirm("确定要弃用此单据吗？")){
			window.location.href = "index.php?mod=internalIoSell&act=internalIoSellAbandon&approvedId="+orderids;
		}
	});
	

	
});

/*
 * 选中或不选中表格中的全部checkbox
 */
function chooseornot(selfobj) {
    var ischecked = selfobj.checked
    var list = $('.checkclass');
    for (i in list) {
        list[i].checked = ischecked;
    }
}
