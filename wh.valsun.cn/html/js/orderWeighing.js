/*
* 小包称重扫描功能
* @author by heminghua
*/
$(document).ready(function(){
	$("#orderid").focus();
	$("#orderid").keydown(function(){
		var e = e||event;
		if(e.keyCode==13||e.keyCode==10){
			var orderid = this.value;
			var ebay_id_p=/^\d+$/;
			if(	orderid=='' || !ebay_id_p.test(orderid)){
				//$("#successLog").html("");
				//$("#errorLog").html("发货单输入错误");
				scanProcessTip("发货单输入错误",false);
				gotoScanStep('orderid');
				return false;
			}

			//alert(orderid);
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "json.php?act=orderWeighingCheck&mod=orderWeighing&jsonp=1",
				data : {'orderid':orderid},
				success:function(msg){
					//console.log(msg);
					if(msg.errCode==0){
						$('#now_ebayid').val(msg.data.orderid);
						if(msg.data.type=="flat"){
							//$("#successLog").html("获取同步重量中.....");
							//$("#errorLog").html("");
							scanProcessTip("获取同步重量中.....",true);
							$("#flat_order").css("display","block");
							$("#register_order").css("display","none");
							flat_out();
						}else if(msg.data.type=="register"){
							scanProcessTip("获取同步重量中.....",true);
							$("#register_order").css("display","block");
							$("#flat_order").css("display","none");
							register_out();
						}
					}else{
						gotoScanStep('orderid');
						scanProcessTip(msg.errMsg,false);
						$("#flat_order").css("display","none");
						$("#register_order").css("display","none");
					}
				
				
				}
			});
		}
	});
});

var checkWeightIntval=0;
var orderweight=0;

//平邮处理
function flat_out(){
	gotoScanStep('curweight_flat');
	checkOrderWeight2_flat1();
}

function checkOrderWeight2_flat1(){
	checkWeightIntval=setInterval(function(){	
				orderweight=window.document.app.currentweight;
				//sometime return weight value 1 ,so must bigger than 5
				if(orderweight>5){
					clearInterval(checkWeightIntval);
					//here must relay for accurate weight result
					setTimeout(function(){
						checkOrderWeight2_flat2();
					},400);
				}
	},100);
}

function checkOrderWeight2_flat2(){
	var currentweight_flat=orderweight;
	//confirm weight
	var nowcurrweight=window.document.app.currentweight;
	if(nowcurrweight==0){
		checkOrderWeight2_flat1();
		return false;
	}else if(nowcurrweight!=currentweight_flat){
		checkOrderWeight2_flat1();
		return false;
	}
	try{
		orderweight=0;
		clearInterval(checkWeightIntval);
	}catch(e){}
	$('#curweight_flat').val(currentweight_flat);
	var weight_p=/^\d+$/;
	if(weight_p.test(currentweight_flat)){
		if(currentweight_flat>=2000){
			scanProcessTip('重量超出2KG范围', false);
			$('#curweight_flat').val('');
			gotoScanStep('orderid');
			return false;
		}
		
		//拍照
		var now_ebayid = $('#now_ebayid').val();
		var ebay_scan_ids = $('#least_scan').val();
		if(ebay_scan_ids==''){
			var now_scan_ids = now_ebayid;
		}else{
			var now_scan_ids = ebay_scan_ids+','+now_ebayid;
		}
		$('#least_scan').val(now_scan_ids);
//		take_photo(now_ebayid);
		
		scanProcessTip('重量录入成功',true);
		$('#curweight_flat').blur();
		var orderid = $('#orderid').val();
		var mailway = $('#mailway_flat').val();
		var partionId;
		if($("#partion").css.display=="block"){
			partionId = $("#partion").val();
		}else{
			partionId = "";
		}
		if(orderid == ''){
			scanProcessTip('请先扫描订单号',false);
			//重新扫ebay id
			gotoScanStep('orderid');
			return false;
		}
		//开始同步
		scanProcessTip('<img src=./images/cx.gif />开始同步...',true);
		flat_out_ajax(orderid,mailway,partionId,currentweight_flat);
	}else{
		scanProcessTip('重量录入失败,请重试',false);
		$('#curweight_flat').blur();		
		setTimeout(function(){
			$('#curweight_flat').focus();
			$('#curweight_flat').val('');
			//重新称重
			flat_out();
			return;
		},200);
	}
}

