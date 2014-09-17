$(function(){
    $('.updatePic').click(function(){
        var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("是否对其修改图片？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=updatePic&infoId="+infoId+"&pendingId="+pendingId;
        }
	});

    $('.back').click(function(){
        var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0 && status != 2){
            return;
        }
        if(confirm("要进行重新回测处理？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=back&infoId="+infoId+"&pendingId="+pendingId;
        }
	});


    $('.return').click(function(){
	    var pendingId = $(this).attr('pending_id');
        var infoId = $(this).attr('info_id');
        var status = $(this).attr('status');
        if(status != 0){
            return;
        }
        if(confirm("要进行待退回处理？")){
            window.location.href = "index.php?mod=PendingProducts&act=updatePendingProducts&type=return&infoId="+infoId+"&pendingId="+pendingId;
        }
	});

});
