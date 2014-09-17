$(function(){
    var mod = $("#mod").val();
    var act = $("#act").val();
    if(mod == 'iqc'){//c1
        $(".c1").css("background-color","#0078b5");
        $(".c1").css("background-image","url(./images/3.gif)");

    }else if(mod == 'iqcDetect'){//c2
        $(".c2").css("background-color","#0078b5");
        $(".c2").css("background-image","url(./images/3.gif)");
    }else if(mod == 'iqcInfo' || mod == 'defectiveProducts' || mod == 'pendingProducts' || mod == 'scrappedProducts' || mod == 'returnProducts'){//c3
        $(".c3").css("background-color","#0078b5");
        $(".c3").css("background-image","url(./images/3.gif)");
    }else if(mod == 'nowSampleStandard' || mod == 'sampleStandard' || mod == 'sampleCoefficient'){//c4
        $(".c4").css("background-color","#0078b5");
        $(".c4").css("background-image","url(./images/3.gif)");
    }

    if(act == 'iqcList'){//c12
        $(".c11").css("background-color","#0092dc");
    }else if(act == 'iqcWaitCheck'){
        $(".c12").css("background-color","#0092dc");
    }else if(act == 'iqcScan'){
        $(".c21").css("background-color","#0092dc");
    }else if(act == 'backScan'){
        $(".c22").css("background-color","#0092dc");
    }else if(act == 'stockScan'){
        $(".c23").css("background-color","#0092dc");
    }else if(act == 'iqcScanList'){
        $(".c31").css("background-color","#0092dc");
    }else if(act == 'getDefectiveProductsList'){
        $(".c32").css("background-color","#0092dc");
    }else if(act == 'getPendingProductsList'){
        $(".c33").css("background-color","#0092dc");
    }else if(act == 'getScrappedProductsList'){
        $(".c34").css("background-color","#0092dc");
    }else if(act == 'getReturnProductsList'){
        $(".c35").css("background-color","#0092dc");
    }else if(act == 'nowSampleType'){
        $(".c41").css("background-color","#0092dc");
    }else if(act == 'sampleStandardList' || act == 'addSampleType' || act == 'openSampleType' || act == 'editSampleType'){
        $(".c42").css("background-color","#0092dc");
    }else if(act == 'skuTypeQcList' || act == 'skuTypeQcAdd' || act == 'skuTypeQcEditList'){
        $(".c43").css("background-color","#0092dc");
    }else if(act == 'detectionTypeList' || act == 'detectionTypeAdd'){
        $(".c44").css("background-color","#0092dc");
    }else if(act == 'sampleSizeList' || act == 'sampleSizeAdd' || act == 'sampleSizeEditList'){
        $(".c45").css("background-color","#0092dc");
    }else if(act == 'getSampleCoefficientList' || act == 'addScanSampleCoefficient' || act == 'updateScanSampleCoefficient'){
        $(".c46").css("background-color","#0092dc");
    }
});

