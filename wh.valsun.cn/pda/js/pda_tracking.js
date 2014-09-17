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
	$("ebay_id").focus();	
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
//发货单号
function checkPartion(){
	var obj         = document.getElementById('ebay_id');
	var ebay_id	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	if(	p_realebayid.test(ebay_id)){
		scanProcessTip('开始同步...',true);
		scanProcess(ebay_id);
	}else{
		scanProcessTip('发货单号编码错误！',false);
		obj.focus();
		obj.value='';
		gotoScanStep('ebay_id');
	}
}

function scanProcess(ebay_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_tracking&act=pda_tracking&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&action=partion';
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
            
			if( res.errCode == '200' ){//扫描处理完毕一个包裹,进入下一个包裹处理流程gotoScanStep
            // alert(res.data.res_info);return;
				scanProcessTip(res.errMsg,true);
                document.getElementById('tracking').focus();
                document.getElementById('tracking').value='';
                //document.getElementById('show_total').style.display =   'block';
                //document.getElementById('partion_total').innerHTML =   data.partion_total;
				gotoScanStep('tracking');
			}else{
			    // alert(data.res_msg);return;
				scanProcessTip(res.errMsg,false);
                 document.getElementById('ebay_id').focus();
                document.getElementById('ebay_id').value='';
				gotoScanStep('ebay_id');
			}
		}
	}
}


function checkeBayID(){
	var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var tracking = document.getElementById('tracking').value; //跟踪号
    //var package_id = document.getElementById('package_id').value; //包裹编号
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id) || p_ups.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess1(ebay_id,tracking);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('tracking');
	}
}

function scanProcess1(ebay_id,tracking){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_tracking&act=tracking_binding&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&tracking='+tracking;
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
           // alert(res);return;
			var res=eval('('+res+')');
            // alert(data.errMsg);return;
			if( res.errCode == 200 ){//扫描处理完毕一个包裹,进入下一个包裹处理流程
				scanProcessTip(res.errMsg,true);
                 document.getElementById('ebay_id').focus();
   	             document.getElementById('ebay_id').value   = '';
                 document.getElementById('tracking').value   = '';
                //document.getElementById('show_review_total').style.display =   'block';
               // document.getElementById('ebay_id').value = '';
				//gotoScanStep('ebay_id');
            }else if(res.errCode == 400){ 
                 scanProcessTip(res.errMsg,true);
                 document.getElementById('tracking').focus();
   	             document.getElementById('tracking').value   = '';
			}else{
				scanProcessTip(res.errMsg,false);
                document.getElementById('tracking').focus();
                document.getElementById('tracking').value   = '';
				gotoScanStep('tracking');
			}
		}
	}
}
function checkeCountBox(){
    var count_box = document.getElementById('count_box').value; //箱子数量
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^[1-9]\d*$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	if(	p_realebayid.test(count_box)){//compablity with EUB
		scanProcessTip('开始同步...',true);
        if(count_box == 1 ){
           scanProcessTip('输入箱子数量为1，则不用填写跟踪号数量',false); 
           document.getElementById('ebay_id').focus();
           document.getElementById('ebay_id').value='';
        }else{
            document.getElementById('count_box').value = count_box ; //箱子数量
            document.getElementById('tracking').focus();
            document.getElementById('tracking').value='';
        }
	//	scanProcess3(count_box);
	}else{
		scanProcessTip('箱子数量不对！',false);
		gotoScanStep('count_box');
	}

}


function trackingCount(){
	var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
//	var weighing = document.getElementById('weighing').value; //重量
    var count_box = document.getElementById('count_box').value; //箱子数量
    var tracking = document.getElementById('tracking').value; //跟踪号数量
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id) || p_ups.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步...',true);
		scanProcess3(ebay_id,count_box,tracking);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('tracking');
	}

}
function scanProcess3(ebay_id,count_box,tracking){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_ExpressWeighing&act=trackingCount&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&count_box='+count_box+'&tracking='+tracking;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse3;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function scanProcessResponse3(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
            //alert(res);return;
			var res=eval('('+res+')');
			if( res.errCode == '200' ){//返回正常数据
				scanProcessTip(res.errMsg,true);
                 document.getElementById('ebay_id').focus();
   	             document.getElementById('ebay_id').value   = '';
                 document.getElementById('weighing').value   = '';
                 document.getElementById('tracking').value   = '';
                 document.getElementById('count_box').value   = '';
				gotoScanStep('ebay_id');
			}else{ //返回错误
				scanProcessTip(res.errMsg,false);
			}
		}
	}
}
function showReview(){
    var package_id = document.getElementById('package_id').value; //包裹编号
    if(package_id == ''){
        scanProcessTip('没有包裹编号', false);
        return false;
    }
    scanProcess2(package_id);
}

function scanProcess2(package_id){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_partitionChecking&act=comparison&jsonp=1';
	var param = 'action=show_review&package_id='+package_id;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse2;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function scanProcessResponse2(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
            //alert(res);return;
			var res=eval('('+res+')');
			if( res.errCode == '200' ){//返回正常数据
				scanProcessTip(res.errMsg,true);
                document.getElementById('show_review_total').style.display =   'block';
                document.getElementById('review_total').innerHTML =   res.data.review_total;
				gotoScanStep('ebay_id');
			}else{ //返回错误
				scanProcessTip(res.errMsg,false);
			}
		}
	}
}
