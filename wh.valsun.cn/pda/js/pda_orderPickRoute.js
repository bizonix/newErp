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
				alert("您的浏览器不支持AJAX！");  
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
function scanProcessTip2(msg,yesorno,id){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		document.getElementById('show_info').innerHTML=str;
	}catch(e){}
}
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}

function orderPickRoute(){
	var e = e || event;
	if (e.keyCode!=13) return false;
	if(e.keyCode==13){
		var invoice = document.getElementById('pickInvoiceRoute').value;
		document.getElementById('show_info').innerHTML='';
		scanProcessTip('开始获取配货区域.....',true);
		postdata(invoice);
	}
}

function postdata(invoice){
	createXMLHttpRequest();
	var url = 'json.php?mod=orderReceipt&act=orderPickRoute&jsonp=1';
	var param = 'invoice='+invoice;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = postdataResponse;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function postdataResponse(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var res=eval('('+res+')');
			if( res.errCode == 200){//收货完毕，进入下一个收货处理流程
				scanProcessTip(res.errMsg,true);
				scanProcessTip2(res.data,true);
				document.getElementById('pickInvoiceRoute').value='';
				gotoScanStep('pickInvoiceRoute');
			}else{
				scanProcessTip(res.errMsg,false);
				gotoScanStep('pickInvoiceRoute');
			}
		}
	}
}

function fuc_onload(){
	document.getElementById('pickInvoiceRoute').focus();
}


