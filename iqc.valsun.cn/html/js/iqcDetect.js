$(function(){

})

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
function check_sku(){
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
				if(msg.errCode==0){
					scanProcessTip(msg.data['info'],true);
					$('#nownum').val(msg.data['num']);
					$('#infoid').val(msg.data['id']);
					$('#category').focus();
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

//分类
function check_category(){
	var keyCode = event.keyCode;
	var cate   = $('#category').val();
	var sku = $.trim($('#sku').val());
	var num = $.trim($('#nownum').val());
	if(keyCode==13&&cate>0){
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
				}				
			}
		});
	}else{
		scanProcessTip1('请选好类别，回车！',true);
		$('#category').focus();
	}
}

//抽样标准
function sampling_show(datas){
	var sku = $.trim($('#sku').val());
	$('#cnum').val(datas[0]['sampleNum']);
	$('#renum').val(datas[0]['Re']);
	$('#acnum').val(datas[0]['Ac']);
	$('#alnum').val(datas[0]['Al']);
	$('#sampling_display').show();
	$('#sampling_display').html('');

	var newtable = '<table width="50%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">';
		newtable += '<tr align="center" class="0" >';
		newtable += ' <td colspan="6" align="center" bgcolor="#eeeeee"> ';
		newtable += '<span style="color:red;font-size:15px">抽样标准</span>';	
		newtable += '</td>';
		newtable += '<tr><th width="10%" bgcolor="#eeeeee" align="center">SKU</th><th width="10%" bgcolor="#eeeeee" align="center">抽样数</th><th width="10%" bgcolor="#eeeeee" align="center">允收数目（AC）</th><th width="10%" bgcolor="#eeeeee" align="center">拒收数目（RE）</th><th width="10%" bgcolor="#eeeeee" align="center">追加数（AL）</th><th width="10%" bgcolor="#eeeeee" align="center">退回百分比</th></tr>';
	
		newtable += '<tr align="center" class="0" >';
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> ';
		newtable += sku;	
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['sampleNum'];
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Ac'];
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Re'];
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Al'];
		newtable += '</td>';
		
		newtable += ' <td align="left" bgcolor="#FFFFFF" class="left_txt"> '
		newtable += datas[0]['Rt'];
		newtable += '</td>';
		
		newtable += '</tr>';
	newtable += '</table>';
	$('#sampling_display').html(newtable);
	
	$('#show_product_image').show();
	var img_str = '<a class="fancybox" href="http://192.168.200.200:9998/imgs/'+datas[0]['sku']+'-G.jpg" target="_blank"><img src="http://192.168.200.200:9998/imgs/'+datas[0]['sku']+'-G.jpg" width="300" height="300" style="border-style:solid;border-width:0;" /></a>';
	$('#show_product_image').html(img_str);		
}

//抽检数目
function checknum(){
	var check_num = $.trim($('#check_num').val());
	var cnum = parseInt($.trim($('#cnum').val()));
	var keyCode   = event.keyCode;
	if (keyCode!=13) return false;
	if (!test_number(check_num)||check_num==0){
		scanProcessTip2('抽检数量只能为大于0的数字!',false);
		$('#check_num').val('');
		$('#check_num').focus();
		return false;
	}else if(check_num<cnum){
		scanProcessTip2('抽检数量不能小于'+cnum+'!',false);
		$('#check_num').val('');
		$('#check_num').focus();
		return false;
	}else{
		$('#rejects_num').focus();
		$('#mstatus2').html('');
	}
	
}

