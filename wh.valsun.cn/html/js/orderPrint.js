var LODOP=getLodop();
$(function(){
    $('#ebay_id').focus();
})
//打印面单
function print_order(obj, e){
    var keycode = e.keyCode;
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
    $.post("/json.php?mod=orderPrint&act=check_order&jsonp=1&is_preview=1", {order:ebay_id},function(msg){
        var type    =   msg.errCode == 200 ? true : false;
        //console.log(msg);return false;
        //alert(msg.data);return false;
        show_msg(msg.errMsg, type);
        select('ebay_id');
        if(msg.errCode == 200){
            $('#print_order').contents().find('body').html(msg.data);
            print_data(msg.data);
            //window.open('/index.php?mod=orderPrint&act=index'); 
        }
    }, "json");
}

//function prn1_preview(data) {
//    
//    //LODOP.SET_PRINT_MODE("PRINT_PAGE_PERCENT","82%");  //按比例打印
//    //SET_PRINT_MODE("FULL_WIDTH_FOR_OVERFLOW",true);
//    //SET_PRINT_MODE("FULL_HEIGHT_FOR_OVERFLOW",true);
//	LODOP.PREVIEW();
//};

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
    var print_type  =   $("input:radio[name=print_type]:checked").val();
    if(print_type == 2){
        LODOP.PRINT();  //直接打印  
    }else{
        LODOP.PREVIEW(); //打印预览
    }
}

/** 选择打印机列表**/
function CreatePrinterList(){
    if (document.getElementById('PrinterList').innerHTML!="") return;

	var iPrinterCount=LODOP.GET_PRINTER_COUNT();
	for(var i=0;i<iPrinterCount;i++){

		var option=document.createElement('option');
		option.innerHTML=LODOP.GET_PRINTER_NAME(i);
		option.value=i;
		document.getElementById('PrinterList').appendChild(option);
	};	
};

/**生成选定打印机纸张列表**/
function CreatePagSizeList(){
   clearPageListChild();
   var strPageSizeList=LODOP.GET_PAGESIZES_LIST(getSelectedPrintIndex(),"\n");
   var Options=new Array(); 
   Options=strPageSizeList.split("\n");       
   for (i in Options)    
   {    
     var option=document.createElement('option');   
	 option.innerHTML=Options[i];
	 option.value=Options[i];
	 document.getElementById('PagSizeList').appendChild(option);
   }  
};
/**获取纸张设置**/
function clearPageListChild(){
   var PagSizeList =document.getElementById('PagSizeList'); 
   while(PagSizeList.childNodes.length>0){
	   var children = PagSizeList.childNodes;	
  		 for(i=0;i<children.length;i++){		
		PagSizeList.removeChild(children[i]);	
  	  };	   
   };	   
};
/**获取打印机**/
function getSelectedPrintIndex(){
	if (document.getElementById("Radio2").checked) 
	return document.getElementById("PrinterList").value;
	else return -1; 		
};
/**获取纸张设置**/
function getSelectedPageSize(){
	if (document.getElementById("Radio4").checked) 
	return document.getElementById("PagSizeList").value;
	else return ""; 		
};
/**跳转到发货单面单打印页面**/
function gotoprint(){
    var printIndex  =   getSelectedPrintIndex();
    var pageIndex   =   getSelectedPageSize();
    window.location.href    =   "/index.php?mod=orderPrint&act=orderPrint&printIndex="+printIndex+"&pageIndex="+pageIndex;
}