function flat_out_ajax(orderid,channelId,partionId,orderweight){
	//var orderweight = window.document.app.currentweight;
	$.ajax({
		type:"POST",
		url:"json.php?act=orderWeighingFlat&mod=orderWeighing&jsonp=1",
		dataType:"json",
		data:{"orderid":orderid,"channelId":channelId,"partionId":partionId,"orderweight":orderweight},
		success:function(msg){
			if(msg.errCode==0){
				//初始化一些全局值
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip("扫描成功！",true);
				$('#orderid').focus();
				$('#orderid').val('');
				$('#curweight_flat').val('');					
				jSound.play({src:'./sounds/allok.wav',volume:100});
				gotoScanStep('orderid');
			}else{
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip(msg.errMsg,false);
				gotoScanStep('orderid');
			}
		}
	});
}

//挂号处理
function register_out(){
	gotoScanStep('curweight_register');
	checkOrderWeight2_register1();


	var orderid = $("#orderid").value;
	var channelId = $("#mailway_register").value;
	var partionId;
	if($("#partion1").css.display=="block"){
		partionId = $("#partion").value;
	}else{
		partionId = "";
	}
	var checkWeight = setInterval(function(){
		var orderweight = window.document.app.currentweight;
		$("#curweight_register").value = orderweight;
		if(orderweight>5){
			setTimeout(function(){
				clearInterval(checkWeight);
				out_ajax(orderId,channelId,partionId);
			},1000);
		}
	},100);
}


function checkOrderWeight2_register1(){
	checkWeightIntval=setInterval(function(){	
				orderweight=window.document.app.currentweight;
				//sometime return weight value 1 ,so must bigger than 5
				if(orderweight>5){
					clearInterval(checkWeightIntval);
					//here must relay for accurate weight result
					setTimeout(function(){
						checkOrderWeight2_register2();
					},400);
				}
	},100);
}

function checkOrderWeight2_register2(){
	var currentweight_flat=orderweight;
	//confirm weight
	var nowcurrweight=window.document.app.currentweight;
	if(nowcurrweight==0){
		checkOrderWeight2_register1();
		return false;
	}else if(nowcurrweight!=currentweight_flat){
		checkOrderWeight2_register1();
		return false;
	}
	try{
		orderweight=0;
		clearInterval(checkWeightIntval);
	}catch(e){}
	$('#curweight_register').val(currentweight_flat);
	var weight_p=/^\d+$/;
	if(weight_p.test(currentweight_flat)){
		if(currentweight_flat>=2000){
			scanProcessTip('重量超出2KG范围', false);
			$('#curweight_register').val('');
			gotoScanStep('orderid');
			return false;
		}
		
		//拍照
		var now_ebayid = $('#now_ebayid').val();
		var ebay_scan_ids = $('#least_scan').val();
		if(ebay_scan_ids==''){
			var now_scan_ids = now_ebayid;
		}else{
			var now_scan_ids = ebay_scan_ids+','+now_ebayid;
		}
		$('#least_scan').val(now_scan_ids);
//		take_photo(now_ebayid);
		
		scanProcessTip('重量录入成功',true);
		$('#curweight_register').blur();
		gotoScanStep('tracknumber');
	}else{
		scanProcessTip('重量录入失败,请重试',false);
		$('#curweight_flat').blur();		
		setTimeout(function(){
			$('#curweight_flat').focus();
			$('#curweight_flat').val('');
			//重新称重
			flat_out();
			return;
		},200);
	}
}

