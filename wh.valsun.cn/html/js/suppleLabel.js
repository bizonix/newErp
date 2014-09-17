var checkpass = 0;
$(document).ready(function(){
	$("#sku").focus();
	
	$("#checksku").click(function(){
		var skus = $.trim($('#sku').val());
		if(skus==''){
			alertify.error('请按格式输入sku及数量');
			$("#sku").focus();
			return;
		}
		$.ajax({
			type    : "POST",
			dataType: "jsonp",
			url     : "json.php?mod=pasteLabel&act=checkSku&jsonp=1",
			data	: {skus:skus},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					if(msg.data.res_errorsku==true){
						checkpass = 2;	
					}else{
						checkpass = 1;	
					}
					scanProcessTip(msg.errMsg, true);
					show_checkresult(msg.data.res_data);
				}else{
					alertify.error(msg.errMsg);
				}				
			}

		});
	});
	
	$("#PostSKU").click(function(){
		var skus = $.trim($('#sku').val());
		if(checkprint()==false)
		{
			return false;
		}
		if(skus==''){
			alertify.error('请按格式输入sku及数量');
			$("#sku").focus();
			return;
		}
		window.open("index.php?act=printBuLabelPrint&mod=printLabel&str="+skus);
	});
	

});

function show_checkresult(datas){
	var showdetail = document.getElementById('showdetail');
	showdetail.innerHTML = '';
	var newtable = '';
	newtable += '<table>';
	for(var i=0; i<datas.length; i++){
		if(datas[i].goodsName!=undefined){
			newtable += '<tr>';
			newtable += '<td><font size=2>'+datas[i].sku+'</font><td>';
			newtable += '<td><font size=2>'+datas[i].pName+'</font><td>';
			newtable += '<td>'+datas[i].goodsName+'<td>';
			newtable += '</tr>';
		}else{
			newtable += '<tr>';
			newtable += '<td><font color="red" size=2>'+datas[i].sku+'</font><td>';
			newtable += '<td colspan="2"><font color="red" size=2>该料号有误</font><td>';
			newtable += '</tr>';
		}
	}
	newtable += '</table>';
	showdetail.innerHTML = newtable;
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

function checkprint(){
	
	if(checkpass==0){
		alert('请先检查料号是否正确!');	
		return false
	}else if(checkpass==2){
		alert('所填料号有误!');	
		return false
	}else{
		checkpass = false;
		return true;
	}
}