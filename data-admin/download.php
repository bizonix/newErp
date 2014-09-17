<?php
include "top.php";
include "sidebar.php";
?>

      <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <h1>统计 报表<small>下载。。。</small></h1>
            <ol class="breadcrumb">
              <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="active"><i class="fa fa-table"></i> Tables</li>
            </ol>
          </div>
        </div><!-- /.row -->

        <div class="row">
			 <div class="col-lg-12">
				<h3>各个平台每日发货报表</h3>
				<hr>
			</div>
			 <div class="col-lg-6 ">
                <label for="dtp_input2" class="col-md-2 control-label">起始时间</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<input type="hidden" id="dtp_input2" value=""><br>
            </div>
			 <div class="col-lg-6">
                <label for="dtp_input2" class="col-md-2 control-label">截止时间</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input3" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<button type="button" class="btn btn-primary btn-sm" id="daysale-btn">下载</button>
				<input type="hidden" id="dtp_input3" value=""><br>
            </div>
        </div><!-- /.row -->

        <div class="row">
			 <div class="col-lg-12">
				<h3>ebay平台每日毛利报表</h3>
				<hr>
			</div>
			 <div class="col-lg-6 ">
                <label for="dtp_input2" class="col-md-2 control-label">起始时间</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="ebay_input2" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<input type="hidden" id="ebay_input2" value=""><br>
            </div>
			 <div class="col-lg-6">
                <label for="dtp_input2" class="col-md-2 control-label">截止时间</label>
                <div class="input-group date form_date col-md-5" data-date="" data-date-format="dd MM yyyy" data-link-field="ebay_input3" data-link-format="yyyy-mm-dd">
                    <input class="form-control" size="16" type="text" value="" >
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
				<button type="button" class="btn btn-primary btn-sm" id="ebay-btn">下载</button>
				<input type="hidden" id="ebay_input3" value=""><br>
            </div>
        </div><!-- /.row -->

      </div><!-- /#page-wrapper -->
	  <div class="row" id="daysale_down">
	  </div>

<?php
include "footer.php";
?>
<script>

function formatDate(timestamp) {
	var time ,year,month,date;
	time  = new Date(timestamp);
	year  = time.getFullYear();
	month = time.getMonth() + 1;
	if(month < 10){
		month = "0"+month;
	}
	date  = time.getDate();

	if(date < 10){
		date = "0"+date;
	}
	return year + '-' + month + '-' + date;
}

$("#daysale-btn").click(function(){
	var start ,end ;
	start = $("#dtp_input2").val();
	end = $("#dtp_input3").val();
	//Date.parse(new Date("2014-7-13"))
	var inttime = Date.parse(new Date(end))-Date.parse(new Date(start));
	var days = inttime/86400000+1;
	console.log(days);
	var downloaddata = '';
	for(var i=0; i<days; i++){
		var date = formatDate(new Date(end)-i*86400000);
		downloaddata += '<div class="col-lg-12"><i class="fa fa-desktop"></i><a href="http://192.168.200.161/download/daysale_'+date+'.xlsx" target="_blank">各平台每天发货数据_'+date+'.xlsx</a></div>';
	}
	$("#daysale_down").html(downloaddata);
});

$("#ebay-btn").click(function(){
	var start ,end ;
	start = $("#ebay_input2").val();
	end = $("#ebay_input3").val();
	console.log(end);
	//Date.parse(new Date("2014-7-13"))
	var inttime = Date.parse(new Date(end))-Date.parse(new Date(start));
	var days = inttime/86400000+1;
	console.log(days);
	var downloaddata = '';
	for(var i=0; i<days; i++){
		var date = formatDate(new Date(end)-i*86400000);
		downloaddata += '<div class="col-lg-12"><i class="fa fa-desktop"></i><a href="http://192.168.200.161/download/ebay_excel_'+date+'.xlsx" target="_blank">ebay平台每日毛利报表_'+date+'.xlsx</a></div>';
	}
	$("#daysale_down").html(downloaddata);
});
</script>


