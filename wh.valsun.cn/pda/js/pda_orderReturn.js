var xmlHttpRequest; 

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

function scanProcessTip(msg,yesorno,id){
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

function gotoScanStep(id, opa){
	var obj=document.getElementById(id);
	if(opa){
		obj.className=obj.className+' stephere2';
	}else{
		obj.className=obj.className+' stephere';	
	}
	obj.select();
}

function check_order(){
	var obj        = document.getElementById('ebay_id');
	var ebay_id	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RA|RB|RC|RR|RF|LN)\d+(CN|HK|DE200)$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess(ebay_id);
	}else{
		scanProcessTip('000',false);
			obj.focus();
			obj.value='';
			gotoScanStep('ebay_id', false);
	}
}

function check_insert_sku(){
	var obj=document.getElementById('insert_sku');
	var sku	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_sku = /^[A-Z0-9_]+$/;
	if(	p_sku.test(sku)){
		scanProcess_sku(sku);
	}else{
		scanProcessTip('sku检测失败,请重试',false);
		gotoScanStep('insert_sku');
	}
}

function check_insert_num(){
	var obj       = document.getElementById('insert_num');
	var submit_rk = document.getElementById('submit_rk');
	var sku	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_sku = /^[0-9]+$/;
	if(	p_sku.test(sku) && sku >0){
		//scanProcessTip('请提交！',true);
		//gotoScanStep('submit_rk');
		submit_rk.focus();
	}else{
		scanProcessTip('入库数量有误,请重新填写',false);
		check_insert_sku();
		gotoScanStep('insert_num');
	}
}

function submit_abnormal_rk(){
	var obj_id  = document.getElementById('ebay_id');
	var obj_num = document.getElementById('insert_num');
	var submit_rk = document.getElementById('submit_rk');
	var sku     = document.getElementById('insert_sku').value;
	var num	    = parseInt(obj_num.value);
	var ebay_id = obj_id.value;
	if(num >0){
		if(confirm('上架入库数量为'+num+',是否确认!')){
			scanProcessTip('开始入库sku数量操作.....',true);
			//submit_rk.focus();
			postdata(sku,num,ebay_id);
		}else{
			gotoScanStep('insert_num');
		}
	}else{
		scanProcessTip('sku数量有误,请重新输入',false);

		gotoScanStep('insert_num')
		
	}
}

function postdata(sku,num,ebay_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_skuReturn&act=postalldate';
	var param = 'sku='+sku+'&num='+num+'&ebay_id='+ebay_id;
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlHttpRequest.onreadystatechange = postdataResponse_all;
	xmlHttpRequest.send(param);
}

function scanProcess_sku(sku){
    var obj        = document.getElementById('ebay_id');
	var ebay_id	= obj.value;
	createXMLHttpRequest();
	var url = 'process_scan_pda_return2.php';
	var param = 'sku='+sku+'&type=check_sku&ebay_id='+ebay_id;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse_sku;
	xmlHttpRequest.send(param);
}

function scanProcess(ebay_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_skuReturn&act=checkorderid&jsonp=1';
	var param = 'ebay_id='+ebay_id;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse;
	xmlHttpRequest.send(param);
}
//处理返回信息函数 
function scanProcessResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res  = xmlHttpRequest.responseText;
			//alert(res);
			var res = eval('('+res+')');			
			if( res.errCode == '200' ){
				scanProcessTip(res.errMsg,true);
					var ebay_id = document.getElementById('ebay_id');
					ebay_id = res.data;
					var join_sku = document.getElementById('join_sku');
					join_sku.style.display = "block";
					var join_num = document.getElementById('join_num');
					join_num.style.display = "block";
					var insert_sku = document.getElementById('insert_sku');
						insert_sku.focus();
						insert_sku.value='';				
			}else{
				var obj = document.getElementById('ebay_id');
				scanProcessTip(res.errMsg,false);
					obj.focus();
					obj.value='';
					gotoScanStep('ebay_id', false);
			}
		}
	}
}

function scanProcessResponse_sku(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res  = xmlHttpRequest.responseText;
			var data = eval('('+res+')');			
			if( data.res_code == '200' ){
				scanProcessTip(data.res_msg,true);
					var insert_num = document.getElementById('insert_num');
						insert_num.focus();	
						insert_num.value='';	
					var obj = document.getElementById('insert_sku');
					obj.value=data.sku;
			}else{
				var obj = document.getElementById('insert_sku');
				scanProcessTip(data.res_msg,false);
					obj.focus();
					obj.value=data.sku;
					gotoScanStep('insert_sku', false);
			}
		}
	}
}

function postdataResponse_all(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res  = xmlHttpRequest.responseText;
			var res = eval('('+res+')');			
			if( res.errCode == '200' ){
				scanProcessTip(res.errMsg,true);
					var join_sku = document.getElementById('join_sku');
					join_sku.value='';
					join_sku.style.display = "none";
					var join_num = document.getElementById('join_num');
					join_num.value='';
					join_num.style.display = "none";
					var ebay_id = document.getElementById('ebay_id');
						ebay_id.focus();
						ebay_id.value='';				
			}else{
				var obj = document.getElementById('ebay_id');
				scanProcessTip(res.errMsg,false);
					obj.focus();
					obj.value='';
					gotoScanStep('ebay_id', false);
			}
		}
	}
}

function show_menu_list(){
	var menuname_obj=document.getElementById('menuname');
	var display_value = menuname_obj.style.display;
	if(display_value!=''){
		menuname_obj.style.display = '';
	}else{
		menuname_obj.style.display = 'block';
	}
}