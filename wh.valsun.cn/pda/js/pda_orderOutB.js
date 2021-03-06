var xmlHttpRequest; 
var scanend = true;
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
function scanProcessTip(msg,yesorno){
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
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}
function checkOrderId(){
	if(!scanend){
		scanProcessTip('上一个订单号还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('orderId');
		var orderId	= obj.value;
		//alert(orderId);return false;
		scanend = false;
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess(orderId);
	}
}
function scanProcess(orderId){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_orderOutB&act=getOrderInfo&jsonp=1';
	var param = 'orderId='+orderId;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res.errMsg);return false;
			var data = eval("("+res+")");
			//console.log(data.data);
			if( data.errCode == 200){
				scanProcessTip(data.errMsg,true);
				var scan_sku = document.getElementById('scan_sku');
				var sku = document.getElementById('sku');
				scan_sku.style.display = 'block';
                document.getElementById('now_orderId').value = data.data.orderId;
				sku.focus();
				return false;			
			}else{
				var obj = document.getElementById('orderId');
				obj.focus();
				obj.value='';
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function checkSku(){
	if(!scanend){
		scanProcessTip('上一个sku还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var sku_obj = document.getElementById('sku');
		var group_obj = document.getElementById('now_orderId');
		//var now_sku = document.getElementById('now_sku').value;
		var orderId	= group_obj.value;
		var sku	= sku_obj.value;
		/*if(sku!=now_sku){
			//sku_obj.value = '';
			sku_obj.value = sku;
			sku_obj.select();
			scanProcessTip('所扫描料号与当前料号不符,'+sku+","+now_sku,false);
			return false;
		}*/
		//alert(sku);return false;
		scanend = false
		scanProcess1(orderId,sku);
	}
}
function scanProcess1(orderId,sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_orderOutB&act=checkSku&jsonp=1';
	var param = 'orderId='+orderId+'&sku='+sku;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse1; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse1(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			//alert(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var newtable = '';
				//newtable += '<br/>';
				
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">SKU</td><td>需求数</td><td>已配</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.sku+'</td><td width="80px">'+data.data.num+'</td><td>'+data.data.assignNum+'</td></tr>';				
				newtable += '</table>';
				newtable += '<span style="font-size:15px; padding-top:2px;">实际配货数量:</span><input type="text" class="textinput" name="sku_num" id="sku_num" onkeydown="checkSkuNum()" value="" />';
                
				showdetail.innerHTML = newtable;
                document.getElementById('sku').value    =   data.data.sku;
                var sku_num = document.getElementById('sku_num');
				sku_num.focus();

			}else{
				var obj = document.getElementById('sku');
				obj.value='';
				obj.select();
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function checkSkuNum(){
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var sku_obj = document.getElementById('sku');
		var group_obj = document.getElementById('now_orderId');
		var orderId	= group_obj.value;
		var sku	= sku_obj.value;
		var sku_num_obj	= document.getElementById('sku_num');
		var sku_num = sku_num_obj.value;
//		var now_orderId = document.getElementById('now_orderId').value;
//		var now_pname = document.getElementById('now_pname').value;
		if(sku_num==''){
			scanProcessTip('配货数量不能为空，请确认!',false);
			return false;
		}
		sku_num_obj.blur();
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess2(orderId, sku, sku_num);
	}
}

function scanProcess2(orderId, sku, sku_num){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_orderOutB&act=checkSkuNum&jsonp=1';
	var param = 'orderId='+orderId+'&sku='+sku+'&sku_num='+sku_num;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse2; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse2(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			//console.log(data.data);
			if( data.errCode == 0){
                var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var newtable = '';
				
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">SKU</td><td>需求数</td><td>已配</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.sku+'</td><td width="80px">'+data.data.num+'</td><td>'+data.data.assignNum+'</td></tr>';				
				newtable += '</table>';
				
				newtable += '<span style="font-size:15px; padding-top:2px;">实际配货数量:</span><input type="text" class="textinput" name="sku_num" id="sku_num" onkeydown="checkSkuNum()" value="" />';
				showdetail.innerHTML = newtable;
                
                scanProcessTip(data.errMsg, true);
                document.getElementById('sku_num').value = '';
                var sku   =   document.getElementById('sku');
                sku.value = '';
                sku.focus();
                //scanProcess(orderId);			
			}else if(data.errCode == 200){
                scanProcessTip(data.errMsg, true);
                document.getElementById('sku').value = '';
                var orderId   =   document.getElementById('orderId');
                orderId.value = '';
                orderId.focus();
                var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
			}else{
				scanProcessTip(data.errMsg,false);
				var sku_num = document.getElementById('sku_num');
				sku_num.focus();
			}
		}
	}
}

function header_page(cid){
	if(cid != undefined){
		switch(cid){
			case 15:
				//配货清单出库
				lhref = 'index.php?mod=pda_orderOutB&act=pda_scanPickList';
				break;
		}
		window.location.href = lhref;
	}
}

function next_sku(){
	var now_orderId = document.getElementById('now_orderId').value;
	var now_sku   = document.getElementById('now_sku').value;
	var now_pname = document.getElementById('now_pname').value;
	var group_obj = document.getElementById('orderId');
	var orderId	= group_obj.value;
	scanProcessTip('数据正在提交，请稍等!',true);
	scanProcess3(orderId,now_orderId,now_sku,now_pname);	
}

function scanProcess3(orderId,now_orderId,now_sku,now_pname){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_orderOutB&act=nextSku&jsonp=1';
	var param = 'orderId='+orderId+'&now_orderId='+now_orderId+'&now_sku='+now_sku+'&now_pname='+now_pname;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse3; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse3(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				var showdetail = document.getElementById('sku_info');
				showdetail.innerHTML = '';
				var newtable = '';
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">仓位</td><td>sku</td><td>需配数量</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.goods_location+'</td><td width="80px">'+data.data.sku+'</td><td>'+data.data.sku_amount+'</td></tr>';				
				newtable += '</table>';
				showdetail.innerHTML = newtable;
				
				var now_orderId = document.getElementById('now_orderId');
				now_orderId.value = data.data.orderId;
				var now_sku = document.getElementById('now_sku');
				now_sku.value = data.data.sku;
				var now_pname = document.getElementById('now_pname');
				now_pname.value = data.data.goods_location;
				var now_sku_num = document.getElementById('now_sku_num');
				now_sku_num.value = data.data.sku_amount;
				var sku_obj = document.getElementById('sku');
				sku_obj.value='';
				sku_obj.focus();
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				scanProcessTip(data.errMsg,true);
			}else{
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

//结束调拨单配货
function endAssignList(){
    var now_orderId    = document.getElementById('now_orderId').value;
    var group           = document.getElementById('orderId').value;
    if(group == ''){
        fuc_onload();
        scanProcessTip('请输入调拨单号!', false);
    }else{
        scanProcessTip('数据正在提交，请稍等!',true);
	    scanProcess4(group, now_orderId);
    }
}

function scanProcess4(group, now_orderId){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_orderOutB&act=endAssignList&jsonp=1';
	var param = 'group='+group+'&now_orderId='+now_orderId;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse4; 
	xmlHttpRequest.send(param); 
}

//处理调拨完结配货返回信息函数 
function scanProcessResponse4(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				document.getElementById('sku_info').innerHTML   =   '';				
				document.getElementById('now_orderId').value   =   '';
                document.getElementById('orderId').value       =   '';
				document.getElementById('now_sku').value        =   '';
				scanProcessTip(data.errMsg,true);  
			}else{
				scanProcessTip(data.errMsg,false);
			}
            fuc_onload();
		}
	}
}

function fuc_onload(){
	document.getElementById('orderId').focus();	
}
