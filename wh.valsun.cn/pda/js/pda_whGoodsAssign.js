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
		scanProcessTip('上一个料号还没有同步成功,请稍等!',false);
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
	var url = 'json.php?mod=scanPdaPickList&act=getGroupInfo&jsonp=1';
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
				var scan_sku = document.getElementById('scan_sku');
				var sku = document.getElementById('sku');
				scan_sku.style.display = 'block';
				sku.focus();
				var next_sku = document.getElementById('next_sku');
				next_sku.style.display = 'block';
				var next_order_button = document.getElementById('next_order_button');
				next_order_button.style.display = 'block';
				var now_group_id = document.getElementById('now_group_id');
				now_group_id.value = data.data.group_id;
				var now_sku = document.getElementById('now_sku');
				now_sku.value = data.data.sku;
				var now_pname = document.getElementById('now_pname');
				now_pname.value = data.data.goods_location;
				var now_sku_num = document.getElementById('now_sku_num');
				now_sku_num.value = data.data.sku_amount;

				var showdetail = document.getElementById('sku_info');
				showdetail.innerHTML = '';
				var newtable = '';
				//newtable += '<br/>';
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">仓位</td><td>sku</td><td>需配数量</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.goods_location+'</td><td width="80px">'+data.data.sku+'</td><td>'+data.data.sku_amount+'</td></tr>';				
				newtable += '</table>';
				showdetail.innerHTML = newtable;

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

function checkSku(){
	if(!scanend){
		scanProcessTip('上一个sku还没有同步成功,请稍等!',false);
		return false;
	}
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var sku_obj = document.getElementById('sku');
		var group_obj = document.getElementById('group_id');
		var now_sku = document.getElementById('now_sku').value;
		var order_group	= group_obj.value;
		var sku	= sku_obj.value;
		/*if(sku!=now_sku){
			//sku_obj.value = '';
			sku_obj.value = sku;
			sku_obj.select();
			scanProcessTip('所扫描料号与当前料号不符,'+sku+","+now_sku,false);
			return false;
		}*/
		//alert(sku);return false;
		scanend = false
		scanProcess1(order_group,sku,now_sku);
	}
}
function scanProcess1(order_group,sku,now_sku){
	createXMLHttpRequest();  
	var url = 'json.php?mod=scanPdaPickList&act=checkSku&jsonp=1';
	var param = 'order_group='+order_group+'&sku='+sku+'&now_sku='+now_sku;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse1; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse1(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			//alert(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				var now_sku_num = document.getElementById('now_sku_num').value;
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var newtable = '';
				//newtable += '<br/>';
				/*
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr><td>SKU</td><td>清单需配数量</td><td>实际配货数量</td></tr>';
				newtable += '<tr><td>'+data.res_info['sku']+'</td><td>'+data.res_info['sku_amount']+'</td><td style="padding:2px"><input type="text" id="sku_num" onkeydown="checkSkuNum()" style="width:60px;" value="'+data.res_info['sku_amount']+'"></td></tr>';
				newtable += '<tr><td colspan="3" style="word-break:break-all">筐号(数量):'+data.res_info['car_number']+'</td></tr>';
				newtable += '</table>';
				*/
				var obj = document.getElementById('sku');
				obj.value=data.data;
				if(now_sku_num==1){
					newtable += '<span style="font-size:15px">实际配货数量:</span><input type="text" class="textinput" name="sku_num" id="sku_num" onkeydown="checkSkuNum()" value="'+now_sku_num+'" />';
					showdetail.innerHTML = newtable;
					checkSkuNum2();
				}else{
					newtable += '<span style="font-size:15px">实际配货数量:</span><input type="text" class="textinput" name="sku_num" id="sku_num" onkeydown="checkSkuNum()" value="" />';
					showdetail.innerHTML = newtable;
					var sku_num = document.getElementById('sku_num');
					sku_num.focus();
					
				}
			}else{
				var obj = document.getElementById('sku');
				obj.value='';
				obj.select();
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function checkSkuNum(){
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var sku_obj = document.getElementById('sku');
		var group_obj = document.getElementById('group_id');
		var order_group	= group_obj.value;
		var sku	= sku_obj.value;
		var sku_num_obj	= document.getElementById('sku_num');
		var sku_num = sku_num_obj.value;
		var now_group_id = document.getElementById('now_group_id').value;
		var now_pname = document.getElementById('now_pname').value;
		//alert(sku_num);return false;
		if(sku_num==''){
			scanProcessTip('配货数量不能为空，请确认!',false);
			return false;
		}
		sku_num_obj.blur();
		//scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess2(order_group,sku,sku_num,now_group_id,now_pname);
	}
}

function checkSkuNum2(){
	var sku_obj = document.getElementById('sku');
	var group_obj = document.getElementById('group_id');
	var order_group	= group_obj.value;
	var sku	= sku_obj.value;
	var now_group_id = document.getElementById('now_group_id').value;
	var now_pname = document.getElementById('now_pname').value;
	var sku_num = 1;
	//scanProcessTip('数据正在提交，请稍等!',true);
	scanProcess2(order_group,sku,sku_num,now_group_id,now_pname);
	
}

function scanProcess2(order_group,sku,sku_num,now_group_id,now_pname){
	createXMLHttpRequest();  
	var url = 'json.php?mod=scanPdaPickList&act=checkSkuNum&jsonp=1';
	var param = 'order_group='+order_group+'&sku='+sku+'&sku_num='+sku_num+'&now_group_id='+now_group_id+'&now_pname='+now_pname;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse2; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse2(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			//console.log(data.data);
			if( data.errCode == 0){
				var submit_orders = document.getElementById('submit_orders');
				submit_orders.value = data.data.submit_orders;
				var submit_nums = document.getElementById('submit_nums');
				submit_nums.value = data.data.submit_nums;
				showWindow(data.data.res_car_info);				
			}else{
				scanProcessTip(data.errMsg,false);
				var sku_num = document.getElementById('sku_num');
				sku_num.focus();
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
				lhref = 'index.php?mod=pda_ScanPdaPickList&act=pda_scanPickList';
				break;
		}
		window.location.href = lhref;
	}
}

function next_sku(){
	var now_group_id = document.getElementById('now_group_id').value;
	var now_sku   = document.getElementById('now_sku').value;
	var now_pname = document.getElementById('now_pname').value;
	var group_obj = document.getElementById('group_id');
	var order_group	= group_obj.value;
	scanProcessTip('数据正在提交，请稍等!',true);
	scanProcess3(order_group,now_group_id,now_sku,now_pname);	
}

function scanProcess3(order_group,now_group_id,now_sku,now_pname){
	createXMLHttpRequest();  
	var url = 'json.php?mod=scanPdaPickList&act=nextSku&jsonp=1';
	var param = 'order_group='+order_group+'&now_group_id='+now_group_id+'&now_sku='+now_sku+'&now_pname='+now_pname;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse3; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse3(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				var showdetail = document.getElementById('sku_info');
				showdetail.innerHTML = '';
				var newtable = '';
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">仓位</td><td>sku</td><td>需配数量</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.goods_location+'</td><td width="80px">'+data.data.sku+'</td><td>'+data.data.sku_amount+'</td></tr>';				
				newtable += '</table>';
				showdetail.innerHTML = newtable;
				
				var now_group_id = document.getElementById('now_group_id');
				now_group_id.value = data.data.group_id;
				var now_sku = document.getElementById('now_sku');
				now_sku.value = data.data.sku;
				var now_pname = document.getElementById('now_pname');
				now_pname.value = data.data.goods_location;
				var now_sku_num = document.getElementById('now_sku_num');
				now_sku_num.value = data.data.sku_amount;
				var sku_obj = document.getElementById('sku');
				sku_obj.value='';
				sku_obj.focus();
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				scanProcessTip(data.errMsg,true);
			}else{
				scanProcessTip(data.errMsg,false);
			}
		}
	}
}

