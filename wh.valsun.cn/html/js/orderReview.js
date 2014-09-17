$(function(){
	$('#ebay_id').focus();
	
	//更新索引
	$('#positionIndex').click(function(){
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=position&act=updatePositionIndex&jsonp=1",
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					window.location.href = "index.php?mod=position&act=positionList&state=更新成功";
				}else{
					window.location.href = "index.php?mod=position&act=positionList&state=更新失败，请重试！";
				}				
			}

		});
	});
});

function scanProcessTip(msg,yesorno,id){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus').html(str);
	}catch(e){}
}

function gotoScanStep(id){
	var obj=document.getElementById(id);
	obj.className=obj.className+' stephere';
	obj.select();
}

function checkeBayID(){
	var ebay_id	= $('#ebay_id').val();	
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	//jSound.play({src:'./sounds/allok.wav',volume:100});return false;
	var p_realebayid=/^\d+$/;
	if(	p_realebayid.test(ebay_id)){//compablity with EUB
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=orderReview&act=checkOrder&jsonp=1",
			data	: {ebay_id:ebay_id},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					jSound.play({src:'./sounds/allok.wav',volume:100});//add wangminwei 2014-03-07
					scanProcessTip(msg.errMsg,true);
					if(show_list(msg.data)){
						$('#ebay_sku').focus();
						$('#ebay_sku').val('');
					}
				}else{
					scanProcessTip(msg.errMsg,false);
					$('#ebay_id').focus();
					$('#ebay_id').val('');
					gotoScanStep('ebay_id');
					jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
				}				
			}

		});
	}else{
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
		scanProcessTip('订单号有误,请重试',false);
		gotoScanStep('ebay_id');
	}
}
function set_shipping_end(){
    var ebay_id	= $('#ebay_id').val();	
    	var p_realebayid=/^\d+$/;
	if(	p_realebayid.test(ebay_id)){//compablity with EUB
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=orderReview&act=setShippingEnd&jsonp=1",
			data	: {ebay_id:ebay_id},
			success	: function (msg){
				//console.log(msg);return false;
                document.getElementById('show_shipping_end').style.display = 'block';
                
                scanProcessTip(msg.errMsg,true);
				if(msg.errCode==200){
				    
					if(show_list_end(msg.data,ebay_id)){
						$('#ebay_sku').focus();
						$('#ebay_sku').val('');
                        $("#show_shipping_end").dialog({
                           width:500,
                           buttons: {
                          // "确认手动完结该发货单的复核吗": function() {
                           //  $("#show_shipping_end").dialog('close');
                          //  },
                              "取消复核": function() {
                                    $("#show_shipping_end").dialog('close');
                                }
                            }
                        });
  	                    /*$("#show_shipping_end" ).dialog()({
                             bgiframe: true,
                                resizable: false,
                                width:1000,
                                modal: true,
                             buttons: {
                          // "确认手动完结该发货单的复核吗": function() {
                           //  $("#show_shipping_end").dialog('close');
                          //  },
                          "取消手动完结复核": function() {
                            $("#show_shipping_end").dialog('close');
                            }
                         }
                     });*/
					}

				}else{
					scanProcessTip(msg.errMsg,false);
					$('#ebay_id').focus();
					$('#ebay_id').val('');
					gotoScanStep('ebay_id');
					jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
				}
		   }

		});
	}else{
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
		scanProcessTip('订单号有误,请重试',false);
		gotoScanStep('ebay_id');
	}
}
function chage_ebay_sku(){
	$('#ebay_sku').val('');
}

