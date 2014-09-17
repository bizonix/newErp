$(function(){
//alertify.error('请选择要合并的订单!');
	//POST数据验证
	$("#orderAttrAddForm").validationEngine({autoHidePrompt:true});

	//返回
	$("#back").click(function(){
		history.back();
	});

});

function getuserinfo(){
	var userid     = $('#userid').val();
	var platformId = $('#platform').val();
	if(userid==''){
		alertify.error('请输入买家名称');
		$('#userid').focus();
		return false;
	}

	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=orderAdd&act=getUserInfo&jsonp=1',
		data	: {platformId:platformId,userid:userid},
		success	: function (msg){
			//console.log(msg.data[0].city);return false;
			if(msg.errCode==0){
				$('#fullname').val(msg.data[0].username);
				$('#street1').val(msg.data[0].street);
				$('#street2').val(msg.data[0].address2);
				$('#city').val(msg.data[0].city);
				$('#state').val(msg.data[0].state);
				$('#country').val(msg.data[0].countryName);
				$('#zip').val(msg.data[0].zipCode);
				$('#tel1').val(msg.data[0].landline);
				$('#tel2').val(msg.data[0].phone);
				$('#ebay_usermail1').val(msg.data[0].email);
				alertify.alert('请确认用户信息是否跟订单一致，以免出错！');
				$('#orderid').focus();
			}else{
				alertify.alert(msg.errMsg);
				$('#fullname').focus();
			}
		}
	});
}

//平台联动
function change_platform(){
	var platformId = $("#platform").val();
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=orderAdd&act=getAccountListByPlatform&jsonp=1',
			data	: {platformId:platformId},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==200){
					$('#account').html('');
					var arr = msg.data;
					var newtab = '';
					for (key in arr){
						newtab +="<option value='"+key+"'>"+arr[key]+"</option>";
					}
					$("#account").html(newtab);
				}else{
				    $("#account").html('');
					alertify.error(msg.errMsg);
				}
			}
		});
}

function test_number(num){
	var teststr = /^\d+$/;
	return teststr.test(num);
}

function test_number_zero(num){
	var teststr = /^0+$/;
	return teststr.test(num);
}