function checkTracknumber(){
	var tracknumber		= $('#tracknumber').val();
	var orderid			= $('#orderid').val();
	var weight			= $('#curweight_register').val();
	var mailway			= $('#mailway_register').val();
	var keyCode 		= event.keyCode;
	var trackenumber_s = /^(00)\d+$/; //001205365709 
	var trackenumber_r = /^(LK|RA|RB|RC|RR|RF|EE|EC|LN|LM|RG|RX)\d{9}(CN|HK|DE200|SG|DE)$/;   //RA521491215CN 
	
	if (keyCode != 13) return false;
	
	if(orderid == ''){
		scanProcessTip('请先扫描订单号',false);
		//重新扫ebay id
		gotoScanStep('orderid');
		return false;
	}
	if(mailway===''){
		scanProcessTip('请先选择邮寄公司',false);
		return false;
	}
	if(orderid == tracknumber){
		scanProcessTip('条码错误,请重新录入',false);
		//重新扫tracknumber
		gotoScanStep('tracknumber');
		return false;
	}
	/*var len		= tracknumber.length;
	if(len <= 6){
		scanProcessTip('挂号条码错误,请重新录入',false);
		gotoScanStep('tracknumber');
		return false;
	}*/
	if(!trackenumber_s.test(tracknumber)&&!trackenumber_r.test(tracknumber)){
		scanProcessTip('挂号条码格式有误,请重新录入',false);
		gotoScanStep('tracknumber');
		return false;
	}
	scanProcessTip('<img src=./images/cx.gif />开始同步...',true);
	register_out_ajax(orderid,mailway,weight,tracknumber);
}

function register_out_ajax(orderid,channelId,orderweight,tracknumber){
	//var orderweight = window.document.app.currentweight;
	$.ajax({
		type:"POST",
		url:"json.php?act=orderWeighingRegister&mod=orderWeighing&jsonp=1",
		dataType:"json",
		data:{"orderid":orderid,"channelId":channelId,"orderweight":orderweight,"tracknumber":tracknumber},
		success:function(msg){
			if(msg.errCode==0){
				//初始化一些全局值
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip("扫描成功！",true);
				$('#orderid').focus();
				$('#orderid').val('');
				$('#tracknumber').val('');
				$('#curweight_flat').val('');					
				jSound.play({src:'./sounds/allok.wav',volume:100});
				gotoScanStep('orderid');
			}else if(msg.errCode== '013'){
				scanProcessTip(msg.errMsg,false);
				gotoScanStep('tracknumber');
			}else{
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip(msg.errMsg,false);
				gotoScanStep('orderid');
			}
		}
	});
}

function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			//jSound.play({src:'./sounds/success.wav',volume:100});
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			jSound.play({src:'./sounds/failure.mp3',volume:100});
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

//拍照
function take_photo(ebay_id){
	take_snapshot(ebay_id);
}

/*webcam.set_swf_url('./js/webcam.swf');
webcam.set_quality( 95 ); // JPEG quality (1 - 100)
webcam.set_shutter_sound( false ); // play shutter click sound
webcam.set_stealth( true );
document.write( webcam.get_html(0, 0, 600,400) );*/

function take_snapshot(ebay_id) {
	// take snapshot and upload to server
	//document.getElementById('results').innerHTML = '<h4>Uploading...</h4>';
	var now_time = document.getElementById('now_time').value;
	var url = './get_flashcam_order.php?action=save&oid='+ebay_id+'&fd=images/cz/'+now_time+',cz_'+ebay_id;
	webcam.set_api_url(url);
	webcam.snap();
}
webcam.set_hook( 'onComplete', 'my_completion_handler' );
function my_completion_handler(msg) {
	if (msg.match(/images.*/)) {
		var image_url = msg;//RegExp.$1;
		jSound.play({src:'./sounds/allok.wav',volume:100});
		show_scan_list();
		//jSound.play({src:'./sounds/allok.wav',volume:100});
		//webcam.reset();
	}else {
		now_ebayid = document.getElementById('ebay_id').value;
		document.getElementById('least_scan_id').innerHTML = '<a style="cursor:pointer" onclick="retake('+now_ebayid+')">重拍</a>';
		//alert("PHP Error: " + msg);
	}
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
	newhtml += '<div width="120px" style="float:left;"><span>订单号：'+ebay_ids[len-1]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" target="_blank"><image width="100px" src="images/cz/'+now_time+'/cz_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[len-1]+')">重拍</a></div>';
	if(len-1>0){
		if(len>5){
			for(var i=len-2;i>=len-5;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}else{
			for(var i=len-2;i>=0;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}
	}
	$('#least_scan_id').html(newhtml);
	show_picture.style.display = 'block';
	$('#show_picture').show();
	return false;
}

//重新拍照
function retake(bill){
	take_photo(bill);
}
