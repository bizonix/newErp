/* 
 * 权限组列表页js
 */
/*
 * 删除权限组
 */
function deletegroup(gid){
    if(confirm('确定要删除该组吗？')){
        $.getJSON(
                 'json.php?mod=powergroup&act=deletePowerGroup&jsonp=1&gid='+gid,
                 function (data){
                     if(data['errCode'] == 0){  //删除失败
                         alert(data['errMsg']);
                     } else { //删除成功 刷新页面
                         window.location.reload();
                     }
                 }
            );
    }
}

/*
 * 删除权限
 */
function deletepower(pid){
    if(!confirm('确定要删除该权限吗？')){
        return false;
    }
    $.getJSON(
                 'json.php?mod=power&act=deletePower&jsonp=1&pid='+pid,
                 function (data){
                     if(data['errCode'] == 0){  //删除失败
                         alert(data['errMsg']);
                     } else { //删除成功 刷新页面
                         window.location.reload();
                     }
                 }
            );
}

/*
 * 搜索表单数据完整性验证
 */
function searchvalidata(){
    var keywords = $('#sekeywords').val();
    if(keywords.length<1){
        $('#showerror').text('关键字不能为空');
        return false;
    }
}

/*
 * 权限搜索表单验证
 */
function powerse(){
    var powergroup = $('#powergourpse').val();
    var keywords = $('#keywords').val();
    if(powergroup == 0){
        $('#showerror').text('请选择权限所属组!');
        return false;
    }
    if(keywords.length <1){
        $('#showerror').text('请输入关键字!');
        return false;
    }
    
}
