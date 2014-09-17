/*** 申请部份预付款 Start ***/
$("#prepartpay-btn").click(function(){
	var dataArr 	= [];
	var parName 	= [];
	var payStatus 	= [];
	var idArr 		= $('input[name="inverse"]');
	$.each(idArr,function(index,item){
		if($(item).attr('checked') == 'checked'){
			var orderid	= $(item).val();
			var parname = $(this).data('partner');
			var status  = $(this).data('paystatus');
			dataArr.push(orderid);
			parName.push(parname);
			payStatus.push(status);
		}
	});
	if(dataArr.length == 0){
		alertify.alert('请选择需要部份预付的订单号');
		return false;
	}
	
	//判断同一个供应商
	var firstName = '';
	for(var i=0; i<parName.length; i++){
		var firstName = parName[0];
		if(firstName != parName[i]){
			alertify.alert('请选择同一供应商订单进行部份预付');
			return false;
		}
	}
	//过滤已部份预付、全部预付和结款状态不能再次部份预付
	for(var k=0;k<payStatus.length;k++){
		if(payStatus[k]==2){
			alertify.alert('所选订单中存在已申请过部份预付的订单,不能再次预付');
			return false;	
		}
		if(payStatus[k]==3){
			alertify.alert('所选订单中存在已申请过全额预付的订单,不能再次预付');
			return false;	
		}
		if(payStatus[k]==4){
			alertify.alert('所选订单中存在已申请过结款的订单,不能再次预付');
			return false;
		}	
	}
	
	var url  = "json.php?mod=purToFinanceAPI&act=getPrePartPayOrder";
	$('.madal-title').html('');
	var len             = 0;
	var html   = '<table class="table table-bordered"><thead><tr><th>采购订单</th><th>料号</th><th>采购数量</th><th>采购单价</th><th>金额</th><th>预付金额</th></tr></thead><tbody>';
	$.post(url,{ "dataArr":dataArr },function(rtn){
		var rtnData 	= rtn[0];
		var totalMoney 	= rtn[1];
		$('.modal-title-prepart').html('供应商[<font style="font-weight:bolder; color:#009900">'+firstName+'</font>]申请部份预付,总额[<font style="font-weight:bolder; color:#0000FF">'+totalMoney+'</font>]RMB,预付<input type="text" id="pre-paymoney" size="8" disabled="disabled" style="font-size:36; color:#FF0000" value="0" />RMB');
		$.each(rtnData,function(i,item){
			len  	= i;
			var money 	= item.count * item.price;
			html += '<tr><td>'+item.ordersn+'<td>'+item.sku+'</td><td>'+item.count+'</td><td>'+item.price+'</td><td>'+money+'<input type="hidden" value="'+money+'" id="money'+i+'" /></td>';
			html += '<td><input type="text" id="premoney'+i+'" size="5" disabled="disabled" /></td></tr>';
		});
		html += '<tr><td>按百分比</td><td><select name="payper" id="payper" onchange="calcPerMoney();"><option value="-1">预付比例</option><option value="10">10%</option>';
		html += '<option value="20">20%</option><option value="30">30%</option><option value="40">40%</option><option value="50">50%</option>';
		html += '<option value="60">60%</option><option value="70">70%</option></select></td>';
		html += '<td>按金额</td><td><input type="text" id="realmoney" size="5" onblur="realMoney();"/><td colspan="2"></td></tr>'
		html += '<tr id="lastprepartpay"><td>备注</td><td colspan="5"><textarea name="note" id="note" cols="40" rows="2"></textarea><input type="hidden" id="prepartlen" value="'+len+'" /><input type="hidden" id="totalmoney" value="'+totalMoney+'" /></td></tr>';
		html += '</tbody></table>';
		$('#prepartpayinfo').html(html);
		$('#prepartpay-layer').modal();	
	},"json");
	
})

//百分比金额验证
function calcPerMoney(){
	var per 	= $.trim($('#payper').val());
	var len     = $('#prepartlen').val();
	var pretotalmoney = 0;
	if(per != -1){
		for(var i=0; i<=len; i++){
			var money 		= $('#money'+i).val();
			var premoney 	= money * (per / 100);//折算每个料号预付金额
			pretotalmoney  += premoney;
			$('#premoney'+i).val(premoney.toFixed(2));
		}
	}else{
		for(var i=0; i<=len; i++){
			$('#premoney'+i).val(0);
		}
	}
	$('#pre-paymoney').val(pretotalmoney.toFixed(2));//预付总金额
	$('#realmoney').val('');
}

