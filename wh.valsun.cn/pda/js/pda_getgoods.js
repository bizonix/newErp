/*
 * 配货单页面js
 */

/*
 * 获得配货单/发货单sku列表
 */
function loaded(){
	focusInput('orderidinput');
}
function getSkuList(e){
    if (e.keyCode != 13) { //没按下了enter键盘
        return;
    }
    var orderid = document.getElementById("orderidinput").value;

    if (orderid == '') {
        showErrorMsg('订单号不能为空');
        return;
    }
    showOkMsg('正在拉取sku信息...');
	createXMLHttpRequest();
	var url = 'json.php?mod=getGoods&act=getSkuListEX&jsonp=1';
	var param = 'orderid='+orderid;
//	window.open(url);
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = getSkuListResponse;
	xmlHttpRequest.send(param);
	
}

var currentSku = {};

function getSkuListResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			res = eval("("+res+")");
			if (res.errCode == '0') { //出错
				showErrorMsg(res.errMsg);
				emptyInput('orderidinput');
				focusInput('orderidinput');
			} else {
				showOkMsg('拉取成功!');
				document.getElementById('detail').style.display = "block";
				currentSku	= {};											//重置列表
				focusInput('scanskuinput');
				rebuildskulist(res.data);
				
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
		
		/***验证发货单号是否存在料号、料号是否配过货 start ***/
		var orderid = document.getElementById("orderidinput").value;
		var pname   = document.getElementById('skulistselect').value;//仓位中文名称
		createXMLHttpRequest();
		var url = 'json.php?mod=getGoods&act=checkOrderSku&jsonp=1';
		var param = 'orderid='+orderid+'&sku='+sku+'&pname='+pname;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = getCheckSku;
		xmlHttpRequest.send(param);
		/***验证发货单号是否存在料号、料号是否配过货 end ***/
		
		if(currentSku[sku] == '1'){									//如果是1 则不用输入 模拟回车事件
			var target	= document.getElementById('skunumberinput');
			target.value = 1;
			submitSku();
		}
	}
}
/***服务器返回料号验证结果 Start ***/
function getCheckSku(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			res = eval("("+res+")");
			if(res.errCode == '0') { 
				showOkMsg(res.errMsg);
				focusInput('skunumberinput');
			}else{
				showErrorMsg(res.errMsg);
			}
		}
	}	
}
/***服务器返回验证结果 Start ***/
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
	var lent	= list.length;
	for(var i in list){
		currentSku[list[i]['sku']]	= list[i]['amount'];
        var opt = document.createElement('option');
        opt.setAttribute('value', list[i]['pName']);
        opt.innerHTML = '[' + list[i]['pName'] + ']' + list[i]['sku'] + '*' + list[i]['amount'] + '@' + list[i]['stockqty'];//带出该仓位库存数量 add by wangminwei 2014-03-07
        seobj.appendChild(opt);
    }
	/***添加发货单只有一个料号,直接填充料号与配货数量 Start add by wangminwei 2014-03-07***
	if(list.length == 1){
		document.getElementById('scanskuinput').value 		= list[0]['sku'];
		document.getElementById('skunumberinput').value 	= list[0]['amount'];
		focusInput('skunumberinput');
	}
	/***添加发货单只有一个料号,直接填充料号与配货数量 End***/
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
	submitSku();
}

function submitSku(){
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
	var pname = document.getElementById('skulistselect').value;
	showOkMsg('在配货...');
	createXMLHttpRequest();
	var url = 'json.php?mod=getGoods&act=scanSubmitEx&jsonp=1';
	var param = 'orderid='+orderid+'&sku='+sku+'&num='+num+'&pname='+pname;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scansubmitResponse;
	xmlHttpRequest.send(param);
}


function scansubmitResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			res = eval("("+res+")");
			if (res.errCode == 0) { 					//出错
				showErrorMsg(res.errMsg);
			} else if(res.errCode == 2) {				//料号全部配货完成
				showOkMsg(res.errMsg);
				emptyInput('orderidinput');
				document.getElementById("skulistselect").innerHTML = "";
				emptyInput('scanskuinput');
				emptyInput('skunumberinput');
				focusInput('orderidinput');
			} else if(res.errCode == 1){				//最后一个料号配货完成
				
				rebuildskulist(res.data);
				emptyInput('scanskuinput');
				emptyInput('skunumberinput');
				focusInput('scanskuinput');
				showOkMsg(res.errMsg);
			} else if(res.errCode == 20){
				showErrorMsg(res.errMsg);
				emptyInput('skunumberinput');
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