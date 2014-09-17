$(function(){
	//POST数据验证
	$("#platformAddForm").validationEngine({autoHidePrompt:true});

	$("#addNewPlatform").click(function(){		
		window.location.href = "index.php?mod=platformManage&act=platformAdd";				
	});
	
	//平台删除
	$('input[name="platform_delete"]').click(function(){
		var this_tr = $(this).parents('tr:first');
		var t_id = this_tr.find('input:checkbox[name="t_id"]').val();
		if(confirm("确定要删除此条记录吗？")){
			window.location.href = "index.php?mod=platformManage&act=platformDel&delId="+t_id;
		}
	});
	
	//返回
	$("#returnPage").click(function(){		
		window.location.href = "index.php?mod=platformManage&act=platformShow";				
	});

});

/*
 * ajax检测平台名称是否重复(添加、编辑页面)
 */
function checkExist($type){
	if($("#platformId").length > 0){//是否编辑页面判断
		var whereEdit = "and id != "+$('#platformId').val(); 		
	}else{
		var whereEdit = " ";
	}	
	if($type == 'En'){
		var En 			= $('#platformNameEnInput').val();		
		if(En.replace(/\ +/g,"") != ''){			
			var whereEn = "platformNameEn = '"+En.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格 
			//alert(whereEn);return false;
			$.getJSON(
	            'json.php?mod=platform&jsonp=1&act=checkPlatformExist&name='+whereEn,	            
	            function (data){
	                if(data['errCode']!=1){
	                	$('#platformAddFormSumit').attr("disabled",true);
	                    $('#showEnMsg').text(data['errMsg']);
	                }else{
	                	$('#platformAddFormSumit').attr("disabled",false);
	                    $('#showEnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	if($type == 'Cn'){
		var Cn 			= $('#platformNameCnInput').val();
		if(Cn.replace(/\ +/g,"") != ''){			
			var whereCn = "platformNameCn = '"+Cn.replace(/^(\s|\xA0)+|(\s|\xA0)+$/g, '')+"'"+whereEdit; //除去前后空格
			$.getJSON(
	            'json.php?mod=platform&jsonp=1&act=checkPlatformExist&name='+whereCn,
	            function (data){
	                if(data['errCode']!=1){ 
	                	$('#platformAddFormSumit').attr("disabled",true);
	                    $('#showCnMsg').text(data['errMsg']);
	                }else{
	                	$('#platformAddFormSumit').attr("disabled",false);
	                    $('#showCnMsg').text(data['errMsg']);
	                }
	            }
		     );	    	
	    }
	}
	
	    
}