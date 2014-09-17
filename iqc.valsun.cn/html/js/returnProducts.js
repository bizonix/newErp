$(function(){
    $('.audit').click(function(){
        var returnId = $(this).attr('return_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("确定审核？")){
            window.location.href = "index.php?mod=ReturnProducts&act=auditReturnProducts&returnId="+returnId;
        }
	});

	$('.package').click(function(){
        var returnId = $(this).attr('return_id');
        var status = $(this).attr('status');
        if(status != 1){
            return;
        }
        if(confirm("确定打包处理？")){
            window.location.href = "index.php?mod=ReturnProducts&act=updateReturnProducts&returnId="+returnId;
        }
	});

});
