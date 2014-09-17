function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
    document.getElementById('data_submit').disabled = false;
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

function checksku(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku = document.getElementById("sku").value;
		if(sku==''){
			scanProcessTip('sku不能为空',false);
			gotoScanStep('sku');
			return;
		}
		createXMLHttpRequest();
		var url = './json.php?act=checkSku&mod=inventory&jsonp=1';
		var param = 'sku='+sku;
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
			//console.log(data.errCode);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("nowlocation").style.display = "";
				document.getElementById("sku").value = data.data;
				gotoScanStep('location');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('sku');
			}
		}
	}
}

function checklocation(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku = document.getElementById("sku").value;
		var location = document.getElementById("location").value;
		if(location==''){
			scanProcessTip('仓位不能为空',false);
			gotoScanStep('location');
			return;
		}
		if(sku==''){
			scanProcessTip('sku不能为空',false);
			gotoScanStep('sku');
			return;
		}
		createXMLHttpRequest();
		var url = './json.php?act=checkSkuPositon&mod=inventory&jsonp=1';
		var param = 'sku='+sku+'&location='+location;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = checkPositionResponse;
		xmlHttpRequest.send(param);
	}
}
function checkPositionResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data = eval("("+res+")");
			//console.log(data.data);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("tbody").style.display = "";
				if(data.data==''){
				    scanProcessTip('获取旧ERP库存失败！',false);
					document.getElementById("nums").value = 0;
				}else{
					document.getElementById("nums").value = data.data;
				}				
				gotoScanStep('invNums');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('location');
			}
		}
	}
}

function subdata(){
    document.getElementById('data_submit').disabled = true;    
	var sku 	 = document.getElementById("sku").value;
	var location = document.getElementById("location").value;
	var invNums  = document.getElementById("invNums").value;
	var reasonId = document.getElementById("reasonId").value;
	var check_number = /^\d+$/;
	if(sku==''){
		scanProcessTip('sku不能为空',false);
		gotoScanStep('sku');
		return;
	}
	if(location==''){
		scanProcessTip('仓位不能为空',false);
		gotoScanStep('location');
		return;
	}
	if(!check_number.test(invNums)){
		scanProcessTip('盘点数量有误，请确认!',false);
		gotoScanStep('invNums');
		return;
	}
	if(reasonId==0){
		scanProcessTip('请选择盘点原因',false);
		document.getElementById('reasonId').focus();
        document.getElementById('data_submit').disabled = false;
		return;
	}
	
	createXMLHttpRequest();
	var url = './json.php?act=sumbInv&mod=inventory&jsonp=1';
	var param = 'sku='+sku+'&location='+location+'&invNums='+invNums+'&reasonId='+reasonId;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = subResponse;
	xmlHttpRequest.send(param);
}
function subResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data = eval("("+res+")");
			//console.log(data);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("sku").value = '';
				document.getElementById("nowlocation").style.display = "none";
				document.getElementById("location").value = '';
				document.getElementById("tbody").style.display = "none";
				document.getElementById("nums").value = '';
				document.getElementById("invNums").value = '';
				document.getElementById("reasonId").value = '0';
				gotoScanStep('sku');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('sku');
			}
		}
	}
}

function fuc_onload(){
	document.getElementById('sku').focus();
}

function inquire_sku(sku){
	sku = sku.toUpperCase();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(sku != ''){
		scanProcess3(sku);
	}else{
		//scanProcessTip('请扫描料号!',false);
		$('inquire_sku_value').value = "";
		gotoScanStep('inquire_sku_value', false);
	}	
}

function showWindow(){
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
   objWin.style.top = "50px";   
   objWin.style.left = "10px";   
   objWin.style.width="200px";
   objWin.style.height="180px";
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
  str+='<div class="winTitle" onMouseDown="startMove(this,event)" onMouseUp="stopMove(this,event)"><span class="title_left">查询</span><span class="title_right"><a href="javascript:closeWindow()" title="单击关闭">关闭</a></span><br style="clear:right"/></div>';  //标题栏
  str+='<div class="winContent">SKU: <input name="inquire_sku_value" type="text" id="inquire_sku_value" onkeydown="inquire_sku(this.value);" style="width:120px;" class="textinput" /><div id="show_search_info"></div></div>';  //窗口内容
  $("divWin").innerHTML=str;
  $("inquire_sku_value").focus();
}
function closeWindow(){
  $("divWin").style.display="none";
  $("win_bg").style.display="none";
  $("sku").focus();
}
function $(o){
  return document.getElementById(o);
}

function startMove(o,e){
  var wb;
  if(document.all && e.button==1) wb=true;
  else if(e.button==0) wb=true;
  if(wb)
  {
    var x_pos=parseInt(e.clientX-o.parentNode.offsetLeft);
    var y_pos=parseInt(e.clientY-o.parentNode.offsetTop);
    if(y_pos<=o.offsetHeight)
    {
      document.documentElement.onmousemove=function(mEvent)
      {
        var eEvent=(document.all)?event:mEvent;
        o.parentNode.style.left=eEvent.clientX-x_pos+"px";
        o.parentNode.style.top=eEvent.clientY-y_pos+"px";
      }
    }
  }
}
function stopMove(o,e){
  document.documentElement.onmousemove=null;
}

function scanProcess3(sku){
	createXMLHttpRequest();
	var url = 'json.php?mod=shiftLibrary&act=searchSku&jsonp=1';
	var param = 'sku='+sku;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse3;
	xmlHttpRequest.send(param);
}

function scanProcessResponse3(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){
			var res = xmlHttpRequest.responseText;
			//console.log(res);return false;
			var data=eval('('+res+')');
			if( data.errCode == '400'){
				scanProcessTip(data.errMsg,true);
				show_search_sku(data.data);
				//$('inquire_sku_value').value = data.sku;
			}else{
				scanProcessTip(data.errMsg,false);
				$('inquire_sku_value').value = "";
				gotoScanStep('inquire_sku_value', false);
			}
		}
	}
}

function show_search_sku(datas){
	var showallsku = $('show_search_info');
	show_search_info.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="show_sku_fieldset" style=" margin-top:5px; padding:5px 5px;"><legend>库存信息</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	var len = datas.length;
	newtable += '<tr style="font-size:12px;"><td>仓位号</td><td>现有库存</td></tr>';
	for(var i=0; i<len; i++){
		newtable += '<tr style="font-size:12px;"><td>'+datas[i].pName+'</td><td>'+datas[i].nums+'</td></tr>';
	}
	newtable += '</table>';
	newtable += '</fieldset>';
	show_search_info.innerHTML = newtable;
	if(newtable != '') return true;
}