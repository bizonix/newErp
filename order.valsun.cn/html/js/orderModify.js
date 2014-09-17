/*
 * 订单中心 orderindex.js
 * ADD BY chenwei 2013.09.11
 */
$(function() {
	$(".fancybox").fancybox({
		helpers: {
			title : {
				type : 'outside'
			},
			overlay : {
				speedOut : 0
			}
		}
	});
    
    var spuArr = new Array();
	$("img[name='ajaxImg']").each(function(index){
		//alert(index);
        var sku = $(this).attr('sku');
        var spu = $(this).attr('spu');
		//console.log(sku+"======"+spu);
        if(sku == '' || spu == ''){
            return true;
        }
        if($.inArray(spu, spuArr) == -1){
            spuArr.push(spu);
        }         
	});

	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: web_api+'json.php?mod=common&act=getSpuAllPic',
		data	: {spu:spuArr},
		timeout	: 10000,
		async	: true,
		success	: function (msg){
			//alert(msg); return;
			if(msg.data != false){
			   $("img[name='ajaxImg']").each(function(index){
      	            var sku = $(this).attr('sku');
                    var spu = $(this).attr('spu');
                    if(sku == '' || spu == ''){
                        return true;
                    }
                    //alert(msg.data[spu]);
                   if (msg.data[spu] == '' || msg.data[spu] == 'null' || msg.data[spu] == null) {
                   		//$("#ajaxImg_"+sku).attr("src",msg.data[spu]);
						$("#ajaxImg_"+sku).attr({"src":"./images/no_image.gif","width":"50px","height":"50px"});
						$("#ajaxA_"+sku).attr("href",msg.data[spu]);
                   } else {
                   		$("#ajaxImg_"+sku).attr({"src":msg.data[spu],"width":"50px","height":"50px"});
						$("#ajaxA_"+sku).attr("href",msg.data[spu]);               	
                   }                    	      
            	});
			} else {
				$("img[name='ajaxImg']").each(function(index){
					//alert(index);
					var sku = $(this).attr('sku');
					var spu = $(this).attr('spu');
					$("#ajaxImg_"+sku).attr({"src":"./images/no_image.gif","width":"50px","height":"50px"});
					$("#ajaxA_"+sku).attr("href","./images/no_image.gif");
				});
			}
		}
	});
});

//订单编辑页面,status=1正常情况；status=2状态为2保存并且关闭
function modifySave(){
	var modifyForm = $('#ordermodifiveForm');
	
	var edit_ostatus = $('#edit_ostatus');
	var edit_otype = $('#edit_otype');
	if(edit_ostatus.val() == '' || edit_otype.val() == ''){
		alertify.error('请选择订单状态！'); return false;	
	}
	var edit_username = $('#edit_username');
	if(edit_username.val() == ''){
		alertify.error('请填写用户名称！'); return false;	
	}
	var edit_street = $('#edit_street');
	if(edit_street.val() == ''){
		alertify.error('请填写街道名称！'); return false;	
	}
	var edit_address2 = $('#edit_address2');
	var edit_city = $('#edit_city');
	if(edit_city.val() == ''){
		alertify.error('请填写城市名称！'); return false;	
	}
	//var edit_currency = $('#edit_currency');
	var edit_state = $('#edit_state');
	var edit_countryName = $('#edit_countryName');
	if(edit_countryName.val() == ''){
		alertify.error('请填写国家名称！'); return false;	
	}
	var edit_zipCode = $('#edit_zipCode');
	var edit_landline = $('#edit_landline');
	var edit_recordNumber = $('#edit_recordNumber');
	if(edit_recordNumber.val() == ''){
		alertify.error('请填写RecordNumber！'); return false;	
	}
	var edit_phone = $('#edit_phone');
	var edit_transportId = $('#edit_transportId');
	if(edit_transportId.val() == ''){
		alertify.error('请选择物流方式！'); return false;
	}
	
	/*var extralContent = '<input type="hidden" id="action" name="action" value="update" />';
	modifyForm.append('extralContent');*/
	//modifyForm.submit();
	/*$("#action").remove();
	return false;
	alertify.confirm("关闭页面吗？", function (e) {
		if (e) {
			window.close();
		} else {
			// user clicked "cancel"
		}
	});*/
}

