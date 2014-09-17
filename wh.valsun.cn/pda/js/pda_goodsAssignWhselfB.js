var scanend = true;
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
	var url = 'json.php?mod=pda_goodsAssignWhselfB&act=getGroupInfo&jsonp=1';
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
				var sku = document.getElementById('sku');
				//scan_sku.style.display = 'block';
				sku.focus();
                var now_group_id = document.getElementById('now_group_id');
				now_group_id.value = data.data.group_id;
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

function checksku(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku = document.getElementById("sku").value;
        var now_group_id = document.getElementById("now_group_id").value;
		createXMLHttpRequest();
		var url = './json.php?act=checkSku&mod=pda_goodsAssignWhselfB&jsonp=1';
		var param = 'sku='+sku+'&now_group_id='+now_group_id;
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
			//alert(data);
			//console.log(data);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("show_position").style.display = "";
				document.getElementById("sku").value = data.data.sku;
                document.getElementById("now_position_id").value = data.data.now_position_id;
                document.getElementById("now_position").value = data.data.now_position;
				show_list(data.data.position);
				show_list1(data.data.storeposition);
				if(data.data.position!=null){
					gotoScanStep("nums");
				}else{
					scanProcessTip('当前没有仓位请先分配仓位',true);
					gotoScanStep('now_position');
					return false;
				}
				
			}else{
				scanProcessTip(data.errMsg,false);
				document.getElementById("sku").value = data.data;
				gotoScanStep("sku");
			}
		}
	}
}

function fuc_onload(){
	document.getElementById("group_id").focus();
}

function checkposition(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var now_position = document.getElementById("now_position").value;
		createXMLHttpRequest();
		var url = './json.php?act=findPosition&mod=pda_goodsAssignWhselfB&jsonp=1';
		var param = 'now_position='+now_position;
		xmlHttpRequest.open("POST",url,true); 
		xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
		xmlHttpRequest.onreadystatechange = findPositionResponse;
		xmlHttpRequest.send(param);
	}
}

function findPositionResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
			var data = eval("("+res+")");
			//console.log(data.data.show_picking);return;
			if(data.errCode==0){
				scanProcessTip(data.errMsg,true);
				document.getElementById('position_id').value = data.data.id;
				//document.getElementById('hope_store_list').style.display = '';
				//show_list2(data.data.show_picking);
				//show_list3(data.data.show_nopicking);
				gotoScanStep("nums");
			}else{
				scanProcessTip(data.errMsg,false);
				document.getElementById('hope_position_list').style.display = 'none';
				document.getElementById('hope_store_list').style.display = 'none';
				show_list2(data.data.show_picking);
				show_list3(data.data.show_nopicking);
				gotoScanStep("now_position");
			}
			
		}
	}
}

function checknums(){
	var e = e || event;
	if(e.keyCode==13 || e.keyCode==10){
		var sku  = document.getElementById("sku").value;
		var nums = document.getElementById("nums").value;
        var now_position_id =   document.getElementById("now_position_id").value;  //料号仓位关系表自增id
        var now_group_id    =   document.getElementById("now_group_id").value; //调拨单id
        var position_id     =   document.getElementById("position_id").value; //仓位positionId
		var check_number    =   /^\d+$/;
        
		if(now_position_id == '' && position_id == ''){
			scanProcessTip('没有仓位请先分配仓位',false);
			gotoScanStep('now_position');
			return false;
		}
		
		if(sku==''){
			scanProcessTip('sku不能为空，请确认!',false);
			return false;
		}
		if(!check_number.test(nums)){
			scanProcessTip('上架数量有误，请确认!',false);
			return false;
		}
		if(confirm('上架入库数量为'+nums+',是否确认!')){
			scanProcessTip("数据正在提交...",true);
			document.getElementById("nums").blur();
			createXMLHttpRequest();
			var url = './json.php?act=whShelf&mod=pda_goodsAssignWhselfB&jsonp=1';
			var param = 'sku='+sku+"&nums="+nums+"&now_position_id="+now_position_id+'&now_group_id='+now_group_id+'&position_id='+position_id;
			xmlHttpRequest.open("POST",url,true); 
			xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
			xmlHttpRequest.onreadystatechange = checkNumsResponse;
			xmlHttpRequest.send(param);
		}else{
			gotoScanStep('nums');
		}
	}
}

function checkNumsResponse(){
	//var sku = document.getElementById("sku").value;
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var result = xmlHttpRequest.responseText;
			data = eval("("+result+")");
			if(data.errCode == 0){
				scanProcessTip(data.errMsg,true);
				document.getElementById("show_position").style.display = "none";
				document.getElementById('now_position').value = '';
                document.getElementById('now_position_id').value = '';
				var showdetail_position = document.getElementById('now_position_list');
				showdetail_position.innerHTML = '';
				var showdetail_store = document.getElementById('now_store_list');
				showdetail_store.innerHTML = '';
				none_show1();
				none_show2();
				document.getElementById('sku').value = '';
				document.getElementById('nums').value = '';
				gotoScanStep('sku');
			}else{
				scanProcessTip(data.errMsg,false);
				gotoScanStep('nums');
			}
		}
	}
}


