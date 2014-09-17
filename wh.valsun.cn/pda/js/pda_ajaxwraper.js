/*
 * pda ajax请求包装器
 * url 请求的url地址
 * postdata post数据 没有的话填 {}
 * callback 回调处理函数 处理真实的业务逻辑
 */

function pdaAjax(url, postdata, callback){
	var xmlHttpRequest;																				//生成ajax请求对象
	try { 																							// Firefox, Opera 8.0+, Safari
		xmlHttpRequest = new XMLHttpRequest();
	} catch (e) {
		// Internet Explorer
		try {
			xmlHttpRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				return false;
			}
		}
	}
	
	var postStr = '';
	for(key in postdata){
		postStr	+= key+postdata[key]+'&'
	}
	
	xmlHttpRequest.open("POST", url, true);
	xmlHttpRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = requesthandel;
	xmlHttpRequest.send(postStr);

	function requesthandel(){
		if(xmlHttpRequest.readyState == 4){  
			if(xmlHttpRequest.status == 200){
				var res = xmlHttpRequest.responseText;
				var data=eval('('+res+')');
				callback(data);
			}
		}
	}
	
}