function check_ebay_sku(){
	var ebay_id = $('#ebay_id').val();
	var ebay_sku = $('#ebay_sku').val();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(ebay_id == ''){
		scanProcessTip('请先扫描订单号',false);
		//重新扫ebay id
		gotoScanStep('ebay_id');
		$('#showdetail').html('');
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
		return false;
	}
	if(ebay_sku != ''){
		$('#ebay_sku').blur();
		//$('#ebay_sku').val('');
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=orderReview&act=checkSku&jsonp=1",
			data	: {ebay_id:ebay_id,ebay_sku:ebay_sku},
			success	: function (msg){
				//console.log(msg.data[0].shipOrderId);return false;
				if(msg.errCode==100){
					scanProcessTip(msg.errMsg,true);
					$('#num').show();
					$('#sku_num').focus();
					$('#sku_num').val('');
					jSound.play({src:'./sounds/allok.wav',volume:100});//add wangminwei 2014-03-07
				}else if(msg.errCode==0 || msg.errCode==1){
					scanProcessTip(msg.errMsg,true);
					if(show_list(msg.data)){
						$('#ebay_sku').focus();
						$('#ebay_sku').val('');
						
						if(msg.errCode==1){
							var ebay_ids = msg.data[0].shipOrderId;
							showWindow(ebay_ids,405);
						}
					}
				}else{
					scanProcessTip(msg.errMsg,false);
					$('#ebay_sku').focus();
					$('#ebay_sku').val('');
					jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
				}				
			}

		});
		var now_ebay_sku = document.getElementById('now_ebay_sku');
		now_ebay_sku.value = ebay_sku;
	}else{
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
	}
}

function check_sku_num(){
	var ebay_id  = $('#ebay_id').val();
	var ebay_sku = $('#ebay_sku').val();
	var sku_num  = $('#sku_num').val();
	var check_num =  /^([0-9]\d*)$/;
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(ebay_id == ''){
		scanProcessTip('请先扫描订单号',false);
		//重新扫ebay id
		gotoScanStep('ebay_id');
		$('#showdetail').html('');
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
		return false;
	}
	if(ebay_sku == ''){
		scanProcessTip('请先扫描料号',false);
		gotoScanStep('ebay_sku');
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
		return false;
	}
	if(sku_num != '' && check_num.test(sku_num)){
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=orderReview&act=scanNum&jsonp=1",
			data	: {ebay_id:ebay_id,ebay_sku:ebay_sku,sku_num:sku_num},
			success	: function (msg){
				//console.log(msg.data[0].shipOrderId);return false;
				if(msg.errCode==0 || msg.errCode==1){
					scanProcessTip(msg.errMsg,true);
					if(show_list(msg.data)){
						$('#ebay_sku').focus();
						$('#ebay_sku').val('');
						$('#now_ebay_sku').val('');
						$('#sku_num').val('');
						$('#num').hide();
						
						if(msg.errCode==1){
							var ebay_ids = msg.data[0].shipOrderId;
							showWindow(ebay_ids,405);
						}
					}
				}else{
				    				    
					scanProcessTip(msg.errMsg,false);
					$('#sku_num').focus();
					$('#sku_num').val('');
					var now_ebay_sku = $('#now_ebay_sku').val();
                   	$('#ebay_sku').val('');
					$('#ebay_sku').val(now_ebay_sku);
				}				
			}

		});
	}else{
		scanProcessTip('数量只能为正整数',false);
		gotoScanStep('sku_num');
		jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
	}
}


