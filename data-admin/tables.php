<?php
include "top.php";
include "topsidebar.php";
?>

      <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <h1>ebay 毛利报表<small>.....</small></h1>
            <ol class="breadcrumb">
              <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="active"><i class="fa fa-table"></i> Tables</li>
            </ol>
          </div>
        </div><!-- /.row -->

        <div class="row">
          <div class="col-lg-12">
			<div class="input-group custom-search-form">
				<input type="text" class="form-control" id="keyword" value="<?php echo $_GET['keyword'];?>" placeholder="Search...">
				<span class="input-group-btn">
				<button class="btn btn-default" type="button" id="search-btn" >
					<i class="fa fa-search"></i>
				</button>
			</span>
           </div>
		  <hr>
            <div class="table-responsive">
              <table cellpadding="2" cellspacing="1" style="white-space:nowrap;"  class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                  <tr>
                    <th>日期<i class="fa fa-sort"></i></th>
                    <th>ebay 账号<i class="fa fa-sort"></i></th>
					<th>客户Id <i class="fa fa-sort"></i></th>
					<th>订单编号<i class="fa fa-sort"></i></th>
                    <th>Record Number<i class="fa fa-sort"></i></th>
                    <th>分sku<i class="fa fa-sort"></i></th>
                    <th>主sku<i class="fa fa-sort"></i></th>
                    <th>组合料号<i class="fa fa-sort"></i></th>
                    <th>Quantity<i class="fa fa-sort"></i></th>
                    <th>币种<i class="fa fa-sort"></i></th>
                    <th>产品售价<i class="fa fa-sort"></i></th>
                    <th>Shipping Fee<i class="fa fa-sort"></i></th>
                    <th>国家<i class="fa fa-sort"></i></th>
                    <th>订单数量<i class="fa fa-sort"></i></th>
                    <th>总收入(USD)<i class="fa fa-sort"></i></th>
                    <th>真实料号总收入<i class="fa fa-sort"></i></th>
                    <th>组合料号总收入<i class="fa fa-sort"></i></th>
                    <th>PP Fee(USD)<i class="fa fa-sort"></i></th>
                    <th>折算RMB总收入(扣除pp)<i class="fa fa-sort"></i></th>
                    <th>是否挂号<i class="fa fa-sort"></i></th>
                    <th>实际过秤重量(kg)<i class="fa fa-sort"></i></th>
                    <th>重量表重量(kg)<i class="fa fa-sort"></i></th>
                    <th>100%运费(RMB)<i class="fa fa-sort"></i></th>
                    <th>运输方式<i class="fa fa-sort"></i></th>
                    <th>发货分区<i class="fa fa-sort"></i></th>
                    <th>折扣后运费(RMB)<i class="fa fa-sort"></i></th>
                    <th>包材成本(RMB)<i class="fa fa-sort"></i></th>
                    <th>产品成本(RMB)<i class="fa fa-sort"></i></th>
                    <th>订单处理成本<i class="fa fa-sort"></i></th>
                    <th>毛利<i class="fa fa-sort"></i></th>
                    <th>真实料号毛利<i class="fa fa-sort"></i></th>
                    <th>组合料号毛利<i class="fa fa-sort"></i></th>
                    <th>毛利率<i class="fa fa-sort"></i></th>
                    <th>采购负责人<i class="fa fa-sort"></i></th>
                    <th>真实料号销售负责人<i class="fa fa-sort"></i></th>
                    <th>虚拟料号负责人<i class="fa fa-sort"></i></th>
                    <th>采购团队<i class="fa fa-sort"></i></th>
                    <th>销售团队-1<i class="fa fa-sort"></i></th>
                    <th>销售团队-2<i class="fa fa-sort"></i></th>
                    <th>是否复制订单<i class="fa fa-sort"></i></th>
                    <th>是否拆分订单<i class="fa fa-sort"></i></th>
                    <th>是否异常订单<i class="fa fa-sort"></i></th>
                    <th>是否补寄<i class="fa fa-sort"></i></th>
                  </tr>
                </thead>
                <tbody>
				<?php
					//$sql = "SELECT * FROM `ebay_ordergrossrate` WHERE `is_effectiveorder` = 1 AND `is_delete` = 0 AND `order_scantime` BETWEEN '1404748800' AND '1404835199' ORDER BY `order_scantime` DESC limit 0,50";
					//$sql = $dbcon->execute($sql);
					//$skuinfo = $dbcon->getResultArray($sql);
					$m = new MongoClient('mongodb://localhost:20000/');
					$where = array("order_platform"=>"ebay");
					if(isset($_GET['keyword'])){
						$where['order_id'] = $_GET['keyword'];
					}

					$total		= $m->bigdata->ebay->find($where)->count();
					$totalpages = $total;
					$pagesize 	= 100;
					$pageindex  =( isset($_GET['PB_page']) )?$_GET['PB_page']:1;
					$page=new page(array('total'=>$total,'perpage'=>$pagesize));
					$sort = array("order_id"=>-1);
					$cursor = $m->bigdata->ebay->find($where)->sort($sort)->skip(($pageindex-1)*$pagesize)->limit($pagesize);
					//$color = array("active","success","warning","danger");
					$color = array();
					//print_r($skuinfo);
					$k = 0;
					foreach($cursor as $item){
						$k++;

				?>

					<tr class="<?php echo $color[$k%4];?>">
					<td><?php echo date("Y-m-d",$item['order_scantime']);?></td>
					<td><?php echo $item['send_account'];?></td>
					<td><?php echo $item['sale_userid'];?></td>
					<td><?php echo $item['order_id'];?></td>
					<td><?php echo $item['recordnumber'];?></td>
					<td><?php echo $item['sku'];?></td>
					<td><?php echo $item['spu'];?></td>
					<td><?php echo $item['csku'];?></td>
					<td><?php echo $item['sell_count'];?></td>
					<td><?php echo $item['order_currency'];?></td>
					<td><?php echo $item['order_total'];?></td>
					<td><?php echo $item['order_shipfee'];?></td>
					<td><?php echo $item['order_countryname'];?></td>
					<td><?php 
						if(isset($item['order_number'])){
							echo $item['order_number'];
						}else{
							echo 1;
						}	
					?></td>
					<td><?php echo $item['order_usdtotal'];?></td>
					<td><?php echo $item['sell_skuprice'];?></td>
					<td><?php echo $item['sell_cskuprice'];?></td>
					<td><?php echo $item['order_ppfee'];?></td>
					<td><?php echo $item['order_cnytotal'];?></td>
					<td><?php

						if($item['is_register'] == 1){
							$register = "是";
						}else{
							$register = "否";
						}
						echo $register;
					?></td>
					<td><?php echo $item['order_weight'];?></td>
					<td><?php echo $item['sku_weight'];?></td>
					<td><?php echo $item['send_allshipfee'];?></td>
					<td><?php echo $item['send_carrier'];?></td>
					<td><?php echo $item['order_sendZone'];?></td>
					<td><?php echo $item['send_rebateshipfee'];?></td>
					<td><?php echo $item['sku_processingcost'];?></td>
					<td><?php echo $item['sku_cost'];?></td>
					<td><?php echo $item['sku_packingcost'];?></td>
					<td><?php echo $item['order_grossrate'];?></td>
					<td><?php echo $item['order_skugrossrate'];?></td>
					<td><?php echo $item['order_cskugrossrate'];?></td>
					<td><?php echo $item['order_grossmarginrate'];?></td>
					<td><?php echo $item['sku_purchase'];?></td>
					<td><?php echo $item['salemember'];?></td>
					<td><?php echo $item['csalemember'];?></td>
					<td><?php echo $item['caigou_team'];?></td>
					<td><?php echo $item['sale_team'];?></td>
					<td><?php echo $item['csale_team'];?></td>
					<?php

					if($item['is_copyorder'] == 1 ){
						$is_copyorder = "是";
					}else{
						$is_copyorder = "否";
					}

					if($item['is_splitorder'] == 1 ){
						$is_splitorder = "是";
					}else{
						$is_splitorder = "否";
					}

					if($item['is_suppleorder'] == 1 ){
						$is_suppleorder = "是";
					}else{
						$is_suppleorder = "否";
					}
					if($item['is_effective'] == 1 ){
						$is_effective = "是";
					}else{
						$is_effective = "否";
					}
					?>
					<td><?php echo $is_copyorder;?></td>
					<td><?php echo $is_splitorder;?></td>
					<td><?php echo $is_effective;?></td>
					<td><?php echo $is_suppleorder;?></td>
                  </tr>
				<?php
					}
				?>

                </tbody>
              </table>

						<?php echo '<center>'.$page->show(1).'</center>';//输出分页 ?> 
            </div>
          </div>
        </div><!-- /.row -->

      </div><!-- /#page-wrapper -->

<?php
include "footer.php";
?>
<script>
$("#search-btn").click(function(){
	var keyword,url;
	url = "http://data.valsun.cn/tables.php?keyword=";
	keyword = $.trim($("#keyword").val());
	window.location.href = url+keyword;
	console.log(keyword);
});
</script>


