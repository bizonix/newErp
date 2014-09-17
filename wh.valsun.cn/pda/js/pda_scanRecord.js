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
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}
function insert_scan_data(){
	if(!scanend){
		scanProcessTip('上一个订单号还没有同步成功,请稍等!',false);
		return false;
	}
	var obj = document.getElementById('insert_scan_data');
	var scan_userobj = document.getElementById('scan_user');
	var data_id	= obj.value;
	var scan_user = scan_userobj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid	=/^\d+$/;
	var p_sku 			= /^[A-Z0-9_]+$/;
	var p_eub_trackno	=/^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$/;
	if(	p_realebayid.test(data_id) || p_eub_trackno.test(data_id) || p_sku.test(data_id)){//compablity with EUB
		scanend = false;
		scanProcessTip('开始录入...',true);
		scanProcess(data_id,scan_user);
	}else{
		scanProcessTip('条码格式有误！',false);		
		obj.focus();
		obj.value='';
		gotoScanStep('insert_scan_data', false);
	}
	
}
function scanProcess(data_id,scan_user){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_scanRecord&act=pda_scanRecord';
	var param = 'data_id='+data_id+'&scan_user='+encodeURI(scan_user);
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
			var data=eval('('+res+')');
			if( data.res_code == '200'){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(data.res_msg,true);
				setTimeout(function(){
					document.getElementById('insert_scan_data').focus();
					document.getElementById('insert_scan_data').value = '';
					gotoScanStep('insert_scan_data');
				},1000);
			}else{
				document.getElementById('insert_scan_data').select();
				scanProcessTip(data.res_msg,false);
			}
		}
	}
}
function show_menu_list(){
	var menuname_obj=document.getElementById('menuname');
	var display_value = menuname_obj.style.display;
	if(display_value!=''){
		menuname_obj.style.display = '';
	}else{
		menuname_obj.style.display = 'block';
	}
}
function $(o){
  return document.getElementById(o);
}
function fuc_onload(){
	$("insert_scan_data").focus();	
}

(function(id){
	gotoScanStep(id);
	document.getElementById(id).focus();
})('insert_scan_data');