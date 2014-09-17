/* 
 * 权限系统用户管理js
 */

/*
 * 从鉴权系统拉取所有的用户信息并更新到本地
 */
function updateuserlist(){
    $.getJSON(
            'ajax.php?mod=power&act=updateAllUsers&jsonp=1',
            function (data){
                if(data['errCode'] == 1){
                    alert(data['errMsg']);
                    window.location.reload();
                }else{
                    alert(data['errMsg']);
                }
            }
        );
}

/*
 * 删除用户
 */

function deleteuser(uid){
    if(!confirm('确定删除该用户吗？')){
        return;
    }
    $.getJSON(
            'ajax.php?mod=power&act=deleteUser&jsonp=1&uid='+uid,
            function (data){
                if(data['errCode'] == 1){
                    alert(data['errMsg']);
                    window.location.reload();
                }else{
                    alert(data['errMsg']);
                }
            }
        );
}