function showWindow(data){
  var sku_num_obj = document.getElementById('sku_num');
  sku_num_obj.blur();
  var len = data.length;
  //var len = 20;
  //alert(data);
  //return false;
  if(document.getElementById("divWin"))
  {
   $("divWin").style.zIndex=999;
   $("divWin").style.display="";
  }
  else
  {
   var objWin=document.createElement("div");
   objWin.id="divWin";
   objWin.style.position="absolute";
   objWin.style.top  = "5px";   
   objWin.style.left = "5px";   
   objWin.style.width="220px";
   objWin.style.height="250px";
   objWin.style.border="2px solid #AEBBCA";
   objWin.style.background="#FFF";
   objWin.style.zIndex=999;
   document.body.appendChild(objWin);
  }
  
  if(document.getElementById("win_bg"))
  {
   $("win_bg").style.zIndex=998;
   $("win_bg").style.display="";
  }
  else
  {
   var obj_bg=document.createElement("div");
   obj_bg.id="win_bg";
   obj_bg.className="win_bg";
   document.body.appendChild(obj_bg);
  }

  var str="";
  str+='<div class="winTitle" ><span class="title_left">筐号及数量</span><span class="title_right"><input type="text" style="width:6px;" value="" id="closeinput" onkeydown="submitInfo()"><a href="javascript:closeWindow()" title="单击返回">返回</a></span><br style="clear:right"/></div>';  //标题栏
  str+='<div class="winContent">';
  for(var i=0;i<len;i++){
	//str+='<div class="winContent"><div id="show_search_info"></div></div>';  //窗口内容
	str+='<div style="width:50px;height:40px;background-color:#98F898;float:left;margin:1px;text-align:center;font-size:15px;"><span align="center">号:'+data[i]['car']+'</span><br/><span align="center">('+data[i]['num']+')</span></div>';
	//str+='<div style="width:50px;height:40px;background-color:#98F898;float:left;margin:1px;text-align:center;font-size:13px;"><span align="center">筐:12</span><br/><span align="center">(12)</span></div>';
  }
  str+='</div>';
  $("divWin").innerHTML=str;
  try{$("closeinput").focus()}catch(err){ alert(err.description);}
}
function closeWindow(){
var obj = document.getElementById("divWin");
  document.body.removeChild(obj);
  var obj1 = document.getElementById("win_bg");
  document.body.removeChild(obj1);
 // $("divWin").style.display="none";
 // $("win_bg").style.display="none";
  var obj = document.getElementById('sku_num');
  obj.focus();
}