//信息验证
function check(form){
	var platform = $.trim($('#platform').val());
	if(platform == ''){
		alertify.error('平台不能为空');
		$('#platform').focus();
		return false;
	}

	var fullname = $.trim($('#fullname').val());
	if(fullname.length < 2){
		alertify.error('fullname字符长度要大于等于2');
		$('#fullname').focus();
		return false;
	}
	if(test_number(fullname)){
		alertify.error('fullname不能全为数字!');
		$('#fullname').focus();
		return false;
	}

	var account = $.trim($('#account').val());
	if(account == ''){
		alertify.error('订单帐号不能为空');
		$('#account').focus();
		return false;
	}

	var street1 = $.trim($('#street1').val());
	if(street1 == ''){
		alertify.error('Street1不能为空');
		$('#street1').focus();
		return false;
	}

	var userid = $.trim($('#userid').val());
	if(userid.length < 2){
		alertify.error('买家名称字符长度要大于等于2');
		document.getElementById('userid').focus();
		return false;
	}
	if(test_number(userid)){
		alertify.error('买家名称不能全为数字!');
		document.getElementById('userid').focus();
		return false;
	}

	var orderid = $.trim($('#orderid').val());
	if(orderid == ''){
		alertify.error('订单号不能为空');
		$('#orderid').focus();
		return false;
	}

	var city = $.trim($('#city').val());
	if(city == ''){
		alertify.error('city不能为空');
		$('#city').focus();
		return false;
	}

	var ebay_createdtime = $.trim($('#ebay_createdtime').val());
	if(ebay_createdtime == ''){
		alertify.error('下单时间不能为空');
		$('#ebay_createdtime').focus();
		return false;
	}

	var state = $.trim($('#state').val());
	if(state == ''){
		alertify.error('state不能为空');
		$('#state').focus();
		return false;
	}

	var ebay_paidtime = $.trim($('#ebay_paidtime').val());
	if(ebay_paidtime == ''){
		alertify.error('付款时间不能为空');
		$('#ebay_paidtime').focus();
		return false;
	}

	var country = $.trim($('#country').val());
	if(country == ''){
		alertify.error('country不能为空');
		$('#country').focus();
		return false;
	}

	var zip = $.trim($('#zip').val());
	if(zip.length < 3 || zip.length > 15){
		alertify.error('Postcode 字符数须在3-15内！');
		document.getElementById('zip').focus();
		return false;
	}
	if(test_number_zero(zip)){
		alertify.error('Postcode不能全为0!');
		document.getElementById('zip').focus();
		return false;
	}

	var tel1 = $.trim($('#tel1').val());
	if(tel1.length < 6 || tel1.length > 20){
		alertify.error('tel1字符数须在6-20内！');
		document.getElementById('tel1').focus();
		return false;
	}
	if(test_number_zero(tel1)){
		alertify.error('tel1不能全为0!');
		document.getElementById('tel1').focus();
		return false;
	}

	var ebay_total = $.trim($('#ebay_total').val());
	if(ebay_total == ''){
		alertify.error('订单金额不能为空');
		$('#ebay_total').focus();
		return false;
	}

	var ebay_currency = $('#ebay_currency').val();
	if(ebay_currency == ''){
		alertify.error('币种不能为空');
		$('#ebay_currency').focus();
		return false;
	}else if(ebay_currency == '其他'){
		var other_currency = $.trim($('#other_currency').val());
		if(other_currency == ''){
			alertify.error('请填写其他币种');
			$('#other_currency').focus();
			return false;
		}
	}

	var ebay_ptid = $('#ebay_ptid').val();
	if(ebay_ptid == ''){
		alertify.error('Transaction ID不能为空');
		$('#ebay_ptid').focus();
		return false;
	}else if(ebay_ptid == 'paypal' || ebay_ptid == 'Escrow' || ebay_ptid == '其他'){
		var other_ptid = $.trim($('#other_ptid').val());
		if(other_ptid == ''){
			alertify.error('请填写对应的Transaction ID');
			$('#other_ptid').focus();
			return false;
		}
	}

	var ebay_usermail1 = $('#ebay_usermail1').val();
	if(!/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/.test(ebay_usermail1)){
		alertify.error('买家邮箱1格式有误');
		$('#ebay_usermail1').focus();
		return false;
	}

	var ebay_carrier = $.trim($('#ebay_carrier').val());
	if(ebay_carrier == ''){
		alertify.error('买家发货物流不能为空 ID');
		$('#ebay_carrier').focus();
		return false;
	}
	if (ebay_carrier != '上门提货') {
		if(street1.length < 2){
			alertify.error('Street1字符长度要大于等于2');
			document.getElementById('street1').focus();
			return false;
		}
		if(test_number(street1)){
			alertify.error('street1不能全为数字!');
			document.getElementById('street1').focus();
			return false;
		}
		if(city.length < 2){
			alertify.error('city字符长度要大于等于2');
			document.getElementById('city').focus();
			return false;
		}
		if(test_number(city)){
			alertify.error('city不能全为数字!');
			document.getElementById('city').focus();
			return false;
		}
		if(state.length < 2){
			alertify.error('state字符长度要大于等于2');
			document.getElementById('state').focus();
			return false;
		}
		if(test_number(state)){
			alertify.error('state不能全为数字!');
			document.getElementById('state').focus();
			return false;
		}
		if(country.length < 2){
			alertify.error('country字符长度要大于等于2');
			document.getElementById('country').focus();
			return false;
		}
		if(test_number(country)){
			alertify.error('country不能全为数字!');
			document.getElementById('country').focus();
			return false;
		}
	}

	var sku = document.getElementsByName('sku[]');
	for(var i=0;i<sku.length;i++){
		if(sku[i].value==""){
			alertify.error('有产品没有输入sku');
			sku[i].focus();
			return false;
		}
	}

	var qty = document.getElementsByName('qty[]');
	for(var i=0;i<qty.length;i++){
		if(qty[i].value==""){
			alertify.error('有产品没有输入数量');
			qty[i].focus();
			return false;
		}
	}

	for(var i=0;i<qty.length;i++){
		var qty_num = qty[i].value;
		if(isNaN(qty_num) || qty_num == ""){
			alertify.error('数量只能输入数字');
			qty[i].focus();
			return false;
		}
	}

	$('#add').submit();

	/***改为在post的action中判定
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=orderAdd&act=checkOrder&jsonp=1',
		data	: {orderid:orderid,account:account},
		success	: function (msg){
			//console.log(msg);return false;
			if(msg.data != false){
				$('#add').submit();
			}else{
				alertify.error(msg.errMsg);
				$('#orderid').focus();
			}
		}
	});
	*/
}

function change_carrier() {
	var ebay_carrier = document.getElementById('ebay_carrier').value;
	if(ebay_carrier == 60){
		document.getElementById('street1').value 	= '';
		document.getElementById('city').value 		= '';
		document.getElementById('state').value 		= '';
		document.getElementById('country').value 	= '';
	}
}

function scanProcessResponse2(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.status == '200'){
				document.getElementById('add').submit();
			}else{
				alertify.alert(data.text);
				var obj = document.getElementById('orderid');
				obj.focus();
			}
		}
	}
}

