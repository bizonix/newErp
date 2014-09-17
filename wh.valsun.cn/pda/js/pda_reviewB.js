/*
 * 配货单页面js
 */

/*
 * 获得配货单/发货单sku列表
 */
function getSkuList(e){
    if (e.keyCode != 13) { //没按下了enter键盘
        return;
    }
    var orderid = document.getElementById('orderidinput').value;
    //orderid = $.trim(orderid);
    if (orderid == '') {
        showErrorMsg('订单号不能为空');
        return;
    }
    
    showOkMsg('正在拉取sku信息...');
	createXMLHttpRequest();
	var url = 'json.php?mod=reviewB&act=getSkuList&jsonp=1';
	var param = 'orderid=' + orderid;
	
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = getSkuListResponse;
	xmlHttpRequest.send(param);

}
function getSkuListResponse(){
	if(xmlHttpRequest.readyState == 4){
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			var res=eval('('+res+')');
			if (res.errCode == 0) { //出错
				showErrorMsg(res.errMsg);
				emptyInput('orderidinput');
				focusInput('orderidinput');
			} else {
				showOkMsg('拉取成功!');
				document.getElementById("detail").style.display = "block";
				rebuildskulist(res.data);
				focusInput('scanskuinput');
			}
		}
	}	
}

function responseskuscan(e){
	if(e.keyCode != 13){
		return;
	}
	var sku = document.getElementById('scanskuinput').value;
	
	if(sku.length == 0){
		showErrorMsg('请输入料号信息！');
		return;
	} else {
		focusInput('skunumberinput');
	}
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
	document.getElementById('showMsgDiv').innerHTML = '<span style="color:green;">' + msg + '</span>';
}

function rebuildskulist(list){
    var seobj = document.getElementById('skulistselect');
	seobj.length = 0;
	for(i in list){
        var opt = document.createElement('option');
        opt.setAttribute('value', list[i]['sku']);
        opt.innerHTML = '料号--'+list[i]['sku'] + ' 数量:--[' + list[i]['totalNums'] + ']';//alert(opt);
        seobj.appendChild(opt);
    }
}

/*
 * 是指定id的元素获得焦点
 */
function focusInput(eid){
	document.getElementById(eid).focus();
}


/*
 * 配货扫描提交
 */
function scansubmit(e){
	if(e.keyCode != 13){
		return;
	}
	var orderid = document.getElementById('orderidinput').value;		//单号
	if(orderid.length == 0){
		showErrorMsg('请输入单号');
		focusInput("orderidinput");
		return;
	}
	var sku = document.getElementById('scanskuinput').value;			//sku
	if(sku.length == 0){
		showErrorMsg('请输入sku');
		focusInput('scanskuinput');
		return;
	}
	var num = document.getElementById('skunumberinput').value;		//数量
	num = parseInt(num);
	if(num == 0){
		showErrorMsg('数量不能为0');
		return;
	}
	showOkMsg('正在复核...');
	createXMLHttpRequest();
	var url = 'json.php?mod=reviewB&act=recheckInfoSubmit&jsonp=1';
	var param = 'orderid='+orderid+'&sku='+sku+'&num='+num;
	xmlHttpRequest.open("POST",url,true);
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
			} else if(res.errCode == 1){	//复核成功
				rebuildskulist(res.data);
				emptyInput('scanskuinput');
				emptyInput('skunumberinput');
				focusInput('scanskuinput');
				showOkMsg(res.errMsg);
			} else if(res.errCode == 2){	//料号复核完成
				emptyInput('orderidinput');
				emptyInput('scanskuinput');
				emptyInput('skunumberinput');
				focusInput('orderidinput');
				showOkMsg(res.errMsg);
				var seobj = document.getElementById('skulistselect');
				seobj.length = 0;
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

function fuc_onload(){
	document.getElementById('orderidinput').focus();
}
