<?php
include "dbconnect.php";
$m = new MongoClient('mongodb://localhost:20000/');
$db = $m->selectDB("bigdata");
$dbcon = new DBClass();
$type = $_POST["type"];
if($type == "addTeam"){
	$insertArr = array(
		"teamType" => $_POST['teamType'],
		"member" => $_POST['teamer_arr'],
		"teamLeader" => $_POST['teamLeader']
	);
	$rtn = array();
	$contion = array("teamLeader" => $_POST['teamLeader']);
	$count = $db->teamInfo->find($contion)->count(); //判断是否存在
	if($count>0){
		$newdata = array('$set' => $insertArr);
		if($db->teamInfo->update($contion,$newdata)){
			$rtn['code'] = 1;
			$rtn['msg'] = "update success....";
		}else{
			$rtn['code'] = 500;
			$rtn['msg'] = "update fail....";
		}
	}else{
		if($m->bigdata->teamInfo->insert($insertArr)){
			$rtn['code'] = 1;
			$rtn['msg'] = "insert success....";
		}else{
			$rtn['code'] = 500;
			$rtn['msg'] = "insert failer....";
		}
	}
	echo json_encode($rtn);
}

?>
