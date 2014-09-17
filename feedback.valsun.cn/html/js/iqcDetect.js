var detectStatus;
$(function(){
	$('#sku').focus();
	detectStatus = 0;
	//$('#sku').select();
	/*$('#products').slides({
		preload: true,
		preloadImage: 'js/slides/loading.gif',
		effect: 'slide, fade',
		crossfade: true,
		slideSpeed: 200,
		fadeSpeed: 500,
		generateNextPrev: true,
		generatePagination: false
	});*/
});
function determined(){
	var state = $('#determined').attr('checked');
	if(state){
		$('#rewrite_type').val('');
		$('#trdet').show();
		$('#mainTable').hide();
		$('#checkNum').css("display","none");
		
	}else{
		$('#mainTable').show('');
		$('#trdet').hide();
		$('#checkNum').css("display","block");
	}
}

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

function scanProcessTip(msg,yesorno){
	try{
		var str;
		if(yesorno){
			//str="<font color='#33CC33'>"+msg+"</font>";
			alertify.success(msg);
			//$('#mstatus').html(str);
		}else{
			//str="<font color='#FF0000'>"+msg+"</font>";
			alertify.error(msg);
			//$('#mstatus').html(str);
		}
		
	}catch(e){}
}

/*function scanProcessTip1(msg,yesorno){
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
			$('#categoryInfo').html(str);
		}
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
}*/

