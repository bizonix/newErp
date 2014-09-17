<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>打印预览</title>
<style media="print"> 
.noprint { display: none } 
</style> 
<style type="text/css">
html{height:100%;}
body {width:100%;margin:auto;padding:0;height:100%; min-width:1200px;}
h1, h2, h3, h4, h5, h6 {margin:0;padding:0;font-size:14px;}
ul, li, dl, dd, dt, p {margin:0;padding:0;}
ul li {list-style:none;}
img {border:none;}
.main{ width: 373px; border: 1px dashed #000; margin:0 10px;padding: 5px;word-wrap: break-word;
word-break:normal;}
.barcode{ text-align: center; float: left;}
.fontWeight{ font-weight: bold;}
.text{padding-bottom: 5px;}
.code{text-align:center;border-top:1px dashed #000;padding-top:5px;}
</style>
</head>

<body>			 
<?php
	$nums = isset($_POST['nums'])? intval($_POST['nums']) : 0;
	$partionId = isset($_POST['partions']) ? intval($_POST['partions']) : 0;
	if(empty($partionId)){
		echo "请先选择分区";exit;
	}
	$partion = WhTransportPartitionModel::find($partionId);
	if(!$partion){
		echo "分区不存在";exit;
	}
	$partionName = $partion['title'];
	for($j=0; $j < $nums;$j++){
		$data = array(
			'partitionId' => $partion['id'],
			'totalWeight' => 0,
			'totalNum' => 0,
			'partion' => $partion['title'],
			'status' => 0,
			'printUserId' => $_SESSION['userId'],
			'printTime' => time(),
			'modifyTime' => 0,
		);
		$id = WhOrderPartionPrintModel::insert($data);
	//$userId = $_SESSION['userId'];
	//echo $userId;
	//$msg = orderPartionModel::insertPrintRecord($partionName,$userId);
	if($id){
	//$id = $msg;
?>				  
<div class="main">
    <div class="text" style="font-weight:bold;">
        <p style="padding-left: 155px; padding-top: 0px;font-size: 22px;">赛维</p>
        <p style="padding-left: <?php echo strlen($partionName) > 30 ? '70' : 130;?>px;padding-top: 2px;"><?php echo $partionName;?></p>
        <p style="margin-top:15px;">
            <span style="margin-bottom: 0px;">总数量：__________</span>
            <span style="margin-bottom: 0px;">总重量：__________</span></p>        
    </div>
    <div class="code">
    	<img src="./barcode128.class.php?data=<?php echo $id;?>" width="300" height="40" />  
    </div>
    <p style="margin-top:15px;">
        <span style="margin-bottom: 0px;"><?php echo $id?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span style="margin-bottom: 0px;">日期：_____________</span></p>
</div>
<?php
		}
        if($j!=($nums-1)){
		   echo "<div style='page-break-after:always;'>&nbsp;</div>";
		}		
	  }

	
			
echo '</body>
</html>';



?>