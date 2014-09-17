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
		scanProcessTip('上一个调拨单还没有同步成功,请稍等!',false);
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
	var url = 'json.php?mod=pda_whEndAssign&act=getGroupInfo&jsonp=1';
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
			//console.log(res.errMsg);return false;
			var data = eval("("+res+")");
			//console.log(data.data);
			if( data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				//var scan_sku = document.getElementById('scan_sku');
//				var sku = document.getElementById('sku');
//				scan_sku.style.display = 'block';
//				sku.focus();
                var group_id = document.getElementById('group_id');
				group_id.value = '';
				//var next_sku = document.getElementById('next_sku');
				//next_sku.style.display = 'block';
				//var next_order_button = document.getElementById('next_order_button');
				//next_order_button.style.display = 'block';
				//var now_group_id = document.getElementById('now_group_id');
//				now_group_id.value = data.data.group_id;
//				var now_sku = document.getElementById('now_sku');
//				now_sku.value = data.data.sku;
				//var now_pname = document.getElementById('now_pname');
				//now_pname.value = data.data.goods_location;
				//var now_sku_num = document.getElementById('now_sku_num');
//				now_sku_num.value = data.data.sku_amount;
//
//				var showdetail = document.getElementById('sku_info');
//				showdetail.innerHTML = '';
//				var newtable = '';
				//newtable += '<br/>';
				//newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
//				newtable += '<tr style="font-size:13px"><td style="padding:2px">SKU</td><td>配货数量</td><td>剩余复核数</td></tr>';
//				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.sku+'</td><td width="80px">'+data.data.sku_amount+'</td><td>'+data.data.need_check+'</td></tr>';				
//				newtable += '</table>';
//				showdetail.innerHTML = newtable;
				return false;			
			}else{
				var obj = document.getElementById('group_id');
				obj.focus();
				obj.value='';
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}


function fuc_onload(){
	$("group_id").focus();	
}

function $(o){
  return document.getElementById(o);
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

function header_page(cid){
	if(cid != undefined){
		switch(cid){
			case 15:
				//配货清单出库
				lhref = 'index.php?mod=pda_checkPdaAssignList&act=pda_scanPickList';
				break;
		}
		window.location.href = lhref;
	}
}