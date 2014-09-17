$(function(){
    $('.audit').click(function(){
        var scrappedId = $(this).attr('scrapped_id');
        var status = $(this).attr('status');
        if(status == 1){
            return;
        }
        if(confirm("确定审核处理？")){
            window.location.href = "index.php?mod=scrappedProducts&act=updateScrappedProducts&scrappedId="+scrappedId;
        }
	});

});
