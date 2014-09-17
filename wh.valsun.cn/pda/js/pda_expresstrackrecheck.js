/*
 * 快递跟踪号复核js
 */ 
function gonextinput(e){
	if(e.keyCode != 13){
		return;
	}
	focusInput('tracknumberinput');
}

/*
 * 显示成功消息
 */
function showErrorMsg(msg){
   document.getElementById('showMsgDiv').innerHTML = '<span style="color:red;">' + msg + '</span>';
}

/*
 * 显示错误消息
 */
function showOkMsg(msg){
    document.getElementById("showMsgDiv").innerHTML = '<span style="color:green;">' + msg + '</span>';
}

/*
 * 是指定id的元素获得焦点
 */
function focusInput(eid){
	document.getElementById(eid).select();
}


/*
 * 配货扫描提交
 */
function scansubmit(e){
	if(e.keyCode != 13){
		return;
	}
	var orderid = document.getElementById('orderidinput').value;		//单号
	//orderid = $.trim(orderid);
	if(orderid.length == 0){
		showErrorMsg('请输入单号');
		focusInput("orderidinput");
		return;
	}

	var num = document.getElementById('tracknumberinput').value;		//跟踪号
	//num = $.trim(num);
	if(num.length == 0){
		showErrorMsg('跟踪号不能为空');
		return;
	}
	showOkMsg('正在提交...');
	createXMLHttpRequest();
	var url = 'json.php?mod=expressTrackRecheck&act=handelSubmit&jsonp=1&orderid='+orderid+'&expressid='+num;
	var param = 'orderid='+orderid+'&expressid='+num;
	xmlHttpRequest.open("GET",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scansubmitResponse;
	xmlHttpRequest.send(param);
}
function scansubmitResponse(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var res=eval('('+res+')');
				if (res.errCode == 0) { //出错
					showErrorMsg(res.errMsg);
				} else if(res.errCode == 1){	//最后一个料号配货完成
					emptyInput('orderidinput');
					emptyInput('tracknumberinput');
					focusInput('orderidinput');
					showOkMsg('ok');
				}
		}
	}	
}
/*
 * 将input置空
 * id号
 */
function emptyInput(id){
	document.getElementById(id).value = "";
}

/*
 * 配货扫描提交多单号
 */
function submitmul(e){
	var orderid = document.getElementById('orderidinput').value;		//单号
	
	if(orderid.length == 0){
		showErrorMsg('请输入单号');
		focusInput("orderidinput");
		return;
	}

	var num = document.getElementById('tracknumberinputs').value;		//跟踪号
	if(num.length == 0){
		showErrorMsg('跟踪号不能为空');
		return;
	}
	showOkMsg('正在提交...');
	createXMLHttpRequest();
	var url = 'json.php?mod=expressTrackRecheck&act=handelSubmit&jsonp=1';
	var param = 'orderid='+orderid+'&expressid='+num;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = submitmulResponse;
	xmlHttpRequest.send(param);
}
function submitmulResponse(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var res = eval('('+res+')');	
			if (res.errCode == 0) { //出错
				showErrorMsg(res.errMsg);
				focusInput('tracknumberinput');
			} else if(res.errCode == 1){	//最后一个料号配货完成
				emptyInput('orderidinput');
				emptyInput('tracknumberinput');
				focusInput('orderidinput');
				showOkMsg('OK');
			}
		}
	}	

}
function multrack(){
	document.getElementById('multrack').style.display = "block";
	document.getElementById('onetrack').style.display = "none";
}
function onetrack(){
	document.getElementById('multrack').style.display = "none";
	document.getElementById('onetrack').style.display = "block";
}
function fuc_onload(){
	document.getElementById('orderidinput').focus();
}