function show_list_end(datas,ebay_id){
	$('#show_shipping_end').html('');
	var newtable = '';
	newtable += '<hr/>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>SKU</td><td>描述</td><td>已扫描数量</td><td>订单数量</td><td>差值</td><td>状态</td></tr>';
	for(var i in datas) {
		newtable += '<tr><td>'+datas[i].sku+'</td><td>'+datas[i].goodsName+'</td><td>'+datas[i].amount+'</td><td>'+datas[i].totalNums+'</td><td>'+(datas[i].totalNums-datas[i].amount)+'</td><td id="'+datas[i].sku+'"></td></tr>';
	}
    newtable +='<tr><td colspan="6"><input type="button" onClick="buttons_end('+ebay_id+')" value="确认手动完结该发货单的复核" /></td></tr>';
	newtable += '</table>';
	$('#show_shipping_end').html(newtable);
	for(var i in datas) {
		if(datas[i].totalNums-datas[i].amount==0){
			$('#'+datas[i].sku).html('<image src="images/right.png" />');
		}else{
			$('#'+datas[i].sku).html('<image src="images/error.png" />');
		}
	}
	if(newtable != '') return true;
}
//add chenxianyu 2014-07-17
function buttons_end(ebay_id){
    document.getElementById('show_shipping_end').style.display='none';
  		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=orderReview&act=completion&jsonp=1",
			data	: {ebay_id:ebay_id},
			success	: function (msg){
				//console.log(msg.data[0].shipOrderId);return false;
				if(msg.errCode==0 || msg.errCode==200){
					scanProcessTip(msg.errMsg,true);
					
						$('#ebay_sku').focus();
						$('#ebay_sku').val('');
						$('#now_ebay_sku').val('');
						$('#sku_num').val('');
						$('#num').hide();
						if(msg.errCode==200){
							var ebay_ids = msg.data.shipOrderId;
							showWindow(ebay_ids,901);
                            gotoScanStep('ebay_id', false);
                            jSound.play({src:'./sounds/success.wav',volume:100});
                            take_photo(ebay_ids);
													
						}
					
				}else{
				    				    
					scanProcessTip(msg.errMsg,false);
					$('#sku_num').focus();
					$('#sku_num').val('');
					var now_ebay_sku = $('#now_ebay_sku').val();
                   	$('#ebay_sku').val('');
					$('#ebay_sku').val(now_ebay_sku);
				}
                $("#show_shipping_end").dialog("close");				
			}
		});
}

function show_list(datas){
    
    if(datas ==''){return false;}
	$('#showdetail').html('');
	var newtable = '';
	newtable += '<hr/>';
	newtable += '<table id="sku_tab" cellpadding="0" cellspacing="0" border="0" width="100%">';
	newtable += '<tr><td>SKU</td><td>描述</td><td>已扫描数量</td><td>订单数量</td><td>差值</td><td>状态</td></tr>';
	for(var i in datas) {
		newtable += '<tr><td>'+datas[i].sku+'</td><td>'+datas[i].goodsName+'</td><td>'+datas[i].amount+'</td><td>'+datas[i].totalNums+'</td><td>'+(datas[i].totalNums-datas[i].amount)+'</td><td id="'+datas[i].sku+'"></td></tr>';
	}
	newtable += '</table>';
	$('#showdetail').html(newtable);
	for(var i in datas) {
		if(datas[i].totalNums-datas[i].amount==0){
			$('#'+datas[i].sku).html('<image src="images/right.png" />');
		}else{
			$('#'+datas[i].sku).html('<image src="images/error.png" />');
		}
	}
	if(newtable != '') return true;
}

//复核完成
function review_complete(id){
	$.ajax({
		type    : "POST",
		dataType: "jsonp",
		url     : "json.php?mod=orderReview&act=complete&jsonp=1",
		data	: {ebay_id:id},
		success	: function (msg){
			//console.log(msg);return false;
			if(msg.errCode==0){
				scanProcessTip(msg.errMsg,true);
				$('#ebay_sku').val('');
				$('#showdetail').html('');
				$('#now_ebay_sku').val('');
				gotoScanStep('ebay_id', false);
				jSound.play({src:'./sounds/success.wav',volume:100});//add wangminwei 2014-03-07              
				take_photo(id);
				$('#ebay_id').focus();
				$('#ebay_id').val(''); 
			}else{
				scanProcessTip(msg.errMsg,false);
				$('#ebay_id').focus();
				$('#ebay_id').val('');
				$('#showdetail').html('');
				$('#now_ebay_sku').val('');
				jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
				gotoScanStep('ebay_id', false);
			}				
		}

	});
}

