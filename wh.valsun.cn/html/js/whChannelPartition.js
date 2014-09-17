/**
 * 分渠道功能js
 */

//根据运输方式获取对用的渠道
function channelChange(obj){
	var transportId = obj.value;
	$('#channelId').html('<option value="0">请选择运输渠道</option>');
	$.post('json.php?mod=whTransportPartition&act=getChannel&jsonp=1',{'transportId':transportId},function(msg){
		htmlstr = '';
		if(msg.errCode == 200){
			var channellist = msg.data;
			for(key in channellist){
				htmlstr += '<option value="'+channellist[key]['id']+'">'+channellist[key]['channelName']+'</option>';
			}
		}
		$('#channelId').append(htmlstr);
	},'json');
}

function selectChannelName(obj){
	$('#channelName').val(obj.options[obj.selectedIndex].text);
}