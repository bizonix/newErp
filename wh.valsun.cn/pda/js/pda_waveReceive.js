var scanend = true;
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
function checkWave(){
	if(!scanend){
		scanProcessTip('上一个配货单还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('wave');
		var wave	= obj.value;
		//alert(order_group);return false;
		scanend = false;
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess(wave);
	}
}
function scanProcess(wave){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_waveReceive&act=checkWave&jsonp=1';
	var param = 'wave='+wave;
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
			var data = eval("("+res+")");
			//console.log(data.data);
            gotoScanStep('wave');
			if( data.errCode == 0){
				scanProcessTip(data.errMsg,true);
                document.getElementById('show_info').innerHTML  =   data.data;
				return false;		
			}else{
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function fuc_onload(){
	document.getElementById("wave").focus();
}