function closeWindow2(){
  var obj = document.getElementById("divWin");
  document.body.removeChild(obj);
  var obj1 = document.getElementById("win_bg");
  document.body.removeChild(obj1);
  //$("divWin").style.display="none";
 // $("win_bg").style.display="none";
  //var obj = document.getElementById('sku');
  //obj.focus();
}
function closeWindow3(){
  var obj = document.getElementById("divWin");
  document.body.removeChild(obj);
  var obj1 = document.getElementById("win_bg");
  document.body.removeChild(obj1);
  //$("divWin").style.display="none";
  //$("win_bg").style.display="none";
  var obj = document.getElementById('group_id');
  obj.focus();
}

function submitInfo(){
	var keyCode = event.keyCode;
	if (keyCode == 13) {
		var closeinput_obj = document.getElementById('closeinput');
		closeinput_obj.blur();
		var now_group_id = document.getElementById('now_group_id').value;
		var sku_obj = document.getElementById('sku');
		var group_obj = document.getElementById('group_id');
		var order_group	= group_obj.value;
		var sku	= sku_obj.value;
		var now_sku = document.getElementById('now_sku').value;
		var now_pname = document.getElementById('now_pname').value;
		var submit_orders = document.getElementById('submit_orders').value;
		var submit_nums = document.getElementById('submit_nums').value;
		//scanProcessTip('数据正在提交，请稍等!',true);
		scanProcess4(order_group,sku,now_sku,submit_orders,submit_nums,now_group_id,now_pname);
	}
}

function scanProcess4(order_group,sku,now_sku,submit_orders,submit_nums,now_group_id,now_pname){
	createXMLHttpRequest();  
	var url = 'json.php?mod=scanPdaPickList&act=submitInfo&jsonp=1';
	var param = 'order_group='+order_group+'&sku='+sku+'&now_sku='+now_sku+'&submit_orders='+submit_orders+'&submit_nums='+submit_nums+'&now_group_id='+now_group_id+'&now_pname='+now_pname;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = scanProcessResponse4; 
	xmlHttpRequest.send(param); 
}
//处理返回信息函数 
function scanProcessResponse4(){
	scanend = true;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//alert(res);return false;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				
				var obj = document.getElementById('sku');
				obj.focus();
				obj.value='';
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				
				var showdetail = document.getElementById('sku_info');
				showdetail.innerHTML = '';
				var newtable = '';
				newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="1" width="100%">';
				newtable += '<tr style="font-size:13px"><td style="padding:2px">仓位</td><td>sku</td><td>需配数量</td></tr>';
				newtable += '<tr style="font-size:13px"><td width="60px" style="padding:2px">'+data.data.goods_location+'</td><td width="80px">'+data.data.sku+'</td><td>'+data.data.sku_amount+'</td></tr>';				
				newtable += '</table>';
				showdetail.innerHTML = newtable;
				var now_group_id = document.getElementById('now_group_id');
				now_group_id.value = data.data.group_id;
				var now_sku = document.getElementById('now_sku');
				now_sku.value = data.data.sku;
				var now_pname = document.getElementById('now_pname');
				now_pname.value = data.data.goods_location;
				var now_sku_num = document.getElementById('now_sku_num');
				now_sku_num.value = data.data.sku_amount;
				
				//showWindow(data.res_car_info);
				closeWindow2();			
			}else if( data.errCode == 1){
				var group_obj = document.getElementById('group_id');
				group_obj.focus();
				group_obj.value='';
				var next_order_button = document.getElementById('next_order_button');
				next_order_button.style.display = 'none';
				var scan_sku = document.getElementById('scan_sku');
				scan_sku.style.display = 'none';
				var sku_obj = document.getElementById('sku');
				sku_obj.value='';
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var sku_info = document.getElementById('sku_info');
				sku_info.innerHTML = '';
				var next_sku = document.getElementById('next_sku');
				next_sku.style.display = 'none';
				scanProcessTip(data.errMsg,true);
				closeWindow3();
			}else{
				scanProcessTip(data.errMsg,false);
				var group_obj = document.getElementById('group_id');
				group_obj.focus();
				group_obj.value='';
				var sku_obj = document.getElementById('sku');
				sku_obj.value='';
				var next_order_button = document.getElementById('next_order_button');
				next_order_button.style.display = 'none';
				var scan_sku = document.getElementById('scan_sku');
				scan_sku.style.display = 'none';
				var showdetail = document.getElementById('operationcontents');
				showdetail.innerHTML = '';
				var sku_info = document.getElementById('sku_info');
				sku_info.innerHTML = '';
				var next_sku = document.getElementById('next_sku');
				next_sku.style.display = 'none';
				closeWindow3();
			}
		}
	}
}