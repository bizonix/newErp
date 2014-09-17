var xmlHttpRequest; 

function createXMLHttpRequest(){  
	try{ // Firefox, Opera 8.0+, Safari  
		xmlHttpRequest=new XMLHttpRequest();  
	}catch (e){  
		// Internet Explorer
		try{  
			xmlHttpRequest=new ActiveXObject("Msxml2.XMLHTTP");  
		}catch (e){  
			try{  
				xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");  
			}catch (e){
				alert("您的浏览器不支持AJAX!");  
				return false;
			}
		}  
	}  
}  

function scanProcessTip(msg,yesorno,id){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		document.getElementById('mstatus').innerHTML=str;
	}catch(e){}
}

function gotoScanStep(id, opa){
	var obj=document.getElementById(id);
	if(opa){
		obj.className=obj.className+' stephere2';
	}else{
		obj.className=obj.className+' stephere';	
	}
	obj.select();
}

function checkeBayID(){
	var start_now = new Date();
	start_now = start_now.getTime();

	var obj = document.getElementById('ebay_id');
	var ebay_id	= obj.value;
	var keyCode = event.keyCode;

	if (keyCode!=13) return false;
	$("showTime").innerHTML = "";
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess(ebay_id);
	}else{
		scanProcessTip('000',false);
		//setTimeout(function(){
			obj.focus();
			obj.value='';
			gotoScanStep('ebay_id', false);
		//},600);
	}
	var end_now = new Date();
	end_now = end_now.getTime();
	var have_time = (end_now - start_now)/1000;
	$("showTime").innerHTML = "["+have_time+" 秒]";
}

function check_warehouse_sku(){
	var obj = document.getElementById('warehouse_sku');
	var warehouse_sku	= obj.value.toUpperCase();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(warehouse_sku != ''){
		//scanProcessTip('<img src=cx.gif />开始搜索SKU...',true);
		scanProcessTip('开始搜索SKU...',true);
		scanProcess4(warehouse_sku);
	}else{
		scanProcessTip('料号录入失败,请重试!',false);
		//setTimeout(function(){
			obj.focus();
			obj.value='';
			gotoScanStep('warehouse_sku', false);
		//},600);
	}	
}

function check_ebay_sku(){
	var start_now = new Date();
	start_now = start_now.getTime();
	var ebay_id_obj=document.getElementById('ebay_id');
	var ebay_id	= ebay_id_obj.value;
	var ebay_sku_obj=document.getElementById('ebay_sku');
	var ebay_sku = ebay_sku_obj.value.toUpperCase();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	$("showTime").innerHTML = "";
	if(ebay_sku != ''){
		scanProcessTip('扫描料号...',true);
		scanProcess2(ebay_id, ebay_sku);
	}else{
		scanProcessTip('料号录入失败,请重试',false);
		gotoScanStep('ebay_sku', false);
	}
	var end_now = new Date();
	end_now = end_now.getTime();
	var have_time = (end_now - start_now)/1000;
	$("showTime").innerHTML = "["+have_time+" 秒]";
}

function check_ebay_sku2(){
	var start_now = new Date();
	start_now = start_now.getTime();
	var ebay_id_obj=document.getElementById('ebay_id');
	var ebay_id	= ebay_id_obj.value;
	var ebay_sku_obj=document.getElementById('ebay_sku');
	var ebay_sku = ebay_sku_obj.value.toUpperCase();
	var pskunum_obj=document.getElementById('pskunum');
	var pskunum = pskunum_obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	$("showTime").innerHTML = "";
	if(pskunum == ''){
		scanProcessTip('请填写数量',false);
		gotoScanStep('pskunum', false);	
	}else if(ebay_sku != ''){
		//scanProcessTip('料号录入成功',true);
		//ebay_sku_obj.className='textinput';
		//ebay_sku_obj.blur();
		//scanProcessTip('<img src=cx.gif />扫描料号...',true);
		scanProcessTip('扫描料号...',true);
		scanProcess21(ebay_id, ebay_sku, pskunum);
		/*var not_complete = judge_sku_tab(ebay_sku);
		if(not_complete){
			setTimeout(function(){
				ebay_sku_obj.focus();
				ebay_sku_obj.value='';
				gotoScanStep('ebay_sku', false);
			},600);
		}else{
			setTimeout(function(){
				ebay_id_obj.focus();
				ebay_id_obj.value='';
				ebay_sku_obj.value='';
				gotoScanStep('ebay_id', true);
			},600);
		}*/
	}else{
		scanProcessTip('料号录入失败,请重试',false);
		gotoScanStep('ebay_sku', false);
	}
	var end_now = new Date();
	end_now = end_now.getTime();
	var have_time = (end_now - start_now)/1000;
	$("showTime").innerHTML = "["+have_time+" 秒]";
}

