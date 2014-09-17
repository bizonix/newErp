<?php
	//include "include/config.php";
	//$fname 		= md5($_SESSION['truename']);
	//$file 		= 'temp/'.$fname.'.txt';
	$action 	= $_REQUEST['action'];
	switch ($action){
		
		//接收相关参数,生成flash监听文件,便于同步拍照
		case "order":
    		$orderid = $_REQUEST['orderid'];
    		$fd 	= $_REQUEST['fd'];
    		if(!$orderid) exit('请传递订单号参数');
    		if(!$fd) exit('请传递保存路径参数');
    		if(@$fp = fopen($file,'w')){
    			$res = 'orderid='.$orderid.'&status=0&fd='.$fd.'&imgurl=';
    			fwrite($fp,$res);
    			fclose($fp);
    			//$chmod($file,0777);
    			echo "ok";
    		}else{
    			die("文件写错误,请检查相关目录权限！");
    		}
    		break;
    		
    		//FLASH监听拍照文件,页面ajax结果同步显示
    		case "check":
    		$res = trim(file_get_contents($file));	
    		if($_REQUEST['dataformat']=="json"){
    			$res = '{"'.str_replace(array('&','='),array('","','":"'),$res).'"}';
    		}
    		echo $res;
            break;
		
		//保存拍照图片,更新状态,便于前台ajax获取图片参数并显示
		case "save":
    		if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
    			$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];
    			//$img = $_GET["img"];
    			//$_REQUEST["oid"] = time();
    			$fds = explode(",",$_REQUEST["fd"]);
    			//$oid = str_replace("/","",$_REQUEST["fd"]);
    			$fd  = $fds[0];
    			$oid = $fds[1];
    			$filename = $fd.'/'.$oid.".jpg";
     
                /** 判断文件夹是否存在并创建文件夹 add BY GARY**/
                $dir_path   =   str_replace('\\', '/', __DIR__).'/'; //网站根目录
                $path       =   $dir_path.$fd;
                //var_dump($path);exit;
                if(!is_dir($path)){
                    $path   =   $dir_path;
                    $arr    =   explode('/', $fd);
                    if(!empty($arr)){
                        foreach($arr as $val){
                            $path   .=  $val.'/';
                            if(!is_dir($path)){
                                mkdir($path, 0777);
                            }
                        }
                    }
                }
                /** end**/
                
    			file_put_contents($filename, $jpg);
                
                /** 添加拍照空白判断 add Gary**/
                if(md5_file($filename) == '5cc8c6bb8a0bfbdc342d2f4a9e024139'){
                    echo '拍照空白!';
                    exit;   //如果上传的是空白的照片，则终止程序 
                }
                /** end**/
    			echo $filename;
    			/*if(@$fp = fopen($file,'w')){
    				//$res = 'orderid='.$oid.'&status=1&fd='.$fd.'&imgurl='.$filename;
    				//fwrite($fp,$res);
    				fclose($fp);
    				//$chmod($file,0777);
    			} else{
    				die("文件写错误,请检查相关目录权限！");
    			}*/
    			 
    	
    		} else{
    		  echo "Encoded JPEG information not received.";
    		}
            break;
		default:
		  die("非法访问,系统已记录！");
    }
?>
