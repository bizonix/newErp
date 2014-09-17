function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
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

function checksku(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku = document.getElementById("sku").value;
		createXMLHttpRequest();
		var url = './json.php?act=whShelfSku&mod=whShelf&jsonp=1';
		var param = 'sku='+sku;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = checkSkuResponse;
		xmlHttpRequest.send(param);
	}
}
function checkSkuResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data = eval("("+res+")");
			//alert(data);
			//console.log(data);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("sku").value = data.data.sku;
				gotoScanStep("nums");	
			}else{
				scanProcessTip(data.errMsg,false);
				document.getElementById("sku").value = data.data;
				gotoScanStep("sku");
			}
		}
	}
}

function fuc_onload(){
	document.getElementById("sku").focus();
}

function checknums(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku  = document.getElementById("sku").value;
		var nums = document.getElementById("nums").value;
		var check_number = /^\d+$/;
		
		if(sku==''){
			scanProcessTip('sku不能为空，请确认!',false);
			return false;
		}
		if(!check_number.test(nums)){
			scanProcessTip('上架数量有误，请确认!',false);
			return false;
		}
		if(confirm('上架入库数量为'+nums+',是否确认!')){
			scanProcessTip("数据正在提交...",true);
			document.getElementById("nums").blur();
			createXMLHttpRequest();
			var url = './json.php?act=whPackageShelf&mod=whShelf&jsonp=1';
			var param = 'sku='+sku+"&nums="+nums;
			xmlHttpRequest.open("POST",url,true); 
			xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
			xmlHttpRequest.onreadystatechange = checkNumsResponse;
			xmlHttpRequest.send(param);
		}else{
			gotoScanStep('nums');
		}
	}
}

function checkNumsResponse(){
	//var sku = document.getElementById("sku").value;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var result = xmlHttpRequest.responseText;
			data = eval("("+result+")");
			if(data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				document.getElementById('sku').value = '';
				document.getElementById('nums').value = '';
				gotoScanStep('sku');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('nums');
			}
		}
	}
}