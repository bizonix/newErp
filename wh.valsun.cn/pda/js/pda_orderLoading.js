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
function fuc_onload(){
	$("group_id").focus();	
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
function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}
function checkGroupID(){
	if(!scanend){
		scanProcessTip('上一个编号还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var obj = document.getElementById('group_id');
		var order_group	= obj.value;
		//alert(order_group);return false;
		scanend = false;
		scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess(order_group);
	}
}
function scanProcess(order_group){
	createXMLHttpRequest();  
	var url = 'json.php?mod=pda_orderLoading&act=orderLoading&jsonp=1';
	var param = 'order_group='+order_group;
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
           // alert(res);
			//console.log(res.errMsg);return false;
			var res = eval("("+res+")");
			//console.log(data.data);
			if( res.errCode == 200){
				scanProcessTip(res.errMsg,true);
				var obj = document.getElementById('group_id');
				obj.focus();
				obj.value='';
            }else if(res.errCode == 400){
                scanProcessTip(res.errMsg,true);
                document.getElementById('order_show').style.display = 'block';
                document.getElementById('group_id').value = res.data.shipOrderId;
                gotoScanStep('ebay_id');
			}else{
				scanProcessTip(res.errMsg,false);
			}
		}
	}
}


function checkOrder(){
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
    var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var shipOrderId    = document.getElementById('group_id').value; //快递单号
	var p_realebayid = /^\d+$/;
	var p_eub_trackno= /^(LK|RA|RI|RL|RB|RM|RC|RD|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
	var p_ups_trackno= /^(1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess1(ebay_id, shipOrderId);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('ebay_id');
	}
}

function scanProcess1(ebay_id, shipOrderId){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_orderLoading&act=orderExpress&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&action=check_ebay_id&shipOrderId='+shipOrderId;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse1;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function scanProcessResponse1(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
         //   alert(res);return;
			var res=eval('('+res+')');
			if( res.errCode == '200' ){//随机复核一个订单
				scanProcessTip(res.errMsg,true);
                //document.getElementById('show_review_total').style.display =   'block';
                document.getElementById('ebay_id').value = '';
				gotoScanStep('ebay_id');
			}else{
				scanProcessTip(res.errMsg,false);
				gotoScanStep('ebay_id');
			}
		}
	}
}

