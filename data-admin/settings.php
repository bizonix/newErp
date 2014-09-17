<?php
include "top.php";
include "sidebar.php";
?>
<div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <h1>添加团队信息<small>Enter Your Data</small></h1>
            <ol class="breadcrumb">
              <li><a href="index.html"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="active"><i class="fa fa-edit"></i> Forms</li>
            </ol>
            <div class="alert alert-info alert-dismissable" style="display:none">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				添加一些默认的基础信息，团队成员信息，外币汇率等。。。。。。。
            </div>
            <div class="alert alert-success " id="success-tip" style="display:none">
            </div>
            <div class="alert alert-dager " id="fail-tip" style="display:none">
            </div>
          </div>
        </div><!-- /.row -->

        <div class="row">
          <div class="col-lg-6">
            <form role="form">

              <div class="form-group">
                <label>成员</label>
				<textarea class="form-control" rows="10" id="teamer"></textarea>
              </div>
				<div class="form-group">
                <label>销售Or 采购</label>
                <label class="radio-inline">
                  <input type="radio" name="teamType" id="sale-select" value="sale" checked="">销售 
                </label>
                <label class="radio-inline">
                  <input type="radio" name="teamType" id="caigou-select" value="caigou">采购 
                </label>
              </div>

              <div class="form-group">
                <label>销售&采购组长</label>
                <select class="form-control" id="sale-teamer">
                  <option>覃雅丽</option>
                  <option>申智波</option>
                  <option>张丽</option>
                  <option>蔡丽宏</option>
                  <option>聂文敏</option>
                  <option>奚克银</option>
                </select>
                <select class="form-control" id="caigou-teamer" style="display:none">
                  <option>兰海</option>
                  <option>张磊</option>
                  <option>卫伟</option>
                  <option>郭玲</option>
                  <option>李玲</option>
                  <option>张良</option>
                  <option>张萍萍</option>
                  <option>张文辉</option>
                </select>
              </div>
              <button type="submit" class="btn btn-default" id="submit-btn">提交</button>
              <button type="reset" class="btn btn-default">重置</button>  

            </form>

          </div>
          <div class="col-lg-6">
			<div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-money"></i> 销售&采购团队</h3>
              </div>
              <div class="panel-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped tablesorter">
                    <thead>
                      <tr>
                        <th>团队type<i class="fa fa-sort"></i></th>
                        <th>成员 <i class="fa fa-sort"></i></th>
                        <th>团队组长<i class="fa fa-sort"></i></th>
                      </tr>
                    </thead>
                    <tbody>
					<?php
						$teamInfo = $db->teamInfo->find()->sort(array("_id"=>-1));
						foreach ($teamInfo as $item) {
							foreach($item['member'] as $sitem){
					?>

                      <tr>
						  <td><?php if($item['teamType'] == "sale"){
								echo "销售";
							 }else{
								echo "采购";
							 }
							?>
						 </td>
						 <td><?php echo $sitem;?></td>
						 <td><?php echo $item['teamLeader'];?></td>
                     </tr>
					<?php
						}
					}
					?>
                    </tbody>
                  </table>
                </div>
                <div class="text-right">
                  <a href="#">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->

      </div><!-- /#page-wrapper -->

<?php
include "footer.php";
?>
<script>
$("#sale-select").change(function(){
	team_trigger();
});
$("#caigou-select").change(function(){
	team_trigger();
});

function team_trigger(){
	var teamType = $("input[name='teamType']:checked").val();
	if(teamType == "sale"){
		$("#sale-teamer").show();
		$("#caigou-teamer").hide();
	}else{
		$("#sale-teamer").hide();
		$("#caigou-teamer").show();
	}
}

$("#submit-btn").click(function(e){
	var teamer,teamType ,teamLeader,teamer_arr = [];
	e.preventDefault();
	teamer = $.trim($("#teamer").val());
	teamType = $("input[name='teamType']:checked").val();
	if(teamType == "sale"){
		teamLeader = $("#sale-teamer").val();
	}else{
		teamLeader = $("#caigou-teamer").val(); 
	}

	if(teamer.indexOf(",") != -1){
		teamer = teamer.replace("," ," ");
		teamer = teamer.replace("，" ," ");
	}
	if(teamer != ""){
		teamer_num_tmp = teamer.split(/(\s+)|(\n+)|(\r+)/);
	}
	$.each(teamer_num_tmp,function(i,item){
		item = $.trim(item);
		if(!(item == "" || item == undefined || item == "\r\n")){
			teamer_arr.push(item);
		}
	});
	teamer_arr = $.unique(teamer_arr);
	$.post("api.php",{"type":"addTeam","teamType":teamType,"teamer_arr":teamer_arr,"teamLeader":teamLeader},function(rtn){
		if(rtn.code == 1){
			$("#success-tip").empty().html(rtn.msg).show();
			setTimeout(function(){
				$("#success-tip").hide();
				$("#fail-tip").hide();
			},1000);
		}else{
			$("#fail-tip").empty().html(rtn.msg).show();
		}
	},"json");
});
</script>


