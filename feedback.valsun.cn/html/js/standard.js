$(function(){
	$("#addstandard").validationEngine({autoHidePrompt:true});
	
	//修改检测样品标准
	$('.mod').click(function(){
		id = $(this).attr('tid');
		window.location.href = "index.php?mod=nowSampleStandard&act=editSampleType&id="+id;
		return false;
	});

	//返回
	$("#back").click(function(){
		history.back();
	});
	
	//新增标准
	$("#addStandard").click(function(){
		window.location.href = "index.php?mod=nowSampleStandard&act=addSampleType";
		return false;
	});
	
	//搜索
	$('#seachstard').click(function(){
		var sName  = $("#sName").val();
		var typeId = $("#typeId").val();

		location.href = "index.php?mod=nowSampleStandard&act=sampleStandardList&sName="+sName+"&typeId="+typeId;
	});
	
	//开启标准页面
	$('#openStandard').click(function(){
		location.href = "index.php?mod=nowSampleStandard&act=openSampleType";
	});
	
	//开启标准
	$('.openst').click(function(){
		var bool = false;
		var id = $(this).val();		
		var name = $("input[name=st"+id+"]:radio:checked").val();
		for(var i=0;i<$("input[name=st"+id+"]").length;i++){  
			if($("input[name=st"+id+"]")[i].checked==true)
			bool=true; 
		}  
		if(!bool){
			//alert('请选择要开启的类别!');return false;
			$('#mess').html('<span style="color:red;font-size:20px">-请选择要开启的类别-<span>');
			return false;
		}else{
			$.ajax({
				type	: "POST",
				dataType: "json",
				url		: 'json.php?mod=detectStandard&act=openStandard&jsonp=1',
				data	: {id:id,name:name},
				success	: function (msg){
					//console.log(msg);return false;
					
					if(msg.errCode==0){
						//alert('开启成功');
						alertify.success('开启成功');
						//$('#mess').html('<span style="color:red;font-size:20px">-开启成功-<span>');
					}else{
						alertify.error(msg.errMsg);
					}				
				}
			});
		}
	});
	
	//更新现在所有标准
	$('#updatast').click(function(){
		$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=detectStandard&act=getnowStandard&jsonp=1',
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					//alert("更新成功！");
					window.location.href = "index.php?mod=nowSampleStandard&act=nowSampleType&state=更新成功";
				}else{
					alert("更新失败，请重试！");
				}				
			}
		});

	});
	

})

//检测类别联动
function change_standard(){
	var typeId = $("#typeId").val();
	if(typeId==0){
		$('#standardname').html('');
		var newtab = '';
		newtab +="<label for='typeId'>标准名称：</label>";
		newtab +="<select name='sName' id='sName' >";
		newtab +="<option value=''>请选择</option>";
		newtab +="</select>";
		$("#standardname").html(newtab);
		return false;
	}
	$.ajax({
			type	: "POST",
			dataType: "jsonp",
			url		: 'json.php?mod=detectStandard&act=getStandardInfo&jsonp=1',
			data	: {id:typeId},
			success	: function (msg){
				//console.log(msg);return false;
				if(msg.errCode==0){
					$('#standardname').html('');
					var len = msg.data.length;
					var newtab = '';
					newtab +="<label for='typeId'>标准名称：</label>";
					newtab +="<select name='sName' id='sName' >";
					newtab +="<option value=''>请选择</option>";
					for(var i=0;i<len;i++){
						newtab +="<option value='"+msg.data[i].sName+"'>"+msg.data[i].sName+"</option>";
					}
					newtab +="</select>";
					$("#standardname").html(newtab);
				}else{
					alert(msg.errMsg);
				}				
			}
		});
}

