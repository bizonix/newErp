$(function(){
    $('#search').click(function(){
        var ioType = $("#ioType").val();
        var keyWord = $("#keyWord").val();
        var select = $("#select").val();
        var ioTypeId = $("#ioTypeId").val();
        var cStartTime = $("#cStartTime").val();
        var cEndTime = $("#cEndTime").val();
        var url = '';
        if(select == 1){
            url = "&id="+keyWord;
        }else if(select == 2){
            url = "&ordersn="+keyWord;
        }else if(select == 3){
            url = "&sku="+keyWord;
        }else if(select == 4){
            url = "&purchaseId="+keyWord;
        }else if(select == 5){
            url = "&userId="+keyWord;
        }else if(select == 6){
            url = '&position='+keyWord;
        }
        url = url + "&ioTypeId="+ioTypeId+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime;
		window.location.href = "index.php?mod=whIoRecords&act=getWhIoRecordsList&type=search&ioType="+ioType+url+"&keyWord="+keyWord+"&select="+select;
	});
});

function exportStatusInfo(){
	var ioType = $("#ioType").val();
	var keyWord = $("#keyWord").val();
	var select = $("#select").val();
	var ioTypeId = $("#ioTypeId").val();
	var cStartTime = $("#cStartTime").val();
	var cEndTime = $("#cEndTime").val();
	var url = '';

	if(select == 1){
		url = "&id="+keyWord;
	}else if(select == 2){
		url = "&ordersn="+keyWord;
	}else if(select == 3){
		url = "&sku="+keyWord;
	}else if(select == 4){
		url = "&purchaseId="+keyWord;
	}else if(select == 5){
		url = "&userId="+keyWord;
	}
	url = url +"&ioType="+ioType+"&ioTypeId="+ioTypeId+"&cStartTime="+cStartTime+"&cEndTime="+cEndTime;
	url = "index.php?mod=whIoRecords&act=export&ioType="+ioType+url;
	window.open(url);
}