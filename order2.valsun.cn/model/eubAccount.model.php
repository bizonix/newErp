<?php
class eubAccountModel extends CommonModel{
	public function __construct(){
		parent::__construct();
	}

	public function geteubAccountByAcid($id){
		return $this->sql('SELECT * FROM '. $this->getTableName()." WHERE accountId= '$id' AND is_delete=0 ")->limit('*')->select();
	}
}
?>