$(function(){
	jQuery("#omAddAverageDailyCount").validationEngine();
    $("#add").click(function(){
        window.location.href = "index.php?mod=averageDailyCount&act=add";
    });
    $(".update").click(function(){
        var id = $(this).attr("pid");
        window.location.href = "index.php?mod=averageDailyCount&act=edit&rc=reset&id="+id;
    });
    $(".delete").click(function(){
        var id = $(this).attr("pid");
        if($.trim(id)){
            alertify.confirm('确定要删除该条记录？',function(e){
                if(e){
                    window.location.href = "index.php?mod=averageDailyCount&act=delete&id="+id;
                }
            });
        }

    });
	$("#back").click(function(){
        history.back();
    });
})

function changePlatform(){
	var platformId	=	$("#platformId").val();
	var htmlStr	=	'';
	$("#accountId").val('');
	$.ajax(
		{
			type: 'post',
			url: 'index.php?act=changePlatformId&mod=orderModify',
			dataType : 'json',
			data        :{"platformId":platformId},
			success : function (data){
				if(data.errCode == 998){
					htmlStr	+=	'<label for="accountId" style="font-size:20px;">帐号：</label>';
					htmlStr	+=	'<select name="accountId" id="accountId" style="width:200px;height:30px;font-size:20px" class="validate[required]">';
					htmlStr	+=	'<option value="">全部账号</option>';
					htmlStr	+=	'</select>';
					htmlStr	+=	'<span id="accountSpan" style="color: red;">*</span>';
					$("#selectAccountList").html(htmlStr);
					alertify.error(data.errMsg);
				}else{
					htmlStr	+=	'<label for="accountId" style="font-size:20px;">帐号：</label>';
					htmlStr	+=	'<select name="accountId" id="accountId" style="width:200px;height:30px;font-size:20px" class="validate[required]">';
					for(i in data.data){
						htmlStr	+=	'<option value = "'+i+'">'+data.data[i]+'</option>';
					}
					htmlStr	+=	'</select>';
					htmlStr	+=	'<span id="accountSpan" style="color: red;">*</span>';
					$("#selectAccountList").html(htmlStr);
				}
			}
		}
	);
}