/*
 * 选择状态时候，对应类别变化
 * add by Herman.Xi @20131213
 */
function changeOstatus(){
	var ostatus	=	$("#edit_ostatus").val();
	var htmlStr	=	'';
	//$("#otype").val('');
	$("#edit_otype").html('');
	$.ajax(
		{
			type: 'POST',
			url: 'json.php?act=changeOstatusId&mod=StatusMenu&jsonp=1',
			dataType : 'json',
			data        :{"ostatus":ostatus},
			success : function (data){
				if(data.errCode == 200){
						htmlStr	+=	'<option value="">请选择</option>';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+data.data[i].statusCode+'">'+data.data[i].statusName+'</option>';
					}
					$("#edit_otype").html(htmlStr);
				}else{
					alertify.error(data.errMsg);
				}
			}
		}
	);
}

/*
 * change跟踪号，对应类别变化
 * add by Herman.Xi @20131221
 */
function changeTracknumberList(){
	var show_tracknumber	=	$("#show_tracknumber");
	var list_tracknumber	=	$("#list_tracknumber");
	var shtm = "";
	if(list_tracknumber.val() == 'add'){
		shtm = '<input name="edit_tracknumber" type="text" id="edit_tracknumber" value="" />';
		show_tracknumber.html(shtm);
		$("#edit_tracknumber").focus();
	}
}

function delOrderDetail(omOrderDetailId){
	//删除
	var orderid = $('#orderid').val();
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	alertify.confirm("确认要删除该料号吗？", function (e) {
		if (e) {
			//alertify.error('线上环境暂时关闭此功能!');
			//return false;
			// user clicked "ok"
			$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=OrderModify&act=deleteDetail&jsonp=1',
        		data	: {orderid:orderid,omOrderDetailId: omOrderDetailId},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						//setTimeout("window.location.reload(true);",1000);
						//window.location.reload(true);
						window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+ostatus+"&otype="+otype;
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        	});
		} else {
			// user clicked "cancel"
		}
	});
}

function modOrderDetail(omOrderDetailId){
	//保存
	var modifiveDetailForm = $('#ordermodifiveDetailForm');
	var orderid = $('#orderid').val();
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	var edit_itemId = $('#edit_pitemid_'+omOrderDetailId).val();
	var edit_recordNumber = $('#edit_precordno_'+omOrderDetailId).val();
	var edit_sku = $('#edit_psku_'+omOrderDetailId).val();
	var edit_itemTitle = $('#edit_pname_'+omOrderDetailId).val();
	var edit_itemPrice = $('#edit_pprice_'+omOrderDetailId).val();
	var edit_shippingFee = $('#edit_sspfee_'+omOrderDetailId).val();
	var edit_amount = $('#edit_pqty_'+omOrderDetailId).val();
	var edit_note = $('#edit_notes_'+omOrderDetailId).val();
	var detail_platformId = $('#detail_platformId').val();
	var error = true;
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=OrderModify&act=updateDetail&jsonp=1',
		data	: {orderid:orderid,omOrderDetailId:omOrderDetailId,itemId:edit_itemId,recordNumber:edit_recordNumber,sku:edit_sku,itemTitle:edit_itemTitle,itemPrice:edit_itemPrice,shippingFee:edit_shippingFee,amount:edit_amount,note:edit_note,detail_platformId:detail_platformId},
		success	: function (ret){
			if(ret.errCode == '200'){
				//alertify.success(ret.errMsg);
				//setTimeout("window.location.reload(true);",1000);
				//window.location.reload(true);
				//window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+ostatus+"&otype="+otype;
				
			}else{
				
				alertify.error(ret.errMsg);
				return false;
			}
		}
	});

	//modifiveDetailForm.submit();
}

