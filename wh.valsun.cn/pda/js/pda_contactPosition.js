var scanend = true;
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}

function fuc_onload(){
	document.getElementById("old_position").focus();
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
function check_old_position(){
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('old_position');
		var old_position	= obj.value;
		if(!old_position){
		      scanProcessTip('请扫描旧仓位!', false);
              return false;
		}
        scanProcess(old_position);
	}
}
function scanProcess(old_position){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_contactPosition&act=check_position&jsonp=1';
	var param = 'position='+old_position;
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
				scanProcessTip('请扫描新仓位!',true);
                gotoScanStep('new_position');
                var old_position    =   window.document.getElementById('old_positionId');
                old_position.value  =   data.data;		
			}else{
				gotoScanStep('old_position');
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function check_new_position(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
        var new_position    =   document.getElementById("new_position").value;
        if(!new_position){
            scanProcessTip('请扫描新仓位!',false);
            gotoScanStep('new_position');
            return false;
        }
        var old_positionId  =   window.document.getElementById('old_positionId').value;
        if(!old_positionId){
            scanProcessTip('请扫描旧仓位!',false);
            gotoScanStep('old_position');
            return false;
        }
        
		createXMLHttpRequest();
		var url = './json.php?act=submitPosition&mod=pda_contactPosition&jsonp=1';
		var param = 'old_positionId='+old_positionId+'&new_position='+new_position;
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
			if(data.errCode==200){
				scanProcessTip('关联成功!请扫描旧仓位',true);
				gotoScanStep('old_position');
                window.document.getElementById('new_position').value    =   '';
                window.document.getElementById('old_positionId').value  =   '';
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep("new_position");
			}
		}
	}
}