//复核完成
function review_complete1(id){
	$.ajax({
		type    : "POST",
		dataType: "jsonp",
		url     : "json.php?mod=orderReview&act=complete&jsonp=1",
		data	: {ebay_id:id},
		success	: function (msg){
			//console.log(msg);return false;
			if(msg.errCode==0){
				scanProcessTip(msg.errMsg,true);
				$('#ebay_id').focus();
				$('#ebay_id').val('');
				$('#ebay_sku').val('');
				$('#showdetail').html('');
				$('#now_ebay_sku').val('');
				jSound.play({src:'./sounds/success.wav',volume:100});//add wangminwei 2014-03-07
				gotoScanStep('ebay_id', false);
			}else{
				scanProcessTip(msg.errMsg,false);
				$('#ebay_id').focus();
				$('#ebay_id').val('');
				$('#showdetail').html('');
				$('#now_ebay_sku').val('');
				jSound.play({src:'./sounds/failure.mp3',volume:100});//add wangminwei 2014-03-07
				gotoScanStep('ebay_id', false);
			}				
		}

	});
}

function get_ebay_sku(){
	var now_ebay_sku = $('#now_ebay_sku').val();
	$('#ebay_sku').val(now_ebay_sku);
}

//拍照
function take_photo(ebay_id){
	take_snapshot(ebay_id);
}

webcam.set_swf_url('./js/webcam.swf');
webcam.set_quality( 95 ); // JPEG quality (1 - 100)
webcam.set_shutter_sound( false ); // play shutter click sound
webcam.set_stealth( true );
document.write( webcam.get_html(0, 0, 600,400) );

function take_snapshot(ebay_id) {
	var now_time = document.getElementById('now_time').value;
	var url = './get_flashcam_order.php?action=save&oid='+ebay_id+'&fd=images/fh/'+now_time+',fh_'+ebay_id; 
	webcam.set_api_url(url);   
	webcam.snap();
}
webcam.set_hook( 'onComplete', 'my_completion_handler' );
function my_completion_handler(msg) {
	if (msg.match(/images.*/)) {
		var image_url = msg;//RegExp.$1;
		jSound.play({src:'./sounds/allok.wav',volume:100});
		show_scan_list();
        publish_mq(msg);
	}else {
		now_ebayid = document.getElementById('ebay_id').value;
		document.getElementById('least_scan_id').innerHTML = '<a style="cursor:pointer" onclick="retake('+now_ebayid+')">重拍</a>';
	}
}


//拍照
function take_photo11(ebay_id){	
	var now_time = $('#now_time').val();
	$.ajax({
		type    : "POST",
		url     : "./get_flashcam_order.php",
		data	: 'action=order&orderid=fh_'+ebay_id+'&fd=images/fh/'+now_time+',fh_'+ebay_id,
		success	: function (msg){
			//console.log(msg);return false;
			setTimeout("get_photo()",1300);
			return false;
		}

	});
}

//获取照片
function get_photo(){
	$.ajax({
		type    : "POST",
		url     : "./get_flashcam_order.php",
		data	: 'action=check&dataformat=json',
		success	: function (msg){
			//console.log(msg);return false;
			jSound.play({src:'./sounds/allok.wav',volume:100});//add wangminwei 2014-03-07
			show_scan_list();//显示最近复核五个订单照片
		}

	});
}