function addOrderDetail(omOrderId){
	var data = {};
	var add_pitemid_data = Array();
	data['add_pitemid'] = Array();
	var add_pitemid = $("input[name='add_pitemid']");
	for(var i=0;i<add_pitemid.length;i++){
		data['add_pitemid'].push(add_pitemid[i].value);
	}
	//data['add_pitemid'] = add_pitemid_data;
	
	data['add_precordno'] = Array();
	var add_precordno = $("input[name='add_precordno']");
	for(var i=0;i<add_precordno.length;i++){
		data['add_precordno'].push(add_precordno[i].value);
	}
	//data['add_precordno'] = add_precordno_data;
	
	data['add_psku'] = Array();
	var add_psku = $("input[name='add_psku']");
	for(var i=0;i<add_psku.length;i++){
		data['add_psku'].push(add_psku[i].value);
	}
	//data['add_psku'] = add_psku_data;
	
	data['add_pname'] = Array();
	var add_pname = $("input[name='add_pname']");
	for(var i=0;i<add_pname.length;i++){
		data['add_pname'].push(add_pname[i].value);
	}
	//data['add_pname'] = add_pname_data;
	
	data['add_pprice'] = Array();
	var add_pprice = $("input[name='add_pprice']");
	for(var i=0;i<add_pprice.length;i++){
		data['add_pprice'].push(add_pprice[i].value);
	}
	//data['add_pprice'] = add_pprice_data;

	data['add_sspfee'] = Array();
	var add_sspfee = $("input[name='add_sspfee']");
	for(var i=0;i<add_sspfee.length;i++){
		data['add_sspfee'].push(add_sspfee[i].value);
	}
	//data['add_sspfee'] = add_sspfee_data;
	
	data['add_pqty'] = Array();
	var add_pqty = $("input[name='add_pqty']");
	for(var i=0;i<add_pqty.length;i++){
		data['add_pqty'].push(add_pqty[i].value);
	}
	//data['add_pqty'] = add_pqty_data;
	
	data['add_notes'] = Array();
	var add_notes = $("textarea[name='add_detail_notes']");
	for(var i=0;i<add_notes.length;i++){
		data['add_notes'].push(add_notes[i].value);
	}
	//data['add_notes'] = add_notes_data;
	
	//var add_notes_data = Array();
	var detail_platformId = $("#detail_platformId").val();
	data['detail_platformId'] = detail_platformId;
	//alert(data['detail_platformId']);
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=OrderModify&act=batchAdd&jsonp=1',
		data	: {data:data, omOrderId: omOrderId},
		success	: function (ret){
			if(ret.errCode == '200'){
				alertify.success(ret.errMsg);
				//setTimeout("window.location.reload(true);",1000);
				//window.location.reload(true);
				//window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+ostatus+"&otype="+otype;
			}else{
				alertify.error(ret.errMsg);
				return false;
			}
		}
	});

}

function addOrderNote(omOrderId){
	//添加
	//$('#action').val('addDetail');
	var orderNoteForm = $('#orderNoteForm');
	var htm = '<input type="hidden" id="action" name="action" value="addNote" /><input type="hidden" id="orderid1" name="orderid" value="'+omOrderId+'" />';
	//alert(action);
	orderNoteForm.append(htm);
	//alert($('#action').val());
	//var thelineshowmsg = $('#thelineshowmsg');
	orderNoteForm.submit();
	//$('#action').val('updateDetail');
}
var note_count = 0;
function addOrderNoteRow(){
	//添加
	
	var orderNoteTable = $('#orderNoteTable'); 
	//var endTr = detailTable.find('tr:end');
	//var row = $("<tr></tr>"); 
	/*var td = $("<td></td>");*/
	var htmltd = '<tr>';
	htmltd += '<td><a onClick="deleteNew(this);">一</a></td>';
	htmltd += '<td><textarea style="width:320px;" name="add_notes" cols="70" rows="3" id="add_notes'+note_count+'"></textarea></td>';
	htmltd += '<td></td>';
	htmltd += '<td></td>';
	htmltd += '</tr>';
	//endTr.append(htmltd); 
	//row.append(td);
	orderNoteTable.append(htmltd);
	row_count++;
}

var note_count2 = 0;
function addOrderNoteTD(){
	//添加
	var orderNoteTD = $('#orderNoteTD'); 
	var htmltd = '<tr valign="top">';
	htmltd += '<td><textarea style="width:220px;" name="add_notes" cols="50" rows="3" id="add_notes'+note_count2+'"></textarea></td>';
	htmltd += '<td><a onClick="deleteNew(this);">一</a></td></tr>';
	orderNoteTD.append(htmltd);
	note_count2++;
}

