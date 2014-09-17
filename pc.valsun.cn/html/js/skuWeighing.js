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
            var ajaxSku = '';
			if(	sku==''){
				scanProcessTip("料号不能为空",false);
				gotoScanStep('sku');
				return false;
			}
            $.ajax({
        		type:"POST",
        		url:"json.php?mod=goods&act=isExistSku&jsonp=1",
        		dataType:"json",
        		data:{"goodsCode":sku},
        		success:function(msg){
                    //alert(msg.errCode);
                    //return;
        			if(msg.data == false){
        				scanProcessTip("料号不存在",false);
        				gotoScanStep('sku');
        				return false;
        			}else{
        				scanProcessTip('料号为 '+msg.data,true);
        			}
        		}
        	});	
			skuWeight();
		}
	});
});

//开始称重
function skuWeight(){
	gotoScanStep('curweight_flat');
	checkSkuWeight1();
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
    var sku = $("#sku").val();
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
	if(weight_p.test(currentweight_flat)){
		//scanProcessTip('重量录入成功',true);
		$('#curweight_flat').blur();
		if(sku == ''){
			scanProcessTip('请先扫描料号',false);
			gotoScanStep('sku');
			return false;
		}
		//开始同步
		scanProcessTip('开始同步...',true);
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
		url:"json.php?mod=goods&act=getNearestSkuWeight&jsonp=1",
		dataType:"json",
		data:{sku:sku},
		success:function(msg){
		    if(skuweight > 1000){
		      if(confirm("检测到该SKU重量大于1KG，是否确定同步该重量？")){
		          if(msg.errCode == 200){
        		       if(msg.data == 0){
                            if(confirm("检测到该SKU为新品，是否确定同步该重量？")){
                                sku_out_ajax_detail(msg.data,sku,skuweight);
                            }else{
                                scanProcessTip("请重新称重",false);
                            }        
                        }else{
                            sku_out_ajax_detail(msg.data,sku,skuweight);
                        }
        		    }else{
        		      scanProcessTip(msg.errMsg,false);
        		    }
		      }else{
		          scanProcessTip("请重新称重",false);
		      }
		    }else{
		      if(msg.errCode == 200){
    		       if(msg.data == 0){
                        if(confirm("检测到该SKU为新品，是否确定同步该重量？")){
                            sku_out_ajax_detail(msg.data,sku,skuweight);
                        }else{
                            scanProcessTip("请重新称重",false);
                        }        
                    }else{
                        sku_out_ajax_detail(msg.data,sku,skuweight);
                    }
    		    }else{
    		      scanProcessTip(msg.errMsg,false);
    		    }
		    }
		    
		}
	});   	
}

function sku_out_ajax_detail(skuNearWeight,sku,skuweight){
    $.ajax({
		type:"POST",
		url:"json.php?mod=goods&act=skuWeighing&jsonp=1",
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
				//jSound.play({src:'./sounds/allok.wav',volume:100});
				gotoScanStep('sku');
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

function inputWeightByHand(){
    var sku = $("#sku").val();
    var currentweight_flat = $('#curweight_flat').val();
    if(!$.trim(sku) || isNaN(currentweight_flat) || currentweight_flat < 1){
        scanProcessTip('输入为空或数据不合法，必须大于1g',false);
        return false;
    }
    scanProcessTip('开始同步...',true);
    sku_out_ajax(sku, currentweight_flat);
}