//获取最近复核5个订单照片
function show_scan_list(){
	var ranNum = 10*Math.random();    //产生随机数
	var now_time = $('#now_time').val();
	var ebay_ids = [];
	var least_scan_ids = document.getElementById('least_scan').value;
	var show_picture = document.getElementById('show_picture');
    
	$('#least_scan_id').html('');
	ebay_ids = least_scan_ids.split(',');
	var len = ebay_ids.length;
	var newhtml = '';
	//显示最近复核的5个订单
	var pic_count	=	0;
	newhtml += '<div width="120px" style="float:left;"><span>订单号：'+ebay_ids[len-1]+'</span><br/><span><a class="inherit" href="images/fh/'+now_time+'/fh_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" target="_blank"><image width="100px" src="images/fh/'+now_time+'/fh_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[len-1]+')">重拍</a></div>';
	if(len-1>0){
		if(len>5){
			for(var i=len-2;i>=len-5;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/fh/'+now_time+'/fh_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/fh/'+now_time+'/fh_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}else{
			for(var i=len-2;i>=0;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/fh/'+now_time+'/fh_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/fh/'+now_time+'/fh_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}
	}
	$('#least_scan_id').html(newhtml);
	show_picture.style.display = 'block';
	$('#show_picture').show();
	return ;
}

//重新拍照
function retake(bill){
	take_photo(bill);
}
function show(bill){
	var ebay_ids = [];
	var least_scan_ids = $('#least_scan').val();
	ebay_ids = least_scan_ids.split(',');
	var len = ebay_ids.length;
	for(var i=0;i<len;i++){
		if(ebay_ids[i]==bill){
			delete ebay_ids[i];	
		}
	}
	ebay_ids.push(bill);
	var new_ebay_ids = ebay_ids.join(',');
	if (new_ebay_ids.substr(0,1)==',') new_ebay_ids=new_ebay_ids.substr(1);
	var new_ebay_id = new_ebay_ids.replace(',,',',');	
	$("#least_scan").val(new_ebay_id);
	show_scan_list();
}

/****************拍照弹出框处理 start*********/
var currKey = '';
function  keydown(e) 
{ 
   var e = e||event;
    currKey += e.keyCode||e.which||e.charCode;
} 

function showWindow(ebay_id,status){
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
   objWin.style.top  = "200px";   
   objWin.style.left = "300px";   
   objWin.style.width="500px";
   objWin.style.height="100px";
   objWin.style.border="2px solid #AEBBCA";
   objWin.style.background="#00FFFF";
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
  if(status == 901){
     str+='<div><span >订单料号数量与目前输入的不符合，请点击拍照，或点击取消跳过拍照！</span><a href="javascript:closeWindow()" >关闭</a></span></div>';  //标题栏
 
  }else{
      str+='<div><span >订单料号数量与目前输入的符合，如果正确请点击拍照，或点击取消跳过拍照！</span><a href="javascript:closeWindow()" >关闭</a></span></div>';  //标题栏

  }
  str+='<div align="center" style="margin:25px;">';
  str+='<a onclick="conFh('+ebay_id+','+status+')"><input type="button" id="yew" value="确定"></a>';
  str+='<a onclick="conNoFh('+ebay_id+','+status+')"><input type="button" id="no" value="取消"></a>';
  str+='</div>';
  document.getElementById('divWin').innerHTML=str;
  document.getElementById('yew').focus();
  currKey = '';
  window.document.onkeydown = keydown;
}

function closeWindow(){
var obj = document.getElementById("divWin");
  document.body.removeChild(obj);
  var obj1 = document.getElementById("win_bg");
  document.body.removeChild(obj1);
}

function conFh(ebay_id,status){
	if(currKey!='13'){
		if(currKey!=''){
			currKey = '';
			return void(0);
		}	
	}
	currKey = '';
	var ebay_scan_ids = $('#least_scan').val();
	var ebay_ids = ebay_id;
	if(ebay_scan_ids==''){
		var now_scan_ids = ebay_ids;
	}else{
		var now_scan_ids = ebay_scan_ids+','+ebay_ids;
	}
	$('#least_scan').val(now_scan_ids);
	if(status!=901){
     	review_complete(ebay_ids);	
	}
	closeWindow();
}
function conNoFh(ebay_id,status){
    if(status !=901){
        
	review_complete1(ebay_id);
    }
	currKey = '';
	closeWindow();
}
/****************拍照弹出框处理 end*********/

/**图片队列推送**/
function publish_mq(msg){
	var url  = "/json.php?mod=publish&act=receive_image&jsonp=1";
	$.post(url,{msg:msg});
}