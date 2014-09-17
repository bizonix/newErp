<?php
/**
 *赛维美国专线口袋编号打印 
 * @author Gary 2014-06-24
 */
?>
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
.main{ width: 373px; border: 1px dashed #000; margin:0 10px;padding: 3px;word-wrap: break-word;
word-break:normal;}
.barcode{ text-align: center; float: left;}
.fontWeight{ font-weight: bold;}
.text{padding-bottom: 3px;}
.code{text-align:center;border-top:1px dashed #000;padding-top:5px;}
</style>
</head>

<body style="font-family:Arial;font-size:18px">
		   
<?php
    $partion    =   trim($_REQUEST['partions']);
    $num        =   trim($_REQUEST['nums']);
    $HAWB       =   trim($_REQUEST['HAWB']);
    if(!$num){
        echo "<script type='text/javascript'>alert('没有输入数量!');window.history.go(-1);</script>";
        exit;
    }
    $userId     =   $_SESSION['userId'];
    for($j = 0; $j<$num;$j++){
        $id     =   orderPartionModel::insertPrintRecord($partion,$userId);
        if($id){
?>
<div class="main">
    <div class="text" style="font-weight:bold;height:80px;">
        <span style="padding-left: 100px; padding-top: 0px;font-size: 22px;">赛维博</span>
        <span style="padding-left: 10px;padding-top: 2px;"><?php echo $partion;?></span>
        <p style="font-size:16px;">HAWB#:<?php echo $HAWB ? $HAWB : '';?></p>
        <p style="float:left;font-size:16px;">CTN NO. <?php echo $id?> <span style="padding-left:90px;">金华达代码：K713</span></p>
        <p style="font-size:14px;">
            <span style="margin-top: 15px; float:left;">总数量(pieces)：__________</span>
            <span style="margin-top: 15px; float:right;">总重量(KG)：__________</span>
        </p>
    </div>
    <p style="margin-top:15px;">
        <span style="margin-top: 10px;margin-bottom:3px; float:left;">日期：_____________</span>
    </p>
    <div class="code">
    	<img src="./barcode128.class.php?data=<?php echo $id;?>" width="300" height="40" />  
    </div>   
</div>
<?php
		}
        if($j!=($num-1)){
		   echo "<div style='page-break-after:always;'></div>";
		}		
	  }		
echo '</body>
</html>';
?>
<!--<script type="text/javascript" src="js/printlabelsku.js?cache=20120817"></script>-->