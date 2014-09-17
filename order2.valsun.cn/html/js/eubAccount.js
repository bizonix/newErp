$(document).ready(function(){
	$("#curform").validationEngine({autoHidePrompt:true});
	$("#back").click(function(){
		window.location.href   ="index.php?mod=Account&act=index&rc=reset";
	});
});