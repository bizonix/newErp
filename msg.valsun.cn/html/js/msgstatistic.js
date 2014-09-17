$(function(){
	$('#export_btn').click(function(){
		$('#export').get(0).action="index.php?mod=amazonMessageStatistics&act=messageStatistics&starttime="+$('#starttime').val()+"&endtime="+$('#endtime').val()+"&export";
		$('#export').submit();
	})
})