<?php
/*
 * 通用actionApi
 * ADD BY zqt 2013.9.13
 * LAST MODIFIED 2014.05.14
 */
class OmAvailableApiAct extends Auth {
	static $errCode = 0;
	static $errMsg = "";
	/*
	 * 取得指定表的记录,成功返回记录集数组，失败返回false
     *
	 */
	function act_getTNameList() {
		$jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $select = $jsonArr['select'];//select，不用关键字SELECT
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($select) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$list = OmAvailableModel :: getTNameList($tName, $select, $where);
		if (is_array($list)) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $list;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 取得指定表的记录数,成功返回记录数count，失败返回false
     *
	 */
	function act_getTNameCount() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$count = OmAvailableModel :: getTNameCount($tName, $where);
		if ($count !== false) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $count;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 添加记录到指定表，成功返回插入的记录ID，失败返回false
     *
	 */
	function act_addTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        if(empty($tName) || empty($set)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$insertId = OmAvailableModel :: addTNameRow($tName, $set);
		if ($insertId !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $insertId;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

    /*
	 * 修改指定表的记录,成功返回影响的记录数affectRows，失败返回false
     *
	 */
	function act_updateTNameRow() {
	    $jsonArr = isset ($_GET['jsonArr']) ? $_GET['jsonArr'] : ''; //传过来的base64编码的json字符串
		if (empty ($jsonArr)) {
			self :: $errCode = 101;
			self :: $errMsg = '参数数组为空';
			return false;
		}
		$jsonArr = json_decode(base64_decode($jsonArr), true); //对base64及json解码
		if (!is_array($jsonArr)) {
			self :: $errCode = 103;
			self :: $errMsg = '参数数组不是数组格式';
			return false;
		}
        $tName = $jsonArr['tName'];//表名
        $set = $jsonArr['set'];//set，用关键字SET
        $where = $jsonArr['where'];//where,要带上关键字WHERE
        if(empty($tName) || empty($set) || empty($where)){
            self :: $errCode = '300';
			self :: $errMsg = '必要参数不完整';
			return false;
        }
		$affectRows = OmAvailableModel :: updateTNameRow($tName, $set, $where);
		if ($affectRows !== FALSE) {
		    self :: $errCode = '200';
			self :: $errMsg = '成功';
			return $affectRows;
		} else {
			self :: $errCode = '404';
			self :: $errMsg = '数据库操作错误';
			return false;
		}
	}

	/*
	 * 同步旧系统的账号表，更新新系统
     *
	 */
	function act_updateOmAccount() {
		require_once WEB_PATH."api/include/functions.php";
		$paramArr= array(
			/* API系统级输入参数 Start */
			'method'	=> 'order.erp.cronOmAccount',  //API名称
			'format'	=> 'json',  //返回格式
			'v'			=> '1.0',   //API版本号
			'username'	=> 'purchase',
			/* API系统级参数 End */

			/* API应用级输入参数 Start*/
			'all'      => "all"
			/* API应用级输入参数 End*/
		);

		$result = callOpenSystem($paramArr, '88');
		$data = json_decode($result,true);
		if(is_array($data) && !empty($data)){
			foreach($data as $value){
				OmAvailableModel::replaceTNameRow2arr('om_account', $value);
			}
			echo 'success';
		}else{
			echo 'false';
		}
		exit;
	}
}
?>