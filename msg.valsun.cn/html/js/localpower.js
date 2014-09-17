/*
 * 本地权限页面js
 */

function skipToPage(url){
	var depid	= $('#userdept').val();
	window.location	= url+"&depname="+depid;
}