//按金额验证
function realMoney(){
	var regmoney    = /^[0-9]+$/;
	var realmoney 	= $.trim($('#realmoney').val());
	var tmpmoney    = realmoney;
	var len     	= $('#prepartlen').val();
	var totalmoney  = $('#totalmoney').val();
	var per         = $('#payper').val();
	var diffmoney   = 0;
	$('#payper').val('-1');
	$('#pre-paymoney').val(0);//预付总金额
	for(var i=0; i<=len; i++){
		$('#premoney'+i).val(0);
	}
	if(per == -1 && realmoney == ''){
		alertify.alert('请输入预付金额');
		return false;
	}
	if(!regmoney.test(realmoney)){
		alertify.alert('金额只能输入正整数');
		return false;
	}
	realmoney 		= parseFloat(realmoney);
	totalmoney 		= parseFloat(totalmoney) * 0.7;
	if(realmoney > totalmoney){
		alertify.alert('请输入有效的预付金额');
		return false;
	}
	for(var i=0; i<=len; i++){
		var money 		= $('#money'+i).val();
		var diffmoney   = realmoney - money;
		if(diffmoney > 0){
			$('#premoney'+i).val(money);
		}
		if(diffmoney < 0 && realmoney > 0){
			$('#premoney'+i).val(realmoney);
		}
		
		if(diffmoney < 0 && realmoney < 0){
			$('#premoney'+i).val(0);
		}
		realmoney 		= diffmoney;
	}
	$('#pre-paymoney').val(tmpmoney);//预付总金额
}
//提交部份预付
$('#prepartpay-save').click(function(){
	var payper 		= $('#payper').val();
	var realmoney 	= $('#realmoney').val();
	var totalmoney  = $('#totalmoney').val();
	var regmoney    = /^[0-9]+$/;
	var cate        = '';
	var digitial    = '';
	if(payper == -1 && realmoney == ''){
		alertify.alert('请选择预付类型');
		return false;
	}
	if(payper == -1){
		if(!regmoney.test(realmoney)){
			alertify.alert('金额只能输入正整数');
			return false;
		}
	}
	realmoney = parseFloat(realmoney);
	if(payper == -1 && realmoney >= totalmoney){
		alertify.alert('请输入有效的预付金额');
		return false;
	}
	if(payper == -1){
		cate 		= 'money';//按金额
		digitial 	= $('#realmoney').val();//预付金额
	}else{
		cate        = 'per';//按百分比
		digitial    = $('#payper').val();//百分比
	}
	
	$('#msgprepartpay').remove();
	$('#lastprepartpay').after('<tr id="msgprepartpay" style="color:#FF0000"><td>处理中,请稍后...</td><td colspan="5"><img src="./public/img/spinner.gif" /></td></tr>');
	var note  = $('#note').val();
	if(note == ''){
		note = 'finance';
	}
	var dataArr 	= '';
	var idArr 		= $('input[name="inverse"]');
	$.each(idArr,function(index,item){
		if($(item).attr('checked') == 'checked'){
			var recod	= $(item).data('recod');
			dataArr += recod + ','; 
		}
	});
	var url  = "json.php?mod=purToFinanceAPI&act=pushPrePartPayOrder";
	$.post(url,{ "ordersn":dataArr, "note":note, "cate":cate, "digitial":digitial },function(rtn){
		if(rtn['rtnCode'] != 1){
			$('#msgprepartpay').remove();
			$('#lastprepartpay').after('<tr id="msgprepartpay" style="color:#FF0000; font-weight:bolder"><td>错误提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}else{
			$('#msgprepartpay').remove();
			window.location.reload();
			//$('#lastprepartpay').after('<tr id="msgprepartpay" style="color:#009900; font-weight:bolder"><td>提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}
	}, "json");
	
})
/*** 申请部份预付款 End ***/

/*** 申请全额预付款 2014-04-01 Start ***/
$("#preallpay-btn").click(function(){
	var dataArr 	= [];
	var parName 	= [];
	var payStatus 	= [];
	var idArr 		= $('input[name="inverse"]');
	$.each(idArr,function(index,item){
		if($(item).attr('checked') == 'checked'){
			var orderid	= $(item).val();
			var parname = $(this).data('partner');
			var status  = $(this).data('paystatus');
			dataArr.push(orderid);
			parName.push(parname);
			payStatus.push(status);
		}
	});
	if(dataArr.length == 0){
		alertify.alert('请选择需要全额预付的订单号');
		return false;
	}
	
	//判断同一个供应商
	var firstName = '';
	for(var i=0; i<parName.length; i++){
		var firstName = parName[0];
		if(firstName != parName[i]){
			alertify.alert('请选择同一供应商订单进行全额预付');
			return false;
		}
	}
	//过滤全部预付和结款状态不能再次预付
	for(var k=0;k<payStatus.length;k++){
		if(payStatus[k]==2){
			alertify.alert('所选订单中存在已申请过部份预付的订单,不能再次预付');
			return false;	
		}
		if(payStatus[k]==3){
			alertify.alert('所选订单中存在已申请过全额预付的订单,不能再次预付');
			return false;	
		}
		if(payStatus[k]==4){
			alertify.alert('所选订单中存在已申请过结款的订单,不能再次预付');
			return false;
		}	
	}
	var url  = "json.php?mod=purToFinanceAPI&act=getPreAllPayOrder";
	$('.madal-title').html('');
	var html   = '<table class="table table-bordered"><thead><tr><th>采购订单</th><th>订单总额</th><th>预付总金额</th></tr></thead><tbody>';
	$.post(url,{ "dataArr":dataArr },function(rtn){
		var rtnData 		= rtn[0];
		var totalAllMoney 	= rtn[1];
		var len             = 0;
		$('.modal-title-preall').html('供应商[<font style="font-weight:bolder; color:#009900">'+firstName+'</font>]申请全额预付,预付金额[<font style="font-weight:bolder; color:#FF0000">'+totalAllMoney+'</font>]RMB');
		$.each(rtnData,function(i,item){
			len				 = i;
			var totalMoney 	 = item.totalmoney;
			html += '<tr><td>'+item.recordnumber+'<input type="hidden" id="ordersn'+i+'" value="'+item.recordnumber+'" /></td><td>'+totalMoney+'</td><td>'+totalMoney+'</td></tr>';
		});
		html += '<tr id="lastpreallpay"><td>备注</td><td colspan="2"><textarea name="preallnote" id="preallnote" cols="40" rows="2"></textarea><input type="hidden" id="prealllen" value="'+len+'" /></td></tr>';
		html += '</tbody></table>';
		$('#preallpayinfo').html(html);
		$('#preallpay-layer').modal();	
	},"json");
	
})

//提交全额预付请求
$("#preallpay-save").click(function(){
	$('#msgpreallpay').remove();
	$('#lastpreallpay').after('<tr id="msgpreallpay" style="color:#FF0000"><td>处理中,请稍后...</td><td colspan="3"><img src="./public/img/spinner.gif" /></td></tr>');
	var len 		= $('#prealllen').val();
	var note        = $('#preallnote').val();
	if(note == ''){
		note = 'finance';
	}
	var dataArr 	= '';
	for(var ii = 0; ii <= len; ii++){
		var ordersn  = $('#ordersn'+ii).val();
		dataArr 	+= ordersn+',';
	}
	var url  = "json.php?mod=purToFinanceAPI&act=pushPreAllPayOrder";
	$.post(url,{ "ordersn":dataArr, "note":note },function(rtn){
		if(rtn['rtnCode'] != 1){
			$('#msgpreallpay').remove();
			$('#lastpreallpay').after('<tr id="msgpreallpay" style="color:#FF0000; font-weight:bolder"><td>错误提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}else{
			$('#msgpreallpay').remove();
			window.location.reload();
			//$('#lastpreallpay').after('<tr id="msgpreallpay" style="color:#009900; font-weight:bolder"><td>提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}
	}, "json");
})
/*** 申请全额预付款 2014-04-01 End ***/

/*** 申请结款 2014-03-31 Start ***/
$("#pay-btn").click(function(){
	var dataArr 	= [];
	var parName 	= [];
	var payStatus 	= [];
	var idArr 		= $('input[name="inverse"]');
	$.each(idArr,function(index,item){
		if($(item).attr('checked') == 'checked'){
			var orderid	= $(item).val();
			var parname = $(this).data('partner');
			var status  = $(this).data('paystatus');
			dataArr.push(orderid);
			parName.push(parname);
			payStatus.push(status);
		}
	});
	if(dataArr.length == 0){
		alertify.alert('请选择需要结款的订单号');
		return false;
	}
	
	//判断同一个供应商
	var firstName = '';
	for(var i=0; i<parName.length; i++){
		var firstName = parName[0];
		if(firstName != parName[i]){
			alertify.alert('请选择同一供应商订单进行结款');
			return false;
		}
	}
	//过滤全部预付和结款状态不能结款
	for(var k=0;k<payStatus.length;k++){
		if(payStatus[k]==3){
			alertify.alert('所选订单中存在已申请过全额预付的订单,不能结款');
			return false;	
		}
		if(payStatus[k]==4){
			alertify.alert('所选订单中存在已申请过结款的订单,不能结款');
			return false;
		}	
	}
	var url  = "json.php?mod=purToFinanceAPI&act=getEndPayOrder";
	$('.madal-title').html('');
	var html   = '<table class="table table-bordered"><thead><tr><th>采购订单</th><th>订单总额</th><th>已预付金额</th><th>本次结款总金额</th></tr></thead><tbody>';
	$.post(url,{ "dataArr":dataArr },function(rtn){
		var rtnData 		= rtn[0];
		var totalAllMoney 	= rtn[1];
		var prePayAllMoney 	= rtn[2];
		var endPayAllMoney 	= totalAllMoney - prePayAllMoney;  
		var len             = 0;
		$('.modal-title-pay').html('供应商[<font style="font-weight:bolder; color:#009900">'+firstName+'</font>]申请结款,本次结款[<font style="font-weight:bolder; color:#FF0000">'+endPayAllMoney+'</font>]RMB');
		$.each(rtnData,function(i,item){
			len				 = i;
			var totalMoney 	 = item.totalmoney;
			var prePayMoney  = item.prepaymoney;
			var endPayMoney  = totalMoney - prePayMoney;
			html += '<tr><td>'+item.recordnumber+'<input type="hidden" id="ordersn'+i+'" value="'+item.recordnumber+'" /></td><td>'+totalMoney+'</td><td>'+prePayMoney+'</td><td>'+endPayMoney+'</td></tr>';
		});
		html += '<tr id="lastpay"><td>备注</td><td colspan="3"><textarea name="paynote" id="paynote" cols="40" rows="2"></textarea><input type="hidden" id="paylen" value="'+len+'" /></td></tr>';
		html += '</tbody></table>';
		$('#payinfo').html(html);
		$('#pay-layer').modal();	
	},"json");
	
})


//提交结款请求
$("#pay-save").click(function(){
	$('#msgpay').remove();
	$('#lastpay').after('<tr id="msgpay" style="color:#FF0000"><td>处理中,请稍后...</td><td colspan="3"><img src="./public/img/spinner.gif" /></td></tr>');
	var len 		= $('#paylen').val();
	var note        = $('#paynote').val();
	if(note == ''){
		note = 'finance';
	}
	var dataArr 	= '';
	for(var ii = 0; ii <= len; ii++){
		var ordersn  = $('#ordersn'+ii).val();
		dataArr 	+= ordersn+',';
	}
	var url  = "json.php?mod=purToFinanceAPI&act=pushEndPayOrder";
	$.post(url,{ "ordersn":dataArr, "note":note },function(rtn){
		if(rtn['rtnCode'] != 1){
			$('#msgpay').remove();
			$('#lastpay').after('<tr id="msgpay" style="color:#FF0000; font-weight:bolder"><td>错误提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}else{
			$('#msgpay').remove();
			window.location.reload();
			//$('#lastpay').after('<tr id="msgpay" style="color:#009900; font-weight:bolder"><td>提示</td><td colspan="3">'+rtn['data']+'</td></tr>');
		}
	}, "json");
})
/*** 申请结款 End ***/

/*** 申请全额退款 Start ***/
$('#backallpay-btn').click(function(){
	var dataArr 	= [];
	var parName 	= [];
	var payStatus 	= [];
	var payResult   = [];
	var idArr 		= $('input[name="inverse"]');
	$.each(idArr,function(index,item){
		if($(item).attr('checked') == 'checked'){
			var orderid	= $(item).val();
			var parname = $(this).data('partner');
			var status  = $(this).data('paystatus');
			var result  = $(this).data('payresult');
			dataArr.push(orderid);
			parName.push(parname);
			payStatus.push(status);
			payResult.push(result);
		}
	});
	if(dataArr.length == 0){
		alertify.alert('请选择需要全额退款的订单号');
		return false;
	}
	
	//判断同一个供应商
	var firstName = '';
	for(var i=0; i<parName.length; i++){
		var firstName = parName[0];
		if(firstName != parName[i]){
			alertify.alert('请选择同一供应商订单进行退款');
			return false;
		}
	}
	//全额付款需满足订单为全额预付、月结且付款状态为已付款
	console.log(payStatus);
	for(var k=0;k<payStatus.length;k++){
		if(payStatus[k]!=3 && payStatus[k] !=4){
			alertify.alert('全额退款需为已全额预付或结款的订单');
			return false;	
		}
	}
	for(var k=0;k<payResult.length;k++){
		if(payResult[k]!=2){
			alertify.alert('全额退款需为已全额预付或结款的订单');
			return false;	
		}
	}
	var url  = "json.php?mod=purToFinanceAPI&act=getBackAllPayOrder";
	$('.madal-title').html('');
	var html   = '<table class="table table-bordered"><thead><tr><th>采购订单</th><th>订单总额</th><th>预付金额</th><th>结款金额</th><th>已付款金额</th></tr></thead><tbody>';
	$.post(url,{ "dataArr":dataArr },function(rtn){
		var rtnData 		= rtn[0];
		var totalAllMoney   = rtn[1];
		var len             = 0;
		$('.modal-title-backall').html('供应商[<font style="font-weight:bolder; color:#009900">'+firstName+'</font>]申请全额退款,本次退款总金额[<font style="font-weight:bolder; color:#FF0000">'+totalAllMoney+'</font>]RMB');
		$.each(rtnData,function(i,item){
			len				= i;
			html += '<tr><td>'+item.recordnumber+'<input type="hidden" id="ordersn'+i+'" value="'+item.recordnumber+'" /></td><td>'+item.totalmoney+'</td><td>'+item.premoney+'</td><td>'+item.endmoney+'</td><td>'+item.totalmoney+'</td></tr>';
		});
		html += '<tr id="lastbackall"><td>备注</td><td colspan="4"><textarea name="backallnote" id="backallnote" cols="40" rows="2"></textarea><input type="hidden" id="backalllen" value="'+len+'" /></td></tr>';
		html += '</tbody></table>';
		$('#backallpayinfo').html(html);
		$('#backallpay-layer').modal();	
	},"json");													
})

//提交全额退款请求
$("#backallpay-save").click(function(){
	$('#msgbackall').remove();
	$('#lastbackall').after('<tr id="msgbackall" style="color:#FF0000"><td>处理中,请稍后...</td><td colspan="3"><img src="./public/img/spinner.gif" /></td></tr>');
	var len 		= $('#backalllen').val();
	var note        = $('#backallnote').val();
	if(note == ''){
		note = 'finance';
	}
	var dataArr 	= '';
	for(var ii = 0; ii <= len; ii++){
		var ordersn  = $('#ordersn'+ii).val();
		dataArr 	+= ordersn+',';
	}
	var url  = "json.php?mod=purToFinanceAPI&act=pushBackAllOrder";
	$.post(url,{ "ordersn":dataArr, "note":note },function(rtn){
		if(rtn['rtnCode'] != 1){
			$('#msgbackall').remove();
			$('#lastbackall').after('<tr id="msgbackall" style="color:#FF0000; font-weight:bolder"><td>错误提示</td><td colspan="4">'+rtn['data']+'</td></tr>');
		}else{
			$('#msgbackall').remove();
			window.location.reload();
		}
	}, "json");
})
/*** 申请全额退款 End ***/