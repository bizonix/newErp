$(function(){
    $('#search').click(function(){
        var keyWord = $.trim($("#keyWord").val());
        var select = $("#select").val();
        var reStatus = $("#reStatus").val();
        var cStartTime = $("#cStartTime").val();
        var cEndTime = $("#cEndTime").val();
		var eStartTime = $("#eStartTime").val();
        var eEndTime = $("#eEndTime").val();
		if(select==0 && keyWord!=''){
			$("#select").focus();
			alertify.error('请选择类型!');
			return;
		}
        var url = "&keyWord="+keyWord+"&select="+select+"&reStatus="+reStatus+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime+"&eStartTime="+eStartTime+"&eEndTime="+eEndTime;
        window.location.href = "index.php?mod=WhRecManage&act=getWhRecManageList&type=search"+url;
	});
    
    $('#export').click(function(){
        var eStartTime = $("#eStartTime").val();
        var eEndTime = $("#eEndTime").val();
        
        window.location.href = "index.php?mod=WhRecManage&act=exportWhRecManageExcel"+"&eStartTime="+eStartTime+"&eEndTime="+eEndTime;
	});
    
});

