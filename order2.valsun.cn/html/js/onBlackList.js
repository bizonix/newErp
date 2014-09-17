$(function(){
	    $("#updateBlackList").validationEngine({autoHidePrompt:true});
	    $('#omAddPlatformList').validationEngine({autoHidePrompt:true});
	 $("#add").click(function(){
	        window.location.href = "index.php?mod=Blacklist&act=add";
	    });
	 
    $(".update").click(function(){
        var id = $(this).attr("pid");
        window.location.href = "index.php?mod=Blacklist&act=edit&id="+id;
	});
    
	$('#all-select').click(function(){
		if(($('#all-select').attr('checked')	==	'checked')&&($('#inverse-select').attr('checked')	==	'checked')){
			$('#inverse-select').attr('checked',false);
		}
		select_all_blackList('all-select',1,'input[name="account[]"]');
	});

	$('#inverse-select').click(function(){
		if(($('#all-select').attr('checked')	==	'checked')&&($('#inverse-select').attr('checked')	==	'checked')){
			$('#all-select').attr('checked',false);
		}
		select_all_blackList('inverse-select',0,'input[name="account[]"]');
	});
    $(".delete").click(function(){
        var id = $(this).attr("pid");
        if($.trim(id)){
        	alertify.confirm('确定要删除该平台记录？',function(e){
        		if(e){
        			window.location.href = "index.php?mod=Blacklist&act=delete&id="+id;
        		}
        	});
        	}
        
        
    });
	$("#search").click(function(){
		var accountId           = $("#accountId").val();
		var platformId          = $("#platformId").val();
		var platformUsername    = $("#platformUsername").val();
		window.location.href = "index.php?mod=Blacklist&act=index&platformId="+platformId+"&accountId="+accountId+"&platformUsername="+platformUsername;
	});
    $("#reback").click(function(){
    	window.location.href  = "index.php?mod=Blacklist&act=index&rc=reset";
    });

});

function onchangeSite(){
	var platformId	=	$("#platformId").val();
	var htmlStr	=	'';
	var	obj;
	$.ajax(
		{
			type: 'POST',
			url: 'index.php?mod=Blacklist&act=getAccountByPId',
			dataType : 'json',
			data        :{"platformId":platformId},
			success : function (data){
				if(data.errCode != 200){
					alertify.error(data.errMsg);
				}else{
					for(obj in data.data){
						htmlStr	+=	' <div style="width:10%;float:left" > <input value="'+data.data[obj].id+'" type="checkbox" data-val="'+data.data[obj].account+'" name="account[]" checked="checked">'+data.data[obj].account+"</div>";
					}
					$("#selectPlatformAccount").html(htmlStr);
				}
			}
		}
	);
}

function onchangModify(){
    var platformId = $('#platformId').val();
	var id	=	$("#id").val();
	window.location.href = "index.php?mod=Blacklist&act=edit&id="+id+"&platformId="+platformId;
}


function select_all_blackList(id,type,selector,callback){
	var ckbutton_cur_checked = $('#'+id).attr('checked'); 
	$(selector).each(function(){
		if(this.disabled) return true;
		var self = $(this);
		if(type==1){
			if(ckbutton_cur_checked == undefined) ckbutton_cur_checked = false;
			self.attr('checked',ckbutton_cur_checked);
		}
		else{
			self.attr('checked',!self[0].checked);
		}
	});
	if(type == 1){
		$('#sku-inverse').attr('checked',false);
	}else{
		$('#sku-all').attr('checked',false);
	}

	try{
		callback.call();
	}
	catch(e){}
}