function sku_stock(){
    var sku_stock = document.getElementById("sku_stock");
	var obj = document.getElementById('warehouse_sku');
	var warehouse_sku = obj.value;
	var keyCode = event.keyCode;
    if (keyCode!=13) return false;
	createXMLHttpRequest();
	var url = 'process_scan_inventory_sku_ajax.php';
	var param = 'sku_stock='+sku_stock.value+'&warehouse_sku='+warehouse_sku+'&type=saomiao';
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = skustockResponse;
	xmlHttpRequest.send(param);
}




function scanProcess(ebay_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=scanAllSkuOrder&act=orderDetail&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&type=detail';
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse;
	xmlHttpRequest.send(param);
}

function scanProcess2(ebay_id, ebay_sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=scanAllSkuOrder&act=checkSku&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&ebay_sku='+ebay_sku;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse2;
	xmlHttpRequest.send(param);
}

function scanProcess21(ebay_id, ebay_sku, pskunum){
	createXMLHttpRequest();
	var url = 'json.php?mod=scanAllSkuOrder&act=checkSku&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&ebay_sku='+ebay_sku+'&pskunum='+pskunum;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse21;
	xmlHttpRequest.send(param);
}

function scanProcess3(ebay_id,sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=scanAllSkuOrder&act=searchSku&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&sku='+sku;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse3;
	xmlHttpRequest.send(param);
}

function scanProcess4(warehouse_sku){
	createXMLHttpRequest();
	var url = 'process_scan_inventory_sku_ajax.php';
	var param = 'warehouse_sku='+warehouse_sku+'&type=pandian';
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse4;
	xmlHttpRequest.send(param);
}

function reset_skustock(goods_store){
    var obj = document.getElementById('warehouse_sku');
	var reset_skustock = document.getElementById('reset_skustock');
	var warehouse_sku = obj.value;
	var goods_store = reset_skustock.value;
    createXMLHttpRequest();
	var url = 'process_scan_inventory_sku_ajax.php';
	var param = 'goods_store='+goods_store+'&warehouse_sku='+warehouse_sku+'&type=qingkong';
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = reset_skustockResponse;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function skustockResponse(){
    var showskucheck = document.getElementById("showskucheck");
	var sku_stock = document.getElementById("sku_stock");
	var obj = document.getElementById('warehouse_sku');
	//showskucheck.innerHTML = '';
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data=eval('('+res+')');
			if( data.res_code == '200' ){//扫描处理完毕一个SKU,进入下一个SKU处理流程
			    
				scanProcessTip(data.res_msg,true);
				
				obj.select();
				
			}else{
				scanProcessTip(data.res_msg,false);
				//setTimeout(function(){
					obj.focus();
					//obj.value='';
					gotoScanStep('warehouse_sku', false);
				//},600);
			}
		}
	}

}

function reset_skustockResponse(){
    var showskucheck = document.getElementById("showskucheck");
	var sku_stock = document.getElementById("sku_stock");
	var obj = document.getElementById('warehouse_sku');
	//showskucheck.innerHTML = '';
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data=eval('('+res+')');
			if( data.res_code == '200' ){//扫描处理完毕一个SKU,进入下一个SKU处理流程
			    sku_stock.value = "无";
				scanProcessTip(data.res_msg,true);
				
				obj.select();
				
			}else{
				scanProcessTip(data.res_msg,false);
				//setTimeout(function(){
					obj.focus();
					//obj.value='';
					gotoScanStep('warehouse_sku', false);
				//},600);
			}
		}
	}
}