function addImg(obj){
	 var table_obj  = obj.parentNode.parentNode.parentNode;
	 var tr_num = table_obj.rows.length;
	 var src  = obj.parentNode.parentNode;
	 var idx  = src.rowIndex;
     var tbl  = document.getElementById('gallery');
     var row  = tbl.insertRow(idx + 1);

	  row.insertCell(-1).innerHTML='<a href="javascript:;" onclick="removeImg(this)">[-]</a>';
	  row.insertCell(-1).innerHTML='<input name="sku[]" type="text" id="tsku'+tr_num+'"  bool="'+tr_num+'"/>';
	  row.insertCell(-1).innerHTML='<input name="qty[]" type="text" id="tqty'+tr_num+'"  />';
	  row.insertCell(-1).innerHTML='<input name="name[]" type="text" id="tname'+tr_num+'"  />';
}

function removeImg(obj){
	  var row = obj.parentNode.parentNode.rowIndex;
      var tbl = document.getElementById('gallery');
      tbl.deleteRow(row);
}

//填充产品
function fillsku(){
	var sku_row = $('#sku_row').val();
	if(sku_row==''){
		$('#sku_row').focus();
		alertify.error('sku列不能为空！');
		return false;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=orderAdd&act=getSkuInfo&jsonp=1',
			data	: {sku_row:sku_row},
			success	: function (msg){
				//console.log(msg.data[0]);return false;
				if(msg.errCode==0){
					var len = msg.data.length;
					var f_sku_value = document.getElementById('tsku0').value;
					var now_tbl = document.getElementById('gallery');
					var now_tr_num = now_tbl.rows.length;
					if(len>=1){
						if(f_sku_value==''){
							var addlen = len;
						}else{
							var addlen = len+1;
						}

						for(var i=1;i<addlen;i++){
							var obj = document.getElementById('addrow');
							var src  = obj.parentNode.parentNode;
							var idx  = src.rowIndex;
							var tbl  = document.getElementById('gallery');
							var row  = tbl.insertRow(idx + 1);
							var tr_num = tbl.rows.length-1;
							row.insertCell(-1).innerHTML='<a href="javascript:;" onclick="removeImg(this)">[-]</a>';
							row.insertCell(-1).innerHTML='<input name="sku[]" type="text" id="tsku'+tr_num+'"  bool="'+tr_num+'"/>';
							row.insertCell(-1).innerHTML='<input name="qty[]" type="text" id="tqty'+tr_num+'"  />';
							row.insertCell(-1).innerHTML='<input name="name[]" type="text" id="tname'+tr_num+'"  />';
						}

					}

					if(f_sku_value==''){
						for(var i=0;i<len;i++){
							var sku_obj = document.getElementById('tsku'+(now_tr_num-1));
							sku_obj.value = msg.data[i][0];
							var qty_obj = document.getElementById('tqty'+(now_tr_num-1));
							qty_obj.value = msg.data[i][1];
							now_tr_num++;
						}
					}else{
						for(var i=0;i<len;i++){
							var sku_obj = document.getElementById('tsku'+now_tr_num);
							sku_obj.value = msg.data[i][0];
							var qty_obj = document.getElementById('tqty'+now_tr_num);
							qty_obj.value = msg.data[i][1];
							now_tr_num++;
						}
					}

					var sku_row_obj = document.getElementById('sku_row');
					sku_row_obj.value = '';
					sku_row_obj.focus();
				}else{
					alertify.error('失败，请联系it！');
				}
			}
		});
}

//查看总数量
function getallqty(){
	var qty = document.getElementsByName('qty[]');
	var num = 0;
	for(var i=0;i<qty.length;i++){
		var qty_num = qty[i].value;
		if(isNaN(qty_num) || qty_num == ""){
			alertify.error('数量只能输入数字');
			qty[i].focus();
			return false;
		}
	}
	for(var i=0;i<qty.length;i++){
		var now_num = parseInt(qty[i].value);
		num += now_num;
	}
	alertify.alert("当前sku总数量为："+num);return false;
}

function othercurrency(){
	var typevalue = $('#ebay_currency').val();
	if(typevalue == '其他'){
		$('#other_currency').show();
		$('#other_currency').focus();
	}else{
		$('#other_currency').hide();
	}
}

function otherptid(){
	var typevalue = $('#ebay_ptid').val();
	if(typevalue == 'paypal' || typevalue == 'Escrow' || typevalue == '其他'){
		$('#other_ptid').show();
		$('#other_ptid').focus();
	}else{
		$('#other_ptid').hide();
	}
}