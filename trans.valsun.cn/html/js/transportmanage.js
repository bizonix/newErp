//add by Herman.Xi @ 20130724
$(function(){
	$("#addNewCarrier").click(function(){
		location.href = "index.php?mod=transportmanage&act=addPage";
	});
	$("#openCarrier").click(function(){
		var checkboxObj = $("input:checkbox[name='carrierName']:checked");
		if(checkboxObj.length == 0){
			$("#showMessage").append(warningMessage);
			$("#warningMessage").append("<p>没有选中要操作的值</p>");
			$("#warningMessage").animate({"top":"0px"},1500,function(){
				$("#warningMessage").remove();
			});
			return false;
		}else{
			var binArr = new Array();
			checkboxObj.each(function(i){
				 binArr[i] = $(this).val();
			});
			var binString = binArr.join(",");
			$("#showMessage").append(successMessage);
			$("#successMessage").append("<p>操作成功</p>");
			$("#successMessage").animate({"top":"0px"},1500,function(){
				$("#successMessage").remove();
				location.href = "index.php?mod=transportmanage&act=openCarrier&carrierIds="+binString;
			});
		}
	});
	$("#dropCarrier").click(function(){
		var checkboxObj = $("input:checkbox[name='carrierName']:checked");
		if(checkboxObj.length == 0){
			$("#showMessage").append(warningMessage);
			$("#warningMessage").append("<p>没有选中要操作的值</p>");
			$("#warningMessage").animate({"top":"0px"},1500,function(){
				$("#warningMessage").remove();
			});
			return false;
		}else{
			var binArr = new Array();
			checkboxObj.each(function(i){
				 binArr[i] = $(this).val();
			});
			var binString = binArr.join(",");
			$("#showMessage").append(successMessage);
			$("#successMessage").append("<p>操作成功</p>");
			$("#successMessage").animate({"top":"0px"},1500,function(){
				$("#successMessage").remove();
				location.href = "index.php?mod=transportmanage&act=dropCarrier&carrierIds="+binString;
			});
		}
	});
	$("#transAddForm").validationEngine({autoHidePrompt:true});
	$("#transEditForm").validationEngine({autoHidePrompt:true});
});

function gobacktoprevious(){
    window.location = "index.php?mod=transportmanage&act=list";
}

/*
 * ajax检测名称是否重复
 */
function checknameexist(){
    var name = $('#carrierNameCnInput').val();
    $.getJSON(
            'json.php?mod=transportmanage&jsonp=1&act=checkNameExist&name='+name,
            function (data){
                if(data['errCode']!=1){ //名称重复
                    $('#showerrmsg').text(data['errMsg']);
                }else{
                    $('#showerrmsg').text(data['errMsg']);
                }
            }
        );
}