function scanProcessResponse(){
	var showdetail = document.getElementById('showdetail');
	showdetail.innerHTML = '';
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			//console.log(data.errMsg);return false;
			if( data.errCode == 0 ){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(data.errMsg,true);
				if(show_list(data.data)){
					show_scansku();
					var next_order_button = document.getElementById('next_order_button');
					next_order_button.style.display = "block";
					var inquire_sku = document.getElementById('inquire_sku');
					inquire_sku.style.display = "block";
					var ebay_sku_obj = document.getElementById('ebay_sku');
					//setTimeout(function(){
						ebay_sku_obj.focus();
						ebay_sku_obj.value='';
						//gotoScanStep('ebay_sku', true);
					//},600);
				}
			}/*if( data.res_code == '005' ){
				scanProcessTip(data.res_msg,false);
				if(show_list2(data.res_data2)){
					setTimeout(function(){
						ebay_sku_obj.focus();
						ebay_sku_obj.value='';
						//gotoScanStep('ebay_sku', true);
					},600);
				}
			}*/else{
				var obj = document.getElementById('ebay_id');
				scanProcessTip(data.errMsg,false);
				//setTimeout(function(){
					obj.focus();
					obj.value='';
					gotoScanStep('ebay_id', false);
				//},600);
			}
		}
	}
}

function scanProcessResponse2(){
	var ebay_sku_obj=document.getElementById('ebay_sku');
	var pskunum_obj=document.getElementById('pskunum');
	var ebay_sku = ebay_sku_obj.value.toUpperCase();
	pskunum_obj.style.display = "none";
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if(data.errCode == '300'){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(data.errMsg,true);
				show_list(data.data);
				ebay_sku_obj.focus();
				//ebay_sku_obj.value=data.sku;
				//重新扫 ebay_sku
				gotoScanStep('ebay_sku', true);
			}else if(data.errCode == '020'){
				scanProcessTip(data.errMsg,true);
				pskunum_obj.style.display = '';
				pskunum_obj.focus();
				pskunum_obj.value='';
				//ebay_sku_obj.value=data.sku;
				//重新扫 ebay_sku
				gotoScanStep('pskunum', true);
			}else if(data.errCode == '005'){
				scanProcessTip(data.errMsg,true);
				//setTimeout(function(){
					lhref = './scan_all_sku_order.php';
					window.location.href = lhref;
				//},100);
				ebay_sku_obj.focus();
				ebay_sku_obj.value='';
				//重新扫 ebay_sku
				gotoScanStep('ebay_sku', false);
			}else{
				scanProcessTip(data.errMsg,false);
				//ebay_sku_obj.className='textinput stephere';
				ebay_sku_obj.focus();
				//ebay_sku_obj.value=data.sku;
				//重新扫 ebay_sku
				gotoScanStep('ebay_sku', false);
			}
			//setTimeout(function(){
			//},600);
		}
	}
}

function scanProcessResponse21(){
	var ebay_sku_obj=document.getElementById('ebay_sku');
	var pskunum_obj=document.getElementById('pskunum');
	var ebay_sku = ebay_sku_obj.value.toUpperCase();
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if(data.errCode == '300'){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				pskunum_obj.style.display = "none";
				ebay_sku_obj.focus();
				ebay_sku_obj.value='';
				scanProcessTip(data.errMsg,true);
				show_list(data.data);
			}else if(data.errCode == '005'){
				scanProcessTip(data.errMsg,true);
				//setTimeout(function(){
					lhref = 'index.php?mod=ScanAllSkuOrder&act=scanOrder';
					window.location.href = lhref;
				//},100);
			}else{
				scanProcessTip(data.errMsg,false);
				//setTimeout(function(){
				pskunum_obj.focus();
				//ebay_sku_obj.value='';
				pskunum_obj.value='';
				//重新扫 ebay_sku
				gotoScanStep('pskunum', false);
			//},600);
				//ebay_sku_obj.className='textinput stephere';
			}
		}
	}
}

function scanProcessResponse3(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == '400'){
				scanProcessTip(data.errMsg,true);
				show_search_sku(data.data);
				//$('inquire_sku_value').value = data.sku;
			}else{
				scanProcessTip(data.errMsg,false);
				$('inquire_sku_value').value = "";
				gotoScanStep('inquire_sku_value', false);
			}
		}
	}
}

