$(function(){
	//POST数据验证
	$("#skuTypeQcAddForm").validationEngine({autoHidePrompt:true});
	
	//添加新分类页面
	$("#skuTypeQcAdd").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=skuTypeQcAdd";				
	});	
	
	//添加新样本大小页面
	$("#sampleSizeAdd").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=sampleSizeAdd";				
	});	
	
	//添加新检测方式页面
	$("#detectionTypeAdd").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=detectionTypeAdd";				
	});
	
	//新分类取消
	$("#returnPage").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=skuTypeQcList";				
	});
	
	//检测方式删除
	$('button[name="detectionTypeDel"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var key_id = this_tr.find('input:checkbox[name="key_id"]').val();
		if(confirm("确定要废弃这个检测方式吗？")){
			window.location.href = "index.php?mod=sampleStandard&act=detectionTypeDel&delId="+key_id;
		}
	});
	
	//检测方式取消
	$("#detectionTypeReturnPage").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=detectionTypeList";				
	});
	
	//添加样本大小取消
	$("#sampleSizeReturnPage").click(function(){	
		window.location.href = "index.php?mod=sampleStandard&act=sampleSizeList";				
	});
	
});

function sortNum(){
	var sortInput 		= $('#sortInput').val();
	var teststr = /^\d+$/;
	if(!teststr.test(sortInput)){
		$('#showSortMsg').text('仅限填写数字！');	
		$('#skuTypeQcAddSubmit').attr("disabled",true);
		$('#sortInput').focus();
		return false;
	}else{
		$('#showSortMsg').text('');		
		$('#skuTypeQcAddSubmit').attr("disabled",false);
		$('#skuTypeQcAddSubmit').focus();
		return true;
	}
}


function sampleNum(){
	var sampleNumInput 		= $('#sampleNumInput').val();
	var teststr = /^\d+$/;
	if(!teststr.test(sampleNumInput)){
		$('#showSortMsg').text('仅限填写数字！');	
		$('#sampleSizeAddSubmit').attr("disabled",true);
		return false;
	}else{
		$('#showSortMsg').text('');		
		$('#sampleSizeAddSubmit').attr("disabled",false);
		return true;
	}
}