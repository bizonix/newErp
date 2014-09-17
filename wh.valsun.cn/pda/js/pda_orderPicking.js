function checkorderid(){
	var orderid = document.getElementById("orderid");
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		
		createXMLHttpRequest();
		var url = './json.php?act=pda_checkOrder&mod=pda_orderPicking';
		var param = 'orderid='+orderid;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = checkorderidResponse;
		xmlHttpRequest.send(param);
	}
}
function checkorderidResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
		}
	}
}