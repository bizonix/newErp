//add by Herman.Xi 2012-11-30
$(function() {
	$( "#accordion" ).accordion({
		collapsible: true
	});
	$("input#ebay_test_start, input#ebay_test_end").datetimepicker({
		beforeShow: customRange_test,
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
	function customRange_test(input) {
		return {minDate: (input.id == "ebay_test_end" ? $("#ebay_test_start").datepicker("getDate") : null),
			maxDate: (input.id == "ebay_test_start" ? $("#ebay_test_end").datepicker("getDate") : null)};
	}	

	$("input#ebay_no_scan_start, input#ebay_no_scan_end").datetimepicker({
		beforeShow: customRange_ebay_no_scan,
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
	function customRange_ebay_no_scan(input) {
		return {minDate: (input.id == "ebay_no_scan_end" ? $("#ebay_no_scan_start").datepicker("getDate") : null),
			maxDate: (input.id == "ebay_no_scan_start" ? $("#ebay_no_scan_end").datepicker("getDate") : null)};
	}
	
	$("input#ali_batch_ship_order_format_start, input#ali_batch_ship_order_format_end").datetimepicker({
		beforeShow: customRange_ali_batch_ship_order_format,
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
	function customRange_ali_batch_ship_order_format(input) {
		return {minDate: (input.id == "ali_batch_ship_order_format_end" ? $("#ali_batch_ship_order_format_start").datepicker("getDate") : null),
			maxDate: (input.id == "ali_batch_ship_order_format_start" ? $("#ali_batch_ship_order_format_end").datepicker("getDate") : null)};
	}
	
	$("input#paypal_refund_start, input#paypal_refund_end").datetimepicker({
		beforeShow: customRange_paypal_refund,
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
	function customRange_paypal_refund(input) {
		return {minDate: (input.id == "paypal_refund_end" ? $("#paypal_refund_start").datepicker("getDate") : null),
			maxDate: (input.id == "paypal_refund_start" ? $("#paypal_refund_end").datepicker("getDate") : null)};
	}
	
	$("input#ali_tag_ship_log_date").datetimepicker({
		beforeShow: customRange_ali_tag_ship_log,
		showSecond: true,
		dateFormat: 'yy-mm-dd',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		dayNamesMin: [ "日","一", "二", "三", "四", "五", "六"],
		monthNamesShort: ["一", "二", "三", "四", "五", "六", "七", "八", "九", "十", "十一", "十二"],
		currentText: '当前时间',
		closeText: '关闭'
	});
	function customRange_ali_tag_ship_log(input) {
		return {data: (input.id == "ali_tag_ship_log_date" ? $("#ali_tag_ship_log_date").datepicker("getDate") : null)};
	}
	
	$("input#b2b_sale_start, input#b2b_sale_end").datetimepicker({
		beforeShow: customRange_b2b,
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
	function customRange_b2b(input) {
		return {minDate: (input.id == "b2b_sale_end" ? $("#b2b_sale_start").datepicker("getDate") : null),
			maxDate: (input.id == "b2b_sale_start" ? $("#b2b_sale_end").datepicker("getDate") : null)};
	}

	$("input#inner_sale_start, input#inner_sale_end").datetimepicker({
		beforeShow: customRange_inner,
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
	function customRange_inner(input) {
		return {minDate: (input.id == "inner_sale_end" ? $("#inner_sale_start").datepicker("getDate") : null),
			maxDate: (input.id == "inner_sale_start" ? $("#inner_sale_end").datepicker("getDate") : null)};
	}

	$("input#amazon_sale_start, input#amazon_sale_end").datetimepicker({
		beforeShow: customRange_amazon,
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
	function customRange_amazon(input) {
		return {minDate: (input.id == "amazon_sale_end" ? $("#amazon_sale_start").datepicker("getDate") : null),
			maxDate: (input.id == "amazon_sale_start" ? $("#amazon_sale_end").datepicker("getDate") : null)};
	}

	$("input#dresslink_sale_start, input#dresslink_sale_end").datetimepicker({
		beforeShow: customRange_dresslink,
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
	function customRange_dresslink(input) {
		return {minDate: (input.id == "dresslink_sale_end" ? $("#dresslink_sale_start").datepicker("getDate") : null),
			maxDate: (input.id == "dresslink_sale_start" ? $("#dresslink_sale_end").datepicker("getDate") : null)};
	}
	$("input#aliexpress_app_start, input#aliexpress_app_end").datetimepicker({
		beforeShow: customRange_dresslink,
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
	function customRange_dresslink(input) {
		return {minDate: (input.id == "aliexpress_app_end" ? $("#aliexpress_app_start").datepicker("getDate") : null),
			maxDate: (input.id == "aliexpress_app_start" ? $("#aliexpress_app_end").datepicker("getDate") : null)};
	}
	$("input#hand_refund_start, input#hand_refund_end").datetimepicker({
		beforeShow: customRange_hand_refund,
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
	function customRange_hand_refund(input) {
		return {minDate: (input.id == "hand_refund_end" ? $("#hand_refund_start").datepicker("getDate") : null),
			maxDate: (input.id == "hand_refund_start" ? $("#hand_refund_end").datepicker("getDate") : null)};
	}
    $("input#xlsbaobiao4_start, input#xlsbaobiao4_end").datetimepicker({
		beforeShow: customRange_xlsbaobiao4,
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
	function customRange_xlsbaobiao4(input) {
		return {minDate: (input.id == "xlsbaobiao4_end" ? $("#xlsbaobiao4_start").datepicker("getDate") : null),
			maxDate: (input.id == "xlsbaobiao4_start" ? $("#xlsbaobiao4_end").datepicker("getDate") : null)};
	}
});

function exportXls(type){
	//alertify.error('性能优化中，请稍后。。');
	//return false;
	if(type=='ali_tag_ship_log'){
		var time		= document.getElementById(type+'_date').value;
	}else{
		var p_date		= /^20[0-9]{2}-[0-9]{2}-[0-9]{2}\s+[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/;
		var start       = document.getElementById(type+'_start').value;
		var end			= document.getElementById(type+'_end').value;
		//开始日期检测
		if(!p_date.test(start)){
			alertify.error('开始日期：'+start+'有误！');
			return false;
		}
		//结束日期检测
		if(!p_date.test(end)){
			alertify.error('结束日期：'+end+'有误！');
			return false;
		}
	}
	if(type!='paypal_refund'){
		var account = '';
		var bill = new Array();
		var len = document.getElementById(type+'_account').options.length;
		for(var i = 0; i < len; i++){
			if( document.getElementById(type+'_account').options[i].selected){
				var e =  document.getElementById(type+'_account').options[i];
				bill.push(e.value);
				//account	+= e.value+'#';
			}
		}
		if(bill.length == 0){
			alertify.error('未选ebay账号');
			return false;
		}
		account = bill.join('#');
	}
	if(type=='ebay_test'){
		//var url	 = "../../json.php?mod=excelExport&act=ebayTest&start="+start+"&end="+end+"&account="+encodeURIComponent(account);
		var url	= "index.php?mod=exportXls&act=ebayTest&start="+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='ebay_no_scan'){
		var url = 'index.php?mod=exportXls&act=ebayNoScan&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='paypal_refund'){
		var url = 'index.php?mod=exportXls&act=paypalRefund&start='+start+"&end="+end;
	}else if(type=='ali_batch_ship_order_format'){
		var url = 'index.php?mod=exportXls&act=aliBatchShipOrderFormat&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='ali_tag_ship_log'){
		var url = 'index.php?mod=exportXls&act=aliTagShipLog&time='+time+"&account="+encodeURIComponent(account);
	}else if (type=='b2b_sale'){
		var url = "index.php?mod=exportXls&act=b2bSale&start="+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if (type=='inner_sale'){
		var url = "index.php?mod=exportXls&act=innerSale&start="+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if (type=='amazon_sale'){
		var url = "index.php?mod=exportXls&act=amazonSale&start="+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if (type=='dresslink_sale'){
	    var dress_type = document.getElementById('dress_type').value;
		var url = "index.php?mod=exportXls&act=dressLinkSale&start="+start+"&end="+end+"&account="+encodeURIComponent(account)+"&dress_type="+dress_type;
	}else if(type=='ebay'){
		var url = 'process_explode_order_data_ajax.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='ebay_new'){
		var url = 'process_explode_order_data_ajax_new.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);	
	}else if(type=='ebay_oversea'){
		//var url = 'process_explode_order_data_oversea.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);	
		var url = 'index.php?mod=exportXls&act=ebayOversea&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='ebay_new1'){
		var url = 'process_explode_order_data_ajax_paytime.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);			
	}else if(type=='ebay_refund'){
		var url = 'process_explode_order_data_ajax_refund.php?start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='aliexpress_app'){
		var url = 'index.php?mod=exportXls&act=aliexpress_app&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='aliexpress_app'){
		var url = 'index.php?mod=exportXls&act=hand_refund&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='newegg_export'){
		var url = 'index.php?mod=exportXls&act=newegg_export&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='xlsbaobiao4'){
		var mailway = $("#mailway4").val();
		var url = 'index.php?mod=exportXls&act=xlsbaobiao4&start='+start+"&end="+end+"&account="+encodeURIComponent(account)+"&mailway="+mailway;
	}else if(type=='amazonInStockExport'){
		var url = 'index.php?mod=exportXls&act=amazonInStockExport&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	}else if(type=='manualRefundxls'){
		var url = 'index.php?mod=exportXls&act=manualRefundxls&start='+start+"&end="+end+"&account="+encodeURIComponent(account);
	} else if(type == 'combSkuPrice') {		//组合料号
		var url = $("#combSkuPageUrl").val();
		var starttimes = start.split('-');
		var endtimes = end.split('-');
		var starttimeTemp = starttimes[0] + '/' + starttimes[1] + '/' + starttimes[2];
		var endtimesTemp = endtimes[0] + '/' + endtimes[1] + '/' + endtimes[2];
		var inttime = Date.parse(new Date(endtimesTemp))-Date.parse(new Date(starttimeTemp));
		var days = inttime/86400000+1;
		var downloaddata = '';
		for(var i = 0; i < days; i++){
			var date = getLocalTime((Date.parse(new Date(endtimesTemp))-i*86400000));
			downloaddata += '<a href="' + url + date + '_' + date + '.xlsx" target="_blank">组合料号价格信息表导出时间_' + date + '</a><br>';
		}
		$("#combSkuPageUrl").parent("div").children("div").html(downloaddata);
		return ;
	} else if(type == 'paypal_case') {	//paypal纠纷数据报表
		var start = document.getElementById('DisputeStartTime').value;
		var end = document.getElementById('DisputeEndTime').value;
		var p_date=/^20[0-9]{2}-[0-9]{2}-[0-9]{2}\s+[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/;
		//开始日期检测
		if(!p_date.test(start)){
			alert('开始日期：'+start+'有误！');
			return false;
		}
		//结束日期检测
		if(!p_date.test(end)){
			alert('结束日期：'+end+'有误！');
			return false;
		}
		var starttimes		= start.split('-');
		var endtimes		= end.split('-');
		var starttimeTemp	= starttimes[2].split(' ');
		var endtimesTemp	= endtimes[2].split(' ');
		var days			= starttimeTemp[0] - endtimesTemp[0];
		var daysPower		= endtimesTemp[0] - starttimeTemp[0];//导出数据量天数限制
		if(days > 0) {
			alert('开始时间必须小于等于结束时间!');
			return false;
		} else {
			var url = 'index.php?mod=exportXls&act=paypalCase&start='+start+'&end='+end;
		}
	} else if(type == 'priceInfo') {
		var start = $("#priceInfo_start").val;
		var end = $("#priceInfo_end").val;
		var url = $("#priceInfoUrl").val();
		var starttimes = start.split('-');
		var endtimes = end.split('-');
		var starttimeTemp = starttimes[0] + '/' + starttimes[1] + '/' + starttimes[2];
		var endtimesTemp = endtimes[0] + '/' + endtimes[1] + '/' + endtimes[2];
		var inttime = Date.parse(new Date(endtimesTemp))-Date.parse(new Date(starttimeTemp));
		
		var days = inttime/86400000+1;
		
		if(days>0) {
			if(str==1){
				var downloaddata = '';
				for(var i=0; i<days; i++){
					var date = getLocalTime((Date.parse(new Date(endtimesTemp))-i*86400000));
					downloaddata += '<a href="'+url+date+'_'+date+'.xls" target="_blank">价格信息表导出时间_'+date+'</a><br>';
				}
				$("#priceInfo_start").parent("div").children("div").html(downloaddata);
			}
			return false;
		}
	}
	//alert(url);
	window.open(url);
	//window.open(url,"_blank");
}

function validate_data(id){
	var dateobj = document.getElementById(id).value;
	var p_date=/^20[0-9]{2}-[0-9]{2}-[0-9]{2}\s+[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/; //2012-05-15  23:59:59
	if(	p_date.test(dateobj)){
		return true;
	}else{
		alert('日期：'+dateobj+'有误！');
	}
}

/**
 * EUB跟踪号报表导出
 */
function eub_trucknumber() {
	var url = 'index.php?mod=exportXls&act=eubTrucknumber';
	window.open(url);
}