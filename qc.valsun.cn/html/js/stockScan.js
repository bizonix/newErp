/*
	* 库存不良品定期检测、不定时发现不良品检测程序
    * stockScan.js
    * add by chenwei 2013.12.11
 */

var detectStatus;
$(function(){
	$('#whSku').focus();
	detectStatus = 0;
});

/*
	* SKU检测提示区
 */
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
/*
	* 检测数量提示区
 */
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
/*
	* 不良品数量提示区
 */
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
/*
	* 不良品原因提示区
 */
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

/*
   * 扫描SKU，检测规格数据显示：
     	1.图片展示
		2.产品信息显示
		3.库存信息显示
		4.产品分类对照检测类别显示
   * ADD BY chenwei 2013.12.11
 */
function whSkuInfoShow(){
	var keyCode  = event.keyCode;
	if (keyCode != 13) return false;
	var sku      = $.trim($('#whSku').val());
	if(sku		!= ''){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=getWhSkuInfo&jsonp=1',
			data	: {sku:sku},
			success	: function (msg){
				if(msg.errCode==200){
					scanProcessTip(msg.data['info'],true);
					$('.picasagallery').picasagallery({username:'alan.hamlett',spu:msg.data['spu'],picType:'F'});
					$('#whNum').val(msg.data['whNum']);
					$('#reWhSku').val(msg.data['sku']);
					$('#reWhSpu').val(msg.data['spu']);
					$('#skuName').val(msg.data['skuName']);
					$('#sampleTypeId').val(msg.data['sampleTypeId']);
					$('#checkMethod').html(msg.data['sampleTypeStr']);
					//$('#nownum').val(msg.data['num']);
					
					
					$('#whCheckNum').focus();
				}else{
					alert(msg.errMsg);return false;
					scanProcessTip(msg.errMsg,false);
					//$('#nownum').val('');
					//$('#infoid').val('');
				}				
			}
		});
		
	}else{
		scanProcessTip('输入料号不能为空,请重新输入!',false);
		$('#sku').focus();
	}
}


/*
   * 填写检测数量：
     	1.格式验证：抽检数量只能为大于0的数字
		2.数据比较：检测数不能超过实际库存数量
		3.无不良品提交
 */
function whCheckNumFunction(){
	var whCheckNum = $.trim($('#whCheckNum').val());
	var whNum      = parseInt($('#whNum').val());
	var state 	   = $('#rejects').attr('checked');
	var keyCode    = event.keyCode;
	if (event.ctrlKey&&event.keyCode==13){
		if (!test_number(whCheckNum)||whCheckNum==0){
			scanProcessTip1('抽检数量只能为大于0的数字!',false);
			$('#whCheckNum').val('');
			$('#whCheckNum').focus();
			return false;
		}else if(whCheckNum>whNum){
			scanProcessTip1('库存：'+whNum+'，请填写正确检测数量！',false);
			$('#whCheckNum').val('');
			$('#whCheckNum').focus();
			return false;
		}else{
			if(state){
				alert("有不良品，请先填写数量！");
				$('#mstatus1').html('<br>');
				$('#rejectsNum').focus();
				return false;
			}else{
				$('#mstatus1').html('<br>');
				checkcomplete();
			}
		}	
	}else if(event.keyCode==13){
		if (!test_number(whCheckNum)||whCheckNum==0){
			scanProcessTip1('抽检数量只能为大于0的数字!',false);
			$('#whCheckNum').val('');
			$('#whCheckNum').focus();
			return false;
		}else if(whCheckNum>whNum){
			scanProcessTip1('库存：'+whNum+'，请填写正确检测数量！',false);
			$('#whCheckNum').val('');
			$('#whCheckNum').focus();
			return false;
		}else{
			$('#mstatus1').html('<br>');
			$('#rejects').attr('checked','checked');
			$('#rejectsInfo').show();
			$('#rejectsNum').focus();
		}
	}
}

/*
   * 是否有不良品选择：
 */
function rejects(){
	var state = $('#rejects').attr('checked');
	if(state){
		$('#rejectsInfo').show();
		$('#rejectsNum').focus();
	}else{
		$('#rejectsInfo').hide();
		$('#rejectsNum').focus();
	}
}

/*
   * 不良品数量填写：
   	  1.填写数字判断
	  
 */
