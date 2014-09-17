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

function inputsku(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku = document.getElementById("nowsku").value;
		if(sku==''){
			scanProcessTip('sku不能为空',false);
			gotoScanStep('nowsku');
			return;
		}
		document.getElementById("old_position").style.display = "";
		scanProcessTip('请扫描要移动料号仓位号',true);
		gotoScanStep('oldposition');
	}
}

function oldposition(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var oldposition = document.getElementById("oldposition").value;
		if(oldposition==''){
			scanProcessTip('旧仓位号不能为空',false);
			gotoScanStep('oldposition');
			return;
		}
		document.getElementById("shownews").style.display = "";
		scanProcessTip('请扫描新仓位号',true);
		gotoScanStep('newposition');
	}
}

function newposition(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var newposition = document.getElementById("newposition").value;
		if(newposition==''){
			scanProcessTip('新仓位号不能为空',false);
			gotoScanStep('newposition');
			return;
		}
		document.getElementById("shownews").style.display = "";
		scanProcessTip('请输入数量，不输入表示移动全部',true);
		gotoScanStep('nums');
	}
}

function subdata(){
	var sku 	 	= document.getElementById("nowsku").value;
	var oldposition = document.getElementById("oldposition").value;
	var newposition = document.getElementById("newposition").value;
	var nums 		= document.getElementById("nums").value;
	var check_number = /^\d+$/;
	if(sku==''){
		scanProcessTip('sku不能为空',false);
		gotoScanStep('nowsku');
		return;
	}
	if(oldposition==''){
		scanProcessTip('旧仓位不能为空',false);
		gotoScanStep('oldposition');
		return;
	}
	if(newposition==''){
		scanProcessTip('新仓位不能为空',false);
		gotoScanStep('newposition');
		return;
	}
	if(oldposition==newposition){
		scanProcessTip('新旧仓位不能相同',false);
		gotoScanStep('newposition');
		return;
	}
	if(nums!=''){
		if(!check_number.test(nums) || nums==0){
			scanProcessTip('移动数量必须为整数或者不填全移，请确认!',false);
			gotoScanStep('nums');
			return;
		}
	}	
	createXMLHttpRequest();
	var url = './json.php?act=shiftLibrary&mod=shiftLibrary&jsonp=1';
	var param = 'sku='+sku+'&oldposition='+oldposition+'&newposition='+newposition+'&nums='+nums;
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
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("nowsku").value = '';
				document.getElementById("old_position").style.display = "none";
				document.getElementById("oldposition").value = '';
				document.getElementById("shownews").style.display = "none";
				document.getElementById("nums").value = '';
				document.getElementById("newposition").value = '';
				gotoScanStep('nowsku');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('nowsku');
			}
		}
	}
}

function fuc_onload(){
	document.getElementById('nowsku').focus();
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
  $("nowsku").focus();
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