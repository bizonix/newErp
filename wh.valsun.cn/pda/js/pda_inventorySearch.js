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
		scanProcessTip('上一个订单号还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('sku');
		var sku	= obj.value;
		//alert(sku);return false;
		scanend = false;
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess(sku);
	}
}
function scanProcess(sku){
	createXMLHttpRequest();  
	var url = 'json.php?mod=inventory&act=getInfo&jsonp=1';
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
			var res = xmlHttpRequest.responseText;
			//console.log(res.errMsg);return false;
			var data = eval("("+res+")");
			//console.log(data.data);
			if( data.errCode == 200){
   	            scanProcessTip(data.errMsg,true);
				var obj = document.getElementById('sku');
				obj.focus();
                obj.value = '';
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var newtable = '';
				
				newtable += '<table id="sku_tab_info" cellpadding="0" cellspacing="0" border="1" height="110px" width="212px"><tbody>';
				newtable += '<tr><td>'+data.data.sku+'</td><td>采购人:'+data.data.cg+'</td></tr>';
                newtable += '<td align="center" width="50%">数量:'+data.data.qty+'</td><td>时间:'+data.data.addtime+'</td></tr>';
				newtable += '<tr><td >仓位:'+data.data.goods_location+'</td><td>存货位:'+data.data.goods_store+'</td></tr>';				
				newtable += '<tr><td>现有库存:'+data.data.goods_count+'</td><td align="center">每日均量:'+data.data.everyday_sale+'</td></tr><tr></tr>';
				newtable += '<tr><td>库存天数:'+data.data.goods_count_day+'</td><td>虚拟库存:'+data.data.virtualgoods_count+'</td></tr></tbody></table>';
                
				showdetail.innerHTML = newtable;
				return false;			
			}else{
				var obj = document.getElementById('sku');
				obj.focus();
				obj.value='';
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}
function fuc_onload(){
	document.getElementById('sku').focus();	
}
