var LODOP=getLodop();
$(function(){
    $('#ebay_id').focus();
})
webcam.set_swf_url('/js/webcam.swf');
webcam.set_quality( 70 ); // JPEG quality (1 - 100)
webcam.set_shutter_sound( false ); // play shutter click sound
webcam.set_stealth( true );
var cam = $("#flashArea"); 
cam.html(
	webcam.get_html(600, 400, 600, 400)  //在#webcam中载入摄像组件 
);
webcam.set_hook( 'onComplete', 'my_completion_handler' );
//打印面单
function print_order(obj, e){
    var keycode = e.which;
    if(keycode !== 13){
        return false;
    }
    var ebay_id     =   $(obj).val();
    var preg        =   /^\d+$/;
    if(ebay_id == ''){
        return false;
    }
    if(!preg.test(ebay_id)){
        select('ebay_id');
        show_msg('请扫描发货单号!', false);
        return false;
    }
    //prn1_preview();
    process_data(ebay_id, true);
    take_photo();
}

/** 打印面单**/
function process_data(ebay_id, shang_last_shipOrderId){
    var is_preview  =   shang_last_shipOrderId === true ? 0 : 1;
    $.post("/json.php?mod=orderPrint&act=check_order&jsonp=1&is_preview="+is_preview, {order:ebay_id},function(msg){
        var type   =   msg.errCode == 200 ? true : false;
        //console.log(msg);return false;
        show_msg(msg.errMsg, type);
        select('ebay_id');
        if(msg.errCode == 200){
            $('#print_order').contents().find('body').html(msg.data);
            print_data(msg.data);
            if(shang_last_shipOrderId === true){
                $("#lastShipOrderId").val(ebay_id);
            }
            //window.open('/index.php?mod=orderPrint&act=index'); 
        }
    }, "json");
}

/**弹出重新打印或重新拍照界面**/
function operate(type){
    $('#reType').val(type);
    $('#dialog').dialog({
        width:450,
        title:'请扫描发货单号',
        position:"left",
        zIndex:9999
    });
}
/** 重新打印或重新拍照功能***/
function re_operation(obj, e){
    var keycode = e.which;
    if(keycode !== 13){
        return false;
    }
    var shipOrderId = $(obj).val();
    var preg        =   /^\d+$/;
    if(shipOrderId == ''){
        return false;
    }
    if(!preg.test(shipOrderId)){
        select('re_shipOrderId');
        show_msg('请扫描发货单号!', false);
        return false;
    }
    
    var type    =   $('#reType').val();
    
    if(type == 'reprint'){
        process_data(shipOrderId, false);
    }else if(type == 'rephoto'){
        retake(shipOrderId);
    }
    $('#dialog').dialog("close");
    $(obj).val('');
    select('ebay_id');
}

//信息提示
function show_msg(msg, type){
    var color       =   type == true ? 'green' : 'red';
    var msgObj      =   $('#show_msg');
    msgObj.html(msg);
    msgObj.css('color', color);
    msgObj.css('display', 'block');
}

/** 选择输入框**/
function select(id){
    $('#'+id).val('');
    $('#'+id).focus();
}

//初始化打印配置
function init_print_config(data){
    LODOP.PRINT_INIT("面单打印");               //首先一个初始化语句
    LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
    LODOP.ADD_PRINT_HTM("1mm","5mm","100%","100%",data); //上边距 左边距 宽度 高度 打印内容
    print_barcode(); //打印条形码
    LODOP.SET_PRINTER_INDEX(getSelectedPrintIndex()); //获取选定的打印机
    LODOP.SET_PRINT_PAGESIZE(0,0,0,getSelectedPageSize()); //获取选定的纸张设置
}

function print_barcode(){
    $(window.document).contents().find('#print_order').contents().find('.print_image').each(function(i){
        var data        =   $(this).attr('data');
        //var data    =   $(this).html();
        var image_type  =   $(this).attr("image_type");
        var height      =   $(this).css('height');
        var width       =   $(this).css('width');
        var image_top   =   $(this).attr('image_top');
        var image_left  =   $(this).attr('image_left');
        image_type      =   typeof(image_type) == 'undefined' ? 'jpg' : image_type;
        //alert(image_type);return false;
        var imagedata   =  'data:image/'+image_type+';base64,'+data;
        LODOP.ADD_PRINT_IMAGE(image_top, image_left, width, height, imagedata);
        LODOP.SET_PRINT_STYLEA(0,"Stretch", 2); //不变形压缩
    });
}


