/*
* 料号称重
* @author by hws
*/
var checkWeightIntval=0;
var orderweight=0;

$(document).ready(function(){
	$("#sku").focus();
	$("#sku").keydown(function(){
		var e = e||event;
		if(e.keyCode==13||e.keyCode==10){
			var sku = $.trim(this.value);
			if(	sku==''){
				scanProcessTip("料号不能为空",false);
				gotoScanStep('sku');
				return false;
			}	
			skuWeight();
		}
	});
});

//开始称重
function skuWeight(){
	var sku = $.trim($("#sku").val());
	$.ajax({
		type:"POST",
		url:"json.php?act=getImgBySku&mod=common&jsonp=1",
		dataType:"json",
		data:{"sku":sku},
		success:function(msg){
			if(msg.errCode==0){
				$("#sku").val(msg.errMsg);
				$("#imgshow").show();
				$("#imgb").attr("href",msg.data);
				$("#imgs").attr("src",msg.data);
				gotoScanStep('curweight_flat');
				checkSkuWeight1();
			}else{
				scanProcessTip(msg.errMsg,false);
			}
		}
	});
	//gotoScanStep('curweight_flat');
	//checkSkuWeight1();
}

function checkSkuWeight1(){
	checkWeightIntval=setInterval(function(){	
		orderweight=window.document.app.currentweight;
		//sometime return weight value 1 ,so must bigger than 5
		//if(orderweight>5){
			clearInterval(checkWeightIntval);
			//here must relay for accurate weight result
			setTimeout(function(){
				checkSkuWeight2();
			},400);
		//}
	},100);
}

function checkSkuWeight2(){
	var currentweight_flat=orderweight;
	//confirm weight
	var nowcurrweight=window.document.app.currentweight;
	if(nowcurrweight==0){
		checkSkuWeight1();
		return false;
	}else if(nowcurrweight!=currentweight_flat){
		checkSkuWeight1();
		return false;
	}
	try{
		orderweight=0;
		clearInterval(checkWeightIntval);
	}catch(e){}
	$('#curweight_flat').val(currentweight_flat);
	var weight_p=/^\d+$/;
	var sku = $.trim($("#sku").val());
	if(weight_p.test(currentweight_flat)){
		//scanProcessTip('重量录入成功',true);
		$('#curweight_flat').blur();
		if(sku == ''){
			scanProcessTip('请先扫描料号',false);
			gotoScanStep('sku');
			return false;
		}
		//开始同步
		scanProcessTip('<img src=./images/cx.gif />开始同步...',true);
		sku_out_ajax(sku,currentweight_flat);
	}else{
		scanProcessTip('重量录入失败,请重试',false);
		$('#curweight_flat').blur();		
		setTimeout(function(){
			$('#curweight_flat').focus();
			$('#curweight_flat').val('');
			//重新称重
			skuWeight();
			return;
		},200);
	}
}

function sku_out_ajax(sku,skuweight){
	$.ajax({
		type:"POST",
		url:"json.php?act=skuWeighing&mod=orderWeighing&jsonp=1",
		dataType:"json",
		data:{"sku":sku,"skuweight":skuweight},
		success:function(msg){
			if(msg.errCode==200){
				//初始化一些全局值
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip(msg.errMsg,true);
				$('#sku').focus();
				$('#sku').val('');
				$('#curweight_flat').val('');					
				jSound.play({src:'./sounds/allok.wav',volume:100});
				gotoScanStep('ebay_id');
                take_photo(sku);
			}else{
				try{
					orderweight=0;
					clearInterval(checkWeightIntval);
					checkWeightIntval=0;
				}catch(e){}
				scanProcessTip(msg.errMsg,false);
			}
		}
	});
}

function scanProcessTip(msg,yesorno){
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

/** ADD BY Gary 新增料号称重后订单同步更新操作**/
function updateOrderWeight(e, obj){
    if(e.keyCode==13||e.keyCode==10){
    	var ebay_id =   $(obj).val();
        if(ebay_id != ''){
            $.ajax({
        		type:"POST",
        		url:"json.php?act=orderWeight&mod=orderWeighing&jsonp=1",
        		dataType:"json",
        		data:{'ebay_id':ebay_id},
        		success:function(msg){
        			if(msg.errCode==200){
        				scanProcessTip(msg.errMsg,true);
        				$('#sku').focus();
                        $('#ebay_id').val('');
        				gotoScanStep('sku');
        			}else{
        				scanProcessTip(msg.errMsg,false);
        			}
        		}
        	});
        }else{
            $('#sku').focus();
        	gotoScanStep('sku');
        }
	}
}

/**料号拍照**/
//拍照
function take_photo(sku){
	take_snapshot(sku);
}
webcam.set_swf_url('./js/webcam.swf');
webcam.set_quality( 95 ); // JPEG quality (1 - 100)
webcam.set_shutter_sound( false ); // play shutter click sound
webcam.set_stealth( true );
document.write( webcam.get_html(0, 0, 600,400) );

function take_snapshot(sku) {
	// take snapshot and upload to server
	//document.getElementById('results').innerHTML = '<h4>Uploading...</h4>';
	var now_time = $('#now_time').val();
	var url = './get_flashcam_order.php?action=save&oid='+sku+'&fd=images/sku_photo/'+now_time+',sku_'+sku;
	webcam.set_api_url(url);
	webcam.snap();
}

webcam.set_hook( 'onComplete', 'my_completion_handler' );
function my_completion_handler(msg) {
    var sku = $('#sku').val();
	if (msg.match(/images.*/)) {
		var image_url = msg;//RegExp.$1;
		jSound.play({src:'./sounds/allok.wav',volume:100});
		//show_scan_list();
        document.getElementById('show_photo').innerHTML = "<img src= '"+msg+"' width='120px' height='100px'/><br /><a style='cursor:pointer' onclick='retake("+sku+")'>重拍</a>";
		//jSound.play({src:'./sounds/allok.wav',volume:100});
		//webcam.reset();
	}else {
		document.getElementById('show_photo').innerHTML = '<a style="cursor:pointer" onclick="retake("'+sku+'")">重拍</a>';
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
