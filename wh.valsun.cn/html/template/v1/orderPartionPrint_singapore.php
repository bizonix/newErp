<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印预览</title>
<style media="print"> 
.noprint { display: none } 
</style> 
<style type="text/css">

body {
	margin-top: 5px;
}
<!--.STYLE5 {font-size: 10px}-->

</style>

<style type="text/css">
html{height:100%;}
body {width:100%;margin:auto;padding:0;height:100%; min-width:1200px;}
h1, h2, h3, h4, h5, h6 {margin:0;padding:0;font-size:14px;}
ul, li, dl, dd, dt, p {margin:0;padding:0;}
ul li {list-style:none;}
img {border:none;}
.main{ width: 373px; border: 1px dashed #000; margin:0 10px;padding: 10px;word-wrap: break-word;
word-break:normal;}
.barcode{ text-align: center; float: left;}
.fontWeight{ font-weight: bold;}
.text{padding-bottom: 10px;}
.code{text-align:center;border-top:1px dashed #000;padding-top:10px;}
</style>
</head>

<body style="font-family:Arial;font-size:14px">
     
				   
<?php
      $partion      =   trim($_REQUEST['partions']);	  
      $num          =   trim($_REQUEST['nums']); 
      $consignment  =   trim($_REQUEST['consignment']);
      $country0     =   trim($_REQUEST['country0']); 
      $country0     =   ucfirst($country0);
      $country1     =   trim($_REQUEST['country1']); 
      $country1     =   ucfirst($country1);
      $country      =   $country1 ? $country1 : $country0; //如果手动输入有国家，则选择手动输入的国家名
      
      $product      =   '';
      $pre          =   '';
      if ($partion == '新加坡DHL GM挂号') {
          $product  =   "GM PACKET PLUS STANDARD";
          $pre      =   '(挂号)';
      } else if ($partion == '新加坡DHL GM平邮') {
          $product  =   "GM PACKET STANDARD";
          $pre      =   '(平邮)';
      }

      $stime    =   strtotime(date('Y-m-d',time())." 00:00:01");
      $res      =   WhOrderPartionPrintDhlModel::get_packageInfo('box_num', array('createtime >'=>$stime), TRUE);
      $box_number   =   isset($res['box_num']) ? trim($res['box_num']) : 0;

	  for($j=0; $j<$num;$j++){
          $box_number++;
          $box_id   =   "SW".date('Ymd',time())."-".$box_number.rand(1,99);
          
          $createtime   =   time();	
          $data     =   array(
                            'box_id'    =>  $box_id,
                            'consignment'=> $consignment,
                            'box_num'   =>  $box_number,
                            'partion'   =>  $partion,
                            'country'   =>  $country,
                            'user'      =>  $_SESSION['userId'],
                            'createtime'=>  $createtime
                        );
		  $id =   WhOrderPartionPrintDhlModel::insert_data($data);	  
?>

<div class="main">
    <div class="text" style="font-weight:bold;">
        <p>Bag ID:&nbsp;<?php echo $box_id;?><span style="margin-left: 70px;">金华达代码:K714</span></p>
        <p>Account#0000511066<span style="margin-left: 130px;">新加坡</span></p>
        <p>Destination(国家):&nbsp;<?php echo $country;?></p>
        <p>Consignment#(提单号):&nbsp;<?php echo $consignment;?></p>        
        <p>Product<?php echo $pre;?>:&nbsp;<?php echo $product; ?> </p>
        <p>Weight:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KG&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Quantity:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;pieces</p>
    </div>
    <div class="code">
    	<img src="barcode128.class.php?data=<?php echo $box_id; ?>" width="330" height="37" />
    </div>
</div>
   
<?php
    if($j!=($num-1)){
	   echo "<div style='page-break-after:always;'>&nbsp;</div>";
	}		
  }

	
			
echo '</body>
</html>';



?>
<!--<script type="text/javascript" src="js/printlabelsku.js?cache=20120817"></script>-->