function scanProcessResponse4(){
	var showskucheck = document.getElementById("showskucheck");
	var obj = document.getElementById('warehouse_sku');
	
	//sku_stock = sku_stock.value;
	showskucheck.innerHTML = '';
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			//alert(res);
			var data=eval('('+res+')');
			if( data.res_code == '200' ){//扫描处理完毕一个SKU,进入下一个SKU处理流程
			    obj.value = data.sku;
				scanProcessTip(data.res_msg,true);
				if(show_sku_returninfo(data.res_data)){
				   if(document.getElementsByTagName("body")[0].id=="scan_sku_restock"){
					  var sku_stock = document.getElementById("sku_stock"); 
					  sku_stock.select();
				   }
				}
			}else{
				scanProcessTip(data.res_msg,false);
				//setTimeout(function(){
					obj.focus();
					obj.value='';
					gotoScanStep('warehouse_sku', false);
				//},600);
			}
		}
	}
}
function judge_sku_tab(ebay_sku){
	var ebay_id_obj=document.getElementById('ebay_id');
	var tableObj = document.getElementById('sku_tab');
	var iscompleteObj = document.getElementById('iscomplete');
	var str = '';
	var sku_list = Array();
	var beaden = false;
	scanProcessTip("记录料号与扫描料号不符,请确认料号的正确性!", false);
	//alert(str);
	for(var i=0;i<tableObj.rows.length;i++)
	{
		//alert(tableObj.rows[i].cells[0].innerHTML);
		if(tableObj.rows[i].cells[0].innerHTML == ebay_sku){
			//sku_nums_obj.value = tableObj.rows[i].cells[4].childNodes[2].value;
			/*for(var i=0; i < tableObj.rows[i].cells.length; i++){
				alert(i+"==="+tableObj.rows[i].cells[i].innerHTML);	
			}*/
			if(tableObj.rows[i].cells[3].innerHTML == '√'){
				scanProcessTip("请不要重复扫描料号!", false);
				
				//str += "<font color='#FF0000'>请不要重复扫描料号!</font><br />";
			}else{
				scanProcessTip("实际料号与记录符合!", true);
				tableObj.rows[i].cells[3].innerHTML = '√';
			}
			break;
		}
		/*for(var j=0;j<tableObj.rows[i].cells.length;j++) 
		{ 
			str += tableObj.rows[i].cells[j].innerHTML+" "; 
			alert(tableObj.rows[i].cells[0].innerHTML);
			for(var z=0;z<tableObj.rows[i].cells[j].children.length;z++) 
			{ 
				var text = tableObj.rows[i].cells[j].children[z];//取得text object 
				str += text.value;
			}
		} 
		str+="\n"; */
	}
	for(var i=0;i<tableObj.rows.length;i++) 
	{
		if(tableObj.rows[i].cells[3].innerHTML == '×'){
			beaden = true;
			sku_list.push(tableObj.rows[i].cells[0].innerHTML);
		}
	}
	if(beaden){
		str += "<font color='#FF0000'>还剩下 ( " + sku_list.join(", ") + " )&nbsp;&nbsp;未完成匹配!</font>";
	}else{
		str += "<font color='#33CC33'>SKU 全部匹配完成!</font>";
	}
	iscompleteObj.innerHTML = str;
	return beaden;
}

