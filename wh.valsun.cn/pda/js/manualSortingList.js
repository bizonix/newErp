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

function fuc_onload(){
	$("ebay_id").focus();
}

function $(o){
  return document.getElementById(o);
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
	//document.getElementById("showTime").innerHTML = "";
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(WA)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess(ebay_id);
	}else{
		scanProcessTip('发货单号格式有误!',false);
		//setTimeout(function(){
			gotoScanStep('ebay_id', false);
		//},600);
	}
	var end_now = new Date();
	end_now = end_now.getTime();
	var have_time = (end_now - start_now)/1000;
	$("showTime").innerHTML = "["+have_time+" 秒]";
}

function scanProcess(ebay_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=WhManualSorting&act=manualSortingCheck&jsonp=1';
	var param = 'waveId='+ebay_id;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse;
	xmlHttpRequest.send(param);
}

function scanProcessResponse(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data = eval("("+res+")");
            var obj = document.getElementById('ebay_id');
			//console.log(data.errMsg);return false;
			if( data.errCode == 200 ){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(data.errMsg,true);
                	obj.focus();
					obj.value='';
			}else{
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