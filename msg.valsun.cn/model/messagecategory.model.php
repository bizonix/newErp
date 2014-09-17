<?php
/*
 * message分类页面
 */
class messagecategoryModel
{
    private $dbconn = null;
    public static $errMsg = '';
    public static $errCode = 0;
    
    /*
     * 构造函数
     */
    public function __construct(){
        global $dbConn;
        $this->dbconn = $dbConn;
    }
    
    /*
     * 获取全部的正常message分类的信息
     * @return array() 数组 没有返回空数组
     */
    public function getAllCategoryInfoList($where='', $platform=FALSE, $field="*"){
        if ($platform !== FALSE) {
        	$platformsql   = ' and platform='.$platform;
        }
        $sql = "select $field from msg_messagecategory where is_delete = 0 $platformsql $where ";//echo $sql;exit;
        $returnval = array();
        $result = $this->dbconn->fetch_array_all($this->dbconn->query($sql));
        if (!empty($result)) {
        	$returnval = $result;
        }
        return $returnval;
    }
    
    /*
     * 新增messagecategory的内容
     * $data 数组 新增分类所需信息
     * 返回 bool值 成功 TRUE 失败 FALSE
     */
    public function addNewCategory($data){
        $name       = $data['name'];
        $rules      = $data['rules'];
        $account    = $data['account'];
        $notes      = $data['notes'];
        $platform   = $data['platform'];
        $insertsql  = "
            insert into msg_messagecategory values (null, '$name', '$notes', '$rules', '$account', $platform,0)
            ";
        $qre = $this->dbconn->query($insertsql);
        return $qre;
    }
    
    /*
     * 更新分类
     * $cid int 分类id
     * $data array 更新数据
     */
    public function updateCategoryInfo($cid, $data){
        $name = $data['name'];
        $rules = $data['rules'];
        $account = $data['account'];
        $notes = $data['notes'];
        $updatesql = "
            update msg_messagecategory set category_name='$name', ebay_note='$notes', rules='$rules',ebay_account='$account'
             where id=$cid
            ";
        return $this->dbconn->query($updatesql);
    }
    
    /*
     * 获得某个messagecategory的信息 根据id
     */
    public function getCategoryInfoById($cid, $where=""){
        $sql = 'select * from msg_messagecategory where id='.$cid.$where;
        return $row = $this->dbconn->fetch_first($sql);
    }
    
    /*
     * 删除一个分类
     * $cid 分类id
     */
    public function delCategoryById($cid){
        $sql = 'update msg_messagecategory set is_delete=1 where id='.$cid;
        return $this->dbconn->query($sql);
    }
    
    /*
     * 获得一组分类的信息
     * $idar    数组表示的id
     */
    public function getFieldInfoByIds($idar, $where='') {
    	$sql_id    = implode(',', $idar);
    	$sql       = "select * from msg_messagecategory where id in ($sql_id) $where";
    	return $this->dbconn->fetch_array_all($this->dbconn->query($sql));
    }
}
