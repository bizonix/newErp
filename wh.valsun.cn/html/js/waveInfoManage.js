function chooseornot(selfobj) { //全选/取消
    var ischecked = selfobj.checked
    var list = $('.checkclass');
    for (i in list) {
        list[i].checked = ischecked;
    }
}

function wave_print(){ //打印配货单
    var ids =   new Array();
    $("input:checkbox[name=orderids]:checked").each(function(){
        ids.push($(this).val());
    });
    if(ids.length == 0){
        alertify.error("请选择需要打印的配货单!");
        return false;
    }
    window.open("index.php?mod=waveInfoManage&act=pritnWave&ids="+ids);
}

$(function(){
    //$('#check_all').click(function(){ //全选
//        $(".checkclass").each(function() { 
//            $(this).attr("checked", true); 
//        });
//        $(this).attr('id','cancel_all');
//    });
//    $('#cancel_all').click(function(){ //取消全选
//        $(".checkclass").each(function() { 
//            $(this).attr("checked", false); 
//        });
//        $(this).attr('id','check_all');
//    });
})