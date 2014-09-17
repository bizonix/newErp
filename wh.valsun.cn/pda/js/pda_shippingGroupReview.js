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

function fuc_onload(){
	$("packageid").focus();	
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

function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}

function checkPackage(){
	var obj         = document.getElementById('packageid');
	var packageid	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	if(	p_realebayid.test(packageid)){
		scanProcessTip('开始同步...',true);
		scanProcess(packageid);
	}else{
		scanProcessTip('口袋编号错误！',false);
		obj.focus();
		obj.value='';
		gotoScanStep('packageid');
	}
}

function scanProcess(packageid){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_shippingGroupReview&act=outReview&jsonp=1';
	var param = 'packageid='+packageid+'&action=checkPackage';
	xmlHttpRequest.open("POST",url,true);
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function scanProcessResponse(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
           // alert(res);return;
			var res=eval('('+res+')');
			if( res.errCode == '200' ){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(res.errMsg,true);
			//	document.getElementById('packageid').value    = res.res_info;
              //  document.getElementById('real_partion').value = res.real_partion;
              //  document.getElementById('packageid').value   = res.packageid;
                document.getElementById('order_show').style.display = 'block';
               // document.getElementById('real_packageid').value     = res.packageid;
                show_result(res.data.partion_data, res.data.review_data); //展示分区和复核数据
                gotoScanStep('ebay_id');
			}else if(res.errCode == '201'){
                scanProcessTip(res.errMsg,false);
				show_result(res.data.partion_data, res.data.review_data); //展示分区和复核数据
                gotoScanStep('packageid');
			}else{
                scanProcessTip(res.errMsg,false);
				gotoScanStep('packageid');
			}
		}
	}
}
//展示分区和复核数据
function show_result(partion_data, review_data){
    document.getElementById('partion').innerHTML    =   partion_data.partion;
    document.getElementById('totalnum').innerHTML   =   partion_data.totalNum;
    document.getElementById('totalweight').innerHTML=   partion_data.totalWeight;
    document.getElementById('review_weight').innerHTML  =   review_data.review_weight;
    document.getElementById('review_num').innerHTML     =   review_data.review_num;
}

function checkOrder(){
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
    var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var packageid    = document.getElementById('packageid').value; //真实包裹编号
	var p_realebayid = /^\d+$/;
	var p_eub_trackno= /^(LK|RA|RI|RL|RB|RM|RC|RD|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
	var p_ups_trackno= /^(1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess1(ebay_id, packageid);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('ebay_id');
	}
}

function scanProcess1(ebay_id, packageid){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_shippingGroupReview&act=orderReview&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&action=check_ebay_id&packageid='+packageid;
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