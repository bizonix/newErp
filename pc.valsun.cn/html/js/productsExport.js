

$(function(){

    $("button#exp").click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        if(startdate == '' || enddate == ''){
            $("#exportSpan").text('起止时间不能为空');
            return;
        }
        if(startdate > enddate){
            $("#exportSpan").text('起止时间不合法');
            return;
        }
        window.open("index.php?mod=products&act=exportProductsFinished&startdate="+startdate+"&enddate="+enddate);
    });

});