/** 打印**/
function print_data(data){
    init_print_config(data);
    var print_type  =   1;
    if(print_type == 2){
        LODOP.PRINT();  //直接打印  
    }else{
        LODOP.PREVIEW(); //打印预览
    }
}

/**获取打印机**/
function getSelectedPrintIndex(){
	return $("#printIndex").val();		
};
/**获取纸张设置**/
function getSelectedPageSize(){
	return $("#pageIndex").val();		
};
/**跳转到发货单面单打印页面**/
function gotoprint(){
    var printIndex  =   getSelectedPrintIndex();
    var pageIndex   =   getSelectedPageSize();
    window.location.href    =   "/index.php?mod=orderPrint&act=orderPrint&printIndex="+printIndex+"&pageIndex="+pageIndex;
}

//拍照
function take_photo(){
    var lastShipOrderId =   $("#lastShipOrderId").val();
    if(lastShipOrderId != ''){
        take_snapshot(lastShipOrderId);
    }
}

function take_snapshot(ebay_id) {
	// take snapshot and upload to server
	//document.getElementById('results').innerHTML = '<h4>Uploading...</h4>';
    $('#now_ebay_id').val(ebay_id);
	var now_time = document.getElementById('now_time').value;
	var url = './get_flashcam_order.php?action=save&oid='+ebay_id+'&fd=images/cz/'+now_time+',cz_'+ebay_id;
	webcam.set_api_url(url);
	webcam.snap();
}

function my_completion_handler(msg) {
	if (msg.match(/images.*/)) {
		var image_url = msg;//RegExp.$1;
		jSound.play({src:'./sounds/allok.wav',volume:100});
        publish_mq(msg);
		show_scan_list(msg);
        publish_mq(msg);
		//jSound.play({src:'./sounds/allok.wav',volume:100});
		//webcam.reset();
	}else {
		now_ebayid = document.getElementById('ebay_id').value;
		document.getElementById('least_scan_id').innerHTML = '<a style="cursor:pointer" onclick="retake('+now_ebayid+')">重拍</a>';
		//alert("PHP Error: " + msg);
	}
}

//获取最近复核5个订单照片
function show_scan_list(msg){
    var img_html    =   '';
    img_html    +=  '<img src="'+msg+'" width="210" height="140"/>';
    var now_ebay_id =   $('#now_ebay_id').val();
    img_html    +=  '<span><a onclick="retake('+now_ebay_id+')">重拍</a></span>';
    $('#show_image').html(img_html);
    $('#show_image').css('display','block');
	/*var ranNum = 10*Math.random();    //产生随机数
	var now_time = $('#now_time').val();
	var ebay_ids = [];
	var least_scan_ids = document.getElementById('least_scan').value;
	var show_picture = document.getElementById('show_picture');

	$('#least_scan_id').html('');
	ebay_ids = least_scan_ids.split(',');
	var len = ebay_ids.length;
	var newhtml = '';
	//显示最近复核的5个订单
	var pic_count	=	0;
	newhtml += '<div width="120px" style="float:left;"><span>订单号：'+ebay_ids[len-1]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" target="_blank"><image width="100px" src="images/cz/'+now_time+'/cz_'+ebay_ids[len-1]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[len-1]+')">重拍</a></div>';
	if(len-1>0){
		if(len>5){
			for(var i=len-2;i>=len-5;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}else{
			for(var i=len-2;i>=0;i--){
				pic_count	=	pic_count+1;
				if(pic_count > 1) continue;
				//ebay_ids[i]==bill;
				newhtml += '<div width="60px" style="float:left;margin-left:10px;"><span>订单号：'+ebay_ids[i]+'</span><br/><span><a class="inherit" href="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" target="_blank"><image width="40px" src="images/cz/'+now_time+'/cz_'+ebay_ids[i]+'.jpg?'+ranNum+'" /></a></span><br/><a class="inherit" style="cursor:pointer" onclick="retake('+ebay_ids[i]+')">重拍</a></div>';
			}
		}
	}
	$('#least_scan_id').html(newhtml);
	show_picture.style.display = 'block';
	$('#show_picture').show();
	return false;*/
}

//重新拍照
function retake(bill){
	take_snapshot(bill);
}

/**图片队列推送**/
function publish_mq(msg){
	var url  = "/json.php?mod=publish&act=receive_image&jsonp=1";
	$.post(url,{msg:msg});
}