function bulk_delete_func(omOrderId){
	//批量删除料号
	var list = $("input[name='skucheckbox']");
	var orderid = $('#orderid').val();
	var ostatus = $('#ostatus').val();
	var otype = $('#otype').val();
	var length = list.length;
	var valuestr = '';
	var idar =  Array();
    for (var i=0; i<length; i++) {
		if(!list[i].checked){
			continue;
		}
		idar.push(list[i].value);
    }
	var len = idar.length;
	valuestr = idar.join(',');
	if(len == 0){
		alertify.error('请选择要批量删除的料号!');
		return false;
	}
	if(list.length == 1){
		 alertify.error("订单明细只有一条数据，请不要选择删除！"); 
		 return false;
	}
	alertify.confirm("确认要批量删除的料号吗？", function (e) {
		if (e) {
			//alertify.error('线上环境暂时关闭此功能!');
			//return false;
			// user clicked "ok"
			$.ajax({
        		type	: "POST",
        		dataType: "jsonp",
        		url		: 'json.php?mod=OrderModify&act=batchDeleteDetail&jsonp=1',
        		data	: {omData:valuestr, omOrderId: omOrderId},
				success	: function (ret){
        			if(ret.errCode == '200'){
						alertify.success(ret.errMsg);
						//setTimeout("window.location.reload(true);",1000);
						//window.location.reload(true);
						window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+ostatus+"&otype="+otype;
        			}else{
						alertify.error(ret.errMsg);
						return false;
					}
        		}
        	});
		} else {
			// user clicked "cancel"
		}
	});
}

function selectall_checkbox(obj){
	//全选反选择
	var checkboxs = document.getElementsByName("skucheckbox");
	
		for(var i=0; i<checkboxs.length; i++) 
		{ 
			if(obj.checked != checkboxs[i].checked){
				checkboxs[i].checked=obj.checked;
			}else{
				checkboxs[i].checked=!obj.checked;	
			}
		} 

}

var row_count = 0;
function addNew() 
{ 
	var detailTable = $('#detailTable'); 
	//var endTr = detailTable.find('tr:end');
	//var row = $("<tr></tr>"); 
	/*var td = $("<td></td>");*/
	var htmltd = '<tr>';
	htmltd += '<td></td><td><a onClick="deleteNew(this);">一</a></td>';
	htmltd += '<td><input id="add_pitemid'+row_count+'" name="add_pitemid" type="text" size="10" value="" /></td>';
	htmltd += '<td><input id="add_precordno'+row_count+'" name="add_precordno" type="text" size="10" value="" /></td>';
	htmltd += '<td><input id="add_psku'+row_count+'" name="add_psku" type="text" value="" size="10" /></td>';
	htmltd += '<td><input id="add_pname'+row_count+'" name="add_pname" type="text" value="" size="30" /></td>';
	htmltd += '<td><input id="add_pprice'+row_count+'" name="add_pprice" type="text" size="3" value="" /></td>';
	htmltd += '<td><input id="add_sspfee'+row_count+'" name="add_sspfee" type="text" size="3" value="" /></td>';
	htmltd += '<td><input id="add_pqty'+row_count+'" name="add_pqty" type="text" size="3" value="" /></td>';
	htmltd += '<td></td><td></td>';
	htmltd += '<td><textarea id="add_notes'+row_count+'" name="add_detail_notes" cols="10" rows="3"></textarea></td>';
	htmltd += '<td></td><td></td>';
	htmltd += '</tr>';
	//endTr.append(htmltd); 
	//row.append(td);
	detailTable.append(htmltd);
	row_count++;
}

