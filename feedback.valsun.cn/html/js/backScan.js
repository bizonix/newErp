var detectStatus;
$(function(){
	detectStatus = 0;
});

function determined(){
	var state = $('#determined').attr('checked');
	if(state){
		$('#trdet').show();
		$('.normal').hide();
	}else{
		$('.normal').show('');
		$('#trdet').hide();
	}
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

function scanProcessTip1(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus1').html(str);
	}catch(e){}
}

function scanProcessTip2(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus2').html(str);
	}catch(e){}
}

function scanProcessTip3(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mstatus3').html(str);
	}catch(e){}
}

function scanProcessTip4(msg,yesorno){
	try{
		var str;
		if(yesorno){
			str="<font color='#33CC33'>"+msg+"</font>";
		}else{
			str="<font color='#FF0000'>"+msg+"</font>";
		}
		$('#mess').html(str);
	}catch(e){}
}

//扫描sku
function checksku(){
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	var sku = $.trim($('#sku').val());
	if(sku!=''){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=getSkuInfo&jsonp=1',
			data	: {sku:sku},
			success	: function (msg){
			//console.log(msg.data['info']);return false;
			    //var msg = eval("("+msg+")");
				if(msg.errCode==200){
					//alert("sgddf");
					scanProcessTip(msg.data['info'],true);
					$('.picasagallery').picasagallery({username:'alan.hamlett',spu:msg.data['spu'],picType:'F'});
					$('#infoid').val(msg.data['infoid']);
					//$("#checkNum").css("display","block");
					$('#nownum').val(msg.data['num']);
					$('#sku').val(msg.data['sku']);
					$('#spu').val(msg.data['spu']);
					$('#check_num').focus();
				}else{
					scanProcessTip(msg.errMsg,false);
					$('#nownum').val('');
					$('#infoid').val('');
				}				
			}
		});
		
	}else{
		scanProcessTip('输入料号不能为空,请重新输入!',false);
		$('#sku').focus();
	}
}

//抽检数目
function checknum(){
	var check_num = $.trim($('#check_num').val());
	var cnum     = parseInt($('#nownum').val());
	var keyCode   = event.keyCode;
	if (event.ctrlKey&&event.keyCode==13){
		if (!test_number(check_num)||check_num==0){
			scanProcessTip1('抽检数量只能为大于0的数字!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else if(check_num!=cnum){
			scanProcessTip1('抽检数量应为'+cnum+'!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else{
			$('#rejects_num').focus();
			$('#mstatus1').html('');
		}
		checkcomplete();
	}else if(event.keyCode==13){
		if (!test_number(check_num)||check_num==0){
			scanProcessTip1('抽检数量只能为大于0的数字!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else if(check_num!=cnum){
			scanProcessTip1('抽检数量应为'+cnum+'!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else{
			$('#rejects_num').focus();
			$('#mstatus1').html('');
		}
	}
}

//不良品数
function check_rejects_num(){
	var sku 		= $('#sku').val();
	var check_num   = $('#check_num').val();
	var rejects_num = $('#rejects_num').val();
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(event.ctrlKey&&event.keyCode==13){
		if (rejects_num==''||rejects_num==0){
			$('#rejects_num').val('0');
			checkcomplete();
		}
	}else{
		if (!test_number(rejects_num)){
			scanProcessTip2('不良品数量只能为数字!',false);
			$('#rejects_num').val('');
			$('#rejects_num').focus();
			return false;
		}else if(rejects_num>check_num){
			scanProcessTip2('不良品数量不能大于抽样数!',false);
			$('#rejects_num').val('');
			$('#rejects_num').focus();
			return false;
		}else{
			$('#bad_reason').focus();
			$('#mstatus2').html('');
		}	
	}
}

//不良原因
function check_bad_reason(){	
	var rejects_num = $.trim($('#rejects_num').val());
	var bad_reason  = $.trim($('#bad_reason').val());

	if(rejects_num>0&&bad_reason==''){
		$('#bad_reason').val("");
		scanProcessTip3('有不良品，需添加原因才能提交!',false);
		return false;
	}else{
		checkcomplete();
	}
	
}

function checkcomplete(){	
	var bad_reason_rule = /^[\s\n]+$/g;
	var typeid      = $('#typeid').val();
	var id 		    = $('#infoid').val();
	var num 		= $('#nownum').val();
	var sku 		= $.trim($('#sku').val());
	var spu 		= $.trim($('#spu').val());
	var check_num 	= $.trim($('#check_num').val());
	check_num 		= parseInt(check_num);
	var rejects_num = $.trim($('#rejects_num').val());
	rejects_num 		= parseInt(rejects_num);
	var bad_reason  = $.trim($('#bad_reason').val());
	var checkTypeID = $.trim($('#checkTypeID').val());
	if(detectStatus==1){
		scanProcessTip('请不要重复提交！',false);
		return false;
	}	
	//alert(spu);
	if(id=='' || num==''){
		scanProcessTip('请先扫描料号或者输入回车!',false);
		$('#sku').focus();
		$('#check_num').val('');
		$('#rejects_num').val('');
		$('#bad_reason').val('');
		return false;
	}
	
	if(sku==''){
		scanProcessTip('请先输入料号!',false);
		$('#sku').focus();
		return false;
	}
	if (!test_number(check_num)||check_num==0){
		scanProcessTip1('抽检数量只能为大于0的数字!',false);
		$('#check_num').focus();
		return false;
	}
	if(check_num!=num){
		scanProcessTip1('抽检数量应为'+num+'!',false);
		$('#check_num').val('');
		$('#check_num').focus();
		return false;
	}

	if(rejects_num>check_num){
		scanProcessTip2('不良品数量不能大于抽样数!',false);
		$('#rejects_num').val('');
		$('#rejects_num').focus();
		return false;
	}
	if (rejects_num>num){
		scanProcessTip2('不良品数量不能大于'+num,false);
		$('#rejects_num').focus();
		return false;
	}
	if(bad_reason_rule.test(bad_reason)) {
		bad_reason = '';
	}

	if (rejects_num>0&&bad_reason==''){
		scanProcessTip3('有不良品，必须提交不良原因!',false);
		$('#bad_reason').focus();
		return false;
	}
	if(confirm('是否提交检测结果!')){
		detectStatus = 1;
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=otherCheck&jsonp=1',
			data	: {typeid:typeid,checkTypeID:checkTypeID,id:id,num:num,sku:sku,spu:spu,check_num:check_num,rejects_num:rejects_num,bad_reason:bad_reason},
			success	: function (msg){
				if(msg.errCode==0){
					if(rejects_num>0){
						if(checkTypeID == '3' && msg.data['errocode'] != 200){
							console.log(msg.data['msg']);
							alertify.error("qc检测不良品扣除ERP库存失败！请联系IT解决！");
						}
					}
					scanProcessTip4(msg.errMsg,true);
					clearrewritetable();
					detectStatus = 0;
				}else{
					alert(msg.errMsg);
					detectStatus = 0;
				}				
			}
		});
	}
	
}  

function clearrewritetable(){	
	$('#mstatus').html('');
	$('#mstatus1').html('');
	$('#mstatus2').html('');
	$('#mstatus3').html('');
	$('#show_product_image').html('');
	$('#sku').val('');
	$('#check_num').val('');
	$('#rejects_num').val('');
	$('#bad_reason').val('');
	$('#sku').focus();
}

function test_number(num){
	var teststr = /^\d+$/;
	return teststr.test(num);
}