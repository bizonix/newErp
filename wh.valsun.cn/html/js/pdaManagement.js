

$(function(){
	//时间
	$("input#startdate, input#enddate").datetimepicker({
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
		return {minDate: (input.id == "enddate" ? jQuery("#startdate").datepicker("getDate") : null),
			maxDate: (input.id == "startdate" ? jQuery("#enddate").datepicker("getDate") : null)};
	}
	
	$('.del').click(function(){
		var order = $(this).attr("orderid");
		var sku   = $(this).attr("sku");
		var pname = $(this).attr("pname");
		if(confirm("确定删除订单号"+order+"下料号"+sku+"在仓位"+pname+"下的配货信息，并且回滚库存！")){
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=pdaManagement&act=removeRollback&jsonp=1',
				data	: {order:order,sku:sku,pname:pname},
				success	: function (msg){
					//console.log(msg);return false;
					alert(msg.errMsg);return;			
				}
			});
		}
	});
    $('.infosubmits').click(function(){
          var orderid = document.getElementById('orderid').value;
         if(orderid ==''){
    	    	alertify.error("请选择需要查询的发货单号，配货单号或者包裹号！");
               // alert(orderid);
               // window.setTimeout("window.location.reload()",10000);
                return false;
           }else{
            var f= document.getElementById('infoForm');
              f.submit();
           }
    });

});

