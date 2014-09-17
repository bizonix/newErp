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
	var url = 'json.php?mod=pda_ExpressWeighing&act=pda_ExpressId&jsonp=1';
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
            //    document.getElementById('weighing').focus();
            //    document.getElementById('weighing').value='';
                if(res.data.content !=null){                   
                    document.getElementById('show_note').style.display =   'block';
                    document.getElementById('show_note').innerHTML =  res.data.content;
                }
				gotoScanStep('weighing');
             }else if(res.errCode == '205'){
                    //扫描到分区复核编号的时候
             // 	 alert(data.res_msg);return;
				scanProcessTip(res.errMsg,false);
               // document.getElementById('ebay_id').focus();
               //	document.getElementById('ebay_id').value   = '';
                //document.getElementById('show_total').style.display =   'block';
                //document.getElementById('partion_total').innerHTML =   data.partion_total;
				gotoScanStep('ebay_id');
			}else{
			    // alert(data.res_msg);return;
				scanProcessTip(res.errMsg,false);
				gotoScanStep('ebay_id');
			}
		}
	}
}


function checkeBayID(){
	var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var weighing = document.getElementById('weighing').value; //重量
    //var package_id = document.getElementById('package_id').value; //包裹编号
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id) || p_ups.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步重量...',true);
		scanProcess1(ebay_id,weighing);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('ebay_id');
	}
}

function scanProcess1(ebay_id,weighing){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_ExpressWeighing&act=pda_ExpressWeighing&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&weighing='+weighing+'&action=ebayId';
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
               //	gotoScanStep('count_box');   
                   var a =  document.getElementById('weighing');             
                a.className='textinput' ; //箱子数量 
                 document.getElementById('count_box').focus();
   	             document.getElementById('count_box').value   = 1;
                  //document.getElementById('weighing').value   = '';
                //document.getElementById('show_review_total').style.display =   'block';
               // document.getElementById('ebay_id').value = '';
            
			}else{
				scanProcessTip(res.errMsg,false);
				gotoScanStep('weighing');
			}
		}
	}
}
function checkeCountBox(){
    var count_box = document.getElementById('count_box').value; //箱子数量
    var weighing = document.getElementById('weighing').value; //重量
   	var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^[1-9]\d*$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	//if(	p_realebayid.test(count_box)){//compablity with EUB
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id) || p_ups.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步箱子数量...',true);
        scanProcess4(ebay_id,count_box,weighing);
	//	scanProcess3(count_box);
	}else{
		scanProcessTip('订单号不对！',false);
		gotoScanStep('ebay_id');
	}

}
function scanProcess4(ebay_id,count_box,weighing){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_ExpressWeighing&act=boxCount&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&count_box='+count_box+'&weighing='+weighing;
	xmlHttpRequest.open("POST",url,true); 
	xmlHttpRequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded"); 
	xmlHttpRequest.onreadystatechange = scanProcessResponse4;
	xmlHttpRequest.send(param);
}

//处理返回信息函数 
function scanProcessResponse4(){
	if(xmlHttpRequest.readyState == 4){  
		if(xmlHttpRequest.status == 200){  
			var res = xmlHttpRequest.responseText;
            //alert(res);return;
			var res=eval('('+res+')');
			if( res.errCode == '200' ){//返回正常数据
				scanProcessTip(res.errMsg,true);
               // gotoScanStep('tracking'); 
                var a =  document.getElementById('count_box');             
                a.className='textinput' ; //箱子数量 箱子数量
                document.getElementById('tracking').focus();
                document.getElementById('tracking').value='';
                
			//	gotoScanStep('ebay_id');
             }else if(res.errCode == '20'){
   	             scanProcessTip(res.errMsg,true);  
                 document.getElementById('ebay_id').focus();
   	             document.getElementById('ebay_id').value   = '';
                 document.getElementById('weighing').value   = '';
                 document.getElementById('tracking').value   = '';
                 document.getElementById('count_box').value   = '';
			}else{ //返回错误
				scanProcessTip(res.errMsg,false);
			}
		}
	}
}

function trackingCount(){
	var obj     = document.getElementById('ebay_id'); //订单编号或跟踪号
	var ebay_id	= obj.value;
	var weighing = document.getElementById('weighing').value; //重量
    var count_box = document.getElementById('count_box').value; //箱子数量
    var tracking = document.getElementById('tracking').value; //跟踪号数量
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var p_realebayid=/^\d+$/;
	var p_eub_trackno=/^(LK|RI|RL|RD|RA|RB|RM|RC|RR|RF|LN|LM|AG)\d+(CN|HK|DE200)$/;
    var p_ups        =/^(1ZA)/;
	var p_ups_trackno=/^(BLVS|1ZR)\d+$/;
	if(	p_realebayid.test(ebay_id) || p_eub_trackno.test(ebay_id) || p_ups_trackno.test(ebay_id) || p_ups.test(ebay_id)){//compablity with EUB
		scanProcessTip('开始同步跟踪号...',true);
		scanProcess3(ebay_id,count_box,tracking,weighing);
	}else{
		scanProcessTip('订单编号规格不符！',false);
		gotoScanStep('ebay_id');
	}

}
function scanProcess3(ebay_id,count_box,tracking,weighing){
	createXMLHttpRequest();
	var url = 'json.php?mod=pda_ExpressWeighing&act=trackingCount&jsonp=1';
	var param = 'ebay_id='+ebay_id+'&count_box='+count_box+'&tracking='+tracking+'&weighing='+weighing;
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
			var data=eval('('+res+')');
			if( data.errCode == '200' ){//返回正常数据
				scanProcessTip(data.errMsg,true);
                 document.getElementById('ebay_id').focus();
   	             document.getElementById('ebay_id').value   = '';
                 document.getElementById('weighing').value   = '';
                 document.getElementById('tracking').value   = '';
                 document.getElementById('count_box').value   = '';
			//	gotoScanStep('ebay_id');
			}else{ //返回错误
				scanProcessTip(data.errMsg,false);
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
