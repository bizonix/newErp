$(function(){
    jQuery("#fromOpenConfigList").validationEngine();
    jQuery("#fromOpenConfigEdit").validationEngine();
    $(".update").click(function(){
        var id = $(this).attr("pid");
        window.location.href = "index.php?mod=fromOpenConfig&act=edit&id="+id;

    });
    
    
    $(".delete").click(function(){
		var id    = $(this).attr("pid");
		alertify.confirm('确定要删除该记录？',function(e){
			if(e && $.trim(id)){
				window.location.href    = "index.php?mod=FromOpenConfig&act=delformList&id="+id;
			}
			
		});
	});
    
    $("#back").click(function(){
        history.back();
    });

    
    
    /*$("select[class*=flexselect]").flexselect();*///搜索自动补全

});
function check(){
	return confirm("确定修改吗？");
	/*return alertify.confirm("确定修改吗？",function(e){
		if(e){
			return true;
		}else{
			return false;
		}
	});*/
}
