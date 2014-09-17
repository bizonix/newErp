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

//收货区域检测
function checkPickZone(){
	var e = e || event;
	if (e.keyCode!=13) return false;
	if(e.keyCode==13){
		var zone = document.getElementById("pickZone").value;
		if(zone==''){
			scanProcessTip('仓位不能为空',false);
			gotoScanStep('pickZone');
			return false;
		}
		scanProcessTip('请输入配货单号',true);
		gotoScanStep('pickInvoice');
	}
}

function orderPick(){
	var e = e || event;
	if (e.keyCode!=13) return false;
	if(e.keyCode==13){
		var zone	= document.getElementById('pickZone').value;
		var invoice = document.getElementById('pickInvoice').value;
		if(zone == "" && invoice != ''){
			scanProcessTip('请先扫描收货区域！',false);
			gotoScanStep('pickZone');
			return false;
		}
		scanProcessTip('开始收货.....',true);
		postdata(zone,invoice);
	}
}

function postdata(zone,invoice){
	createXMLHttpRequest();
	var url = 'json.php?mod=orderReceipt&act=orderPicking&jsonp=1';
	var param = 'zone='+zone+'&invoice='+invoice;
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
				document.getElementById('pickInvoice').value='';
				gotoScanStep('pickInvoice');
			}else{
				scanProcessTip(res.errMsg,false);
				gotoScanStep('pickInvoice');
			}
		}
	}
}

function fuc_onload(){
	document.getElementById('pickZone').focus();
}