function show_list(datas){
	var showdetail = document.getElementById('showdetail');
	showdetail.innerHTML = '';
	var newtable = '';
	//var rowspan_length = parseInt(datas.detail.length) + 1;
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:5px; padding:3px 0;"><legend>未扫描料号</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>&nbsp;&nbsp;SKU</td><td>数量</td><td>库存</td><td>仓位号</td></tr>';
	newtable += '<tr><td colspan="4">';
	newtable += '<select name="select_sku" id="select_sku" width="100%">';
	for(var i=0; i<datas.detail.length; i++){
		newtable += '<option value="i">'+datas.detail[i].sku+'-------['+datas.detail[i].nums+']-------['+datas.detail[i].goods_count+']---'+datas.detail[i].gl+'</option>';
	}
	//newtable +='</td><td align="center">'+datas.detail[0].nums+'</td><td align="center">'+datas.detail[0].gl+'</td>';
	newtable += '</select></td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}

/*function show_list2(datas){
	var showdetail = document.getElementById('showdetail');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:5px; padding:3px 0;"><legend>订单已经扫描完成</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>&nbsp;&nbsp;SKU</td><td>扫描人</td><td>扫描时间</td></tr>';
	for(var i=0; i<datas.detail.length; i++){
		newtable += '<tr><td>'+datas.detail[i].sku+'</td><td>'+datas.detail[i].scaner+'</td><td>'+datas.detail[i].scantime+'</td></tr>';
	}
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}*/
function show_scansku(){
	var scansku = document.getElementById('scansku');
	scansku.innerHTML = '';	
	scansku.innerHTML = 'SKU: <input name="ebay_sku" type="text" id="ebay_sku" onkeydown="check_ebay_sku();" style="width:90px;" class="textinput" />&nbsp;&nbsp;<input style="display:none;" name="pskunum" id="pskunum" onkeydown="check_ebay_sku2();" value="" style="width:40px;" class="numinput" />';
}
function show_all_sku(datas){
	var showallsku = document.getElementById('showallsku');
	showallsku.innerHTML = '';
	var newtable = '';
	//var rowspan_length = parseInt(datas.detail.length) + 1;
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td style="border-bottom:solid 1px #333333;">SKU</td><td style="border-bottom:solid 1px #333333;">状态( √ / × )</td></tr>';
	for(var i=0; i<datas.detail.length; i++){
		newtable += '<tr><td>'+datas.detail[i].sku+'</option></td><td>'+datas.detail[i].status+'</td></tr>';
	}
	newtable += '</table>';
	showallsku.innerHTML = newtable;
	if(newtable != '') showallsku.style.display="block";
}

function show_search_sku(datas){
	var showallsku = $('show_search_info');
	show_search_info.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="show_sku_fieldset" style=" margin-top:5px; padding:5px 5px;"><legend>信息</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	/*newtable += '<tr><td style="border-bottom:solid 1px #333333;">SKU</td><td style="border-bottom:solid 1px #333333;">数量</td><td style="border-bottom:solid 1px #333333;">仓位号</td><td style="border-bottom:solid 1px #333333;">库存</td><td style="border-bottom:solid 1px #333333;">状态</td></tr>';*/
	newtable += '<tr><td>数量:'+datas.nums+'</td></tr><tr><td>仓位号:'+datas.gl+'</td></tr><tr><td>现有库存:'+datas.gc+'</td></tr><tr><td>库存天数:'+datas.day+'</td></tr><tr><td>状态:'+datas.is_scan+'</td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	show_search_info.innerHTML = newtable;
	if(newtable != '') return true;
}

function show_sku_returninfo(datas){
	var showskucheck = $('showskucheck');
	var bodyid = document.getElementsByTagName("body");
	//var url = window.location;
	//alert(bodyid[0].id);
	showskucheck.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="skuinfo"><legend>信息</legend>';
	newtable += '<table id="sku_tab_info" cellpadding="0" cellspacing="0" border="0" height="110px" width="212px">';
	newtable += '<tr><td>采购人:'+datas.cg+'</td></tr><tr><td>仓位号:'+datas.goods_location+'</td></tr>';
	if(bodyid[0].id=="scan_sku_restock"){
	    newtable += '<tr><td>存货位置:<input type="text" value="'+datas.goods_store+'" id="sku_stock" onkeydown="sku_stock()"/>&nbsp;<input id="reset_skustock" value="清空" type="button" onclick="reset_skustock()"/></td></tr>';
	}else{
	    newtable += '<tr><td>存货位置:'+datas.goods_store+'</td></tr>';
	}
	newtable += '<tr><td>现有库存:'+datas.goods_count+'</td></tr><tr><td>库存天数:'+datas.goods_count_day+'</td></tr><tr><td>虚拟库存:'+datas.virtualgoods_count+'</td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showskucheck.innerHTML = newtable;
	if(newtable != '') showskucheck.style.display="block";
	return true;
}

function show_sku_info(datas){
	var showallsku = $('show_search_info');
	show_search_info.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="show_sku_fieldset" style=" margin-top:5px; padding:5px 5px;"><legend>信息</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	/*newtable += '<tr><td style="border-bottom:solid 1px #333333;">SKU</td><td style="border-bottom:solid 1px #333333;">数量</td><td style="border-bottom:solid 1px #333333;">仓位号</td><td style="border-bottom:solid 1px #333333;">库存</td><td style="border-bottom:solid 1px #333333;">状态</td></tr>';*/
	newtable += '<tr><td>数量:'+datas.nums+'</td></tr><tr><td>仓位号:'+datas.gl+'</td></tr><tr><td>现有库存:'+datas.gc+'</td></tr><tr><td>库存天数:'+datas.day+'</td></tr><tr><td>状态:'+datas.is_scan+'</td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	show_search_info.innerHTML = newtable;
	if(newtable != '') show_search_info.style.display="block";
}

function header_page(cid){
	if(cid != undefined){
		switch(cid){
			case -1:
				lhref = './scan_pda_index.php';
				break;
			case 0:
				lhref = './scan_pda_index.php?action=Loginout';
				break;
			case 1:
				lhref = './scan_pda_index3.php';
				break;
			case 2:
				//alert("开发中。。。");
				lhref = './scan_pda_index2.php';
				break;
			case 3:
				lhref = './scan_search_sku.php';
				break;
			case 4:
				//常规入库
				lhref = './scan_sku_shelf.php';
				break;
			case 5:
				//退货入库
				lhref = './scan_sku_return.php';
				break;
			case 6:
				//调整入库
				lhref = './scan_pda_cancel.php';
				break;
			case 7:
				//常规出库
				lhref = 'index.php?mod=ScanAllSkuOrder&act=scanOrder';
				break;
			case 8:
				//快递出库
				lhref = './scan_pda_express_order.php';
				break;
			case 9:
				//包装扫描
				lhref = './scan_pda_package_page.php';
				break;
			case 10:
				//包装扫描
				lhref = './scan_pda_order_return.php';
				break;
			case 11:
				//复核主页
				lhref = './scan_pda_review.php';
				break;
			case 12:
				//挂号复核
				lhref = './scan_pda_register_review.php';
				break;
			case 13:
				//退货操作页面
				lhref = './scan_pda_return_index.php';
				break;
			case 14:
				//料号退货
				lhref = './scan_pda_order_return2.php';
				break;
			case 15:
				//点货扫描操作
				//alert("待开发!");
				
				//return false;
				lhref = './scan_pda_package_check.php';
				break;
			case 16:
				//记录扫描操作
				lhref = './scan_pda_insert_data.php';
				break;
			case 20:
				//配货清单出库
				lhref = './scan_pda_pick_list.php';
				break;
			case 21:
				//包装材料出库
				lhref = './scan_pda_wrapper_skuout.php';
				break;
			case 22:
				//包装材料出库
				lhref = './scan_pda_sku_test.php';
				break;
                    case 23:
                        //快递复核
                                lhref = './expressrecheck.php'
                                    break;
		    case 24:
				//订单分区扫描出库
				lhref = './pda_order_partion_scan.php';
				break;
			case 25:
				//移库主页
				lhref = './scan_pda_shiftlibrary_index.php';
				break;
			case 26:
				//移入
				lhref = './scan_pda_shiftlibrary_in.php';
				break;
			case 27:
				//移出
				lhref = './scan_pda_shiftlibrary_out.php';
				break;
                        case 28:
				//快递IQC复核
				lhref = './expressiqccheck.php';
				break;
			case 29:
				//新版常规出库
				lhref = './scan_sku_shelf_new.php';
				break;
			case 30:
				//补货操作
				lhref = './scan_sku_restock.php';
				break;
		}
		window.location.href = lhref;
	}
}

function inquire_sku(sku){
	sku = sku.toUpperCase();
	var ebay_id	= $('ebay_id').value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(sku != ''){
		scanProcess3(ebay_id,sku);
	}else{
		scanProcessTip('请扫描料号!',false);
		$('inquire_sku_value').value = "";
		gotoScanStep('inquire_sku_value', false);
	}	
}

function showWindow(){
  if(document.getElementById("divWin"))
  {
   $("divWin").style.zIndex=999;
   $("divWin").style.display="";
  }
  else
  {
   var objWin=document.createElement("div");
   objWin.id="divWin";
   objWin.style.position="absolute";
   objWin.style.top = "50px";   
   objWin.style.left = "10px";   
   objWin.style.width="180px";
   objWin.style.height="150px";
   objWin.style.border="2px solid #AEBBCA";
   objWin.style.background="#FFF";
   objWin.style.zIndex=999;
   document.body.appendChild(objWin);
  }
  
  if(document.getElementById("win_bg"))
  {
   $("win_bg").style.zIndex=998;
   $("win_bg").style.display="";
  }
  else
  {
   var obj_bg=document.createElement("div");
   obj_bg.id="win_bg";
   obj_bg.className="win_bg";
   document.body.appendChild(obj_bg);
  }

  var str="";
  str+='<div class="winTitle" onMouseDown="startMove(this,event)" onMouseUp="stopMove(this,event)"><span class="title_left">查询</span><span class="title_right"><a href="javascript:closeWindow()" title="单击关闭">关闭</a></span><br style="clear:right"/></div>';  //标题栏
  str+='<div class="winContent">SKU: <input name="inquire_sku_value" type="text" id="inquire_sku_value" onkeydown="inquire_sku(this.value);" style="width:120px;" class="textinput" /><div id="show_search_info"></div></div>';  //窗口内容
  $("divWin").innerHTML=str;
  $("inquire_sku_value").focus();
}
function closeWindow(){
  $("divWin").style.display="none";
  $("win_bg").style.display="none";
  $("ebay_sku").focus();
}
function $(o){
  return document.getElementById(o);
}
function startMove(o,e){
  var wb;
  if(document.all && e.button==1) wb=true;
  else if(e.button==0) wb=true;
  if(wb)
  {
    var x_pos=parseInt(e.clientX-o.parentNode.offsetLeft);
    var y_pos=parseInt(e.clientY-o.parentNode.offsetTop);
    if(y_pos<=o.offsetHeight)
    {
      document.documentElement.onmousemove=function(mEvent)
      {
        var eEvent=(document.all)?event:mEvent;
        o.parentNode.style.left=eEvent.clientX-x_pos+"px";
        o.parentNode.style.top=eEvent.clientY-y_pos+"px";
      }
    }
  }
}
function stopMove(o,e){
  document.documentElement.onmousemove=null;
}
function fuc_onload(){
	$("ebay_id").focus();	
}
function fuc_onload2(){
	$("scansku").focus();	
}
function fuc_onload3(){
	$("warehouse_sku").focus();	
}

function startList() {
	if (document.all&&document.getElementById) {
		navRoot = document.getElementById("nav");
		for (i=0; i<navRoot.childNodes.length; i++) {
			node = navRoot.childNodes[i];
			if (node.nodeName=="LI") {
				node.onmouseover=function() {
					this.className+=" over";
				}
				node.onmouseout=function() {
					this.className=this.className.replace(" over", "");
				}
			}
		}
	}
}

function scan_in_wh(){
	skunums
	var scansku_obj=document.getElementById('scansku');
	var skunums=document.getElementById('skunums').value;
	var scansku = scansku_obj.value.toUpperCase();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(scansku != '' && skunums != ''){
		//scanProcessTip('<img src=cx.gif />扫描料号...',true);
		scanProcess5(scansku,skunums);
	}else{
		scanProcessTip('料号录入失败,请重试',false);
		gotoScanStep('ebay_sku', false);
	}	
}
function scanProcess5(scansku,skunums){
	createXMLHttpRequest();
	var url = 'process_scan_all_sku_order_ajax.php';
	var param = 'scan_sku='+scansku+'&skunums='+skunums+'&type=inwarehouse';
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse5;
	xmlHttpRequest.send(param);
}
function scanProcessResponse5(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var data=eval('('+res+')');
			if( data.res_code == '200'){
				scanProcessTip(data.res_msg,true);
				show_search_sku(data.res_data);
			}else{
				scanProcessTip(data.res_msg,false);
				$('inquire_sku_value').value = "";
				gotoScanStep('inquire_sku_value', false);
			}
		}
	}
}
function show_menu_list(){
	var menuname_obj=document.getElementById('menuname');
	var display_value = menuname_obj.style.display;
	if(display_value!=''){
		menuname_obj.style.display = '';
		//menuname_obj.style.backgroundColor = '#f4f4f4';
	}else{
		menuname_obj.style.display = 'block';
		//menuname_obj.style.backgroundColor = '#f4f4f4';
	}
}