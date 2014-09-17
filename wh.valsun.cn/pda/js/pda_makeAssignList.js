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
function checkSku(){
	if(!scanend){
		scanProcessTip('上一个料号还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('sku');
		var sku	= obj.value;
		//alert(order_group);return false;
		scanend = false;
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess(sku);
	}
}
function scanProcess(sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_makeAssignList&act=getSkuInfo&jsonp=1';
	var param = 'sku='+sku;
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
			var res  = xmlHttpRequest.responseText;
			//console.log(res.errMsg);return false;
			var data = eval("("+res+")");
			//console.log(data.data);
            var sku = document.getElementById('sku');
			if( data.errCode == 200){
				scanProcessTip(data.errMsg,true);
                sku.value   =   data.data;
                document.getElementById('now_sku').value =   data.data;
				document.getElementById('sku_num').focus();
				return false;			
			}else{
				sku.html= '';
				sku.focus();
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

//function checkSku(){
//	if(!scanend){
//		scanProcessTip('上一个sku还没有同步成功,请稍等!',false);
//		return false;
//	}
//	var keyCode = event.keyCode;
//	if (keyCode == 13) {
//		var sku_obj   =   document.getElementById('sku');
//		var sku       =   sku_obj.value;
//		scanend = false;
//		scanProcess1(order_group,sku);
//	}
//}
//function scanProcess1(order_group,sku){
//	createXMLHttpRequest();
//	var url = 'json.php?mod=checkPdaAssignList&act=checkSku&jsonp=1';
//	var param = 'order_group='+order_group+'&sku='+sku;
//	xmlHttpRequest.open("POST",url,true); 
//	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
//	xmlHttpRequest.onreadystatechange = scanProcessResponse1; 
//	xmlHttpRequest.send(param); 
//}
////处理返回信息函数 
//function scanProcessResponse1(){
//	scanend = true;
//	if(xmlHttpRequest.readyState == 4){  
//		if(xmlHttpRequest.status == 200){
//			var res = xmlHttpRequest.responseText;
//			//console.log(res);return false;
//			//alert(res);return false;
//			var data=eval('('+res+')');
//			if( data.errCode == 0){
//				scanProcessTip(data.errMsg,true);
//				//var now_sku_num = document.getElementById('now_sku_num').value;
//				var showdetail = document.getElementById('operationcontents');
//				showdetail.innerHTML = '';
//				var newtable = '';
//				newtable += '<br/>';
//                
//				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
//				newtable += '<tr style="font-size:13px"><td style="padding:2px">SKU</td><td>配货数量</td><td>已复核数</td></tr>';
//				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.sku+'</td><td width="80px">'+data.data.sku_amount+'</td><td>'+data.data.check_num+'</td></tr>';				
//				newtable += '</table>';
//				showdetail.innerHTML = newtable;
//				
//                var obj = document.getElementById('sku');
//				obj.value='';
//				obj.select();
//			}else if(data.errCode == 006){
//			     scanProcessTip(data.errMsg,true);
//                 var obj = document.getElementById('group_id');
//				 obj.value='';
//				 obj.select();
//                 document.getElementById('sku').value='';
//			}else{
//				var obj = document.getElementById('sku');
//				obj.value='';
//				obj.select();
//				var showdetail = document.getElementById('operationcontents');
//				showdetail.innerHTML = '';
//				scanProcessTip(data.errMsg,false);
//			}
//		}
//	}
//}

function checkSkuNum(){
	var keyCode = event.keyCode;
	if (keyCode == 13) {
	    var sku_num_obj   =   document.getElementById('sku_num')
		var sku_num   =   sku_num_obj.value;
		var sku       =   document.getElementById('now_sku').value;
		//alert(sku_num);return false;
		if(sku_num==''){
			scanProcessTip('配货数量不能为空，请确认!',false);
			return false;
		}
		sku_num_obj.blur();
		//scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess2(sku,sku_num);
	}
}

function scanProcess2(sku,sku_num){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_makeAssignList&act=checkSkuNum&jsonp=1';
	var param = 'sku='+sku+'&sku_num='+sku_num;
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
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 200){
			    scanProcessTip(data.errMsg, true);
                //var sku_obj =   document.getElementById('sku');
                $('sku').value  =   '';
                $('sku').focus();
                document.getElementById('sku_num').value = '';			
			}else{
				scanProcessTip(data.errMsg,false);
				var sku_num = document.getElementById('sku_num');
				sku_num.focus();
                sku_num.value = '';
			}
		}
	}
}



function fuc_onload(){
	document.getElementById('sku').focus();	
}

function $(o){
  return document.getElementById(o);
}
function makeAssignList(){
	scanProcessTip('数据正在提交，请稍等!',true);
	scanProcess3();	
}

function scanProcess3(){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_makeAssignList&act=makeAssignList&jsonp=1';
	var param = '';
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
            var sku_obj     =   $('sku');
            sku_obj.value   =   '';
            sku_obj.focus();
			if( data.errCode == 200){
				scanProcessTip(data.errMsg,true);
			}else{
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}