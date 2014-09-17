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
function checkesku(){
	var obj=document.getElementById('sku');
	var sku	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_sku = /^MT+[0-9_A-Z]+$/;
	if(	p_sku.test(sku) || true){
		scanProcess(sku);
	}else{
		scanProcessTip('非包材料号,请确认！',false);
		gotoScanStep('sku')
	}
}

function checknum(){
	var sku=document.getElementById('sku').value;
	var position=document.getElementById('position').value;
	var obj=document.getElementById('num');
	var num	= obj.value;
	if(sku == ""){
		scanProcessTip('请先扫描条码！',false);
		gotoScanStep('sku');
		return false;
	}
	if(position == ""){
		scanProcessTip('请先扫描出库仓位！',false);
		gotoScanStep('position');
		return false;
	}
	if(num == ''){
		scanProcessTip('请先填写出库数量！',false);
		gotoScanStep('num');
		return false;
	}
	var p_sku = /^[0-9]+$/;
	if(	p_sku.test(num)){
		if(confirm('出库数量为'+num+',是否确认!')){
			scanProcessTip('开始同步数量.....',true);
			obj.className='numinput';
			obj.blur();
			postdata(sku,position,num);
		}else{
			gotoScanStep('sku')
		}
	}else{
		scanProcessTip('数量有误,请重新输入',false);
		gotoScanStep('num')
	}
}

function scanProcess(sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=wrapperSkuOut&act=selectsku&jsonp=1';
	var param = 'sku='+sku;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse;
	xmlHttpRequest.send(param);
} 

function postdata(sku,position,num){
	createXMLHttpRequest();
	var url = 'json.php?mod=wrapperSkuOut&act=postsku&jsonp=1';
	var param = 'sku='+sku+'&position='+position+'&num='+num;
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
			if( res.errCode == 200){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(res.errMsg,true);
				document.getElementById('num').value='';
				document.getElementById('sku').value='';
				document.getElementById('position').value='';
				document.getElementById('show_info').innerHTML='';
				gotoScanStep('sku');
			}else{
				scanProcessTip(res.errMsg,false);
				gotoScanStep('sku');
			}
		}
	}
}

//处理返回信息函数 
function scanProcessResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var res=eval('('+res+')');
			if(res.errCode == 2){
				scanProcessTip(res.errMsg,true);
				var obj=document.getElementById('sku');
				//obj.className='textinput';
				obj.blur();
				obj.value = res.data['sku'];
				scanProcessTip2(res.data['res_info'],true);
				gotoScanStep('position');
			}else{
				var obj=document.getElementById('sku');
			    obj.value = res.data['sku'];
				scanProcessTip(res.errMsg,false);
				gotoScanStep('ebay_id');
			}
		}
	}
}

function get_array(name, type){
	var garray = document.getElementsByName(name);
	var results = [];
	for(var i=0; i<garray.length; i++){
		results.push(garray[i].value);
	}
	return type=='str' ? results.join(',') : results;
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

function $(o){
  return document.getElementById(o);
}

function fuc_onload(){
	document.getElementById('sku').focus();
}

function func_plus(){
	var num=$('num').value;
	if(num == ''){num = 0;}
	num = parseInt(num);
	num++;
	if(num < 10000){
	$('num').value = num;
	}
}

function func_minus(){
	var num=$('num').value;
	num = parseInt(num);
	num--;
	if(num > 0){
	$('num').value = num;
	}
}

function inputposition(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var position = document.getElementById("position").value;
		if(position==''){
			scanProcessTip('仓位不能为空',false);
			gotoScanStep('position');
			return;
		}
		scanProcessTip('请输入出库数量',true);
		gotoScanStep('num');
	}
}