function check_rejects_num(){
	var whCheckNum      = $.trim($('#whCheckNum').val());
	var rejectsNum      = $.trim($('#rejectsNum').val());
	var bad_reason  	= $.trim($('#badReason').val());
	var keyCode 	    = event.keyCode;
	if (keyCode!=13) return false;
	if(event.ctrlKey&&event.keyCode==13){
		if (!test_number(rejectsNum) || rejectsNum == 0){
			scanProcessTip2('不良品数量只能为大于0的数字!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}else if(rejectsNum>whCheckNum){
			scanProcessTip2('不良品数量不能大于抽样数!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}
		
		if(bad_reason == ''){
			scanProcessTip2('请选择不良原因↓',false);
			$('#badReason').focus();
		}else{
			$('#mstatus2').html('');
			checkcomplete();
		}				
	}else if(event.keyCode==13){
		if (!test_number(rejectsNum) || rejectsNum == 0){
			scanProcessTip2('不良品数量只能为大于0的数字!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}else if(rejectsNum>whCheckNum){
			scanProcessTip2('不良品数量不能大于抽样数!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}	
		
		if(bad_reason == ''){
			scanProcessTip2('请选择不良原因↓',false);
			$('#badReason').focus();
		}else{
			scanProcessTip2('请用CTRL+ENTER提交！',false);
			return false;
		}
	}
}

/*
   * 不良原因触发提交数据：
   	  1.提交判断
	  
 */
function check_bad_reason(){	
	var rejectsNum  = $.trim($('#rejectsNum').val());
	var bad_reason  = $.trim($('#badReason').val());

	if(rejectsNum==''){
		scanProcessTip3('请选择不良原因↑',false);
		$('#rejectsNum').focus();
		return false;
	}else if(bad_reason == ''){
		scanProcessTip3('请选择不良原因！',false);
		return false;
	}else{
		checkcomplete();
	}
	
}
/*
   * 完成检测：
   	  1.提交判断	  
 */
function checkcomplete(){	
	var sku 		= $.trim($('#whSku').val());
	var num 		= $('#whCheckNum').val();
	var reSku       = $('#reWhSku').val();
	var reNum 	    = $('#whNum').val();
	var reSpu 		= $.trim($('#reWhSpu').val());
	var skuName 	= $.trim($('#skuName').val());
	var sampleTypeId= $.trim($('#sampleTypeId').val());
	
	if(detectStatus==1){
		scanProcessTip('请不要重复提交！',false);
		return false;
	}	
	
	if(reSku==''){
		scanProcessTip('请先扫描料号或者输入回车！',false);
		$('#whSku').focus();
		return false;
	}
	
	if(reSpu==''){
		scanProcessTip('SPU有误，请确认！',false);
		$('#whSku').focus();
		return false;
	}
		
	if(reNum==''){
		scanProcessTip('库存不足！',false);
		$('#whSku').focus();
		return false;
	}
	
	if(sku==''){
		scanProcessTip('请先输入料号!',false);
		$('#whSku').focus();
		return false;
	}
	
	if(sku != reSku){
		scanProcessTip('请先扫描料号或者输入回车！',false);
		$('#whSku').focus();
		return false;
	}
	
	if (!test_number(num)||num==0){
		scanProcessTip1('抽检数量只能为大于0的数字!',false);
		$('#whCheckNum').focus();
		return false;
	}
	
	if(parseInt(num) > parseInt(reNum)){
		scanProcessTip1('抽检数量不能大于库存数量！',false);
		$('#whCheckNum').focus();
		return false;
	}
	
	var state 	   = $('#rejects').attr('checked');
	if(state){
		var rejectsNum 		= $.trim($('#rejectsNum').val());
		var bad_reason 		= $.trim($('#badReason').val());
		if(!test_number(rejectsNum) || rejectsNum == 0){
			scanProcessTip2('不良品数量只能为大于0的数字!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}
		
		if(rejectsNum>num){
			scanProcessTip2('不良品数量不能大于抽样数!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}
		
		if(bad_reason == ""){
			scanProcessTip2('请选择不良原因↓',false);
			$('#badReason').focus();
			return false;
		}
		
		if(confirm('是否确定要提交检测结果!')){
			detectStatus = 1;
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqcDetect&act=whRegularInspection&jsonp=1',
				data	: {type:1,sku:reSku,spu:reSpu,check_num:num,rejects_num:rejectsNum,bad_reason:bad_reason,skuName:skuName,reNum:reNum,sampleTypeId:sampleTypeId},
				success	: function (msg){
					if(msg.errCode==200){	
						clearrewritetable();											
						scanProcessTip(msg.errMsg,true);						
						detectStatus = 0;
					}else{
						alert(msg.errMsg);
						detectStatus = 0;
					}				
				}
			});
		}
		
	}else{
		if(confirm('是否确定要提交检测结果!')){
			detectStatus = 1;
			$.ajax({
				type	: "POST",
				dataType: "jsonp",
				url		: 'json.php?mod=iqcDetect&act=whRegularInspection&jsonp=1',
				data	: {type:2,sku:reSku,check_num:num,skuName:skuName,reNum:reNum,sampleTypeId:sampleTypeId},
				success	: function (msg){
					if(msg.errCode==200){		
						clearrewritetable();										
						scanProcessTip(msg.errMsg,true);						
						detectStatus = 0;
					}else{
						alert(msg.errMsg);
						detectStatus = 0;
					}				
				}
			});
		}
	}	
}  

function clearrewritetable(){	
	$('#mstatus').html('<br>');
	$('#mstatus1').html('<br>');
	$('#mstatus2').html('');
	$('#mstatus3').html('');
	$('#checkMethod').html('');
	$('#show_product_image').html('');
	$('#whSku').val('');
	$('#whCheckNum').val('');
	$('#rejectsNum').val('');
	$('#badReason').val('');
	$('#rejects').attr('checked',false);
	$('#rejectsInfo').hide();
	$('#whSku').focus();
}

function test_number(num){
	var teststr = /^\d+$/;
	return teststr.test(num);
}