//扫描sku检测
function iqcScanSkuInfo(){
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	clearrewritetable2();
	var sku = $.trim($('#sku').val());
	if(sku!=''){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=getSkuInfo&jsonp=1',
			data	: {sku:sku},
			success	: function (msg){
			//console.log(msg.data.images.size);
			//console.log(msg.data['info']);return false;
			if(msg.errCode==200){
				$("#detactTable").css("display","block");
				$("#all_wait").css("display","block");
				
				var str = "<font color='#33CC33'>"+msg.data['info']+"</font>";
				$('#basicInfo').html(str);
				//console.log(msg.data['categoryInfo']);
				//console.log(msg.data['category']);
				str = show_select_lists(msg.data['categoryInfo'],msg.data['category']);
				//str="<font color='#FF0000'>"+msg.data['category']+"</font>";
				//$('#categoryInfo').show();
				$('#categoryInfo').html(str);
				$('#infoid').val(msg.data['infoid']);
				$("#checkNum").css("display","block");
				$('#nownum').val(msg.data['num']);
				$('#spu').val(msg.data['spu']);
				$('#sku').val(msg.data['sku']);
				//$('#mstatus').html(msg.data['sku']);
				$('.picasagallery').picasagallery({username:'alan.hamlett',spu:msg.data['spu'],picType:'F'});
				//show_image_lists(msg.data['images']);
				//抽样标准显示
				sampling_show(msg.data['cate']);	
				/*$('#sampling_display').html('');
				var newtable = '<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">';
				newtable += '<tr align="center" class="0" >';
				newtable += ' <td colspan="6" align="center" bgcolor="#eeeeee"> ';
				newtable += '<span style="color:red;font-size:15px">抽样标准</span>';	
				newtable += '</td>';
				newtable += '<tr><th width="10%" bgcolor="#eeeeee" align="center">SKU</th><th width="10%" bgcolor="#eeeeee" align="center">抽样数</th><th width="10%" bgcolor="#eeeeee" align="center">允收数目（AC）</th><th width="10%" bgcolor="#eeeeee" align="center">拒收数目（RE）</th></tr>';
				
				newtable += '<tr align="center" class="0" >';
				newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> ';
				newtable += msg.data['sku'];	
				newtable += '</td>';
				
				newtable += ' <td id="check_num" align="left" bgcolor="#FFFFFF" class="left_txt">'
				newtable += msg.data['cate'][0]['sampleNum'];
				newtable += '</td>';
				
				newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
				newtable += msg.data['cate'][0]['Ac'];
				newtable += '</td>';
				
				newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
				newtable += msg.data['cate'][0]['Re'];
				newtable += '</td>';
				newtable += '</tr>';
				newtable += '</table>';
				$('#sampling_display').html(newtable);*/
				$('#selectNum').focus();
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

function show_image_lists(images){
	var images_str = '<div id="products"><div class="slides_container">';
	for(var i in images){
		images_str += '<img src='+images[i]+' width="318" alt="1144953 '+i+' 2x">';
		/*<a href="javascript:void(0)" target="_blank"><img src="img/1144953-1-2x.jpg" width="366" alt="1144953 1 2x"></a>
		<a href="javascript:void(0)" target="_blank"><img src="img/1144953-2-2x.jpg" width="366" alt="1144953 2 2x"></a>
		<a href="javascript:void(0)" target="_blank"><img src="img/1144953-4-2x.jpg" width="366" alt="1144953 4 2x"></a>
		<a href="javascript:void(0)" target="_blank"><img src="img/1144953-5-2x.jpg" width="366" alt="1144953 5 2x"></a>
		<a href="javascript:void(0)" target="_blank"><img src="img/1144953-6-2x.jpg" width="366" alt="1144953 6 2x"></a>
		<a href="javascript:void(0)" target="_blank"><img src="img/1144953-p-2x.jpg" width="366" alt="1144953 P 2x"></a>*/
	}
	images_str += '</div><ul class="pagination">';
	for(var i in images){
		images_str += '<li><img src='+images[i]+' width="125" alt="1144953 '+i+' 2x"></li>';
		/*<li><a href="javascript:void(0)"><img src="img/1144953-3-2x.jpg" width="55" alt="1144953 3 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-1-2x.jpg" width="55" alt="1144953 1 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-2-2x.jpg" width="55" alt="1144953 2 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-4-2x.jpg" width="55" alt="1144953 4 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-5-2x.jpg" width="55" alt="1144953 5 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-6-2x.jpg" width="55" alt="1144953 6 2x"></a></li>
		<li><a href="javascript:void(0)"><img src="img/1144953-p-2x.jpg" width="55" alt="1144953 P 2x"></a></li>*/
	}
	images_str += '</ul></div>';
	$('#show_product_image').html(images_str);	
	$('#show_product_image').show();
}

function show_select_lists(data,selectId){
	//var showstr = '<select id="categoryInfo" name="categoryInfo">';
	var showstr = "";
	showstr += '<option value="">请选择</option>';
	for(var i in data){
		//console.log(i+"======="+data[i]);
		if(i == selectId){
			showstr += '<option value="'+i+'" selected="selected">'+data[i]+'</option>';
		}else{
			showstr += '<option value="'+i+'">'+data[i]+'</option>';	
		}
		//alert(i+"===="+data[i]);
	}
	return showstr;
	//alert(data);
	//alert(selectId);
}

//分类
function check_category(){
	var keyCode = event.keyCode;
	var cate   = $('#categoryInfo').val();
	var sku = $.trim($('#sku').val());
	var num = $.trim($('#nownum').val());

	$('#mstatus1').html('');
	$.ajax({
		type	: "POST",
		dataType: "jsonp",
		url		: 'json.php?mod=iqcDetect&act=getTypeInfo&jsonp=1',
		data	: {cate:cate,num:num,sku:sku},
		success	: function (msg){
		//console.log(msg);return false;
			if(msg.errCode==0){
				sampling_show(msg.data);//抽样标准
				$('#check_num').focus();
			}else{
				scanProcessTip(msg.errMsg,false);
				sampling_show(msg.data);
			}				
		}
	});

}

//抽样标准显示
function sampling_show(datas){
	$('#sampling_display').html('');
	if(datas == ''){
		$('#sampling_display').html('<font color="#FF0000">没有抽样执行标准！</font>');
		
		return;	
	}
	var num 		= parseInt($('#nownum').val());   //来货数
	if(num<datas[0]['sampleNum']){
		$('#sampling_display').html('<font color="#FF0000">来货数小于抽样标准数量，没有抽样执行标准！请全检！</font>');
		
		return;	
	}
	
	var info = document.getElementById("categoryInfo");
	info = info.options[info.selectedIndex].text;
	//alert(info);
	var sku = $.trim($('#sku').val());
	
	//$('#cnum').val(datas[0]['sampleNum']);
	//$('#acnum').val(datas[0]['Ac']);//允收数
	//$('#renum').val(datas[0]['Re']);//拒绝数量
	//$('#alnum').val(datas[0]['Al']);//追加数量
	//$('#rtnum').val(datas[0]['Rt']);//检测退回抽检百分比
	//$('#sampling_display').show();
	//$('#sampling_display').html('');

	var newtable = '<table width="50%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">';
		newtable += '<tr align="center" class="0" >';
		newtable += ' <td colspan="6" align="center" bgcolor="#eeeeee"> ';
		newtable += '<span style="color:red;font-size:15px">'+info+'抽样标准</span>';	
		newtable += '</td>';
		newtable += '<tr><th width="10%" bgcolor="#eeeeee" align="center">SKU</th><th width="10%" bgcolor="#eeeeee" align="center">抽样数</th><th width="10%" bgcolor="#eeeeee" align="center">允收数目（AC）</th><th width="10%" bgcolor="#eeeeee" align="center">拒收数目（RE）</th><th width="10%" bgcolor="#eeeeee" align="center">追加数（AL）</th><th width="10%" bgcolor="#eeeeee" align="center">退回百分比</th></tr>';
	
		newtable += '<tr align="center" class="0" >';
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> ';
		newtable += sku;	
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['sampleNum'];
		newtable += ' <input type="hidden" id="cnum" value="'+datas[0]['sampleNum']+'" /> ';
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Ac'];
		newtable += ' <input type="hidden" id="acnum" value="'+datas[0]['Ac']+'" /> ';
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Re'];
		newtable += ' <input type="hidden" id="renum" value="'+datas[0]['Re']+'" /> ';
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Al'];
		newtable += ' <input type="hidden" id="alnum" value="'+datas[0]['Al']+'" /> ';
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Rt'];
		newtable += ' <input type="hidden" id="rtnum" value="'+datas[0]['Rt']+'" /> ';
		newtable += '</td>';
		
		newtable += '</tr>';
	newtable += '</table>';
	$('#sampling_display').html(newtable);
	
	/*$('#show_product_image').show();
	var img_str = '<a class="fancybox" href="http://192.168.200.200:9998/imgs/'+datas[0]['sku']+'-G.jpg" target="_blank"><img src="http://192.168.200.200:9998/imgs/'+datas[0]['sku']+'-G.jpg" width="300" height="300" style="border-style:solid;border-width:0;" /></a>';
	$('#show_product_image').html(img_str);	*/	
}

//抽检数目
function checknum(){
	var check_num = $.trim($('#check_num').val());
	var cnum = parseInt($.trim($('#cnum').val()));
	var keyCode   = event.keyCode;
	if (keyCode!=13) return false;
	if (!test_number(check_num)||check_num==0){
		scanProcessTip('抽检数量只能为大于0的数字!',false);
		$('#check_num').val('');
		$('#check_num').focus();
		return false;
	}else if(check_num<cnum){
		scanProcessTip('抽检数量不能小于'+cnum+'!',false);
		$('#check_num').val('');
		$('#check_num').focus();
		return false;
	}else{
		$('#rejectsNum').focus();
		$('#mstatus2').html('');
	}
	
}

//不良品数
function check_rejects_num(){
	var sku 		= $('#sku').val();
	var rejects_num = $('#rejectsNum').val();
	var cnum		= $('#cnum').val();
	var renum 		= parseInt($('#renum').val());
	var alnum 		= $('#alnum').val();
	var totalnum    = parseInt(cnum)+parseInt(alnum);
	var check_num	= $('#check_num').text();
	//alert(check_num);
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(event.ctrlKey&&event.keyCode==13){
		if (rejects_num==''||rejects_num==0){
			$('#rejectsNum').val('0');
			checkcomplete();
		}
	}else{
		if (!test_number(rejects_num)){
			scanProcessTip('不良品数量只能为数字!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
			return false;
		}else if(rejects_num>=renum && check_num<totalnum){
			scanProcessTip('抽样数目需为'+totalnum+'!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else if(rejects_num>check_num){
			scanProcessTip('不良品数量不能大于抽样数!',false);
			$('#rejectsNum').val('');
			$('#rejectsNum').focus();
		}else{
			$('#badReason').focus();
		}	
	}
}

//不良原因
function check_bad_reason(){
	var rejects_num = $.trim($('#rejectsNum').val());
	var bad_reason  = $.trim($('#badReason').val());
	var selectNum = $.trim($("#selectNum").val());
	//alert(event.ctrlKey);
	//alert(event.keyCode);

	if(isNaN(selectNum)||isNaN(rejects_num) || rejects_num<=0 || selectNum<=0){
		$("#badReason").val("");
		scanProcessTip('抽样数目和不良品数必须是大于零数字!',false);
		return false;
	}
	if(rejects_num>=0&&bad_reason==''){
		$("#badReason").val("");
		scanProcessTip('有不良品，需选择原因才能提交!',false);
		return false;
	}else{
		checkcomplete();
	}

}

function checkcomplete(){
	var bad_reason_rule = /^[\s\n]+$/g;
	var id 		    = $('#infoid').val();
	var num 		= parseInt($('#nownum').val());
	var rtnum 		= $('#rtnum').val();
	var cnum 		= parseInt($('#cnum').val());
	var sku 		= $.trim($('#sku').val());
	var spu 		= $.trim($('#spu').val());
	//var check_num 	= parseInt($.trim($('#check_num').text()));
	//var category    = $.trim($('#category').val());
	var rejects_num = $.trim($('#rejectsNum').val());
	var bad_reason  = $.trim($('#badReason').val());
	var categoryId  = $.trim($('#categoryInfo').val());
	var check_num  	= parseInt($.trim($("#selectNum").val()));
	if(detectStatus == 1){
		scanProcessTip('请不要重复提交！',false);
		return false;
	}
	if(id=='' || num==''){
		scanProcessTip('请先扫描sku或输入回车',false);
		$('#sku').focus();
		return false;
	}
	if(sku==''){
		scanProcessTip('请先输入料号!',false);
		$('#sku').focus();
		return false;
	}
	/*if(category==0){
		scanProcessTip('请填写检测类别!',false);
		$('#category').focus();
		return false;
	}*/
	//alert(check_num);
	if (!test_number(check_num)||check_num==0){
		scanProcessTip('抽检数量只能为大于0的数字!',false);
		//$('#check_num').focus();
		return false;
	}	
	if (check_num>num){
		scanProcessTip('抽检数量不能大于'+num,false);
		//$('#check_num').focus();
		return false;
	}	
	var state = $('#rejects').attr('checked');
	if(state){
		if (!test_number(rejects_num)){
			scanProcessTip('不良品数量只能为数字!',false);
			$('#rejectsNum').focus();
			//$('#rejectsNum').select();
			return false;
		}
	}
	if (rejects_num>num){
		scanProcessTip('不良品数量不能大于'+num,false);
		$('#rejectsNum').focus();
		//$('#rejectsNum').select();
		return false;
	}
	if (rejects_num>check_num){
		scanProcessTip('不良品数量不能大于抽样数',false);
		$('#rejectsNum').focus();
		//$('#rejectsNum').select();
		return false;
	}
	if(bad_reason_rule.test(bad_reason)) {
		bad_reason = '';
	}
	
	if (rejects_num>0&&bad_reason==''){
		scanProcessTip('有不良品，必须提交不良原因!',false);
		$('#badReason').focus();
		$('#badReason').select();
		return false;
	}
	if(confirm("是否提交检测结果!")){
		scanProcessTip('数据提交成功，请等待。。。。',true);
		detectStatus = 1;
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=subcheck&jsonp=1',
			data	: {id:id,num:num,sku:sku,spu:spu,check_num:check_num,rejects_num:rejects_num,bad_reason:bad_reason,rtnum:rtnum,checkTypeID:1,categoryId:categoryId},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					
					/*if(rejects_num>0){
						if(msg.data['msg']['errocode'] != 200){
							console.log(msg.data['msg']['msg']);
							alertify.error("qc检测不良品扣除ERP库存失败！请联系IT解决！");
						}
					}
					$.ajax({
						type	: "GET",
						dataType: "jsonp",
						async	: true,
						timeout	: 60000,
						url		: 'index.php?mod=notice&act=sendMessage',
						data	: {content:msg.data['content'],from:msg.data['from'],to:msg.data['to'],type:msg.data['type'],callback:msg.data['callback']},
						success	: function (info){
							//info = eval("("+info+")");
							//alert(info.errCode);
							if(info.errCode=="2013"){
								scanProcessTip("已成功发送邮件给"+msg.data['to'],true);
							}else{
								scanProcessTip("发送邮件给"+msg.data['to']+"失败",false);
							}
							
						}
					});*/
					scanProcessTip(msg.errMsg,true);
					clearrewritetable();
					detectStatus = 0;
				}else{
					scanProcessTip(msg.errMsg,false);
					//alert(msg.errMsg);
					detectStatus = 0;
				}				
			}
		});
	}else{
		scanProcessTip('您选择放弃提交',false);
	}
	return;
}  

function clearrewritetable(){	
	$('#mstatus').html('');
	$('#mstatus1').html('');
	$('#mstatus2').html('');
	$('#mstatus3').html('');
	$('#sku').val('');
	//$('#category').val('');
	$('#checkNum').val('');
	$('#selectNum').val('');
	$('#rejectsNum').val('');
	$('#badReason').val('');
	$('#sampling_display').html('');
	$('#show_product_image').html('');
	$('.picasagallery').html('');
	$("#detactTable").css("display","none");
	$("#all_wait").css("display","none");
	$("#checkNum").css("display","none");
	$('#sku').focus();
	
	$('#cnum').val('');//抽样数
	$('#acnum').val('');//允收数
	$('#renum').val('');//拒绝数量
	$('#alnum').val('');//追加数量
	$('#rtnum').val('');//检测退回抽检百分比
}

function clearrewritetable2(){
	$('#mstatus').html('');
	$('#mstatus1').html('');
	$('#mstatus2').html('');
	$('#mstatus3').html('');
	//$('#category').val('');
	$('#checkNum').val('');
	$('#selectNum').val('');
	$('#rejectsNum').val('');
	$('#badReason').val('');
	$('#sampling_display').html('');
	$('#show_product_image').html('');
	$('.picasagallery').html('');
	$("#detactTable").css("display","none");
	$("#all_wait").css("display","none");
	$("#checkNum").css("display","none");
	$('#cnum').val('');//抽样数
	$('#acnum').val('');//允收数
	$('#renum').val('');//拒绝数量
	$('#alnum').val('');//追加数量
	$('#rtnum').val('');//检测退回抽检百分比
}

//待定原因
function check_wait_reason(){	
	var bad_reason_rule = /^[\s\n]+$/g;
	var id 		    = $('#infoid').val();
	var num 		= $('#nownum').val();
	var rewrite_type= $('#rewrite_type').val();
	var sku 		= $.trim($('#sku').val());
	var spu 		= $.trim($('#spu').val());
	var wait_reason = $.trim($('#wait_reason').val());
	//alert(spu);
	if(detectStatus == 1){
		scanProcessTip('请不要重复提交！',false);
		return false;
	}
	if(event.ctrlKey&&event.keyCode==13){
		if(id=='' || num==''){
			scanProcessTip('请先扫描sku或输入回车',false);
			$('#sku').focus();
			return false;
		}
		if(wait_reason=='') {
			scanProcessTip('待定原因不能为空',false);
			$('#wait_reason').focus();
			return false;
		}else{
			if(confirm('是否提交待定结果!')){
				detectStatus = 1;
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=iqcDetect&act=allDetermined&jsonp=1',
					data	: {id:id,num:num,sku:sku,spu:spu,rewrite_type:rewrite_type,wait_reason:wait_reason},
					success	: function (msg){
						//console.log(msg);return false;
						if(msg.errCode==0){
							//alert(msg.data);
							/*$.ajax({
								type	: "GET",
								dataType: "jsonp",
								async	: true,
								timeout	: 60000,
								url		: 'index.php?mod=notice&act=sendMessage',
								data	: {content:msg.data['content'],from:msg.data['from'],to:msg.data['to'],type:msg.data['type'],callback:msg.data['callback']},
								success	: function (info){
									//info = eval("("+info+")");
									//alert(info.errCode);
									if(info.errCode=="2013"){
										scanProcessTip("已成功发送邮件给"+msg.data['to'],true);
									}else{
										scanProcessTip("发送邮件给"+msg.data['to']+"失败",false);
									}
									
								}
							});*/
							scanProcessTip(msg.errMsg,true);
							$('.normal').show('');
							$('#trdet').hide();
							$('#mstatus').html('');
							$('#show_product_image').html('');
							$("#all_wait").css("display","none");
	
							$('#sku').val('');
							$('#sku').focus();
							$('#wait_reason').val('');
							$('#determined').attr('checked',false);
							detectStatus = 0;
						}else{
							scanProcessTip(msg.errMsg,false);
							detectStatus = 0;
							//alert(msg.errMsg);
						}				
					}
				});
			}
		}
	}	
}

function test_number(num){
	var teststr = /^\d+$/;
	return teststr.test(num);
}

function submitall(){
	var keyCode   = event.keyCode;
	if(event.ctrlKey&&event.keyCode==13){
		var state = $('#rejects').attr('checked');
		if(state){
			alertify.error("您选择了有不良品选择框，请在不良品原因文本域里提交！");
			return false;
		}
		checkcomplete();
	}
}
//待定下选择是否修改图片