//不良品数
function check_rejects_num(){
	var sku 		= $('#sku').val();
	var check_num   = $('#check_num').val();
	var rejects_num = $('#rejects_num').val();
	var cnum		= $('#cnum').val();
	var renum 		= $('#renum').val();
	var alnum 		= $('#alnum').val();
	var totalnum    = parseInt(cnum)+parseInt(alnum);
	var keyCode = event.keyCode;
	if (keyCode!=13) return false;
	if(event.ctrlKey&&event.keyCode==13){
		if (rejects_num==''||rejects_num==0){
			$('#rejects_num').val('0');
			checkcomplete();
		}
	}else{
		if (!test_number(rejects_num)){
			scanProcessTip3('不良品数量只能为数字!',false);
			$('#rejects_num').val('');
			$('#rejects_num').focus();
			return false;
		}else if(rejects_num>=renum && check_num<totalnum){
			scanProcessTip2('抽样数目需为'+totalnum+'!',false);
			$('#check_num').val('');
			$('#check_num').focus();
			return false;
		}else{
			$('#bad_reason').focus();
		}	
	}
}

//不良原因
function check_bad_reason(){	
	var rejects_num = $.trim($('#rejects_num').val());
	var bad_reason  = $.trim($('#bad_reason').val());
	if(event.ctrlKey&&event.keyCode==13){
		if(rejects_num>0&&bad_reason==''){
			alert('有不良品，需添加原因才能提交!');
			return false;
		}else{
			checkcomplete();
		}
	}
}

function checkcomplete(){	
	var bad_reason_rule = /^[\s\n]+$/g;
	var id 		    = $('#infoid').val();
	var num 		= $('#nownum').val();
	var sku 		= $.trim($('#sku').val());
	var check_num 	= $.trim($('#check_num').val());
	var category    = $.trim($('#category').val());
	var rejects_num = $.trim($('#rejects_num').val());
	var bad_reason  = $.trim($('#bad_reason').val());

	if(sku==''){
		scanProcessTip('请先输入料号!',false);
		$('#sku').focus();
		return false;
	}
	if(category==0){
		scanProcessTip1('请填写检测类别!',false);
		$('#category').focus();
		return false;
	}
	if (!test_number(check_num)||check_num==0){
		scanProcessTip2('抽检数量只能为大于0的数字!',false);
		$('#check_num').focus();
		return false;
	}
	if (!test_number(rejects_num)){
		scanProcessTip3('不良品数量只能为数字!',false);
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
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=iqcDetect&act=subcheck&jsonp=1',
			data	: {id:id,num:num,sku:sku,check_num:check_num,rejects_num:rejects_num,bad_reason:bad_reason},
			success	: function (msg){
				if(msg.errCode==0){
					scanProcessTip4(msg.errMsg,true);
					clearrewritetable();
				}else{
					alert(msg.errMsg);
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
	$('#sku').val('');
	$('#check_num').val('');
	$('#rejects_num').val('');
	$('#bad_reason').val('');
	$('#sampling_display').html('');
	$('#show_product_image').html('');
	$('#sku').focus();
}

//待定原因
function check_wait_reason(){	
	var bad_reason_rule = /^[\s\n]+$/g;
	var id 		    = $('#infoid').val();
	var num 		= $('#nownum').val();
	var sku 		= $.trim($('#sku').val());
	var wait_reason = $.trim($('#wait_reason').val());
	if(event.ctrlKey&&event.keyCode==13){
		if(wait_reason=='') {
			scanProcessTip4('待定原因不能为空',false);
			$('#wait_reason').focus();
			return false;
		}else{
			if(confirm('是否提交待定结果!')){
				$.ajax({
					type	: "POST",
					dataType: "jsonp",
					url		: 'json.php?mod=iqcDetect&act=allDetermined&jsonp=1',
					data	: {id:id,num:num,sku:sku,wait_reason:wait_reason},
					success	: function (msg){
						//console.log(msg);return false;
						if(msg.errCode==0){
							scanProcessTip4(msg.errMsg,true);
							$('.normal').show('');
							$('#trdet').hide();
							$('#mstatus').html('');
							$('#sku').val('');
							$('#sku').focus();
							$('#wait_reason').val('');
							$('#determined').attr('checked',false);
						}else{
							alert(msg.errMsg);
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