function change_hope_position(){
	document.getElementById('select_now_store').value = '0';
	document.getElementById('select_hope_store').value = '0';
}
function change_now_store(){
	document.getElementById('select_hope_position').value = '0';
	document.getElementById('select_hope_store').value = '0';
}
function change_hope_store(){
	document.getElementById('select_now_store').value = '0';
	document.getElementById('select_hope_position').value = '0';
}

function show_list(datas){
	var showdetail = document.getElementById('now_position_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:2px; padding:3px 0;width:100px;"><legend>现存储仓位</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>仓位号</td><td>数量</td></tr>';
	newtable += '<tr><td colspan="2">';
	newtable += '<select name="select_now_position" id="select_now_position" style="width:99px;">';
	if(datas!=null){
		for(var i=0; i<datas.length; i++){
			newtable += '<option value="'+datas[i].id+'">'+datas[i].pName+'---'+datas[i].nums+'</option>';
		}
	}else{
		newtable += '<option value="0">当前没有仓位</option>';
	}
	
	newtable += '</select></td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}

function show_list1(datas){
	var showdetail = document.getElementById('now_store_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:2px; padding:3px 0;width:100px;"><legend>现存货位置</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>仓位号</td><td>数量</td></tr>';
	newtable += '<tr><td colspan="2">';
	newtable += '<select name="select_now_store" id="select_now_store" style="width:99px;" onchange="change_now_store();">';
	if(datas!=null){
		newtable += '<option value="0">选择存货位置</option>';
		for(var i=0; i<datas.length; i++){
			newtable += '<option value="'+datas[i].id+'">'+datas[i].pName+'---'+datas[i].nums+'</option>';
		}
	}else{
		newtable += '<option value="0">当前没存货位</option>';
	}
	newtable += '</select></td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}

function show_list2(datas){
	var showdetail = document.getElementById('hope_position_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:2px; padding:3px 0;width:100px;"><legend>最近空仓位号</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	//newtable += '<tr><td>仓位号</td></tr>';
	newtable += '<tr><td>';
	newtable += '<select name="select_hope_position" id="select_hope_position" style="width:99px;" onchange="change_hope_position();">';
	if(datas!=null){
		var now_position = document.getElementById("now_position").value.toUpperCase();
		newtable += '<option value="0">选择仓位号</option>';
		for(var i=0; i<datas.length; i++){
			if(datas[i].pName==now_position){
				newtable += '<option selected value="'+datas[i].id+'">---['+datas[i].pName+']---</option>';
			}else{
				newtable += '<option value="'+datas[i].id+'">---['+datas[i].pName+']---</option>';
			}
		}
	}else{
		newtable += '<option value="0">没有空仓位</option>';
	}
	newtable += '</select></td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}

function show_list3(datas){
	var showdetail = document.getElementById('hope_store_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<fieldset id="sku_tab_fieldset" style=" float:left; margin-top:2px; padding:3px 0;width:100px;"><legend>最近空存货位号</legend>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	//newtable += '<tr><td>仓位号</td></tr>';
	newtable += '<tr><td>';
	newtable += '<select name="select_hope_store" id="select_hope_store" style="width:99px;" onchange="change_hope_store();">';
	if(datas!=null){
		var now_position = document.getElementById("now_position").value.toUpperCase();
		newtable += '<option value="0">选择存货位置</option>';
		for(var i=0; i<datas.length; i++){
			if(datas[i].pName==now_position){
				newtable += '<option selected value="'+datas[i].id+'">---['+datas[i].pName+']---</option>';
			}else{
				newtable += '<option value="'+datas[i].id+'">---['+datas[i].pName+']---</option>';
			}
		}
	}else{
		newtable += '<option value="0">没空货位置</option>';
	}
	newtable += '</select></td></tr>';
	newtable += '</table>';
	newtable += '</fieldset>';
	showdetail.innerHTML = newtable;
	if(newtable != '') return true;
}

function none_show1(){
	var showdetail = document.getElementById('hope_store_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<select name="select_hope_position" id="select_hope_position" width="100%">';
	newtable += '<option value="0">选择仓位号</option>';
	newtable += '</select>';
	showdetail.innerHTML = newtable;
	document.getElementById('hope_store_list').style.display = "none";
	if(newtable != '') return true;
}

function none_show2(){
	var showdetail = document.getElementById('hope_position_list');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<select name="select_hope_store" id="select_hope_store" width="100%">';
	newtable += '<option value="0">选择存货位置</option>';
	newtable += '</select>';
	showdetail.innerHTML = newtable;
	document.getElementById('hope_position_list').style.display = "none";
	if(newtable != '') return true;
}