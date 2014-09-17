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

function inputgroup(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var groupid = document.getElementById("groupid").value;
		if(groupid==''){
			scanProcessTip('groupid',false);
			gotoScanStep('groupid');
			return;
		}
		document.getElementById("old_position").style.display = "";
		scanProcessTip('请输入多出数量',true);
		gotoScanStep('num');
	}
}

function inputnum(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var groupid 	= document.getElementById("groupid").value;
		var num 		= document.getElementById("num").value;
		var check_number = /^\d+$/;
		if(groupid==''){
			scanProcessTip('groupid不能为空',false);
			gotoScanStep('groupid');
			return;
		}
		
		if(!check_number.test(num) || num==0){
			scanProcessTip('请输入多出数量!',false);
			gotoScanStep('num');
			return;
		}

		createXMLHttpRequest();
		var url = './json.php?act=pdaAdjust&mod=packageCheck&jsonp=1';
		var param = 'groupid='+groupid+'&num='+num;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = subResponse;
		xmlHttpRequest.send(param);
	}
	
}
function subResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data = eval("("+res+")");
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("groupid").value = '';
				document.getElementById("old_position").style.display = "none";
				document.getElementById("num").value = '';
				gotoScanStep('groupid');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('groupid');
			}
		}
	}
}

function fuc_onload(){
	document.getElementById('groupid').focus();
}