function deleteNew(obj){
	var thisTr = $(obj).parent().parent();
	//alert(thisTr.html());
	thisTr.remove();
}
function saveAll(){
	var info = modifySave();
	if(info == false){
		return false;
	}
	var orderid = $("#orderid").val();
	var msg = addOrderDetail(orderid);
	if(msg == false){
		alertify.error("添加明细失败！");
		return false;
	}
    var omOrderdetailId = $("input[name='detailId']");
	for(var i=0;i<omOrderdetailId.length;i++){
		var detail_info = modOrderDetail(omOrderdetailId[i].value);
	}
	var data = {};
	
	
	var notes = $("input[name='notes']");
	data['update_notes'] = Array();
	for(var i=0;i<notes.length;i++){
		if(notes[i].value != notes[i].nextSibling.value){
			data['update_notes'].push(notes[i].value+"###"+notes[i].nextSibling.value);
		}
	}
	var notes_new = $("textarea[name='add_notes']");
	data['note_new'] = Array();
	for(var i=0;i<notes_new.length;i++){
		if(notes_new[i].value != ""){
			data['note_new'].push(notes_new[i].value);
			
		}
	}
	var username = $("#username").val();
	var edit_username = $("#edit_username").val();
	if(username != edit_username){
		data['username'] = edit_username;
	}
	
	var ostatus = $("#ostatus").val();
	var edit_ostatus = $("#edit_ostatus").val();
	if(ostatus != edit_ostatus){
		data['orderStatus'] = edit_ostatus;
	}
	
	var otype = $("#otype").val();
	var edit_otype = $("#edit_otype").val();
	if(otype != edit_otype){
		data['orderType'] = edit_otype;
		//alert(edit_otype);
	}
	
	var street = $("#street").val();
	var edit_street = $("#edit_street").val();
	if(street != edit_street){
		data['street'] = edit_street;
	}
	
	var platformUsername = $("#edit_platformUsername").val();
	var edit_platformUsername = $("#edit_platformUsername").val();
	if(platformUsername != edit_platformUsername){
		data['platformUsername'] = edit_platformUsername;
	}
	
	var address2 = $("#address2").val();
	var edit_address2 = $("#edit_address2").val();
	if(address2 != edit_address2){
		data['address2'] = edit_address2;
	}
	
	var actualShipping = $("#actualShipping").val();
	var edit_actualShipping = $("#edit_actualShipping").val();
	if(actualShipping != edit_actualShipping){
		data['actualShipping'] = edit_actualShipping;
	}
	
	var city = $("#city").val();
	var edit_city = $("#edit_city").val();
	if(city != edit_city){
		data['city'] = edit_city;
	}
	
	var state = $("#state").val();
	var edit_state = $("#edit_state").val();
	if(state != edit_state){
		data['state'] = edit_state;
	}
	
	var countryName = $("#countryName").val();
	var edit_countryName = $("#edit_countryName").val();
	if(countryName != edit_countryName){
		data['countryName'] = edit_countryName;
	}
	
	var zipCode = $("#zipCode").val();
	var edit_zipCode = $("#edit_zipCode").val();
	if(zipCode != edit_zipCode){
		data['zipCode'] = edit_zipCode;
	}
	
	var landline = $("#landline").val();
	var edit_landline = $("#edit_landline").val();
	if(landline != edit_landline){
		data['landline'] = edit_landline;
	}
	
	var phone = $("#phone").val();
	var edit_phone = $("#edit_phone").val();
	if(phone != edit_phone){
		data['phone'] = edit_phone;
	}
	
	var transportId = $("#transportId").val();
	var edit_transportId = $("#edit_transportId").val();
	if(transportId != edit_transportId){
		data['transportId'] = edit_transportId;
	}
	
	var edit_tracknumber = $("#edit_tracknumber").val();
	if(edit_tracknumber != "" && edit_tracknumber != null){
		data['edit_tracknumber'] = edit_tracknumber;
	}
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=OrderModify&act=modifyOrder&jsonp=1',
		data	: {orderid:orderid, ostatus: ostatus,otype:otype,data:data},
		success	: function (ret){
			if(ret.errCode == '200'){
				alertify.success(ret.errMsg);
				//setTimeout("window.location.reload(true);",1000);
				
				window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+edit_ostatus+"&otype="+edit_otype;
				
				//window.location.reload(true); 
				//window.location.href = "index.php?mod=orderModify&act=modifyOrderList&jsonp=1&orderid="+orderid+"&ostatus="+ostatus+"&otype="+otype+"&data="+data;
			}else{
				alertify.error(ret.errMsg);
			}
		}
	});
	

}

function recalculated(orderId){
	$.ajax({
		type	:"POST",
	    dataType:"jsonp",
		url		: 'json.php?mod=OrderModify&act=recalculated_bak',
		data	:{orderid:orderId},
		success:function(ret){
			if(ret.errCode == '0'){
				$("#edit_calcWeight").val(ret.data);
				alertify.success('重新计算成功!');
			}else{
				alertify.error(ret.errMsg);
				return false;
			}
		}
	});

}